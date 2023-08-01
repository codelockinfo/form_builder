<?php

include_once '../append/connection.php';
include_once  ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';

$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
$user_obj = new Client_functions($shop);

function verify_webhook($data, $hmac_header, $cls_functions)
{
    $where_query = array(["", "status", "=", "1"]);
    $comeback= $cls_functions->select_result(CLS_TABLE_THIRDPARTY_APIKEY, '*',$where_query);
    $SHOPIFY_SECRET = (isset($comeback['data'][2]['thirdparty_apikey']) && $comeback['data'][2]['thirdparty_apikey'] !== '') ? $comeback['data'][2]['thirdparty_apikey'] : '';
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $SHOPIFY_SECRET, true));
    return hash_equals($calculated_hmac, $hmac_header);
}

$data = file_get_contents('php://input');
$verified = verify_webhook($data, $hmac_header, $cls_functions);

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
        echo
        http_response_code(400);
        exit();
    }
} else {
  generate_log('shop-redact-webhook' , "in else");
  http_response_code(401);
}
?>