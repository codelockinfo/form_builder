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
    if( $topic_header == "collections/create" ) {
			$collection = json_decode($data);
			if(!empty($collection)){
				$shopinfo = $cls_functions->get_store_detail_obj();
				$collectionid = isset($collection->id) ? $collection->id : '';
				$store_user_id = isset($shopinfo["store_user_id"]) ? $shopinfo["store_user_id"] : "";
				if($store_user_id  == ""){
					generate_log('collection_create-webhook', "Store user id is not found");
				}
				$where_query = array(["", "collection_id", "=", "$collectionid"], ["AND", "store_user_id", "=", "$store_user_id"]);
				$comeback = $cls_functions->select_result(TABLE_COLLECTION_MASTER, '*', $where_query);
				$CollectionId = isset($comeback['data']->collection_id) ? $comeback['data']->collection_id : '';

				if(empty($CollectionId) || $CollectionId == ""){
					$field_array = array();
					$img_src = ($collection->image == '') ? '' : $collection->image->src;     
					$field_array = array(
						'`collection_id`' => $collection->id,
						'`title`' => $collection->title,
						'`image`' =>$img_src,
						'`description`' =>str_replace("'", "\'",$collection->body_html),
						'`handle`' =>$collection->handle,
						'`store_user_id`' => $store_user_id,
						'`created_at`' => date('Y-m-d H:i:s'),
						'`updated_at`' => date('Y-m-d H:i:s'),
					);
					$sql_prod_id = $cls_functions->post_data(TABLE_COLLECTION_MASTER, array($field_array));
				}
			}
    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('collection_create-webhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>









