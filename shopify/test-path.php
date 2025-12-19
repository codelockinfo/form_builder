<?php
/**
 * Test script to check the actual path resolution
 */

echo "Current __DIR__: " . __DIR__ . "\n";
echo "Resolved blocks_dir: " . realpath(__DIR__ . '/../extensions/form-builder-block/blocks/') . "\n";

$blocks_dir = __DIR__ . '/../extensions/form-builder-block/blocks/';
echo "Blocks dir (relative): $blocks_dir\n";

// Check if directory exists
if (is_dir($blocks_dir)) {
    echo "✓ Directory exists\n";
    echo "Directory is writable: " . (is_writable($blocks_dir) ? 'YES' : 'NO') . "\n";
    
    // List files
    echo "\nFiles in directory:\n";
    $files = scandir($blocks_dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "  - $file\n";
        }
    }
} else {
    echo "✗ Directory does not exist\n";
    echo "Attempting to create...\n";
    if (mkdir($blocks_dir, 0755, true)) {
        echo "✓ Directory created\n";
    } else {
        echo "✗ Failed to create directory\n";
    }
}

