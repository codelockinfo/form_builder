<?php
include_once '../append/connection.php';
include_once ABS_PATH . '/user/cls_functions.php';
require_once '../cls_shopifyapps/config.php';

$shop = isset($_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN']) ? $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'] : '';
$hmac_header = isset($_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256']) ? $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'] : '';

function verify_webhook($data, $hmac_header) {
    if (empty($hmac_header) || empty($data)) {
        return false;
    }
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_SECRET, true));
    return hash_equals($hmac_header, $calculated_hmac);
}

$data = file_get_contents('php://input');
$verified = verify_webhook($data, $hmac_header);

if ($verified) {
    generate_log('customers_redact', 'Received customer redact webhook for shop: ' . $shop);
    // Process the customer redaction request here.
    // Remove customer data from DB.
    http_response_code(200);
} else {
    generate_log('customers_redact', 'Verification failed for shop: ' . $shop);
    http_response_code(401);
}
?>