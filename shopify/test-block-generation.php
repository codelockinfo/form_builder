<?php
/**
 * Test script to verify block generation works
 */

require_once(__DIR__ . '/../append/connection.php');

if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
}

require_once ABS_PATH . '/user/cls_functions.php';

$blocks_dir = __DIR__ . '/../extensions/form-builder-block/blocks/';
$test_file = $blocks_dir . 'test-block.liquid';

echo "Testing block generation...\n";
echo "Blocks directory: $blocks_dir\n";
echo "Directory exists: " . (is_dir($blocks_dir) ? 'YES' : 'NO') . "\n";
echo "Directory writable: " . (is_writable($blocks_dir) ? 'YES' : 'NO') . "\n";
echo "Test file path: $test_file\n";

// Try to write a test file
$test_content = "Test block content";
$result = file_put_contents($test_file, $test_content);

if ($result !== false) {
    echo "✓ Test file written successfully\n";
    echo "File exists: " . (file_exists($test_file) ? 'YES' : 'NO') . "\n";
    echo "File size: " . filesize($test_file) . " bytes\n";
    
    // Clean up
    unlink($test_file);
    echo "✓ Test file deleted\n";
} else {
    echo "✗ Failed to write test file\n";
    echo "Error: " . error_get_last()['message'] . "\n";
}

