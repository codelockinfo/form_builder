<?php
/**
 * Auto Sync Blocks - Called automatically when forms are created/updated
 * 
 * This file should be included or called after form operations
 * to automatically regenerate and sync blocks
 */

function autoSyncFormBlocks($shop = '') {
    // Only run if we're in a web context (not CLI)
    if (php_sapi_name() === 'cli') {
        return false;
    }
    
    // Get shop from current context if not provided
    if (empty($shop) && isset($GLOBALS['current_shop'])) {
        $shop = $GLOBALS['current_shop'];
    }
    
    if (empty($shop)) {
        return false;
    }
    
    // Build sync URL
    $sync_url = CLS_SITE_URL . '/shopify/sync-form-blocks.php?shop=' . urlencode($shop);
    
    // Use async call to avoid blocking the main request
    // For Windows/XAMPP, we'll use a simple file-based trigger instead
    $trigger_file = __DIR__ . '/../extensions/form-builder-block/.sync-trigger';
    file_put_contents($trigger_file, time() . "\n" . $shop);
    
    // Try to trigger sync via background process (non-blocking)
    if (function_exists('exec')) {
        // Try to run sync in background (Windows)
        $php_path = 'php'; // Try default first
        $possible_paths = [
            'C:\\xampp\\php\\php.exe',
            'C:\\php\\php.exe',
            'php'
        ];
        
        $php_exe = 'php';
        foreach ($possible_paths as $path) {
            if (file_exists($path) || $path === 'php') {
                $php_exe = $path;
                break;
            }
        }
        
        $sync_script = __DIR__ . '/sync-form-blocks.php';
        $command = escapeshellarg($php_exe) . ' ' . escapeshellarg($sync_script) . ' ' . escapeshellarg($shop);
        
        // For Windows, use start command to run in background
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = 'start /B ' . $command . ' > NUL 2>&1';
        } else {
            $command .= ' > /dev/null 2>&1 &';
        }
        
        @exec($command);
    }
    
    return true;
}

