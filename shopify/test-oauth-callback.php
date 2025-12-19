<?php
/**
 * Test OAuth Callback Endpoint
 * 
 * This simulates what happens when Shopify redirects back after OAuth
 * Usage: /shopify/test-oauth-callback.php?shop=shop-name.myshopify.com&code=test_code
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>OAuth Callback Test</h1>";

echo "<h2>GET Parameters:</h2>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h2>Server Variables:</h2>";
echo "<pre>";
echo "REQUEST_URI: " . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'N/A') . "\n";
echo "QUERY_STRING: " . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : 'N/A') . "\n";
echo "HTTP_REFERER: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'N/A') . "\n";
echo "</pre>";

$shop = isset($_GET['shop']) ? trim($_GET['shop']) : '';
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($shop)) {
    echo "<p style='color: red;'>❌ Shop parameter is missing</p>";
} else {
    echo "<p style='color: green;'>✅ Shop parameter: $shop</p>";
}

if (empty($code)) {
    echo "<p style='color: orange;'>⚠️ Code parameter is missing (this is normal if you're just testing the URL)</p>";
} else {
    echo "<p style='color: green;'>✅ Code parameter present (length: " . strlen($code) . ")</p>";
}

echo "<hr>";
echo "<h2>Expected OAuth Callback URL:</h2>";
echo "<p>When Shopify redirects back after OAuth, the URL should be:</p>";
echo "<code>https://codelocksolutions.com/form_builder/index.php?shop={$shop}&code=...</code>";

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Make sure you've uploaded the fixed files to the live server</li>";
echo "<li>Go through the OAuth flow by clicking 'Install' in Shopify</li>";
echo "<li>After authorization, Shopify will redirect to: <code>https://codelocksolutions.com/form_builder/index.php?shop={$shop}&code=...</code></li>";
echo "<li>Check the <code>oauth-debug.log</code> file for detailed logs</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>Test the Actual Callback:</h2>";
echo "<p>To test if the callback endpoint is working, visit:</p>";
echo "<code>https://codelocksolutions.com/form_builder/index.php?shop={$shop}&code=test_code_123</code>";
echo "<p>(Note: This will fail at token exchange, but you should see logs in oauth-debug.log)</p>";

