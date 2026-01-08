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
                $fields = array(
                    'status' => '0',
                    'is_demand_accept' => '0'
                );
                $where_query = array(["", "shop_name", "=",$shop]);
                $data =  $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query);
                
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

