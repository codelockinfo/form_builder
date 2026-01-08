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

    generate_log('OAUTH_CALLBACK', 'Access token obtained', ['shop' => $shop, 'password_length' => strlen($password)]);

    // Get shop information first (needed for both new and existing stores)
    $shopuinfo = shopify_call($password, $shop, "/admin/".CLS_API_VERSIION."/shop.json", [], 'GET');
    $shopuinfo = json_decode($shopuinfo['response']);

    if (!isset($shopuinfo->shop)) {
        generate_log('OAUTH_CALLBACK', 'Failed to retrieve shop info', ['shop' => $shop]);
        die('Error: Failed to retrieve shop information. Please try installing again.');
    }

    // Check if store exists (regardless of status - for reinstallations)
    $where_query = [["", "shop_name", "=", $shop], ["OR", "store_name", "=", $shop]];
    $comeback_client = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
    
    generate_log('OAUTH_CALLBACK', 'Store lookup result', [
        'shop' => $shop,
        'lookup_status' => isset($comeback_client['status']) ? $comeback_client['status'] : 'NOT SET',
        'has_data' => !empty($comeback_client['data']) ? 'YES' : 'NO',
        'store_user_id' => isset($comeback_client['data']['store_user_id']) ? $comeback_client['data']['store_user_id'] : 'N/A',
        'current_status' => isset($comeback_client['data']['status']) ? $comeback_client['data']['status'] : 'N/A',
        'current_shop_name' => isset($comeback_client['data']['shop_name']) ? $comeback_client['data']['shop_name'] : 'N/A',
        'current_store_name' => isset($comeback_client['data']['store_name']) ? $comeback_client['data']['store_name'] : 'N/A'
    ]);

    // Webhooks registration (needed for both new and existing stores)
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

    // Script tag registration (needed for both new and existing stores)
    $asset = ["script_tag" => ["event" => "onload", "src" => "https://codelocksolutions.com/form_builder/assets/js/shopify_front5.js"]];
    shopify_call($password, $shop, "/admin/".CLS_API_VERSIION."/script_tags.json", $asset, 'POST', ['Content-Type: application/json']);

    if ($comeback_client['status'] == 1 && !empty($comeback_client['data'])) {
        // Store exists - UPDATE (reinstallation scenario)
        $store_user_id = isset($comeback_client['data']['store_user_id']) ? intval($comeback_client['data']['store_user_id']) : 0;
        $existing_shop_name = isset($comeback_client['data']['shop_name']) ? $comeback_client['data']['shop_name'] : '';
        $existing_store_name = isset($comeback_client['data']['store_name']) ? $comeback_client['data']['store_name'] : '';
        
        generate_log('OAUTH_CALLBACK', 'Store exists - updating access token and reactivating', [
            'shop' => $shop,
            'store_user_id' => $store_user_id,
            'current_status' => isset($comeback_client['data']['status']) ? $comeback_client['data']['status'] : 'N/A',
            'existing_shop_name' => $existing_shop_name,
            'existing_store_name' => $existing_store_name,
            'new_password_length' => strlen($password),
            'new_password_preview' => substr($password, 0, 10) . '...'
        ]);
        
        // Update store with new access token and reactivate
        $update_data = [
            '`password`' => $password, 
            '`updated_on`' => DATE, 
            '`updated_at`' => DATE,
            '`status`' => '1',
            '`is_demand_accept`' => '1',
            // Update shop info in case it changed
            '`email`' => $shopuinfo->shop->email ?? '',
            '`store_idea`' => $shopuinfo->shop->plan_name ?? '',
            '`address11`' => $shopuinfo->shop->address1 ?? '',
            '`address22`' => $shopuinfo->shop->address2 ?? '',
            '`city`' => $shopuinfo->shop->city ?? '',
            '`country_name`' => $shopuinfo->shop->country_name ?? '',
            '`price_pattern`' => isset($shopuinfo->shop->price_pattern) ? htmlspecialchars(strip_tags($shopuinfo->shop->price_pattern), ENT_QUOTES, "ISO-8859-1") : '',
            '`zip`' => $shopuinfo->shop->zip ?? '',
            '`timezone`' => $shopuinfo->shop->timezone ?? '',
        ];
        
        // Use store_user_id if available (most reliable), otherwise use shop_name/store_name
        if ($store_user_id > 0) {
            $update_where = [["", "store_user_id", "=", $store_user_id]];
            generate_log('OAUTH_CALLBACK', 'Using store_user_id for update', ['store_user_id' => $store_user_id]);
        } else {
            // Fallback: use the same OR condition as the SELECT query
            $update_where = [["", "shop_name", "=", $shop], ["OR", "store_name", "=", $shop]];
            generate_log('OAUTH_CALLBACK', 'Using shop_name/store_name for update', ['shop' => $shop]);
        }
        
        generate_log('OAUTH_CALLBACK', 'Update data prepared', [
            'update_fields' => array_keys($update_data),
            'password_included' => isset($update_data['`password`']) ? 'YES' : 'NO',
            'where_clause' => json_encode($update_where)
        ]);
        
        $update_result = $cls_functions->put_data(TABLE_USER_SHOP, $update_data, $update_where);
        
        $update_result_decoded = json_decode($update_result, true);
        generate_log('OAUTH_CALLBACK', 'Update result', [
            'shop' => $shop,
            'result_status' => isset($update_result_decoded['status']) ? $update_result_decoded['status'] : 'NOT SET',
            'affected_rows' => isset($update_result_decoded['data']['affected_rows']) ? $update_result_decoded['data']['affected_rows'] : 'N/A',
            'query_status' => isset($update_result_decoded['data']['query_status']) ? $update_result_decoded['data']['query_status'] : 'N/A',
            'full_result' => $update_result
        ]);
        
        // Verify the update by querying the store again
        $verify_query = [["", "shop_name", "=", $shop], ["OR", "store_name", "=", $shop]];
        $verify_result = $cls_functions->select_result(TABLE_USER_SHOP, 'password, status', $verify_query, ['single' => true]);
        
        if ($verify_result['status'] == 1 && !empty($verify_result['data'])) {
            $updated_password = isset($verify_result['data']['password']) ? $verify_result['data']['password'] : '';
            $updated_status = isset($verify_result['data']['status']) ? $verify_result['data']['status'] : '';
            $password_match = ($updated_password === $password);
            
            generate_log('OAUTH_CALLBACK', 'Verification after update', [
                'shop' => $shop,
                'password_updated' => $password_match ? 'YES' : 'NO',
                'status_updated' => ($updated_status == '1') ? 'YES' : 'NO',
                'stored_password_length' => strlen($updated_password),
                'stored_password_preview' => substr($updated_password, 0, 10) . '...'
            ]);
            
            if ($password_match && $updated_status == '1') {
                generate_log('OAUTH_CALLBACK', '✅ Store successfully updated with new access token', ['shop' => $shop]);
            } else {
                generate_log('OAUTH_CALLBACK', '⚠️ WARNING: Update verification failed - password or status mismatch', [
                    'shop' => $shop,
                    'password_match' => $password_match,
                    'status_match' => ($updated_status == '1')
                ]);
            }
        } else {
            generate_log('OAUTH_CALLBACK', '⚠️ WARNING: Could not verify update - store not found after update', ['shop' => $shop]);
        }
        
        header('Location: ' . SITE_CLIENT_URL . '?shop=' . $shop);
        exit;
    }

    // Store doesn't exist - REGISTER NEW STORE
    generate_log('OAUTH_CALLBACK', 'Store does not exist - registering new store', ['shop' => $shop]);

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

    generate_log('OAUTH_CALLBACK', 'New store successfully registered', ['shop' => $shop]);
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
