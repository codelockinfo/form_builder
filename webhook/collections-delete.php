<?php

$__multiLanguageNotNeeded = TRUE ;
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
    if( $topic_header == "collections/delete" ) {
        $collection = json_decode($data);
        $shopinfo = $cls_functions->get_store_detail_obj();
        $store_user_id = isset($shopinfo["store_user_id"]) ? $shopinfo["store_user_id"] : "";
        $where_query = array(['', 'collection_id', '=', $collection->id, ' ', 'store_user_id', '=', $store_user_id]);
        $data = $cls_functions->delete_data(TABLE_COLLECTION_MASTER, $where_query);
        echo $cls_functions->last_query();
    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('collection_delete-webhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>









