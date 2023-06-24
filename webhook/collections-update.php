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
generate_log('collection_update-webhook' , var_export($verified, true)); //check error.log to see the result


$topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];
$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];



if($verified == true){
   generate_log('collection_update-webhook', json_encode($verified) . "  verified"); 
    if( $topic_header == "collections/update" ) {
			$collection = json_decode($collection_update);
			generate_log('collection_update-webhook', json_encode($collection));
			if(!empty($collection) && isset($collection->id)){
				$shopinfo = $cls_functions->get_store_detail_obj();
				$field_array = array();
				$p_id = '';
				$img_src = ($collection->image == '') ? '' : $collection->image->src;  
				$fields = array(
							  '`title`' => $collection->title,
							  '`image`' =>$img_src,
							  '`description`' =>str_replace("'", "\'",$collection->body_html),
							  '`handle`' =>$collection->handle,
						  );
						  $where_query = array(
							  ["", "collection_id", "=", $collection->id],
						  );
			   $comeback = $cls_functions->put_data(TABLE_COLLECTION_MASTER, $fields, $where_query);
			}

    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('collection_update-webhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>









