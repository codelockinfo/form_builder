<?php

header("Access-Control-Allow-Origin: *");
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';

$is_bad_shop = 0;
$comeback = array('result' => 'fail', 'message' => 'Opps! Bad request call!');

if (isset($_POST['routine_name']) && $_POST['routine_name'] != '' && isset($_POST['store']) && $_POST['store'] != '') {
 
    $obj_Client_functions = new Client_functions($_POST['store']);
    $current_user = $obj_Client_functions->get_store_detail_obj();
    if (!empty($current_user)) {
        $comeback = call_user_func(array($obj_Client_functions,$_POST['routine_name']));
        echo json_encode($comeback);
        exit;
    } else {
        $is_bad_shop++;
        $comeback['message'] = "Opps! Your shop is not authenticated";
        $comeback['code'] = "403"; 
    }
} else {
    $is_bad_shop++;
}
if ($is_bad_shop > 0) {
    echo json_encode($comeback);
    exit;
}
