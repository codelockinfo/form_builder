<!-- <!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/polaris_style.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/customstyle.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../assets/js/style.js" type="text/javascript"></script>
    <script src="../assets/js/owl.carousel.js" type="text/javascript"></script>
    <script src="../assets/js/owl.carousel.min.js" type="text/javascript"></script>
    <script src="../assets/js/ckeditor.js" type="text/javascript"></script>
</head> -->
<?php 
include_once('cls_header.php'); 
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : 0;
?>
    <div>
        <div class=" form_header">
        <input type="hidden" class="formid" name="formid" value="<?php echo $form_id ?>">
            <div class="context">
                <div class="context-inner">
                    <div class="item form-name-wrapper">
                        <div class="Polaris-Labelled--hidden sortBy">
                            <div class="Polaris-Connected">
                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary disp_flex_input">
                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                        <input id="PolarisTextField1" placeholder=""
                                            class="Polaris-TextField__Input form_name_form_design" name="form_name_form_design" type="text"
                                            aria-labelledby="PolarisTextField1Label" aria-invalid="false"
                                            value="">
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                    <!-- <button class="Polaris-Button Polaris-Button--primary btnFormSubmit save_loader_show" aria-disabled="false"
                                        type="button">
                                        <span class="Polaris-Button__Content">
                                            <span class="Polaris-Button__Text">
                                                <span>Save</span>
                                            </span>
                                        </span>
                                    </button> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item viewport">
                        <ul class="view_icon">
                            <li class="mobile "data-id="mobile">
                                <span class="Polaris-Icon">
                                    <span class="Polaris-VisuallyHidden"></span>
                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                        aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M3 1.5c0-.8.7-1.5 1.5-1.5h11c.8 0 1.5.7 1.5 1.5v17c0 .8-.7 1.5-1.5 1.5h-11c-.8 0-1.5-.7-1.5-1.5v-17zm2 .5h10v14h-10v-14zm4 15a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2h-2z">
                                        </path>
                                    </svg>
                                </span>
                            </li>
                            <li class="desktop active" data-id="desktop">
                                <span class="Polaris-Icon">
                                    <span class="Polaris-VisuallyHidden"></span>
                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                        aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M2.5 1a1.5 1.5 0 0 0-1.5 1.5v11a1.5 1.5 0 0 0 1.5 1.5h4.5c0 .525-.015.793-.144 1.053-.12.239-.416.61-1.303 1.053a1 1 0 0 0 .469 1.894h7.956a1.004 1.004 0 0 0 .995-.77 1.001 1.001 0 0 0-.544-1.134c-.873-.439-1.166-.806-1.285-1.043-.13-.26-.144-.528-.144-1.053h4.5a1.5 1.5 0 0 0 1.5-1.5v-11a1.5 1.5 0 0 0-1.5-1.5h-15zm8.883 16a2.621 2.621 0 0 1-.027-.053c-.357-.714-.357-1.42-.356-1.895v-.052h-2v.052c0 .475.001 1.181-.356 1.895a2.913 2.913 0 0 1-.027.053h2.766zm5.617-6h-14v2h14v-2z">
                                        </path>
                                    </svg>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="item action">
                        <div class="Polaris-ButtonGroup">
                            <div class="Polaris-ButtonGroup__Item">
                                <button class="Polaris-Button" type="button">
                                    <span class="Polaris-Button__Content">
                                        <span class="Polaris-Button__Text">
                                            <span>Cancel</span>
                                        </span>
                                    </span>
                                </button>
                            </div>
                            <div class="Polaris-ButtonGroup__Item">
                                <div>
                                    <button class="Polaris-Button Polaris-Button--primary saveForm" aria-disabled="false"
                                        type="button">
                                        <span class="Polaris-Button__Content">
                                            <span class="Polaris-Button__Text">
                                                <span>Save</span>
                                            </span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="brand desktop">
                <div class="logo">
                    <a href="index.php?store=<?php echo $store; ?>">
                        <button type="button" class="Polaris-Link backtoindex">
                            <span class="Polaris-Icon">
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                    </a>
                </div>
                <!-- <div class="title">
                    <button type="button" class="Polaris-Link">
                        <span>Back to list</span>
                    </button>
                </div> -->
                <div style="color: rgb(0, 128, 96); padding: 0px 0.8rem;">
                    <button class="Polaris-Button Polaris-Button--outline Polaris-Button--monochrome" type="button">
                        <span class="Polaris-Button__Content">
                            <span class="Polaris-Button__Text">
                                <span>Explore Pricing Plans</span>
                            </span>
                        </span>
                    </button>
                </div>
            </div>

        </div>
        <div class="form_content">
            <div class="preview-card">
                <div class="banner">
                    <div></div>
                </div>
                <div class="preview-box iframe-wrapper desktop">
                    <div class="contact-form">
                        <div class="code-form-app boxed-layout">
                         
                            
                        </div> 
                    </div>
                </div>
            </div>

        </div>
        <div class="element_start">
            <div class="owl-carousel">
                <div class="">
                    <div class="Polaris-Tabs__Wrapper">
                        <ul role="tablist" class="Polaris-Tabs Polaris-Tabs--fitted settingselect">
                            <li class="Polaris-Tabs__TabContainer" role="presentation" data-tab="1">
                                <button id="elements" role="tab" type="button" tabindex="0"
                                    class="Polaris-Tabs__Tab settingsbtn Polaris-Tabs__Tab--selected"
                                    aria-selected="true" aria-controls="elements-fitted-content">
                                    <span class="Polaris-Tabs__Title">Elements</span>
                                </button>
                            </li>
                            <li class="Polaris-Tabs__TabContainer" role="presentation" data-tab="2">
                                <button id="settings" role="tab" type="button" tabindex="-1"
                                    class="Polaris-Tabs__Tab settingsbtn" aria-selected="false"
                                    aria-controls="settings-fitted-content">
                                    <span class="Polaris-Tabs__Title">Settings</span>
                                </button>
                            </li>
                            <li class="Polaris-Tabs__TabContainer" role="presentation" data-tab="3" data-owl="1">
                                <button id="publish" role="tab" type="button" tabindex="-1"
                                    class="Polaris-Tabs__Tab settingsbtn" aria-selected="false"
                                    aria-controls="publishs-fitted-content">
                                    <span class="Polaris-Tabs__Title">Publish</span>
                                </button>

                            </li>
                            <li class="Polaris-Tabs__DisclosureTab" role="presentation">
                                <div>
                                    <button type="button" class="Polaris-Tabs__DisclosureActivator"
                                        aria-label="More tabs" tabindex="0" aria-controls="Polarispopover2"
                                        aria-owns="Polarispopover2" aria-expanded="false">
                                        <span class="Polaris-Tabs__Title">
                                            <span
                                                class="Polaris-Icon Polaris-Icon--colorSubdued Polaris-Icon--applyColor">
                                                <span class="Polaris-VisuallyHidden"></span>
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                    aria-hidden="true">
                                                    <path
                                                        d="M6 10a2 2 0 1 1-4.001-.001 2 2 0 0 1 4.001.001zm6 0a2 2 0 1 1-4.001-.001 2 2 0 0 1 4.001.001zm6 0a2 2 0 1 1-4.001-.001 2 2 0 0 1 4.001.001z">
                                                    </path>
                                                </svg>
                                            </span>
                                        </span>
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="Polaris-Tabs__Panel tab-content  active" id="tab-1" role="tabpanel"
                        aria-labelledby="elements" tabindex="-1">
                        <div class="tabContent">
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item" data-owl="2">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path
                                                            d="M1 2.5v6.5h18v-6.5a1.5 1.5 0 0 0-1.5-1.5h-15a1.5 1.5 0 0 0-1.5 1.5zm1 16.5a1 1 0 0 1-1-1v-2h2v1h1v2h-2zm17-1a1 1 0 0 1-1 1h-2v-2h1v-1h2v2zm-18-4v-3h2v3h-2zm16-3v3h2v-3h-2zm-11 6h3v2h-3v-2zm8 0h-3v2h3v-2z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Header</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="root elementroot">
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Elements</h3>
                                        <div>
                                            <div class="selected_element_set">
                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="root">
                                <div class="builder-item-wrapper btn_add_element">
                                    <div class="list-item" data-owl="6">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path
                                                            d="M3 3h1v-2h-1.5a1.5 1.5 0 0 0-1.5 1.5v1.5h2v-1zm3 0h3v-2h-3v2zm5 0h3v-2h-3v2zm-2 16h-3v-2h3v2zm2 0h3v-2h-3v2zm6-15v-1h-1v-2h1.5a1.5 1.5 0 0 1 1.5 1.5v1.5h-2zm-14 13v-1h-2v1.5a1.5 1.5 0 0 0 1.5 1.5h1.5v-2h-1zm13 0h1v-1h2v1.5a1.5 1.5 0 0 1-1.5 1.5h-1.5v-2zm-6-11a1 1 0 0 1 1 1v2h2a1 1 0 1 1 0 2h-2v2a1 1 0 1 1-2 0v-2h-2a1 1 0 1 1 0-2h2v-2a1 1 0 0 1 1-1zm-9 3v-3h2v3h-2zm0 2v3h2v-3h-2zm16-2v-3h2v3h-2zm0 2v3h2v-3h-2z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>
                                                    <div>Add element</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item"  data-owl="7">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path
                                                            d="M1 2a1 1 0 0 1 1-1h2v2h-1v1h-2v-2zm17-1a1 1 0 0 1 1 1v2h-2v-1h-1v-2h2zm1 16.5v-6.5h-18v6.5a1.5 1.5 0 0 0 1.5 1.5h15a1.5 1.5 0 0 0 1.5-1.5zm0-11.5v3h-2v-3h2zm-16 3v-3h-2v3h2zm11-8v2h-3v-2h3zm-5 2v-2h-3v2h3z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Footer</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="Polaris-Tabs__Panel  tab-content" id="tab-2" role="tabpanel" aria-labelledby="settings"
                        tabindex="-1">
                        <div class="tabContent">
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item" data-owl="8">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path
                                                            d="M0 5.324v10.176a1.5 1.5 0 0 0 1.5 1.5h17a1.5 1.5 0 0 0 1.5-1.5v-10.176l-9.496 5.54a1 1 0 0 1-1.008 0l-9.496-5.54z">
                                                        </path>
                                                        <path
                                                            d="M19.443 3.334a1.494 1.494 0 0 0-.943-.334h-17a1.49 1.49 0 0 0-.943.334l9.443 5.508 9.443-5.508z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Mail</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item"  data-owl="9">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path
                                                            d="M19.685 1.46c.1-.099.2-.198.2-.298.184-.276.113-.551.023-.905l-.024-.092c-.3-.2-.6-.2-.899-.1-.05.05-.1.075-.15.1-.05.025-.1.05-.15.1l-6.495 3.887c-2.598 1.495-4.596 3.688-5.995 6.28 1.499.598 2.798 1.893 3.298 3.488a16.485 16.485 0 0 0 6.295-5.98l3.897-6.48zm-15.688 18.54c2.198 0 3.997-1.794 3.997-3.987s-1.799-3.987-3.997-3.987-3.997 1.794-3.997 3.987v3.987h3.997z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Appearance</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item" data-owl="10">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                            d="M0 10a10 10 0 1 0 20 0 10 10 0 0 0-20 0zm15.2-1.8a1 1 0 0 0-1.4-1.4l-4.8 4.8-2.3-2.3a1 1 0 0 0-1.4 1.4l3 3c.4.4 1 .4 1.4 0l5.5-5.5z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Google reCaptcha</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item" data-owl="11">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                            d="m1.16 16.829 7.498-15c.553-1.106 2.13-1.106 2.683 0l7.498 15a1.5 1.5 0 0 1-1.341 2.171h-14.996a1.5 1.5 0 0 1-1.342-2.171zm8.84-9.829a1 1 0 0 1 1 1v3a1 1 0 0 1-2 0v-3a1 1 0 0 1 1-1zm1 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Error message</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item"data-owl="12">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path
                                                            d="m9.37 8.07 10 4a1 1 0 0 1 .08 1.82l-3.7 1.86-1.85 3.7a1 1 0 0 1-.9.55h-.04a1 1 0 0 1-.89-.63l-4-10a1 1 0 0 1 1.3-1.3zm.337-3.363a1 1 0 0 1-1.707-.707v-3a1 1 0 0 1 2 0v3a1 1 0 0 1-.293.707zm-5 3.586a1 1 0 0 1-.707 1.707h-3a1 1 0 0 1 0-2h3a1 1 0 0 1 .707.293zm-1-6a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414-1.414l-2-2zm12 0a1 1 0 0 0-1.414 0l-2 2a1 1 0 0 0 1.414 1.414l2-2a1 1 0 0 0 0-1.414zm-13.414 12 2-2a1 1 0 0 1 1.414 1.414l-2 2a1 1 0 0 1-1.414-1.414z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>After submit</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item"data-owl="13">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                            d="M6.086 9.882a5 5 0 1 1 3.91-5.063l4.378.73a3 3 0 0 1 5.626 1.451 3 3 0 0 1-3.919 2.857l-2.866 3.763a4 4 0 1 1-5.77-.697l-1.36-3.041zm1.826-.817 1.342 3.005a4.022 4.022 0 0 1 2.407.29l2.83-3.716a2.983 2.983 0 0 1-.446-1.123l-4.375-.729a5.015 5.015 0 0 1-1.757 2.273z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Integration</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="Polaris-Tabs__Panel tab-content" id="tab-3" role="tabpanel" aria-labelledby="publish"
                        tabindex="-1">
                        <div class="tabContent">
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item" data-owl="1">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path
                                                            d="M12.44.44a1.5 1.5 0 0 0-1.062-.44h-6.878a1.5 1.5 0 0 0-1.5 1.5v17a1.5 1.5 0 0 0 1.5 1.5h11a1.5 1.5 0 0 0 1.5-1.5v-12.879a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Other page</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="nested active Publishh d_none tabContent polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Other page</div>
                    </div>
                    <div class="container">
                        <div>
                            <div class="">
                                <div class="form-control">
                                    <div>
                                        <label class="Polaris-Choice">
                                            <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input id="required_login" type="checkbox"
                                                        class="Polaris-Checkbox__Input" aria-invalid="false"
                                                        aria-describedby="PolarisCheckbox26HelpText" role="checkbox"
                                                        aria-checked="false" value="">
                                                    <span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-VisuallyHidden"></span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                focusable="false" aria-hidden="true">
                                                                <path
                                                                    d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                                </path>
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">Required Login</span>
                                        </label>
                                        <div class="Polaris-Choice__Descriptions">
                                            <div class="Polaris-Choice__HelpText" id="PolarisCheckbox26HelpText">Only
                                                allow logged users to access</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control hidden required_message">
                                    <div class="textarea-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <label class="Polaris-Label__Text">Required Login Message</label>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div
                                                        class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                        <textarea class="Polaris-TextField__Input" type="text" rows="2">Please a href='/account/login' title='login'&gt;login&lt;/a&gt; to continue
                                                        </textarea>
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label class="Polaris-Label__Text">Select publication type</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Select selectmain">
                                            <select id="PolarisSelect18" class="select_code">
                                                <option value="embedCode">Embed code</option>
                                                <option value="shortCode">Short code</option>
                                                <option value="popup">Popup</option>
                                                <option value="lightbox">Lightbox</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control selectval embedCode" id="embedCode">
                                    <div class="textfield-wrapper">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label class="Polaris-Label__Text">
                                                    <div>Copy and paste the embed code on your page</div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div
                                                    class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--readOnly">
                                                    <input id="PolarisTextField54" readonly="" placeholder=""
                                                        class="Polaris-TextField__Input" type="text"
                                                        aria-describedby="PolarisTextField54HelpText"
                                                        aria-labelledby="PolarisTextField54Label" aria-invalid="false"
                                                        value="<div class=&quot;globo-formbuilder&quot; data-id=&quot;ZmFsc2U=&quot;></div>">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected__Item">
                                                <button class="Polaris-Button" type="button">
                                                    <span class="Polaris-Button__Content">
                                                        <span class="Polaris-Button__Text">
                                                            <span class="Polaris-Icon">
                                                                <span class="Polaris-VisuallyHidden"></span>
                                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                    focusable="false" aria-hidden="true">
                                                                    <path
                                                                        d="M7.5 2a1.5 1.5 0 0 0-1.5 1.5v9.5a1 1 0 0 0 1 1h9.5a1.5 1.5 0 0 0 1.5-1.5v-9a1.5 1.5 0 0 0-1.5-1.5h-9zm-4 4h.5v10h10v.5a1.5 1.5 0 0 1-1.5 1.5h-9a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5z">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </span>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="Polaris-Labelled__HelpText" id="PolarisTextField54HelpText">Copy
                                            this shortcode and add it to your Shopify page or any Shopify file where you
                                            want to display the form
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control selectval  shortCode" id="shortCode">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField55Label" for="PolarisTextField55"
                                                        class="Polaris-Label__Text">
                                                        <div>Copy and paste the short code on your page
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div
                                                        class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--readOnly">
                                                        <input id="PolarisTextField55" readonly="" placeholder=""
                                                            class="Polaris-TextField__Input" type="text"
                                                            aria-describedby="PolarisTextField55HelpText"
                                                            aria-labelledby="PolarisTextField55Label"
                                                            aria-invalid="false" value="{formbuilder:ZmFsc2U=}">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected__Item"><button class="Polaris-Button"
                                                        type="button"><span class="Polaris-Button__Content"><span
                                                                class="Polaris-Button__Text"><span
                                                                    class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M7.5 2a1.5 1.5 0 0 0-1.5 1.5v9.5a1 1 0 0 0 1 1h9.5a1.5 1.5 0 0 0 1.5-1.5v-9a1.5 1.5 0 0 0-1.5-1.5h-9zm-4 4h.5v10h10v.5a1.5 1.5 0 0 1-1.5 1.5h-9a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5z">
                                                                        </path>
                                                                    </svg></span></span></span></button>
                                                </div>
                                            </div>
                                            <div class="Polaris-Labelled__HelpText" id="PolarisTextField55HelpText">Copy
                                                this shortcode and add
                                                it to your Shopify page or any Shopify file where you want
                                                to display the form
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control selectval popup" id="popup">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField56Label"
                                                        for="PolarisTextField56" class="Polaris-Label__Text">
                                                        <div>Use this code to create popup form display path
                                                        </div>
                                                    </label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div
                                                        class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--readOnly">
                                                        <input id="PolarisTextField56" readonly="" placeholder=""
                                                            class="Polaris-TextField__Input" type="text"
                                                            aria-describedby="PolarisTextField56HelpText"
                                                            aria-labelledby="PolarisTextField56Label"
                                                            aria-invalid="false"
                                                            value="<button class=&quot;globo-formbuilder-open&quot; data-id=&quot;ZmFsc2U=&quot;>Open form</button>">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected__Item"><button class="Polaris-Button"
                                                        type="button"><span class="Polaris-Button__Content"><span
                                                                class="Polaris-Button__Text"><span
                                                                    class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M7.5 2a1.5 1.5 0 0 0-1.5 1.5v9.5a1 1 0 0 0 1 1h9.5a1.5 1.5 0 0 0 1.5-1.5v-9a1.5 1.5 0 0 0-1.5-1.5h-9zm-4 4h.5v10h10v.5a1.5 1.5 0 0 1-1.5 1.5h-9a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5z">
                                                                        </path>
                                                                    </svg></span></span></span></button>
                                                </div>
                                            </div>
                                            <div class="Polaris-Labelled__HelpText" id="PolarisTextField56HelpText">Copy
                                                this code and add it to
                                                your Shopify page or any Shopify file where you want to
                                                display the form</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control selectval lightbox" id="lightbox">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField57Label"
                                                        for="PolarisTextField57" class="Polaris-Label__Text">
                                                        <div>Use this code to have your form appear in a
                                                            lightbox</div>
                                                    </label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div
                                                        class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--readOnly">
                                                        <input id="PolarisTextField57" readonly="" placeholder=""
                                                            class="Polaris-TextField__Input" type="text"
                                                            aria-labelledby="PolarisTextField57Label"
                                                            aria-invalid="false"
                                                            value="<div class=&quot;globo-form-publish-modal lightbox hidden&quot; data-id=&quot;ZmFsc2U=&quot;><div class=&quot;globo-form-modal-content&quot;><div class=&quot;globo-formbuilder&quot; data-id=&quot;ZmFsc2U=&quot;></div></div></div>">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected__Item"><button class="Polaris-Button"
                                                        type="button"><span class="Polaris-Button__Content"><span
                                                                class="Polaris-Button__Text"><span
                                                                    class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M7.5 2a1.5 1.5 0 0 0-1.5 1.5v9.5a1 1 0 0 0 1 1h9.5a1.5 1.5 0 0 0 1.5-1.5v-9a1.5 1.5 0 0 0-1.5-1.5h-9zm-4 4h.5v10h10v.5a1.5 1.5 0 0 1-1.5 1.5h-9a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5z">
                                                                        </path>
                                                                    </svg></span></span></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <label class="Polaris-Choice">
                                        <span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                    id="PolarisCheckbox27" type="checkbox"
                                                    class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                    aria-checked="false" value=""><span
                                                    class="Polaris-Checkbox__Backdrop"></span><span
                                                    class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                            class="Polaris-VisuallyHidden"></span><svg
                                                            viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                            focusable="false" aria-hidden="true">
                                                            <path
                                                                d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                            </path>
                                                        </svg></span></span></span></span><span
                                            class="Polaris-Choice__Label">Add short code to
                                            page</span>
                                    </label>
                                </div>
                                <div class="form-control hidden shortcode">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label id="PolarisSelect19Label">Select page</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Select selectmain ">
                                            <select id="PolarisSelect19" class="select_code">
                                                <option value="89667436713">1kfounder</option>
                                                <option value="87362437289">contact</option>
                                                <option value="90644250793">ddd</option>
                                                <option value="91029668009">DDD</option>
                                                <option value="90743799977">EEEEE</option>
                                                <option value="84723105961">hello everyone</option>
                                                <option value="84966473897">hgf</option>
                                                <option value="87429316777">hiiiii</option>
                                                <option value="82853920937">home</option>
                                                <option value="82854019241">index</option>
                                                <option value="88030445737">login-overlay</option>
                                                <option value="91482849449">Order Tracking Form</option>
                                                <option value="93188620457">our-roots</option>
                                                <option value="87361224873">page-contact-template</option>
                                                <option value="93137731753">picture wall new</option>
                                                <option value="92683239593">Register</option>
                                                <option value="86016000169">Search Results</option>
                                                <option value="83176292521">Seller Profile</option>
                                                <option value="92045836457">shopthelook</option>
                                                <option value="87190601897">Size chart</option>
                                                <option value="79408234665">testing</option>
                                                <option value="86984982697">testing time</option>
                                                <option value="83172851881">tracking page</option>
                                                <option value="82492424361">urvisha</option>
                                                <option value="84966441129">vdvd</option>
                                                <option value="88450629801">wishlist</option>
                                                <option value="92931752105">www</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control hidden shortcode">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label>Select position on page</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Select selectmain">
                                            <select id="PolarisSelect20" class="select_code">
                                                <option value="top">At the top of the page</option>
                                                <option value="bottom">At the bottom of the page</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control hidden  " id="lightbox2">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label class="Polaris-Label__Text">
                                                    <span tabindex="0" data-polaris-tooltip-activator="true">
                                                        <span class="">
                                                            <div class="labelToolTip">Select desired time to
                                                                open form again after close it <span
                                                                    class="Polaris-Icon Polaris-Icon--colorSubdued Polaris-Icon--applyColor"><span
                                                                        class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="M18 10a8 8 0 1 0-16 0 8 8 0 0 0 16 0zm-9 3a1 1 0 1 0 2 0v-2a1 1 0 1 0-2 0v2zm0-6a1 1 0 1 0 2 0 1 1 0 0 0-2 0z">
                                                                        </path>
                                                                    </svg>
                                                                </span></div>
                                                        </span></span></label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Select selectmain">
                                            <select id="PolarisSelect21" class="select_code">
                                                <option value="forever">Automatically open and never show upagain after
                                                    close</option>
                                                <option value="weeks">Automatically open after a period of time (Weeks)
                                                </option>
                                                <option value="days">Automatically open after a period of time (Days)
                                                </option>
                                                <option value="hours">Automatically open after a period of time (Hours)
                                                </option>
                                                <option value="none">Automatically open each time the page loads
                                                </option>
                                            </select>
                                            <!-- <div class="Polaris-Select__Content" aria-hidden="true"
                                                aria-disabled="false"><span
                                                    class="Polaris-Select__SelectedOption">Automatically
                                                    open and never show up again after close</span><span
                                                    class="Polaris-Select__Icon"><span
                                                        class="Polaris-Icon"><span
                                                            class="Polaris-VisuallyHidden"></span><svg
                                                            viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                            focusable="false" aria-hidden="true">
                                                            <path
                                                                d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z">
                                                            </path>
                                                        </svg></span></span></div>
                                            <div class="Polaris-Select__Backdrop"></div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control hidden selectval" id="days">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label>Days</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <div class="Polaris-TextField__Prefix"
                                                        id="PolarisTextField58-Prefix">days</div>
                                                    <input class="Polaris-TextField__Input quentity" min="1"
                                                        type="number" aria-invalid="false" value="1">
                                                    <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                        <div role="button" class="Polaris-TextField__Segment plus"
                                                            tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                                        </path>
                                                                    </svg></span>
                                                            </div>
                                                        </div>
                                                        <div role="button" class="Polaris-TextField__Segment min"
                                                            tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control hidden selectval" id="hours">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label>Hours</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <div class="Polaris-TextField__Prefix"
                                                        id="PolarisTextField59-Prefix">hours</div>
                                                    <input id="PolarisTextField59"
                                                        class="Polaris-TextField__Input hoursadd" min="1" type="number"
                                                        aria-labelledby="PolarisTextField59Label PolarisTextField59-Prefix"
                                                        aria-invalid="false" value="1">
                                                    <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                        <div role="button" class="Polaris-TextField__Segment hourplus"
                                                            tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div role="button" class="Polaris-TextField__Segment houminus"
                                                            tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon">
                                                                <span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                                        </path>
                                                                    </svg></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control hidden selectval" id="weeks">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                                <label>Weeks</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <div class="Polaris-TextField__Prefix"
                                                        id="PolarisTextField60-Prefix">weeks</div>
                                                    <input id="PolarisTextField60"
                                                        class="Polaris-TextField__Input weekadd" min="1" type="number"
                                                        aria-labelledby="PolarisTextField60Label PolarisTextField60-Prefix"
                                                        aria-invalid="false" value="1">
                                                    <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                        <div role="button" class="Polaris-TextField__Segment weekplus"
                                                            tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon">
                                                                <span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                                        </path>
                                                                    </svg></span>
                                                            </div>
                                                        </div>
                                                        <div role="button" class="Polaris-TextField__Segment weekminus"
                                                            tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon">
                                                                <span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                                        </path>
                                                                    </svg></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="polarisformcontrol  headerData">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Header</div>
                    </div>
                    <div class="">
                        <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox12"><span
                                    class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                            id="PolarisCheckbox12" type="checkbox" class="Polaris-Checkbox__Input showHeader"
                                            aria-invalid="false" role="checkbox" aria-checked="true" value=""
                                            checked=""><span class="Polaris-Checkbox__Backdrop"></span><span
                                            class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                    class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20"
                                                    class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path
                                                        d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                    </path>
                                                </svg></span></span></span></span><span
                                    class="Polaris-Choice__Label">Show Header</span></label></div>
                        <div class="form-control">
                            <div class="textfield-wrapper">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField58Label"
                                                for="PolarisTextField58" class="Polaris-Label__Text">
                                                <div>Title</div>
                                            </label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue"><input
                                                    id="PolarisTextField58" placeholder=""
                                                    class="Polaris-TextField__Input headerTitle" type="text"
                                                    aria-labelledby="PolarisTextField58Label" aria-invalid="false"
                                                    >
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <div class="Polaris-Labelled__LabelWrapper">
                                <div class="Polaris-Label">
                                    <label id=":R1n6:Label" for=":R1n6:" class="Polaris-Label__Text">Description</label>
                                </div>
                            </div>
                            <textarea name="content" class="editor headerDescription">
                                &lt;p&gt;This is some sample content.&lt;/p&gt;
                            </textarea>
                        </div>
                    </div>
                </div>

                <div class="polarisformcontrol elementAppend">                  
                </div>
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Add element</div>
                    </div>
                    <div class="container tabContent">
                        <div class="">
                            <div class="form-control">
                                <div class="hidden">
                                    <div class="">
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField148" class="Polaris-TextField__Input"
                                                        type="text" aria-labelledby="PolarisTextField148Label"
                                                        aria-invalid="false" value="email">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <div class="hidden">
                                    <div class="">
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField149" class="Polaris-TextField__Input"
                                                        type="text" aria-labelledby="PolarisTextField149Label"
                                                        aria-invalid="false" value="email">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <div class="textfield-wrapper">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField150Label"
                                                    for="PolarisTextField150" class="Polaris-Label__Text">
                                                    <div>Label</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField150" placeholder=""
                                                        class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField150Label" aria-invalid="false"
                                                        value="Email">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <div class="textfield-wrapper">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField151Label"
                                                    for="PolarisTextField151" class="Polaris-Label__Text">
                                                    <div>Placeholder</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField151" placeholder=""
                                                        class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField151Label" aria-invalid="false"
                                                        value="Email">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control">
                                <div class="textfield-wrapper">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField152Label"
                                                    for="PolarisTextField152" class="Polaris-Label__Text">
                                                    <div>Description</div>
                                                </label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField"><input id="PolarisTextField152"
                                                        placeholder="" class="Polaris-TextField__Input" type="text"
                                                        aria-labelledby="PolarisTextField152Label" aria-invalid="false"
                                                        value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox81"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox81" type="checkbox" class="Polaris-Checkbox__Input"
                                                aria-invalid="false" role="checkbox" aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20"
                                                        class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Limit characters</span></label></div>
                            <div class="form-control hidden">
                                <div class="">
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue"><input
                                                    id="PolarisTextField153" class="Polaris-TextField__Input"
                                                    type="number" aria-labelledby="PolarisTextField153Label"
                                                    aria-invalid="false" value="100">
                                                <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                    <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon"><span
                                                                class="Polaris-Icon"><span
                                                                    class="Polaris-VisuallyHidden"></span><svg
                                                                    viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                    focusable="false" aria-hidden="true">
                                                                    <path
                                                                        d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                                    </path>
                                                                </svg></span></div>
                                                    </div>
                                                    <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon"><span
                                                                class="Polaris-Icon"><span
                                                                    class="Polaris-VisuallyHidden"></span><svg
                                                                    viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                    focusable="false" aria-hidden="true">
                                                                    <path
                                                                        d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                                    </path>
                                                                </svg></span></div>
                                                    </div>
                                                </div>
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox82"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox82" type="checkbox" class="Polaris-Checkbox__Input"
                                                aria-invalid="false" role="checkbox" aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20"
                                                        class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Hide label</span></label></div>
                            <div class="form-control hidden"><label class="Polaris-Choice" for="PolarisCheckbox83"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox83" type="checkbox" class="Polaris-Checkbox__Input"
                                                aria-invalid="false" role="checkbox" aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20"
                                                        class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Keep position of label</span></label>
                            </div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox84"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox84" type="checkbox" class="Polaris-Checkbox__Input"
                                                aria-invalid="false" role="checkbox" aria-checked="true" value=""
                                                checked=""><span class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20"
                                                        class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Required</span></label></div>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox85"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                id="PolarisCheckbox85" type="checkbox" class="Polaris-Checkbox__Input"
                                                aria-invalid="false" role="checkbox" aria-checked="false" value=""><span
                                                class="Polaris-Checkbox__Backdrop"></span><span
                                                class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                        class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20"
                                                        class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path
                                                            d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                        </path>
                                                    </svg></span></span></span></span><span
                                        class="Polaris-Choice__Label">Show required note if hide
                                        label?</span></label></div>

                            <div class="form-control">
                                <div class="chooseInput">
                                    <div class="label">Column width</div>
                                    <div class="chooseItems">
                                        <div class="chooseItem " data-value="3">33%</div>
                                        <div class="chooseItem active" data-value="2">50%</div>
                                        <div class="chooseItem " data-value="1">100%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <button class="Polaris-Button Polaris-Button--destructive" type="button">
                                <span class="Polaris-Button__Content">
                                    <span class="Polaris-Button__Text">Remove this element</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Other page</div>
                    </div>
                    <div class="container tabContent">
                        <div>
                            <div class="">
                                <div class="form-control">
                                    <div class="hidden">
                                        <div class="">
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue"><input
                                                            id="PolarisTextField154" class="Polaris-TextField__Input"
                                                            type="text" aria-labelledby="PolarisTextField154Label"
                                                            aria-invalid="false" value="textarea">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="hidden">
                                        <div class="">
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue"><input
                                                            id="PolarisTextField155" class="Polaris-TextField__Input"
                                                            type="text" aria-labelledby="PolarisTextField155Label"
                                                            aria-invalid="false" value="textarea">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField156Label"
                                                        for="PolarisTextField156" class="Polaris-Label__Text">
                                                        <div>Label</div>
                                                    </label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue"><input
                                                            id="PolarisTextField156" placeholder=""
                                                            class="Polaris-TextField__Input" type="text"
                                                            aria-labelledby="PolarisTextField156Label"
                                                            aria-invalid="false" value="Message">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField157Label"
                                                        for="PolarisTextField157" class="Polaris-Label__Text">
                                                        <div>Placeholder</div>
                                                    </label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue"><input
                                                            id="PolarisTextField157" placeholder=""
                                                            class="Polaris-TextField__Input" type="text"
                                                            aria-labelledby="PolarisTextField157Label"
                                                            aria-invalid="false" value="Message">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField158Label"
                                                        for="PolarisTextField158" class="Polaris-Label__Text">
                                                        <div>Description</div>
                                                    </label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField"><input id="PolarisTextField158"
                                                            placeholder="" class="Polaris-TextField__Input" type="text"
                                                            aria-labelledby="PolarisTextField158Label"
                                                            aria-invalid="false" value="">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox86"><span
                                            class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                    id="PolarisCheckbox86" type="checkbox"
                                                    class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                    aria-checked="false" value=""><span
                                                    class="Polaris-Checkbox__Backdrop"></span><span
                                                    class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                            class="Polaris-VisuallyHidden"></span><svg
                                                            viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                            focusable="false" aria-hidden="true">
                                                            <path
                                                                d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                            </path>
                                                        </svg></span></span></span></span><span
                                            class="Polaris-Choice__Label">Limit characters</span></label></div>
                                <div class="form-control hidden">
                                    <div class="">
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue"><input
                                                        id="PolarisTextField159" class="Polaris-TextField__Input"
                                                        type="number" aria-labelledby="PolarisTextField159Label"
                                                        aria-invalid="false" value="100">
                                                    <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                        <div role="button" class="Polaris-TextField__Segment"
                                                            tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon"><span
                                                                    class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z">
                                                                        </path>
                                                                    </svg></span></div>
                                                        </div>
                                                        <div role="button" class="Polaris-TextField__Segment"
                                                            tabindex="-1">
                                                            <div class="Polaris-TextField__SpinnerIcon"><span
                                                                    class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z">
                                                                        </path>
                                                                    </svg></span></div>
                                                        </div>
                                                    </div>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox87"><span
                                            class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                    id="PolarisCheckbox87" type="checkbox"
                                                    class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                    aria-checked="false" value=""><span
                                                    class="Polaris-Checkbox__Backdrop"></span><span
                                                    class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                            class="Polaris-VisuallyHidden"></span><svg
                                                            viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                            focusable="false" aria-hidden="true">
                                                            <path
                                                                d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                            </path>
                                                        </svg></span></span></span></span><span
                                            class="Polaris-Choice__Label">Hide label</span></label></div>
                                <div class="form-control hidden"><label class="Polaris-Choice"
                                        for="PolarisCheckbox88"><span class="Polaris-Choice__Control"><span
                                                class="Polaris-Checkbox"><input id="PolarisCheckbox88" type="checkbox"
                                                    class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                    aria-checked="false" value=""><span
                                                    class="Polaris-Checkbox__Backdrop"></span><span
                                                    class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                            class="Polaris-VisuallyHidden"></span><svg
                                                            viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                            focusable="false" aria-hidden="true">
                                                            <path
                                                                d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                            </path>
                                                        </svg></span></span></span></span><span
                                            class="Polaris-Choice__Label">Keep position of label</span></label></div>
                                <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox89"><span
                                            class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                    id="PolarisCheckbox89" type="checkbox"
                                                    class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                    aria-checked="true" value="" checked=""><span
                                                    class="Polaris-Checkbox__Backdrop"></span><span
                                                    class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                            class="Polaris-VisuallyHidden"></span><svg
                                                            viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                            focusable="false" aria-hidden="true">
                                                            <path
                                                                d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                            </path>
                                                        </svg></span></span></span></span><span
                                            class="Polaris-Choice__Label">Required</span></label></div>
                                <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox90"><span
                                            class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                    id="PolarisCheckbox90" type="checkbox"
                                                    class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox"
                                                    aria-checked="false" value=""><span
                                                    class="Polaris-Checkbox__Backdrop"></span><span
                                                    class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span
                                                            class="Polaris-VisuallyHidden"></span><svg
                                                            viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                            focusable="false" aria-hidden="true">
                                                            <path
                                                                d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                            </path>
                                                        </svg></span></span></span></span><span
                                            class="Polaris-Choice__Label">Show required note if hide
                                            label?</span></label></div>
                                <div class="form-control">
                                    <div class="chooseInput">
                                        <div class="label">Column width</div>
                                        <div class="chooseItems">
                                            <div class="chooseItem " data-value="3">33%</div>
                                            <div class="chooseItem " data-value="2">50%</div>
                                            <div class="chooseItem active" data-value="1">100%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-control"><button
                                class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth"
                                type="button"><span class="Polaris-Button__Content"><span
                                        class="Polaris-Button__Text"><span>Remove this
                                            element</span></span></span></button></div>
                    </div>
                </div>  
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Add element</div>
                    </div>
                    <div class="nested toggle active">
                        <div class="container tabContent">
                            <div class="">
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Input</h3>
                                        <div>
                                            <div class="setvalue_element">
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Selects</h3>
                                        <div>
                                            <div class="setvalue_element_select">
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Static text</h3>
                                        <div>
                                            <div class="setvalue_element_static">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Structure</h3>
                                        <div>
                                            <div class="setvalue_element_structure">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Customization</h3>
                                        <div>
                                            <div class="setvalue_element_customization">
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading"></h3>
                                        <div>
                                            <div class=""></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Footer</div>
                    </div>
                    <div class="">
                        <div class="container tabContent">
                            <div class="container">
                                <div class="footerData">
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                            <div class="">
                                                <textarea name="content" class="editor">
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                            <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label"><label id="PolarisTextField17Label"
                                                            for="PolarisTextField17" class="Polaris-Label__Text">
                                                            <div>Submit text</div>
                                                        </label></div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div
                                                        class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                            <input id="PolarisTextField17" placeholder=""
                                                                class="Polaris-TextField__Input submitText" type="text"
                                                                aria-labelledby="PolarisTextField17Label"
                                                                aria-invalid="false" value="Submit">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control"><label class="Polaris-Choice"
                                            for="PolarisCheckbox6"><span class="Polaris-Choice__Control"><span
                                                    class="Polaris-Checkbox"><input id="PolarisCheckbox6"
                                                        type="checkbox" class="Polaris-Checkbox__Input resetButton"
                                                        aria-invalid="false" role="checkbox" aria-checked="false"
                                                        value=""><span
                                                        class="Polaris-Checkbox__Backdrop"></span><span
                                                        class="Polaris-Checkbox__Icon"><span
                                                            class="Polaris-Icon"><span
                                                                class="Polaris-VisuallyHidden"></span><svg
                                                                viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                focusable="false" aria-hidden="true">
                                                                <path
                                                                    d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                                </path>
                                                            </svg></span></span></span></span><span
                                                class="Polaris-Choice__Label">Reset button</span></label></div>
                                    <div class="form-control hidden reset">
                                        <div class="textfield-wrapper">
                                            <div class="">
                                                <div class="Polaris-Labelled__LabelWrapper">
                                                    <div class="Polaris-Label"><label id="PolarisTextField18Label"
                                                            for="PolarisTextField18" class="Polaris-Label__Text">
                                                            <div>Reset button text</div>
                                                        </label></div>
                                                </div>
                                                <div class="Polaris-Connected">
                                                    <div
                                                        class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                            <input id="PolarisTextField18" placeholder=""
                                                                class="Polaris-TextField__Input resetbuttonText" type="text"
                                                                aria-labelledby="PolarisTextField18Label"
                                                                aria-invalid="false" value="Reset">
                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control"><label class="Polaris-Choice"
                                            for="PolarisCheckbox7"><span class="Polaris-Choice__Control"><span
                                                    class="Polaris-Checkbox"><input id="PolarisCheckbox7"
                                                        type="checkbox" class="Polaris-Checkbox__Input fullFooterButton"
                                                        aria-invalid="false" role="checkbox" aria-checked="false"
                                                        value=""><span
                                                        class="Polaris-Checkbox__Backdrop"></span><span
                                                        class="Polaris-Checkbox__Icon"><span
                                                            class="Polaris-Icon"><span
                                                                class="Polaris-VisuallyHidden"></span><svg
                                                                viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                focusable="false" aria-hidden="true">
                                                                <path
                                                                    d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                                </path>
                                                            </svg></span></span></span></span><span
                                                class="Polaris-Choice__Label">Full width footer
                                                button</span></label></div>
                                    <div class="form-control alignment" >
                                        <div class="chooseInput">
                                            <div class="label">Alignment</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem active" data-value="0">Left</div>
                                                <div class="chooseItem" data-value="1">Center</div>
                                                <div class="chooseItem" data-value="2">Right</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Mail</div>
                    </div>
                    <div class="">
                        <div class="container">
                            <div>
                                <div class="">
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item"  data-owl="14">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M10 0c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm6.24 15a7.99 7.99 0 0 1-12.48 0 7.99 7.99 0 0 1 12.48 0zm-6.24-5a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>Admin</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10 0c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm6.24 15a7.99 7.99 0 0 1-12.48 0 7.99 7.99 0 0 1 12.48 0zm-6.24-5a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Auto Responder</div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Appearance</div>
                    </div>
                    <div class="">
                        <div class="container">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                        <div class="chooseInput">
                                        <div class="label">Layout</div>
                                        <div class="chooseItems">
                                            <div class="chooseItem ">Default</div>
                                            <div class="chooseItem active">Boxed</div>
                                            <div class="chooseItem ">Float</div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField57Label" for="PolarisTextField57" class="Polaris-Label__Text">Width</label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <div class="Polaris-TextField__Prefix" id="PolarisTextField57-Prefix">px</div>
                                                    <input id="PolarisTextField57" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField57Label PolarisTextField57-Prefix" aria-invalid="false" value="600">
                                                    <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                    <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon">
                                                            <span class="Polaris-Icon">
                                                                <span class="Polaris-VisuallyHidden"></span>
                                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                <path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z"></path>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon">
                                                            <span class="Polaris-Icon">
                                                                <span class="Polaris-VisuallyHidden"></span>
                                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                <path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect66Label" for="PolarisSelect66" class="Polaris-Label__Text">Style</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect66" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="classic">Classic</option>
                                                <option value="flat">Flat</option>
                                                <option value="classic_rounded">Classic rounded</option>
                                                <option value="flat_rounded">Flat rounded</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Classic</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="color">
                                        <button class="preview" style="background: rgb(235, 18, 86);"></button>
                                        <div class="label">Main color</div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="color">
                                        <button class="preview" style="background: rgb(0, 0, 0);"></button>
                                        <div class="label">Heading color</div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="color">
                                        <button class="preview" style="background: rgb(0, 0, 0);"></button>
                                        <div class="label">Label color</div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="color">
                                        <button class="preview" style="background: rgb(108, 117, 125);"></button>
                                        <div class="label">Description color</div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="color">
                                        <button class="preview" style="background: rgb(0, 0, 0);"></button>
                                        <div class="label">Option color</div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="color">
                                        <button class="preview" style="background: rgb(0, 0, 0);"></button>
                                        <div class="label">Paragraph color</div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="color">
                                        <button class="preview" style="background: rgb(255, 255, 255);"></button>
                                        <div class="label">Paragraph background</div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect67Label" for="PolarisSelect67" class="Polaris-Label__Text">Background</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect67" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="none">None</option>
                                                <option value="color">Color</option>
                                                <option value="image">Image</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Image</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="color">
                                        <button class="preview" style="background: rgb(255, 255, 255);"></button>
                                        <div class="label">Background color</div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField58Label" for="PolarisTextField58" class="Polaris-Label__Text">
                                                    <div>Background image url</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField58" placeholder="https://" class="Polaris-TextField__Input" type="text" aria-describedby="PolarisTextField58HelpText" aria-labelledby="PolarisTextField58Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Labelled__HelpText" id="PolarisTextField58HelpText"><span>Upload your background image to Shopify then use it's URL to fill in. <a href="https://help.shopify.com/en/manual/shopify-admin/productivity-tools/file-uploads" target="_blank">Learn more</a></span></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect68Label" for="PolarisSelect68" class="Polaris-Label__Text">Image alignment</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect68" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="top">Top</option>
                                                <option value="center">Middle</option>
                                                <option value="bottom">Bottom</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Top</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="picker">
                                        <div class="label">Floating Icon</div>
                                        <div class="pickerbox">
                                            <div class="">
                                                <div class="preview">
                                                    <span class="">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="envelope" class="svg-inline--fa fa-envelope fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"></path>
                                                    </svg>
                                                    </span>
                                                </div>
                                                <div class="action"><button class="change">Floating Icon</button><button class="remove">Remove</button></div>
                                            </div>
                                        </div>
                                        <div class="pickerList false">
                                            <div class="header">
                                                <button class="ui-btn back-icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="m11.414 10 6.293-6.293a1 1 0 1 0-1.414-1.414l-6.293 6.293-6.293-6.293a1 1 0 0 0-1.414 1.414l6.293 6.293-6.293 6.293a1 1 0 1 0 1.414 1.414l6.293-6.293 6.293 6.293a.998.998 0 0 0 1.707-.707.999.999 0 0 0-.293-.707l-6.293-6.293z"></path>
                                                    </svg>
                                                    </span>
                                                </button>
                                                <div class="title">Floating Icon</div>
                                            </div>
                                            <div class="container grid">
                                                <div class="item">
                                                    <div class="label">Laugh solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="laugh" class="svg-inline--fa fa-laugh fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512">
                                                        <path fill="currentColor" d="M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm80 152c17.7 0 32 14.3 32 32s-14.3 32-32 32-32-14.3-32-32 14.3-32 32-32zm-160 0c17.7 0 32 14.3 32 32s-14.3 32-32 32-32-14.3-32-32 14.3-32 32-32zm88 272h-16c-73.4 0-134-55-142.9-126-1.2-9.5 6.3-18 15.9-18h270c9.6 0 17.1 8.4 15.9 18-8.9 71-69.5 126-142.9 126z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Grin solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="grin" class="svg-inline--fa fa-grin fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512">
                                                        <path fill="currentColor" d="M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm80 168c17.7 0 32 14.3 32 32s-14.3 32-32 32-32-14.3-32-32 14.3-32 32-32zm-160 0c17.7 0 32 14.3 32 32s-14.3 32-32 32-32-14.3-32-32 14.3-32 32-32zm80 256c-60.6 0-134.5-38.3-143.8-93.3-2-11.8 9.3-21.6 20.7-17.9C155.1 330.5 200 336 248 336s92.9-5.5 123.1-15.2c11.3-3.7 22.6 6.1 20.7 17.9-9.3 55-83.2 93.3-143.8 93.3z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Gift solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="gift" class="svg-inline--fa fa-gift fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="currentColor" d="M32 448c0 17.7 14.3 32 32 32h160V320H32v128zm256 32h160c17.7 0 32-14.3 32-32V320H288v160zm192-320h-42.1c6.2-12.1 10.1-25.5 10.1-40 0-48.5-39.5-88-88-88-41.6 0-68.5 21.3-103 68.3-34.5-47-61.4-68.3-103-68.3-48.5 0-88 39.5-88 88 0 14.5 3.8 27.9 10.1 40H32c-17.7 0-32 14.3-32 32v80c0 8.8 7.2 16 16 16h480c8.8 0 16-7.2 16-16v-80c0-17.7-14.3-32-32-32zm-326.1 0c-22.1 0-40-17.9-40-40s17.9-40 40-40c19.9 0 34.6 3.3 86.1 80h-86.1zm206.1 0h-86.1c51.4-76.5 65.7-80 86.1-80 22.1 0 40 17.9 40 40s-17.9 40-40 40z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">File contract solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="file-contract" class="svg-inline--fa fa-file-contract fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                                        <path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zM64 72c0-4.42 3.58-8 8-8h80c4.42 0 8 3.58 8 8v16c0 4.42-3.58 8-8 8H72c-4.42 0-8-3.58-8-8V72zm0 64c0-4.42 3.58-8 8-8h80c4.42 0 8 3.58 8 8v16c0 4.42-3.58 8-8 8H72c-4.42 0-8-3.58-8-8v-16zm192.81 248H304c8.84 0 16 7.16 16 16s-7.16 16-16 16h-47.19c-16.45 0-31.27-9.14-38.64-23.86-2.95-5.92-8.09-6.52-10.17-6.52s-7.22.59-10.02 6.19l-7.67 15.34a15.986 15.986 0 0 1-14.31 8.84c-.38 0-.75-.02-1.14-.05-6.45-.45-12-4.75-14.03-10.89L144 354.59l-10.61 31.88c-5.89 17.66-22.38 29.53-41 29.53H80c-8.84 0-16-7.16-16-16s7.16-16 16-16h12.39c4.83 0 9.11-3.08 10.64-7.66l18.19-54.64c3.3-9.81 12.44-16.41 22.78-16.41s19.48 6.59 22.77 16.41l13.88 41.64c19.77-16.19 54.05-9.7 66 14.16 2.02 4.06 5.96 6.5 10.16 6.5zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">File alt solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="file-alt" class="svg-inline--fa fa-file-alt fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                                        <path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm64 236c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-64c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-72v8c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm96-114.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Evelope solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="envelope" class="svg-inline--fa fa-envelope fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Evelope regular</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="envelope" class="svg-inline--fa fa-envelope fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Edit solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="edit" class="svg-inline--fa fa-edit fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                        <path fill="currentColor" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Comment dots solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="comment-dots" class="svg-inline--fa fa-comment-dots fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="currentColor" d="M256 32C114.6 32 0 125.1 0 240c0 49.6 21.4 95 57 130.7C44.5 421.1 2.7 466 2.2 466.5c-2.2 2.3-2.8 5.7-1.5 8.7S4.8 480 8 480c66.3 0 116-31.8 140.6-51.4 32.7 12.3 69 19.4 107.4 19.4 141.4 0 256-93.1 256-208S397.4 32 256 32zM128 272c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32zm128 0c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32zm128 0c-17.7 0-32-14.3-32-32s14.3-32 32-32 32 14.3 32 32-14.3 32-32 32z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Calendar regular</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="calendar" class="svg-inline--fa fa-calendar fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                        <path fill="currentColor" d="M400 64h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zm-6 400H54c-3.3 0-6-2.7-6-6V160h352v298c0 3.3-2.7 6-6 6z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Calendar alt regular</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="calendar-alt" class="svg-inline--fa fa-calendar-alt fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                        <path fill="currentColor" d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Bullhom solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bullhorn" class="svg-inline--fa fa-bullhorn fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                        <path fill="currentColor" d="M576 240c0-23.63-12.95-44.04-32-55.12V32.01C544 23.26 537.02 0 512 0c-7.12 0-14.19 2.38-19.98 7.02l-85.03 68.03C364.28 109.19 310.66 128 256 128H64c-35.35 0-64 28.65-64 64v96c0 35.35 28.65 64 64 64h33.7c-1.39 10.48-2.18 21.14-2.18 32 0 39.77 9.26 77.35 25.56 110.94 5.19 10.69 16.52 17.06 28.4 17.06h74.28c26.05 0 41.69-29.84 25.9-50.56-16.4-21.52-26.15-48.36-26.15-77.44 0-11.11 1.62-21.79 4.41-32H256c54.66 0 108.28 18.81 150.98 52.95l85.03 68.03a32.023 32.023 0 0 0 19.98 7.02c24.92 0 32-22.78 32-32V295.13C563.05 284.04 576 263.63 576 240zm-96 141.42l-33.05-26.44C392.95 311.78 325.12 288 256 288v-96c69.12 0 136.95-23.78 190.95-66.98L480 98.58v282.84z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Bookmark solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bookmark" class="svg-inline--fa fa-bookmark fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                                        <path fill="currentColor" d="M0 512V48C0 21.49 21.49 0 48 0h288c26.51 0 48 21.49 48 48v464L192 400 0 512z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Bell solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bell" class="svg-inline--fa fa-bell fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                        <path fill="currentColor" d="M224 512c35.32 0 63.97-28.65 63.97-64H160.03c0 35.35 28.65 64 63.97 64zm215.39-149.71c-19.32-20.76-55.47-51.99-55.47-154.29 0-77.7-54.48-139.9-127.94-155.16V32c0-17.67-14.32-32-31.98-32s-31.98 14.33-31.98 32v20.84C118.56 68.1 64.08 130.3 64.08 208c0 102.3-36.15 133.53-55.47 154.29-6 6.45-8.66 14.16-8.61 21.71.11 16.4 12.98 32 32.1 32h383.8c19.12 0 32-15.6 32.1-32 .05-7.55-2.61-15.27-8.61-21.71z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Tag solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="tag" class="svg-inline--fa fa-tag fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="currentColor" d="M0 252.118V48C0 21.49 21.49 0 48 0h204.118a48 48 0 0 1 33.941 14.059l211.882 211.882c18.745 18.745 18.745 49.137 0 67.882L293.823 497.941c-18.745 18.745-49.137 18.745-67.882 0L14.059 286.059A48 48 0 0 1 0 252.118zM112 64c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Phone alt solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="phone-alt" class="svg-inline--fa fa-phone-alt fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="currentColor" d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6A370.66 370.66 0 0 1 130.6 204.11l60.6-49.6a23.94 23.94 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6.61l-104 24A24 24 0 0 0 0 48c0 256.5 207.9 464 464 464a24 24 0 0 0 23.4-18.6l24-104a24.29 24.29 0 0 0-14.01-27.6z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Marker solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="marker" class="svg-inline--fa fa-marker fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="currentColor" d="M93.95 290.03A327.038 327.038 0 0 0 .17 485.11l-.03.23c-1.7 15.28 11.21 28.2 26.49 26.51a327.02 327.02 0 0 0 195.34-93.8l75.4-75.4-128.02-128.02-75.4 75.4zM485.49 26.51c-35.35-35.35-92.67-35.35-128.02 0l-21.76 21.76-36.56-36.55c-15.62-15.62-40.95-15.62-56.56 0L138.47 115.84c-6.25 6.25-6.25 16.38 0 22.63l22.62 22.62c6.25 6.25 16.38 6.25 22.63 0l87.15-87.15 19.59 19.59L191.98 192 320 320.02l165.49-165.49c35.35-35.35 35.35-92.66 0-128.02z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="label">Location arrow solid</div>
                                                    <div class="value">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="location-arrow" class="svg-inline--fa fa-location-arrow fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="currentColor" d="M444.52 3.52L28.74 195.42c-47.97 22.39-31.98 92.75 19.19 92.75h175.91v175.91c0 51.17 70.36 67.17 92.75 19.19l191.9-415.78c15.99-38.39-25.59-79.97-63.97-63.97z"></path>
                                                    </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div></div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField59Label" for="PolarisTextField59" class="Polaris-Label__Text">
                                                    <div>Floating text</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField59" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField59Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox24">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox24" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Display the form on all pages</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect69Label" for="PolarisSelect69" class="Polaris-Label__Text">Position</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect69" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="top right">Top - Right</option>
                                                <option value="half-top right">Half top - Right</option>
                                                <option value="vertical-center right">Center - Right</option>
                                                <option value="half-bottom right">Half bottom - Right</option>
                                                <option value="bottom right">Bottom - Right</option>
                                                <option value="bottom left">Bottom - Left</option>
                                                <option value="half-bottom left">Half bottom - Left</option>
                                                <option value="vertical-center left">Center - Left</option>
                                                <option value="half-top left">Half top - Left</option>
                                                <option value="top left">Top - Left</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Bottom - Right</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="hidden">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField60Label" for="PolarisTextField60" class="Polaris-Label__Text">Form type</label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField60" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField60Label" aria-invalid="false" value="normalForm">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="hidden">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label"><label id="PolarisTextField61Label" for="PolarisTextField61" class="Polaris-Label__Text">New template</label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField61" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField61Label" aria-invalid="false" value="true">
                                                    <div class="Polaris-TextField__Backdrop"></div>
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
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Google reCaptcha</div>
                    </div>
                    <div class="">
                        <div class="container">
                            <div>
                                <div class="">
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox25">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox25" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Enable</span>
                                        </label>
                                    </div>
                                    <div class="form-control">
                                        <div>
                                        <div class="paragraph ">
                                            <div><span>Please make sure that you have set Google reCaptcha v2 Site key and Secret key in <a href="/admin/settings?forceRedirect=true&amp;shop=dashboardmanage.myshopify.com" target="_blank">Settings</a></span></div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Error message</div>
                    </div>
                    <div class="">
                        <div class="container">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField62Label" for="PolarisTextField62" class="Polaris-Label__Text">
                                                    <div>Required</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField62" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField62Label" aria-invalid="false" value="Please fill in field">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField63Label" for="PolarisTextField63" class="Polaris-Label__Text">
                                                    <div>Invalid</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField63" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField63Label" aria-invalid="false" value="Invalid">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField64Label" for="PolarisTextField64" class="Polaris-Label__Text">
                                                    <div>Invalid name</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField64" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField64Label" aria-invalid="false" value="Invalid name">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField65Label" for="PolarisTextField65" class="Polaris-Label__Text">
                                                    <div>Invalid email</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField65" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField65Label" aria-invalid="false" value="Invalid email">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField66Label" for="PolarisTextField66" class="Polaris-Label__Text">
                                                    <div>Invalid url</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField66" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField66Label" aria-invalid="false" value="Invalid URL">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField67Label" for="PolarisTextField67" class="Polaris-Label__Text">
                                                    <div>Invalid phone</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField67" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField67Label" aria-invalid="false" value="Invalid phone">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField68Label" for="PolarisTextField68" class="Polaris-Label__Text">
                                                    <div>Invalid number</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField68" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField68Label" aria-invalid="false" value="Invalid number">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField69Label" for="PolarisTextField69" class="Polaris-Label__Text">
                                                    <div>Invalid password</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField69" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField69Label" aria-invalid="false" value="Invalid password">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField70Label" for="PolarisTextField70" class="Polaris-Label__Text">
                                                    <div>Confirmed password does't match</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField70" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField70Label" aria-invalid="false" value="Confirmed password doesn't match">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField71Label" for="PolarisTextField71" class="Polaris-Label__Text">
                                                    <div>Customer already exists</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField71" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField71Label" aria-invalid="false" value="Customer already exists">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField72Label" for="PolarisTextField72" class="Polaris-Label__Text">
                                                    <div>File size limit</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField72" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField72Label" aria-invalid="false" value="File size limit exceeded">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField73Label" for="PolarisTextField73" class="Polaris-Label__Text">
                                                    <div>File not allowed</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField73" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField73Label" aria-invalid="false" value="File extension not allowed">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField74Label" for="PolarisTextField74" class="Polaris-Label__Text">
                                                    <div>Required captcha</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField74" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField74Label" aria-invalid="false" value="Please, enter the captcha">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField75Label" for="PolarisTextField75" class="Polaris-Label__Text">
                                                    <div>Required product</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField75" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField75Label" aria-invalid="false" value="Please select product">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField76Label" for="PolarisTextField76" class="Polaris-Label__Text">
                                                    <div>Limit quantity</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField76" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField76Label" aria-invalid="false" value="The number of products left in stock has been exceeded">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField77Label" for="PolarisTextField77" class="Polaris-Label__Text">
                                                    <div>[Shopify] phone - Enter a valid phone number to use this delivery method</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField77" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField77Label" aria-invalid="false" value="phone - Enter a valid phone number to use this delivery method">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField78Label" for="PolarisTextField78" class="Polaris-Label__Text">
                                                    <div>[Shopify] phone - Phone has already been taken</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField78" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField78Label" aria-invalid="false" value="phone - Phone has already been taken">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField79Label" for="PolarisTextField79" class="Polaris-Label__Text">
                                                    <div>[Shopify] addresses.province - is not valid</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField79" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField79Label" aria-invalid="false" value="addresses.province - is not valid">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField80Label" for="PolarisTextField80" class="Polaris-Label__Text">
                                                    <div>Something went wrong, please try again</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField80" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField80Label" aria-invalid="false" value="Something went wrong, please try again">
                                                    <div class="Polaris-TextField__Backdrop"></div>
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
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">
                                    After submit
                        </div>
                    </div>
                    <div class="">
                        <div class="container">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect70Label" for="PolarisSelect70" class="Polaris-Label__Text">Action</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect70" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="clearForm">Clear form</option>
                                                <option value="redirectToPage">Redirect to page</option>
                                                <option value="hideForm">Hide form</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Clear form</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div>
                                        <div class="paragraph ">
                                            <div><span>You can use variables which will help you create a dynamic content.<br><br>Note: The customer variable is only available when the customer is logged in.</span></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="">
                                        <div class="">
                                            <div class="list">
                                                <div class="Polaris-Stack">
                                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><span class="Polaris-TextStyle--variationStrong">Customer name</span></div>
                                                    <div class="Polaris-Stack__Item">
                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent45" data-polaris-tooltip-activator="true">
                                                        <div><code class="Polaris-TextStyle--variationCode">{{customer.name}}</code></div>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Stack">
                                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><span class="Polaris-TextStyle--variationStrong">Customer email</span></div>
                                                    <div class="Polaris-Stack__Item">
                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent46" data-polaris-tooltip-activator="true">
                                                        <div><code class="Polaris-TextStyle--variationCode">{{customer.email}}</code></div>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Stack">
                                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><span class="Polaris-TextStyle--variationStrong">Page title</span></div>
                                                    <div class="Polaris-Stack__Item">
                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent47" data-polaris-tooltip-activator="true">
                                                        <div><code class="Polaris-TextStyle--variationCode">{{page.title}}</code></div>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Stack">
                                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><span class="Polaris-TextStyle--variationStrong">Page url</span></div>
                                                    <div class="Polaris-Stack__Item">
                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent48" data-polaris-tooltip-activator="true">
                                                        <div><code class="Polaris-TextStyle--variationCode">{{page.href}}</code></div>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="Polaris-Button Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Show more</span></span></button>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div>
                                        <div class="label">Message</div>
                                        <textarea id="tiny-react_10697013241690199329348" style="display: none;" aria-hidden="true"></textarea>
                                        <div role="application" class="tox tox-tinymce" aria-disabled="false" style="visibility: hidden; height: 425px;">
                                            <div class="tox-editor-container">
                                                <div data-alloy-vertical-dir="toptobottom" class="tox-editor-header">
                                                    <div role="menubar" data-alloy-tabstop="true" class="tox-menubar">
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" aria-expanded="false" style="user-select: none; width: 36.9531px;">
                                                        <span class="tox-mbtn__select-label">File</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 39.4688px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Edit</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 45.5312px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">View</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 50.5312px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Insert</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 59.8438px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Format</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 47.7188px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Tools</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 47.8281px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Table</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 44.8906px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Help</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    </div>
                                                    <div role="group" class="tox-toolbar-overlord" aria-disabled="false">
                                                    <div role="group" class="tox-toolbar__primary">
                                                        <div title="" role="toolbar" data-alloy-tabstop="true" tabindex="-1" class="tox-toolbar__group">
                                                            <button aria-label="Undo" title="Undo" type="button" tabindex="-1" class="tox-tbtn tox-tbtn--disabled" aria-disabled="true" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="M6.4 8H12c3.7 0 6.2 2 6.8 5.1.6 2.7-.4 5.6-2.3 6.8a1 1 0 0 1-1-1.8c1.1-.6 1.8-2.7 1.4-4.6-.5-2.1-2.1-3.5-4.9-3.5H6.4l3.3 3.3a1 1 0 1 1-1.4 1.4l-5-5a1 1 0 0 1 0-1.4l5-5a1 1 0 0 1 1.4 1.4L6.4 8Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                            <button aria-label="Redo" title="Redo" type="button" tabindex="-1" class="tox-tbtn tox-tbtn--disabled" aria-disabled="true" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="M17.6 10H12c-2.8 0-4.4 1.4-4.9 3.5-.4 2 .3 4 1.4 4.6a1 1 0 1 1-1 1.8c-2-1.2-2.9-4.1-2.3-6.8.6-3 3-5.1 6.8-5.1h5.6l-3.3-3.3a1 1 0 1 1 1.4-1.4l5 5a1 1 0 0 1 0 1.4l-5 5a1 1 0 0 1-1.4-1.4l3.3-3.3Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div title="" role="toolbar" data-alloy-tabstop="true" tabindex="-1" class="tox-toolbar__group">
                                                            <button title="Blocks" aria-label="Blocks" aria-haspopup="true" type="button" tabindex="-1" unselectable="on" class="tox-tbtn tox-tbtn--select tox-tbtn--bespoke" style="user-select: none; width: 130px;" aria-expanded="false">
                                                                <span class="tox-tbtn__select-label">Heading 4</span>
                                                                <div class="tox-tbtn__select-chevron">
                                                                <svg width="10" height="10" focusable="false">
                                                                    <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </div>
                                                            </button>
                                                        </div>
                                                        <div title="" role="toolbar" data-alloy-tabstop="true" tabindex="-1" class="tox-toolbar__group">
                                                            <button aria-label="Bold" title="Bold" type="button" tabindex="-1" class="tox-tbtn" aria-disabled="false" aria-pressed="false" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="M7.8 19c-.3 0-.5 0-.6-.2l-.2-.5V5.7c0-.2 0-.4.2-.5l.6-.2h5c1.5 0 2.7.3 3.5 1 .7.6 1.1 1.4 1.1 2.5a3 3 0 0 1-.6 1.9c-.4.6-1 1-1.6 1.2.4.1.9.3 1.3.6s.8.7 1 1.2c.4.4.5 1 .5 1.6 0 1.3-.4 2.3-1.3 3-.8.7-2.1 1-3.8 1H7.8Zm5-8.3c.6 0 1.2-.1 1.6-.5.4-.3.6-.7.6-1.3 0-1.1-.8-1.7-2.3-1.7H9.3v3.5h3.4Zm.5 6c.7 0 1.3-.1 1.7-.4.4-.4.6-.9.6-1.5s-.2-1-.7-1.4c-.4-.3-1-.4-2-.4H9.4v3.8h4Z" fill-rule="evenodd"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                            <button aria-label="Italic" title="Italic" type="button" tabindex="-1" class="tox-tbtn" aria-disabled="false" aria-pressed="false" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="m16.7 4.7-.1.9h-.3c-.6 0-1 0-1.4.3-.3.3-.4.6-.5 1.1l-2.1 9.8v.6c0 .5.4.8 1.4.8h.2l-.2.8H8l.2-.8h.2c1.1 0 1.8-.5 2-1.5l2-9.8.1-.5c0-.6-.4-.8-1.4-.8h-.3l.2-.9h5.8Z" fill-rule="evenodd"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                            <div aria-pressed="false" aria-label="Text color" title="Text color" role="button" aria-haspopup="true" tabindex="-1" unselectable="on" class="tox-split-button" style="user-select: none;" aria-disabled="false" aria-expanded="false" aria-describedby="aria_1725133685791690199329479">
                                                                <span role="presentation" class="tox-tbtn" aria-disabled="false" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                    <svg width="24" height="24" focusable="false">
                                                                        <g fill-rule="evenodd">
                                                                            <path id="tox-icon-text-color__color" d="M3 18h18v3H3z" fill="#000000"></path>
                                                                            <path d="M8.7 16h-.8a.5.5 0 0 1-.5-.6l2.7-9c.1-.3.3-.4.5-.4h2.8c.2 0 .4.1.5.4l2.7 9a.5.5 0 0 1-.5.6h-.8a.5.5 0 0 1-.4-.4l-.7-2.2c0-.3-.3-.4-.5-.4h-3.4c-.2 0-.4.1-.5.4l-.7 2.2c0 .3-.2.4-.4.4Zm2.6-7.6-.6 2a.5.5 0 0 0 .5.6h1.6a.5.5 0 0 0 .5-.6l-.6-2c0-.3-.3-.4-.5-.4h-.4c-.2 0-.4.1-.5.4Z"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                </span>
                                                                <span role="presentation" class="tox-tbtn tox-split-button__chevron" aria-disabled="false">
                                                                <svg width="10" height="10" focusable="false">
                                                                    <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </span>
                                                                <span aria-hidden="true" style="display: none;" id="aria_1725133685791690199329479">To open the popup, press Shift+Enter</span>
                                                            </div>
                                                        </div>
                                                        <div role="toolbar" data-alloy-tabstop="true" tabindex="-1" class="tox-toolbar__group">
                                                            <button aria-label="More..." title="More..." aria-haspopup="true" type="button" tabindex="-1" data-alloy-tabstop="true" class="tox-tbtn" aria-expanded="false">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="M6 10a2 2 0 0 0-2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2 2 2 0 0 0-2-2Zm12 0a2 2 0 0 0-2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2 2 2 0 0 0-2-2Zm-6 0a2 2 0 0 0-2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2 2 2 0 0 0-2-2Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="tox-anchorbar"></div>
                                                </div>
                                                <div class="tox-sidebar-wrap">
                                                    <div class="tox-edit-area"><iframe id="tiny-react_10697013241690199329348_ifr" frameborder="0" allowtransparency="true" title="Rich Text Area" class="tox-edit-area__iframe" srcdoc="<!DOCTYPE html><html><head><meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=UTF-8&quot; /></head><body id=&quot;tinymce&quot; class=&quot;mce-content-body &quot; data-id=&quot;tiny-react_10697013241690199329348&quot; aria-label=&quot;Rich Text Area. Press ALT-0 for help.&quot;><br></body></html>"></iframe></div>
                                                    <div role="presentation" class="tox-sidebar">
                                                    <div data-alloy-tabstop="true" tabindex="-1" class="tox-sidebar__slider tox-sidebar--sliding-closed" style="width: 0px;">
                                                        <div class="tox-sidebar__pane-container"></div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="tox-statusbar">
                                                    <div class="tox-statusbar__text-container">
                                                    <div role="navigation" data-alloy-tabstop="true" class="tox-statusbar__path" aria-disabled="false">
                                                        <div data-index="0" aria-level="1" role="button" tabindex="-1" class="tox-statusbar__path-item" aria-disabled="false">h4</div>
                                                    </div>
                                                    <button type="button" tabindex="-1" data-alloy-tabstop="true" class="tox-statusbar__wordcount">26 words</button>
                                                    <span class="tox-statusbar__branding">
                                                        <a href="https://www.tiny.cloud/powered-by-tiny?utm_campaign=editor_referral&amp;utm_medium=poweredby&amp;utm_source=tinymce&amp;utm_content=v6" rel="noopener" target="_blank" aria-label="Powered by Tiny" tabindex="-1">
                                                            <svg width="50px" height="16px" viewBox="0 0 50 16" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.143 0c2.608.015 5.186 2.178 5.186 5.331 0 0 .077 3.812-.084 4.87-.361 2.41-2.164 4.074-4.65 4.496-1.453.284-2.523.49-3.212.623-.373.071-.634.122-.785.152-.184.038-.997.145-1.35.145-2.732 0-5.21-2.04-5.248-5.33 0 0 0-3.514.03-4.442.093-2.4 1.758-4.342 4.926-4.963 0 0 3.875-.752 4.036-.782.368-.07.775-.1 1.15-.1Zm1.826 2.8L5.83 3.989v2.393l-2.455.475v5.968l6.137-1.189V9.243l2.456-.476V2.8ZM5.83 6.382l3.682-.713v3.574l-3.682.713V6.382Zm27.173-1.64-.084-1.066h-2.226v9.132h2.456V7.743c-.008-1.151.998-2.064 2.149-2.072 1.15-.008 1.987.92 1.995 2.072v5.065h2.455V7.359c-.015-2.18-1.657-3.929-3.837-3.913a3.993 3.993 0 0 0-2.908 1.296Zm-6.3-4.266L29.16 0v2.387l-2.456.475V.476Zm0 3.2v9.132h2.456V3.676h-2.456Zm18.179 11.787L49.11 3.676H46.58l-1.612 4.527-.46 1.382-.384-1.382-1.611-4.527H39.98l3.3 9.132L42.15 16l2.732-.537ZM22.867 9.738c0 .752.568 1.075.921 1.075.353 0 .668-.047.998-.154l.537 1.765c-.23.154-.92.537-2.225.537-1.305 0-2.655-.997-2.686-2.686a136.877 136.877 0 0 1 0-4.374H18.8V3.676h1.612v-1.98l2.455-.476v2.456h2.302V5.9h-2.302v3.837Z"></path>
                                                            </svg>
                                                        </a>
                                                    </span>
                                                    </div>
                                                    <div title="Resize" data-alloy-tabstop="true" tabindex="-1" class="tox-statusbar__resize-handle">
                                                    <svg width="10" height="10" focusable="false">
                                                        <g fill-rule="nonzero">
                                                            <path d="M8.1 1.1A.5.5 0 1 1 9 2l-7 7A.5.5 0 1 1 1 8l7-7ZM8.1 5.1A.5.5 0 1 1 9 6l-3 3A.5.5 0 1 1 5 8l3-3Z"></path>
                                                        </g>
                                                    </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            <div aria-hidden="true" class="tox-view-wrap" style="display: none;">
                                                <div class="tox-view-wrap__slot-container"></div>
                                            </div>
                                            <div aria-hidden="true" class="tox-throbber" style="display: none;"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField81Label" for="PolarisTextField81" class="Polaris-Label__Text">
                                                    <div>Redirect Url</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField81" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField81Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox26">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox26" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Enable Google Analytics</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField82Label" for="PolarisTextField82" class="Polaris-Label__Text">
                                                    <div>Google Analytics Event Name</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField82" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField82Label" aria-invalid="false" value="globo_form_submit">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField83Label" for="PolarisTextField83" class="Polaris-Label__Text">
                                                    <div>Event Category</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField83" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField83Label" aria-invalid="false" value="Form Builder by Globo">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField84Label" for="PolarisTextField84" class="Polaris-Label__Text">
                                                    <div>Event Action</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField84" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField84Label" aria-invalid="false" value="Submit">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField85Label" for="PolarisTextField85" class="Polaris-Label__Text">
                                                    <div>Event Label</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField85" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField85Label" aria-invalid="false" >
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox27">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox27" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Enable Facebook Pixel</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField86Label" for="PolarisTextField86" class="Polaris-Label__Text">
                                                    <div>Custom event title</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField86" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField86Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
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
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Integration</div>
                    </div>
                    <div class="">
                        <div class="container">
                            <div>
                                <div class="">
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="m1.791 2.253-.597 3.583a1 1 0 0 0 .986 1.164h.893a1.5 1.5 0 0 0 1.342-.83l.585-1.17.585 1.17a1.5 1.5 0 0 0 1.342.83h1.146a1.5 1.5 0 0 0 1.342-.83l.585-1.17.585 1.17a1.5 1.5 0 0 0 1.342.83h1.146a1.5 1.5 0 0 0 1.342-.83l.585-1.17.585 1.17a1.5 1.5 0 0 0 1.342.83h.893a1 1 0 0 0 .986-1.164l-.597-3.583a1.5 1.5 0 0 0-1.48-1.253h-13.458a1.5 1.5 0 0 0-1.48 1.253zm2.209 16.247a1.5 1.5 0 0 1 1.5-1.5h2.5v-3h4v3h2.5a1.5 1.5 0 0 1 1.5 1.5v.5h-12v-.5z"></path>
                                                        <path d="M2 9h2v4h12v-4h2v4.5a1.5 1.5 0 0 1-1.5 1.5h-13a1.5 1.5 0 0 1-1.5-1.5v-4.5z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>
                                                        <div class="badge-upgrade inlineButton">
                                                            Shopify
                                                            <div>
                                                                <span tabindex="0" aria-describedby="PolarisTooltipContent49" data-polaris-tooltip-activator="true">
                                                                    <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                        <path d="M5.2 18a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z"></path>
                                                                    </svg>
                                                                    </span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>Mailchimp</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>Klaviyo</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>
                                                            <div class="badge-upgrade inlineButton">
                                                                Zapier
                                                                <div>
                                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent50" data-polaris-tooltip-activator="true">
                                                                        <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M5.2 18a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z"></path>
                                                                        </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>
                                                            <div class="badge-upgrade inlineButton">
                                                                Hubspot
                                                                <div>
                                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent51" data-polaris-tooltip-activator="true">
                                                                        <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M5.2 18a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z"></path>
                                                                        </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>Omnisend</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>
                                                            <div class="badge-upgrade inlineButton">
                                                                GetResponse
                                                                <div>
                                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent52" data-polaris-tooltip-activator="true">
                                                                        <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M5.2 18a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z"></path>
                                                                        </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>
                                                            <div class="badge-upgrade inlineButton">
                                                                Sendinblue
                                                                <div>
                                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent53" data-polaris-tooltip-activator="true">
                                                                        <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M5.2 18a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z"></path>
                                                                        </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>
                                                            <div class="badge-upgrade inlineButton">
                                                                Campaign Monitor
                                                                <div>
                                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent54" data-polaris-tooltip-activator="true">
                                                                        <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M5.2 18a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z"></path>
                                                                        </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>
                                                            <div class="badge-upgrade inlineButton">
                                                                ActiveCampaign
                                                                <div>
                                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent55" data-polaris-tooltip-activator="true">
                                                                        <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M5.2 18a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z"></path>
                                                                        </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M17.5 2h-2.5v-1a1 1 0 1 0-2 0v1h-7v-1a1 1 0 0 0-2 0v1h-1.5c-.8 0-1.5.7-1.5 1.5v15c0 .8.7 1.5 1.5 1.5h15c.8 0 1.5-.7 1.5-1.5v-15c0-.8-.7-1.5-1.5-1.5zm-14.5 16h14v-10h-14v10z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>
                                                            <div class="badge-upgrade inlineButton">
                                                                Google Calendar
                                                                <div>
                                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent56" data-polaris-tooltip-activator="true">
                                                                        <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M5.2 18a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z"></path>
                                                                        </svg>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="builder-item-wrapper ">
                                        <div class="list-item">
                                            <div class="row">
                                                <div class="icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M10 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.707 4.293a1 1 0 0 0 0 1.414l2.293 2.293-2.293 2.293a1 1 0 1 0 1.414 1.414l3-3a1 1 0 0 0 0-1.414l-3-3a1 1 0 0 0-1.414 0z"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="title">
                                                    <div>
                                                        <div>
                                                            <div class="badge-upgrade inlineButton">
                                                                Google Sheet
                                                                <div>
                                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent57" data-polaris-tooltip-activator="true">
                                                                        <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M5.2 18a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z"></path>
                                                                        </svg>
                                                                        </span>
                                                                    </span>
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
                <div class="polarisformcontrol">
                    <div class="header backheader">

                        <button class="ui-btn back-icon">
                            <span class="Polaris-Icon backBtn" data-id='0'>
                                <span class="Polaris-VisuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path
                                        d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                    </path>
                                </svg>
                            </span>
                        </button>
                        <div class="title">Admin</div>
                    </div>
                    <div class="">
                        <div class="container">
                            <div>
                                <div class="">
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField133Label" for="PolarisTextField133" class="Polaris-Label__Text">
                                                    <div>Email</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField133" placeholder="" class="Polaris-TextField__Input" type="text" aria-describedby="PolarisTextField133HelpText" aria-labelledby="PolarisTextField133Label" aria-invalid="false" value="codelock2021@gmail.com">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Labelled__HelpText" id="PolarisTextField133HelpText"><span>You can put multiple email addresses separated with a comma</span></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div>
                                        <label class="Polaris-Choice" for="PolarisCheckbox46">
                                            <span class="Polaris-Choice__Control">
                                                <span class="Polaris-Checkbox">
                                                    <input id="PolarisCheckbox46" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" aria-describedby="PolarisCheckbox46HelpText" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                        </svg>
                                                    </span>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">Also send to dynamic email</span>
                                        </label>
                                        <div class="Polaris-Choice__Descriptions">
                                            <div class="Polaris-Choice__HelpText" id="PolarisCheckbox46HelpText"><span>Set up admin email based on selected option on the form <a target="_blank" href="https://globosoftware.net/kb/receive-admin-email-at-different-email-address-based-on-selected-fields/">Learn more</a></span></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect80Label" for="PolarisSelect80" class="Polaris-Label__Text">Select email's element</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect80" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="text" type="text">Your Name</option>
                                                <option value="textarea" type="textarea">Message</option>
                                                <option value="text-2" type="text">Text</option>
                                                <option value="textarea-2" type="textarea">Textarea</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Your Name</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div>
                                        <div class="paragraph ">
                                            <div><span>You can use variables which will help you create a dynamic content.<br><br>Note: The customer variable is only available when the customer is logged in.</span></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="">
                                        <div class="">
                                            <div class="list">
                                                <div class="Polaris-Stack">
                                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><span class="Polaris-TextStyle--variationStrong">All visible input data</span></div>
                                                    <div class="Polaris-Stack__Item">
                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent78" data-polaris-tooltip-activator="true">
                                                        <div><code class="Polaris-TextStyle--variationCode">{{data}}</code></div>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Stack">
                                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><span class="Polaris-TextStyle--variationStrong">Customer name</span></div>
                                                    <div class="Polaris-Stack__Item">
                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent79" data-polaris-tooltip-activator="true">
                                                        <div><code class="Polaris-TextStyle--variationCode">{{customer.name}}</code></div>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Stack">
                                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><span class="Polaris-TextStyle--variationStrong">Customer email</span></div>
                                                    <div class="Polaris-Stack__Item">
                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent80" data-polaris-tooltip-activator="true">
                                                        <div><code class="Polaris-TextStyle--variationCode">{{customer.email}}</code></div>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Stack">
                                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><span class="Polaris-TextStyle--variationStrong">Page title</span></div>
                                                    <div class="Polaris-Stack__Item">
                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent81" data-polaris-tooltip-activator="true">
                                                        <div><code class="Polaris-TextStyle--variationCode">{{page.title}}</code></div>
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Stack">
                                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><span class="Polaris-TextStyle--variationStrong">Page url</span></div>
                                                    <div class="Polaris-Stack__Item">
                                                    <span tabindex="0" aria-describedby="PolarisTooltipContent82" data-polaris-tooltip-activator="true">
                                                        <div><code class="Polaris-TextStyle--variationCode">{{page.href}}</code></div>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="Polaris-Button Polaris-Button--plain Polaris-Button--fullWidth" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Show more</span></span></button>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField134Label" for="PolarisTextField134" class="Polaris-Label__Text">
                                                    <div>Subject</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField134" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField134Label" aria-invalid="false" value="You have new submission">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div>
                                        <div class="label">Content</div>
                                        <textarea id="tiny-react_16891693471690201830143" style="display: none;" aria-hidden="true"></textarea>
                                        <div role="application" class="tox tox-tinymce" aria-disabled="false" style="visibility: hidden; height: 425px;">
                                            <div class="tox-editor-container">
                                                <div data-alloy-vertical-dir="toptobottom" class="tox-editor-header">
                                                    <div role="menubar" data-alloy-tabstop="true" class="tox-menubar">
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" aria-expanded="false" style="user-select: none; width: 36.9531px;">
                                                        <span class="tox-mbtn__select-label">File</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 39.4688px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Edit</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 45.5312px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">View</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 50.5312px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Insert</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 59.8438px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Format</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 47.7188px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Tools</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 47.8281px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Table</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    <button aria-haspopup="true" role="menuitem" type="button" tabindex="-1" data-alloy-tabstop="true" unselectable="on" class="tox-mbtn tox-mbtn--select" style="user-select: none; width: 44.8906px;" aria-expanded="false">
                                                        <span class="tox-mbtn__select-label">Help</span>
                                                        <div class="tox-mbtn__select-chevron">
                                                            <svg width="10" height="10" focusable="false">
                                                                <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                            </svg>
                                                        </div>
                                                    </button>
                                                    </div>
                                                    <div role="group" class="tox-toolbar-overlord" aria-disabled="false">
                                                    <div role="group" class="tox-toolbar__primary">
                                                        <div title="" role="toolbar" data-alloy-tabstop="true" tabindex="-1" class="tox-toolbar__group">
                                                            <button aria-label="Undo" title="Undo" type="button" tabindex="-1" class="tox-tbtn tox-tbtn--disabled" aria-disabled="true" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="M6.4 8H12c3.7 0 6.2 2 6.8 5.1.6 2.7-.4 5.6-2.3 6.8a1 1 0 0 1-1-1.8c1.1-.6 1.8-2.7 1.4-4.6-.5-2.1-2.1-3.5-4.9-3.5H6.4l3.3 3.3a1 1 0 1 1-1.4 1.4l-5-5a1 1 0 0 1 0-1.4l5-5a1 1 0 0 1 1.4 1.4L6.4 8Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                            <button aria-label="Redo" title="Redo" type="button" tabindex="-1" class="tox-tbtn tox-tbtn--disabled" aria-disabled="true" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="M17.6 10H12c-2.8 0-4.4 1.4-4.9 3.5-.4 2 .3 4 1.4 4.6a1 1 0 1 1-1 1.8c-2-1.2-2.9-4.1-2.3-6.8.6-3 3-5.1 6.8-5.1h5.6l-3.3-3.3a1 1 0 1 1 1.4-1.4l5 5a1 1 0 0 1 0 1.4l-5 5a1 1 0 0 1-1.4-1.4l3.3-3.3Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div title="" role="toolbar" data-alloy-tabstop="true" tabindex="-1" class="tox-toolbar__group">
                                                            <button title="Blocks" aria-label="Blocks" aria-haspopup="true" type="button" tabindex="-1" unselectable="on" class="tox-tbtn tox-tbtn--select tox-tbtn--bespoke" style="user-select: none; width: 130px;" aria-expanded="false">
                                                                <span class="tox-tbtn__select-label">Paragraph</span>
                                                                <div class="tox-tbtn__select-chevron">
                                                                <svg width="10" height="10" focusable="false">
                                                                    <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </div>
                                                            </button>
                                                        </div>
                                                        <div title="" role="toolbar" data-alloy-tabstop="true" tabindex="-1" class="tox-toolbar__group">
                                                            <button aria-label="Bold" title="Bold" type="button" tabindex="-1" class="tox-tbtn" aria-disabled="false" aria-pressed="false" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="M7.8 19c-.3 0-.5 0-.6-.2l-.2-.5V5.7c0-.2 0-.4.2-.5l.6-.2h5c1.5 0 2.7.3 3.5 1 .7.6 1.1 1.4 1.1 2.5a3 3 0 0 1-.6 1.9c-.4.6-1 1-1.6 1.2.4.1.9.3 1.3.6s.8.7 1 1.2c.4.4.5 1 .5 1.6 0 1.3-.4 2.3-1.3 3-.8.7-2.1 1-3.8 1H7.8Zm5-8.3c.6 0 1.2-.1 1.6-.5.4-.3.6-.7.6-1.3 0-1.1-.8-1.7-2.3-1.7H9.3v3.5h3.4Zm.5 6c.7 0 1.3-.1 1.7-.4.4-.4.6-.9.6-1.5s-.2-1-.7-1.4c-.4-.3-1-.4-2-.4H9.4v3.8h4Z" fill-rule="evenodd"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                            <button aria-label="Italic" title="Italic" type="button" tabindex="-1" class="tox-tbtn" aria-disabled="false" aria-pressed="false" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="m16.7 4.7-.1.9h-.3c-.6 0-1 0-1.4.3-.3.3-.4.6-.5 1.1l-2.1 9.8v.6c0 .5.4.8 1.4.8h.2l-.2.8H8l.2-.8h.2c1.1 0 1.8-.5 2-1.5l2-9.8.1-.5c0-.6-.4-.8-1.4-.8h-.3l.2-.9h5.8Z" fill-rule="evenodd"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                            <div aria-pressed="false" aria-label="Text color" title="Text color" role="button" aria-haspopup="true" tabindex="-1" unselectable="on" class="tox-split-button" style="user-select: none;" aria-disabled="false" aria-expanded="false" aria-describedby="aria_21507833133491690201830213">
                                                                <span role="presentation" class="tox-tbtn" aria-disabled="false" style="width: 34px;">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                    <svg width="24" height="24" focusable="false">
                                                                        <g fill-rule="evenodd">
                                                                            <path id="tox-icon-text-color__color" d="M3 18h18v3H3z" fill="#000000"></path>
                                                                            <path d="M8.7 16h-.8a.5.5 0 0 1-.5-.6l2.7-9c.1-.3.3-.4.5-.4h2.8c.2 0 .4.1.5.4l2.7 9a.5.5 0 0 1-.5.6h-.8a.5.5 0 0 1-.4-.4l-.7-2.2c0-.3-.3-.4-.5-.4h-3.4c-.2 0-.4.1-.5.4l-.7 2.2c0 .3-.2.4-.4.4Zm2.6-7.6-.6 2a.5.5 0 0 0 .5.6h1.6a.5.5 0 0 0 .5-.6l-.6-2c0-.3-.3-.4-.5-.4h-.4c-.2 0-.4.1-.5.4Z"></path>
                                                                        </g>
                                                                    </svg>
                                                                </span>
                                                                </span>
                                                                <span role="presentation" class="tox-tbtn tox-split-button__chevron" aria-disabled="false">
                                                                <svg width="10" height="10" focusable="false">
                                                                    <path d="M8.7 2.2c.3-.3.8-.3 1 0 .4.4.4.9 0 1.2L5.7 7.8c-.3.3-.9.3-1.2 0L.2 3.4a.8.8 0 0 1 0-1.2c.3-.3.8-.3 1.1 0L5 6l3.7-3.8Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </span>
                                                                <span aria-hidden="true" style="display: none;" id="aria_21507833133491690201830213">To open the popup, press Shift+Enter</span>
                                                            </div>
                                                        </div>
                                                        <div role="toolbar" data-alloy-tabstop="true" tabindex="-1" class="tox-toolbar__group">
                                                            <button aria-label="More..." title="More..." aria-haspopup="true" type="button" tabindex="-1" data-alloy-tabstop="true" class="tox-tbtn" aria-expanded="false">
                                                                <span class="tox-icon tox-tbtn__icon-wrap">
                                                                <svg width="24" height="24" focusable="false">
                                                                    <path d="M6 10a2 2 0 0 0-2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2 2 2 0 0 0-2-2Zm12 0a2 2 0 0 0-2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2 2 2 0 0 0-2-2Zm-6 0a2 2 0 0 0-2 2c0 1.1.9 2 2 2a2 2 0 0 0 2-2 2 2 0 0 0-2-2Z" fill-rule="nonzero"></path>
                                                                </svg>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="tox-anchorbar"></div>
                                                </div>
                                                <div class="tox-sidebar-wrap">
                                                    <div class="tox-edit-area"><iframe id="tiny-react_16891693471690201830143_ifr" frameborder="0" allowtransparency="true" title="Rich Text Area" class="tox-edit-area__iframe" srcdoc="<!DOCTYPE html><html><head><meta http-equiv=&quot;Content-Type&quot; content=&quot;text/html; charset=UTF-8&quot; /></head><body id=&quot;tinymce&quot; class=&quot;mce-content-body &quot; data-id=&quot;tiny-react_16891693471690201830143&quot; aria-label=&quot;Rich Text Area. Press ALT-0 for help.&quot;><br></body></html>"></iframe></div>
                                                    <div role="presentation" class="tox-sidebar">
                                                    <div data-alloy-tabstop="true" tabindex="-1" class="tox-sidebar__slider tox-sidebar--sliding-closed" style="width: 0px;">
                                                        <div class="tox-sidebar__pane-container"></div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="tox-statusbar">
                                                    <div class="tox-statusbar__text-container">
                                                    <div role="navigation" data-alloy-tabstop="true" class="tox-statusbar__path" aria-disabled="false">
                                                        <div data-index="0" aria-level="1" role="button" tabindex="-1" class="tox-statusbar__path-item" aria-disabled="false">table</div>
                                                        <div aria-hidden="true" class="tox-statusbar__path-divider"> › </div>
                                                        <div data-index="1" aria-level="2" role="button" tabindex="-1" class="tox-statusbar__path-item" aria-disabled="false">tbody</div>
                                                        <div aria-hidden="true" class="tox-statusbar__path-divider"> › </div>
                                                        <div data-index="2" aria-level="3" role="button" tabindex="-1" class="tox-statusbar__path-item" aria-disabled="false">tr</div>
                                                        <div aria-hidden="true" class="tox-statusbar__path-divider"> › </div>
                                                        <div data-index="3" aria-level="4" role="button" tabindex="-1" class="tox-statusbar__path-item" aria-disabled="false">td</div>
                                                    </div>
                                                    <button type="button" tabindex="-1" data-alloy-tabstop="true" class="tox-statusbar__wordcount">0 words</button>
                                                    <span class="tox-statusbar__branding">
                                                        <a href="https://www.tiny.cloud/powered-by-tiny?utm_campaign=editor_referral&amp;utm_medium=poweredby&amp;utm_source=tinymce&amp;utm_content=v6" rel="noopener" target="_blank" aria-label="Powered by Tiny" tabindex="-1">
                                                            <svg width="50px" height="16px" viewBox="0 0 50 16" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.143 0c2.608.015 5.186 2.178 5.186 5.331 0 0 .077 3.812-.084 4.87-.361 2.41-2.164 4.074-4.65 4.496-1.453.284-2.523.49-3.212.623-.373.071-.634.122-.785.152-.184.038-.997.145-1.35.145-2.732 0-5.21-2.04-5.248-5.33 0 0 0-3.514.03-4.442.093-2.4 1.758-4.342 4.926-4.963 0 0 3.875-.752 4.036-.782.368-.07.775-.1 1.15-.1Zm1.826 2.8L5.83 3.989v2.393l-2.455.475v5.968l6.137-1.189V9.243l2.456-.476V2.8ZM5.83 6.382l3.682-.713v3.574l-3.682.713V6.382Zm27.173-1.64-.084-1.066h-2.226v9.132h2.456V7.743c-.008-1.151.998-2.064 2.149-2.072 1.15-.008 1.987.92 1.995 2.072v5.065h2.455V7.359c-.015-2.18-1.657-3.929-3.837-3.913a3.993 3.993 0 0 0-2.908 1.296Zm-6.3-4.266L29.16 0v2.387l-2.456.475V.476Zm0 3.2v9.132h2.456V3.676h-2.456Zm18.179 11.787L49.11 3.676H46.58l-1.612 4.527-.46 1.382-.384-1.382-1.611-4.527H39.98l3.3 9.132L42.15 16l2.732-.537ZM22.867 9.738c0 .752.568 1.075.921 1.075.353 0 .668-.047.998-.154l.537 1.765c-.23.154-.92.537-2.225.537-1.305 0-2.655-.997-2.686-2.686a136.877 136.877 0 0 1 0-4.374H18.8V3.676h1.612v-1.98l2.455-.476v2.456h2.302V5.9h-2.302v3.837Z"></path>
                                                            </svg>
                                                        </a>
                                                    </span>
                                                    </div>
                                                    <div title="Resize" data-alloy-tabstop="true" tabindex="-1" class="tox-statusbar__resize-handle">
                                                    <svg width="10" height="10" focusable="false">
                                                        <g fill-rule="nonzero">
                                                            <path d="M8.1 1.1A.5.5 0 1 1 9 2l-7 7A.5.5 0 1 1 1 8l7-7ZM8.1 5.1A.5.5 0 1 1 9 6l-3 3A.5.5 0 1 1 5 8l3-3Z"></path>
                                                        </g>
                                                    </svg>
                                                    </div>
                                                </div>
                                            </div>
                                            <div aria-hidden="true" class="tox-view-wrap" style="display: none;">
                                                <div class="tox-view-wrap__slot-container"></div>
                                            </div>
                                            <div aria-hidden="true" class="tox-throbber" style="display: none;"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <label class="Polaris-Choice" for="PolarisCheckbox47">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox47" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Limit content width</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField135Label" for="PolarisTextField135" class="Polaris-Label__Text">Max width</label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <div class="Polaris-TextField__Prefix" id="PolarisTextField135-Prefix">px</div>
                                                    <input id="PolarisTextField135" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField135Label PolarisTextField135-Prefix" aria-invalid="false" value="600">
                                                    <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                    <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon">
                                                            <span class="Polaris-Icon">
                                                                <span class="Polaris-VisuallyHidden"></span>
                                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                <path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z"></path>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div role="button" class="Polaris-TextField__Segment" tabindex="-1">
                                                        <div class="Polaris-TextField__SpinnerIcon">
                                                            <span class="Polaris-Icon">
                                                                <span class="Polaris-VisuallyHidden"></span>
                                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                <path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path>
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect81Label" for="PolarisSelect81" class="Polaris-Label__Text">Use SMTP &amp; API</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect81" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="false">Choose option</option>
                                                <option value="useSMTP">Use SMTP &amp; API from Settings</option>
                                                <option value="true">Use custom SMTP</option>
                                                <option value="useCustomAPI">Use custom API</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Choose option</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                        <div style="margin-top: 5px;"></div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField136Label" for="PolarisTextField136" class="Polaris-Label__Text">
                                                    <div>SMTP</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField136" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField136Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect82Label" for="PolarisSelect82" class="Polaris-Label__Text">Port</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect82" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="465">465</option>
                                                <option value="587">587</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">465</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect83Label" for="PolarisSelect83" class="Polaris-Label__Text">Mail encryption</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect83" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="ssl">SSL</option>
                                                <option value="tls">TLS</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">SSL</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField137Label" for="PolarisTextField137" class="Polaris-Label__Text">
                                                    <div>Username / Email address</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField137" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField137Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField138Label" for="PolarisTextField138" class="Polaris-Label__Text">Password / App password</label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input id="PolarisTextField138" class="Polaris-TextField__Input" type="password" aria-labelledby="PolarisTextField138Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect84Label" for="PolarisSelect84" class="Polaris-Label__Text">Choose provider</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="PolarisSelect84" class="Polaris-Select__Input" aria-invalid="false">
                                                <option value="false">Choose provider</option>
                                                <option value="sendinblue">Sendinblue</option>
                                                <option value="sendgrid">Sendgrid</option>
                                                <option value="pepipost">Pepipost</option>
                                                <option value="amazonses">Amazon SES</option>
                                                <option value="elasticemail">Elastic Email</option>
                                                <option value="mailgun">Mailgun</option>
                                                <option value="zoho">Zoho</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true" aria-disabled="false">
                                                <span class="Polaris-Select__SelectedOption">Choose provider</span>
                                                <span class="Polaris-Select__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="Polaris-Select__Backdrop"></div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField139Label" for="PolarisTextField139" class="Polaris-Label__Text">API key</label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input id="PolarisTextField139" class="Polaris-TextField__Input" type="password" aria-labelledby="PolarisTextField139Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField140Label" for="PolarisTextField140" class="Polaris-Label__Text">
                                                    <div>Key ID</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField140" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField140Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisTextField141Label" for="PolarisTextField141" class="Polaris-Label__Text">Secret key</label></div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                <div class="Polaris-TextField">
                                                    <input id="PolarisTextField141" class="Polaris-TextField__Input" type="password" aria-labelledby="PolarisTextField141Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField142Label" for="PolarisTextField142" class="Polaris-Label__Text">
                                                    <div>Region</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField142" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField142Label" aria-invalid="false" value="us-east-2">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox48">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox48" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Send mail from another email</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField143Label" for="PolarisTextField143" class="Polaris-Label__Text">
                                                    <div>From email</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField143" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField143Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox49">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox49" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Custom from name</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField144Label" for="PolarisTextField144" class="Polaris-Label__Text">
                                                    <div>From name</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField144" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField144Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice" for="PolarisCheckbox50">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                                <input id="PolarisCheckbox50" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""><span class="Polaris-Checkbox__Backdrop"></span>
                                                <span class="Polaris-Checkbox__Icon">
                                                    <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                        <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z"></path>
                                                    </svg>
                                                    </span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">Reply to another email</span>
                                        </label>
                                    </div>
                                    <div class="form-control hidden">
                                        <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label id="PolarisTextField145Label" for="PolarisTextField145" class="Polaris-Label__Text">
                                                    <div>Reply to</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField145" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField145Label" aria-invalid="false" value="">
                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden">
                                        <div><button class="Polaris-Button Polaris-Button--primary" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Send a test email</span></span></button></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
    $(document).ready(function() {
        get_selected_elements(<?php echo $form_id; ?>);
        // getFormTitle(<?php echo $form_id; ?>);
    });

</script>