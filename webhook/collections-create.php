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
generate_log('collection_create-webhook ' , var_export($verified, true)); //check error.log to see the result


$topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];
$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];



if($verified == true){
   generate_log('collection_create-webhook', json_encode($verified) . "  verified"); 
    if( $topic_header == "collections/create" ) {
			$collection = json_decode($data);
			generate_log('collection_create-webhook', json_encode($collection));
			if(!empty($collection)){
				$shopinfo = $cls_functions->get_store_detail_obj();
				$collectionid = isset($collection->id) ? $collection->id : '';
						$where_query = array(["", "collection_id", "=", "$collectionid"], ["AND", "store_user_id", "=", "$shopinfo->store_user_id"]);
						$comeback = $cls_functions->select_result(TABLE_COLLECTION_MASTER, '*', $where_query);
			   generate_log('collection_create-webhook', json_encode($comeback['data'])   . "collection DATA");
			   generate_log('collection_create-webhook', json_encode($comeback['data']->collection_id)   . "DATA  collection ID");
				$CollectionId = isset($comeback['data']->collection_id) ? $comeback['data']->collection_id : '';
				if(empty($CollectionId)){
				$field_array = array();
				// $p_id = '';
				$img_src = ($collection->image == '') ? '' : $collection->image->src;     
				$field_array = array(
					'`collection_id`' => $collection->id,
					'`title`' => $collection->title,
					'`image`' =>$img_src,
					'`description`' =>str_replace("'", "\'",$collection->body_html),
					'`handle`' =>$collection->handle,
					'`store_user_id`' => $shopinfo->store_user_id,
					'`created_at`' => date('Y-m-d H:i:s'),
					'`updated_at`' => date('Y-m-d H:i:s'),
				);
				generate_log('collection_create-webhook', json_encode(array($field_array)));
				$sql_prod_id = $cls_functions->post_data(TABLE_COLLECTION_MASTER, array($field_array));
				generate_log('collection_create-webhook', json_encode($sql_prod_id));
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









