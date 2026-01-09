<?php
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
include_once ABS_PATH . '/cls_shopifyapps/config.php';

$default_shop = 'dashboardmanage.myshopify.com';
$store = isset($_GET['shop']) ? $_GET['shop'] : $default_shop;
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
        <link rel="icon" type="image/webp" href="<?php echo main_url('assets/images/logo.webp'); ?>">
        <link rel="shortcut icon" type="image/webp" href="<?php echo main_url('assets/images/logo.webp'); ?>">
        <link rel="apple-touch-icon" href="<?php echo main_url('assets/images/logo.webp'); ?>">

        <title><?php echo CLS_SITE_NAME; ?></title>
        <link rel="stylesheet" href="<?php echo main_url('assets/css/polaris_style1.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/customstyle1.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/style1.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/owl.carousel.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/owl.carousel.min.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/owl.theme.default.min.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/style_create-new-for1.css'); ?>" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <script> var store = "<?php echo $store; ?>"; </script>
            <?php  $_SESSION['store'] = $store; ?>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script> -->
        
        <script src="<?php echo main_url('assets/js/jquery3.6.4.min.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/jquery-ui.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/ckeditor/ckeditor.js'); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="<?php echo main_url('assets/js/owl.carousel.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/owl.carousel.min.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/style2.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/shopify_client4.js'); ?>"></script>
  
   