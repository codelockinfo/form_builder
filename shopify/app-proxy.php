<?php
/**
 * Shopify App Proxy Handler
 * Handles requests from Shopify Theme Customizer
 * Routes: /apps/form-builder/list and /apps/form-builder/render
 */

// Set JSON header FIRST before any output
header('Content-Type: application/json; charset=utf-8');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// DON'T use output buffering - it's causing blank pages
// ob_start();

// Try to include connection file
try {
    if (!file_exists('../append/connection.php')) {
        throw new Exception('connection.php not found');
    }
    include_once '../append/connection.php';
} catch (Exception $e) {
    // Output buffering disabled - no need to clean
    http_response_code(500);
    echo json_encode(['error' => 'Configuration error', 'message' => $e->getMessage()]);
    exit;
}

try {
    if (DB_OBJECT == 'mysql') {
        $common_file = ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
        if (!file_exists($common_file)) {
            throw new Exception('common_function.php not found at: ' . $common_file);
        }
        include $common_file;
    } else {
        $common_file = ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
        if (!file_exists($common_file)) {
            throw new Exception('common_function.php not found at: ' . $common_file);
        }
        include $common_file;
    }

    $cls_file = ABS_PATH . '/user/cls_functions.php';
    if (!file_exists($cls_file)) {
        throw new Exception('cls_functions.php not found at: ' . $cls_file);
    }
    require_once($cls_file);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'File include error', 
        'message' => $e->getMessage(),
        'abs_path' => defined('ABS_PATH') ? ABS_PATH : 'not defined',
        'file' => __FILE__
    ]);
    exit;
}

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
    // Clean any output and send JSON error
    // Output buffering disabled - no need to clean
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Shop parameter is required', 'message' => 'Please provide the shop parameter in the query string']);
    exit;
}

// Initialize functions with error handling
try {
    $cls_functions = new Client_functions($shop);
    
    // Check if store was found
    if (empty($cls_functions->current_store_obj)) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
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
    // Output buffering disabled - no need to clean
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

// Check if path is in query string (from .htaccess RewriteRule)
$query_path = isset($_GET['path']) ? $_GET['path'] : '';

// Determine route type
$is_list_request = false;
$is_render_request = false;

// Check full path first (when accessed directly or via index.php)
if (strpos($path, '/apps/form-builder/list') !== false || strpos($path, '/apps/easy-form-builder/list') !== false) {
    $is_list_request = true;
} elseif (strpos($path, '/apps/form-builder/render') !== false || strpos($path, '/apps/easy-form-builder/render') !== false) {
    $is_render_request = true;
}
// Check query path (from .htaccess routing: path=list or path=render)
elseif (!empty($query_path)) {
    if (strpos($query_path, 'list') !== false || $query_path == 'list') {
        $is_list_request = true;
    } elseif (strpos($query_path, 'render') !== false || $query_path == 'render') {
        $is_render_request = true;
    }
}

// Route handling
// Debug: Log route detection BEFORE handling
error_log("App Proxy Route Detection - Path: $path, Query Path: $query_path, Is List: " . ($is_list_request ? 'YES' : 'NO') . ", Is Render: " . ($is_render_request ? 'YES' : 'NO'));

// If no route detected, output debug info immediately
if (!$is_list_request && !$is_render_request) {
    echo json_encode([
        'error' => 'Route not detected',
        'path' => $path,
        'query_path' => $query_path,
        'request_uri' => $request_uri,
        'get_params' => $_GET,
        'is_list_request' => $is_list_request ? 'YES' : 'NO',
        'is_render_request' => $is_render_request ? 'YES' : 'NO',
        'help' => 'Add ?path=list to URL to test list route'
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($is_list_request) {
    // Handle form list request
    // Header already set at top
    
    // Debug logging
    error_log("App Proxy - List handler. Shop: $shop, Path: $path, Query Path: $query_path");
    
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
        error_log("Forms array: " . json_encode($forms));
        
        // Clean any output and send JSON
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        // Always include debug info in response
        $response = [
            'forms' => $forms,
            'debug' => [
                'store_user_id' => isset($shopinfo->store_user_id) ? $shopinfo->store_user_id : 'NOT SET',
                'query_result_status' => isset($comeback_client['status']) ? $comeback_client['status'] : 'NOT SET',
                'query_result_count' => isset($comeback_client['data']) && is_array($comeback_client['data']) ? count($comeback_client['data']) : 0,
                'forms_after_filter' => count($forms),
                'shop' => $shop
            ]
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    } catch (Exception $e) {
        error_log("App Proxy Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Clean any output and send JSON error
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Failed to fetch forms: ' . $e->getMessage()]);
        exit;
    }
    
} elseif ($is_render_request) {
    // Handle form render request
    // Clean output buffer for HTML
    // Output buffering disabled - no need to clean
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
    // Unknown route - provide helpful debug info
    http_response_code(404);
    
    $debug_info = [
        'error' => 'Route not found',
        'message' => 'The requested route does not exist. Available routes: /apps/easy-form-builder/list and /apps/easy-form-builder/render',
        'path' => $path,
        'query_path' => $query_path,
        'request_uri' => $request_uri,
        'is_list_request' => $is_list_request ? 'YES' : 'NO',
        'is_render_request' => $is_render_request ? 'YES' : 'NO',
        'get_params' => $_GET,
        'note' => 'Access via: https://YOUR-STORE.myshopify.com/apps/easy-form-builder/list?shop=YOUR-STORE.myshopify.com'
    ];
    
    echo json_encode($debug_info, JSON_PRETTY_PRINT);
    exit;
}

