<?php
// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Immediate logging to verify file is being called
$log_file = __DIR__ . '/oauth-debug.log';
$initial_log = "\n" . str_repeat("=", 80) . "\n";
$initial_log .= "[" . date('Y-m-d H:i:s') . "] ========== INDEX.PHP LOADED ==========\n";
$initial_log .= "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
$initial_log .= "GET params: " . json_encode($_GET) . "\n";
$initial_log .= "Has code param: " . (isset($_GET['code']) ? 'YES' : 'NO') . "\n";
$initial_log .= "Shop param: " . (isset($_GET['shop']) ? $_GET['shop'] : 'NOT SET') . "\n";
file_put_contents($log_file, $initial_log, FILE_APPEND);
error_log("INDEX.PHP: File loaded - URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));

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
    // Direct logging to oauth-debug.log
    $log_file = __DIR__ . '/oauth-debug.log';
    $log_entry = "\n" . str_repeat("=", 80) . "\n";
    $log_entry .= "[" . date('Y-m-d H:i:s') . "] OAUTH_CALLBACK STARTED\n";
    $log_entry .= "Shop: " . ($shop ?: 'NOT SET') . "\n";
    $log_entry .= "Code: " . (isset($_GET['code']) ? substr($_GET['code'], 0, 20) . '...' : 'NOT SET') . "\n";
    $log_entry .= "GET params: " . json_encode($_GET) . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    error_log("OAUTH_CALLBACK: Started for shop: " . $shop);
    
    if (empty($shop)) {
        $error_msg = 'OAuth Callback Error: Shop parameter missing. Check redirect URI in Shopify Partner Dashboard.';
        file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] ERROR: " . $error_msg . "\n", FILE_APPEND);
        error_log("OAUTH_CALLBACK ERROR: " . $error_msg);
        generate_log('OAUTH_CALLBACK', 'Shop parameter is missing in callback', $_GET);
        die($error_msg);
    }

    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Creating common_function and ShopifyClient\n", FILE_APPEND);
    $cls_functions = new common_function($shop);
    $shopifyClient = new ShopifyClient($shop, "", $CLS_API_KEY, $SHOPIFY_SECRET);
    
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Requesting access token...\n", FILE_APPEND);
    $password = $shopifyClient->getEntrypassword($_GET['code']);

    if (empty($password)) {
        $error_msg = 'Failed to get access token';
        file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] ERROR: " . $error_msg . "\n", FILE_APPEND);
        error_log("OAUTH_CALLBACK ERROR: " . $error_msg);
        generate_log('OAUTH_CALLBACK', 'Failed to get access token', ['shop' => $shop]);
        die('Error: Failed to authenticate with Shopify. Please try installing again.');
    }

    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Access token obtained (length: " . strlen($password) . ")\n", FILE_APPEND);
    error_log("OAUTH_CALLBACK: Access token obtained for shop: " . $shop);
    generate_log('OAUTH_CALLBACK', 'Access token obtained', ['shop' => $shop, 'password_length' => strlen($password)]);

    // Get shop information first (needed for both new and existing stores)
    $shopuinfo = shopify_call($password, $shop, "/admin/".CLS_API_VERSIION."/shop.json", [], 'GET');
    $shopuinfo = json_decode($shopuinfo['response']);

    if (!isset($shopuinfo->shop)) {
        generate_log('OAUTH_CALLBACK', 'Failed to retrieve shop info', ['shop' => $shop]);
        die('Error: Failed to retrieve shop information. Please try installing again.');
    }

    // Check if store exists (regardless of status - for reinstallations)
    // Use direct SQL query as primary method to ensure we find the store
    $log_file = __DIR__ . '/oauth-debug.log';
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Starting store lookup for: " . $shop . "\n", FILE_APPEND);
    error_log("OAUTH_CALLBACK: Starting store lookup for: " . $shop);
    
    $comeback_client = array('status' => 0, 'data' => array());
    
    try {
        $conn = $GLOBALS['conn'];
        if (!$conn) {
            file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] ERROR: Database connection not available\n", FILE_APPEND);
            error_log("OAUTH_CALLBACK ERROR: Database connection not available");
        } else {
            $shop_escaped = mysqli_real_escape_string($conn, $shop);
            $direct_query = "SELECT * FROM " . TABLE_USER_SHOP . " WHERE (`shop_name` = '" . $shop_escaped . "' OR `store_name` = '" . $shop_escaped . "') LIMIT 1";
            file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Executing query: " . $direct_query . "\n", FILE_APPEND);
            error_log("OAUTH_CALLBACK: Executing lookup query");
            generate_log('OAUTH_CALLBACK', 'Direct SQL lookup query', ['query' => $direct_query]);
            
            $result = mysqli_query($conn, $direct_query);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $comeback_client = array('status' => 1, 'data' => $row);
                $log_msg = "[" . date('Y-m-d H:i:s') . "] ✅ Store FOUND - store_user_id: " . (isset($row['store_user_id']) ? $row['store_user_id'] : 'N/A') . 
                          ", shop_name: " . (isset($row['shop_name']) ? $row['shop_name'] : 'N/A') . 
                          ", current_password: " . (isset($row['password']) ? substr($row['password'], 0, 20) . '...' : 'N/A') . "\n";
                file_put_contents($log_file, $log_msg, FILE_APPEND);
                error_log("OAUTH_CALLBACK: Store found - store_user_id: " . (isset($row['store_user_id']) ? $row['store_user_id'] : 'N/A'));
                generate_log('OAUTH_CALLBACK', 'Direct SQL lookup SUCCESS', [
                    'store_user_id' => isset($row['store_user_id']) ? $row['store_user_id'] : 'N/A',
                    'shop_name' => isset($row['shop_name']) ? $row['shop_name'] : 'N/A',
                    'store_name' => isset($row['store_name']) ? $row['store_name'] : 'N/A',
                    'status' => isset($row['status']) ? $row['status'] : 'N/A'
                ]);
            } else {
                $log_msg = "[" . date('Y-m-d H:i:s') . "] ❌ Store NOT FOUND - num_rows: " . ($result ? mysqli_num_rows($result) : 0) . 
                          ", error: " . mysqli_error($conn) . "\n";
                file_put_contents($log_file, $log_msg, FILE_APPEND);
                error_log("OAUTH_CALLBACK: Store NOT FOUND for shop: " . $shop);
                generate_log('OAUTH_CALLBACK', 'Direct SQL lookup - Store NOT FOUND', [
                    'shop' => $shop,
                    'num_rows' => $result ? mysqli_num_rows($result) : 0,
                    'error' => mysqli_error($conn)
                ]);
            }
        }
    } catch (Exception $e) {
        $log_msg = "[" . date('Y-m-d H:i:s') . "] EXCEPTION in store lookup: " . $e->getMessage() . "\n";
        file_put_contents($log_file, $log_msg, FILE_APPEND);
        error_log("OAUTH_CALLBACK EXCEPTION: " . $e->getMessage());
        generate_log('OAUTH_CALLBACK', 'Direct SQL lookup exception', ['error' => $e->getMessage()]);
    }
    
    // Fallback to select_result if direct query didn't find it
    if ($comeback_client['status'] != 1 || empty($comeback_client['data'])) {
        generate_log('OAUTH_CALLBACK', 'Falling back to select_result for store lookup');
        $where_query = [["", "shop_name", "=", $shop], ["OR", "store_name", "=", $shop]];
        $comeback_client = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
    }
    
    generate_log('OAUTH_CALLBACK', 'Final store lookup result', [
        'shop' => $shop,
        'lookup_status' => isset($comeback_client['status']) ? $comeback_client['status'] : 'NOT SET',
        'has_data' => !empty($comeback_client['data']) ? 'YES' : 'NO',
        'store_user_id' => isset($comeback_client['data']['store_user_id']) ? $comeback_client['data']['store_user_id'] : 'N/A',
        'current_status' => isset($comeback_client['data']['status']) ? $comeback_client['data']['status'] : 'N/A',
        'current_shop_name' => isset($comeback_client['data']['shop_name']) ? $comeback_client['data']['shop_name'] : 'N/A',
        'current_store_name' => isset($comeback_client['data']['store_name']) ? $comeback_client['data']['store_name'] : 'N/A',
        'current_password' => isset($comeback_client['data']['password']) ? substr($comeback_client['data']['password'], 0, 15) . '...' : 'N/A'
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

    $log_file = __DIR__ . '/oauth-debug.log';
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Checking if store exists...\n", FILE_APPEND);
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] comeback_client status: " . (isset($comeback_client['status']) ? $comeback_client['status'] : 'NOT SET') . "\n", FILE_APPEND);
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] comeback_client has_data: " . (!empty($comeback_client['data']) ? 'YES' : 'NO') . "\n", FILE_APPEND);
    error_log("OAUTH_CALLBACK: Checking store existence - status: " . (isset($comeback_client['status']) ? $comeback_client['status'] : 'NOT SET'));
    
    if ($comeback_client['status'] == 1 && !empty($comeback_client['data'])) {
        // Store exists - UPDATE (reinstallation scenario)
        file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] ✅ STORE EXISTS - Going to UPDATE path\n", FILE_APPEND);
        error_log("OAUTH_CALLBACK: Store exists - going to UPDATE path");
        
        $store_user_id = isset($comeback_client['data']['store_user_id']) ? intval($comeback_client['data']['store_user_id']) : 0;
        $existing_shop_name = isset($comeback_client['data']['shop_name']) ? $comeback_client['data']['shop_name'] : '';
        $existing_store_name = isset($comeback_client['data']['store_name']) ? $comeback_client['data']['store_name'] : '';
        
        file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Store details - store_user_id: " . $store_user_id . ", shop_name: " . $existing_shop_name . "\n", FILE_APPEND);
        
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
        // Use direct SQL with mysqli connection to ensure password is updated correctly
        try {
            $conn = $GLOBALS['conn'];
            $log_file = __DIR__ . '/oauth-debug.log';
            
            if (!$conn) {
                file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] ERROR: Database connection not available\n", FILE_APPEND);
                throw new Exception("Database connection not available");
            }
            
            // Verify connection is working
            if (!mysqli_ping($conn)) {
                file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] ERROR: Database connection ping failed\n", FILE_APPEND);
                throw new Exception("Database connection ping failed");
            }
            
            file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Database connection verified - proceeding with update\n", FILE_APPEND);
            
            // Prepare update data
            $email = $shopuinfo->shop->email ?? '';
            $store_idea = $shopuinfo->shop->plan_name ?? '';
            $address11 = $shopuinfo->shop->address1 ?? '';
            $address22 = $shopuinfo->shop->address2 ?? '';
            $city = $shopuinfo->shop->city ?? '';
            $country_name = $shopuinfo->shop->country_name ?? '';
            $price_pattern = isset($shopuinfo->shop->price_pattern) ? htmlspecialchars(strip_tags($shopuinfo->shop->price_pattern), ENT_QUOTES, "ISO-8859-1") : '';
            $zip = $shopuinfo->shop->zip ?? '';
            $timezone = $shopuinfo->shop->timezone ?? '';
            $updated_at = DATE;
            
            // Escape password for SQL (though we'll use prepared statement)
            $password_escaped = mysqli_real_escape_string($conn, $password);
            
            generate_log('OAUTH_CALLBACK', 'Preparing direct SQL update', [
                'store_user_id' => $store_user_id,
                'shop' => $shop,
                'password_length' => strlen($password),
                'password_preview' => substr($password, 0, 15) . '...'
            ]);
            
            // Use store_user_id if available (most reliable)
            if ($store_user_id > 0) {
                $update_sql = "UPDATE " . TABLE_USER_SHOP . " SET 
                    `password` = '" . mysqli_real_escape_string($conn, $password) . "', 
                    `updated_at` = '" . mysqli_real_escape_string($conn, $updated_at) . "',
                    `status` = '1',
                    `is_demand_accept` = '1',
                    `email` = '" . mysqli_real_escape_string($conn, $email) . "',
                    `store_idea` = '" . mysqli_real_escape_string($conn, $store_idea) . "',
                    `address11` = '" . mysqli_real_escape_string($conn, $address11) . "',
                    `address22` = '" . mysqli_real_escape_string($conn, $address22) . "',
                    `city` = '" . mysqli_real_escape_string($conn, $city) . "',
                    `country_name` = '" . mysqli_real_escape_string($conn, $country_name) . "',
                    `price_pattern` = '" . mysqli_real_escape_string($conn, $price_pattern) . "',
                    `zip` = '" . mysqli_real_escape_string($conn, $zip) . "',
                    `timezone` = '" . mysqli_real_escape_string($conn, $timezone) . "'
                    WHERE `store_user_id` = " . intval($store_user_id);
                
                generate_log('OAUTH_CALLBACK', 'Using store_user_id for update', [
                    'store_user_id' => $store_user_id,
                    'sql_preview' => substr($update_sql, 0, 200) . '...'
                ]);
            } else {
                // Fallback: use shop_name or store_name
                $shop_escaped = mysqli_real_escape_string($conn, $shop);
                $update_sql = "UPDATE " . TABLE_USER_SHOP . " SET 
                    `password` = '" . mysqli_real_escape_string($conn, $password) . "', 
                    `updated_at` = '" . mysqli_real_escape_string($conn, $updated_at) . "',
                    `status` = '1',
                    `is_demand_accept` = '1',
                    `email` = '" . mysqli_real_escape_string($conn, $email) . "',
                    `store_idea` = '" . mysqli_real_escape_string($conn, $store_idea) . "',
                    `address11` = '" . mysqli_real_escape_string($conn, $address11) . "',
                    `address22` = '" . mysqli_real_escape_string($conn, $address22) . "',
                    `city` = '" . mysqli_real_escape_string($conn, $city) . "',
                    `country_name` = '" . mysqli_real_escape_string($conn, $country_name) . "',
                    `price_pattern` = '" . mysqli_real_escape_string($conn, $price_pattern) . "',
                    `zip` = '" . mysqli_real_escape_string($conn, $zip) . "',
                    `timezone` = '" . mysqli_real_escape_string($conn, $timezone) . "'
                    WHERE (`shop_name` = '" . $shop_escaped . "' OR `store_name` = '" . $shop_escaped . "')";
                
                generate_log('OAUTH_CALLBACK', 'Using shop_name/store_name for update', [
                    'shop' => $shop,
                    'sql_preview' => substr($update_sql, 0, 200) . '...'
                ]);
            }
            
            // Execute the update query
            $log_file = __DIR__ . '/oauth-debug.log';
            file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Executing UPDATE query...\n", FILE_APPEND);
            file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] SQL: " . $update_sql . "\n", FILE_APPEND);
            error_log("OAUTH_CALLBACK: Executing UPDATE query");
            
            // Test connection before query
            if (!mysqli_ping($conn)) {
                $log_msg = "[" . date('Y-m-d H:i:s') . "] ERROR: Database connection lost before query\n";
                file_put_contents($log_file, $log_msg, FILE_APPEND);
                error_log("OAUTH_CALLBACK ERROR: Database connection lost");
                throw new Exception("Database connection lost");
            }
            
            // Execute query with error handling
            $execute_result = false;
            $affected_rows = 0;
            $error = '';
            
            try {
                $execute_result = @mysqli_query($conn, $update_sql);
                $error = mysqli_error($conn);
                $affected_rows = mysqli_affected_rows($conn);
            } catch (Exception $e) {
                $error = $e->getMessage();
                $log_msg = "[" . date('Y-m-d H:i:s') . "] EXCEPTION during query: " . $error . "\n";
                file_put_contents($log_file, $log_msg, FILE_APPEND);
                error_log("OAUTH_CALLBACK EXCEPTION: " . $error);
            }
            
            // Log result immediately - use multiple methods to ensure it's written
            $log_msg = "[" . date('Y-m-d H:i:s') . "] ========== UPDATE QUERY RESULT ==========\n";
            $log_msg .= "[" . date('Y-m-d H:i:s') . "] execute_result: " . ($execute_result ? 'TRUE (SUCCESS)' : 'FALSE (FAILED)') . "\n";
            $log_msg .= "[" . date('Y-m-d H:i:s') . "] affected_rows: " . $affected_rows . "\n";
            $log_msg .= "[" . date('Y-m-d H:i:s') . "] mysqli_error: " . ($error ?: 'NONE') . "\n";
            $log_msg .= "[" . date('Y-m-d H:i:s') . "] Connection status: " . (mysqli_ping($conn) ? 'CONNECTED' : 'DISCONNECTED') . "\n";
            
            // Write to log file multiple times to ensure it's saved
            file_put_contents($log_file, $log_msg, FILE_APPEND);
            file_put_contents($log_file, $log_msg, FILE_APPEND); // Write twice to ensure
            error_log("OAUTH_CALLBACK: Update result - execute: " . ($execute_result ? 'SUCCESS' : 'FAILED') . ", affected_rows: " . $affected_rows . ", error: " . ($error ?: 'NONE'));
            
            // Force flush
            if (function_exists('ob_flush')) {
                @ob_flush();
            }
            if (function_exists('flush')) {
                @flush();
            }
            
            // Verify the update by querying the database immediately
            if ($execute_result) {
                $verify_sql = "SELECT store_user_id, password, status FROM " . TABLE_USER_SHOP . " WHERE store_user_id = " . intval($store_user_id) . " LIMIT 1";
                $verify_result = @mysqli_query($conn, $verify_sql);
                if ($verify_result && mysqli_num_rows($verify_result) > 0) {
                    $verify_row = mysqli_fetch_assoc($verify_result);
                    $log_msg = "[" . date('Y-m-d H:i:s') . "] ========== VERIFICATION QUERY ==========\n";
                    $log_msg .= "[" . date('Y-m-d H:i:s') . "] Stored password: " . substr($verify_row['password'], 0, 20) . "...\n";
                    $log_msg .= "[" . date('Y-m-d H:i:s') . "] Expected password: " . substr($password, 0, 20) . "...\n";
                    $log_msg .= "[" . date('Y-m-d H:i:s') . "] Password match: " . ($verify_row['password'] === $password ? 'YES ✅' : 'NO ❌') . "\n";
                    $log_msg .= "[" . date('Y-m-d H:i:s') . "] Status: " . $verify_row['status'] . "\n";
                    file_put_contents($log_file, $log_msg, FILE_APPEND);
                    file_put_contents($log_file, $log_msg, FILE_APPEND); // Write twice
                    error_log("OAUTH_CALLBACK: Verification - Password match: " . ($verify_row['password'] === $password ? 'YES' : 'NO'));
                } else {
                    $log_msg = "[" . date('Y-m-d H:i:s') . "] VERIFICATION FAILED - Could not fetch row after update\n";
                    file_put_contents($log_file, $log_msg, FILE_APPEND);
                    error_log("OAUTH_CALLBACK: Verification failed - could not fetch row");
                }
            } else {
                $log_msg = "[" . date('Y-m-d H:i:s') . "] UPDATE QUERY FAILED - Not running verification\n";
                file_put_contents($log_file, $log_msg, FILE_APPEND);
                error_log("OAUTH_CALLBACK: Update query failed, skipping verification");
            }
            
            generate_log('OAUTH_CALLBACK', 'Direct SQL update result', [
                'shop' => $shop,
                'execute_result' => $execute_result ? 'SUCCESS' : 'FAILED',
                'affected_rows' => $affected_rows,
                'error' => $error ? $error : 'NONE',
                'sql' => $update_sql
            ]);
            
            if (!$execute_result) {
                throw new Exception("Update failed: " . $error . " | SQL: " . substr($update_sql, 0, 200));
            }
            
            if ($affected_rows == 0) {
                generate_log('OAUTH_CALLBACK', 'WARNING: Update executed but no rows affected', [
                    'shop' => $shop,
                    'store_user_id' => $store_user_id,
                    'sql' => $update_sql
                ]);
                // Try to find why no rows were affected
                $check_sql = "SELECT store_user_id, shop_name, store_name, status FROM " . TABLE_USER_SHOP . " WHERE ";
                if ($store_user_id > 0) {
                    $check_sql .= "store_user_id = " . intval($store_user_id);
                } else {
                    $shop_escaped = mysqli_real_escape_string($conn, $shop);
                    $check_sql .= "(shop_name = '" . $shop_escaped . "' OR store_name = '" . $shop_escaped . "')";
                }
                $check_result = mysqli_query($conn, $check_sql);
                if ($check_result) {
                    $check_row = mysqli_fetch_assoc($check_result);
                    generate_log('OAUTH_CALLBACK', 'Store check result', [
                        'found' => $check_row ? 'YES' : 'NO',
                        'data' => $check_row
                    ]);
                }
            } else {
                $log_file = __DIR__ . '/oauth-debug.log';
                $log_msg = "[" . date('Y-m-d H:i:s') . "] ✅ UPDATE SUCCESSFUL - affected_rows: " . $affected_rows . "\n";
                $log_msg .= "[" . date('Y-m-d H:i:s') . "] Redirecting to: " . SITE_CLIENT_URL . "?shop=" . $shop . "\n";
                file_put_contents($log_file, $log_msg, FILE_APPEND);
                file_put_contents($log_file, $log_msg, FILE_APPEND); // Write twice
                error_log("OAUTH_CALLBACK: Update successful - redirecting");
                generate_log('OAUTH_CALLBACK', '✅ Update successful - redirecting immediately', [
                    'shop' => $shop,
                    'affected_rows' => $affected_rows
                ]);
                
                // Small delay to ensure logs are written
                usleep(100000); // 0.1 second
                
                // Redirect immediately after successful update to prevent new store registration
                header('Location: ' . SITE_CLIENT_URL . '?shop=' . $shop);
                exit;
            }
            
        } catch (Exception $e) {
            $log_file = __DIR__ . '/oauth-debug.log';
            $log_msg = "[" . date('Y-m-d H:i:s') . "] ========== EXCEPTION IN UPDATE BLOCK ==========\n";
            $log_msg .= "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n";
            $log_msg .= "[" . date('Y-m-d H:i:s') . "] File: " . $e->getFile() . "\n";
            $log_msg .= "[" . date('Y-m-d H:i:s') . "] Line: " . $e->getLine() . "\n";
            $log_msg .= "[" . date('Y-m-d H:i:s') . "] Trace: " . substr($e->getTraceAsString(), 0, 500) . "\n";
            file_put_contents($log_file, $log_msg, FILE_APPEND);
            file_put_contents($log_file, $log_msg, FILE_APPEND); // Write twice
            error_log("OAUTH_CALLBACK EXCEPTION: " . $e->getMessage());
            generate_log('OAUTH_CALLBACK', 'ERROR in direct SQL update', [
                'shop' => $shop,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: try using put_data with store_user_id
            try {
                $update_data = [
                    '`password`' => $password, 
                    '`updated_on`' => DATE, 
                    '`updated_at`' => DATE,
                    '`status`' => '1',
                    '`is_demand_accept`' => '1',
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
                
                if ($store_user_id > 0) {
                    $update_where = [["", "store_user_id", "=", $store_user_id]];
                } else {
                    $update_where = [["", "shop_name", "=", $shop], ["OR", "store_name", "=", $shop]];
                }
                
                $update_result = $cls_functions->put_data(TABLE_USER_SHOP, $update_data, $update_where);
                generate_log('OAUTH_CALLBACK', 'Fallback put_data result', ['result' => $update_result]);
            } catch (Exception $e2) {
                generate_log('OAUTH_CALLBACK', 'ERROR in fallback put_data', ['error' => $e2->getMessage()]);
            }
        }
        
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
                // Redirect to prevent new store registration
                header('Location: ' . SITE_CLIENT_URL . '?shop=' . $shop);
                exit;
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
        
        // If we reach here and update was successful (affected_rows > 0), redirect anyway
        if (isset($affected_rows) && $affected_rows > 0) {
            generate_log('OAUTH_CALLBACK', '✅ Update had affected rows, redirecting to prevent duplicate registration', ['shop' => $shop, 'affected_rows' => $affected_rows]);
            header('Location: ' . SITE_CLIENT_URL . '?shop=' . $shop);
            exit;
        }
        
        header('Location: ' . SITE_CLIENT_URL . '?shop=' . $shop);
        exit;
    }

    // Store doesn't exist - REGISTER NEW STORE
    $log_file = __DIR__ . '/oauth-debug.log';
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] ❌ STORE NOT FOUND - Going to NEW STORE registration path\n", FILE_APPEND);
    error_log("OAUTH_CALLBACK: Store NOT found - going to NEW STORE registration path");
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] comeback_client status: " . (isset($comeback_client['status']) ? $comeback_client['status'] : 'NOT SET') . "\n", FILE_APPEND);
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] comeback_client data empty: " . (empty($comeback_client['data']) ? 'YES' : 'NO') . "\n", FILE_APPEND);
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

// Log the check result
$log_file = __DIR__ . '/oauth-debug.log';
file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Checking if store exists for initial visit...\n", FILE_APPEND);
file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Store lookup status: " . (isset($comeback['status']) ? $comeback['status'] : 'NOT SET') . "\n", FILE_APPEND);
file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Has code param: " . (isset($_GET['code']) ? 'YES' : 'NO') . "\n", FILE_APPEND);
error_log("INSTALL: Store lookup - status: " . (isset($comeback['status']) ? $comeback['status'] : 'NOT SET') . ", has_code: " . (isset($_GET['code']) ? 'YES' : 'NO'));

// IMPORTANT: Even if store exists, we should still go through OAuth if there's no code
// This ensures password gets updated on reinstallation
// Only skip OAuth if we already have a valid code (OAuth callback)
if ($comeback['status'] == 1 && isset($_GET['code'])) {
    // Store exists AND we have OAuth code - this means OAuth callback already processed
    // Just redirect to client
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Store exists and has code - redirecting to client\n", FILE_APPEND);
    generate_log('INSTALL', 'Store exists and OAuth complete, redirecting to client', ['shop' => $shop]);
    header('Location: ' . SITE_CLIENT_URL . '?shop=' . $shop);
    exit;
}

// If store exists but no code, still go through OAuth to update password
if ($comeback['status'] == 1 && !isset($_GET['code'])) {
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Store exists but no code - forcing OAuth flow to update password\n", FILE_APPEND);
    error_log("INSTALL: Store exists but no code - forcing OAuth");
    // Continue to OAuth redirect below
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
