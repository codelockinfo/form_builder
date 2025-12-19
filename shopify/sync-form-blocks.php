<?php
/**
 * Sync Form Blocks - Web Accessible Endpoint
 * 
 * This endpoint can be called to regenerate all form blocks.
 * Can be accessed via: /shopify/sync-form-blocks.php?shop=shop-name.myshopify.com
 * 
 * This should be called:
 * - After creating a new form
 * - After updating a form name
 * - After deleting a form
 * - Manually to sync all blocks
 */

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// Start output buffering to capture any output
ob_start();

try {
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
    $shop = isset($_GET['shop']) ? trim($_GET['shop']) : '';
    
    // Path to the blocks directory
    $blocks_dir = __DIR__ . '/../extensions/form-builder-block/blocks/';
    // Resolve to absolute path
    $blocks_dir = realpath(dirname($blocks_dir)) . '/' . basename($blocks_dir) . '/';
    // Fallback if realpath fails
    if (!$blocks_dir || !is_dir($blocks_dir)) {
        $blocks_dir = __DIR__ . '/../extensions/form-builder-block/blocks/';
    }
    
    $template_file = $blocks_dir . 'form-block-template.liquid';
    
    // Debug output
    $output_messages[] = "=== Debug Info ===";
    $output_messages[] = "__DIR__: " . __DIR__;
    $output_messages[] = "Blocks directory: $blocks_dir";
    $output_messages[] = "Directory exists: " . (is_dir($blocks_dir) ? 'YES' : 'NO');
    $output_messages[] = "Directory writable: " . (is_writable($blocks_dir) ? 'YES' : 'NO');
    
    // Verify template exists
    if (!file_exists($template_file)) {
        throw new Exception("Template file not found: $template_file");
    }
    
    $output_messages[] = "Template file: $template_file";
    $output_messages[] = "Template exists: " . (file_exists($template_file) ? 'YES' : 'NO');
    
    // Read template
    $template_content = file_get_contents($template_file);
    if ($template_content === false) {
        throw new Exception("Could not read template file: $template_file");
    }
    
    // Function to generate block file for a form
    function generateFormBlock($form, $template_content, $blocks_dir) {
        $form_id = (int)$form['id'];
        $form_name = isset($form['form_name']) ? $form['form_name'] : 'Unnamed Form';
        
        // Sanitize form name for filename (remove special characters)
        $safe_form_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $form_name);
        $safe_form_name = preg_replace('/_+/', '_', $safe_form_name); // Replace multiple underscores with single
        $safe_form_name = trim($safe_form_name, '_');
        
        // If form name is empty after sanitization, use form ID
        if (empty($safe_form_name)) {
            $safe_form_name = 'form_' . $form_id;
        }
        
        // Create display name for Shopify (max 25 characters)
        // Format: "{form_name} Form" - truncate form_name if needed
        $suffix = ' Form';
        $max_form_name_length = 25 - strlen($suffix);
        if (strlen($form_name) > $max_form_name_length) {
            $form_name_display = substr($form_name, 0, $max_form_name_length - 3) . '...' . $suffix;
        } else {
            $form_name_display = $form_name . $suffix;
        }
        
        // Ensure it doesn't exceed 25 characters (safety check)
        if (strlen($form_name_display) > 25) {
            $form_name_display = substr($form_name_display, 0, 22) . '...';
        }
        
        // Create filename: form-{id}-{name}.liquid
        $filename = 'form-' . $form_id . '-' . strtolower($safe_form_name) . '.liquid';
        $filepath = $blocks_dir . $filename;
        
        // Replace placeholders in template
        $block_content = str_replace('{{ FORM_ID }}', $form_id, $template_content);
        $block_content = str_replace('{{ FORM_NAME }}', addslashes($form_name), $block_content);
        $block_content = str_replace('{{ FORM_NAME_DISPLAY }}', addslashes($form_name_display), $block_content);
        
        // Ensure directory exists and is writable
        if (!is_dir($blocks_dir)) {
            if (!mkdir($blocks_dir, 0755, true)) {
                return array(
                    'success' => false,
                    'error' => "Failed to create blocks directory: $blocks_dir"
                );
            }
        }
        
        // Write the block file
        $result = file_put_contents($filepath, $block_content);
        
        if ($result === false) {
            $error = error_get_last();
            return array(
                'success' => false,
                'error' => "Failed to write file: $filepath (Directory writable: " . (is_writable($blocks_dir) ? 'yes' : 'no') . ", Error: " . ($error ? $error['message'] : 'unknown') . ")"
            );
        }
        
        // Verify file was actually created
        if (!file_exists($filepath)) {
            return array(
                'success' => false,
                'error' => "File was not created: $filepath (Written bytes: $result)"
            );
        }
        
        // Verify file content
        $actual_size = filesize($filepath);
        if ($actual_size != $result) {
            return array(
                'success' => false,
                'error' => "File size mismatch: Expected $result bytes, got $actual_size bytes"
            );
        }
        
        return array(
            'success' => true,
            'filename' => $filename,
            'form_id' => $form_id,
            'form_name' => $form_name
        );
    }
    
    // Function to clean up old block files
    function cleanupOldBlocks($existing_form_ids, $blocks_dir) {
        $deleted = 0;
        $files = glob($blocks_dir . 'form-*.liquid');
        
        foreach ($files as $file) {
            // Skip the template file and original form-block.liquid
            $basename = basename($file);
            if ($basename === 'form-block-template.liquid' || $basename === 'form-block.liquid') {
                continue;
            }
            
            // Extract form ID from filename (form-{id}-{name}.liquid)
            if (preg_match('/form-(\d+)-/', $basename, $matches)) {
                $form_id = (int)$matches[1];
                
                // If this form ID is not in the existing forms list, delete the file
                if (!in_array($form_id, $existing_form_ids)) {
                    if (unlink($file)) {
                        $deleted++;
                    }
                }
            }
        }
        
        return $deleted;
    }
    
    // Get all shops or specific shop
    if (!empty($shop)) {
        // Initialize with specific shop
        $cls_functions = new Client_functions($shop);
        
        // Verify shop was found
        if (empty($cls_functions->current_store_obj)) {
            throw new Exception("Shop '$shop' not found or not active.");
        }
        
        $shop_info = $cls_functions->current_store_obj;
        $shops = array($shop_info);
    } else {
        // Get all active shops first
        $temp_functions = new Client_functions();
        $where_query = array(["", "status", "=", "1"]);
        $shops_result = $temp_functions->select_result(TABLE_USER_SHOP, '*', $where_query);
        
        if ($shops_result['status'] != 1 || empty($shops_result['data'])) {
            throw new Exception("No active shops found.");
        }
        
        $shops = $shops_result['data'];
    }
    
    $total_generated = 0;
    $total_errors = 0;
    $all_form_ids = array();
    $output_messages = array();
    
    // Process each shop
    foreach ($shops as $shop_data) {
        $shop_name = is_array($shop_data) ? $shop_data['shop_name'] : (isset($shop_data->shop_name) ? $shop_data->shop_name : '');
        $store_user_id = is_array($shop_data) ? $shop_data['store_user_id'] : (isset($shop_data->store_user_id) ? $shop_data->store_user_id : '');
        
        if (empty($shop_name) || empty($store_user_id)) {
            $output_messages[] = "Skipping invalid shop data";
            continue;
        }
        
        $output_messages[] = "=== Processing shop: $shop_name ===";
        
        // Initialize Client_functions for this shop
        $cls_functions = new Client_functions($shop_name);
        
        if (empty($cls_functions->current_store_obj)) {
            $output_messages[] = "Warning: Could not initialize shop '$shop_name', skipping...";
            continue;
        }
        
        // Get all active forms for this shop
        $where_query = array(["", "store_client_id", "=", "$store_user_id"], ["AND", "status", "=", "1"]);
        $forms_result = $cls_functions->select_result(TABLE_FORMS, 'id, form_name, status', $where_query);
        
        if ($forms_result['status'] != 1 || empty($forms_result['data'])) {
            $output_messages[] = "No active forms found for shop: $shop_name";
            continue;
        }
        
        $forms = $forms_result['data'];
        $output_messages[] = "Found " . count($forms) . " active form(s)";
        
        // Generate block for each form
        foreach ($forms as $form) {
            $result = generateFormBlock($form, $template_content, $blocks_dir);
            
            if ($result['success']) {
                $output_messages[] = "✓ Generated: {$result['filename']} (Form: {$result['form_name']}, ID: {$result['form_id']})";
                $total_generated++;
                $all_form_ids[] = $result['form_id'];
            } else {
                $output_messages[] = "✗ Error: {$result['error']}";
                $total_errors++;
            }
        }
    }
    
    // Clean up old blocks (after processing all shops)
    if (!empty($all_form_ids)) {
        $output_messages[] = "=== Cleaning up old block files ===";
        $deleted = cleanupOldBlocks($all_form_ids, $blocks_dir);
        if ($deleted > 0) {
            $output_messages[] = "Deleted $deleted old block file(s)";
        } else {
            $output_messages[] = "No old blocks to clean up";
        }
    }
    
    // Summary
    $output_messages[] = "=== Summary ===";
    $output_messages[] = "Total blocks generated: $total_generated";
    if ($total_errors > 0) {
        $output_messages[] = "Total errors: $total_errors";
    }
    $output_messages[] = "Total forms processed: " . count($all_form_ids);
    
    // Clean any output buffer
    ob_end_clean();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Form blocks generated successfully',
        'output' => implode("\n", $output_messages),
        'stats' => [
            'generated' => $total_generated,
            'errors' => $total_errors,
            'forms_processed' => count($all_form_ids)
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Clean output buffer
    ob_end_clean();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}

