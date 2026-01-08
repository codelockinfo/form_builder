<?php
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';

$topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];
$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];

$cls_functions = new Client_functions($shop);

function verify_webhook($data, $hmac_header, $cls_functions)
{
    $where_query = array(["", "status", "=", "1"]);
    $comeback= $cls_functions->select_result(CLS_TABLE_THIRDPARTY_APIKEY, '*',$where_query);
    $SHOPIFY_SECRET = (isset($comeback['data'][2]['thirdparty_apikey']) && $comeback['data'][2]['thirdparty_apikey'] !== '') ? $comeback['data'][2]['thirdparty_apikey'] : '';
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $SHOPIFY_SECRET, true));
    return hash_equals($hmac_header, $calculated_hmac);
}

$data = file_get_contents('php://input');
$verified = verify_webhook($data, $hmac_header, $cls_functions);

if($verified == true){
    if( $topic_header == "app/uninstalled" ) {
        $shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
        
        generate_log('app-uninstall', "=== UNINSTALL WEBHOOK RECEIVED ===");
        generate_log('app-uninstall', "Shop: $shop");
        generate_log('app-uninstall', "Topic: $topic_header");
        
        // Get store_user_id from shop
        $where_query = array(["", "shop_name", "=", $shop], ["OR", "store_name", "=", $shop]);
        $store_result = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
        
        generate_log('app-uninstall', "Store lookup result status: " . (isset($store_result['status']) ? $store_result['status'] : 'NOT SET'));
        generate_log('app-uninstall', "Store lookup data: " . (isset($store_result['data']) ? json_encode($store_result['data']) : 'EMPTY'));
        
        if ($store_result['status'] == 1 && !empty($store_result['data'])) {
            $store_user_id = isset($store_result['data']['store_user_id']) ? intval($store_result['data']['store_user_id']) : 0;
            
            generate_log('app-uninstall', "Store user ID: $store_user_id");
            
            if ($store_user_id > 0) {
                try {
                    $conn = $GLOBALS['conn'];
                    
                    if (!$conn) {
                        throw new Exception("Database connection not available");
                    }
                    
                    // Start transaction for data integrity
                    mysqli_begin_transaction($conn);
                    
                    $total_deleted = 0;
                    
                    // 1. Delete form analytics
                    $analytics_where = array(["", "store_client_id", "=", $store_user_id]);
                    $analytics_delete = $cls_functions->delete_data(TABLE_FORM_ANALYTICS, $analytics_where);
                    $analytics_result = json_decode($analytics_delete, true);
                    $analytics_deleted = isset($analytics_result['data']['affected_rows']) ? $analytics_result['data']['affected_rows'] : 0;
                    generate_log('app-uninstall', "Deleted $analytics_deleted analytics records");
                    $total_deleted += $analytics_deleted;
                    
                    // 2. Get all form IDs for this store
                    $forms_where = array(["", "store_client_id", "=", $store_user_id]);
                    $forms_result = $cls_functions->select_result(TABLE_FORMS, 'id', $forms_where);
                    $form_ids = array();
                    
                    if ($forms_result['status'] == 1 && !empty($forms_result['data'])) {
                        $forms_data = is_array($forms_result['data']) ? $forms_result['data'] : array($forms_result['data']);
                        foreach ($forms_data as $form) {
                            if (isset($form['id'])) {
                                $form_ids[] = intval($form['id']);
                            }
                        }
                    }
                    
                    generate_log('app-uninstall', "Found " . count($form_ids) . " forms to delete");
                    
                    if (!empty($form_ids)) {
                        // 3. Delete form submissions for each form
                        $submissions_deleted = 0;
                        foreach ($form_ids as $form_id) {
                            $submissions_where = array(["", "form_id", "=", $form_id]);
                            $submissions_delete = $cls_functions->delete_data(TABLE_FORM_SUBMISSIONS, $submissions_where);
                            $submissions_result = json_decode($submissions_delete, true);
                            $submissions_deleted += isset($submissions_result['data']['affected_rows']) ? $submissions_result['data']['affected_rows'] : 0;
                        }
                        generate_log('app-uninstall', "Deleted $submissions_deleted submission records");
                        $total_deleted += $submissions_deleted;
                        
                        // 4. Delete form data (elements) for each form
                        $form_data_deleted = 0;
                        foreach ($form_ids as $form_id) {
                            $form_data_where = array(["", "form_id", "=", $form_id]);
                            $form_data_delete = $cls_functions->delete_data(TABLE_FORM_DATA, $form_data_where);
                            $form_data_result = json_decode($form_data_delete, true);
                            $form_data_deleted += isset($form_data_result['data']['affected_rows']) ? $form_data_result['data']['affected_rows'] : 0;
                        }
                        generate_log('app-uninstall', "Deleted $form_data_deleted form data records");
                        $total_deleted += $form_data_deleted;
                    }
                    
                    // 5. Delete all forms
                    $forms_delete = $cls_functions->delete_data(TABLE_FORMS, $forms_where);
                    $forms_result = json_decode($forms_delete, true);
                    $forms_deleted = isset($forms_result['data']['affected_rows']) ? $forms_result['data']['affected_rows'] : 0;
                    generate_log('app-uninstall', "Deleted $forms_deleted form records");
                    $total_deleted += $forms_deleted;
                    
                    // Commit transaction
                    mysqli_commit($conn);
                    
                    // 6. Update store status to inactive
                    $fields = array(
                        'status' => '0',
                        'is_demand_accept' => '0'
                    );
                    $where_query = array(["", "shop_name", "=", $shop]);
                    $update_result = $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query);
                    
                    generate_log('app-uninstall', "Successfully deleted $total_deleted total records for store: $shop (store_user_id: $store_user_id)");
                    generate_log('app-uninstall', "Store status updated to inactive");
                    echo "Success: Deleted $total_deleted records for store: $shop";
                    
                } catch (Exception $e) {
                    // Rollback transaction on error
                    if (isset($conn)) {
                        mysqli_rollback($conn);
                    }
                    generate_log('app-uninstall', "ERROR deleting data: " . $e->getMessage());
                    generate_log('app-uninstall', "Stack trace: " . $e->getTraceAsString());
                    
                    // Still update store status even if deletion fails
                    try {
                        $fields = array(
                            'status' => '0',
                            'is_demand_accept' => '0'
                        );
                        $where_query = array(["", "shop_name", "=", $shop]);
                        $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query);
                    } catch (Exception $e2) {
                        generate_log('app-uninstall', "ERROR updating store status: " . $e2->getMessage());
                    }
                    
                    echo "Error: " . $e->getMessage();
                }
            } else {
                generate_log('app-uninstall', "Store user ID is 0 or invalid for shop: $shop");
                // Still update store status
                $fields = array(
                    'status' => '0',
                    'is_demand_accept' => '0'
                );
                $where_query = array(["", "shop_name", "=", $shop]);
                $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query);
            }
        } else {
            generate_log('app-uninstall', "Store not found in database: $shop");
            generate_log('app-uninstall', "Store result: " . json_encode($store_result));
            // Still try to update store status if it exists
            $fields = array(
                'status' => '0',
                'is_demand_accept' => '0'
            );
            $where_query = array(["", "shop_name", "=", $shop]);
            $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query);
        }
    }
    else {
        generate_log('app-uninstall', "Invalid topic header: $topic_header");
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('uninstall-webhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>

