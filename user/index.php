<?php

error_reporting(E_ALL);          // Report all PHP errors
ini_set('display_errors', 1);    // Display errors on the page (for development)
ini_set('log_errors', 1);

// Post/Redirect/Get pattern to prevent "Confirm Form Resubmission" popup on refresh
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_SERVER['HTTP_X_REQUESTED_WITH']) && !isset($_POST['routine_name'])) {
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

require_once('include.php');
include_once('cls_header.php');
$common_function = new common_function();
include 'dashboard.php';
die;

 /*  BACKUP  */
//function generateRandomString($length = 10) {
//    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
//    $charactersLength = strlen($characters);
//    $randomString = $key = '';
//    for ($i = 0; $i < $length; $i++) {
//        $key .= $characters[rand(0, $charactersLength - 1)];
//    }
//    return $key;
//}
//
//$key = generateRandomString($length = 10);
//
//$mysql_date = date('Y-m-d H:i:s');
//$fields_arr = array(
//    'url_key' => $key,
//    '`created_at`' => $mysql_date,
//    '`updated_at`' => $mysql_date
//);
//$common_function->post_data(TABLE_CUSTOMIZE, array($fields_arr));
//
//if ($ologin->isUserLoggedIn() == true) {
//    header('Location: dashboard.php?key='.$key.'&shop=' . $store);
//    exit;
//} elseif (!isset($_GET['destroy'])) {
//    include 'welcome.php';
//} else {
//    include 'login.php';
//    exit;
//}
?>
