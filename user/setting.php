<?php 
    include "cls_header.php";
    include_once('dashboard_header.php');   
?>
<body>
    <div class="Polaris-Page">
        <div class="Polaris-Page__Content">
            <div class="Polaris-Layout">
                <div class="Polaris-Layout__AnnotatedSection">
                    <div class="Polaris-Layout__AnnotationWrapper">
                        <div class="Polaris-Layout__AnnotationContent">
                            <div class="Polaris-Card">
                                    <div class="Polaris-Card__Header">
                                        <h2 class="Polaris-Heading">App settings</h2>
                                    </div>
                                    <div class="Polaris-Card__Section display-inline">
                                            <div class="Polaris-Stack__Item seeting-content">
                                                <div class="SettingToggle_Title">
                                                    <span class="Polaris-Icon iocon-set">
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M10 0C4.486 0 0 4.486 0 10s4.486 10 10 10 10-4.486 10-10S15.514 0 10 0zM9 6a1 1 0 1 1 2 0v4a1 1 0 1 1-2 0V6zm1 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"></path>
                                                        </svg>
                                                    </span> &nbsp; Re Writer App is <b class="red"></b></div>
                                            </div>
                                            <div class="Polaris-Stack__Item">
                                                <div class="Polaris-Stack Polaris-Stack--spacingTight">
                                                    <div class="Polaris-Stack__Item cls_design_enaDisa">
                                                        <button data-polaris-unstyled="true" class="Polaris-Button Polaris-Button--primary  enable-btn"  value=""></button>
                                                        <!--<button  data-polaris-unstyled="true" class="Polaris-Button Polaris-Button--primary  enable-btn" value="0" style="display:none;" >Disable</button>-->
                                                      
                                                    </div>
                                                <!--<button class="Polaris-Button" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Disable</span></span></button></div>-->
                                                </div>
                                            </div>
                                    </div>
                            </div>
                            <div class="Polaris-Card pricewidth">
                                <div class="Polaris-Card__Header">
                                    <h1 class="Polaris-Heading headingplanprice">Free to install</h1>
                                    <p>ULTIMATE</p>
                                    <div class="Polaris-TextContainer">
                                        <img class="pricingIcon" src="https://cdn.shopify.com/s/files/1/0070/3666/5911/files/tick_minor_1.png?v=1584070573" alt="Tick mark">All features in FREE, plus
                                    </div>
                                    <div class="Polaris-TextContainer">
                                        <img class="pricingIcon" src="https://cdn.shopify.com/s/files/1/0070/3666/5911/files/tick_minor_1.png?v=1584070573" alt="Tick mark">More to sell
                                    </div>
                                    <div class="Polaris-TextContainer">
                                        <img class="pricingIcon" src="https://cdn.shopify.com/s/files/1/0070/3666/5911/files/tick_minor_1.png?v=1584070573" alt="Tick mark">Write beautiful blog
                                    </div>
                                    <div class="Polaris-TextContainer">
                                        <img class="pricingIcon" src="https://cdn.shopify.com/s/files/1/0070/3666/5911/files/tick_minor_1.png?v=1584070573" alt="Tick mark">Cool product description
                                    </div>
                                    <!--<div class="Polaris-TextContainer">-->
                                    <!--    <img class="pricingIcon" src="https://cdn.shopify.com/s/files/1/0070/3666/5911/files/tick_minor_1.png?v=1584070573" alt="Tick mark">Add table-->
                                    <!--</div>-->
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
    
   seeting_enable_disable(store);

</script>