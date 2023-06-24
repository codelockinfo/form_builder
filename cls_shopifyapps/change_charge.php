<?php 
if (MODE == 'live') { ?>
    <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
    <script type="text/javascript">
        ShopifyApp.init({
            forceRedirect: true,
            apiKey: '<?php echo CLS_SHOPIFY_API_KEY; ?>',       
            shopOrigin: 'https://<?php echo $shop; ?>'
        });
        ShopifyApp.ready(function () {
            ShopifyApp.Bar.initialize({
                icon: '<?php echo CLS_SITE_URL; ?>assets/images/logo.png',
            });
        });
        ShopifyApp.Bar.loadingOff();
    </script>
<?php
  $current_user = (object)$current_user;
    if ($current_user->is_demand_accept == 0) {
        $array = array("recurring_application_charge" => array('name' => CLS_SITE_NAME, 'charge' => charge, 'return_url' => CLS_SITE_URL . 'user/accept_charge.php?shop=' . $shop, "trial_days" => "7", 'test' => "true"));
        $recurring_application_charge = $cls_functions->cls_recurring_application_charge($array);
        $redirect_url = $recurring_application_charge->recurring_application_charge->confirmation_url;
        $application_language = 'en';
        if(isset($_REQUEST['locale']) && $_REQUEST['locale'] != ''){
            /* $availble_lang_arr = array('en','es','he_IL','fil','fr','ru','sp','pt','gr','jp'); */
            $availble_lang_arr = $cls_functions->cls_app_language_drop_down(TRUE);
            if(in_array($_REQUEST['locale'], $availble_lang_arr)){
                $current_user->application_language = $application_language = $_REQUEST['locale'];
            }
        }
        $fields = array(
            'application_language' => $application_language,
        );
        $where_query = "store_user_id = $current_user->store_user_id AND application_language IS NULL";
        $lang_updated = $cls_functions->put_data(TABLE_USER_SHOP, $fields, $where_query, 1);
        if(!$lang_updated){
            if(MODE == 'live'){
                $str = "\n"."client_id = $current_user->store_user_id AND query is : " ."\n" . $cls_functions->last_query();
                 generate_log('lang_not_update', $str);
            }
        }
        
        ?>
        <script>
            ShopifyApp.ready(function () {
                ShopifyApp.Bar.loadingOff();
                ShopifyApp.redirect("<?php echo $redirect_url; ?>");
            });
        </script>
        <?php
        exit;
    }
}
?>
