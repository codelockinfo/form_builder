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
        // Get all forms for this shop
        // current_store_obj is an array from the database query
        $shopinfo = is_array($cls_functions->current_store_obj) 
            ? $cls_functions->current_store_obj 
            : (array)$cls_functions->current_store_obj;
        
        // Debug logging
        error_log("App Proxy - List Forms Request");
        error_log("Shop: " . $shop);
        error_log("Current Store Obj: " . json_encode($shopinfo));
        
        // Get store_user_id from the store object array
        // The database column is 'store_user_id' and it's in the array
        $store_user_id = null;
        
        // Try array access first
        if (is_array($shopinfo) && isset($shopinfo['store_user_id'])) {
            $store_user_id = (int)$shopinfo['store_user_id'];
        }
        // Try object access
        elseif (is_object($cls_functions->current_store_obj) && isset($cls_functions->current_store_obj->store_user_id)) {
            $store_user_id = (int)$cls_functions->current_store_obj->store_user_id;
        }
        // Try direct array access on current_store_obj
        elseif (is_array($cls_functions->current_store_obj) && isset($cls_functions->current_store_obj['store_user_id'])) {
            $store_user_id = (int)$cls_functions->current_store_obj['store_user_id'];
        }
        
        error_log("Store User ID: " . ($store_user_id ? $store_user_id : 'NOT SET'));
        
        if (empty($store_user_id) || $store_user_id <= 0) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Store user ID not found',
                'message' => 'Unable to identify store user. Store object: ' . json_encode($shopinfo),
                'shop' => $shop
            ]);
            exit;
        }
        
        // Build WHERE query - match the format used in getAllFormFunction()
        // Use string interpolation like: "$shopinfo->store_user_id"
        $where_query = array(["", "store_client_id", "=", "$store_user_id"], ["AND", "status", "=", "1"]);
        
        error_log("Query params - store_user_id: " . $store_user_id . " (type: " . gettype($store_user_id) . ")");
        error_log("WHERE query array: " . json_encode($where_query));
        
        $comeback_client = $cls_functions->select_result(TABLE_FORMS, 'id, form_name', $where_query);
        
        error_log("Forms query result status: " . (isset($comeback_client['status']) ? $comeback_client['status'] : 'NOT SET'));
        error_log("Forms count: " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : '0'));
        
        // If no results with status filter, try without status filter (like getAllFormFunction does)
        if (!isset($comeback_client['data']) || empty($comeback_client['data']) || count($comeback_client['data']) == 0) {
            error_log("No forms found with status filter, trying without status filter...");
            $where_query_no_status = array(["", "store_client_id", "=", "$store_user_id"]);
            $comeback_client = $cls_functions->select_result(TABLE_FORMS, 'id, form_name', $where_query_no_status);
            error_log("Forms count (no status filter): " . (isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : '0'));
        }
        
        error_log("Final forms query result: " . json_encode($comeback_client));
        
        $forms = array();
        if (isset($comeback_client['data']) && is_array($comeback_client['data'])) {
            foreach ($comeback_client['data'] as $form) {
                $forms[] = array(
                    'id' => (int)$form['id'],
                    'name' => $form['form_name']
                );
            }
        }
        
        error_log("Forms array: " . json_encode($forms));
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

