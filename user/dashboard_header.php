 
<?php 
$store = (isset($_GET['store']) && $_GET['store'] != '') ? $_GET['store'] : "managedashboard.myshopify.com";
?>
</head>
    <body>
        <div class="Polaris-Page__Header Polaris-Page__Header--hasPagination Polaris-Page__Header--hasBreadcrumbs Polaris-Page__Header--hasRollup Polaris-Page__Header--hasSecondaryActions" style="padding-bottom: 2.4rem;box-shadow: 0px 1px #dfe3e8;">
            <div class="Polaris-Page Polaris-Page--fullWidth">
                <div class="Polaris-Page-Header Polaris-Page-Header__Header--hasBreadcrumbs">
                    <div class="Polaris-Page-Header__MainContent polaris-nav-menu">
                        <div class="Polaris-Page-Header__TitleAndActions cls_header_css">
                            <div class="Polaris-Page__Title cls_header_logo_image">
                                <a href="index.php?store=<?php echo $_SESSION['store']; ?>" class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">  <img  src="<?php echo CLS_SITE_URL; ?>/assets/images/logo-icon.png" alt="your image" class="logoimg" /></a>
                            </div>
                        
                        <div class=" cls_enbledisable_msg">
                            <div class="Polaris-Page__Title cls_msg_enadisa_for_mobile">
                                <span class="app-setting-msg" style="display: none;">
                                        <div class="Polaris-Banner Polaris-Banner--statusWarning Polaris-Banner--withinPage clsdesign_for_msg" tabindex="0" role="alert" aria-live="polite" aria-labelledby="Banner7Heading" aria-describedby="Banner7Content">
                                            <div class="Polaris-Banner__Ribbon">
                                                <span class="Polaris-Icon Polaris-Icon--colorYellowDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop">
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <circle fill="currentColor" cx="10" cy="10" r="9"></circle>
                                                    <path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m0-13a1 1 0 0 0-1 1v4a1 1 0 1 0 2 0V6a1 1 0 0 0-1-1m0 8a1 1 0 1 0 0 2 1 1 0 0 0 0-2"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="Polaris-Banner__Heading" id="Banner7Heading">
                                                <p class="Polaris-Heading">Please Enable App</p>
                                            </div>
                                        </div>
                                </span>
                            </div></div>
                             <div class="cls_enbledisable_btn ">
        
          <a href="setting.php?store=<?php echo $store; ?>"  class="Polaris-Button" style="text-decoration: none;">
            <span class="Polaris-Button__Content">
              <span>App Setting</span>
            </span>
         </a>
        <a href="help.php?store=<?php echo $store; ?>"  class="Polaris-Button" style="text-decoration: none;" >
            <span class="Polaris-Button__Content">
              <span>Help</span>
            </span>
         </a>
        
<!--          <a href="index.php?destroy=true&store=<?php echo $store; ?>"  class="Polaris-Button" style="text-decoration: none;">
            <span class="Polaris-Button__Content">
                <span class="Polaris-Button__Icon">
                    <span class="Polaris-Icon">
                        <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true">
                        <path d="M10 16a1 1 0 1 1 0 2c-4.411 0-8-3.589-8-8s3.589-8 8-8a1 1 0 1 1 0 2c-3.309 0-6 2.691-6 6s2.691 6 6 6zm7.707-6.707a.999.999 0 0 1 0 1.414l-3 3a.997.997 0 0 1-1.414 0 .999.999 0 0 1 0-1.414L14.586 11H10a1 1 0 1 1 0-2h4.586l-1.293-1.293a.999.999 0 1 1 1.414-1.414l3 3z" fill-rule="evenodd" fill="#000"></path>
                        </svg>
                    </span>
                </span>
              <span>logout</span>
            </span>
         </a>-->
   </div>  
                            </div>
                 
<!--     <div class="Polaris-Page-Header__PrimaryAction">
         <a class="Polaris-Button" href="dashboard.php?store=<?php  echo $_SESSION['store'];?>">
            <span class="Polaris-Button__Content">
                <span>Dashboard</span>
            </span>
        </a>
    </div>                        -->
<!--    <div class="Polaris-Page-Header__PrimaryAction">
        <a class="Polaris-Button" href="blog_post.php?store=<?php  echo  $_SESSION['store'];?>">
            <span class="Polaris-Button__Content">
                <span>BlogPost</span>
            </span>
        </a>
    </div>-->
<!--    <div class="Polaris-Page-Header__PrimaryAction">
        <a class="Polaris-Button" href="pages.php?store=<?php echo $_SESSION['store'];?>">
            <span class="Polaris-Button__Content">
                <span>Pages</span>
            </span>
        </a>
    </div>
    <div class="Polaris-Page-Header__PrimaryAction">
        <a class="Polaris-Button" href="products.php?store=<?php echo $_SESSION['store'];?>">
            <span class="Polaris-Button__Content">
                <span>Products</span>
            </span>
        </a>
    </div>
    <div class="Polaris-Page-Header__PrimaryAction">
        <a class="Polaris-Button" href="collection.php?store=<?php echo $_SESSION['store']; ?>">
            <span class="Polaris-Button__Content">
                <span>Collection</span>
            </span>
        </a>
    </div>-->
    <button type="button" class="Polaris-Button bootstrap-navbar cls_menu_mobile" data-toggle="popover"  data-container="body" data-placement="bottom" data-html="true" id="form-components" tabindex="0" aria-haspopup="true" aria-expanded="false">
        <span class="Polaris-Button__Content"><span>Menu</span>
            <span class="Polaris-Button__Icon"><span class="Polaris-Icon"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M5 8l5 5 5-5z" fill-rule="evenodd"></path></svg></span></span></span>
    </button>                    
    <div class="Polaris-Popover__Wrapper" style="display: none">
        <div id="popover-content-form-components" tabindex="-1" class="Polaris-Popover__Content">
            <div class="Polaris-Popover__Pane Polaris-Scrollable Polaris-Scrollable--vertical" data-polaris-scrollable="true" polaris="[object Object]">
                <div class="Polaris-ActionList">
                    <div class="Polaris-ActionList__Section--withoutTitle">
                        <ul class="Polaris-ActionList__Actions">
                             <li><a class="Polaris-ActionList__Item" href="index.php?store=<?php echo $_SESSION['store']; ?>"><div class="Polaris-ActionList__Content">Dashboard</div></a></li>
                            <li><a class="Polaris-ActionList__Item" href="blog_post.php?store=<?php echo $_SESSION['store']; ?>"><div class="Polaris-ActionList__Content">BlogPost</div></a></li>
                            <li><a class="Polaris-ActionList__Item" href="pages.php?store=<?php echo $_SESSION['store']; ?>"><div class="Polaris-ActionList__Content">Pages</div></a></li>
                            <li><a class="Polaris-ActionList__Item" href="products.php?store=<?php echo $_SESSION['store']; ?>"><div class="Polaris-ActionList__Content">Products</div></a></li>
                            <li><a class="Polaris-ActionList__Item" href="collection.php?store=<?php echo $_SESSION['store']; ?>"><div class="Polaris-ActionList__Content">Collection</div></a></li>
                            <li><a class="Polaris-ActionList__Item" href="setting.php?store=<?php echo $store; ?>"><div class="Polaris-ActionList__Content">App Setting</div></a></li>
                        </ul>
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
