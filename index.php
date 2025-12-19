<?php
// Enable error logging for OAuth debugging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/oauth-debug.log');

// Handle Shopify App Proxy requests first
$request_uri = $_SERVER['REQUEST_URI'];
if (strpos($request_uri, '/apps/form-builder/') !== false || strpos($request_uri, '/apps/easy-form-builder/') !== false) {
    include_once 'shopify/app-proxy.php';
    exit;
}

// Log OAuth callback attempts
if (isset($_GET['code'])) {
    error_log("=== OAuth Callback Started ===");
    error_log("GET parameters: " . json_encode($_GET));
    error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
    error_log("HTTP_REFERER: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'N/A'));
}

include_once 'append/connection.php';
if(isset($_GET['shop'])){
   header('X-Frame-Options:ALLOW-FROM '.$_GET['shop']);
   header("Content-Security-Policy: frame-ancestors ".$_GET['shop']);
}
else {
    header('X-Frame-Options:SAMEORIGIN');
}

if (DB_OBJECT == 'mysql') {
    include ABS_PATH . "/collection/mongo_mysql/mysql/common_function.php";
} else {
    include ABS_PATH . "/collection/mongo_mysql/mongo/common_function.php";
}
require_once(ABS_PATH . '/cls_shopifyapps/config.php');
require_once(ABS_PATH . '/cls_shopifyapps/cls_shopify.php');
require_once(ABS_PATH . '/cls_shopifyapps/cls_shopify_call.php');

$__webhook_arr = array(
    'app/uninstalled',
    'products/create',
    'products/delete',
    'products/update',
    'collections/create',
    'collections/update',
    'collections/delete',
    'customers/create',
    'customers/update',
    'customers/delete'
);

// Get shop parameter - can be in $_GET['shop'] directly
$shop = isset($_GET['shop']) ? trim($_GET['shop']) : '';

// If shop not in GET, try to extract from redirect_uri parameter or referer
if (empty($shop) && isset($_GET['code'])) {
    // Check redirect_uri parameter (Shopify sends this)
    if (isset($_GET['redirect_uri']) && preg_match('/[?&]shop=([^&]+)/', urldecode($_GET['redirect_uri']), $matches)) {
        $shop = urldecode($matches[1]);
    }
    // Also check referer header
    if (empty($shop) && isset($_SERVER['HTTP_REFERER']) && preg_match('/[?&]shop=([^&]+)/', $_SERVER['HTTP_REFERER'], $matches)) {
        $shop = urldecode($matches[1]);
    }
    // Also check current URL
    if (empty($shop) && isset($_SERVER['REQUEST_URI']) && preg_match('/[?&]shop=([^&]+)/', $_SERVER['REQUEST_URI'], $matches)) {
        $shop = urldecode($matches[1]);
    }
}

// Handle OAuth callback even if shop extraction failed (for debugging)
if (isset($_GET['code'])) {
    if (empty($shop)) {
        // Log all available information for debugging
        error_log("OAuth Callback: Shop parameter is EMPTY!");
        error_log("All GET parameters: " . json_encode($_GET));
        error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
        error_log("HTTP_REFERER: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'N/A'));
        
        // Show error page with debugging info
        die('
        <html>
        <head><title>OAuth Error</title></head>
        <body>
            <h1>OAuth Callback Error</h1>
            <p><strong>Error:</strong> Shop parameter is missing from OAuth callback.</p>
            <p>This usually means the redirect URI doesn\'t include the shop parameter.</p>
            <h2>Debug Information:</h2>
            <p><strong>GET Parameters:</strong></p>
            <pre>' . htmlspecialchars(print_r($_GET, true)) . '</pre>
            <p><strong>REQUEST_URI:</strong> ' . htmlspecialchars($_SERVER['REQUEST_URI']) . '</p>
            <p><strong>HTTP_REFERER:</strong> ' . htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'N/A') . '</p>
            <p><strong>Expected:</strong> The redirect URI should be: <code>https://codelocksolutions.com/form_builder/?shop=shop-name.myshopify.com</code></p>
            <p>Check your Shopify Partner Dashboard App Settings to ensure the redirect URI is correct.</p>
        </body>
        </html>');
    }
}

if (!empty($shop)) {
    // Normalize shop name
    $shop = preg_replace('#^https?://#', '', $shop);
    $shop = rtrim($shop, '/');
    $shop = strtolower($shop);
    
    $cls_functions = new common_function($shop);
    $where_query = array(["", "status", "=", "1"]);
    $comeback= $cls_functions->select_result(CLS_TABLE_THIRDPARTY_APIKEY, '*',$where_query);
    $CLS_API_KEY = (isset($comeback['data'][1]['thirdparty_apikey']) && $comeback['data'][1]['thirdparty_apikey'] !== '') ? $comeback['data'][1]['thirdparty_apikey'] : '';
    $SHOPIFY_SECRET = (isset($comeback['data'][2]['thirdparty_apikey']) && $comeback['data'][2]['thirdparty_apikey'] !== '') ? $comeback['data'][2]['thirdparty_apikey'] : '';
    if (mysqli_connect_errno()) {
        echo "Failed : connect to MySQL: " . mysqli_connect_error();
        die;
    }
    if (isset($_GET['code']) && !empty($shop)) {
        // Shop is already normalized above, use it directly
        error_log("OAuth Callback: Processing shop installation for: $shop");
        error_log("OAuth Callback: Shop parameter from GET: " . (isset($_GET['shop']) ? $_GET['shop'] : 'not set'));
        error_log("OAuth Callback: Normalized shop: $shop");
        
        // Get OAuth code and exchange for access token
        $shopifyClient = new ShopifyClient($shop, "", $CLS_API_KEY, $SHOPIFY_SECRET);
        $password = $shopifyClient->getEntrypassword($_GET['code']);
        
        if (empty($password)) {
            error_log("OAuth Callback Error: Failed to get access token for shop: $shop");
            die('Error: Failed to authenticate with Shopify. Please try installing again.');
        }
        
        error_log("OAuth Callback: Successfully obtained access token for: $shop");
        
        // Check if store already exists (check both shop_name and store_name)
        $where_query = array(["", "shop_name", "=", "$shop"], ["OR", "store_name", "=", "$shop"]);
        $comeback_client = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
   
        if ($comeback_client['status'] == 1) {
            // Store exists, update password and redirect
            $shop_row = $comeback_client['data'];
            $update_data = array('password' => $password, 'updated_on' => DATE, 'status' => '1');
            $update_where = array(["", "shop_name", "=", "$shop"]);
            $cls_functions->put_data(TABLE_USER_SHOP, $update_data, $update_where);
            header('Location: ' . SITE_CLIENT_URL . '?store=' . $shop);
            exit;
        } else {
            // Store doesn't exist, create it
            $shopuinfo = shopify_call($password, $shop, "/admin/".CLS_API_VERSIION."/shop.json", array(), 'GET');
            $shopuinfo = json_decode($shopuinfo['response']);
            
            $path = '/admin/api/2022-10/webhooks.json';
            $store_password = md5($SHOPIFY_SECRET . $password);
            $baseurl = "https://" . $CLS_API_KEY . ":" . $password . "@" . $shop . "/";
            $shopify_url = $baseurl . ltrim($path, '/');
            if (!empty($__webhook_arr)) {
                foreach ($__webhook_arr as $topic) {
                    $file_name = str_replace('/', '-', $topic) . '.php';
                    $params = '{"webhook": {"topic":"' . $topic . '", 
                               "address":"https://codelocksolutions.com/form_builder/webhook/' . $file_name . '",
                                "format":"json"
				}}';
                $responce = $cls_functions->register_webhook($shopify_url, $params, $password);
                }
            }
            $asset = array("script_tag" =>
                array(
                    "event" => "onload",
                    "src" => "https://codelocksolutions.com/form_builder/assets/js/shopify_front.js"
                )
            );
            
            $script_add = shopify_call($password, $shop, "/admin/".CLS_API_VERSIION."/script_tags.json", $asset, 'POST',array("Content-Type: application/json"));
            $str = "\n" . date('H:i:s') ."Having a Some problem \n".  json_encode($script_add);
            // Ensure shop info was retrieved successfully
            if (!isset($shopuinfo->shop) || empty($shopuinfo->shop)) {
                error_log("OAuth Callback Error: Failed to retrieve shop info for: $shop");
                die('Error: Failed to retrieve shop information. Please try installing again.');
            }
            
            $store_information = array(
                'email' => isset($shopuinfo->shop->email) ? $shopuinfo->shop->email : '',
                'shop_name' => $shop,
                'store_name' => $shop, 
                'password' => $password,
                'store_idea' => isset($shopuinfo->shop->plan_name) ? $shopuinfo->shop->plan_name : '',
                'address11' => isset($shopuinfo->shop->address1) ? $shopuinfo->shop->address1 : '',
                'address22' => isset($shopuinfo->shop->address2) ? $shopuinfo->shop->address2 : '',
                'city' => isset($shopuinfo->shop->city) ? $shopuinfo->shop->city : '',
                'country_name' => isset($shopuinfo->shop->country_name) ? $shopuinfo->shop->country_name : '',
                'price_pattern' => isset($shopuinfo->shop->price_pattern) ? htmlspecialchars(strip_tags($shopuinfo->shop->price_pattern), ENT_QUOTES, "ISO-8859-1") : '',
                'zip' => isset($shopuinfo->shop->zip) ? $shopuinfo->shop->zip : '',
                'timezone' => isset($shopuinfo->shop->timezone) ? $shopuinfo->shop->timezone : '',
            );
          
            error_log("OAuth Callback: Attempting to register store: $shop");
            $result = $cls_functions->registerNewClientApi($store_information);
            
            if ($result === false) {
                error_log("OAuth Callback Error: Failed to register store: $shop");
                die('Error: Failed to register store. Please check error logs and try again.');
            }
            
            error_log("OAuth Callback: Store successfully registered: $shop");
            
            // Verify store was saved
            $verify_query = array(["", "shop_name", "=", "$shop"]);
            $verify = $cls_functions->select_result(TABLE_USER_SHOP, 'store_user_id', $verify_query, ['single' => true]);
            if ($verify['status'] != 1) {
                error_log("OAuth Callback Error: Store registration verification failed for: $shop");
                die('Error: Store registration failed verification. Please contact support.');
            }
           
            $message = file_get_contents('user/thankemail_template.php');
            $to = $shopuinfo->shop->email;	
            $subject = "Rewriter App"; 
            $headers ="From:codelockinfo@gmail.com"." \r\n";     
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $responceEmail = mail ($to, $subject, $message, $headers);	

            header('Location: ' . SITE_CLIENT_URL . '?store=' . $shop);
            exit;
        }
    } else {
        $shop = isset($_POST['shop']) ? $_POST['shop'] : (isset($_GET['shop']) ? $_GET['shop'] : '');
        
        if (empty($shop)) {
            die('Shop parameter is required');
        }
        
        // Normalize shop name
        $shop = preg_replace('#^https?://#', '', $shop);
        $shop = rtrim($shop, '/');
        $shop = strtolower($shop);
        
        // Check if store exists (check both shop_name and store_name fields)
        $where_query = array(["", "shop_name", "=", "$shop"], ["OR", "store_name", "=", "$shop"]);
        $comeback = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
        
        if ($comeback['status'] == 1) {
            // Store exists, redirect to admin
            header('Location: ' . SITE_CLIENT_URL . '?store=' . $shop);
            exit;
        } else {
            // Store doesn't exist, redirect to OAuth installation
            // Use the full URL with shop parameter for redirect_uri
            $redirect_uri = SITE_PATH . '/?shop=' . urlencode($shop);
            $install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . $CLS_API_KEY . "&scope=" . urlencode(SHOPIFY_SCOPE) . "&redirect_uri=" . urlencode($redirect_uri);
            header("Location: " . $install_url);
            exit;
        }
    }
}
else{
    generate_log('URL_TRACKING', "NOT GET SHOP");
    generate_log('URL_TRACKING', $_POST['shop']."POST DATA");
    generate_log('URL_TRACKING', $_GET['shop']."GET DATA");
    header('Location: https://apps.shopify.com/ReWriter-Mega-Description');
    exit;
}
?>
