<?php

// Disable error display for AJAX to avoid corrupting JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header("Access-Control-Allow-Origin: *");
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';

$is_bad_shop = 0;
$comeback = array('result' => 'fail', 'message' => 'Opps! Bad request call!');
if (isset($_POST['routine_name']) && $_POST['routine_name'] != '' && isset($_POST['store']) && $_POST['store'] != '') {
    $obj_Client_functions = new Client_functions($_POST['store']);
    $current_user = $obj_Client_functions->get_store_detail_obj();

    if (!empty($current_user)) {
        // Capture any output that might be generated (PHP warnings/notices)
        ob_start();
        $comeback = call_user_func(array($obj_Client_functions,$_POST['routine_name']));
        $output = ob_get_clean();
        
        // Only output JSON, ignore any PHP warnings/notices
        header('Content-Type: application/json');
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
