<?php
/**
 * Auto-add custom_code column to forms table
 * This script checks if the column exists and adds it if not
 */

// Set SERVER_NAME for connection.php
$_SERVER['SERVER_NAME'] = 'localhost';

// Include database connection
require_once('append/connection.php');

// Get database connection
$db_obj = new DB_Class();
$db = $db_obj->db;

// Check if custom_code column exists
$table_name = TABLE_FORMS;
$column_name = 'custom_code';

$check_query = "SELECT COUNT(*) as count 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = '$table_name' 
                AND COLUMN_NAME = '$column_name'";

$result = mysqli_query($db, $check_query);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    // Column doesn't exist, add it
    $alter_query = "ALTER TABLE $table_name ADD COLUMN $column_name LONGTEXT NULL AFTER design_settings";
    
    if (mysqli_query($db, $alter_query)) {
        echo "SUCCESS: custom_code column added to $table_name table\n";
        error_log("custom_code column added to $table_name table");
    } else {
        echo "ERROR: Failed to add custom_code column: " . mysqli_error($db) . "\n";
        error_log("Failed to add custom_code column: " . mysqli_error($db));
    }
} else {
    echo "INFO: custom_code column already exists in $table_name table\n";
    error_log("custom_code column already exists in $table_name table");
}

mysqli_close($db);
?>
