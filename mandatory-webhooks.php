<?php

include_once 'append/connection.php';
/* include main client function file */
include_once ABS_PATH . '/user/cls_functions.php';

if(MODE == 'local'){
    $shop_info = '{"store_name": "happyeventsurat.myshopify.com"}';
}else{
    $shop_info = file_get_contents('php://input');
}

/* shop info array */
$shop_info = json_decode($shop_info, TRUE);

$shop = $shop_info['store_name'];
$CF_obj = new Client_functions($shop);

$selected_field = 'store_name,email';
$where = array(['', 'store_name', '=', $shop_info['store_name']]);

$table_shop_info = $CF_obj->select_result(TABLE_USER_SHOP, $selected_field, $where);
if($table_shop_info['status'] == 1 && !empty($table_shop_info['data'])){
    $table_shop_info = $table_shop_info['data'];
    
    $fields = array(
        'shop_name' => '', 
        'store_name' => '', 
        'address11' => '',
        'address22' => '',
        'city' => '',
        'country_name' => '',
        'zip' => '',
        'timezone' => '',        
        'domain' => '',
        'mobile_no' => '',/*phone number*/
        'store_holder' => '',/*shop owner*/
        'cash' => '',/*currency*/
        'price_pattern' => '',/*money format*/

    );
    
    $where = array(['', 'store_name', '=', $table_shop_info->store_name]);
    $returrnn = $CF_obj->put_data(TABLE_USER_SHOP, $fields, $where);
    
}

?>