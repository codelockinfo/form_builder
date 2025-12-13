<?php
/**
 * Simple test file to verify PHP is working
 */
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'working',
    'time' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'message' => 'If you see this, PHP is working correctly!'
]);
exit;

