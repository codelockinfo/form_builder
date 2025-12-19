<?php
/**
 * Generate Form Blocks Script
 * 
 * This script generates individual block files for each form in the database.
 * Each form will appear as a separate section in the Shopify Theme Customizer.
 * 
 * Usage:
 * - Run this script after creating/updating forms
 * - Can be called via webhook or cron job
 * - Or run manually: php generate-form-blocks.php [shop_name]
 */

// Include connection
require_once(__DIR__ . '/../append/connection.php');

// Get shop parameter (optional - if not provided, will generate for all shops)
$shop = isset($argv[1]) ? $argv[1] : (isset($_GET['shop']) ? $_GET['shop'] : '');

// Path to the blocks directory
$blocks_dir = __DIR__ . '/../extensions/form-builder-block/blocks/';
$template_file = $blocks_dir . 'form-block-template.liquid';

// Verify template exists
if (!file_exists($template_file)) {
    die("Error: Template file not found: $template_file\n");
}

// Include required files
if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
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
    
    // Replace placeholders in template (must replace in order, updating $block_content each time)
    $block_content = str_replace('{{ FORM_ID }}', $form_id, $template_content);
    $block_content = str_replace('{{ FORM_NAME }}', addslashes($form_name), $block_content);
    $block_content = str_replace('{{ FORM_NAME_DISPLAY }}', addslashes($form_name_display), $block_content);
    
    // Write the block file
    $result = file_put_contents($filepath, $block_content);
    
    if ($result === false) {
        return array(
            'success' => false,
            'error' => "Failed to write file: $filepath"
        );
    }
    
    return array(
        'success' => true,
        'filename' => $filename,
        'form_id' => $form_id,
        'form_name' => $form_name
    );
}

// Function to clean up old block files (remove blocks for forms that no longer exist)
function cleanupOldBlocks($existing_form_ids, $blocks_dir) {
    $deleted = 0;
    $files = glob($blocks_dir . 'form-*.liquid');
    
    foreach ($files as $file) {
        // Skip the template file
        if (basename($file) === 'form-block-template.liquid') {
            continue;
        }
        
        // Extract form ID from filename (form-{id}-{name}.liquid)
        if (preg_match('/form-(\d+)-/', basename($file), $matches)) {
            $form_id = (int)$matches[1];
            
            // If this form ID is not in the existing forms list, delete the file
            if (!in_array($form_id, $existing_form_ids)) {
                if (unlink($file)) {
                    $deleted++;
                    echo "Deleted old block file: " . basename($file) . "\n";
                }
            }
        }
    }
    
    return $deleted;
}

try {
    // Get all shops or specific shop first (before initializing Client_functions)
    if (!empty($shop)) {
        // Initialize with specific shop
        $cls_functions = new Client_functions($shop);
        
        // Verify shop was found
        if (empty($cls_functions->current_store_obj)) {
            die("Error: Shop '$shop' not found or not active.\n");
        }
        
        $shop_info = $cls_functions->current_store_obj;
        $shops = array($shop_info);
    } else {
        // Get all active shops first
        $temp_functions = new Client_functions();
        $where_query = array(["", "status", "=", "1"]);
        $shops_result = $temp_functions->select_result(TABLE_USER_SHOP, '*', $where_query);
        
        if ($shops_result['status'] != 1 || empty($shops_result['data'])) {
            die("Error: No active shops found.\n");
        }
        
        $shops = $shops_result['data'];
    }
    
    // Read template
    $template_content = file_get_contents($template_file);
    if ($template_content === false) {
        die("Error: Could not read template file: $template_file\n");
    }
    
    $total_generated = 0;
    $total_errors = 0;
    $all_form_ids = array();
    
    // Process each shop
    foreach ($shops as $shop_data) {
        $shop_name = is_array($shop_data) ? $shop_data['shop_name'] : (isset($shop_data->shop_name) ? $shop_data->shop_name : '');
        $store_user_id = is_array($shop_data) ? $shop_data['store_user_id'] : (isset($shop_data->store_user_id) ? $shop_data->store_user_id : '');
        
        if (empty($shop_name) || empty($store_user_id)) {
            echo "Skipping invalid shop data\n";
            continue;
        }
        
        echo "\n=== Processing shop: $shop_name ===\n";
        
        // Initialize Client_functions for this shop
        $cls_functions = new Client_functions($shop_name);
        
        if (empty($cls_functions->current_store_obj)) {
            echo "Warning: Could not initialize shop '$shop_name', skipping...\n";
            continue;
        }
        
        // Get all active forms for this shop
        $where_query = array(["", "store_client_id", "=", "$store_user_id"], ["AND", "status", "=", "1"]);
        $forms_result = $cls_functions->select_result(TABLE_FORMS, 'id, form_name, status', $where_query);
        
        if ($forms_result['status'] != 1 || empty($forms_result['data'])) {
            echo "No active forms found for shop: $shop_name\n";
            continue;
        }
        
        $forms = $forms_result['data'];
        echo "Found " . count($forms) . " active form(s)\n";
        
        // Generate block for each form
        foreach ($forms as $form) {
            $result = generateFormBlock($form, $template_content, $blocks_dir);
            
            if ($result['success']) {
                echo "✓ Generated: {$result['filename']} (Form: {$result['form_name']}, ID: {$result['form_id']})\n";
                $total_generated++;
                $all_form_ids[] = $result['form_id'];
            } else {
                echo "✗ Error: {$result['error']}\n";
                $total_errors++;
            }
        }
    }
    
    // Clean up old blocks (after processing all shops)
    if (!empty($all_form_ids)) {
        echo "\n=== Cleaning up old block files ===\n";
        $deleted = cleanupOldBlocks($all_form_ids, $blocks_dir);
        if ($deleted > 0) {
            echo "Deleted $deleted old block file(s)\n";
        } else {
            echo "No old blocks to clean up\n";
        }
    }
    
    // Summary
    echo "\n=== Summary ===\n";
    echo "Total blocks generated: $total_generated\n";
    if ($total_errors > 0) {
        echo "Total errors: $total_errors\n";
    }
    echo "Total forms processed: " . count($all_form_ids) . "\n";
    echo "\nDone! Block files are ready in: $blocks_dir\n";
    echo "Note: You may need to redeploy your Shopify app extension for changes to take effect.\n";
    
} catch (Exception $e) {
    die("Fatal error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
}

