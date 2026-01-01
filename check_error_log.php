<?php
// Quick script to check PHP error log location
echo "=== PHP Error Log Information ===\n\n";

// Check php.ini error_log setting
$error_log = ini_get('error_log');
echo "PHP error_log setting: " . ($error_log ? $error_log : 'Not set (using default)') . "\n\n";

// Common XAMPP locations
$common_locations = array(
    'C:\\xampp\\php\\logs\\php_error_log',
    'C:\\xampp\\apache\\logs\\error.log',
    'C:\\xampp\\apache\\logs\\access.log',
    $_SERVER['DOCUMENT_ROOT'] . '\\logs\\app-log-' . date('Y-m-d') . '.log'
);

echo "Checking common log locations:\n";
foreach ($common_locations as $location) {
    if (file_exists($location)) {
        $size = filesize($location);
        $modified = date('Y-m-d H:i:s', filemtime($location));
        echo "✓ FOUND: $location\n";
        echo "  Size: " . number_format($size) . " bytes\n";
        echo "  Last Modified: $modified\n\n";
    } else {
        echo "✗ NOT FOUND: $location\n";
    }
}

// Check application logs directory
$app_logs_dir = $_SERVER['DOCUMENT_ROOT'] . '\\form_builder\\logs\\';
if (is_dir($app_logs_dir)) {
    echo "\nApplication logs directory: $app_logs_dir\n";
    $files = scandir($app_logs_dir);
    echo "Files in logs directory:\n";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $full_path = $app_logs_dir . $file;
            if (is_file($full_path)) {
                $size = filesize($full_path);
                $modified = date('Y-m-d H:i:s', filemtime($full_path));
                echo "  - $file (Size: " . number_format($size) . " bytes, Modified: $modified)\n";
            } else if (is_dir($full_path)) {
                echo "  - $file/ (directory)\n";
            }
        }
    }
}

// Display current PHP error reporting settings
echo "\n=== PHP Error Reporting Settings ===\n";
echo "error_reporting: " . error_reporting() . "\n";
echo "display_errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "\n";
echo "log_errors: " . (ini_get('log_errors') ? 'On' : 'Off') . "\n";
echo "error_log: " . ini_get('error_log') . "\n";

?>

