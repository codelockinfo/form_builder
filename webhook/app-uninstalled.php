<?php
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
    if( $topic_header == "app/uninstalled" ) {
        $shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
        $fields = array(
            'status' => '0',
            'is_demand_accept' => '0'
        );
        $where_query = array(["", "shop_name", "=",$shop]);
        $data =  $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query);
    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('uninstall-webhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>

