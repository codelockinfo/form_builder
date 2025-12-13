<?php
/**
 * Shopify App Proxy Handler
 * Handles requests from Shopify Theme Customizer
 * Routes: /apps/form-builder/list and /apps/form-builder/render
 */

// Enable error reporting for debugging FIRST
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Keep 0 to prevent HTML errors breaking JSON
ini_set('log_errors', 1);

// Set JSON header FIRST before any output
header('Content-Type: application/json; charset=utf-8');

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Clean any output buffer
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'Fatal error',
            'message' => $error['message'],
            'file' => $error['file'],
            'line' => $error['line'],
            'type' => $error['type']
        ], JSON_PRETTY_PRINT);
    }
});

// DON'T use output buffering - it's causing blank pages
// ob_start();

// TEMPORARY: Test output to verify file is executing
if (isset($_GET['test']) && $_GET['test'] == '1') {
    // Clean any output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    echo json_encode(['test' => 'File is executing', 'time' => date('Y-m-d H:i:s'), 'php_version' => phpversion()]);
    exit;
}

// DEBUG: Test if we can output JSON before includes
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    // Clean any output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    echo json_encode(['debug' => 'Before includes', 'time' => date('Y-m-d H:i:s')]);
    exit;
}

// Try to include connection file
// Use output buffering to catch any output from includes
try {
    if (!file_exists('../append/connection.php')) {
        throw new Exception('connection.php not found');
    }
    
    // Start output buffering
    ob_start();
    
    // Temporarily suppress session warnings (session may already be started)
    $session_started = session_status() === PHP_SESSION_ACTIVE;
    if (!$session_started) {
        @session_start();
    }
    
    include_once '../append/connection.php';
    
    $output = ob_get_clean();
    
    // Check if connection.php output anything (like "Undefine host")
    if (!empty($output)) {
        error_log("App Proxy - connection.php output detected: " . substr($output, 0, 200));
        // If output contains error messages, throw exception
        if (stripos($output, 'Undefine host') !== false || 
            stripos($output, 'Connection failed') !== false ||
            stripos($output, 'No mysql connection') !== false ||
            stripos($output, 'No mongodb connection') !== false) {
            throw new Exception('Database connection error: ' . $output);
        }
    }
    
    // Verify required constants are defined
    if (!defined('ABS_PATH')) {
        throw new Exception('ABS_PATH not defined after including connection.php');
    }
    if (!defined('DB_OBJECT')) {
        throw new Exception('DB_OBJECT not defined after including connection.php');
    }
    
} catch (Exception $e) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(500);
    echo json_encode([
        'error' => 'Configuration error', 
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
    exit;
} catch (Error $e) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(500);
    echo json_encode([
        'error' => 'Fatal error in connection.php', 
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
    exit;
}

// Include cls_functions
// Note: cls_functions.php includes common_function.php which includes base_function.php
// Both use 'include' not 'include_once', so we need to prevent double inclusion
ob_start();
try {
    // Only include cls_functions if Client_functions class doesn't exist
    if (!class_exists('Client_functions')) {
        $cls_file = ABS_PATH . '/user/cls_functions.php';
        if (!file_exists($cls_file)) {
            throw new Exception('cls_functions.php not found at: ' . $cls_file);
        }
        
        // cls_functions.php will include common_function.php which includes base_function.php
        // Since they use 'include' not 'include_once', we need to prevent double declaration
        // We'll use a custom include wrapper that checks if class exists first
        require_once($cls_file);
    }
    
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("App Proxy - common_function/cls_functions output: " . substr($output, 0, 200));
    }
    
} catch (Exception $e) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(500);
    echo json_encode([
        'error' => 'File include error', 
        'message' => $e->getMessage(),
        'abs_path' => defined('ABS_PATH') ? ABS_PATH : 'not defined',
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
    exit;
} catch (Error $e) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(500);
    echo json_encode([
        'error' => 'Fatal error in file includes', 
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
    exit;
}

// Get shop parameter from Shopify
// Shopify App Proxy sends shop in query string
$shop = isset($_GET['shop']) ? trim($_GET['shop']) : '';

// Also check for shop in the path (some Shopify configurations)
if (empty($shop)) {
    $request_uri = $_SERVER['REQUEST_URI'];
    if (preg_match('/shop=([^&]+)/', $request_uri, $matches)) {
        $shop = trim($matches[1]);
    }
}

// Normalize shop domain - remove protocol, trailing slashes, etc.
if (!empty($shop)) {
    $shop = preg_replace('#^https?://#', '', $shop); // Remove http:// or https://
    $shop = rtrim($shop, '/'); // Remove trailing slash
    $shop = strtolower($shop); // Convert to lowercase for consistency
}

error_log("App Proxy - Shop parameter received: '" . $shop . "'");

// Verify shop parameter exists
if (empty($shop)) {
    http_response_code(400);
    echo json_encode(['error' => 'Shop parameter is required', 'message' => 'Please provide the shop parameter in the query string'], JSON_PRETTY_PRINT);
    exit;
}

// DEBUG: Test output after includes but before Client_functions
if (isset($_GET['debug']) && $_GET['debug'] == '2') {
    echo json_encode(['debug' => 'After includes, before Client_functions', 'shop' => $shop, 'time' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT);
    exit;
}

// Initialize functions with error handling
try {
    $cls_functions = new Client_functions($shop);
    
    // Check if store was found
    if (empty($cls_functions->current_store_obj)) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Store not found',
            'message' => 'The shop "' . $shop . '" is not registered in the app. Please install the app first.',
            'shop' => $shop
        ]);
        exit;
    }
} catch (Exception $e) {
    error_log("App Proxy - Exception during initialization: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to initialize Client_functions',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'shop' => $shop
    ], JSON_PRETTY_PRINT);
    exit;
} catch (Error $e) {
    // Catch fatal errors (PHP 7+)
    error_log("App Proxy - Fatal error during initialization: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Fatal error during initialization',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'shop' => $shop
    ], JSON_PRETTY_PRINT);
    exit;
}

// Get the request path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Check if path is in query string (from .htaccess RewriteRule)
$query_path = isset($_GET['path']) ? $_GET['path'] : '';

// Shopify App Proxy adds path_prefix parameter - check that too
$path_prefix = isset($_GET['path_prefix']) ? trim($_GET['path_prefix'], '/') : '';

// Determine route type
$is_list_request = false;
$is_render_request = false;

// Check full path first (when accessed directly or via index.php)
if (strpos($path, '/apps/form-builder/list') !== false || 
    strpos($path, '/apps/easy-form-builder/list') !== false ||
    strpos($path, '/list') !== false ||
    strpos($path, 'app-proxy.php/list') !== false) {
    $is_list_request = true;
}
if (strpos($path, '/apps/form-builder/render') !== false || 
    strpos($path, '/apps/easy-form-builder/render') !== false ||
    strpos($path, '/render') !== false ||
    strpos($path, 'app-proxy.php/render') !== false) {
    $is_render_request = true;
}

// Check query path (from .htaccess routing: path=list or path=render)
if (!empty($query_path)) {
    if (strpos($query_path, 'list') !== false || $query_path == 'list') {
        $is_list_request = true;
    }
    if (strpos($query_path, 'render') !== false || $query_path == 'render') {
        $is_render_request = true;
    }
}

// Check path_prefix from Shopify App Proxy (Shopify sends this parameter)
if (!empty($path_prefix)) {
    if (strpos($path_prefix, 'list') !== false || $path_prefix == 'list' || $path_prefix == 'apps/easy-form-builder/list') {
        $is_list_request = true;
    }
    if (strpos($path_prefix, 'render') !== false || $path_prefix == 'render' || $path_prefix == 'apps/easy-form-builder/render') {
        $is_render_request = true;
    }
}

// Last resort: check if path ends with /list or /render
if (!$is_list_request && !$is_render_request) {
    if (preg_match('#/(list|render)(\?|$)#', $path)) {
        if (strpos($path, '/list') !== false) {
            $is_list_request = true;
        }
        if (strpos($path, '/render') !== false) {
            $is_render_request = true;
        }
    }
}

// Route handling
// Debug: Log route detection BEFORE handling
error_log("App Proxy Route Detection - Path: $path, Query Path: $query_path, Path Prefix: $path_prefix, Is List: " . ($is_list_request ? 'YES' : 'NO') . ", Is Render: " . ($is_render_request ? 'YES' : 'NO'));

// If no route detected, output debug info immediately
if (!$is_list_request && !$is_render_request) {
    echo json_encode([
        'error' => 'Route not detected',
        'path' => $path,
        'query_path' => $query_path,
        'path_prefix' => $path_prefix,
        'request_uri' => $request_uri,
        'get_params' => $_GET,
        'is_list_request' => $is_list_request ? 'YES' : 'NO',
        'is_render_request' => $is_render_request ? 'YES' : 'NO',
        'help' => 'Route detection failed. Check path, query_path, and path_prefix parameters.'
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
        
        // Return forms array directly for Shopify compatibility
        // Shopify expects a simple array, but we can include debug in development
        $json_output = json_encode($forms, JSON_PRETTY_PRINT);
        if ($json_output === false) {
            error_log("JSON encode error: " . json_last_error_msg());
            echo json_encode(['error' => 'JSON encoding failed', 'json_error' => json_last_error_msg()]);
        } else {
            echo $json_output;
        }
        
        // Log debug info separately
        error_log("App Proxy - Forms returned: " . count($forms) . " forms for shop: " . $shop);
        exit;
    } catch (Exception $e) {
        error_log("App Proxy Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Send JSON error with full details
        http_response_code(500);
        $error_response = [
            'error' => 'Failed to fetch forms', 
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        echo json_encode($error_response, JSON_PRETTY_PRINT);
        exit;
    } catch (Error $e) {
        // Catch fatal errors too
        error_log("App Proxy Fatal Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => 'Fatal error', 
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], JSON_PRETTY_PRINT);
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

