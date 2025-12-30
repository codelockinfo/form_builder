<?php
ob_start();
include_once('cls_header.php');
//include_once('../append/session.php');
$common_function = new common_function();

if (isset($_GET['shop']) && $_GET['shop'] != '') {
    include_once('dashboard_header.php');
    include("create-new-form.php");
} else {
    echo "Store not found";die;
    header('Location:https://accounts.shopify.com/store-login');
}
?>


<?php include_once('dashboard_footer.php'); ?>