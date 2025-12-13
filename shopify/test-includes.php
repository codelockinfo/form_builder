<?php
/**
 * Test file to debug include issues
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: application/json; charset=utf-8');

echo json_encode(['step' => '1', 'message' => 'Starting test']);

try {
    echo json_encode(['step' => '2', 'message' => 'Before ob_start']);
    
    ob_start();
    
    echo json_encode(['step' => '3', 'message' => 'Before connection.php include']);
    
    if (!file_exists('../append/connection.php')) {
        throw new Exception('connection.php not found');
    }
    
    $session_started = session_status() === PHP_SESSION_ACTIVE;
    if (!$session_started) {
        @session_start();
    }
    
    include_once '../append/connection.php';
    
    $output = ob_get_clean();
    
    echo json_encode([
        'step' => '4',
        'message' => 'After connection.php include',
        'output_caught' => !empty($output) ? substr($output, 0, 200) : 'none',
        'abs_path_defined' => defined('ABS_PATH') ? ABS_PATH : 'NOT DEFINED',
        'db_object_defined' => defined('DB_OBJECT') ? DB_OBJECT : 'NOT DEFINED'
    ]);
    
} catch (Exception $e) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    echo json_encode([
        'error' => 'Exception caught',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} catch (Error $e) {
    if (ob_get_level() > 0) {
        ob_end_clean();
    }
    echo json_encode([
        'error' => 'Fatal error caught',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

