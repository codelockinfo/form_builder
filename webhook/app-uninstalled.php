<?php
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
generate_log('Webhook verified: ' , var_export($verified, true)); //check error.log to see the result
// define('SHOPIFY_APP_SECRET', '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'); // Replace with your SECRET KEY

// function verify_webhook($data, $hmac_header) {
//     $calculated_hmac = base64_encode(hash_hmac('sha256',$data,SHOPIFY_APP_SECRET,true));
//     generate_log('testingwebhook', $calculated_hmac . "   calculated_hmac");
//     return hash_equals($hmac_header, $calculated_hmac);
//     // return ($hmac_header == $calculated_hmac);
// }
$topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];
$shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
// $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
// generate_log('testingwebhook', $hmac_header . "  hmac_header ");
// $datas = file_get_contents('php://input');
// $verified = verify_webhook($datas, $hmac_header);
//     generate_log('testingwebhook',var_export($verified, true)); //check error.log to see the result
  


if($verified == true){
   generate_log('testingwebhook', json_encode($verified) . "  verified"); 
    if( $topic_header == "app/uninstalled" ) {
        $shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
        $fields = array(
            'status' => '0',
            'is_demand_accept' => '0'
        );
        $where_query = array(["", "shop_name", "=",$shop]);
        generate_log('resulr', json_encode($where_query) . "were  result");
        $data =  $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query);
        generate_log('resulr', $data);
    }
    else {
        echo "Access Denied";
        exit;
    }    
}
else {
    generate_log('testingwebhook', json_encode($verified) . "  not verified"); 
    echo "Access Denied main ";
}

?>

