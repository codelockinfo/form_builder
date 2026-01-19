<?php

$__multiLanguageNotNeeded = TRUE ;
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';

$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$cls_functions = new Client_functions($shop);

function verify_webhook($data, $hmac_header)
{   
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_SECRET , true));
	return hash_equals($hmac_header, $calculated_hmac);
}

$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
$data = file_get_contents('php://input');
$verified = verify_webhook($data, $hmac_header);
$topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];

if($verified == true){
    if( $topic_header == "products/update" ) {
        $product = json_decode($data);
			if(!empty($product) && isset($product->id)){
				$shopinfo = $cls_functions->get_store_detail_obj();
				$field_array = array();
				$p_id = '';
				foreach ($product->variants as $i => $variants) {
					  $main_price = ($variants->price != '') ? $variants->price : "";
				}
				$img_src = ($product->image == '') ? '' : $product->image->src;  
				$fields = array(
					'`title`' => $product->title,
					'`image`' =>$img_src,
					'`description`' =>str_replace("'", "\'",$product->body_html),
					'`price`' =>$main_price,
					'`vendor`' =>$product->vendor,
					'`handle`' =>$product->handle,
				);
				$where_query = array(["", "product_id", "=", $product->id]);
						  $comeback = $cls_functions->put_data(TABLE_PRODUCT_MASTER, $fields, $where_query);
			}
    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('product_update-webhook', json_encode($verified) . "  not verified"); 
    http_response_code(401);
    echo "Access Denied main ";
}

?>





