<?php

include_once '../append/connection.php';
include_once  ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';
generate_log('shop-redact-webhook' , json_encode($_SERVER ));
$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];

// $shop = 'dashboardmanage.myshopify.com';
$user_obj = new Client_functions($shop);

$shopinfo = (array) $user_obj->current_store_obj;
generate_log('shop-redact-webhook' , json_encode($shopinfo ));
$store_user_id = $shopinfo['store_user_id'];

$wh_data = file_get_contents('php://input');
generate_log('mandatory-webhook' , $wh_data);

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
        generate_log('shop-redact-webhook' , json_encode($returrnn));
    if (!empty($returrnn['data']) && $returrnn['status'] == 0) {
        generate_log('shop-redact-webhook' , json_encode($returrnn));
    }

    http_response_code(200);
    exit();
}else{
    echo
    http_response_code(400);
    exit();
}
die;


$__multiLanguageNotNeeded = TRUE ;
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';

generate_log('shop-redact' , 'STEPP222');

$cls_functions = new Client_functions($_GET['store']);
// Retrieve the request headers and body
$headers = getallheaders();
$requestBody = file_get_contents('php://input');

// Retrieve the Shopify API secret key
$shopifyApiSecret = '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';

// Calculate the HMAC signature
$calculatedHmac = base64_encode(hash_hmac('sha256', $requestBody, $shopifyApiSecret, true));

// Compare the calculated HMAC with the received HMAC
if (isset($headers['X-Shopify-Hmac-SHA256']) && hash_equals($headers['X-Shopify-Hmac-SHA256'], $calculatedHmac)) {
    // HMAC verification successful, process the webhook payload
    // Your webhook handling code goes here
    $selected_field = 'store_name,email';
$where = array(['', 'store_name', '=', $_GET['store']]);

$table_shop_info = $CF_obj->select_result(TABLE_USER_SHOP, $selected_field, $where);
generate_log('shop-redact' , json_encode($table_shop_info));
if($table_shop_info['status'] == 1 && !empty($table_shop_info['data'])){
    $table_shop_info = $table_shop_info['data'];
    
    $fields = array(
        'shop_name' => '', 
        'store_name' => '', 
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
    
    $where = array(['', 'store_name', '=', $table_shop_info->store_name]);
    $returrnn = $CF_obj->put_data(TABLE_USER_SHOP, $fields, $where);
    generate_log('shop-redact' , json_encode($returrnn));
    
}
} else {
    // HMAC verification failed, do not trust the webhook payload
    // Handle the error or log the incident
}












// if(MODE == 'local'){
//     $shop_info = '{"store_name": "happyeventsurat.myshopify.com"}';
// }else{
//     $shop_info = file_get_contents('php://input');
// }
// generate_log('shop-redact' , $shop_info);

// /* shop info array */
// $shop_info = json_decode($shop_info, TRUE);

// $shop = $shop_info['store_name'];
// $CF_obj = new Client_functions($shop);

// $selected_field = 'store_name,email';
// $where = array(['', 'store_name', '=', $shop_info['shop_domain']]);

// $table_shop_info = $CF_obj->select_result(TABLE_USER_SHOP, $selected_field, $where);
// generate_log('shop-redact' , json_encode($table_shop_info));
// if($table_shop_info['status'] == 1 && !empty($table_shop_info['data'])){
//     $table_shop_info = $table_shop_info['data'];
    
//     $fields = array(
//         'shop_name' => '', 
//         'store_name' => '', 
//         'address11' => '',
//         'address22' => '',
//         'city' => '',
//         'country_name' => '',
//         'zip' => '',
//         'timezone' => '',        
//         'domain' => '',
//         'mobile_no' => '',/*phone number*/
//         'store_holder' => '',/*shop owner*/
//         'cash' => '',/*currency*/
//         'price_pattern' => '',/*money format*/

//     );
    
//     $where = array(['', 'store_name', '=', $table_shop_info->store_name]);
//     $returrnn = $CF_obj->put_data(TABLE_USER_SHOP, $fields, $where);
//     // generate_log('shop-redact' , json_encode($returrnn));
    
// }

?>