<?php
/**
 * Webhook Endpoint for Auto-Syncing Blocks
 * 
 * This can be called via webhook or directly to sync blocks
 * Usage: POST/GET to /shopify/webhook-sync-blocks.php?shop=shop-name.myshopify.com
 */

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// Include connection
require_once(__DIR__ . '/../append/connection.php');

// Include required files
if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
}

// Include Client_functions class
require_once ABS_PATH . '/user/cls_functions.php';

// Get shop parameter
$shop = isset($_GET['shop']) ? trim($_GET['shop']) : (isset($_POST['shop']) ? trim($_POST['shop']) : '');

if (empty($shop)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Shop parameter is required'
    ]);
    exit;
}

// Initialize Client_functions
try {
    $cls_functions = new Client_functions($shop);
    
    if (empty($cls_functions->current_store_obj)) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Shop not found'
        ]);
        exit;
    }
    
    // Trigger sync
    $sync_url = CLS_SITE_URL . '/shopify/sync-form-blocks.php?shop=' . urlencode($shop);
    
    // Use curl to trigger sync
    $ch = curl_init($sync_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $result = json_decode($response, true);
        echo json_encode([
            'success' => true,
            'message' => 'Blocks synced successfully',
            'details' => $result
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to sync blocks',
            'http_code' => $http_code,
            'response' => $response
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

