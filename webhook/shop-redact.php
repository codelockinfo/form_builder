<?php

include_once '../append/connection.php';
include_once  ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';

$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
$user_obj = new Client_functions($shop);
$cls_functions = new Client_functions($shop);
generate_log('shop-redact-webhook' , "STEP1");

function verify_webhook($data, $hmac_header)
{
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_SECRET, true));
    return hash_equals($hmac_header, $calculated_hmac); // Fix argument order for standard consistency, though hash_equals is symmetric
}

$data = file_get_contents('php://input');
$verified = verify_webhook($data, $hmac_header);

if ($verified) {
    $shopinfo = (array) $user_obj->current_store_obj;
    $store_user_id = $shopinfo['store_user_id'];
    if (!empty($shopinfo)) {
        $fields = array(
            'address11' => '',
            'address22' => '',
            'city' => '',
            'country_name' => '',
            'zip' => '',
            'timezone' => '',        
            'domain' => '',
            'mobile_no' => '',/*phone number*/
            'store_holder' => '',/*shop owner*/
            'cash' => '',/*currency*/
            'price_pattern' => '',/*money format*/
        );
    
        $where = array(['','store_user_id','=',$store_user_id]);
        $returrnn = $user_obj->put_data(TABLE_USER_SHOP, $fields, $where);
        http_response_code(200);
        exit();
    }else{
        http_response_code(200);
        exit();
    }
} else {
  generate_log('shop-redact-webhook' , "in else");
  http_response_code(401);
}
?>