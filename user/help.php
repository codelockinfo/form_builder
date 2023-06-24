<?php include_once('cls_header.php'); 
    include_once('dashboard_header.php');
?>
<div class="Polaris-Page">
    <div class="Polaris-Page__Header Polaris-Page__Header--hasBreadcrumbs Polaris-Page__Header--hasSecondaryActions Polaris-Page__Header--hasSeparator">
        <div class="Polaris-Page__MainContent">
            <div class="Polaris-Page__TitleAndActions">
                <div class="Polaris-Page__Title">
                    <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge"><?php _e("Support/FAQ"); ?></h1>
                </div>
            </div>
        </div>
    </div>
    <div class="Polaris-Page__Content">
        <div class="Polaris-Layout">
            <div class="Polaris-Layout__Section">
                <div class="Polaris-Card">
                    <div class="Polaris-Card__Header">
                        <h2 class="Polaris-Heading">FAQ</h2>
                    </div>
                    <div class="Polaris-Card__Section">
                        <div id="accordion" class="accordion">
                            <div class="card mb-2">
                                <div class="card-header collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
                                    <a class="card-title">
                                     How can i revert my font back to original?
                                    </a>
                                </div>
                                <div id="collapseFour" class="collapse" data-parent="#accordion" >
                                    <div class="card-body">
                                            <div class="col-sm-12">
                                                Very simple, you just need to deactivate the ReEriter App or remove the fonts. it will revert to original font of the theme.
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-2">
                                <div class="card-header collapsed" data-toggle="collapse" href="#collapseOne">
                                    <a class="card-title">
                                       Why  i must pay to using Upload font?
                                    </a>
                                </div>
                                <div id="collapseOne" class="collapse" data-parent="#accordion" >
                                    <div class="card-body">
                                            <div class="col-sm-12">
                                                The the fonts will store on Our server and database , so we must a bit fee to maintain it.
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-2">
                                <div class="card-header collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                    <a class="card-title">
                                       How can i change the font for an element only?
                                    </a>
                                </div>
                                <div id="collapseTwo" class="collapse" data-parent="#accordion" >
                                    <div class="card-body">
                                       <div class="row">
                                            <div class="col-sm-6">
                                              ​To change font of anywhere on your site, please use Element Picker feature. ​When click on the magnifying icon, you can see your live store, then select all the element that you want to apply the new font. ​If it does not work, please let us know via Live chat. 
​Thanks
                                            </div>
                                            <div class="col-sm-6">  
                                                <a href="../assets/images/Screenshot_1.png" target="_BLANK">
                                                    <img src="../assets/images/Screenshot_1.png" alt="Avatar" class="image">
                                                    <div class="overlay">
                                                        <div class="text">
                                                            <h5 class="cover_text">
                                                                <i class="fa fa-search-plus cover_symbol"></i>
                                                                <br><?php _e("Click here to zoom"); ?>
                                                            </h5> 
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-2">
                                <div class="card-header collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                    <a class="card-title">
                                     Why i can't find the fonts in Theme Customize?
                                    </a>
                                </div>
                                <div id="collapseThree" class="collapse" data-parent="#accordion" >
                                    <div class="card-body">
                                            <div class="col-sm-12">
                                                The fonts family created by using ReWriter only show in ReWriter app. It will not show on Theme customize. To change or update the fonts, Please using ReWriter  app instead go to Theme customize
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
    <div class="Polaris-Banner Polaris-Banner--statusInfo Polaris-Banner--withinPage" tabindex="0" role="status" aria-live="polite" aria-labelledby="Banner18Heading" aria-describedby="Banner18Content">
        <div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorTealDark Polaris-Icon--hasBackdrop"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><g fill-rule="evenodd"><circle cx="10" cy="10" r="9" fill="currentColor"></circle><path d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0m0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8m1-5v-3a1 1 0 0 0-1-1H9a1 1 0 1 0 0 2v3a1 1 0 0 0 1 1h1a1 1 0 1 0 0-2m-1-5.9a1.1 1.1 0 1 0 0-2.2 1.1 1.1 0 0 0 0 2.2"></path></g></svg></span></div>
        <div>
            <div class="Polaris-Banner__Heading" id="Banner18Heading">
                <p class="Polaris-Heading"><?php _e("Need any other help?"); ?></p>
            </div>
            <div class="Polaris-Banner__Content" id="Banner6Content">
                <p><?php _e("We are always here to help you. Please "); ?><a class="Polaris-Link openChatBox" href="mailto:codelock2021@gmail.com"><?php _e("email us"); ?></a>
                    <?php _e(" or contact us ."); ?>
                </p>
            </div>
        </div>
    </div>
    <?php include_once('cls_footer.php'); ?>