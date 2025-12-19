<?php
/**
 * Test Store Registration
 * 
 * This script helps test if store registration is working correctly
 * Usage: /shopify/test-store-registration.php?shop=shop-name.myshopify.com
 */

header('Content-Type: text/html; charset=utf-8');

require_once(__DIR__ . '/../append/connection.php');

if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
}

require_once ABS_PATH . '/user/cls_functions.php';

$shop = isset($_GET['shop']) ? trim($_GET['shop']) : '';

if (empty($shop)) {
    die('<h1>Error: Shop parameter required</h1><p>Usage: ?shop=shop-name.myshopify.com</p>');
}

// Normalize shop name
$shop = preg_replace('#^https?://#', '', $shop);
$shop = rtrim($shop, '/');
$shop = strtolower($shop);

echo "<h1>Store Registration Test</h1>";
echo "<h2>Shop: $shop</h2>";

// Check if store exists
$cls_functions = new Client_functions($shop);

if (empty($cls_functions->current_store_obj)) {
    echo "<p style='color: red;'>❌ Store NOT found in database</p>";
    echo "<p>This store needs to be registered. Install the app to register it.</p>";
} else {
    $store_data = $cls_functions->current_store_obj;
    echo "<p style='color: green;'>✅ Store found in database</p>";
    echo "<h3>Store Details:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>store_user_id</td><td>" . (isset($store_data['store_user_id']) ? $store_data['store_user_id'] : 'N/A') . "</td></tr>";
    echo "<tr><td>shop_name</td><td>" . (isset($store_data['shop_name']) ? $store_data['shop_name'] : 'N/A') . "</td></tr>";
    echo "<tr><td>store_name</td><td>" . (isset($store_data['store_name']) ? $store_data['store_name'] : 'N/A') . "</td></tr>";
    echo "<tr><td>email</td><td>" . (isset($store_data['email']) ? $store_data['email'] : 'N/A') . "</td></tr>";
    echo "<tr><td>status</td><td>" . (isset($store_data['status']) ? $store_data['status'] : 'N/A') . "</td></tr>";
    echo "</table>";
    
    // Check forms for this store
    $store_user_id = isset($store_data['store_user_id']) ? (int)$store_data['store_user_id'] : 0;
    if ($store_user_id > 0) {
        $where_query = array(["", "store_client_id", "=", "$store_user_id"], ["AND", "status", "=", "1"]);
        $forms_result = $cls_functions->select_result(TABLE_FORMS, 'id, form_name', $where_query);
        
        echo "<h3>Forms for this store:</h3>";
        if ($forms_result['status'] == 1 && !empty($forms_result['data'])) {
            echo "<ul>";
            foreach ($forms_result['data'] as $form) {
                echo "<li>Form ID: {$form['id']} - {$form['form_name']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No forms found for this store.</p>";
        }
    }
}

echo "<hr>";
echo "<h3>All Stores in Database:</h3>";
$all_stores = $cls_functions->select_result(TABLE_USER_SHOP, 'store_user_id, shop_name, store_name, status', array(["", "status", "=", "1"]));
if ($all_stores['status'] == 1 && !empty($all_stores['data'])) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>store_user_id</th><th>shop_name</th><th>store_name</th><th>status</th></tr>";
    foreach ($all_stores['data'] as $store) {
        $highlight = ($store['shop_name'] == $shop || $store['store_name'] == $shop) ? "style='background: yellow;'" : "";
        echo "<tr $highlight>";
        echo "<td>{$store['store_user_id']}</td>";
        echo "<td>{$store['shop_name']}</td>";
        echo "<td>{$store['store_name']}</td>";
        echo "<td>{$store['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No stores found in database.</p>";
}

