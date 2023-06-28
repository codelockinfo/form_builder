<?php
include_once 'append/connection.php';
if(isset($_GET['shop'])){
   header('X-Frame-Options:ALLOW-FROM '.$_GET['shop']);
   header("Content-Security-Policy: frame-ancestors ".$_GET['shop']);
}
else {
    header('X-Frame-Options:SAMEORIGIN');
}
generate_log('URL_TRACKING', "STEP 1 START");
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
    'collections/delete'
);

generate_log('URL_TRACKING', "STEP 1");
if ($_GET['shop'] != "") {
    generate_log('URL_TRACKING', "GET SHOP");
    $cls_functions = new common_function($_GET['shop']);
    if (mysqli_connect_errno()) {
        echo "Failed : connect to MySQL: " . mysqli_connect_error();
        die;
    }
    if (isset($_GET['code'])) {
         generate_log('location', json_encode($_GET) . "GET SHOP");
        $shopifyClient = new ShopifyClient($_GET['shop'], "", CLS_SHOPIFY_API_KEY, SHOPIFY_SECRET);
        $password = $shopifyClient->getEntrypassword($_GET['code']);
        $shop = $_GET['shop'];
        $where_query = array(["", "shop_name", "=", "$shop"],["AND", "status", "=", "1"]);
        $comeback_client = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
   
        if ($comeback_client['status'] == 1) {
            generate_log('URL_TRACKING', "GET status 1");
            $shop_row = $comeback_client['data'];
            header('Location: ' . SITE_CLIENT_URL . '?store=' . $shop);
        } else {
            generate_log('URL_TRACKING', $password. "GET status 0");
            $shopuinfo = shopify_call($password, $shop, "/admin/".CLS_API_VERSIION."/shop.json", array(), 'GET');
             generate_log('URL_TRACKING', json_encode($shopuinfo['response']) ."shopuinfo");
            $shopuinfo = $shopuinfo['response'];
            
            $path = '/admin/api/2021-07/webhooks.json';
            $store_password = md5(SHOPIFY_SECRET . $password);
            $baseurl = "https://" . CLS_SHOPIFY_API_KEY . ":" . $password . "@" . $shop . "/";
            $shopify_url = $baseurl . ltrim($path, '/');
            if (!empty($__webhook_arr)) {
                
            generate_log('webhook',json_encode($__webhook_arr) );
                foreach ($__webhook_arr as $topic) {
                    $file_name = str_replace('/', '-', $topic) . '.php';
                    $params = '{"webhook": {"topic":"' . $topic . '",
                               "address":"https://codelocksolutions.com/cls-rewriter/webhook/' . $file_name . '",
                                "format":"json"
				}}';
				
                    $responce = $cls_functions->register_webhook($shopify_url, $params, $password);
                    generate_log('webhook', json_encode($responce));
                }
            }
            $asset = array("script_tag" =>
                array(
                    "event" => "onload",
                    "src" => "https://codelocksolutions.com/cls-rewriter/assets/js/shopify_front.js"
                )
            );
            
            $script_add = shopify_call($password, $shop, "/admin/".CLS_API_VERSIION."/script_tags.json", $asset, 'POST',array("Content-Type: application/json"));
            $str = "\n" . date('H:i:s') ."Having a Some problem \n".  json_encode($script_add);
            generate_log('fornt_js', $str);
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
            generate_log('URL_TRACKING', json_encode($result) , "   result");
            generate_log('location', 'Location: ' . SITE_CLIENT_URL . '?store=' . $shop);
            // header('Location: ' . SITE_CLIENT_URL . '?store=' . $shop);
            
            header('Location: https://' . $shop . '/admin/apps/' . CLS_SHOPIFY_API_KEY);
            exit;
        }
    } else {
        
        // generate_log('get ', json_encode($_GET) . "GET");
        //   generate_log('post ', json_encode($_POST) . "POST");
        $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $where_query = array(["", "store_name", "=", "$shop"], ["AND", "status", "=", "1"]);
        $comeback = $cls_functions->select_result(TABLE_USER_SHOP, '*', $where_query, ['single' => true]);
        if ($comeback['status'] == 1) {
            generate_log('Location: ' . SITE_CLIENT_URL . '?store=' . $shop    . "install - uninstall location");
            header('Location: ' . SITE_CLIENT_URL . '?store=' . $shop);
        } else {
            $install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . CLS_SHOPIFY_API_KEY . "&scope=" . urlencode(SHOPIFY_SCOPE) . "&redirect_uri=" . urlencode(SITE_PATH);
            header("Location: " . $install_url);
            exit;
        }
    }
}
else{
    generate_log('URL_TRACKING', "NOT GET SHOP");
    generate_log('URL_TRACKING', $_POST['shop']."POST DATA");
    generate_log('URL_TRACKING', $_GET['shop']."GET DATA");
    //   header('Location: https://apps.shopify.com/ReWriter-Mega-Description');
    //   exit;

}
?>
