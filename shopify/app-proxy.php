<?php
/**
 * Shopify App Proxy Handler
 * Handles requests from Shopify Theme Customizer
 * Routes: /apps/form-builder/list and /apps/form-builder/render
 */

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
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Shop parameter is required']);
    exit;
}

// Initialize functions
$cls_functions = new Client_functions($shop);

// Get the request path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Route handling
// Support both form-builder and easy-form-builder subpaths
if (strpos($path, '/apps/form-builder/list') !== false || strpos($path, '/apps/easy-form-builder/list') !== false) {
    // Handle form list request
    header('Content-Type: application/json');
    
    try {
        // Get all forms for this shop
        $shopinfo = (object)$cls_functions->current_store_obj;
        $where_query = array(["", "store_client_id", "=", "$shopinfo->store_user_id"], ["AND", "status", "=", "1"]);
        $comeback_client = $cls_functions->select_result(TABLE_FORMS, 'id, form_name', $where_query);
        
        $forms = array();
        if (isset($comeback_client['data']) && is_array($comeback_client['data'])) {
            foreach ($comeback_client['data'] as $form) {
                $forms[] = array(
                    'id' => (int)$form['id'],
                    'name' => $form['form_name']
                );
            }
        }
        
        echo json_encode($forms);
    } catch (Exception $e) {
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
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Route not found']);
}

