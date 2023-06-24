<?php
include_once 'append/connection.php';

if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
}

require_once(ABS_PATH . '/cls_shopifyapps/config.php');
require_once(ABS_PATH . '/cls_shopifyapps/cls_shopify.php');
require_once(ABS_PATH . '/cls_shopifyapps/cls_shopify_call.php');

$__webhook_arr = array(
    'products/create'
);
 if (!empty($__webhook_arr)) {
                foreach ($__webhook_arr as $topic) {
                    $file_name = str_replace('/', '-', $topic) . '.php';
                    $params = '{"webhook": {"topic":"' . $topic . '",
                                "address":"' . SITE_PATH . 'webhook/' . $file_name . '",
                                "format":"json"
				}}';
				
                    $responce = $cls_functions->register_webhook($shopify_url, $params, $password);
                  
                }
            }
// $shopuinfo = shopify_call($password, $shop, "admin/api/2021-07/webhooks.json", array(), 'POST');
// $shopuinfo = json_decode($shopuinfo['response']);
// echo print_r($shopuinfo);
 ?>