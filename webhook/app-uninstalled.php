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
        
        // Get store_user_id from shop
        $where_query = array(["", "shop_name", "=", $shop], ["OR", "store_name", "=", $shop]);
        $store_result = $cls_functions->select_result(TABLE_USER_SHOP, 'store_user_id', $where_query, ['single' => true]);
        
        if ($store_result['status'] == 1 && !empty($store_result['data'])) {
            $store_user_id = isset($store_result['data']['store_user_id']) ? intval($store_result['data']['store_user_id']) : 0;
            
            if ($store_user_id > 0) {
                // Log the uninstall
                generate_log('app-uninstall', "Starting data deletion for store: $shop, store_user_id: $store_user_id");
                
                try {
                    $conn = $GLOBALS['conn'];
                    
                    // 1. Get all form IDs for this store
                    $forms_query = "SELECT id FROM " . TABLE_FORMS . " WHERE store_client_id = " . intval($store_user_id);
                    $forms_result = mysqli_query($conn, $forms_query);
                    $form_ids = array();
                    
                    if ($forms_result) {
                        while ($row = mysqli_fetch_assoc($forms_result)) {
                            $form_ids[] = intval($row['id']);
                        }
                    }
                    
                    if (!empty($form_ids)) {
                        $form_ids_str = implode(',', $form_ids);
                        
                        // 2. Delete form analytics
                        $analytics_query = "DELETE FROM " . TABLE_FORM_ANALYTICS . " WHERE store_client_id = " . intval($store_user_id);
                        mysqli_query($conn, $analytics_query);
                        $analytics_deleted = mysqli_affected_rows($conn);
                        generate_log('app-uninstall', "Deleted $analytics_deleted analytics records");
                        
                        // 3. Delete form submissions
                        $submissions_query = "DELETE FROM " . TABLE_FORM_SUBMISSIONS . " WHERE form_id IN ($form_ids_str)";
                        mysqli_query($conn, $submissions_query);
                        $submissions_deleted = mysqli_affected_rows($conn);
                        generate_log('app-uninstall', "Deleted $submissions_deleted submission records");
                        
                        // 4. Delete form data (elements)
                        $form_data_query = "DELETE FROM " . TABLE_FORM_DATA . " WHERE form_id IN ($form_ids_str)";
                        mysqli_query($conn, $form_data_query);
                        $form_data_deleted = mysqli_affected_rows($conn);
                        generate_log('app-uninstall', "Deleted $form_data_deleted form data records");
                    }
                    
                    // 5. Delete all forms
                    $forms_delete_query = "DELETE FROM " . TABLE_FORMS . " WHERE store_client_id = " . intval($store_user_id);
                    mysqli_query($conn, $forms_delete_query);
                    $forms_deleted = mysqli_affected_rows($conn);
                    generate_log('app-uninstall', "Deleted $forms_deleted form records");
                    
                    // 6. Update store status to inactive
                    $fields = array(
                        'status' => '0',
                        'is_demand_accept' => '0'
                    );
                    $where_query = array(["", "shop_name", "=", $shop]);
                    $update_result = $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query);
                    
                    generate_log('app-uninstall', "Successfully deleted all data for store: $shop (store_user_id: $store_user_id)");
                    echo "Success: All data deleted for store: $shop";
                    
                } catch (Exception $e) {
                    generate_log('app-uninstall', "Error deleting data: " . $e->getMessage());
                    // Still update store status even if deletion fails
                    $fields = array(
                        'status' => '0',
                        'is_demand_accept' => '0'
                    );
                    $where_query = array(["", "shop_name", "=", $shop]);
                    $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query);
                    echo "Error: " . $e->getMessage();
                }
            } else {
                generate_log('app-uninstall', "Store user ID not found for shop: $shop");
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
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('uninstall-webhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>

