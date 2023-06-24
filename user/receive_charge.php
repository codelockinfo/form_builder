<?php
include_once ABS_PATH . '/append/connection.php';
include_once ABS_PATH.'/user/cls_functions.php';
include_once ABS_PATH.'/cls_shopifyapps/config.php';
include_once '../append/en.php';
$cls_functions = new Client_functions($_REQUEST['shop']);
if (isset($_REQUEST['cls_price_id']) && $_REQUEST['cls_price_id'] != "") {
    $invoice_activate = $cls_functions->charge_activate($_REQUEST['cls_price_id']);
    if ($invoice_activate['flg'] == 1) {
        header('Location: https://' . $invoice_activate['store_name'] . '/admin/apps/' . CLS_SHOPIFY_API_KEY.'?shop=' . $_REQUEST['shop']);
    }else {
        header('Location: ' . CLS_SITE_URL . 'decline.php?shop=' . $_REQUEST['shop']);
    }
    exit;
}