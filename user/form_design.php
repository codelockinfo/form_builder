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
                        <div class="Polaris-Labelled--hidden">
                            <div class="Polaris-Connected">
                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary disp_flex_input">
                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                        <input id="PolarisTextField1" placeholder=""
                                            class="Polaris-TextField__Input form_name_form_design" name="form_name_form_design" type="text"
                                            aria-labelledby="PolarisTextField1Label" aria-invalid="false"
                                            value="">
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                    <button class="Polaris-Button Polaris-Button--primary btnFormSubmit save_loader_show" aria-disabled="false"
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
                    <div class="item viewport">
                        <ul class="view_icon">
                            <li class="mobile ">
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
                            <li class="desktop active">
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
                                    <button class="Polaris-Button Polaris-Button--primary" aria-disabled="false"
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
                    <button type="button" class="Polaris-Link">
                        <span class="Polaris-Icon">
                            <span class="Polaris-VisuallyHidden"></span>
                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                <path
                                    d="M14 20.001a.994.994 0 0 1-.747-.336l-8-9a.999.999 0 0 1 0-1.328l8-9a1 1 0 0 1 1.494 1.328l-7.41 8.336 7.41 8.336a.998.998 0 0 1-.747 1.664z">
                                </path>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="title">
                    <button type="button" class="Polaris-Link">
                        <span>Back to list</span>
                    </button>
                </div>
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
                <div class="preview-box iframe-wrapper desktop"></div>
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
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Elements</h3>
                                        <div>
                                            <div class="selected_element_set">
                                                <!-- <div class="builder-item-wrapper ">
                                                    <div class="list-item" data-owl="3">
                                                        <div class="row">
                                                            <div class="icon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="m8.24 9 .816 2.33a1 1 0 0 0 1.888-.66l-3.335-9.528a1.705 1.705 0 0 0-3.218 0l-3.335 9.528a1 1 0 0 0 1.888.66l.815-2.33h4.482zm-.7-2-1.54-4.401-1.54 4.401h3.08zm7.96-2c.608 0 1.18.155 1.68.428a.999.999 0 0 1 1.82.572v5a1 1 0 0 1-1.82.572 3.5 3.5 0 1 1-1.68-6.572zm0 5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z">
                                                                        </path>
                                                                        <path
                                                                            d="M2 14a1 1 0 1 0 0 2h16a1 1 0 1 0 0-2h-16zm0 4a1 1 0 1 0 0 2h12a1 1 0 1 0 0-2h-12z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Your Name</div>
                                                                </div>
                                                            </div>
                                                            <div title="Duplicate this element" class="duplicate">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M7.5 2a1.5 1.5 0 0 0-1.5 1.5v9.5a1 1 0 0 0 1 1h9.5a1.5 1.5 0 0 0 1.5-1.5v-9a1.5 1.5 0 0 0-1.5-1.5h-9zm-4 4h.5v10h10v.5a1.5 1.5 0 0 1-1.5 1.5h-9a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <div title="Sort this element" class="softable">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M7 2a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm6-8a2 2 0 1 0-.001-4.001 2 2 0 0 0 .001 4.001zm0 2a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item" data-owl="4">
                                                        <div class="row">
                                                            <div class="icon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
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
                                                                <div>
                                                                    <div>Email</div>
                                                                </div>
                                                            </div>
                                                            <div title="Duplicate this element" class="duplicate">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M7.5 2a1.5 1.5 0 0 0-1.5 1.5v9.5a1 1 0 0 0 1 1h9.5a1.5 1.5 0 0 0 1.5-1.5v-9a1.5 1.5 0 0 0-1.5-1.5h-9zm-4 4h.5v10h10v.5a1.5 1.5 0 0 1-1.5 1.5h-9a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <div title="Sort this element" class="softable">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M7 2a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm6-8a2 2 0 1 0-.001-4.001 2 2 0 0 0 .001 4.001zm0 2a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item" data-owl="5">
                                                        <div class="row">
                                                            <div class="icon">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="M2.5 1a1.5 1.5 0 0 0-1.5 1.5v15a1.5 1.5 0 0 0 1.5 1.5h15a1.5 1.5 0 0 0 1.5-1.5v-15a1.5 1.5 0 0 0-1.5-1.5h-15zm13.5 4h-12v2h12v-2zm-12 4h12v2h-12v-2zm6 4h-6v2h6v-2z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Message</div>
                                                                </div>
                                                            </div>
                                                            <div title="Duplicate this element" class="duplicate">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M7.5 2a1.5 1.5 0 0 0-1.5 1.5v9.5a1 1 0 0 0 1 1h9.5a1.5 1.5 0 0 0 1.5-1.5v-9a1.5 1.5 0 0 0-1.5-1.5h-9zm-4 4h.5v10h10v.5a1.5 1.5 0 0 1-1.5 1.5h-9a1.5 1.5 0 0 1-1.5-1.5v-9a1.5 1.5 0 0 1 1.5-1.5z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <div title="Sort this element" class="softable">
                                                                <span class="Polaris-Icon">
                                                                    <span class="Polaris-VisuallyHidden"></span>
                                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M7 2a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm6-8a2 2 0 1 0-.001-4.001 2 2 0 0 0 .001 4.001zm0 2a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001zm0 6a2 2 0 1 0 .001 4.001 2 2 0 0 0-.001-4.001z">
                                                                        </path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
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
                                    <div class="list-item">
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
                                    <div class="list-item">
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
                                    <div class="list-item">
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
                                    <div class="list-item">
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
                                    <div class="list-item">
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
                                    <div class="list-item">
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
                    <div class="">
                        <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox12"><span
                                    class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                            id="PolarisCheckbox12" type="checkbox" class="Polaris-Checkbox__Input"
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
                                                    class="Polaris-TextField__Input" type="text"
                                                    aria-labelledby="PolarisTextField58Label" aria-invalid="false"
                                                    value="Contact us">
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
                            <textarea name="content" class="editor">
                                &lt;p&gt;This is some sample content.&lt;/p&gt;
                            </textarea>
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
                        <div class="title clsname_of_element">Other page</div>
                    </div>
                    <div class="">
                        <div class="container tabContent">
                            <div class="">
                                <div class="form-control">
                                    <div class="hidden">
                                        <div class="">
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                        <input id="PolarisTextField23" class="Polaris-TextField__Input"
                                                            type="text" aria-labelledby="PolarisTextField23Label"
                                                            aria-invalid="false" value="text">
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
                                                        <input id="PolarisTextField24" class="Polaris-TextField__Input"
                                                            type="text" aria-labelledby="PolarisTextField24Label"
                                                            aria-invalid="false" value="text">
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
                                                <div class="Polaris-Label"><label id="PolarisTextField25Label"
                                                        for="PolarisTextField25" class="Polaris-Label__Text">
                                                        <div class="lbl_form">Label</div>
                                                    </label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                        <input id="PolarisTextField25" placeholder="Your Name"
                                                            class="Polaris-TextField__Input clsname_of_element_val" type="text"
                                                            aria-labelledby="PolarisTextField25Label"
                                                            aria-invalid="false" value="">
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
                                                <div class="Polaris-Label"><label id="PolarisTextField26Label"
                                                        for="PolarisTextField26" class="Polaris-Label__Text">
                                                        <div>Placeholder</div>
                                                    </label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                        <input id="PolarisTextField26" placeholder="Your Name"
                                                            class="Polaris-TextField__Input" type="text"
                                                            aria-labelledby="PolarisTextField26Label"
                                                            aria-invalid="false" value="">
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
                                                <div class="Polaris-Label"><label id="PolarisTextField27Label"
                                                        for="PolarisTextField27" class="Polaris-Label__Text">
                                                        <div>Description</div>
                                                    </label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField"><input id="PolarisTextField27"
                                                            placeholder="" class="Polaris-TextField__Input" type="text"
                                                            aria-labelledby="PolarisTextField27Label"
                                                            aria-invalid="false" value="">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox20"><span
                                            class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                    id="PolarisCheckbox20" type="checkbox"
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
                                                        id="PolarisTextField28" class="Polaris-TextField__Input"
                                                        type="number" aria-labelledby="PolarisTextField28Label"
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
                                <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox21"><span
                                            class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                    id="PolarisCheckbox21" type="checkbox"
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
                                        for="PolarisCheckbox22"><span class="Polaris-Choice__Control"><span
                                                class="Polaris-Checkbox"><input id="PolarisCheckbox22" type="checkbox"
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
                                            class="Polaris-Choice__Label">Keep position of label</span></label>
                                </div>
                                <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox23"><span
                                            class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                    id="PolarisCheckbox23" type="checkbox"
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
                                <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox24"><span
                                            class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                    id="PolarisCheckbox24" type="checkbox"
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
                                            <div class="chooseItem active">33%</div>
                                            <div class="chooseItem ">50%</div>
                                            <div class="chooseItem ">100%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="element_id_in_form" name="element_id_in_form" value="">
                            <input type="hidden" class="form_data_id_second" name="form_data_id_second" value="">
                            <div class="form-control">
                            <div style="color:#bf0711;text-align: center;">
                                  <button class="Polaris-Button Polaris-Button--outline Polaris-Button--monochrome remove_this_element" type="button">
                                    <span class="Polaris-Button__Content">
                                      <span class="Polaris-Button__Text">Remove this
                                                element</span>
                                    </span>
                                  </button>
</div>
                                <!-- <button
                                    class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain Polaris-Button--fullWidth remove_this_element"
                                    type="button"><span class="Polaris-Button__Content"><span
                                            class="Polaris-Button__Text"><span>Remove this
                                                element</span></span></span></button> -->
                                                
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
                        <div class="title">Other page</div>
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
                                        <div class="chooseItem ">33%</div>
                                        <div class="chooseItem active">50%</div>
                                        <div class="chooseItem ">100%</div>
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
                                            <div class="chooseItem ">33%</div>
                                            <div class="chooseItem ">50%</div>
                                            <div class="chooseItem active">100%</div>
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
                                                <!-- <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="m8.24 9 .816 2.33a1 1 0 0 0 1.888-.66l-3.335-9.528a1.705 1.705 0 0 0-3.218 0l-3.335 9.528a1 1 0 0 0 1.888.66l.815-2.33h4.482zm-.7-2-1.54-4.401-1.54 4.401h3.08zm7.96-2c.608 0 1.18.155 1.68.428a.999.999 0 0 1 1.82.572v5a1 1 0 0 1-1.82.572 3.5 3.5 0 1 1-1.68-6.572zm0 5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z">
                                                                        </path>
                                                                        <path
                                                                            d="M2 14a1 1 0 1 0 0 2h16a1 1 0 1 0 0-2h-16zm0 4a1 1 0 1 0 0 2h12a1 1 0 1 0 0-2h-12z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Text</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M0 5.324v10.176a1.5 1.5 0 0 0 1.5 1.5h17a1.5 1.5 0 0 0 1.5-1.5v-10.176l-9.496 5.54a1 1 0 0 1-1.008 0l-9.496-5.54z">
                                                                        </path>
                                                                        <path
                                                                            d="M19.443 3.334a1.494 1.494 0 0 0-.943-.334h-17a1.49 1.49 0 0 0-.943.334l9.443 5.508 9.443-5.508z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Email</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M10 13c-1.654 0-3-1.346-3-3s1.346-3 3-3 3 1.346 3 3-1.346 3-3 3zm0-13c-5.514 0-10 4.486-10 10s4.486 10 10 10a1 1 0 0 0 0-2c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8v1c0 .827-.673 1.5-1.5 1.5s-1.5-.673-1.5-1.5v-1c0-2.757-2.243-5-5-5s-5 2.243-5 5 2.243 5 5 5c1.531 0 2.887-.707 3.805-1.795a3.477 3.477 0 0 0 2.695 1.295c1.93 0 3.5-1.57 3.5-3.5v-1c0-5.514-4.486-10-10-10z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Name</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="M2.5 1a1.5 1.5 0 0 0-1.5 1.5v15a1.5 1.5 0 0 0 1.5 1.5h15a1.5 1.5 0 0 0 1.5-1.5v-15a1.5 1.5 0 0 0-1.5-1.5h-15zm13.5 4h-12v2h12v-2zm-12 4h12v2h-12v-2zm6 4h-6v2h6v-2z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Textarea</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M6.534 18a4.507 4.507 0 0 1-3.208-1.329 4.54 4.54 0 0 1 0-6.414l1.966-1.964a.999.999 0 1 1 1.414 1.414l-1.966 1.964a2.54 2.54 0 0 0 0 3.586c.961.959 2.631.958 3.587 0l1.966-1.964a1 1 0 1 1 1.415 1.414l-1.966 1.964a4.503 4.503 0 0 1-3.208 1.329zm7.467-6a.999.999 0 0 1-.707-1.707l1.966-1.964a2.54 2.54 0 0 0 0-3.586c-.961-.959-2.631-.957-3.587 0l-1.966 1.964a1 1 0 1 1-1.415-1.414l1.966-1.964a4.503 4.503 0 0 1 3.208-1.329c1.211 0 2.351.472 3.208 1.329a4.541 4.541 0 0 1 0 6.414l-1.966 1.964a.997.997 0 0 1-.707.293zm-6.002 1a.999.999 0 0 1-.707-1.707l4.001-4a1 1 0 1 1 1.415 1.414l-4.001 4a1 1 0 0 1-.708.293z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Url</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="m7.876 6.976-.534-2.67a1.5 1.5 0 0 0-1.471-1.206h-3.233c-.86 0-1.576.727-1.537 1.586.461 10.161 5.499 14.025 14.415 14.413.859.037 1.584-.676 1.584-1.535v-3.235a1.5 1.5 0 0 0-1.206-1.471l-2.67-.534a1.5 1.5 0 0 0-1.636.8l-.488.975c-2 0-5-3-5-5l.975-.488c.606-.302.934-.972.801-1.635z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Phone</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="M7.992 2.124a1 1 0 1 0-1.984-.248l-.39 3.124h-3.618a1 1 0 0 0 0 2h3.367l-.75 6h-2.617a1 1 0 1 0 0 2h2.367l-.36 2.876a1 1 0 1 0 1.985.248l.39-3.124h5.985l-.36 2.876a1 1 0 0 0 1.985.248l.39-3.124h3.618a1 1 0 1 0 0-2h-3.367l.75-6h2.617a1 1 0 1 0 0-2h-2.367l.36-2.876a1 1 0 1 0-1.985-.248l-.39 3.124h-5.986l.36-2.876zm4.625 10.876.75-6h-5.984l-.75 6h5.984z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Number</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M10 12a2 2 0 0 0 2-2c0-.178-.03-.348-.074-.512l5.781-5.781a.999.999 0 1 0-1.414-1.414l-2.61 2.61a7.757 7.757 0 0 0-3.683-.903c-5.612 0-7.837 5.399-7.929 5.628a1.017 1.017 0 0 0 0 .744c.054.133.835 2.011 2.582 3.561l-2.36 2.36a.999.999 0 1 0 1.414 1.414l5.781-5.781c.164.043.334.074.512.074zm-4-2a4 4 0 0 1 4-4c.742 0 1.432.208 2.025.561l-1.513 1.513a2.004 2.004 0 0 0-.512-.074 2 2 0 0 0-2 2c0 .178.031.347.074.511l-1.513 1.514a3.959 3.959 0 0 1-.561-2.025zm10.145-3.144-2.252 2.252c.064.288.106.585.106.893a4 4 0 0 1-4 4 3.97 3.97 0 0 1-.89-.108l-1.682 1.68a7.903 7.903 0 0 0 2.573.427c5.613 0 7.837-5.399 7.928-5.629a1.004 1.004 0 0 0 0-.742c-.044-.111-.596-1.437-1.784-2.773z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Password</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="M17.5 2h-2.5v-1a1 1 0 1 0-2 0v1h-7v-1a1 1 0 0 0-2 0v1h-1.5c-.8 0-1.5.7-1.5 1.5v15c0 .8.7 1.5 1.5 1.5h15c.8 0 1.5-.7 1.5-1.5v-15c0-.8-.7-1.5-1.5-1.5zm-14.5 16h14v-10h-14v10z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Date time</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M5.243 20a5.228 5.228 0 0 1-3.707-1.533 5.213 5.213 0 0 1-1.536-3.708c0-1.402.546-2.719 1.536-3.708l9.515-9.519a5.25 5.25 0 0 1 8.553 1.7 5.21 5.21 0 0 1 .396 2.008 5.208 5.208 0 0 1-1.535 3.708l-4.258 4.26a3.124 3.124 0 0 1-5.092-1.012 3.098 3.098 0 0 1-.236-1.196c0-.835.324-1.619.914-2.208l4.5-4.501a1 1 0 1 1 1.414 1.414l-4.5 4.501a1.112 1.112 0 0 0-.328.794 1.114 1.114 0 0 0 1.121 1.12c.297 0 .582-.118.793-.327l4.258-4.26a3.223 3.223 0 0 0 .949-2.293c0-.866-.337-1.681-.949-2.293a3.248 3.248 0 0 0-4.586 0l-9.515 9.518a3.224 3.224 0 0 0-.95 2.295c0 .866.338 1.68.95 2.293a3.248 3.248 0 0 0 4.586 0l1.757-1.758a1 1 0 1 1 1.414 1.414l-1.757 1.758a5.236 5.236 0 0 1-3.707 1.533z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>File</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M10 12a2 2 0 0 0 2-2c0-.178-.03-.348-.074-.512l5.781-5.781a.999.999 0 1 0-1.414-1.414l-2.61 2.61a7.757 7.757 0 0 0-3.683-.903c-5.612 0-7.837 5.399-7.929 5.628a1.017 1.017 0 0 0 0 .744c.054.133.835 2.011 2.582 3.561l-2.36 2.36a.999.999 0 1 0 1.414 1.414l5.781-5.781c.164.043.334.074.512.074zm-4-2a4 4 0 0 1 4-4c.742 0 1.432.208 2.025.561l-1.513 1.513a2.004 2.004 0 0 0-.512-.074 2 2 0 0 0-2 2c0 .178.031.347.074.511l-1.513 1.514a3.959 3.959 0 0 1-.561-2.025zm10.145-3.144-2.252 2.252c.064.288.106.585.106.893a4 4 0 0 1-4 4 3.97 3.97 0 0 1-.89-.108l-1.682 1.68a7.903 7.903 0 0 0 2.573.427c5.613 0 7.837-5.399 7.928-5.629a1.004 1.004 0 0 0 0-.742c-.044-.111-.596-1.437-1.784-2.773z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Hidden</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Selects</h3>
                                        <div>
                                            <div class="setvalue_element_select">
                                                <!-- <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="M4.5 3a1.5 1.5 0 0 0-1.5 1.5v11a1.5 1.5 0 0 0 1.5 1.5h11a1.5 1.5 0 0 0 1.5-1.5v-11a1.5 1.5 0 0 0-1.5-1.5h-11zm9.207 5.707a1 1 0 0 0-1.414-1.414l-3.293 3.293-1.293-1.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Checkboxes</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="M9.128.233c-2.37 1.383-5.37 2.33-7.635 2.646-.821.115-1.495.79-1.493 1.62l.001.497c-.03 6.043.477 11.332 9.462 14.903a1.45 1.45 0 0 0 1.062 0c8.993-3.571 9.503-8.86 9.473-14.903v-.501c-.001-.828-.674-1.51-1.492-1.638-2.148-.337-5.281-1.274-7.65-2.628a1.733 1.733 0 0 0-1.728.004zm4.577 8.478a1 1 0 0 0-1.414-1.415l-3.293 3.294-1.293-1.293a1 1 0 1 0-1.415 1.413l2 2.001a1 1 0 0 0 1.414 0l4-4.001z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Accept terms</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M10 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm0-14c-3.309 0-6 2.691-6 6s2.691 6 6 6 6-2.691 6-6-2.691-6-6-6zm-1 9a.997.997 0 0 1-.707-.293l-2-2a1 1 0 1 1 1.414-1.414l1.293 1.293 3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a.996.996 0 0 1-.707.293z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Radio buttons</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0zm-4.293-1.707a1 1 0 0 0-1.414 0l-2.293 2.293-2.293-2.293a1 1 0 0 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l3-3a1 1 0 0 0 0-1.414z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Dropdown</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="M3.07 6a8.025 8.025 0 0 1 4.262-3.544 12.802 12.802 0 0 0-1.737 3.544h-2.525zm-.818 2a8.015 8.015 0 0 0-.252 2c0 .69.088 1.36.252 2h2.89a13.886 13.886 0 0 1-.142-2c0-.704.051-1.371.143-2h-2.891zm4.916 0c-.108.62-.168 1.286-.168 2 0 .713.061 1.38.168 2h5.664c.107-.62.168-1.287.168-2 0-.714-.061-1.38-.168-2h-5.664zm7.69 0a14.102 14.102 0 0 1-.001 4h2.891a8 8 0 0 0 .252-2 8 8 0 0 0-.252-2h-2.89zm2.072-2h-2.525a12.805 12.805 0 0 0-1.737-3.544 8.025 8.025 0 0 1 4.262 3.544zm-4.638 0h-4.584c.324-.865.725-1.596 1.124-2.195.422-.633.842-1.117 1.168-1.452.326.335.746.82 1.168 1.452.4.599.8 1.33 1.124 2.195zm-1.124 10.195c.4-.599.8-1.33 1.124-2.195h-4.584c.324.865.725 1.596 1.124 2.195.422.633.842 1.117 1.168 1.452.326-.335.746-.82 1.168-1.452zm-8.098-2.195h2.525a12.802 12.802 0 0 0 1.737 3.544 8.025 8.025 0 0 1-4.262-3.544zm9.762 3.305a12.9 12.9 0 0 1-.164.24 8.025 8.025 0 0 0 4.262-3.545h-2.525a12.805 12.805 0 0 1-1.573 3.305zm7.168-7.305c0 5.52-4.472 9.994-9.99 10h-.022c-5.518-.006-9.988-4.481-9.988-10 0-5.523 4.477-10 10-10s10 4.477 10 10z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Country</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Static text</h3>
                                        <div>
                                            <div class="setvalue_element_static">
                                                <!-- <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                            d="M9 0c-.6 0-1.1.4-1.4 1l-5.6 16.3a1 1 0 0 1-.9.7 1 1 0 1 0 0 2h4a1 1 0 1 0 0-2 1 1 0 0 1-.9-1.2l.8-2.8h7l.9 2.8a1 1 0 0 1-.9 1.2 1 1 0 1 0 0 2h7a1 1 0 1 0 0-2 1 1 0 0 1-1-.7l-5.5-16.3c-.3-.6-.8-1-1.5-1h-2zm-.5 4.3-2.7 7.7h5.4l-2.7-7.7z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Heading</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M3 3h14a1 1 0 1 1 0 2h-14a1 1 0 0 1 0-2zm0 4h10a1 1 0 1 1 0 2h-10a1 1 0 0 1 0-2zm0 4h14a1 1 0 0 1 0 2h-14a1 1 0 0 1 0-2zm0 4h10a1 1 0 0 1 0 2h-10a1 1 0 0 1 0-2z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Paragraph</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Rating</h3>
                                        <div>
                                            <div class="">
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="m6.71 15.116 3.357-1.658.892.452 2.327 1.178-.56-3.912.708-.707 1.29-1.29-3.235-.576-.445-.915-1.059-2.176-1.585 3.171-1.005.168-2.098.35 1.975 1.975-.141.99-.422 2.95zm-1.51 2.884a.8.8 0 0 1-.792-.914l.743-5.203-2.917-2.917a.8.8 0 0 1 .434-1.355l4.398-.733 2.218-4.435a.8.8 0 0 1 1.435.008l2.123 4.361 4.498.801a.8.8 0 0 1 .425 1.353l-2.917 2.917.744 5.203a.8.8 0 0 1-1.154.828l-4.382-2.22-4.502 2.223a.792.792 0 0 1-.354.083z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Rating star</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Structure</h3>
                                        <div>
                                            <div class="setvalue_element_structure">
                                                <!-- <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M19 17.5v-13a1.5 1.5 0 0 0-1.5-1.5h-6.879a1.5 1.5 0 0 1-1.06-.44l-1.122-1.12a1.5 1.5 0 0 0-1.059-.44h-4.88a1.5 1.5 0 0 0-1.5 1.5v15a1.5 1.5 0 0 0 1.5 1.5h15a1.5 1.5 0 0 0 1.5-1.5z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Group</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M14.167 9h-8.334c-.46 0-.833.448-.833 1s.372 1 .833 1h8.334c.46 0 .833-.448.833-1s-.373-1-.833-1">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>Divider</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="builder-item-wrapper ">
                                    <div class="">
                                        <h3 class="subheading">Customization</h3>
                                        <div>
                                            <div class="setvalue_element_customization">
                                                <!-- <div class="builder-item-wrapper ">
                                                    <div class="list-item">
                                                        <div class="row">
                                                            <div class="icon"><span class="Polaris-Icon"><span
                                                                        class="Polaris-VisuallyHidden"></span><svg
                                                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg"
                                                                        focusable="false" aria-hidden="true">
                                                                        <path
                                                                            d="M2.707 9.707a.996.996 0 0 0 .293-.707v-4a1 1 0 0 1 1-1 1 1 0 0 0 0-2c-1.654 0-3 1.346-3 3v3.586l-.707.707a.999.999 0 0 0 0 1.414l.707.707v3.586c0 1.654 1.346 3 3 3a1 1 0 0 0 0-2 1 1 0 0 1-1-1v-4a.996.996 0 0 0-.293-.707l-.293-.293.293-.293zm17.217-.09a1.001 1.001 0 0 0-.217-.324l-.707-.707v-3.586c0-1.654-1.346-3-3-3a1 1 0 1 0 0 2 1 1 0 0 1 1 1v4a.997.997 0 0 0 .293.707l.293.293-.293.293a.996.996 0 0 0-.293.707v4a1 1 0 0 1-1 1 1 1 0 1 0 0 2c1.654 0 3-1.346 3-3v-3.586l.707-.707a1.001 1.001 0 0 0 .217-1.09zm-7.227-4.333a1.002 1.002 0 0 0-1.63.346l-3.996 8a.999.999 0 0 0 .56 1.299 1.006 1.006 0 0 0 1.302-.557l3.995-8a.997.997 0 0 0-.23-1.088z">
                                                                        </path>
                                                                    </svg></span></div>
                                                            <div class="title">
                                                                <div>
                                                                    <div>HTML</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
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
                                <div class="">
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
                                                                class="Polaris-TextField__Input" type="text"
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
                                                        type="checkbox" class="Polaris-Checkbox__Input"
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
                                    <div class="form-control hidden">
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
                                                                class="Polaris-TextField__Input" type="text"
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
                                                        type="checkbox" class="Polaris-Checkbox__Input"
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
                                    <div class="form-control">
                                        <div class="chooseInput">
                                            <div class="label">Alignment</div>
                                            <div class="chooseItems">
                                                <div class="chooseItem active">Left</div>
                                                <div class="chooseItem ">Center</div>
                                                <div class="chooseItem ">Right</div>
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
        <script>
    $(document).ready(function() {
        // get_three_element();
        set_all_element_selected(<?php echo $form_id; ?>);
        getFormTitle(<?php echo $form_id; ?>);
    });

</script>