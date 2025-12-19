<?php
/**
 * OAuth Debug Script
 * 
 * This helps debug OAuth callback issues
 * Access this after OAuth redirect to see what parameters were received
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>OAuth Callback Debug</h1>";
echo "<h2>GET Parameters:</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h2>POST Parameters:</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Server Variables:</h2>";
echo "<pre>";
echo "REQUEST_URI: " . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'N/A') . "\n";
echo "HTTP_REFERER: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'N/A') . "\n";
echo "QUERY_STRING: " . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : 'N/A') . "\n";
echo "</pre>";

// Extract shop parameter
$shop = isset($_GET['shop']) ? trim($_GET['shop']) : '';

if (empty($shop) && isset($_GET['code'])) {
    if (isset($_GET['redirect_uri']) && preg_match('/[?&]shop=([^&]+)/', urldecode($_GET['redirect_uri']), $matches)) {
        $shop = urldecode($matches[1]);
        echo "<p>Extracted shop from redirect_uri: $shop</p>";
    }
    if (empty($shop) && isset($_SERVER['HTTP_REFERER']) && preg_match('/[?&]shop=([^&]+)/', $_SERVER['HTTP_REFERER'], $matches)) {
        $shop = urldecode($matches[1]);
        echo "<p>Extracted shop from referer: $shop</p>";
    }
    if (empty($shop) && isset($_SERVER['REQUEST_URI']) && preg_match('/[?&]shop=([^&]+)/', $_SERVER['REQUEST_URI'], $matches)) {
        $shop = urldecode($matches[1]);
        echo "<p>Extracted shop from REQUEST_URI: $shop</p>";
    }
}

if (!empty($shop)) {
    echo "<h2>Extracted Shop: $shop</h2>";
    
    // Normalize
    $shop_normalized = preg_replace('#^https?://#', '', $shop);
    $shop_normalized = rtrim($shop_normalized, '/');
    $shop_normalized = strtolower($shop_normalized);
    
    echo "<p>Normalized Shop: $shop_normalized</p>";
    
    // Check database
    require_once(__DIR__ . '/../append/connection.php');
    if (DB_OBJECT == 'mysql') {
        include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
    } else {
        include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
    }
    
    $cls_functions = new common_function($shop_normalized);
    $where_query = array(["", "shop_name", "=", "$shop_normalized"], ["OR", "store_name", "=", "$shop_normalized"]);
    $result = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
    
    if ($result['status'] == 1) {
        echo "<p style='color: green;'>✅ Store found in database</p>";
        echo "<pre>";
        print_r($result['data']);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>❌ Store NOT found in database</p>";
        echo "<p>This store needs to be registered via OAuth callback.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Could not extract shop parameter</p>";
    echo "<p>Make sure the OAuth redirect URI includes ?shop=shop-name.myshopify.com</p>";
}

