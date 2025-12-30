<?php
/**
 * Quick script to check if public_id column exists and verify form data
 * Run this to diagnose the 404 error
 */

include_once('append/connection.php');

$conn = $GLOBALS['conn'];

// Check if public_id column exists
$check_column_query = "SHOW COLUMNS FROM forms LIKE 'public_id'";
$result = mysqli_query($conn, $check_column_query);

if (mysqli_num_rows($result) == 0) {
    echo "❌ ERROR: public_id column does NOT exist in forms table!\n";
    echo "You need to run the migration first:\n";
    echo "ALTER TABLE `forms` ADD COLUMN `public_id` VARCHAR(6) NULL DEFAULT NULL AFTER `publishdata`;\n";
    echo "ALTER TABLE `forms` ADD INDEX `idx_public_id_store` (`public_id`, `store_client_id`);\n\n";
} else {
    echo "✅ public_id column exists\n\n";
}

// Check forms with public_id
$check_forms_query = "SELECT id, form_name, public_id, store_client_id, status FROM forms WHERE public_id IS NOT NULL AND public_id != '' LIMIT 10";
$forms_result = mysqli_query($conn, $check_forms_query);

echo "Forms with public_id:\n";
echo str_repeat("-", 80) . "\n";
echo sprintf("%-5s %-20s %-10s %-15s %-5s\n", "ID", "Form Name", "Public ID", "Store Client ID", "Status");
echo str_repeat("-", 80) . "\n";

while ($row = mysqli_fetch_assoc($forms_result)) {
    echo sprintf("%-5s %-20s %-10s %-15s %-5s\n", 
        $row['id'], 
        substr($row['form_name'], 0, 20),
        $row['public_id'] ?: 'NULL',
        $row['store_client_id'],
        $row['status']
    );
}

// Check specific form (ID 29 with public_id 141233)
echo "\n\nChecking form with public_id = 141233:\n";
$specific_query = "SELECT id, form_name, public_id, store_client_id, status FROM forms WHERE public_id = '141233'";
$specific_result = mysqli_query($conn, $specific_query);

if (mysqli_num_rows($specific_result) > 0) {
    $form = mysqli_fetch_assoc($specific_result);
    echo "✅ Found form:\n";
    echo "   Database ID: " . $form['id'] . "\n";
    echo "   Form Name: " . $form['form_name'] . "\n";
    echo "   Public ID: " . $form['public_id'] . "\n";
    echo "   Store Client ID: " . $form['store_client_id'] . "\n";
    echo "   Status: " . $form['status'] . "\n";
} else {
    echo "❌ Form with public_id = 141233 NOT FOUND\n";
}

// Check forms without public_id
$no_public_id_query = "SELECT COUNT(*) as count FROM forms WHERE public_id IS NULL OR public_id = ''";
$no_public_id_result = mysqli_query($conn, $no_public_id_query);
$no_public_id = mysqli_fetch_assoc($no_public_id_result);

echo "\n\nForms without public_id: " . $no_public_id['count'] . "\n";
if ($no_public_id['count'] > 0) {
    echo "⚠️  You need to generate public_id for existing forms\n";
    echo "Run the migration script in sql_migration_add_public_id.sql\n";
}

?>

