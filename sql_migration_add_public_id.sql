-- Migration Script: Add public_id column to forms table
-- This adds a 6-digit secure ID for forms instead of exposing database IDs

-- Step 1: Add public_id column to forms table
ALTER TABLE `forms` 
ADD COLUMN `public_id` VARCHAR(6) NULL DEFAULT NULL AFTER `publishdata`,
ADD INDEX `idx_public_id_store` (`public_id`, `store_client_id`);

-- Step 2: Generate public_id for existing forms
-- This will generate unique 6-digit IDs for all existing forms
-- Run this PHP script to populate public_id for existing forms:

<?php
/**
 * Migration script to generate public_id for existing forms
 * Run this once after adding the column
 */

include_once('append/connection.php');
include_once('user/cls_functions.php');

// Get all forms without public_id
$db = new DB_Class();
$query = "SELECT id, store_client_id FROM forms WHERE public_id IS NULL OR public_id = ''";
$result = mysqli_query($GLOBALS['conn'], $query);

$updated = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $form_id = $row['id'];
    $store_user_id = $row['store_client_id'];
    
    // Generate unique 6-digit ID
    $maxAttempts = 100;
    $attempts = 0;
    
    do {
        $public_id = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Check if this ID already exists for this shop
        $check_query = "SELECT id FROM forms WHERE public_id = '$public_id' AND store_client_id = '$store_user_id'";
        $check_result = mysqli_query($GLOBALS['conn'], $check_query);
        $exists = mysqli_num_rows($check_result) > 0;
        
        $attempts++;
        if ($attempts >= $maxAttempts) {
            // Fallback: use timestamp-based ID
            $public_id = substr(str_replace('.', '', microtime(true)), -6);
            break;
        }
    } while ($exists);
    
    // Update the form with public_id
    $update_query = "UPDATE forms SET public_id = '$public_id' WHERE id = $form_id";
    if (mysqli_query($GLOBALS['conn'], $update_query)) {
        $updated++;
        echo "Updated form ID $form_id with public_id: $public_id\n";
    } else {
        echo "Error updating form ID $form_id: " . mysqli_error($GLOBALS['conn']) . "\n";
    }
}

echo "\nMigration complete! Updated $updated forms.\n";
?>

-- Step 3: Make public_id NOT NULL after migration (optional, for new forms)
-- ALTER TABLE `forms` MODIFY COLUMN `public_id` VARCHAR(6) NOT NULL;

