<?php
// Handle Shopify App Proxy requests first
$request_uri = $_SERVER['REQUEST_URI'];
if (strpos($request_uri, '/apps/form-builder/') !== false || strpos($request_uri, '/apps/easy-form-builder/') !== false) {
    include_once 'shopify/app-proxy.php';
    exit;
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

if ($_GET['shop'] != "") {
    $cls_functions = new common_function($_GET['shop']);
    $where_query = array(["", "status", "=", "1"]);
    $comeback= $cls_functions->select_result(CLS_TABLE_THIRDPARTY_APIKEY, '*',$where_query);
    $CLS_API_KEY = (isset($comeback['data'][1]['thirdparty_apikey']) && $comeback['data'][1]['thirdparty_apikey'] !== '') ? $comeback['data'][1]['thirdparty_apikey'] : '';
    $SHOPIFY_SECRET = (isset($comeback['data'][2]['thirdparty_apikey']) && $comeback['data'][2]['thirdparty_apikey'] !== '') ? $comeback['data'][2]['thirdparty_apikey'] : '';
    if (mysqli_connect_errno()) {
        echo "Failed : connect to MySQL: " . mysqli_connect_error();
        die;
    }
    if (isset($_GET['code'])) {
        $shopifyClient = new ShopifyClient($_GET['shop'], "", $CLS_API_KEY, $SHOPIFY_SECRET);
        $password = $shopifyClient->getEntrypassword($_GET['code']);
        $shop = $_GET['shop'];
        $where_query = array(["", "shop_name", "=", "$shop"],["AND", "status", "=", "1"]);
        $comeback_client = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
   
        if ($comeback_client['status'] == 1) {
            $shop_row = $comeback_client['data'];
            header('Location: ' . SITE_CLIENT_URL . '?store=' . $shop);
        } else {
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
            $store_information = array(
                'email' => $shopuinfo->shop->email,
                'shop_name' => $shop,
                'store_name' => $shop, 
                'password' => $password,
                'store_idea' => $shopuinfo->shop->plan_name,
                'address11' => $shopuinfo->shop->address1,
                'address22' => $shopuinfo->shop->address2,
                'city' => $shopuinfo->shop->city,
                'country_name' => $shopuinfo->shop->country_name,
                'price_pattern' => htmlspecialchars(strip_tags($shopuinfo->shop->price_pattern), ENT_QUOTES, "ISO-8859-1"),
                'zip' => $shopuinfo->shop->zip,
                'timezone' => $shopuinfo->shop->timezone,
            );
          
            $result = $cls_functions->registerNewClientApi($store_information);
           
            $message = file_get_contents('user/thankemail_template.php');
            $to = $shopuinfo->shop->email;	
            $subject = "Rewriter App"; 
            $headers ="From:codelockinfo@gmail.com"." \r\n";     
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $responceEmail = mail ($to, $subject, $message, $headers);	

            header('Location: https://' . $shop . '/admin/apps/' . $CLS_API_KEY);
            exit;
        }
    } else {
        $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $where_query = array(["", "store_name", "=", "$shop"], ["AND", "status", "=", "1"]);
        $comeback = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
        if ($comeback['status'] == 1) {
            header('Location: ' . SITE_CLIENT_URL . '?store=' . $shop);
        } else {
            $install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . $CLS_API_KEY . "&scope=" . urlencode(SHOPIFY_SCOPE) . "&redirect_uri=" . urlencode(SITE_PATH);
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
