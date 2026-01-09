<?php
/**
 * Verify Extension Deployment Status
 * 
 * This script helps verify if the extension is properly configured and ready for deployment.
 * 
 * Usage: /shopify/verify-extension-deployment.php
 */

header('Content-Type: text/html; charset=utf-8');

echo '<!DOCTYPE html><html><head><title>Extension Deployment Verification</title>';
echo '<style>body{font-family:Arial,sans-serif;max-width:800px;margin:20px auto;padding:20px;}';
echo '.success{color:#10b981;background:#d1fae5;padding:10px;border-radius:4px;margin:10px 0;}';
echo '.error{color:#ef4444;background:#fee2e2;padding:10px;border-radius:4px;margin:10px 0;}';
echo '.warning{color:#f59e0b;background:#fef3c7;padding:10px;border-radius:4px;margin:10px 0;}';
echo '.info{color:#3b82f6;background:#dbeafe;padding:10px;border-radius:4px;margin:10px 0;}';
echo 'pre{background:#f3f4f6;padding:10px;border-radius:4px;overflow-x:auto;}';
echo 'h2{color:#1f2937;border-bottom:2px solid #e5e7eb;padding-bottom:10px;}</style></head><body>';
echo '<h1>🔍 Extension Deployment Verification</h1>';

$issues = [];
$warnings = [];
$success = [];

// Check 1: Extension TOML file exists
$toml_file = __DIR__ . '/../extensions/form-builder-block/shopify.extension.toml';
if (file_exists($toml_file)) {
    $success[] = "✓ Extension TOML file exists: shopify.extension.toml";
    $toml_content = file_get_contents($toml_file);
    
    // Check for required fields
    if (strpos($toml_content, 'type = "theme"') !== false) {
        $success[] = "✓ Extension type is set to 'theme'";
    } else {
        $issues[] = "✗ Extension type not set to 'theme'";
    }
    
    if (strpos($toml_content, 'name = "form-builder-block"') !== false) {
        $success[] = "✓ Extension name is configured";
    } else {
        $issues[] = "✗ Extension name missing or incorrect";
    }
    
    if (strpos($toml_content, 'api_version') !== false) {
        $success[] = "✓ API version is specified";
    } else {
        $warnings[] = "⚠ API version might be missing";
    }
} else {
    $issues[] = "✗ Extension TOML file not found: $toml_file";
}

// Check 2: Block file exists
$block_file = __DIR__ . '/../extensions/form-builder-block/blocks/form-dynamic.liquid';
if (file_exists($block_file)) {
    $success[] = "✓ Block file exists: form-dynamic.liquid";
    
    $block_content = file_get_contents($block_file);
    
    // Check for schema
    if (strpos($block_content, '{% schema %}') !== false) {
        $success[] = "✓ Block schema is present";
        
        // Check for name
        if (preg_match('/"name":\s*"([^"]+)"/', $block_content, $matches)) {
            $success[] = "✓ Block name is set: " . $matches[1];
        } else {
            $issues[] = "✗ Block name not found in schema";
        }
        
        // Check for target
        if (strpos($block_content, '"target":') !== false) {
            $success[] = "✓ Block target is specified";
        } else {
            $warnings[] = "⚠ Block target might be missing";
        }
        
        // Check for settings
        if (strpos($block_content, '"settings":') !== false) {
            $success[] = "✓ Block settings are configured";
        } else {
            $issues[] = "✗ Block settings missing";
        }
    } else {
        $issues[] = "✗ Block schema not found";
    }
} else {
    $issues[] = "✗ Block file not found: $block_file";
}

// Check 3: Locales file
$locales_file = __DIR__ . '/../extensions/form-builder-block/locales/en.default.json';
if (file_exists($locales_file)) {
    $success[] = "✓ Locales file exists";
} else {
    $warnings[] = "⚠ Locales file missing (optional but recommended)";
}

// Check 4: Directory structure
$blocks_dir = __DIR__ . '/../extensions/form-builder-block/blocks/';
if (is_dir($blocks_dir)) {
    $success[] = "✓ Blocks directory exists";
    $files = glob($blocks_dir . '*.liquid');
    $success[] = "✓ Found " . count($files) . " block file(s)";
} else {
    $issues[] = "✗ Blocks directory not found";
}

// Check 5: shopify.app.toml
$app_toml = __DIR__ . '/../shopify.app.toml';
if (file_exists($app_toml)) {
    $success[] = "✓ App TOML file exists";
    $app_content = file_get_contents($app_toml);
    
    if (strpos($app_content, 'client_id') !== false) {
        $success[] = "✓ Client ID is configured";
    }
    
    if (strpos($app_content, '[app_proxy]') !== false) {
        $success[] = "✓ App proxy is configured";
    } else {
        $warnings[] = "⚠ App proxy might not be configured";
    }
} else {
    $warnings[] = "⚠ App TOML file not found (might be in different location)";
}

// Display results
echo '<h2>✅ Success Checks</h2>';
if (!empty($success)) {
    foreach ($success as $msg) {
        echo '<div class="success">' . htmlspecialchars($msg) . '</div>';
    }
} else {
    echo '<div class="warning">No successful checks</div>';
}

if (!empty($warnings)) {
    echo '<h2>⚠️ Warnings</h2>';
    foreach ($warnings as $msg) {
        echo '<div class="warning">' . htmlspecialchars($msg) . '</div>';
    }
}

if (!empty($issues)) {
    echo '<h2>❌ Issues Found</h2>';
    foreach ($issues as $msg) {
        echo '<div class="error">' . htmlspecialchars($msg) . '</div>';
    }
}

// Deployment instructions
echo '<h2>📋 Next Steps</h2>';
echo '<div class="info">';
echo '<strong>To deploy the extension:</strong><br><br>';
echo '<pre>cd C:\\xampp\\htdocs\\form_builder\nshopify app deploy</pre>';
echo '<br>';
echo '<strong>When prompted, select:</strong> form-builder-block<br><br>';
echo '<strong>After deployment:</strong><br>';
echo '1. Wait 1-2 minutes for Shopify to process<br>';
echo '2. Go to store theme customizer<br>';
echo '3. Click "Add section" → "Apps" tab<br>';
echo '4. Look for "Easy Form Builder"<br>';
echo '</div>';

// Check if Shopify CLI is available
echo '<h2>🔧 Shopify CLI Check</h2>';
$cli_check = shell_exec('shopify version 2>&1');
if ($cli_check && strpos($cli_check, 'Shopify CLI') !== false) {
    echo '<div class="success">✓ Shopify CLI is installed</div>';
    echo '<pre>' . htmlspecialchars($cli_check) . '</pre>';
} else {
    echo '<div class="error">✗ Shopify CLI not found or not in PATH</div>';
    echo '<div class="info">Install Shopify CLI: <a href="https://shopify.dev/docs/apps/tools/cli/installation" target="_blank">https://shopify.dev/docs/apps/tools/cli/installation</a></div>';
}

echo '</body></html>';

