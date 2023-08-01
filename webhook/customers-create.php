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
$customer = json_decode($data);
$verified = verify_webhook($data, $hmac_header, $cls_functions);

if($verified == true){
    if( $topic_header == "customers/create" ) {
        if(!empty($customer)){
			$shopinfo = $cls_functions->get_store_detail_obj();
			$customerid = isset($customer->id) ? $customer->id : '';
			$store_user_id = isset($shopinfo["store_user_id"]) ? $shopinfo["store_user_id"] : '';
			if($store_user_id == ''){
				generate_log('customer_create-webhook' , "STORE USER ID"); 
			}
			$where_query = array(["", "customer_id", "=", "$customerid"], ["AND", "store_user_id", "=", "$store_user_id"]);
			$comeback = $cls_functions->select_result(TABLE_CUSTOMER_MASTER, '*', $where_query);
			$customerId = isset($comeback['data']->customer_id) ? $comeback['data']->customer_id : '';
			if(empty($customerId)){
				$field_array = array();
				// foreach ($product->variants as $i => $variants) {
				// 	$main_price = ($variants->price != '') ? $variants->price : "";
				// }
                $customerid = isset($customer->id) ? $customer->id : '';   
				$field_array = array(
					'`customer_id`' => $customerid,
				  	'`email`' =>$customer->email,
				  	'`first_name`' =>$customer->first_name,
				  	'`last_name`' =>$customer->last_name,
				  	'`phone`' =>$customer->phone,
				  	'`orders_count`' =>$customer->orders_count,
				  	'`currency`' =>$customer->currency,
				  	'`address1`' =>$customer->default_address->address1,
				  	'`address2`' =>$customer->default_address->address2,
				  	'`city`' =>$customer->default_address->city,
				  	'`province`' =>$customer->default_address->Jharkhand,
				  	'`country`' =>$customer->default_address->India,
				  	'`zip`' =>$customer->default_address->zip,
				  	'`store_user_id`' => $store_user_id,
				  	'`created_at`' => date('Y-m-d H:i:s'),
				  	'`updated_at`' => date('Y-m-d H:i:s'),
				);
				$sql_prod_id = $cls_functions->post_data(TABLE_CUSTOMER_MASTER, array($field_array));
			}
		}
    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('customer_create-webhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>