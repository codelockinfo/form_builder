
<?php
if ($ologin->isUserLoggedIn() == true) {
    header('Location: ' . SITE_CLIENT_URL);
    die();
}
include_once ('../append/connection.php');
include_once ('cls_functions.php');
//include_once('login_header.php');
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo CLS_SITE_NAME; ?></title>
        <link rel="stylesheet" href="<?php echo main_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/shopify_client.css'); ?>" />
        <link rel="stylesheet" href="<?php echo main_url('assets/css/polaris.min.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/spectrum.css'); ?>" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo main_url('assets/css/owl.carousel.min.css'); ?>" />
        <link rel="stylesheet" href="<?php echo main_url('assets/css/font-awesome/css/font-awesome.css'); ?>" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo main_url('assets/css/bootstrap-tagsinput.css'); ?>" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo main_url('assets/css/select2.min.css'); ?>" rel="stylesheet"> 
        <script src="<?php echo main_url('assets/js/jquery-2.1.1.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/jquery-ui.min.js'); ?>"></script>
        <?php // include_once('../cls_shopifyapps/change_charge.php'); ?>    
        <script src="<?php echo main_url('assets/js/spectrum.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/owl.carousel.min.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/popper.min.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/star_rating.js'); ?>"></script>
        <script src="<?php echo main_url('assets/js/bootstrap.min.js'); ?>"></script>
	<script src="<?php echo main_url('assets/js/ckeditor/ckeditor.js'); ?>"></script>
	<script src="<?php echo main_url('assets/js/bootstrap-tagsinput.min.js'); ?>"></script>
	<script src="<?php echo main_url('assets/js/select2.full.min.js'); ?>"></script>
    </head>
    <body>