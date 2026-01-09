<?php
/**
 * Check App Block Status for Stores
 * 
 * This script helps verify why app blocks might not be appearing in some stores.
 * 
 * Usage: 
 * - Via browser: /shopify/check-app-block-status.php?shop=store1.myshopify.com
 * - Via browser (compare): /shopify/check-app-block-status.php?shop1=store1.myshopify.com&shop2=store2.myshopify.com
 */

header('Content-Type: application/json; charset=utf-8');

try {
    require_once(__DIR__ . '/../append/connection.php');
    
    if (DB_OBJECT == 'mysql') {
        include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
    } else {
        include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
    }
    
    require_once ABS_PATH . '/user/cls_functions.php';
    
    $shop1 = isset($_GET['shop1']) ? trim($_GET['shop1']) : (isset($_GET['shop']) ? trim($_GET['shop']) : '');
    $shop2 = isset($_GET['shop2']) ? trim($_GET['shop2']) : '';
    
    $results = [];
    
    // Check Shop 1
    if (!empty($shop1)) {
        $results['shop1'] = checkStoreStatus($shop1);
    }
    
    // Check Shop 2
    if (!empty($shop2)) {
        $results['shop2'] = checkStoreStatus($shop2);
    }
    
    // If only one shop provided, check all stores
    if (empty($shop1) && empty($shop2)) {
        $temp_functions = new Client_functions();
        $where_query = array(["", "status", "=", "1"]);
        $shops_result = $temp_functions->select_result(TABLE_USER_SHOP, 'shop_name', $where_query);
        
        if ($shops_result['status'] == 1 && !empty($shops_result['data'])) {
            foreach ($shops_result['data'] as $shop_data) {
                $shop_name = is_array($shop_data) ? $shop_data['shop_name'] : $shop_data->shop_name;
                if (!empty($shop_name)) {
                    $results[$shop_name] = checkStoreStatus($shop_name);
                }
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'App block status check completed',
        'results' => $results,
        'recommendations' => getRecommendations($results)
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}

function checkStoreStatus($shop) {
    $status = [
        'shop' => $shop,
        'app_installed' => false,
        'store_registered' => false,
        'forms_count' => 0,
        'active_forms' => 0,
        'extension_deployed' => 'unknown',
        'issues' => []
    ];
    
    try {
        $cls_functions = new Client_functions($shop);
        
        // Check if store is registered
        if (!empty($cls_functions->current_store_obj)) {
            $status['store_registered'] = true;
            $status['app_installed'] = true; // If store is registered, app is installed
            
            $store_user_id = $cls_functions->current_store_obj['store_user_id'];
            
            // Count forms
            $where_query = array(
                ["", "store_client_id", "=", "$store_user_id"]
            );
            $forms_result = $cls_functions->select_result(TABLE_FORMS, 'id, form_name, status', $where_query);
            
            if ($forms_result['status'] == 1 && !empty($forms_result['data'])) {
                $status['forms_count'] = count($forms_result['data']);
                $status['active_forms'] = count(array_filter($forms_result['data'], function($form) {
                    return (isset($form['status']) && $form['status'] == '1') || 
                           (isset($form['status']) && $form['status'] == 1);
                }));
            }
        } else {
            $status['issues'][] = 'Store not registered in database';
            $status['issues'][] = 'App may not be installed on this store';
        }
        
        // Check if extension files exist
        $extension_file = __DIR__ . '/../extensions/form-builder-block/blocks/form-dynamic.liquid';
        if (file_exists($extension_file)) {
            $status['extension_file_exists'] = true;
        } else {
            $status['extension_file_exists'] = false;
            $status['issues'][] = 'Extension file not found';
        }
        
    } catch (Exception $e) {
        $status['error'] = $e->getMessage();
        $status['issues'][] = 'Error checking store: ' . $e->getMessage();
    }
    
    return $status;
}

function getRecommendations($results) {
    $recommendations = [];
    
    foreach ($results as $shop => $status) {
        if (is_array($status) && isset($status['shop'])) {
            $shop_recs = [];
            
            if (!$status['app_installed']) {
                $shop_recs[] = "Install the app on {$status['shop']}: Go to https://apps.shopify.com/easy-form-builder-email and click 'Add app'";
            }
            
            if (!$status['extension_file_exists']) {
                $shop_recs[] = "Extension file missing. Verify deployment: shopify app deploy";
            }
            
            if ($status['active_forms'] == 0) {
                $shop_recs[] = "No active forms found. Create at least one form in the app dashboard";
            }
            
            if (empty($shop_recs)) {
                $shop_recs[] = "Store looks good! If blocks still don't appear, try: 1) Deploy extension: shopify app deploy, 2) Refresh theme customizer, 3) Check theme supports app blocks (Online Store 2.0)";
            }
            
            $recommendations[$shop] = $shop_recs;
        }
    }
    
    return $recommendations;
}

