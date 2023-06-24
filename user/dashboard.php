<?php
ob_start();
include_once('cls_header.php');
//include_once('../append/session.php');
$common_function = new common_function();

if (isset($_GET['store']) && $_GET['store'] != '') {
    include_once('dashboard_header.php');
//    $mysql_date = date('Y-m-d H:i:s');
//    $key = (isset($_GET['key']) ? $_GET['key'] : '');
//    $fields = '*';
//    $where_query = array(["", "url_key", "=", "$key"], ["AND", "used", "=", "0"]);
//    $options_arr = array('single' => true);
//    $comeback = $common_function->select_result(TABLE_CUSTOMIZE, $fields, $where_query, $options_arr);
//    if (!empty($comeback["data"]) && isset($comeback["data"]->url_key)) {
//        $row['used'] = '1';
//        $where_query = array(["", "url_key", "=", "$key"]);
//        $common_function->put_data(TABLE_CUSTOMIZE, $row, $where_query, false);
//        $_SESSION["url_key"] = $key;
//    } else if (!isset($_SESSION["url_key"])) {
//        header('Location:index.php?store=' . $_GET['store']);
//        exit;
//    }
} else {

    header('Location:https://accounts.shopify.com/store-login');
}
?>

<style>
    .hover01 p img {
        -webkit-transform: scale(1);
        transform: scale(1);
        -webkit-transition: .3s ease-in-out;
        transition: .3s ease-in-out;
    }
    .hover01 p:hover img {
        -webkit-transform: scale(1.3);
        transform: scale(1.3);
    }
</style>
<body>
    <div class="Polaris-Page">
                            <div class="Polaris-Page__Title">
                                        <div class="Polaris-Banner Polaris-Banner--statusSuccess  Polaris-Banner--withinPage" tabindex="0" role="alert" aria-live="polite" aria-labelledby="Banner7Heading" aria-describedby="Banner7Content">
                                            <div class="Polaris-Banner__Ribbon">
                                                <span class="Polaris-Icon Polaris-Icon--colorGreenDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop">
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <circle fill="currentColor" cx="10" cy="10" r="9"></circle>
                                                    <path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m0-13a1 1 0 0 0-1 1v4a1 1 0 1 0 2 0V6a1 1 0 0 0-1-1m0 8a1 1 0 1 0 0 2 1 1 0 0 0 0-2"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="Polaris-Banner__Heading" id="Banner7Heading">
                                                <p class="Polaris-Heading">You can able to add, edit and view your products, blogs, and collections so please go to the section and make better content using our functions according to your needs </p>
                                            </div>
                                        </div>
                            </div>
        <div class="Polaris-Page__Content">
            <div class="Polaris-Layout">
                <div class="Polaris-Layout__AnnotatedSection">
                    <div class="Polaris-Layout__AnnotationWrapper">
                        <div class="Polaris-Layout__AnnotationContent">
                            <div class="Polaris-Card">
                                <div class="Polaris-Card__Section">
                                    <div class="hover01 column">
                                        <div class="Polaris-Card__Header"></div>
                                        <div class="Polaris-Layout">
                                            <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary">
                                                <div>
                                                    <div class="Polaris-Card">
                                                        <div class="Polaris-CalloutCard__Container">
                                                            <div class="Polaris-Card__Section">
                                                                <div class="Polaris-CalloutCard">
                                                                    <div class="Polaris-CalloutCard__Content">
                                                                        <div class="Polaris-CalloutCard__Title">
                                                                            <!--<h2 class="Polaris-Heading">Customize the style of your checkout</h2>-->
                                                                        </div>
                                                                        <h2 class="Polaris-Heading">
                                                                            <p>Upload your Blog desciption, change colors and fonts, and more.</p>
                                                                        </h2>
                                                                        <div class="Polaris-CalloutCard__Buttons"><a class="Polaris-Button" href="blog_post.php?store=<?php echo $_SESSION['store']; ?>" data-polaris-unstyled="true"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Add /Edit Blog</span></span></a></div>
                                                                    </div><img src="https://cdn.shopify.com/s/assets/admin/checkout/settings-customizecart-705f57c725ac05be5a34ec20c05b94298cb8afd10aac7bd9c7ad02030f48cfa0.svg" alt="" class="Polaris-CalloutCard__Image">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="PolarisPortalsContainer"></div>
                                                </div>
                                                <div class="Polaris-Card">                                            
                                                    <div class="Polaris-Card__Header header-data">
                                                        <h2 class="Polaris-Heading head-title"><a class="Polaris-Link" href="blog_post.php?store=<?php echo $_SESSION['store']; ?>" data-polaris-unstyled="true">Article</a></h2>
                                                        <h2 class="Polaris-Heading-number numberConvertBlog"></h2>
                                                    </div>
                                                    <div class="Polaris-Card__Section">
                                                        <a href="blog_post.php?store=<?php echo $_SESSION['store']; ?>" style="display:block; width:100%; height:100%;">
                                                            <p class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">
                                                                <img src="<?php echo CLS_SITE_URL; ?>/assets/images/imagesb1.png" style="width:350px;height:100%;margin-left: 15px;" class="mobile_view_img"></p>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>  
                                            <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary">
                                                <div>
                                                    <div class="Polaris-Card">
                                                        <div class="Polaris-CalloutCard__Container">
                                                            <div class="Polaris-Card__Section">
                                                                <div class="Polaris-CalloutCard">
                                                                    <div class="Polaris-CalloutCard__Content">
                                                                        <div class="Polaris-CalloutCard__Title">
                                                                            <!--<h2 class="Polaris-Heading">Customize the style of your checkout</h2>-->
                                                                        </div>
                                                                        <h2 class="Polaris-Heading">
                                                                            <p>Upload your pages contents, change colors and fonts style, and more.</p>
                                                                        </h2>
                                                                        <div class="Polaris-CalloutCard__Buttons"><a class="Polaris-Button" href="pages.php?store=<?php echo $_SESSION['store']; ?>" data-polaris-unstyled="true"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Add /Edit Pages</span></span></a></div>
                                                                    </div><img src="https://cdn.shopify.com/s/assets/admin/checkout/settings-customizecart-705f57c725ac05be5a34ec20c05b94298cb8afd10aac7bd9c7ad02030f48cfa0.svg" alt="" class="Polaris-CalloutCard__Image">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="PolarisPortalsContainer"></div>
                                                </div>
                                                <div class="Polaris-Card">                                            
                                                    <div class="Polaris-Card__Header header-data">
                                                        <h2 class="Polaris-Heading head-title"><a class="Polaris-Link" href="pages.php?store=<?php echo $_SESSION['store']; ?>" data-polaris-unstyled="true">Pages</a></h2>
                                                        <h2 class="Polaris-Heading-number numberConvertPages"></h2>
                                                    </div>
                                                    <div class="Polaris-Card__Section">
                                                        <a href="pages.php?store=<?php echo $_SESSION['store']; ?>" style="display:block; width:100%; height:100%;">
                                                            <p class="Polaris-DisplayText Polaris-DisplayText--sizeLarge"><img src="<?php echo CLS_SITE_URL; ?>/assets/images/img.png" style="width:385px;height:100%;"></p>
                                                        </a> 
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>                               
                                        <div class="Polaris-Card__Header"></div>
                                        <div class="Polaris-Layout">
                                            <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary">
                                                <div>
                                                    <div class="Polaris-Card">
                                                        <div class="Polaris-CalloutCard__Container">
                                                            <div class="Polaris-Card__Section">
                                                                <div class="Polaris-CalloutCard">
                                                                    <div class="Polaris-CalloutCard__Content">
                                                                        <div class="Polaris-CalloutCard__Title">
                                                                            <!--<h2 class="Polaris-Heading">Customize the style of your checkout</h2>-->
                                                                        </div>
                                                                        <h2 class="Polaris-Heading">
                                                                            <p>Upload your product Description, change colors and fonts style, and more.</p>
                                                                        </h2>
                                                                        <div class="Polaris-CalloutCard__Buttons"><a class="Polaris-Button" href="products.php?store=<?php echo $_SESSION['store']; ?>" data-polaris-unstyled="true"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Add /Edit Product</span></span></a></div>
                                                                    </div><img src="https://cdn.shopify.com/s/assets/admin/checkout/settings-customizecart-705f57c725ac05be5a34ec20c05b94298cb8afd10aac7bd9c7ad02030f48cfa0.svg" alt="" class="Polaris-CalloutCard__Image">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="PolarisPortalsContainer"></div>
                                                </div>
                                                <div class="Polaris-Card">                                            
                                                    <div class="Polaris-Card__Header header-data">
                                                        <h2 class="Polaris-Heading head-title"><a class="Polaris-Link" href="products.php?store=<?php echo $_SESSION['store']; ?>" data-polaris-unstyled="true">Product</a></h2>
                                                        <h2 class="Polaris-Heading-number numberConvertProduct"></h2>
                                                    </div>
                                                    <div class="Polaris-Card__Section">
                                                        <a href="products.php?store=<?php echo $_SESSION['store']; ?>" style="display:block; width:100%; height:100%;">
                                                            <p class="Polaris-DisplayText Polaris-DisplayText--sizeLarge"><img src="<?php echo CLS_SITE_URL; ?>/assets/images/unname1.png" style="width:350px;height:100%;"> </p>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>                                 
                                            <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary">
                                                <div>
                                                    <div class="Polaris-Card">
                                                        <div class="Polaris-CalloutCard__Container">
                                                            <div class="Polaris-Card__Section">
                                                                <div class="Polaris-CalloutCard">
                                                                    <div class="Polaris-CalloutCard__Content">
                                                                        <div class="Polaris-CalloutCard__Title">
                                                                            <!--<h2 class="Polaris-Heading">Customize the style of your checkout</h2>-->
                                                                        </div>
                                                                        <h2 class="Polaris-Heading">
                                                                            <p>Upload your collection Description , change colors and fonts style, and more.</p>
                                                                        </h2>
                                                                        <div class="Polaris-CalloutCard__Buttons"><a class="Polaris-Button" href="collection.php?store=<?php echo $_SESSION['store']; ?>" data-polaris-unstyled="true"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Add /Edit Collection</span></span></a></div>
                                                                    </div><img src="https://cdn.shopify.com/s/assets/admin/checkout/settings-customizecart-705f57c725ac05be5a34ec20c05b94298cb8afd10aac7bd9c7ad02030f48cfa0.svg" alt="" class="Polaris-CalloutCard__Image">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="PolarisPortalsContainer"></div>
                                                </div>
                                                <div class="Polaris-Card">                                            
                                                    <div class="Polaris-Card__Header header-data">
                                                        <h2 class="Polaris-Heading head-title"><a class="Polaris-Link" href="collection.php?store=<?php echo $_SESSION['store']; ?>" data-polaris-unstyled="true">Collection</a></h2>
                                                        <h2 class="Polaris-Heading-number numberConvertCollection"></h2>
                                                    </div>
                                                    <div class="Polaris-Card__Section">
                                                        <a href="collection.php?store=<?php echo $_SESSION['store']; ?>" style="display:block; width:100%; height:100%;">
                                                            <p class="Polaris-DisplayText Polaris-DisplayText--sizeLarge"><img src="<?php echo CLS_SITE_URL; ?>/assets/images/imgc1.png"  style="width:357px;height:100%;margin-left: 33px;"></p>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>                     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</body>
</html> 
<script>
    $(document).ready(function () {
        var routineName = 'get_store_product';
        var shopify_api = 'products';
        get_api_data(routineName, shopify_api);
        var routineName = 'get_store_article';
        var shopify_api = 'articles';
        get_api_data(routineName, shopify_api);
        var routineName = 'get_store_pages';
        var shopify_api = 'pages';
        get_api_data(routineName, shopify_api);
        var routineName = 'get_store_collection';
        var shopify_api = 'custom_collections';
        get_api_data(routineName, shopify_api);
        var routineName = 'get_store_blog';
        var shopify_api = 'blogs';
        get_api_data(routineName, shopify_api);

    });

</script>
<?php include_once('dashboard_footer.php'); ?>