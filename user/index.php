<?php
require_once('include.php');
include_once('cls_header.php');
$common_function = new common_function();
include 'create-new-form.php';
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
//    header('Location: dashboard.php?key='.$key.'&store=' . $store);
//    exit;
//} elseif (!isset($_GET['destroy'])) {
//    include 'welcome.php';
//} else {
//    include 'login.php';
//    exit;
//}
?>
