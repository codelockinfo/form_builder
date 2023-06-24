<?php
include_once ('append/connection.php');
include "cls_shopifyapps/config.php";
$shop = "";
if (isset($_REQUEST['shop']) && $_REQUEST['shop'] != "") {
    $shop = $_REQUEST['shop'];
} else {
    $shop = $_SESSION['shop'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo CLS_SITE_NAME; ?> | <?php echo $shop; ?></title>
        <link href="assets/css/polaris.css" rel="stylesheet">
        <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
        <script type="text/javascript">
            ShopifyApp.init({
                forceRedirect: true,
                apiKey: '<?php echo CLS_SHOPIFY_API_KEY; ?>',
                shopOrigin: 'https://<?php echo $shop; ?>'
            });
            ShopifyApp.Bar.loadingOff();
        </script>
    </head>
    <body>
        <div class="Polaris-Page Polaris-Page--fullWidth" style="text-align: center;">
            <div class="Polaris-Page__Content">
                <div class="Polaris-Card">
                    <div class="Polaris-Card__Header">
                        <p class="Polaris-DisplayText Polaris-DisplayText--sizeMedium"><span class="Polaris-TextStyle--variationSubdued"><strong>Sorry, but we weren't able to charge your Shopify account.</strong></span></p>
                    </div>
                    <div class="Polaris-Card__Section">
                        <p><?php echo CLS_SITE_NAME; ?> will not work until you approve this charge.</p>
                        <p>If you want to remove <?php echo CLS_SITE_NAME; ?> instead, please delete the app from the Apps section in Shopify.</p>
                        <br>
                        <a href="https://<?php echo $shop; ?>/admin/apps" class="Polaris-Button"><span class="Polaris-Button__Content"><span>Not Now</span></span></a>
                        <a href="<?php echo SITE_CLIENT_URL;?>?shop=<?php echo $shop;?>" class="Polaris-Button"><span class="Polaris-Button__Content"><span>I'm Interested</span></span></a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
