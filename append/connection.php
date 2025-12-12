<?php
error_reporting(E_ALL);
ini_set("error_reporting", E_ALL);
 session_start();
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    define("DB_SERVER", "localhost");
    define("DB_DATABASE", "u402017191_form_builder");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "");
    define("DB_OBJECT", "mysql"); 
    define('MODE', 'dev');
    define('ABS_PATH', dirname(dirname(__FILE__)));
    define('MAIN_URL', 'http://localhost/form_builder');
    define('CLS_SITE_URL', 'http://localhost/form_builder');
    define('SITE_CLIENT_URL', 'http://localhost/form_builder/user/');
    define('CLS_TRACK_PATH', $_SERVER['DOCUMENT_ROOT']);
    define('SITE_ADMIN_URL', 'http://localhost/form_builder/admin/');
}elseif ($_SERVER['SERVER_NAME'] == 'codelocksolutions.com') {
    define("DB_SERVER", "localhost");
    define("DB_DATABASE", "u402017191_form_builder");
    define("DB_USERNAME", "u402017191_form_builder");
    define("DB_PASSWORD", "Codelock@99");
    define("DB_OBJECT", "mysql");
    define('MODE', 'live');
    define('ABS_PATH', dirname(dirname(__FILE__)));
    define('MAIN_URL', 'https://codelocksolutions.com/form_builder/');
    define('CLS_SITE_URL', 'https://codelocksolutions.com/form_builder/');
     define('CLS_TRACK_PATH', $_SERVER['DOCUMENT_ROOT']);
    define('SITE_CLIENT_URL', 'https://codelocksolutions.com/form_builder/user/');
    define('SITE_ADMIN_URL', 'https://codelocksolutions.com/form_builder/admin/');
} else {
    echo 'Undefine host';
    exit;
}
if (!function_exists('main_url')) {
    function main_url($uri = '') {
        $main_url = '';
        if (MAIN_URL == '') {
            echo "<pre>";
            print_r("Please set MAIN_URL");
            echo "</pre>";
            exit;
        } elseif (MAIN_URL != '' && $uri != '') {
            return MAIN_URL . '/' . $uri;
        } elseif (MAIN_URL != '') {
            return MAIN_URL . '/';
        }
    }

}
$lang = 'english';
require_once ABS_PATH . '/language/' . $lang . '/common_constant_msg.php';
class DB_Class {
    var $db; 
    function __construct() {
        // First, connect without selecting a database to create it if it doesn't exist
        $temp_conn = @mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
        if ($temp_conn) {
            // Create database if it doesn't exist
            $create_db_query = "CREATE DATABASE IF NOT EXISTS `" . DB_DATABASE . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            mysqli_query($temp_conn, $create_db_query);
            mysqli_close($temp_conn);
        }
        
        // Now connect to the database
        $this->db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        
        if (!$this->db) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        try {
            if (DB_OBJECT == "mysql") {
                $odbclink = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
                $odbclink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $odbclink->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);               
                if (true === empty($odbclink)) {
                    echo "No mysql connection";
                    exit;
                }
            } else {
                if (MODE == "dev") {
                    $m = new MongoClient(DB_SERVER);
                    $odbclink = $m->selectDB(DB_DATABASE);
                } else {
                }
                if (true === empty($odbclink)) {    
                    echo "No mongodb connection";
                    exit;
                }
            }
            $GLOBALS['conn'] = $this->db;
            return $this->db;
        } catch (Exception $error) {
            echo $error->getMessage();
            exit;
        }
        }
                
}
define('CLS_SITE_NAME','Form builder');
define('CLS_SITE_EMAIL', '#');
define('CLS_NO_IMAGE','no-image.png');
define('CHARGE', '0.99');
define('CLS_APP_shop_URL', '#');
define('CLS_SITE_COPYRIGHT', CLS_SITE_NAME . ' &copy; ' . date('Y') . ' - Made with team');   
define('CLS_SVG_EYE', '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M17.928 9.628c-.092-.229-2.317-5.628-7.929-5.628s-7.837 5.399-7.929 5.628c-.094.239-.094.505 0 .744.092.229 2.317 5.628 7.929 5.628s7.837-5.399 7.929-5.628c.094-.239.094-.505 0-.744m-7.929 4.372c-2.209 0-4-1.791-4-4s1.791-4 4-4c2.21 0 4 1.791 4 4s-1.79 4-4 4m0-6c-1.104 0-2 .896-2 2s.896 2 2 2c1.105 0 2-.896 2-2s-.895-2-2-2" fill="#000" fill-rule="evenodd"></path></svg>');
define('CLS_SVG_CLOSE_EYE', '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M10 12a2 2 0 0 0 2-2c0-.178-.03-.348-.074-.512l5.78-5.78a1 1 0 1 0-1.413-1.415l-2.61 2.61A7.757 7.757 0 0 0 10 4C4.388 4 2.163 9.4 2.07 9.628a1.017 1.017 0 0 0 0 .744c.055.133.836 2.01 2.583 3.56l-2.36 2.36a1 1 0 1 0 1.414 1.415l5.78-5.78c.165.042.335.073.513.073zm-4-2a4 4 0 0 1 4-4c.742 0 1.432.208 2.025.56l-1.513 1.514A2.004 2.004 0 0 0 10 8a2 2 0 0 0-2 2c0 .178.03.347.074.51L6.56 12.026A3.96 3.96 0 0 1 6 10zm10.144-3.144l-2.252 2.252c.065.288.107.585.107.893a4 4 0 0 1-4 4c-.308 0-.604-.04-.892-.107l-1.682 1.68a7.903 7.903 0 0 0 2.573.428c5.612 0 7.836-5.398 7.928-5.628a1.004 1.004 0 0 0 0-.743c-.044-.112-.596-1.438-1.784-2.774z" fill="#000" fill-rule="evenodd"></path></svg>');
define('CLS_SVG_EDIT', '<svg class="Polaris-Icon__Svg" viewBox="0 0 469.331 469.331"><path d="M438.931,30.403c-40.4-40.5-106.1-40.5-146.5,0l-268.6,268.5c-2.1,2.1-3.4,4.8-3.8,7.7l-19.9,147.4   c-0.6,4.2,0.9,8.4,3.8,11.3c2.5,2.5,6,4,9.5,4c0.6,0,1.2,0,1.8-0.1l88.8-12c7.4-1,12.6-7.8,11.6-15.2c-1-7.4-7.8-12.6-15.2-11.6   l-71.2,9.6l13.9-102.8l108.2,108.2c2.5,2.5,6,4,9.5,4s7-1.4,9.5-4l268.6-268.5c19.6-19.6,30.4-45.6,30.4-73.3   S458.531,49.903,438.931,30.403z M297.631,63.403l45.1,45.1l-245.1,245.1l-45.1-45.1L297.631,63.403z M160.931,416.803l-44.1-44.1   l245.1-245.1l44.1,44.1L160.931,416.803z M424.831,152.403l-107.9-107.9c13.7-11.3,30.8-17.5,48.8-17.5c20.5,0,39.7,8,54.2,22.4   s22.4,33.7,22.4,54.2C442.331,121.703,436.131,138.703,424.831,152.403z" fill="#000" fill-rule="evenodd"></path></svg>');
define('CLS_SVG_DELETE', '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M16 6a1 1 0 1 1 0 2h-1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8H4a1 1 0 1 1 0-2h12zM9 4a1 1 0 1 1 0-2h2a1 1 0 1 1 0 2H9zm2 12h2V8h-2v8zm-4 0h2V8H7v8z" fill="#000" fill-rule="evenodd"></path></svg>');
define('CLS_SVG_NEXT_PAGE', '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M17.707 9.293l-5-5a.999.999 0 1 0-1.414 1.414L14.586 9H3a1 1 0 1 0 0 2h11.586l-3.293 3.293a.999.999 0 1 0 1.414 1.414l5-5a.999.999 0 0 0 0-1.414" fill-rule="evenodd"></path></svg>');
define('CLS_SVG_PREV_PAGE', '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M17 9H5.414l3.293-3.293a.999.999 0 1 0-1.414-1.414l-5 5a.999.999 0 0 0 0 1.414l5 5a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L5.414 11H17a1 1 0 1 0 0-2" fill-rule="evenodd"></path></svg>');
define('CLS_CLS_SVG_RESET', '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path style="fill:#030104;" d="M18,10.473c0-1.948-0.618-3.397-2.066-4.844c-0.391-0.39-1.023-0.39-1.414,0c-0.391,0.391-0.391,1.024,0,1.415C15.599,8.122,16,9.051,16,10.473c0,1.469-0.572,2.85-1.611,3.888c-1.004,1.003-2.078,1.502-3.428,1.593l1.246-1.247c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0L7.086,17l3.707,3.707C10.988,20.902,11.244,21,11.5,21s0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414l-1.337-1.336c1.923-0.082,3.542-0.792,4.933-2.181C17.22,14.36,18,12.477,18,10.473z"/><path style="fill:#030104;" d="M5,10.5c0-1.469,0.572-2.85,1.611-3.889c1.009-1.009,2.092-1.508,3.457-1.594L8.793,6.292c-0.391,0.391-0.391,1.023,0,1.414C8.988,7.902,9.244,8,9.5,8s0.512-0.098,0.707-0.293L13.914,4l-3.707-3.707c-0.391-0.391-1.023-0.391-1.414,0s-0.391,1.023,0,1.414l1.311,1.311C8.19,3.104,6.579,3.814,5.197,5.197C3.78,6.613,3,8.496,3,10.5c0,1.948,0.618,3.397,2.066,4.844c0.195,0.195,0.451,0.292,0.707,0.292s0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.024,0-1.415C5.401,12.851,5,11.922,5,10.5z"></path></svg>');
define('CLS_SVG_SEARCH', '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M8 12c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm9.707 4.293l-4.82-4.82C13.585 10.493 14 9.296 14 8c0-3.313-2.687-6-6-6S2 4.687 2 8s2.687 6 6 6c1.296 0 2.492-.415 3.473-1.113l4.82 4.82c.195.195.45.293.707.293s.512-.098.707-.293c.39-.39.39-1.023 0-1.414z" fill="#95a7b7" fill-rule="evenodd"></path></svg>');
define('CLS_SVG_CIRCLE_PLUS', '<svg class="Polaris-Icon__Svg" viewBox="0 0 510 510" focusable="false" aria-hidden="true"><path d="M255,0C114.75,0,0,114.75,0,255s114.75,255,255,255s255-114.75,255-255S395.25,0,255,0z M382.5,280.5h-102v102h-51v-102    h-102v-51h102v-102h51v102h102V280.5z" fill-rule="evenodd" fill="#3f4eae"></path></svg>');
define('CLS_SVG_CIRCLE_MINUS', '<svg class="Polaris-Icon__Svg" viewBox="0 0 80 80" focusable="false" aria-hidden="true"><path d="M39.769,0C17.8,0,0,17.8,0,39.768c0,21.956,17.8,39.768,39.769,39.768   c21.965,0,39.768-17.812,39.768-39.768C79.536,17.8,61.733,0,39.769,0z M13.261,45.07V34.466h53.014V45.07H13.261z" fill-rule="evenodd" fill="#DE3618"></path></svg>');
define('TABLE_PAGE_MASTER', 'page_master');
define('TABLE_PRODUCT_MASTER', 'product_master');
define('TABLE_BLOGPOST_MASTER','article_master');
define('TABLE_BLOG_MASTER','blog_master');
define('TABLE_USER_SHOP', 'user_shops');
define('TABLE_BACKDROPS', 'store_settings');
define('TABLE_CUSTOMIZE', 'customize');
define('TABLE_COLLECTION_MASTER', 'collection_master');
define('TABLE_FORMS', 'forms');
define('TABLE_ELEMENTS', 'elements');
define('TABLE_FORM_DATA', 'form_data');
define('TABLE_SETTINGS', 'settings');
define('CLS_TABLE_THIRDPARTY_APIKEY','thirdparty_apikey');
define('TABLE_CUSTOMER_MASTER','customer_master');

define("CLS_API_VERSIION",'api/2022-10');
define('CLS_TABLE_FONT_FAMILYS', 'font_family');
define('CLS_TABLE_SHIPMENT_METHOD', 'shipping_type');
define('CLS_TABLE_LOGIN_USER','login_user');
define('DATE', date('Y-m-d H:i:s'));
define('CLS_PAGE_PER', '5');
define("ENTITY_WENT_INCORRECT_report", "Something went incorrect");
define("BACKDROP_GENERATED_SUCCESS_report", "Report updated successfully");
define("PRODUCT_WORD_COLOR_REQUIRED_report", "The Product subject text color required");
define("PRODUCT_WORD_COLOR_FORMAT_report", "The Product subject text color should have used");
define("PRODUCT_FONT_AREA_NEEDED_report", "The Product subject font size needed");
define("PRODUCT_FONT_AREA_ONLYFLOAT_report", "The Product subject font size must be float used");
define("REAL_TOTAL_COLOR_NEEDED_report", "Real total color Needed");
define("REAL_TOTAL_COLOR_PATTERN_report", "Real total color should have hex used");
define("REAL_TOTAL_FONT_SIZE_NEEDED_report", "Real total font size Needed");
define("REAL_TOTAL_FONT_SIZE_ONLYFLOAT_report", "Real total font size must be float used");
define("MARKDOWN_TOTAL_COLOR_NEEDED_report", "Markdown total color Needed");
define("MARKDOWN_TOTAL_COLOR_FORMAT_report", "Markdown total color should have hex used");
define("MARKDOWN_TOTAL_FONT_SIZE_NEEDED_report", "Markdown total font size Needed");
define("MARKDOWN_TOTAL_FONT_SIZE_ONLYFLOAT_report", "Markdown total font size must be float used");
define("PROFILE_WIDTH_NEEDED_report", "Profile width needed");
define("PROFILE_WIDTH_ONLYFLOAT_report", "Profile width must be float used");
define("PROFILE_HEIGHT_REQUIRED_report", "Profile height Needed");
define("PROFILE_HEIGHT_ONLYFLOAT_report", "Profile height must be float used");
define("NOT_ALL_MESSAGE_NEEDED_report", "Not all of above message required");
define("NOT_ALL_COLOR_NEEDED_report", "Not all above message color needed");
define("NOT_ALL_COLOR_PARRERN_report", "Not all above message color should have hex used");
define("NOT_ALL_SIZE_NEEDED_report", "Not all above message font size needed");
define("NOT_ALL_FONT_SIZE_ONLYFLOAT_report", "Not all above message font size must be float used");
define("CLS_POST_NOT_SUBMIT_ERROR_report", "Post  are not used submiT");
define("SENDING_TYPE_IMPORVE_SUCCESS_report", 'Sending type updated successfully.');
define("SENDING_TYPE_GENERATE_SUCCESS_report", 'Sending type generated successfully.');
define("SENDING_TYPE_RECORD_FETCHED_SUCCESS_report", 'Sending type data fetched successfully.');
define("SENDING_TYPE_PLAN_EXPIRE_report", 'Opps ! Your plan is expired :).');
define("SENDING_TYPE_PLAN_NOT_SUBSCRIBE_report", 'Opps ! You have not subcription :).');
define("SENDING_TYPE_RANK_CHANGE_SUCCESS_report", 'Rank changed successfully !');
define("SENDING_TYPE_REMOVED_SUCCESS_report", 'Remove successfully !');
define("SENDING_APP_RANK_CHANGE_SUCCESS_report", 'App Remove changed successfully !');
define("CLS_SOMETHING_WENT_WRONG", "Something went wrong");
define("CLS_STORE_PASSWORD_EMPTY", "Store password empty");
define("CLS_MESSAGE_USERNAME_EMPTY", "Username empty");
define("CLS_MESSAGE_PASSWORD_EMPTY", "Password empty");
define("CLS_MESSAGE_PASSWARD_RESET", "Password Reset");
define("CLS_LOGIN_MESSAGE", "Username & Password Invalid");



if (!isset($__variousLanguageNeeded) || (isset($__variousLanguageNeeded) && $__variousLanguageNeeded === true)) {
    require_once ABS_PATH . '/collection/pomo_library/pomo/mo.php';
    require_once ABS_PATH . '/collection/pomo_library/l10n.php';
    require_once ABS_PATH . '/collection/pomo_library/formatting.php';
    require_once ABS_PATH . '/collection/pomo_library/calender_lang_locale.php'; 
    $database_class = new DB_Class();
    $connection = $GLOBALS['conn'];
    $store_name = (isset($_GET['store']) && $_GET['store'] != '') ? $_GET['store'] : null;
    $user_lang = 'en';

    if (!isset($store_name)) {
        $store_name = (isset($_POST['store']) && $_POST['store'] != '') ? $_POST['store'] : null;
    }
/*
//    if (isset($store_name)) {
//       
//        $query = $connection->prepare("SELECT application_language FROM `user_shops` WHERE store_name = '$store_name' LIMIT 1;");
//        $query->execute();
//        $query->setFetchMode(PDO::FETCH_OBJ);
//        $cls_rows = $query->fetch();
//        $user_lang = $cls_rows->application_language;
//    }
*/
    $locations = $locations = array();
    $locations = array($user_lang);
    $locations[] = ABS_PATH . '/language';

    foreach ($locations as $locale) {
        foreach ($locations as $location) {
            if (file_exists($location . '/' . $locale . '.mo')) {
                load_textdomain('default', $location . '/' . $locale . '.mo');
            }
        }
    }
    $calder_locale_obj = new Calender_Locale();
}
function  generate_log($inventory = 'General', $log_information = 'test') {

  
    if (MODE == 'live') {
       $log_filled_track = CLS_TRACK_PATH.'/form_builder/logs/'. $inventory . '/'. date('Y-m-d') . ".txt";
        $directoryname = dirname($log_filled_track);
        if (!is_dir($directoryname)) {
            mkdir($directoryname,0777, true);
        }
        $cls_myfile = fopen($log_filled_track, "a+") or die("file is not generated");
        
        $str = "\n\n" . '---------------------' . date('H:i:s') . "\n" . $log_information . "\n" . '-*-*-*-*-*-*-*-*-*';
        fwrite($cls_myfile, $str);
        fclose($cls_myfile);
    } else {
        echo $str = str_replace('\n', '<br>', "\n\n" . '---------------------' . date('H:i:s') . "\n" . $log_information . "\n" . '-*-*-*-*-*-*-*-*-*');
    }
}

