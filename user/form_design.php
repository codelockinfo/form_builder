<?php 
include_once('cls_header.php'); 
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : 0;

// Get public_id (6-digit ID) for this form
$public_id = '';
if ($form_id > 0) {
    $where_query = array(["", "id", "=", "$form_id"]);
    $form_check = $functions->select_result(TABLE_FORMS, 'public_id', $where_query, ['single' => true]);
    if ($form_check['status'] == 1 && !empty($form_check['data'])) {
        $public_id = isset($form_check['data']['public_id']) && !empty($form_check['data']['public_id']) 
            ? $form_check['data']['public_id'] 
            : $form_id; // Fallback to database ID if public_id not set
    } else {
        $public_id = $form_id; // Fallback
    }
} else {
    $public_id = '';
}
?>
<body style="padding: 0; margin: 0;">
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
                                            value="" readonly>
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                    <div class="form-id-display-header" style="margin-left: 10px; display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 13px; color: #6b7280; font-weight: 500;">Form ID:</span>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span id="form-id-display" class="form-id-value" style="font-family: monospace; font-size: 13px; background: #f3f4f6; padding: 4px 10px; border-radius: 4px; color: #1f2937; font-weight: 600;"><?php echo htmlspecialchars($public_id); ?></span>
                                            <button type="button" onclick="copyFormIdToClipboard('<?php echo htmlspecialchars($public_id); ?>')" class="Polaris-Button Polaris-Button--plain copy-form-id-header-btn" style="padding: 4px; font-size: 12px; min-height: auto; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;" title="Copy Form ID">
                                                <span class="Polaris-Button__Content">
                                                    <span class="Polaris-Button__Icon">
                                                        <svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8 2C7.44772 2 7 2.44772 7 3V4H5C3.89543 4 3 4.89543 3 6V16C3 17.1046 3.89543 18 5 18H13C14.1046 18 15 17.1046 15 16V14H16C16.5523 14 17 13.5523 17 13V5C17 4.44772 16.5523 4 16 4H9C8.44772 4 8 3.55228 8 3V2Z" fill="currentColor" opacity="0.6"/>
                                                            <path d="M5 6H13V16H5V6Z" fill="currentColor"/>
                                                        </svg>
                                                    </span>
                                                </span>
                                            </button>
                                            <span id="copy-success-msg" style="font-size: 11px; color: #10b981; display: none;">✓ Copied!</span>
                                        </div>
                                        <span style="font-size: 11px; color: #6b7280; margin-left: 8px;">(Use this ID in Theme Customizer)</span>
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
                            <div class="Polaris-ButtonGroup__Item cls_design_enaDisa">
                                <button class="Polaris-Button Polaris-Button--primary publish-form-btn" type="button">
                                    <span class="Polaris-Button__Content">
                                        <span class="Polaris-Button__Text">
                                            <span>Publish</span>
                                        </span> 
                                    </span>
                                </button>
                            </div>
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
                                    <button class="Polaris-Button Polaris-Button--primary saveForm save_loader_show" aria-disabled="false"
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
                    <a href="index.php?shop=<?php echo $store; ?>">
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
                <!-- <div style="color: rgb(0, 128, 96); padding: 0px 0.8rem;">
                    <button class="Polaris-Button Polaris-Button--outline Polaris-Button--monochrome" type="button">
                        <span class="Polaris-Button__Content">
                            <span class="Polaris-Button__Text">
                                <span>Explore Pricing Plans</span>
                            </span>
                        </span>
                    </button>
                </div> -->
            </div>

        </div>
        <div class="form_content">
            <div class="preview-card">
                <div class="banner">
                    <div></div>
                </div>
                <div class="preview-box iframe-wrapper desktop">
                    <div class="contact-form">
                        <div class="code-form-app boxed-layout" style="padding: 0;">
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
                            <li class="Polaris-Tabs__TabContainer hidden" role="presentation" data-tab="2">
                                <button id="settings" role="tab" type="button" tabindex="-1"
                                    class="Polaris-Tabs__Tab settingsbtn" aria-selected="false"
                                    aria-controls="settings-fitted-content">
                                    <span class="Polaris-Tabs__Title">Settings</span>
                                </button>
                            </li>
                            <li class="Polaris-Tabs__TabContainer" role="presentation" data-tab="3">
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
                                        <!-- <h3 class="subheading">Elements</h3> -->
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
                            <div class="root">
                                <div class="builder-item-wrapper ">
                                    <div class="list-item theme-settings-item" data-owl="15">
                                        <div class="row">
                                            <div class="icon">
                                                <span class="Polaris-Icon">
                                                    <span class="Polaris-VisuallyHidden"></span>
                                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false"
                                                        aria-hidden="true">
                                                        <path
                                                            d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm0 14a6 6 0 1 1 0-12 6 6 0 0 1 0 12zm-1-9a1 1 0 0 0 0 2h2a1 1 0 1 0 0-2H9zm0 4a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H9z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="title">
                                                <div>Theme Settings</div>
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
                        <div class="tabContent polarisformcontrol">
                            <div class="container">
                                <div>
                                    <div class="">
                                        <form class="add_publishdata" method="POST" >
                                        <input type="hidden" class="formid" name="formid" value="<?php echo $form_id ?>">
                                            <div class="form-control">
                                                <div>
                                                    <label class="Polaris-Choice">
                                                        <span class="Polaris-Choice__Control">
                                                            <span class="Polaris-Checkbox">
                                                                <input name="require_login" id="required_login" type="checkbox"
                                                                    class="Polaris-Checkbox__Input required_login" aria-invalid="false"
                                                                    aria-describedby="PolarisCheckbox26HelpText" role="checkbox"
                                                                    aria-checked="false" value="1">
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
                                                                <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                                    <textarea name="login_message" class="Polaris-TextField__Input login_message" type="text" rows="2">Please a href='/account/login' title='login'&gt;login&lt;/a&gt; to continue
                                                                    </textarea>
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
                                                                    class="Polaris-TextField__Input embed_code" type="text"
                                                                    aria-describedby="PolarisTextField54HelpText"
                                                                    aria-labelledby="PolarisTextField54Label" aria-invalid="false"
                                                                    value=''>
                                                                <div class="Polaris-TextField__Backdrop"></div>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-Connected__Item copyButton">
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
                                            <div class="form-control hidden">
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
                                                                    </svg></span></span></span><span
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
                                            <div class="form-control hidden" id="lightbox2">
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
                                                                                </svg></span>
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
                                        </form>
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
                                <form class="add_publishdata" method="POST" >
                                <input type="hidden" class="formid" name="formid" value="<?php echo $form_id ?>">
                                    <div class="form-control">
                                        <div>
                                            <label class="Polaris-Choice">
                                                <span class="Polaris-Choice__Control">
                                                    <span class="Polaris-Checkbox">
                                                        <input name="require_login" id="required_login_nested" type="checkbox"
                                                            class="Polaris-Checkbox__Input required_login" aria-invalid="false"
                                                            aria-describedby="PolarisCheckbox26HelpText_nested" role="checkbox"
                                                            aria-checked="false" value="1">
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
                                                <div class="Polaris-Choice__HelpText" id="PolarisCheckbox26HelpText_nested">Only
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
                                                        <div class="Polaris-TextField Polaris-TextField--hasValue Polaris-TextField--multiline">
                                                            <textarea name="login_message" class="Polaris-TextField__Input login_message" type="text" rows="2">Please a href='/account/login' title='login'&gt;login&lt;/a&gt; to continue
                                                            </textarea>
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
                                                <div class="Polaris-Label">
                                                    <label class="Polaris-Label__Text">Select publication type</label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Select selectmain">
                                                <select id="PolarisSelect18_nested" class="select_code">
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
                                                        <input id="PolarisTextField54_nested" readonly="" placeholder=""
                                                            class="Polaris-TextField__Input embed_code" type="text"
                                                            aria-describedby="PolarisTextField54HelpText_nested"
                                                            aria-labelledby="PolarisTextField54Label_nested" aria-invalid="false"
                                                            value=''>
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected__Item copyButton">
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
                                            <div class="Polaris-Labelled__HelpText" id="PolarisTextField54HelpText_nested">Copy
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
                                                        <label id="PolarisTextField55Label_nested" for="PolarisTextField55_nested"
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
                                                            <input id="PolarisTextField55_nested" readonly="" placeholder=""
                                                                class="Polaris-TextField__Input" type="text"
                                                                aria-describedby="PolarisTextField55HelpText_nested"
                                                                aria-labelledby="PolarisTextField55Label_nested"
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
                                                            <input id="PolarisTextField56_nested" readonly="" placeholder=""
                                                                class="Polaris-TextField__Input" type="text"
                                                                aria-describedby="PolarisTextField56HelpText_nested"
                                                                aria-labelledby="PolarisTextField56Label_nested"
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
                                                            <input id="PolarisTextField57_nested" readonly="" placeholder=""
                                                                class="Polaris-TextField__Input" type="text"
                                                                aria-labelledby="PolarisTextField57Label_nested"
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
                                    <div class="form-control hidden">
                                        <label class="Polaris-Choice">
                                            <span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input
                                                        id="PolarisCheckbox27_nested" type="checkbox"
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
                                                <select id="PolarisSelect19_nested" class="select_code">
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
                                                <select id="PolarisSelect20_nested" class="select_code">
                                                    <option value="top">At the top of the page</option>
                                                    <option value="bottom">At the bottom of the page</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-control hidden" id="lightbox2">
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
                                                <select id="PolarisSelect21_nested" class="select_code">
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
                                                            id="PolarisTextField59-Prefix_nested">hours</div>
                                                        <input id="PolarisTextField59_nested"
                                                            class="Polaris-TextField__Input hoursadd" min="1" type="number"
                                                            aria-labelledby="PolarisTextField59Label_nested PolarisTextField59-Prefix_nested"
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
                                                            id="PolarisTextField60-Prefix_nested">weeks</div>
                                                        <input id="PolarisTextField60_nested"
                                                            class="Polaris-TextField__Input weekadd" min="1" type="number"
                                                            aria-labelledby="PolarisTextField60Label_nested PolarisTextField60-Prefix_nested"
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
                                </form>
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
                    <div class="container">
                        <form class="add_headerdata" method="POST">
                            <input type="hidden" class="form_id" name="form_id"  value=''>
                            <div class="form-control"><label class="Polaris-Choice" for="PolarisCheckbox12"><span
                                        class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input name="showheader"
                                                id="PolarisCheckbox12" type="checkbox" class="Polaris-Checkbox__Input showHeader"
                                                aria-invalid="false" role="checkbox" aria-checked="true" value="1"
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
                                                <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input name="header__title" placeholder="" class="Polaris-TextField__Input headerTitle" type="text" aria-labelledby="PolarisTextField58Label" aria-invalid="false">
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
                                <textarea name="contentheader" id="contentheader" class="myeditor"></textarea>
                            </div>
                            
                            <!-- Header Design Customization -->
                            <div class="form-control design-customizer-section" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                                <div style="margin-bottom: 16px;">
                                    <div class="Polaris-Label">
                                        <label class="Polaris-Label__Text" style="font-weight: 600; font-size: 16px;">Design Customization</label>
                                    </div>
                                </div>
                                
                                <!-- Heading Font Size -->
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label class="Polaris-Label__Text">Heading Font Size</label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                        <input type="number" name="header_heading_font_size" id="headerHeadingFontSize" class="Polaris-TextField__Input header-design-heading-font-size" value="24" min="10" max="72" step="1" placeholder="24">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected__Item" style="width: 45px;">
                                                    <div style="display: flex; align-items: center; height: 100%; padding-left: 8px; color: #6d7175; font-size: 14px;">px</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Heading Text Color -->
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label class="Polaris-Label__Text">Heading Text Color</label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item" style="width: 60px;">
                                                    <input type="color" name="header_heading_text_color" class="header-design-heading-text-color" value="#000000" style="width: 100%; height: 40px; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                                                </div>
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                        <input type="text" name="header_heading_text_color_text" class="Polaris-TextField__Input header-design-heading-text-color-text" value="#000000" placeholder="#000000">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Sub-heading Font Size -->
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label class="Polaris-Label__Text">Sub-heading Font Size</label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                        <input type="number" name="header_subheading_font_size" id="headerSubheadingFontSize" class="Polaris-TextField__Input header-design-subheading-font-size" value="16" min="10" max="72" step="1" placeholder="16">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                                <div class="Polaris-Connected__Item" style="width: 45px;">
                                                    <div style="display: flex; align-items: center; height: 100%; padding-left: 8px; color: #6d7175; font-size: 14px;">px</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Sub-heading Text Color -->
                                <div class="form-control">
                                    <div class="textfield-wrapper">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                    <label class="Polaris-Label__Text">Sub-heading Text Color</label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item" style="width: 60px;">
                                                    <input type="color" name="header_subheading_text_color" class="header-design-subheading-text-color" value="#000000" style="width: 100%; height: 40px; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                                                </div>
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                        <input type="text" name="header_subheading_text_color_text" class="Polaris-TextField__Input header-design-subheading-text-color-text" value="#000000" placeholder="#000000">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Text Align -->
                                <div class="form-control">
                                    <input name="header_text_align" type="hidden" value="center" class="header-text-align-input">
                                    <div class="chooseInput">
                                        <div class="label">Alignment</div>
                                        <div class="chooseItems">
                                            <div class="chooseItem-align" data-value="left">Left</div>
                                            <div class="chooseItem-align active" data-value="center">Center</div>
                                            <div class="chooseItem-align" data-value="right">Right</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ensure proper spacing at bottom -->
                            <div style="margin-bottom: 20px;"></div>
                        </form>
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
                            <button class="Polaris-Button Polaris-Button--destructive removeElement" type="button">
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
                        <form class="add_footerdata" method="POST">
                            <div class="container tabContent footerData">
                                    <div class="">
                                        <div class="footerData">
                                    
                                        <input type="hidden" class="form_id" name="form_id" value=''>
                                        <div class="form-control">
                                            <div class="textfield-wrapper">
                                                <div class="">
                                                    <textarea name="contentfooter" id="contentfooter" class="myeditor"></textarea>
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
                                                                <input id="PolarisTextField17" placeholder="" name="footer-data__submittext"
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
                                                        class="Polaris-Checkbox"><input name="resetbutton" id="PolarisCheckbox6"
                                                            type="checkbox" class="Polaris-Checkbox__Input resetButton"
                                                            aria-invalid="false" role="checkbox" aria-checked="false"
                                                            value="1"><span
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
                                        <div class="form-control hidden reset input_reset">
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
                                                                <input id="PolarisTextField18" placeholder=""   name="footer-data__resetbuttontext"
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
                                                        class="Polaris-Checkbox"><input name="fullwidth" id="PolarisCheckbox7"
                                                            type="checkbox" class="Polaris-Checkbox__Input fullFooterButton"
                                                            aria-invalid="false" role="checkbox" aria-checked="false"
                                                            value="1"><span
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
                                                    class="Polaris-Choice__Label">Full width footerbutton</span></label></div>
                                        <div class="form-control alignment" >
                                            <input name="footer-button__alignment" type="hidden" value="align-left" class="footer-button__alignment">
                                            <div class="chooseInput">
                                                <div class="label">Alignment</div>
                                                <div class="chooseItems">
                                                    <div class="chooseItem-align active" data-value="align-left">Left</div>
                                                    <div class="chooseItem-align" data-value="align-center">Center</div>
                                                    <div class="chooseItem-align" data-value="align-right">Right</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Button Design Customization -->
                                        <div class="form-control design-customizer-section" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                                            <div style="margin-bottom: 16px;">
                                                <div class="Polaris-Label">
                                                    <label class="Polaris-Label__Text" style="font-weight: 600; font-size: 16px;">Button Design Customization</label>
                                                </div>
                                            </div>
                                            
                                            <!-- Button Text Size -->
                                            <div class="form-control">
                                                <div class="textfield-wrapper">
                                                    <div class="">
                                                        <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label">
                                                                <label class="Polaris-Label__Text">Button Text Size</label>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                <div class="Polaris-TextField">
                                                                    <input type="number" name="footer_button_text_size" id="footerButtonTextSize" class="Polaris-TextField__Input footer-design-button-text-size" value="16" min="10" max="72" step="1" placeholder="16">
                                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                                </div>
                                                            </div>
                                                            <div class="Polaris-Connected__Item" style="width: 45px;">
                                                                <div style="display: flex; align-items: center; height: 100%; padding-left: 8px; color: #6d7175; font-size: 14px;">px</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Button Text Color -->
                                            <div class="form-control">
                                                <div class="textfield-wrapper">
                                                    <div class="">
                                                        <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label">
                                                                <label class="Polaris-Label__Text">Button Text Color</label>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item" style="width: 60px;">
                                                                <input type="color" name="footer_button_text_color" class="footer-design-button-text-color" value="#ffffff" style="width: 100%; height: 40px; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                                                            </div>
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                <div class="Polaris-TextField">
                                                                    <input type="text" name="footer_button_text_color_text" class="Polaris-TextField__Input footer-design-button-text-color-text" value="#ffffff" placeholder="#ffffff">
                                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Button Background Color -->
                                            <div class="form-control">
                                                <div class="textfield-wrapper">
                                                    <div class="">
                                                        <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label">
                                                                <label class="Polaris-Label__Text">Button Background Color</label>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item" style="width: 60px;">
                                                                <input type="color" name="footer_button_bg_color" class="footer-design-button-bg-color" value="#EB1256" style="width: 100%; height: 40px; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                                                            </div>
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                <div class="Polaris-TextField">
                                                                    <input type="text" name="footer_button_bg_color_text" class="Polaris-TextField__Input footer-design-button-bg-color-text" value="#EB1256" placeholder="#EB1256">
                                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Button Hover Background Color -->
                                            <div class="form-control">
                                                <div class="textfield-wrapper">
                                                    <div class="">
                                                        <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label">
                                                                <label class="Polaris-Label__Text">Button Hover Background Color</label>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item" style="width: 60px;">
                                                                <input type="color" name="footer_button_hover_bg_color" class="footer-design-button-hover-bg-color" value="#C8104A" style="width: 100%; height: 40px; border: 1px solid #d1d5db; border-radius: 4px; cursor: pointer;">
                                                            </div>
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                <div class="Polaris-TextField">
                                                                    <input type="text" name="footer_button_hover_bg_color_text" class="Polaris-TextField__Input footer-design-button-hover-bg-color-text" value="#C8104A" placeholder="#C8104A">
                                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Border Radius -->
                                            <div class="form-control">
                                                <div class="textfield-wrapper">
                                                    <div class="">
                                                        <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label">
                                                                <label class="Polaris-Label__Text">Border Radius</label>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                <div class="Polaris-TextField">
                                                                    <input type="number" name="footer_button_border_radius" id="footerButtonBorderRadius" class="Polaris-TextField__Input footer-design-button-border-radius" value="4" min="0" max="50" step="1" placeholder="4">
                                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                                </div>
                                                            </div>
                                                            <div class="Polaris-Connected__Item" style="width: 45px;">
                                                                <div style="display: flex; align-items: center; height: 100%; padding-left: 8px; color: #6d7175; font-size: 14px;">px</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="polarisformcontrol theme-settings-content">
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
                        <div class="title">Theme Settings</div>
                    </div>
                    <div class="" style="height: calc(100vh - 5.6rem); overflow: hidden;">
                        <div class="themeSettingsData" style="height: 100%; overflow-y: auto; padding: 20px;">
                                    <!-- Loader -->
                                    <div id="themeSettingsLoader" style="display: none; text-align: center; padding: 40px 20px;">
                                        <div class="Polaris-Spinner Polaris-Spinner--sizeSmall"></div>
                                        <span style="margin-left: 10px;">Loading theme settings...</span>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div id="themeSettingsContent" style="display: none;">
                                        <!-- Colors Section -->
                                        <div class="theme-settings-section">
                                            <div class="theme-settings-section-header">
                                                <h3 class="theme-settings-section-title">Colors</h3>
                                                <button class="theme-settings-expand-btn" data-section="colors">
                                                    <svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor">
                                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="theme-settings-section-content" id="colorsSectionContent">
                                                <!-- Schemes Subsection -->
                                                <div class="theme-settings-subsection">
                                                    <h4 class="theme-settings-subsection-title">Schemes</h4>
                                                    <p class="theme-settings-subsection-description">Color schemes can be applied to sections throughout your online store.</p>
                                                    <div id="colorSchemaContainer" class="color-schemes-grid">
                                                        <!-- Color schemes will be loaded here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Fonts Section -->
                                        <div class="theme-settings-section">
                                            <div class="theme-settings-section-header">
                                                <h3 class="theme-settings-section-title">Fonts</h3>
                                                <button class="theme-settings-expand-btn" data-section="fonts">
                                                    <svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor">
                                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="theme-settings-section-content" id="fontsSectionContent">
                                                <div id="fontsContainer" class="fonts-list">
                                                    <!-- Fonts will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                                        
                                        <!-- Text Presets Section -->
                                        <div class="theme-settings-section">
                                            <div class="theme-settings-section-header">
                                                <h3 class="theme-settings-section-title">Text presets</h3>
                                                <button class="theme-settings-expand-btn" data-section="textPresets">
                                                    <svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor">
                                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="theme-settings-section-content" id="textPresetsSectionContent">
                                                <p class="theme-settings-note">Sizes automatically scale for all screen sizes.</p>
                                                <div id="typographyContainer" class="text-presets-list">
                                                    <!-- Text presets will be loaded here -->
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
                                                    <input placeholder="https://" class="Polaris-TextField__Input" type="text" aria-describedby="PolarisTextField58HelpText" aria-labelledby="PolarisTextField58Label" aria-invalid="false" value="">
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
                                                    <label id="PolarisTextField59Label_appearance" for="PolarisTextField59_appearance" class="Polaris-Label__Text">
                                                    <div>Floating text</div>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                    <input id="PolarisTextField59_appearance" placeholder="" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField59Label_appearance" aria-invalid="false" value="">
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
                                                <div class="Polaris-Label"><label id="PolarisTextField60Label_appearance" for="PolarisTextField60_appearance" class="Polaris-Label__Text">Form type</label></div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                    <input id="PolarisTextField60_appearance" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField60Label_appearance" aria-invalid="false" value="normalForm">
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
    </div>
</body>
    <!-- Publish Form Modals -->
    <!-- Modal 1: Select Store Page -->
    <div id="publishPageModal" class="Polaris-Modal-Dialog__Container" style="display: none; z-index: 10000;">
        <div class="Polaris-Modal-Dialog Polaris-Modal-Dialog--sizeLarge">
            <div class="Polaris-Modal-Dialog__Modal" style="height: auto;  width: 500px!important;">
                <div class="Polaris-Modal-Dialog__Body">
                    <div class="Polaris-Modal-Dialog__Content">
                        <div class="Polaris-Page" style="padding: 0;">
                            <div class="Polaris-Page__Content">
                                <div class="Polaris-Layout">
                                    <div class="Polaris-Layout__Section" style="margin-top: 0;">
                                        <div class="Polaris-Card" style="border-radius: 0; box-shadow: none; padding: 0;">
                                            <div class="Polaris-Card__Header">
                                                <h2 class="Polaris-Heading">Select Store Page</h2>
                                            </div>
                                            <div class="Polaris-Card__Section" style="padding-bottom: 0;">
                                                <!-- Content -->
                                                <div id="pageSelectionContent">
                                                    <div class="Polaris-TextField">
                                                        <input type="text" id="pageSearchInput" class="Polaris-TextField__Input" placeholder="Search pages..." aria-invalid="false">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                    <div class="Polaris-DataTable" style="margin-top: 20px;">
                                                        <table class="table" style="width: 100%!important; margin-bottom: 0;">
                                                            <thead style="text-align: left!important;">
                                                                <tr>
                                                                    <th>Title</th>
                                                                    <th style="text-align: right!important;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="pagesListBody">
                                                                <!-- Pages will be loaded here -->
                                                            </tbody>
                                                        </table>
                                                        <!-- <div id="pagesPagination" class="cls-page-pagination mb-4" style="margin-top: 20px;"></div> -->
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
                <div class="Polaris-Modal-Dialog__Footer">
                    <div class="Polaris-ButtonGroup">
                        <div class="Polaris-ButtonGroup__Item">
                            <button type="button" class="Polaris-Button closePageModal">
                                <span class="Polaris-Button__Content">
                                    <span class="Polaris-Button__Text">Cancel</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal 2: Confirm Publish with Form ID -->
    <div id="publishConfirmModal" class="Polaris-Modal-Dialog__Container" style="display: none; z-index: 10001;">
        <div class="Polaris-Modal-Dialog Polaris-Modal-Dialog--sizeLarge">
            <div class="Polaris-Modal-Dialog__Modal" style="height: auto;">
                <div class="Polaris-Modal-Dialog__Body">
                    <div class="Polaris-Modal-Dialog__Content">
                        <div class="Polaris-Page" style="padding: 0;">
                            <div class="Polaris-Page__Content">
                                <div class="Polaris-Layout">
                                    <div class="Polaris-Layout__Section" style="margin-top: 0;">
                                        <div class="Polaris-Card" style="border-radius: 0; box-shadow: none; padding: 0;">
                                            <div class="Polaris-Card__Header">
                                                <h2 class="Polaris-Heading">Publish Form</h2>
                                            </div>
                                            <div class="Polaris-Card__Section" style="padding-bottom: 0;">
                                                <!-- Loader -->
                                                <div id="publishConfirmLoader" style="text-align: center; padding: 40px 20px;">
                                                    <div class="Polaris-Spinner Polaris-Spinner--sizeSmall"></div>
                                                    <span style="margin-left: 10px;">Loading...</span>
                                                </div>
                                                
                                                <!-- Content (hidden initially) -->
                                                <div id="publishConfirmContent" style="display: none;">
                                                    <p style="margin-bottom: 15px;"><strong>Selected Page:</strong> <span id="selectedPageTitle"></span></p>
                                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 30px;">
                                                        <p style="margin: 0;"><strong>Form ID:</strong></p>
                                                        <span id="publishFormId" style="font-family: monospace; background: #f3f4f6; padding: 8px 12px; border-radius: 4px; font-weight: 600; font-size: 14px;"><?php echo htmlspecialchars($public_id); ?></span>
                                                        <button type="button" onclick="copyFormIdToClipboard('<?php echo htmlspecialchars($public_id); ?>')" class="Polaris-Button Polaris-Button--plain copy-form-id-header-btn" style="padding: 4px; font-size: 12px; min-height: auto; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;" title="Copy Form ID">
                                                            <span class="Polaris-Button__Content">
                                                                <span class="Polaris-Button__Icon">
                                                                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M8 2C7.44772 2 7 2.44772 7 3V4H5C3.89543 4 3 4.89543 3 6V16C3 17.1046 3.89543 18 5 18H13C14.1046 18 15 17.1046 15 16V14H16C16.5523 14 17 13.5523 17 13V5C17 4.44772 16.5523 4 16 4H9C8.44772 4 8 3.55228 8 3V2Z" fill="currentColor" opacity="0.6"/>
                                                                        <path d="M5 6H13V16H5V6Z" fill="currentColor"/>
                                                                    </svg>
                                                                </span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Guidance Steps -->
                                                    <div style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
                                                        <h3 class="Polaris-Heading" style="font-size: 16px; margin-bottom: 20px;">How to Add Form to Your Page:</h3>
                                                        
                                                        <!-- Step 1: How to Add section -->
                                                        <div style="margin-bottom: 25px;">
                                                            <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                                                <span style="background: #00848e; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; margin-right: 12px;">1</span>
                                                                <h4 style="margin: 0; font-size: 15px; font-weight: 600;">How to Add section?</h4>
                                                            </div>
                                                            <div style="margin-left: 40px; margin-top: 10px;">
                                                                <img src="<?php echo main_url('assets/images/ADD_SECTION.png'); ?>" alt="How to Add Section" style="max-width: 100%; height: auto; border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Step 2: How to Get Form -->
                                                        <div style="margin-bottom: 20px;">
                                                            <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                                                <span style="background: #00848e; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; margin-right: 12px;">2</span>
                                                                <h4 style="margin: 0; font-size: 15px; font-weight: 600;">How to get Form?</h4>
                                                            </div>
                                                            <div style="margin-left: 40px; margin-top: 10px;">
                                                                <img src="<?php echo main_url('assets/images/GET_FORM.png'); ?>" alt="How to Get Form" style="max-width: 100%; height: auto; border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <p style="margin-top: 20px; font-size: 13px; color: #6b7280;">Click Publish to redirect to the page customizer.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Polaris-Modal-Dialog__Footer">
                    <div class="Polaris-ButtonGroup">
                        <div class="Polaris-ButtonGroup__Item">
                            <button type="button" class="Polaris-Button closeConfirmModal">
                                <span class="Polaris-Button__Content">
                                    <span class="Polaris-Button__Text">Cancel</span>
                                </span>
                            </button>
                        </div>
                        <div class="Polaris-ButtonGroup__Item">
                            <button type="button" class="Polaris-Button Polaris-Button--primary confirmPublishBtn" id="confirmPublishBtn" style="display: none;">
                                <span class="Polaris-Button__Content">
                                    <span class="Polaris-Button__Text">Publish</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* Copy Form ID Header Button Styling */
        .copy-form-id-header-btn {
            background: transparent !important;
            border: none !important;
            padding: 4px !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            color: #6d7175;
            min-width: 28px;
            min-height: 28px;
        }
        
        .copy-form-id-header-btn:hover {
            background: #f6f6f7 !important;
            color: #202223;
        }
        
        .copy-form-id-header-btn:active {
            background: #e1e3e5 !important;
            transform: scale(0.95);
        }
        
        .copy-form-id-header-btn svg {
            width: 24px;
            height: 24px;
            fill: black;
        }
        
        .copy-form-id-header-btn .Polaris-Button__Content {
            /* padding-left: 10px!important; */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .Polaris-Modal-Dialog__Container {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10000;
        }
        .Polaris-Modal-Dialog__Modal {
            background: #fff;
            border-radius: 8px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        .Polaris-Modal-Dialog__Body {
            padding: 20px;
        }
        .Polaris-Modal-Dialog__Footer {
            padding: 16px 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
        }
        .page-item-row {
            cursor: pointer;
        }
        .page-item-row:hover {
            background-color: #f3f4f6;
        }
        .page-item-row.selected {
            background-color: #e0f2fe;
        }
        
        /* Theme Settings Styles */
        .theme-settings-section {
            margin-bottom: 32px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 24px;
        }
        .theme-settings-section:last-child {
            border-bottom: none;
        }
        .theme-settings-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            cursor: pointer;
        }
        .theme-settings-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #202223;
            margin: 0;
        }
        .theme-settings-expand-btn {
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: #6d7175;
            display: flex;
            align-items: center;
            transition: transform 0.2s;
        }
        .theme-settings-expand-btn:hover {
            color: #202223;
        }
        .theme-settings-expand-btn.expanded {
            transform: rotate(180deg);
        }
        .theme-settings-section-content {
            display: block;
        }
        .theme-settings-section-content.collapsed {
            display: none;
        }
        .theme-settings-subsection {
            margin-top: 16px;
        }
        .theme-settings-subsection-title {
            font-size: 14px;
            font-weight: 600;
            color: #202223;
            margin: 0 0 8px 0;
        }
        .theme-settings-subsection-description {
            font-size: 13px;
            color: #6d7175;
            margin: 0 0 16px 0;
        }
        .theme-settings-note {
            font-size: 13px;
            color: #6d7175;
            margin: 0 0 16px 0;
        }
        
        /* Color Schemes Grid */
        .color-schemes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .color-scheme-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            position: relative;
        }
        .color-scheme-box:hover {
            border-color: #c9cccf;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .color-scheme-box.add-scheme {
            border: 2px dashed #008060;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 160px;
        }
        .color-scheme-box.add-scheme:hover {
            border-color: #006e52;
            background: #f6fbf9;
        }
        .color-scheme-preview {
            width: 100%;
            height: 120px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            position: relative;
            overflow: hidden;
        }
        .color-scheme-preview.transparent {
            background-image: 
                linear-gradient(45deg, #ccc 25%, transparent 25%),
                linear-gradient(-45deg, #ccc 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #ccc 75%),
                linear-gradient(-45deg, transparent 75%, #ccc 75%);
            background-size: 12px 12px;
            background-position: 0 0, 0 6px, 6px -6px, -6px 0px;
        }
        .color-scheme-preview-text {
            font-size: 32px;
            font-weight: 600;
            line-height: 1;
        }
        .color-scheme-swatches {
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
        }
        .color-scheme-swatch {
            width: 24px;
            height: 16px;
            border-radius: 4px;
            border: 1px solid rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        .color-scheme-label {
            font-size: 13px;
            font-weight: 500;
            color: #202223;
            text-align: center;
        }
        .add-scheme-icon {
            width: 24px;
            height: 24px;
            color: #008060;
            margin-bottom: 8px;
        }
        .add-scheme-label {
            font-size: 13px;
            font-weight: 500;
            color: #008060;
        }
        
        /* Form Design Customizer Styles */
        .form-design-customizer {
            padding: 20px;
        }
        .design-customizer-header {
            margin-bottom: 24px;
        }
        .design-customizer-header h3 {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 8px 0;
            color: #202223;
        }
        .design-customizer-header p {
            font-size: 14px;
            color: #6d7175;
            margin: 0;
        }
        .design-element-selector {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .design-element-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
        }
        .design-element-item:hover {
            border-color: #008060;
            background: #f6fbf9;
        }
        .design-element-item.active {
            border-color: #008060;
            background: #e6f7f2;
        }
        .design-element-item .element-icon {
            font-size: 20px;
            margin-right: 12px;
        }
        .design-element-item .element-label {
            font-size: 14px;
            font-weight: 500;
            color: #202223;
        }
        .design-customizer-panel {
            margin-top: 24px;
            padding: 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
        }
        .design-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        .design-panel-header h4 {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: #202223;
        }
        .design-panel-close {
            background: none;
            border: none;
            font-size: 24px;
            color: #6d7175;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        .design-panel-close:hover {
            color: #202223;
        }
        .design-controls {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .design-control-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .design-control-label {
            font-size: 14px;
            font-weight: 500;
            color: #202223;
        }
        .design-control-input {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .design-control-input input[type="number"],
        .design-control-input input[type="text"],
        .design-control-input select {
            flex: 1;
        }
        .design-control-unit {
            font-size: 14px;
            color: #6d7175;
            min-width: 30px;
        }
        .design-color-picker {
            width: 50px;
            height: 40px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            cursor: pointer;
        }
        .design-color-text {
            flex: 1;
            max-width: 120px;
        }
        .design-control-actions {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .design-control-actions .Polaris-Button {
            flex: 1;
        }
        
        /* Fonts List */
        .fonts-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .font-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .font-item-label {
            font-size: 14px;
            font-weight: 500;
            color: #202223;
            flex: 0 0 120px;
        }
        .font-item-control {
            flex: 1;
            max-width: 300px;
        }
        .font-preview-select {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .font-preview-icon {
            font-size: 16px;
            color: #6d7175;
        }
        
        /* Text Presets List */
        .text-presets-list {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .text-preset-item {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 24px;
        }
        .text-preset-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .text-preset-title {
            font-size: 14px;
            font-weight: 600;
            color: #202223;
            margin-bottom: 16px;
        }
        .text-preset-controls {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .text-preset-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .text-preset-control-label {
            font-size: 14px;
            font-weight: 500;
            color: #202223;
            flex: 0 0 120px;
        }
        .text-preset-control-input {
            flex: 1;
            max-width: 200px;
        }
        .font-toggle-group {
            display: flex;
            gap: 4px;
            background: #f6f6f7;
            border-radius: 6px;
            padding: 2px;
        }
        .font-toggle-btn {
            padding: 6px 12px;
            border: none;
            background: transparent;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            color: #6d7175;
            cursor: pointer;
            transition: all 0.2s;
        }
        .font-toggle-btn.active {
            background: #fff;
            color: #202223;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .case-toggle-group {
            display: flex;
            gap: 4px;
            background: #f6f6f7;
            border-radius: 6px;
            padding: 2px;
        }
        .case-toggle-btn {
            padding: 6px 12px;
            border: none;
            background: transparent;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            color: #6d7175;
            cursor: pointer;
            transition: all 0.2s;
        }
        .case-toggle-btn.active {
            background: #fff;
            color: #202223;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .color-scheme-box.selected {
            border-color: #008060;
            border-width: 2px;
            box-shadow: 0 0 0 2px rgba(0, 128, 96, 0.1);
        }
    </style>
    
        <script>
    // Function to copy Form ID to clipboard
    function copyFormIdToClipboard(formId) {
        // Handle both string and number types
        const formIdText = String(formId);
        
        // Try modern clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(formIdText).then(function() {
                showCopySuccess();
            }).catch(function(err) {
                fallbackCopy(formIdText);
            });
        } else {
            // Fallback for older browsers
            fallbackCopy(formIdText);
        }
    }
    
    function fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess();
            }
        } catch (err) {
            // Silent fail
        }
        
        document.body.removeChild(textArea);
    }
    
    function showCopySuccess() {
        const successMsg = document.getElementById('copy-success-msg');
        if (successMsg) {
            successMsg.style.display = 'inline';
            setTimeout(function() {
                successMsg.style.display = 'none';
            }, 2000);
        }
    }
    
    $(document).ready(function() {
        get_selected_elements(<?php echo $form_id; ?>);
        
        // Load saved design settings after form is loaded (with longer delay)
        setTimeout(function() {
            loadSavedDesignSettings();
        }, 1500); // Longer delay to ensure form preview is loaded
        seeting_enable_disable(<?php echo $form_id; ?>);
        // getFormTitle(<?php echo $form_id; ?>);
        
        
        // Publish Form Functionality
        var selectedPageId = null;
        var selectedPageTitle = null;
        var selectedPageHandle = null;
        var selectedPageType = null; // 'home', 'product', 'collection', 'collections-list', 'page'
        var currentPageNo = 1;
        var searchKeyword = '';
        var allPages = []; // Store all pages for hierarchical display
        
        // Build hierarchical selection structure
        function buildHierarchicalSelection(response) {
            var html = '';
            var pages = [];
            
            // Extract pages from response
            if (Array.isArray(response.html)) {
                if (response.html.length > 0 && typeof response.html[0] === 'object') {
                    pages = response.html;
                } else if (typeof response.html[0] === 'string') {
                    // Parse HTML strings to extract page data
                    response.html.forEach(function(htmlRow) {
                        if (typeof htmlRow === 'string') {
                            var $row = $(htmlRow);
                            var $cells = $row.find('td');
                            if ($cells.length >= 3) {
                                pages.push({
                                    id: $cells.eq(0).text().trim(),
                                    title: $cells.eq(1).text().trim(),
                                    handle: $cells.eq(2).text().trim()
                                });
                            }
                        }
                    });
                }
            } else if (typeof response.html === 'string') {
                // Parse HTML string to extract pages
                var $rows = $(response.html);
                $rows.each(function() {
                    var $cells = $(this).find('td');
                    if ($cells.length >= 3) {
                        pages.push({
                            id: $cells.eq(0).text().trim(),
                            title: $cells.eq(1).text().trim(),
                            handle: $cells.eq(2).text().trim()
                        });
                    }
                });
            }
            
            // Store pages for later use
            allPages = pages;
            
            // Build hierarchical structure
            // 1. Home Page
            html += '<tr class="hierarchical-item" data-page-type="home" data-page-handle="index">';
            html += '<td><strong>Home Page</strong></td>';
            html += '<td style="text-align: right;"><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>';
            html += '</tr>';
            
            // 2. Product
            html += '<tr class="hierarchical-item" data-page-type="product" data-page-handle="product">';
            html += '<td><strong>Product</strong></td>';
            html += '<td style="text-align: right;"><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>';
            html += '</tr>';
            
            // 3. Collections
            html += '<tr class="hierarchical-item" data-page-type="collection" data-page-handle="collection">';
            html += '<td><strong>Collections</strong></td>';
            html += '<td style="text-align: right;"><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>';
            html += '</tr>';
            
            // 4. Collections List
            html += '<tr class="hierarchical-item" data-page-type="collections-list" data-page-handle="collections">';
            html += '<td><strong>Collections List</strong></td>';
            html += '<td style="text-align: right;"><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>';
            html += '</tr>';
            
            // 5. Page (with expandable sub-items)
            html += '<tr class="hierarchical-item page-parent-row" data-page-type="page-parent">';
            html += '<td><strong>Page</strong> <span class="page-toggle-icon" style="margin-left: 10px; cursor: pointer;">▼</span></td>';
            html += '<td></td>';
            html += '</tr>';
            
            // Add scrollable container row for page sub-items only
            html += '<tr class="page-scrollable-container-row">';
            html += '<td colspan="2" style="padding: 0;">';
            html += '<div class="page-sub-items-scrollable" style="max-height: 350px; overflow-y: auto; overflow-x: hidden; background-color: #f9fafb;">';
            html += '<table class="table" style="width: 100%; margin-bottom: 0; background-color: #f9fafb;">';
            html += '<tbody class="page-sub-items-body">';
            
            // Add sub-items for pages (initially visible/expanded by default)
            // Show all pages at once (no pagination)
            if (pages.length > 0) {
                pages.forEach(function(page) {
                    html += '<tr class="page-item-row page-sub-item" data-page-id="' + escapeHtml(page.id) + '" data-page-title="' + escapeHtml(page.title) + '" data-page-handle="' + escapeHtml(page.handle) + '" data-page-type="page" style="background-color: #f9fafb;">';
                    html += '<td style="padding-left: 30px; padding: 5px;">' + escapeHtml(page.title) + '</td>';
                    html += '<td style="text-align: right; padding: 5px;"><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>';
                    html += '</tr>';
                });
            }
            
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
            html += '</td>';
            html += '</tr>';
            
            return html;
        }
        
        // Toggle page sub-items visibility
        $(document).on('click', '.page-parent-row, .page-toggle-icon', function(e) {
            e.stopPropagation();
            var $scrollableContainer = $('.page-scrollable-container-row');
            var $icon = $('.page-toggle-icon');
            
            if ($scrollableContainer.is(':visible')) {
                $scrollableContainer.hide();
                $icon.text('▶');
            } else {
                $scrollableContainer.show();
                $icon.text('▼');
            }
        });
        
        // Open page selection modal
        $(document).on('click', '.publish-form-btn', function() {
            $('#publishPageModal').show();
            loadStorePages(1, '');
        });
        
        // Close page selection modal
        $(document).on('click', '.closePageModal', function() {
            $('#publishPageModal').hide();
            selectedPageId = null;
            selectedPageTitle = null;
            selectedPageHandle = null;
        });
        
        // Close confirm modal
        $(document).on('click', '.closeConfirmModal', function() {
            $('#publishConfirmModal').hide();
        });
        
        // Search pages
        var searchTimeout;
        $('#pageSearchInput').on('keyup', function() {
            clearTimeout(searchTimeout);
            var keyword = $(this).val();
            searchTimeout = setTimeout(function() {
                searchKeyword = keyword;
                loadStorePages(1, keyword);
            }, 500);
        });
        
        // Load store pages from Shopify API (using GraphQL)
        function loadStorePages(pageNo, searchKeyword, cursor) {
            currentPageNo = pageNo;
            
            $('#pagesListBody').html('<tr><td colspan="2" style="text-align: center; padding: 20px;"><div class="Polaris-Spinner Polaris-Spinner--sizeSmall"></div><span style="margin-left: 10px;">Loading pages...</span></td></tr>');
            
            var ajaxData = {
                routine_name: 'take_api_shopify_data',
                shopify_api: 'pages',
                store: store,
                limit: 250, // Load all pages at once (Shopify max is 250)
                pageno: pageNo,
                listing_id: 'pagesData',
                pagination_method: 'pagination'
            };
            
            // Add cursor for GraphQL pagination
            if (cursor) {
                ajaxData.cursor = cursor;
            }
            
            $.ajax({
                url: "ajax_call.php",
                type: "POST",
                dataType: "json",
                data: ajaxData,
                success: function(response) {
                    if (response['code'] != undefined && response['code'] == '403') {
                        redirect403();
                        return;
                    }
                    
                    if (response.outcome == 'true' || response.outcome === 'true') {
                        // Store API response for later use
                        window.lastAPIResponse = {
                            hasNextPage: response.hasNextPage,
                            endCursor: response.endCursor,
                            pageNo: pageNo
                        };
                        
                        // Build hierarchical selection structure
                        var html = buildHierarchicalSelection(response);
                        
                        if (html) {
                            $('#pagesListBody').html(html);
                        } else {
                            $('#pagesListBody').html('<tr><td colspan="2" style="text-align: center; padding: 20px;">No pages found.</td></tr>');
                        }
                        
                        // No pagination needed - all pages loaded at once
                        $('#pagesPagination').html('');
                    } else {
                        var errorMsg = 'No pages found.';
                        if (response.report) {
                            errorMsg = response.report;
                        }
                        
                        $('#pagesListBody').html('<tr><td colspan="2" style="text-align: center; padding: 20px;">' + errorMsg + '</td></tr>');
                        $('#pagesPagination').html('');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = 'Error loading pages. Please try again.';
                    if (xhr.responseText) {
                        try {
                            var errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.report) {
                                errorMsg = errorResponse.report;
                            }
                        } catch (e) {
                            // Silent fail
                        }
                    }
                    
                    $('#pagesListBody').html('<tr><td colspan="2" style="text-align: center; padding: 20px; color: #dc2626;">' + errorMsg + '</td></tr>');
                }
            });
        }
        
        // Handle pagination clicks (for REST API)
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            var pageNo = $(this).data('page') || $(this).attr('data-page');
            if (pageNo) {
                loadStorePages(pageNo, searchKeyword);
            }
        });
        
        // Handle "Load More" for GraphQL cursor-based pagination
        $(document).on('click', '.loadMorePages', function(e) {
            e.preventDefault();
            var loadType = $(this).data('load-type'); // 'api' or 'local'
            var cursor = $(this).data('cursor');
            var pageNo = $(this).data('page') || currentPageNo + 1;
            
            // Check if we should load from local remaining pages first
            if (loadType === 'local' && window.remainingPagesForLoadMore && window.remainingPagesForLoadMore.length > 0) {
                // Load next 10 pages from local storage
                var pagesToLoad = window.remainingPagesForLoadMore.slice(0, 10);
                window.remainingPagesForLoadMore = window.remainingPagesForLoadMore.slice(10);
                
                // Append pages to the list (make them visible if Page section is expanded)
                pagesToLoad.forEach(function(page) {
                    var pageRow = '<tr class="page-item-row page-sub-item" data-page-id="' + escapeHtml(page.id) + '" data-page-title="' + escapeHtml(page.title) + '" data-page-handle="' + escapeHtml(page.handle) + '" data-page-type="page" style="background-color: #f9fafb;">';
                    pageRow += '<td style="padding-left: 30px;">' + escapeHtml(page.title) + '</td>';
                    pageRow += '<td style="text-align: right;"><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>';
                    pageRow += '</tr>';
                    $('#pagesListBody').append(pageRow);
                });
                
                // Update pagination - check if there are more local pages or API pages
                var hasMoreLocalPages = window.remainingPagesForLoadMore && window.remainingPagesForLoadMore.length > 0;
                var hasMoreAPIPages = false;
                
                // Check last API response info
                if (window.lastAPIResponse) {
                    if (window.lastAPIResponse.hasNextPage === true || window.lastAPIResponse.hasNextPage === 'true') {
                        if (window.lastAPIResponse.endCursor && window.lastAPIResponse.endCursor !== null && window.lastAPIResponse.endCursor !== '' && window.lastAPIResponse.endCursor !== 'null') {
                            hasMoreAPIPages = true;
                        }
                    }
                }
                
                if (hasMoreLocalPages || hasMoreAPIPages) {
                    var paginationHtml = '<div class="pagination" style="margin-top: 20px; text-align: center;">';
                    if (hasMoreLocalPages) {
                        paginationHtml += '<button class="Polaris-Button Polaris-Button--primary loadMorePages" type="button" data-load-type="local">';
                    } else if (hasMoreAPIPages) {
                        paginationHtml += '<button class="Polaris-Button Polaris-Button--primary loadMorePages" type="button" data-cursor="' + escapeHtml(window.lastAPIResponse.endCursor) + '" data-page="' + (parseInt(window.lastAPIResponse.pageNo || 1) + 1) + '" data-load-type="api">';
                    }
                    paginationHtml += '<span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Load More</span></span>';
                    paginationHtml += '</button>';
                    paginationHtml += '</div>';
                    $('#pagesPagination').html(paginationHtml);
                } else {
                    $('#pagesPagination').html('');
                }
                return;
            }
            
            // Load from API
            if (cursor) {
                // Append new pages instead of replacing
                var $loadingRow = $('<tr><td colspan="2" style="text-align: center; padding: 20px;"><div class="Polaris-Spinner Polaris-Spinner--sizeSmall"></div><span style="margin-left: 10px;">Loading more pages...</span></td></tr>');
                $('#pagesListBody').append($loadingRow);
                
                $.ajax({
                    url: "ajax_call.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        routine_name: 'take_api_shopify_data',
                        shopify_api: 'pages',
                        store: store,
                        limit: 10,
                        pageno: pageNo,
                        cursor: cursor,
                        listing_id: 'pagesData',
                        pagination_method: 'pagination'
                    },
                    success: function(response) {
                        $loadingRow.remove();
                        
                        if (response['code'] != undefined && response['code'] == '403') {
                            redirect403();
                            return;
                        }
                        
                        if (response.outcome == 'true' && response.html && response.html.length > 0) {
                            // Format and append new pages to existing list (under Page sub-items)
                            var pages = [];
                            
                            // Extract pages from response
                            if (Array.isArray(response.html)) {
                                if (response.html.length > 0 && typeof response.html[0] === 'object') {
                                    pages = response.html;
                                } else if (typeof response.html[0] === 'string') {
                                    // Parse HTML strings to extract page data
                                    response.html.forEach(function(htmlRow) {
                                        if (typeof htmlRow === 'string') {
                                            var $row = $(htmlRow);
                                            var $cells = $row.find('td');
                                            if ($cells.length >= 3) {
                                                pages.push({
                                                    id: $cells.eq(0).text().trim(),
                                                    title: $cells.eq(1).text().trim(),
                                                    handle: $cells.eq(2).text().trim()
                                                });
                                            } else if ($cells.length >= 2) {
                                                // Handle case where we might have different structure
                                                var pageId = $row.data('page-id') || $cells.eq(0).text().trim();
                                                var pageTitle = $row.data('page-title') || $cells.eq(0).text().trim();
                                                var pageHandle = $row.data('page-handle') || '';
                                                pages.push({
                                                    id: pageId,
                                                    title: pageTitle,
                                                    handle: pageHandle
                                                });
                                            }
                                        }
                                    });
                                }
                            } else if (typeof response.html === 'string') {
                                // Parse HTML string to extract pages
                                var $rows = $(response.html);
                                $rows.each(function() {
                                    var $row = $(this);
                                    var $cells = $row.find('td');
                                    if ($cells.length >= 3) {
                                        pages.push({
                                            id: $cells.eq(0).text().trim(),
                                            title: $cells.eq(1).text().trim(),
                                            handle: $cells.eq(2).text().trim()
                                        });
                                    } else if ($cells.length >= 2) {
                                        var pageId = $row.data('page-id') || $cells.eq(0).text().trim();
                                        var pageTitle = $row.data('page-title') || $cells.eq(0).text().trim();
                                        var pageHandle = $row.data('page-handle') || '';
                                        pages.push({
                                            id: pageId,
                                            title: pageTitle,
                                            handle: pageHandle
                                        });
                                    }
                                });
                            }
                            
                            // Format pages with 2 columns and append as sub-items
                            pages.forEach(function(page) {
                                var pageRow = '<tr class="page-item-row page-sub-item" data-page-id="' + escapeHtml(page.id) + '" data-page-title="' + escapeHtml(page.title) + '" data-page-handle="' + escapeHtml(page.handle) + '" data-page-type="page" style="background-color: #f9fafb;">';
                                pageRow += '<td style="padding-left: 30px;">' + escapeHtml(page.title) + '</td>';
                                pageRow += '<td style="text-align: right;"><button class="Polaris-Button Polaris-Button--primary selectPageBtn" type="button" style="padding: 4px 12px; font-size: 12px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select</span></span></button></td>';
                                pageRow += '</tr>';
                                $('#pagesListBody').append(pageRow);
                            });
                            
                            // Update pagination - only show "Load More" if there are more pages
                            var hasMorePages = false;
                            if (response.hasNextPage === true || response.hasNextPage === 'true') {
                                if (response.endCursor && response.endCursor !== null && response.endCursor !== '' && response.endCursor !== 'null') {
                                    if (pages.length > 0) {
                                        hasMorePages = true;
                                    }
                                }
                            }
                            
                            if (hasMorePages) {
                                var paginationHtml = '<div class="pagination" style="margin-top: 20px; text-align: center;">';
                                paginationHtml += '<button class="Polaris-Button Polaris-Button--primary loadMorePages" type="button" data-cursor="' + escapeHtml(response.endCursor) + '" data-page="' + (parseInt(pageNo) + 1) + '" data-load-type="api">';
                                paginationHtml += '<span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Load More</span></span>';
                                paginationHtml += '</button>';
                                paginationHtml += '</div>';
                                $('#pagesPagination').html(paginationHtml);
                            } else {
                                // All pages loaded - hide the Load More button
                                $('#pagesPagination').html('');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        $loadingRow.remove();
                    }
                });
            }
        });
        
        // Helper function to initialize theme settings UI
        function initializeThemeSettings() {
            // Show loader and hide content initially
            $('#themeSettingsLoader').show();
            $('#themeSettingsContent').hide();
            
            // Initialize sections as expanded (will be used when content loads)
            $('.theme-settings-expand-btn').each(function() {
                var $btn = $(this);
                var section = $btn.data('section');
                var $content = $('#' + section + 'SectionContent');
                if ($content.length) {
                    $content.removeClass('collapsed');
                    $btn.addClass('expanded');
                }
            });
            
            // Load theme settings from API
            loadThemeSettingsFromAPI();
        }
        
        // Handle Theme Settings click - initialize when carousel navigates to slide 15
        $(document).on('click', '.theme-settings-item', function() {
            setTimeout(function() {
                initializeThemeSettings();
            }, 300);
        });
        
        // Listen for carousel change events to initialize theme settings when slide 15 is shown
        $('.owl-carousel').on('changed.owl.carousel', function(event) {
            var currentSlide = event.item.index;
            var $currentSlide = $(event.target).find('.owl-item').eq(currentSlide);
            if ($currentSlide.find('.theme-settings-content').length > 0) {
                initializeThemeSettings();
            }
        });
        
        // Function to load theme settings from API
        function loadThemeSettingsFromAPI() {
            // Ensure loader is visible and content is hidden
            $('#themeSettingsLoader').show();
            $('#themeSettingsContent').hide();
            
            $.ajax({
                url: "ajax_call.php",
                type: "POST",
                dataType: "json",
                data: {
                    routine_name: 'take_api_shopify_data',
                    shopify_api: 'theme_settings',
                    store: store
                },
                success: function(response) {
                    // Hide loader
                    $('#themeSettingsLoader').hide();
                    
                    if (response['code'] != undefined && response['code'] == '403') {
                        redirect403();
                        return;
                    }
                    
                    if (response.outcome == 'true' || response.outcome === 'true') {
                        // Show content and display theme settings
                        $('#themeSettingsContent').show();
                        displayThemeSettings(
                            response.color_schemes || [],
                            response.colors || [],
                            response.typography || [],
                            response.text_presets || []
                        );
                    } else {
                        // Show content even on error (it will be empty)
                        $('#themeSettingsContent').show();
                    }
                },
                error: function(xhr, status, error) {
                    // Hide loader and show content (will be empty)
                    $('#themeSettingsLoader').hide();
                    $('#themeSettingsContent').show();
                }
            });
        }
        
        // Function to display theme settings
        function displayThemeSettings(colorSchemes, colors, typography, textPresets) {
            // Use API color schemes only - no fallback to defaults
            var schemesToDisplay = [];
            if (colorSchemes && colorSchemes.length > 0) {
                // Use real data from API
                schemesToDisplay = colorSchemes.map(function(scheme, index) {
                    return {
                        id: scheme.id || (index + 1),
                        bg: scheme.bg || '#ffffff',
                        text: scheme.text || '#000000',
                        swatch1: scheme.swatch1 || scheme.text || '#000000',
                        swatch2: scheme.swatch2 || scheme.bg || '#ffffff'
                    };
                });
            }
            
            // Generate color schemes HTML
            var colorsHtml = '';
            if (schemesToDisplay.length === 0) {
                colorsHtml = '<div style="padding: 20px; text-align: center; color: #666;">No color schemes available</div>';
            } else {
                schemesToDisplay.forEach(function(scheme) {
                    var bgStyle = scheme.bg === 'transparent' ? 'background: transparent;' : 'background-color: ' + scheme.bg + ';';
                    var transparentClass = scheme.bg === 'transparent' ? ' transparent' : '';
                    colorsHtml += '<div class="color-scheme-box" data-scheme-id="' + scheme.id + '">';
                    colorsHtml += '<div class="color-scheme-preview' + transparentClass + '" style="' + bgStyle + '">';
                    colorsHtml += '<div class="color-scheme-preview-text" style="color: ' + scheme.text + ';">Aa</div>';
                        colorsHtml += '</div>';
                    colorsHtml += '<div class="color-scheme-swatches">';
                    colorsHtml += '<div class="color-scheme-swatch" style="background-color: ' + scheme.swatch1 + ';"></div>';
                    colorsHtml += '<div class="color-scheme-swatch" style="background-color: ' + scheme.swatch2 + ';"></div>';
                        colorsHtml += '</div>';
                    colorsHtml += '<div class="color-scheme-label">Scheme ' + scheme.id + '</div>';
                        colorsHtml += '</div>';
                    });
                // Add Scheme button (only show if we have schemes)
                colorsHtml += '<div class="color-scheme-box add-scheme">';
                colorsHtml += '<svg class="add-scheme-icon" viewBox="0 0 20 20" fill="currentColor">';
                colorsHtml += '<path d="M10 4a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V5a1 1 0 011-1z"></path>';
                colorsHtml += '</svg>';
                colorsHtml += '<div class="add-scheme-label">Add Scheme</div>';
                colorsHtml += '</div>';
            }
            
            $('#colorSchemaContainer').html(colorsHtml);
            
            // Use API typography only - no fallback to defaults
            var fontsToDisplay = typography && typography.length > 0 ? typography : [];
            
            // Common font list (for dropdowns) - collect unique fonts from API data
            var fontList = ['Newsreader', 'Red Hat Display', 'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Playfair Display', 'Merriweather'];
            if (typography && typography.length > 0) {
                typography.forEach(function(font) {
                    if (font.value && fontList.indexOf(font.value) === -1) {
                        fontList.push(font.value);
                    }
                });
            }
            
            // Generate fonts HTML
            var fontsHtml = '';
            if (fontsToDisplay.length === 0) {
                fontsHtml = '<div style="padding: 20px; text-align: center; color: #666;">No fonts available</div>';
            } else {
                fontsToDisplay.forEach(function(font) {
                    fontsHtml += '<div class="font-item">';
                    fontsHtml += '<div class="font-item-label">' + escapeHtml(font.label) + '</div>';
                    fontsHtml += '<div class="font-item-control">';
                    fontsHtml += '<div class="Polaris-Select">';
                    fontsHtml += '<select class="Polaris-Select__Input font-select" data-font-type="' + font.id + '">';
                    fontList.forEach(function(fontOption) {
                        var selected = fontOption === font.value ? 'selected' : '';
                        fontsHtml += '<option value="' + escapeHtml(fontOption) + '" ' + selected + ' style="font-family: ' + escapeHtml(fontOption) + ';">' + escapeHtml(fontOption) + '</option>';
                    });
                    fontsHtml += '</select>';
                    fontsHtml += '<div class="Polaris-Select__Content" aria-hidden="true">';
                    fontsHtml += '<span class="Polaris-Select__SelectedOption">' + escapeHtml(font.value) + '</span>';
                    fontsHtml += '<span class="Polaris-Select__Icon">';
                    fontsHtml += '<svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor">';
                    fontsHtml += '<path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>';
                    fontsHtml += '</svg>';
                    fontsHtml += '</span>';
                    fontsHtml += '</div>';
                    fontsHtml += '<div class="Polaris-Select__Backdrop"></div>';
                    fontsHtml += '</div>';
                    fontsHtml += '</div>';
                    fontsHtml += '</div>';
                });
            }
            $('#fontsContainer').html(fontsHtml);
            
            // Generate text presets HTML
            var typographyHtml = '';
            
            // Check if we have any text presets
            if (!textPresets || textPresets.length === 0) {
                typographyHtml = '<div style="padding: 20px; text-align: center; color: #666;">No text presets available</div>';
                $('#typographyContainer').html(typographyHtml);
                return; // Early return if no data
            }
            
            // Group text presets by type (paragraph, heading, etc.)
            var paragraphPresets = textPresets ? textPresets.filter(function(p) { 
                return p.label.toLowerCase().indexOf('paragraph') !== -1 || p.id.toLowerCase().indexOf('paragraph') !== -1; 
            }) : [];
            var headingPresets = textPresets ? textPresets.filter(function(p) { 
                return p.label.toLowerCase().indexOf('heading') !== -1 || p.id.toLowerCase().indexOf('heading') !== -1; 
            }) : [];
            
            // Get paragraph size and line height from API only
            var paragraphSize = '';
            var paragraphLineHeight = '';
            if (paragraphPresets.length > 0) {
                var sizePreset = paragraphPresets.find(function(p) { return p.label.toLowerCase().indexOf('size') !== -1 || p.id.toLowerCase().indexOf('size') !== -1; });
                var lineHeightPreset = paragraphPresets.find(function(p) { return p.label.toLowerCase().indexOf('line') !== -1 || p.id.toLowerCase().indexOf('line') !== -1; });
                if (sizePreset && sizePreset.value) paragraphSize = sizePreset.value;
                if (lineHeightPreset && lineHeightPreset.value) paragraphLineHeight = lineHeightPreset.value;
            }
            
            // Paragraph preset
            typographyHtml += '<div class="text-preset-item" data-preset="paragraph">';
            typographyHtml += '<div class="text-preset-title">Paragraph</div>';
            typographyHtml += '<div class="text-preset-controls">';
            
            typographyHtml += '<div class="text-preset-control">';
            typographyHtml += '<div class="text-preset-control-label">Size</div>';
            typographyHtml += '<div class="text-preset-control-input">';
            typographyHtml += '<div class="Polaris-TextField Polaris-TextField--hasValue">';
            typographyHtml += '<input type="number" class="Polaris-TextField__Input text-preset-size" data-preset="paragraph" value="' + escapeHtml(paragraphSize) + '">';
            typographyHtml += '<div class="Polaris-TextField__Spinner" aria-hidden="true">';
            typographyHtml += '<div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z"></path></svg>';
            typographyHtml += '</div></div><div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path></svg>';
            typographyHtml += '</div></div></div>';
            typographyHtml += '<div class="Polaris-TextField__Backdrop"></div>';
            typographyHtml += '</div></div></div>';
            
            typographyHtml += '<div class="text-preset-control">';
            typographyHtml += '<div class="text-preset-control-label">Line height</div>';
            typographyHtml += '<div class="text-preset-control-input">';
            typographyHtml += '<div class="Polaris-TextField Polaris-TextField--hasValue">';
            typographyHtml += '<input type="text" class="Polaris-TextField__Input text-preset-line-height" data-preset="paragraph" value="' + escapeHtml(paragraphLineHeight) + '">';
            typographyHtml += '<div class="Polaris-TextField__Spinner" aria-hidden="true">';
            typographyHtml += '<div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z"></path></svg>';
            typographyHtml += '</div></div><div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path></svg>';
            typographyHtml += '</div></div></div>';
            typographyHtml += '<div class="Polaris-TextField__Backdrop"></div>';
            typographyHtml += '</div></div></div>';
            
            typographyHtml += '</div></div>';
            
            // Get heading 1 presets from API only
            var heading1Size = '';
            var heading1LineHeight = '';
            var heading1LetterSpacing = '';
            if (headingPresets.length > 0) {
                var h1SizePreset = headingPresets.find(function(p) { return (p.label.toLowerCase().indexOf('size') !== -1 || p.id.toLowerCase().indexOf('size') !== -1) && (p.label.toLowerCase().indexOf('1') !== -1 || p.id.toLowerCase().indexOf('1') !== -1 || p.id.toLowerCase().indexOf('h1') !== -1); });
                var h1LineHeightPreset = headingPresets.find(function(p) { return (p.label.toLowerCase().indexOf('line') !== -1 || p.id.toLowerCase().indexOf('line') !== -1) && (p.label.toLowerCase().indexOf('1') !== -1 || p.id.toLowerCase().indexOf('1') !== -1 || p.id.toLowerCase().indexOf('h1') !== -1); });
                var h1LetterSpacingPreset = headingPresets.find(function(p) { return (p.label.toLowerCase().indexOf('letter') !== -1 || p.id.toLowerCase().indexOf('letter') !== -1) && (p.label.toLowerCase().indexOf('1') !== -1 || p.id.toLowerCase().indexOf('1') !== -1 || p.id.toLowerCase().indexOf('h1') !== -1); });
                if (h1SizePreset && h1SizePreset.value) heading1Size = h1SizePreset.value;
                if (h1LineHeightPreset && h1LineHeightPreset.value) heading1LineHeight = h1LineHeightPreset.value;
                if (h1LetterSpacingPreset && h1LetterSpacingPreset.value) heading1LetterSpacing = h1LetterSpacingPreset.value;
            }
            
            // Heading 1 preset
            typographyHtml += '<div class="text-preset-item" data-preset="heading1">';
            typographyHtml += '<div class="text-preset-title">Heading 1</div>';
            typographyHtml += '<div class="text-preset-controls">';
            
            typographyHtml += '<div class="text-preset-control">';
            typographyHtml += '<div class="text-preset-control-label">Font</div>';
            typographyHtml += '<div class="text-preset-control-input">';
            typographyHtml += '<div class="font-toggle-group">';
            typographyHtml += '<button type="button" class="font-toggle-btn active" data-font-type="heading">Heading</button>';
            typographyHtml += '<button type="button" class="font-toggle-btn" data-font-type="accent">Accent</button>';
            typographyHtml += '</div></div></div>';
            
            typographyHtml += '<div class="text-preset-control">';
            typographyHtml += '<div class="text-preset-control-label">Size</div>';
            typographyHtml += '<div class="text-preset-control-input">';
            typographyHtml += '<div class="Polaris-TextField Polaris-TextField--hasValue">';
            typographyHtml += '<input type="number" class="Polaris-TextField__Input text-preset-size" data-preset="heading1" value="' + escapeHtml(heading1Size) + '">';
            typographyHtml += '<div class="Polaris-TextField__Spinner" aria-hidden="true">';
            typographyHtml += '<div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z"></path></svg>';
            typographyHtml += '</div></div><div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path></svg>';
            typographyHtml += '</div></div></div>';
            typographyHtml += '<div class="Polaris-TextField__Backdrop"></div>';
            typographyHtml += '</div></div></div>';
            
            typographyHtml += '<div class="text-preset-control">';
            typographyHtml += '<div class="text-preset-control-label">Line height</div>';
            typographyHtml += '<div class="text-preset-control-input">';
            typographyHtml += '<div class="Polaris-TextField Polaris-TextField--hasValue">';
            typographyHtml += '<input type="text" class="Polaris-TextField__Input text-preset-line-height" data-preset="heading1" value="' + escapeHtml(heading1LineHeight) + '">';
            typographyHtml += '<div class="Polaris-TextField__Spinner" aria-hidden="true">';
            typographyHtml += '<div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z"></path></svg>';
            typographyHtml += '</div></div><div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path></svg>';
            typographyHtml += '</div></div></div>';
            typographyHtml += '<div class="Polaris-TextField__Backdrop"></div>';
            typographyHtml += '</div></div></div>';
            
            typographyHtml += '<div class="text-preset-control">';
            typographyHtml += '<div class="text-preset-control-label">Letter spacing</div>';
            typographyHtml += '<div class="text-preset-control-input">';
            typographyHtml += '<div class="Polaris-TextField Polaris-TextField--hasValue">';
            typographyHtml += '<input type="text" class="Polaris-TextField__Input text-preset-letter-spacing" data-preset="heading1" value="' + escapeHtml(heading1LetterSpacing) + '">';
            typographyHtml += '<div class="Polaris-TextField__Spinner" aria-hidden="true">';
            typographyHtml += '<div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M6.902 12h6.196c.751 0 1.172-.754.708-1.268l-3.098-3.432c-.36-.399-1.055-.399-1.416 0l-3.098 3.433c-.464.513-.043 1.267.708 1.267Z"></path></svg>';
            typographyHtml += '</div></div><div role="button" class="Polaris-TextField__Segment" tabindex="-1"><div class="Polaris-TextField__SpinnerIcon">';
            typographyHtml += '<svg viewBox="0 0 20 20" width="12" height="12"><path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path></svg>';
            typographyHtml += '</div></div></div>';
            typographyHtml += '<div class="Polaris-TextField__Backdrop"></div>';
            typographyHtml += '</div></div></div>';
            
            typographyHtml += '<div class="text-preset-control">';
            typographyHtml += '<div class="text-preset-control-label">Case</div>';
            typographyHtml += '<div class="text-preset-control-input">';
            typographyHtml += '<div class="case-toggle-group">';
            typographyHtml += '<button type="button" class="case-toggle-btn active" data-case="default">Default</button>';
            typographyHtml += '<button type="button" class="case-toggle-btn" data-case="uppercase">Uppercase</button>';
            typographyHtml += '</div></div></div>';
            
            typographyHtml += '</div></div>';
            
            $('#typographyContainer').html(typographyHtml);
        }
        
        // Handle back button for theme settings
        $(document).on('click', '.theme-settings-content .backBtn', function() {
            $('.theme-settings-content').hide();
        });
        
        // Handle expand/collapse for theme settings sections
        $(document).on('click', '.theme-settings-expand-btn', function() {
            var $btn = $(this);
            var section = $btn.data('section');
            var $content = $('#' + section + 'SectionContent');
            var isExpanded = !$content.hasClass('collapsed');
            
            if (isExpanded) {
                $content.addClass('collapsed');
                $btn.removeClass('expanded');
            } else {
                $content.removeClass('collapsed');
                $btn.addClass('expanded');
            }
        });
        
        // Handle font select changes
        $(document).on('change', '.font-select', function() {
            var $select = $(this);
            var fontType = $select.data('font-type');
            var selectedFont = $select.val();
            var $selectedOption = $select.closest('.Polaris-Select').find('.Polaris-Select__SelectedOption');
            $selectedOption.text(selectedFont);
            // Here you would typically save the font selection via AJAX
        });
        
        // Handle font toggle buttons (Heading/Accent)
        $(document).on('click', '.font-toggle-btn', function() {
            var $btn = $(this);
            var $group = $btn.closest('.font-toggle-group');
            $group.find('.font-toggle-btn').removeClass('active');
            $btn.addClass('active');
            var fontType = $btn.data('font-type');
            // Here you would typically update the font selection based on the toggle
        });
        
        // Handle case toggle buttons (Default/Uppercase)
        $(document).on('click', '.case-toggle-btn', function() {
            var $btn = $(this);
            var $group = $btn.closest('.case-toggle-group');
            $group.find('.case-toggle-btn').removeClass('active');
            $btn.addClass('active');
            var caseType = $btn.data('case');
            // Here you would typically save the case setting via AJAX
        });
        
        // ===== Form Design Customizer (Integrated into Element Panels) =====
        // Sync color picker and text input (using event delegation for dynamically loaded elements)
        $(document).on('change', '.element-design-color', function() {
            var $picker = $(this);
            var formdataid = $picker.data('formdataid');
            var $textInput = $('.element-design-color-text[data-formdataid="' + formdataid + '"]');
            $textInput.val($picker.val());
            updateElementDesignPreview(formdataid);
        });
        
        $(document).on('input', '.element-design-color-text', function() {
            var $textInput = $(this);
            var color = $textInput.val();
            var formdataid = $textInput.data('formdataid');
            if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
                var $picker = $('.element-design-color[data-formdataid="' + formdataid + '"]');
                $picker.val(color);
                updateElementDesignPreview(formdataid);
            }
        });
        
        $(document).on('change', '.element-design-bg-color', function() {
            var $picker = $(this);
            var formdataid = $picker.data('formdataid');
            var $textInput = $('.element-design-bg-color-text[data-formdataid="' + formdataid + '"]');
            $textInput.val($picker.val());
            updateElementDesignPreview(formdataid);
        });
        
        $(document).on('input', '.element-design-bg-color-text', function() {
            var $textInput = $(this);
            var color = $textInput.val();
            var formdataid = $textInput.data('formdataid');
            if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
                var $picker = $('.element-design-bg-color[data-formdataid="' + formdataid + '"]');
                $picker.val(color);
                updateElementDesignPreview(formdataid);
            }
        });
        
        // Update preview on design control changes (real-time preview)
        // Function to update element design preview in real-time (global function)
        window.updateElementDesignPreview = function(formdataid) {
            if (!formdataid) return;
            
            // Get design settings
            var fontSize = parseInt($('.element-design-font-size[data-formdataid="' + formdataid + '"]').val()) || 16;
            var fontWeight = $('.element-design-font-weight[data-formdataid="' + formdataid + '"]').val() || '400';
            var color = $('.element-design-color-text[data-formdataid="' + formdataid + '"]').val() || '#000000';
            var borderRadiusVal = $('.element-design-border-radius[data-formdataid="' + formdataid + '"]').val();
            var borderRadius = (borderRadiusVal !== '' && borderRadiusVal !== null && borderRadiusVal !== undefined) ? parseInt(borderRadiusVal) : 4;
            if (isNaN(borderRadius) || borderRadius < 0) {
                borderRadius = 4;
            }
            
            // Apply to input, textarea, select elements in both preview containers
            $('.code-form-app .code-form-control[data-formdataid="' + formdataid + '"] .classic-input, .contact-form .code-form-control[data-formdataid="' + formdataid + '"] .classic-input').css({
                'font-size': fontSize + 'px',
                'font-weight': fontWeight,
                'color': color,
                'border-radius': borderRadius + 'px'
            });
            
            // Apply to file upload elements (upload-area and file_button) in both preview containers
            // IMPORTANT: Use !important to override any CSS that might be conflicting
            // Try multiple selector strategies to find the upload-area
            var $uploadArea = $('.code-form-app .globo-form-input[data-formdataid="' + formdataid + '"] .upload-area, .contact-form .globo-form-input[data-formdataid="' + formdataid + '"] .upload-area');
            if ($uploadArea.length === 0) {
                // Try finding through code-form-control
                $uploadArea = $('.code-form-app .code-form-control[data-formdataid="' + formdataid + '"] .upload-area, .contact-form .code-form-control[data-formdataid="' + formdataid + '"] .upload-area');
            }
            if ($uploadArea.length === 0) {
                // Fallback: find any upload-area and check if it's inside the right element
                $('.code-form-app .upload-area, .contact-form .upload-area').each(function() {
                    var $this = $(this);
                    var $parent = $this.closest('[data-formdataid="' + formdataid + '"]');
                    if ($parent.length) {
                        $uploadArea = $uploadArea.add($this);
                    }
                });
            }
            if ($uploadArea.length) {
                // Use attr to add !important to override any CSS
                $uploadArea.each(function() {
                    var $this = $(this);
                    var currentStyle = $this.attr('style') || '';
                    // Remove existing border-radius if any (with or without !important)
                    currentStyle = currentStyle.replace(/border-radius\s*:\s*[^;]+!important;?/gi, '');
                    currentStyle = currentStyle.replace(/border-radius\s*:\s*[^;]+;?/gi, '');
                    // Add new border-radius with !important
                    currentStyle += ' border-radius: ' + borderRadius + 'px !important;';
                    $this.attr('style', currentStyle.trim());
                });
            } else {
                // Debug: log if element not found
                console.log('Upload area not found for formdataid:', formdataid);
                console.log('Available upload-areas:', $('.code-form-app .upload-area, .contact-form .upload-area').length);
            }
            
            var $fileButton = $('.code-form-app .globo-form-input[data-formdataid="' + formdataid + '"] .file_button, .contact-form .globo-form-input[data-formdataid="' + formdataid + '"] .file_button');
            if ($fileButton.length === 0) {
                $fileButton = $('.code-form-app .code-form-control[data-formdataid="' + formdataid + '"] .file_button, .contact-form .code-form-control[data-formdataid="' + formdataid + '"] .file_button');
            }
            if ($fileButton.length === 0) {
                $('.code-form-app .file_button, .contact-form .file_button').each(function() {
                    var $this = $(this);
                    var $parent = $this.closest('[data-formdataid="' + formdataid + '"]');
                    if ($parent.length) {
                        $fileButton = $fileButton.add($this);
                    }
                });
            }
            if ($fileButton.length) {
                $fileButton.css({
                    'font-size': fontSize + 'px',
                    'font-weight': fontWeight,
                    'color': color,
                    'border-radius': borderRadius + 'px'
                });
            }
        };
        
        $(document).on('input change keyup', '.element-design-font-size, .element-design-font-weight, .element-design-color-text, .element-design-border-radius', function() {
            var $control = $(this);
            var formdataid = $control.data('formdataid');
            if (formdataid) {
                // Call global function directly for immediate update
                if (typeof window.updateElementDesignPreview === 'function') {
                    window.updateElementDesignPreview(formdataid);
                } else if (typeof updateElementDesignPreview === 'function') {
                    updateElementDesignPreview(formdataid);
                }
            }
        });
        
        // Function to update footer button preview in real-time (make it global)
        window.updateFooterButtonPreview = function() {
            // Read button design settings
            var buttonTextSize = parseInt($('.footer-design-button-text-size').val()) || 16;
            var buttonTextColor = $('.footer-design-button-text-color-text').val() || $('.footer-design-button-text-color').val() || '#ffffff';
            var buttonBgColor = $('.footer-design-button-bg-color-text').val() || $('.footer-design-button-bg-color').val() || '#EB1256';
            var buttonHoverBgColor = $('.footer-design-button-hover-bg-color-text').val() || $('.footer-design-button-hover-bg-color').val() || '#C8104A';
            var borderRadius = parseInt($('.footer-design-button-border-radius').val()) || 4;
            
            // Validate color formats
            if (!/^#[0-9A-Fa-f]{6}$/i.test(buttonTextColor)) {
                buttonTextColor = '#ffffff';
            }
            if (!/^#[0-9A-Fa-f]{6}$/i.test(buttonBgColor)) {
                buttonBgColor = '#EB1256';
            }
            if (!/^#[0-9A-Fa-f]{6}$/i.test(buttonHoverBgColor)) {
                buttonHoverBgColor = '#C8104A';
            }
            
            // Calculate padding based on font size for dynamic button sizing
            // Padding should scale proportionally: larger font = more padding
            // Base padding ratio: for 16px font, use ~12px vertical and ~24px horizontal
            var verticalPadding = Math.max(8, Math.round(buttonTextSize * 0.75)); // 75% of font size, minimum 8px
            var horizontalPadding = Math.max(16, Math.round(buttonTextSize * 1.5)); // 150% of font size, minimum 16px
            
            // Apply to submit button
            $('.footer .action.submit.classic-button').css({
                'font-size': buttonTextSize + 'px',
                'color': buttonTextColor,
                'background-color': buttonBgColor,
                'border-color': buttonBgColor,
                'border-radius': borderRadius + 'px',
                'padding': verticalPadding + 'px ' + horizontalPadding + 'px',
                'line-height': '1.2'
            }).attr('data-hover-bg', buttonHoverBgColor);
            
            // Apply to reset button
            $('.footer .action.reset.classic-button').css({
                'font-size': buttonTextSize + 'px',
                'color': buttonTextColor,
                'background-color': buttonBgColor,
                'border-color': buttonBgColor,
                'border-radius': borderRadius + 'px',
                'padding': verticalPadding + 'px ' + horizontalPadding + 'px',
                'line-height': '1.2'
            }).attr('data-hover-bg', buttonHoverBgColor);
        };
        
        // Real-time preview updates for footer button design controls
        $(document).on('input change', '.footer-design-button-text-size, .footer-design-button-text-color, .footer-design-button-text-color-text, .footer-design-button-bg-color, .footer-design-button-bg-color-text, .footer-design-button-hover-bg-color, .footer-design-button-hover-bg-color-text, .footer-design-button-border-radius', function() {
            updateFooterButtonPreview();
        });
        
        // Sync button text color picker with text input
        $(document).on('change', '.footer-design-button-text-color', function() {
            var colorValue = $(this).val();
            $('.footer-design-button-text-color-text').val(colorValue);
            updateFooterButtonPreview();
        });
        
        $(document).on('input', '.footer-design-button-text-color-text', function() {
            var colorValue = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/i.test(colorValue)) {
                $('.footer-design-button-text-color').val(colorValue);
                updateFooterButtonPreview();
            }
        });
        
        // Sync button bg color picker with text input
        $(document).on('change', '.footer-design-button-bg-color', function() {
            var colorValue = $(this).val();
            $('.footer-design-button-bg-color-text').val(colorValue);
            updateFooterButtonPreview();
        });
        
        $(document).on('input', '.footer-design-button-bg-color-text', function() {
            var colorValue = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/i.test(colorValue)) {
                $('.footer-design-button-bg-color').val(colorValue);
                updateFooterButtonPreview();
            }
        });
        
        // Sync button hover bg color picker with text input
        $(document).on('change', '.footer-design-button-hover-bg-color', function() {
            var colorValue = $(this).val();
            $('.footer-design-button-hover-bg-color-text').val(colorValue);
            updateFooterButtonPreview();
        });
        
        $(document).on('input', '.footer-design-button-hover-bg-color-text', function() {
            var colorValue = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/i.test(colorValue)) {
                $('.footer-design-button-hover-bg-color').val(colorValue);
                updateFooterButtonPreview();
            }
        });
        
        // Add hover effect using CSS data attribute
        $(document).on('mouseenter', '.footer .action.submit.classic-button, .footer .action.reset.classic-button', function() {
            var hoverBg = $(this).attr('data-hover-bg');
            if (hoverBg) {
                $(this).css({
                    'background-color': hoverBg,
                    'border-color': hoverBg
                });
            }
        });
        
        $(document).on('mouseleave', '.footer .action.submit.classic-button, .footer .action.reset.classic-button', function() {
            var bgColor = $('.footer-design-button-bg-color-text').val() || $('.footer-design-button-bg-color').val() || '#EB1256';
            $(this).css({
                'background-color': bgColor,
                'border-color': bgColor
            });
        });
        
        // Apply saved settings immediately when customization panel inputs are available
        function applySavedSettingsToPreview() {
            $('.element-design-font-size').each(function() {
                var formdataid = $(this).data('formdataid');
                if (formdataid) {
                    updateElementDesignPreview(formdataid);
                }
            });
        }
        
        // Apply settings when customization panel is loaded - use MutationObserver
        if (typeof MutationObserver !== 'undefined') {
            $(document).ready(function() {
                var elementAppend = document.querySelector('.elementAppend');
                if (elementAppend) {
                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.addedNodes.length) {
                                for (var i = 0; i < mutation.addedNodes.length; i++) {
                                    var node = mutation.addedNodes[i];
                                    if (node.nodeType === 1) {
                                        var $node = $(node);
                                        // Check if this node or any child contains design inputs
                                        if ($node.find('.element-design-font-size').length > 0 || $node.hasClass('element-design-font-size') || $node.closest('.element-design-font-size').length > 0) {
                                            // Sync color pickers and text inputs when panel is loaded
                                            setTimeout(function() {
                                                $('.element-design-color').each(function() {
                                                    var $picker = $(this);
                                                    var formdataid = $picker.data('formdataid');
                                                    var colorValue = $picker.val();
                                                    var $textInput = $('.element-design-color-text[data-formdataid="' + formdataid + '"]');
                                                    
                                                    // Sync text input with color picker value (HTML already has correct value)
                                                    if ($textInput.length && colorValue) {
                                                        $textInput.val(colorValue);
                                                    }
                                                });
                                                applySavedSettingsToPreview();
                                            }, 200);
                                            break;
                                        }
                                    }
                                }
                            }
                        });
                    });
                    observer.observe(elementAppend, { childList: true, subtree: true });
                }
            });
        }
        
        // Also trigger when design inputs are present on page load (in case panel was already open)
        $(document).ready(function() {
            setTimeout(function() {
                if ($('.element-design-font-size').length > 0) {
                    applySavedSettingsToPreview();
                }
                // Also update header preview if header design controls exist
                if ($('.header-design-heading-font-size').length > 0 || $('.header-design-subheading-font-size').length > 0) {
                    updateHeaderPreview();
                }
            }, 2000);
        });
        
        // Function to update header preview in real-time (make it global)
        window.updateHeaderPreview = function() {
            // Read heading (title) settings
            var headingFontSize = parseInt($('.header-design-heading-font-size').val()) || 24;
            var headingTextColor = $('.header-design-heading-text-color-text').val() || $('.header-design-heading-text-color').val() || '#000000';
            
            // Read sub-heading (description) settings
            var subheadingFontSize = parseInt($('.header-design-subheading-font-size').val()) || 16;
            var subheadingTextColor = $('.header-design-subheading-text-color-text').val() || $('.header-design-subheading-text-color').val() || '#000000';
            
            // Read alignment (applies to both)
            var textAlign = $('.header-text-align-input').val() || $('.header-design-text-align').val() || 'center';
            
            // Validate color formats
            if (!/^#[0-9A-Fa-f]{6}$/i.test(headingTextColor)) {
                headingTextColor = '#000000';
            }
            if (!/^#[0-9A-Fa-f]{6}$/i.test(subheadingTextColor)) {
                subheadingTextColor = '#000000';
            }
            
            // Apply to header title (heading)
            $('.globo-heading').css({
                'font-size': headingFontSize + 'px',
                'text-align': textAlign,
                'color': headingTextColor
            });
            
            // Apply to description (sub-heading)
            $('.globo-description').css({
                'font-size': subheadingFontSize + 'px',
                'text-align': textAlign,
                'color': subheadingTextColor
            });
            
            // Also apply alignment classes to formHeader
            $(".formHeader").removeClass("align-left align-center align-right").addClass(textAlign);
        };
        
        // Real-time preview updates for header design controls
        // Real-time preview updates for header design controls
        $(document).on('input change', '.header-design-heading-font-size, .header-design-subheading-font-size, .header-design-text-align, .header-text-align-input, .header-design-heading-text-color, .header-design-heading-text-color-text, .header-design-subheading-text-color, .header-design-subheading-text-color-text', function() {
            updateHeaderPreview();
        });
        
        // Sync heading color picker with text input
        $(document).on('change', '.header-design-heading-text-color', function() {
            var colorValue = $(this).val();
            $('.header-design-heading-text-color-text').val(colorValue);
            updateHeaderPreview();
        });
        
        $(document).on('input', '.header-design-heading-text-color-text', function() {
            var colorValue = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/i.test(colorValue)) {
                $('.header-design-heading-text-color').val(colorValue);
                updateHeaderPreview();
            }
        });
        
        // Sync sub-heading color picker with text input
        $(document).on('change', '.header-design-subheading-text-color', function() {
            var colorValue = $(this).val();
            $('.header-design-subheading-text-color-text').val(colorValue);
            updateHeaderPreview();
        });
        
        $(document).on('input', '.header-design-subheading-text-color-text', function() {
            var colorValue = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/i.test(colorValue)) {
                $('.header-design-subheading-text-color').val(colorValue);
                updateHeaderPreview();
            }
        });
        
        // Ensure select displays selected value properly
        $(document).ready(function() {
            // Force select to show selected value
            $('.header-design-text-align').each(function() {
                var $select = $(this);
                var selectedValue = $select.val();
                var selectedText = $select.find('option:selected').text();
                
                // Ensure the select is visible and shows the value
                $select.css({
                    'opacity': '1',
                    'visibility': 'visible',
                    'display': 'block',
                    'color': '#212b36'
                });
                
                // If select is empty, set default
                if (!selectedValue) {
                    $select.val('center');
                }
            });
        });
        
        
        // Prevent preview input and labels from being focused when clicking on design controls
        $(document).on('click focus mousedown', '.element-design-font-size, .element-design-font-weight, .element-design-border-radius, .element-design-color, .element-design-color-text, .element-design-bg-color, .element-design-bg-color-text', function(e) {
            // Prevent any focus on preview inputs and labels
            e.stopPropagation();
            // Blur any focused preview inputs and labels to prevent blue highlight
            $('.contact-form input, .contact-form textarea, .contact-form select, .contact-form label, .contact-form .label-content').blur();
            // Remove any background color that might have been applied
            $('.contact-form label, .contact-form .label-content').css({
                'background-color': 'transparent',
                'background': 'transparent'
            });
            // Remove focus immediately
            setTimeout(function() {
                $('.contact-form input, .contact-form textarea, .contact-form select, .contact-form label, .contact-form .label-content').blur();
                $('.contact-form label, .contact-form .label-content').css({
                    'background-color': 'transparent',
                    'background': 'transparent'
                });
            }, 0);
        });
        
        // Prevent preview inputs and labels from receiving focus at all
        $(document).on('focus', '.contact-form input, .contact-form textarea, .contact-form select, .contact-form label, .contact-form .label-content', function(e) {
            $(this).blur();
            $(this).css({
                'background-color': 'transparent',
                'background': 'transparent'
            });
            return false;
        });
        
        // Update preview for a specific element (local function - calls global if available)
        function updateElementDesignPreview(formdataid) {
            // Use global function if available (it has better selectors for both preview containers)
            if (typeof window.updateElementDesignPreview === 'function') {
                window.updateElementDesignPreview(formdataid);
                return;
            }
            
            // Fallback: Get all design values for this element
            var fontSize = parseInt($('.element-design-font-size[data-formdataid="' + formdataid + '"]').val()) || 16;
            var fontWeight = $('.element-design-font-weight[data-formdataid="' + formdataid + '"]').val() || '400';
            var color = $('.element-design-color-text[data-formdataid="' + formdataid + '"]').val() || '#000000';
            var borderRadiusVal = $('.element-design-border-radius[data-formdataid="' + formdataid + '"]').val();
            var borderRadius = (borderRadiusVal !== '' && borderRadiusVal !== null && borderRadiusVal !== undefined) ? parseInt(borderRadiusVal) : 4;
            if (isNaN(borderRadius) || borderRadius < 0) {
                borderRadius = 4;
            }
            var bgColor = $('.element-design-bg-color-text[data-formdataid="' + formdataid + '"]').val() || '';
            
            // Apply styles to the label (not the input field) with this formdataid
            var $previewLabel = $('.contact-form .label-content[data-formdataid="' + formdataid + '"], .code-form-app .label-content[data-formdataid="' + formdataid + '"]');
            if ($previewLabel.length) {
                $previewLabel.css({
                    'font-size': fontSize + 'px',
                    'font-weight': fontWeight,
                    'color': color
                });
                if (bgColor) {
                    $previewLabel.css('background-color', bgColor);
                }
            }
            
            // Apply border-radius to the input field (not font-size)
            var $previewInput = $('.contact-form input[data-formdataid="' + formdataid + '"], .contact-form textarea[data-formdataid="' + formdataid + '"], .code-form-app input[data-formdataid="' + formdataid + '"], .code-form-app textarea[data-formdataid="' + formdataid + '"]');
            if ($previewInput.length) {
                $previewInput.css({
                    'border-radius': borderRadius + 'px'
                });
            }
            
            // Apply to file upload elements (upload-area and file_button) in both preview containers
            var $uploadArea = $('.code-form-app .code-form-control[data-formdataid="' + formdataid + '"] .upload-area, .contact-form .code-form-control[data-formdataid="' + formdataid + '"] .upload-area');
            if ($uploadArea.length) {
                $uploadArea.css({
                    'border-radius': borderRadius + 'px'
                });
            }
            
            var $fileButton = $('.code-form-app .code-form-control[data-formdataid="' + formdataid + '"] .file_button, .contact-form .code-form-control[data-formdataid="' + formdataid + '"] .file_button');
            if ($fileButton.length) {
                $fileButton.css({
                    'font-size': fontSize + 'px',
                    'font-weight': fontWeight,
                    'color': color,
                    'border-radius': borderRadius + 'px'
                });
            }
        }
        
        // Load and apply saved design settings on page load
        function loadSavedDesignSettings() {
            var formId = $('.formid').val();
            if (!formId) {
                return;
            }
            
            // Ensure color picker and text input are synced for all elements
            $('.element-design-color').each(function() {
                var $picker = $(this);
                var formdataid = $picker.data('formdataid');
                var colorValue = $picker.val();
                var $textInput = $('.element-design-color-text[data-formdataid="' + formdataid + '"]');
                
                // Sync text input with color picker value
                if ($textInput.length && colorValue) {
                    $textInput.val(colorValue);
                }
            });
            
            // Apply settings for any design inputs that exist
            setTimeout(function() {
                applySavedSettingsToPreview();
            }, 500);
        }
        
        // Function to update star rating display when a star is selected (for customizer preview)
        function updateStarRatingDisplay(checkedInput) {
            var $fieldset = $(checkedInput).closest('fieldset');
            if (!$fieldset.length) return;
            
            var checkedValue = parseInt($(checkedInput).val()) || 0;
            var $allInputs = $fieldset.find('input[type="radio"]');
            
            // For each input, find its associated label and update
            $allInputs.each(function() {
                var $input = $(this);
                var inputValue = parseInt($input.val()) || 0;
                var labelId = $input.attr('id');
                if (!labelId) return;
                
                var $label = $fieldset.find('label[for="' + labelId + '"]');
                if (!$label.length) return;
                
                // Remove existing classes
                $label.removeClass('star-filled star-empty');
                
                // In RTL layout: inputs are ordered 5,4,3,2,1 in DOM
                // When value=3 is checked, we want to fill 5,4,3 (which appear as first 3 from left)
                // So fill all inputs with value >= checkedValue
                if (checkedValue > 0 && inputValue >= checkedValue) {
                    $label.addClass('star-filled');
                } else {
                    $label.addClass('star-empty');
                }
            });
        }
        
        // Initialize star rating handlers for customizer preview
        function initializeStarRatingHandlers() {
            // Handle star rating input changes
            $('.code-form-app .star-rating fieldset input[type="radio"]').off('change.starRating').on('change.starRating', function() {
                updateStarRatingDisplay(this);
            });
            
            // Handle star rating label clicks
            $('.code-form-app .star-rating fieldset label').off('click.starRating').on('click.starRating', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var labelFor = $(this).attr('for');
                if (!labelFor) return;
                
                var $input = $('#' + labelFor);
                if ($input.length && $input.attr('type') === 'radio') {
                    $input.prop('checked', true);
                    updateStarRatingDisplay($input[0]);
                    $input.trigger('change');
                }
            });
            
            // Initialize all stars as empty by default
            $('.code-form-app .star-rating fieldset label').each(function() {
                if (!$(this).hasClass('star-filled') && !$(this).hasClass('star-empty')) {
                    $(this).addClass('star-empty');
                }
            });
        }
        
        // Initialize on page load
        $(document).ready(function() {
            // Wait for form to load, then initialize
            setTimeout(function() {
                initializeStarRatingHandlers();
            }, 2000);
        });
        
        // Re-initialize when form elements are loaded/updated
        var starRatingObserver = new MutationObserver(function(mutations) {
            var shouldReinit = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    for (var i = 0; i < mutation.addedNodes.length; i++) {
                        var node = mutation.addedNodes[i];
                        if (node.nodeType === 1 && (node.classList.contains('star-rating') || node.querySelector('.star-rating'))) {
                            shouldReinit = true;
                            break;
                        }
                    }
                }
            });
            if (shouldReinit) {
                setTimeout(initializeStarRatingHandlers, 100);
            }
        });
        
        // Observe the preview container for changes
        $(document).ready(function() {
            var previewContainer = document.querySelector('.code-form-app');
            if (previewContainer) {
                starRatingObserver.observe(previewContainer, {
                    childList: true,
                    subtree: true
                });
            }
        });
        
        // Also re-initialize when form HTML is updated via AJAX (fallback)
        $(document).ajaxComplete(function() {
            setTimeout(initializeStarRatingHandlers, 300);
        });
        
        // Save element design settings
        $(document).on('click', '.save-element-design', function() {
            var $btn = $(this);
            var formdataid = $btn.data('formdataid');
            var elementid = $btn.data('elementid');
            var formId = $('.formid').val();
            
            if (!formId) {
                // Form ID is missing - silent fail
                return;
            }
            
            // Get current settings
            var settings = {
                fontSize: parseInt($('.element-design-font-size[data-formdataid="' + formdataid + '"]').val()) || 16,
                fontWeight: $('.element-design-font-weight[data-formdataid="' + formdataid + '"]').val() || '400',
                color: $('.element-design-color-text[data-formdataid="' + formdataid + '"]').val() || '#000000',
                borderRadius: parseInt($('.element-design-border-radius[data-formdataid="' + formdataid + '"]').val()) || 4,
                bgColor: $('.element-design-bg-color-text[data-formdataid="' + formdataid + '"]').val() || ''
            };
            
            // Save to database using formdataid as the key
            $.ajax({
                url: "ajax_call.php",
                type: "POST",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                data: {
                    routine_name: 'save_element_design_settings',
                    store: store,
                    form_id: formId,
                    formdata_id: formdataid,
                    element_id: elementid,
                    settings: JSON.stringify(settings)
                },
                success: function(response) {
                    if (response.result === 'success') {
                        // Show success message
                        var originalText = $btn.find('.Polaris-Button__Text').text();
                        $btn.find('.Polaris-Button__Text').text('Saved!');
                        setTimeout(function() {
                            $btn.find('.Polaris-Button__Text').text(originalText);
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    // Silent error handling
                }
            });
        });
        
        // Handle color scheme box clicks
        $(document).on('click', '.color-scheme-box:not(.add-scheme)', function() {
            var $box = $(this);
            var schemeId = $box.data('scheme-id');
            // Remove selected state from all boxes
            $('.color-scheme-box').removeClass('selected');
            // Add selected state to clicked box
            $box.addClass('selected');
            // Here you would typically apply the color scheme via AJAX
        });
        
        // Handle add scheme button click
        $(document).on('click', '.color-scheme-box.add-scheme', function() {
            // Here you would typically open a modal or form to create a new color scheme
        });
        
        // Select page
        $(document).on('click', '.selectPageBtn', function(e) {
            e.stopPropagation();
            var row = $(this).closest('tr');
            
            // Get page type
            selectedPageType = row.data('page-type');
            
            // Try to get data from data attributes first
            selectedPageId = row.data('page-id') || '-';
            selectedPageTitle = row.data('page-title') || row.find('td').eq(0).text().trim();
            selectedPageHandle = row.data('page-handle');
            
            // If not in data attributes, handle is stored in data attributes
            // No need to extract from cells since we only have Title and Action columns
            
            // Set display title based on type
            var displayTitle = selectedPageTitle;
            if (selectedPageType === 'home') {
                displayTitle = 'Home Page';
            } else if (selectedPageType === 'product') {
                displayTitle = 'Product';
            } else if (selectedPageType === 'collection') {
                displayTitle = 'Collections';
            } else if (selectedPageType === 'collections-list') {
                displayTitle = 'Collections List';
            } else if (selectedPageType === 'page') {
                displayTitle = 'Page: ' + selectedPageTitle;
            }
            
            if (selectedPageHandle) {
                // Close first modal and open confirm modal with loader
                $('#publishPageModal').hide();
                $('#publishConfirmLoader').show();
                $('#publishConfirmContent').hide();
                $('#confirmPublishBtn').hide();
                $('#publishConfirmModal').show();
                
                // Simulate loading delay, then show content
                setTimeout(function() {
                    $('#selectedPageTitle').text(displayTitle);
                    $('#publishConfirmLoader').hide();
                    $('#publishConfirmContent').show();
                    $('#confirmPublishBtn').show();
                }, 500);
            }
        });
        
        // Confirm and redirect to customizer
        $(document).on('click', '.confirmPublishBtn', function() {
            if (selectedPageHandle) {
                var customizerUrl = '';
                
                // Build customizer URL based on page type
                if (selectedPageType === 'home') {
                    customizerUrl = 'https://' + store + '/admin/themes/current/editor?previewPath=/';
                } else if (selectedPageType === 'product') {
                    customizerUrl = 'https://' + store + '/admin/themes/current/editor?previewPath=/products';
                } else if (selectedPageType === 'collection') {
                    customizerUrl = 'https://' + store + '/admin/themes/current/editor?previewPath=/collections';
                } else if (selectedPageType === 'collections-list') {
                    customizerUrl = 'https://' + store + '/admin/themes/current/editor?previewPath=/collections/all';
                } else if (selectedPageType === 'page') {
                    customizerUrl = 'https://' + store + '/admin/themes/current/editor?previewPath=/pages/' + encodeURIComponent(selectedPageHandle);
                } else {
                    // Fallback to pages
                    customizerUrl = 'https://' + store + '/admin/themes/current/editor?previewPath=/pages/' + encodeURIComponent(selectedPageHandle);
                }
                
                window.location.href = customizerUrl;
            } else {
                // Please select a page first - silent fail
            }
        });
        
        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
        }
        
        // Close modals when clicking outside
        $(document).on('click', '.Polaris-Modal-Dialog__Container', function(e) {
            if ($(e.target).hasClass('Polaris-Modal-Dialog__Container')) {
                $(this).hide();
            }
        });
    });

</script>
</script>