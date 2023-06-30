<?php
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
include_once ABS_PATH . '/cls_shopifyapps/config.php';

$default_shop = 'dashboardmanage.myshopify.com';
$store = isset($_GET['store']) ? $_GET['store'] : $default_shop;
if ((isset($store) && $store != '')) {
    $functions = new Client_functions($store);
    $current_user = $functions->get_store_detail_obj();
} else {
    header('Location: https://www.shopify.com/admin/apps');
    exit;
}
$view = (isset($_GET["view"]) && $_GET["view"]) ? $_GET["view"] : FALSE;
?>  
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
       

        <title><?php echo CLS_SITE_NAME; ?></title>
        <link rel="stylesheet" href="<?php echo main_url('assets/css/polaris_style.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/customstyle.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/owl.carousel.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/owl.carousel.min.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/owl.theme.default.min.css'); ?>" rel="stylesheet">
       
        <script> var store = "<?php echo $store; ?>"; </script>
            <?php  $_SESSION['store'] = $store; ?>
        <script src="<?php echo main_url('assets/js/jquery3.6.4.min.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/style.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/owl.carousel.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/owl.carousel.min.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/ckeditor.js'); ?>"></script>
   