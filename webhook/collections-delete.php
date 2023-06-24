<?php

$__multiLanguageNotNeeded = TRUE ;
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';
$cls_functions = new Client_functions($_GET['store']);
 
 
define('SHOPIFY_APP_SECRET', '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');
function verify_webhook($data, $hmac_header)
{
  $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
  return hash_equals($hmac_header, $calculated_hmac);
}


$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
$data = file_get_contents('php://input');
$verified = verify_webhook($data, $hmac_header);
generate_log('collection_delete-webhook' , var_export($verified, true)); //check error.log to see the result


$topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];
$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];



if($verified == true){
   generate_log('collection_delete-webhook', json_encode($verified) . "  verified"); 
    if( $topic_header == "collections/delete" ) {
			$collection = json_decode($data);
			generate_log('collection_delete-webhook', json_encode($collection));
			$shopinfo = $cls_functions->get_store_detail_obj();
			$where_query = array(['', 'collection_id', '=', $collection->id, ' ', 'store_user_id', '=', $shopinfo->store_user_id]);
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









