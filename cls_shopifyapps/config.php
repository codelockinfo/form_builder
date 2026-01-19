<?php
// Function to load .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load .env from root directory (up 2 levels from cls_shopifyapps/config.php)
$envPath = dirname(__DIR__) . '/.env';
loadEnv($envPath);

define("SHOPIFY_SCOPE", "read_content,write_content,read_products,write_products,read_script_tags,write_script_tags,write_customers,read_customers,read_themes");
define("SITE_PATH", "https://codelocksolutions.com/form_builder");
define("SHOPIFY_SECRET", getenv('SHOPIFY_API_SECRET'));
define("CLS_API_KEY", getenv('SHOPIFY_API_KEY'));
?>