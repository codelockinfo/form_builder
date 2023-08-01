<?php

$__multiLanguageNotNeeded = TRUE ;
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';

$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
$topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];
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
$product = json_decode($data);
$verified = verify_webhook($data, $hmac_header, $cls_functions);

if($verified == true){
    if( $topic_header == "products/create" ) {
        if(!empty($product)){
			$shopinfo = $cls_functions->get_store_detail_obj();
			$productid = isset($product->id) ? $product->id : '';
			$store_user_id = isset($shopinfo["store_user_id"]) ? $shopinfo["store_user_id"] : '';

			if($store_user_id == ''){
				generate_log('product_create-webhook' , "STORE USER ID"); 
			}
			$where_query = array(["", "product_id", "=", "$productid"], ["AND", "store_user_id", "=", "$store_user_id"]);
			$comeback = $cls_functions->select_result(TABLE_PRODUCT_MASTER, '*', $where_query);
			$productData = (object)$comeback->data;
			$clsProductId = isset($productData->['product_id']) ? $productData->['product_id'] : '';
			$ProductId = isset($comeback->data['product_id']) ? $comeback->data['product_id'] : '';

			if(empty($clsProductId)){
				$field_array = array();
				foreach ($product->variants as $i => $variants) {
					$main_price = ($variants->price != '') ? $variants->price : "";
				}
				$img_src = ($product->image == '') ? '' : $product->image->src;     
				$field_array = array(
					'`product_id`' => $product->id,
					'`title`' => $product->title,
				  	'`image`' =>$img_src,
				  	'`description`' =>str_replace("'", "\'",$product->body_html),
				  	'`handle`' =>$product->handle,
				  	'`price`' =>$main_price,
				  	'`vendor`' =>$product->vendor,
				  	'`store_user_id`' => $store_user_id,
				  	'`created_at`' => date('Y-m-d H:i:s'),
				  	'`updated_at`' => date('Y-m-d H:i:s'),
				);
				$sql_prod_id = $cls_functions->post_data(TABLE_PRODUCT_MASTER, array($field_array));
			}
		}
    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('product_create-webhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>









