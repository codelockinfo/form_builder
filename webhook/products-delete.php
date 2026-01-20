<?php

$__multiLanguageNotNeeded = TRUE ;
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';

$topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];
$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
$cls_functions = new Client_functions($shop);

function verify_webhook($data, $hmac_header)
{
  $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_SECRET, true));
  return hash_equals($hmac_header, $calculated_hmac);
}

$data = file_get_contents('php://input');
$product = json_decode($data);
$verified = verify_webhook($data, $hmac_header);

if($verified == true){
    if( $topic_header == "products/delete" ) {
        $shopinfo = $cls_functions->get_store_detail_obj();
        $store_user_id = $shopinfo["store_user_id"];
        $where_query = array(['', 'product_id', '=', $product->id, ' ', 'store_user_id', '=', $store_user_id]);
        $data = $cls_functions->delete_data(TABLE_PRODUCT_MASTER, $where_query);
        echo $cls_functions->last_query();
    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('product_delete-webhook', json_encode($verified) . "  not verified"); 
    http_response_code(401);
    echo "Access Denied main ";
}

?>

