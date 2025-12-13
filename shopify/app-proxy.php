<?php
/**
 * Shopify App Proxy Handler
 * Handles requests from Shopify Theme Customizer
 * Routes: /apps/form-builder/list and /apps/form-builder/render
 */

// Suppress PHP warnings/notices from being output (they break JSON)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include_once '../append/connection.php';

if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
}

require_once(ABS_PATH . '/user/cls_functions.php');

// Get shop parameter from Shopify
// Shopify App Proxy sends shop in query string
$shop = isset($_GET['shop']) ? $_GET['shop'] : '';

// Also check for shop in the path (some Shopify configurations)
if (empty($shop)) {
    $request_uri = $_SERVER['REQUEST_URI'];
    if (preg_match('/shop=([^&]+)/', $request_uri, $matches)) {
        $shop = $matches[1];
    }
}

// Verify shop parameter exists
if (empty($shop)) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Shop parameter is required']);
    exit;
}

// Initialize functions with error handling
try {
    $cls_functions = new Client_functions($shop);
    
    // Check if store was found
    if (empty($cls_functions->current_store_obj)) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'Store not found',
            'message' => 'The shop "' . $shop . '" is not registered in the app. Please install the app first.',
            'shop' => $shop
        ]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error' => 'Failed to initialize',
        'message' => $e->getMessage()
    ]);
    exit;
}

// Get the request path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Route handling
// Support both form-builder and easy-form-builder subpaths
if (strpos($path, '/apps/form-builder/list') !== false || strpos($path, '/apps/easy-form-builder/list') !== false) {
    // Handle form list request
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // Get all forms for this shop - use EXACT same approach as getAllFormFunction()
        $shopinfo = (object)$cls_functions->current_store_obj;
        
        // Debug logging
        error_log("App Proxy - List Forms Request for shop: " . $shop);
        
        if (empty($cls_functions->current_store_obj)) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Store not found',
                'message' => 'The shop is not registered in the app.',
                'shop' => $shop
            ]);
            exit;
        }
        
        // Check if store_user_id exists
        if (!isset($shopinfo->store_user_id) || empty($shopinfo->store_user_id)) {
            error_log("ERROR: store_user_id not found in shopinfo object");
            http_response_code(500);
            echo json_encode([
                'error' => 'Store user ID not found',
                'message' => 'Unable to identify store user.',
                'shop' => $shop,
                'debug' => 'Store object keys: ' . (is_object($shopinfo) ? implode(', ', array_keys((array)$shopinfo)) : 'not object')
            ]);
            exit;
        }
        
        // Use EXACT same format as getAllFormFunction() line 256
        // First try without status filter (like getAllFormFunction does)
        $where_query = array(["", "store_client_id", "=", "$shopinfo->store_user_id"]);
        
        error_log("Querying forms for store_user_id: " . $shopinfo->store_user_id);
        
        $comeback_client = $cls_functions->select_result(TABLE_FORMS, 'id, form_name, status', $where_query);
        
        error_log("Query result status: " . (isset($comeback_client['status']) ? $comeback_client['status'] : 'NOT SET'));
        error_log("Query result data count: " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : '0'));
        
        $forms = array();
        if (isset($comeback_client['data']) && is_array($comeback_client['data'])) {
            foreach ($comeback_client['data'] as $form) {
                // Filter to only include forms with status = 1
                $form_status = isset($form['status']) ? (int)$form['status'] : 1;
                if ($form_status == 1) {
                    $forms[] = array(
                        'id' => (int)$form['id'],
                        'name' => isset($form['form_name']) ? $form['form_name'] : 'Unnamed Form'
                    );
                }
            }
        }
        
        error_log("Final active forms count: " . count($forms));
        echo json_encode($forms);
    } catch (Exception $e) {
        error_log("App Proxy Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch forms: ' . $e->getMessage()]);
    }
    
} elseif (strpos($path, '/apps/form-builder/render') !== false || strpos($path, '/apps/easy-form-builder/render') !== false) {
    // Handle form render request
    header('Content-Type: text/html; charset=utf-8');
    
    $form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;
    
    if ($form_id <= 0) {
        http_response_code(400);
        echo '<div style="padding: 20px; color: #d32f2f;">Error: Form ID is required</div>';
        exit;
    }
    
    try {
        // Set POST data to simulate the get_selected_elements_fun call
        $_POST['store'] = $shop;
        $_POST['form_id'] = $form_id;
        
        // Get form HTML
        $result = $cls_functions->get_selected_elements_fun();
        
        if (isset($result['form_html']) && !empty($result['form_html'])) {
            // Output the form HTML
            echo $result['form_html'];
        } else {
            http_response_code(404);
            echo '<div style="padding: 20px; color: #d32f2f;">Form not found or has no content</div>';
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo '<div style="padding: 20px; color: #d32f2f;">Error rendering form: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    
} else {
    // Unknown route
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Route not found', 'path' => $path]);
}

