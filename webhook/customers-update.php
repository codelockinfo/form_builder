<?php

$__multiLanguageNotNeeded = TRUE ;
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';

$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$cls_functions = new Client_functions($shop);

function verify_webhook($data, $hmac_header, $cls_functions)
{   
	$where_query = array(["", "status", "=", "1"]);
	$comeback= $cls_functions->select_result(CLS_TABLE_THIRDPARTY_APIKEY, '*',$where_query);
	$SHOPIFY_SECRET = (isset($comeback['data'][2]['thirdparty_apikey']) && $comeback['data'][2]['thirdparty_apikey'] !== '') ? $comeback['data'][2]['thirdparty_apikey'] : '';
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $SHOPIFY_SECRET , true));
	return hash_equals($hmac_header, $calculated_hmac);
}

$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
$data = file_get_contents('php://input');
$verified = verify_webhook($data, $hmac_header,$cls_functions);
$topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];

if($verified == true){
    if( $topic_header == "customers/update" ) {
        $customer = json_decode($data);
			if(!empty($customer) && isset($customer->id)){
				$field_array = array();
				$field_array = array(
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
				);
				$where_query = array(["", "customer_id", "=", $customer->id]);
				$comeback = $cls_functions->put_data(TABLE_CUSTOMER_MASTER, $field_array, $where_query);
			}
    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('customer_update-webhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>





