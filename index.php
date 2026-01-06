<?php
// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

require_once './core/logger.php';
ini_set('error_log', __DIR__ . '/oauth-debug.log');

// --------------------
// Utility: logging function
// --------------------

// --------------------
// App proxy handling
// --------------------
$request_uri = $_SERVER['REQUEST_URI'];
if (strpos($request_uri, 'form-builder') !== false) {
    generate_log('APP_PROXY', "Redirecting to app-proxy.php");
    include_once ABS_PATH.'/shopify/app-proxy.php';
    exit;
}

// --------------------
// DB connection and shop headers
// --------------------
include_once './append/connection.php';
$shop = isset($_GET['shop']) ? trim($_GET['shop']) : '';
if ($shop) {
    header('X-Frame-Options:ALLOW-FROM ' . $shop);
    header("Content-Security-Policy: frame-ancestors " . $shop);
} else {
    header('X-Frame-Options:SAMEORIGIN');
}

// --------------------
// Include common functions
// --------------------
if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
}
require_once ABS_PATH . '/cls_shopifyapps/config.php';
require_once ABS_PATH . '/cls_shopifyapps/cls_shopify.php';
require_once ABS_PATH . '/cls_shopifyapps/cls_shopify_call.php';

// --------------------
// Get API keys
// --------------------
$temp_cls = new common_function('');
$where_query = array(["", "status", "=", "1"]);
$comeback = $temp_cls->select_result(CLS_TABLE_THIRDPARTY_APIKEY, '*', $where_query);
$CLS_API_KEY = $comeback['data'][1]['thirdparty_apikey'] ?? '';
$SHOPIFY_SECRET = $comeback['data'][2]['thirdparty_apikey'] ?? '';

// --------------------
// Webhook topics
// --------------------
$__webhook_arr = [
    'app/uninstalled',
    'products/create',
    'products/delete',
    'products/update',
    'collections/create',
    'collections/update',
    'collections/delete',
    'customers/create',
    'customers/update',
    'customers/delete'
];

// --------------------
// OAuth error handling
// --------------------
if (isset($_GET['error'])) {
    generate_log('OAUTH_ERROR', $_GET['error'], $_GET);
    die('OAuth Error: ' . htmlspecialchars($_GET['error_description'] ?? $_GET['error']));
}

// --------------------
// Normalize shop
// --------------------
if ($shop) {
    $shop = preg_replace('#^https?://#', '', $shop);
    $shop = rtrim($shop, '/');
    $shop = strtolower($shop);
}

// --------------------
// Handle OAuth callback
// --------------------
if (isset($_GET['code'])) {
    if (empty($shop)) {
        generate_log('OAUTH_CALLBACK', 'Shop parameter is missing in callback', $_GET);
        die('OAuth Callback Error: Shop parameter missing. Check redirect URI in Shopify Partner Dashboard.');
    }

    $cls_functions = new common_function($shop);
    $shopifyClient = new ShopifyClient($shop, "", $CLS_API_KEY, $SHOPIFY_SECRET);
    $password = $shopifyClient->getEntrypassword($_GET['code']);

    if (empty($password)) {
        generate_log('OAUTH_CALLBACK', 'Failed to get access token', ['shop' => $shop]);
        die('Error: Failed to authenticate with Shopify. Please try installing again.');
    }

    generate_log('OAUTH_CALLBACK', 'Access token obtained', ['shop' => $shop]);

    // Check if store exists
    $where_query = [["", "shop_name", "=", $shop], ["OR", "store_name", "=", $shop]];
    $comeback_client = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);

    if ($comeback_client['status'] == 1) {
        $update_data = ['password' => $password, 'updated_on' => DATE, 'status' => '1'];
        $update_where = [["", "shop_name", "=", $shop]];
        $cls_functions->put_data(TABLE_USER_SHOP, $update_data, $update_where);
        header('Location: ' . SITE_CLIENT_URL . '?shop=' . $shop);
        exit;
    }

    // Register new store
    $shopuinfo = shopify_call($password, $shop, "/admin/".CLS_API_VERSIION."/shop.json", [], 'GET');
    $shopuinfo = json_decode($shopuinfo['response']);

    if (!isset($shopuinfo->shop)) {
        generate_log('OAUTH_CALLBACK', 'Failed to retrieve shop info', ['shop' => $shop]);
        die('Error: Failed to retrieve shop information. Please try installing again.');
    }

    // Webhooks registration
    $baseurl = "https://" . $CLS_API_KEY . ":" . $password . "@" . $shop . "/";
    foreach ($__webhook_arr as $topic) {
        $file_name = str_replace('/', '-', $topic) . '.php';
        $params = json_encode([
            'webhook' => [
                'topic' => $topic,
                'address' => "https://codelocksolutions.com/form_builder/webhook/$file_name",
                'format' => "json"
            ]
        ]);
        $shopify_url = $baseurl . "admin/api/2022-10/webhooks.json";
        $cls_functions->register_webhook($shopify_url, $params, $password);
    }

    // Script tag registration
    $asset = ["script_tag" => ["event" => "onload", "src" => "https://codelocksolutions.com/form_builder/assets/js/shopify_front1.js"]];
    shopify_call($password, $shop, "/admin/".CLS_API_VERSIION."/script_tags.json", $asset, 'POST', ['Content-Type: application/json']);

    // Register store in DB
    $store_information = [
        'email' => $shopuinfo->shop->email ?? '',
        'shop_name' => $shop,
        'store_name' => $shop,
        'password' => $password,
        'store_idea' => $shopuinfo->shop->plan_name ?? '',
        'address11' => $shopuinfo->shop->address1 ?? '',
        'address22' => $shopuinfo->shop->address2 ?? '',
        'city' => $shopuinfo->shop->city ?? '',
        'country_name' => $shopuinfo->shop->country_name ?? '',
        'price_pattern' => isset($shopuinfo->shop->price_pattern) ? htmlspecialchars(strip_tags($shopuinfo->shop->price_pattern), ENT_QUOTES, "ISO-8859-1") : '',
        'zip' => $shopuinfo->shop->zip ?? '',
        'timezone' => $shopuinfo->shop->timezone ?? '',
    ];

    $result = $cls_functions->registerNewClientApi($store_information);
    if ($result === false) {
        generate_log('OAUTH_CALLBACK', 'Failed to register store', ['shop' => $shop, 'errors' => $cls_functions->errors]);
        die('Error: Failed to register store. Please check error logs.');
    }

    header('Location: ' . SITE_CLIENT_URL . '?shop=' . $shop);
    exit;
}

// --------------------
// Initial visit or install redirect
// --------------------
if (empty($shop)) {
    generate_log('URL_TRACKING', 'No shop parameter in GET or POST', ['GET' => $_GET, 'POST' => $_POST]);
    header('Location: https://apps.shopify.com/ReWriter-Mega-Description');
    exit;
}

$shop = preg_replace('#^https?://#', '', $shop);
$shop = rtrim($shop, '/');
$shop = strtolower($shop);

$temp_cls_functions = new common_function('');
$where_query = [["", "shop_name", "=", $shop], ["OR", "store_name", "=", $shop]];
$comeback = $temp_cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);

if ($comeback['status'] == 1) {
    generate_log('INSTALL', 'Store exists, redirecting to client', ['shop' => $shop]);
    header('Location: ' . SITE_CLIENT_URL . '?shop=' . $shop);
    exit;
}

// Redirect to Shopify OAuth
$state = bin2hex(random_bytes(16));
$redirect_uri = SITE_PATH . '/index.php'; // Must match Shopify Partner Dashboard exactly
$params = [
    'client_id' => $CLS_API_KEY,
    'scope' => SHOPIFY_SCOPE,
    'redirect_uri' => $redirect_uri,
    'state' => $state,
    'grant_options[]' => 'per-user'
];

$install_url = "https://" . $shop . "/admin/oauth/authorize?" . http_build_query($params);
generate_log('INSTALL', 'Redirecting to Shopify OAuth', ['shop' => $shop, 'install_url' => $install_url]);

// If embedded app, redirect out of iframe
echo '<!DOCTYPE html><html><head><script>top.location.href = "' . $install_url . '";</script></head><body>Redirecting to install...</body></html>';
exit;
?>
