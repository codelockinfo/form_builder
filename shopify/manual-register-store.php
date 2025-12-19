<?php
/**
 * Manual Store Registration
 * 
 * Use this to manually register a store for testing
 * Usage: /shopify/manual-register-store.php?shop=shop-name.myshopify.com&password=access_token
 * 
 * WARNING: This is for testing only. In production, stores should be registered via OAuth.
 */

header('Content-Type: text/html; charset=utf-8');

require_once(__DIR__ . '/../append/connection.php');

if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
}

require_once ABS_PATH . '/cls_shopifyapps/cls_shopify_call.php';

$shop = isset($_GET['shop']) ? trim($_GET['shop']) : '';
$password = isset($_GET['password']) ? trim($_GET['password']) : '';

if (empty($shop)) {
    die('<h1>Error: Shop parameter required</h1><p>Usage: ?shop=shop-name.myshopify.com&password=access_token</p>');
}

// Normalize shop name
$shop = preg_replace('#^https?://#', '', $shop);
$shop = rtrim($shop, '/');
$shop = strtolower($shop);

echo "<h1>Manual Store Registration</h1>";
echo "<h2>Shop: $shop</h2>";

// Check if store already exists
$cls_functions = new common_function($shop);
$where_query = array(["", "shop_name", "=", "$shop"], ["OR", "store_name", "=", "$shop"]);
$comeback_client = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);

if ($comeback_client['status'] == 1) {
    echo "<p style='color: orange;'>⚠️ Store already exists in database</p>";
    echo "<p>Store User ID: " . $comeback_client['data']['store_user_id'] . "</p>";
    
    if (!empty($password)) {
        echo "<p>Updating password...</p>";
        $update_data = array('password' => $password, 'updated_on' => DATE, 'status' => '1');
        $update_where = array(["", "shop_name", "=", "$shop"]);
        $result = $cls_functions->put_data(TABLE_USER_SHOP, $update_data, $update_where);
        echo "<p style='color: green;'>✅ Password updated</p>";
    }
    exit;
}

// If no password provided, try to get shop info (will fail, but shows what's needed)
if (empty($password)) {
    echo "<p style='color: red;'>❌ Password (access token) required for new store registration</p>";
    echo "<p>To register a new store, you need:</p>";
    echo "<ol>";
    echo "<li>Go through OAuth flow to get access token</li>";
    echo "<li>Or provide password parameter: ?shop=$shop&password=access_token</li>";
    echo "</ol>";
    exit;
}

// Get shop info from Shopify
echo "<p>Fetching shop information from Shopify...</p>";
$shopuinfo = shopify_call($password, $shop, "/admin/api/2022-10/shop.json", array(), 'GET');
$shopuinfo = json_decode($shopuinfo['response']);

if (!isset($shopuinfo->shop) || empty($shopuinfo->shop)) {
    echo "<p style='color: red;'>❌ Failed to retrieve shop info from Shopify</p>";
    echo "<p>Response: <pre>" . print_r($shopuinfo, true) . "</pre></p>";
    exit;
}

echo "<p style='color: green;'>✅ Shop info retrieved successfully</p>";

// Prepare store information
$store_information = array(
    'email' => isset($shopuinfo->shop->email) ? $shopuinfo->shop->email : '',
    'shop_name' => $shop,
    'store_name' => $shop, 
    'password' => $password,
    'store_idea' => isset($shopuinfo->shop->plan_name) ? $shopuinfo->shop->plan_name : '',
    'address11' => isset($shopuinfo->shop->address1) ? $shopuinfo->shop->address1 : '',
    'address22' => isset($shopuinfo->shop->address2) ? $shopuinfo->shop->address2 : '',
    'city' => isset($shopuinfo->shop->city) ? $shopuinfo->shop->city : '',
    'country_name' => isset($shopuinfo->shop->country_name) ? $shopuinfo->shop->country_name : '',
    'price_pattern' => isset($shopuinfo->shop->price_pattern) ? htmlspecialchars(strip_tags($shopuinfo->shop->price_pattern), ENT_QUOTES, "ISO-8859-1") : '',
    'zip' => isset($shopuinfo->shop->zip) ? $shopuinfo->shop->zip : '',
    'timezone' => isset($shopuinfo->shop->timezone) ? $shopuinfo->shop->timezone : '',
);

echo "<h3>Registering store...</h3>";
$result = $cls_functions->registerNewClientApi($store_information);

if ($result === false) {
    echo "<p style='color: red;'>❌ Failed to register store</p>";
    echo "<p>Check error logs for details.</p>";
    exit;
}

echo "<p style='color: green;'>✅ Store registration initiated</p>";

// Verify store was saved
$verify_query = array(["", "shop_name", "=", "$shop"]);
$verify = $cls_functions->select_result(TABLE_USER_SHOP, 'store_user_id', $verify_query, ['single' => true]);

if ($verify['status'] == 1) {
    echo "<p style='color: green;'>✅ Store successfully registered!</p>";
    echo "<p>Store User ID: " . $verify['data']['store_user_id'] . "</p>";
    echo "<p><a href='test-store-registration.php?shop=$shop'>Test Store Registration</a></p>";
} else {
    echo "<p style='color: red;'>❌ Store registration verification failed</p>";
    echo "<p>Registration returned success but store not found in database.</p>";
}

