<?php
include_once('cls_header.php');
$cls_font_family = $shopify_pt_font_size = $shopify_pt_text_color = $shopify_ori_charge_color = $shopify_ori_charge_font_size = $shopify_dis_charge_color = $shopify_dis_charge_font_size = $shopify_image_width = $shopify_image_height = '';
$shopify_none_of_color = $shopify_none_of_font_size = $shopify_none_of_above_text = '';
$comeback_settings_arr = $cls_functions->receive_settings($shop);
if (!empty($comeback_settings_arr)) {
    extract($comeback_settings_arr);
}
?>
<div class="Polaris-Page">
    <div class="Polaris-Page__Header Polaris-Page__Header--hasBreadcrumbs Polaris-Page__Header--hasSecondaryActions Polaris-Page__Header--hasSeparator">
        <div class="Polaris-Page__MainContent">
            <div class="Polaris-Page__TitleAndActions">
                <div class="Polaris-Page__Title">
                    <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Settings</h1>
                </div>
            </div>
        </div>
    </div>
    <form method="post" name="addClientSettingFrm" id="addClientSettingFrm">
        <div class="Polaris-Page__Content">
            <div class="Polaris-Layout">
                <div class="Polaris-Layout__AnnotatedSection">
                    <div class="Polaris-Layout__AnnotationWrapper">
                        <div class="Polaris-Layout__AnnotationContent">
                            <div id="errorsmsgBlock" class="Polaris-Banner Polaris-Banner--statusCritical" tabindex="0" role="alert" aria-live="polite" aria-labelledby="errorBlockManage" aria-describedby="errorBlockInfo" style="display:none">
                                <div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorRedDark Polaris-Icon--isColored Polaris-Icon--hasBackdrop"><svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true"><g fill-rule="evenodd"><circle fill="currentColor" cx="10" cy="10" r="9"></circle><path d="M2 10c0-1.846.635-3.543 1.688-4.897l11.209 11.209A7.954 7.954 0 0 1 10 18c-4.411 0-8-3.589-8-8m14.312 4.897L5.103 3.688A7.954 7.954 0 0 1 10 2c4.411 0 8 3.589 8 8a7.952 7.952 0 0 1-1.688 4.897M0 10c0 5.514 4.486 10 10 10s10-4.486 10-10S15.514 0 10 0 0 4.486 0 10"></path></g></svg></span></div>
                                <div>
                                    <div class="Polaris-Banner__Heading" id="errorBlockManage">

                                    </div>
                                    <div class="Polaris-Banner__Content" id="errorBlockInfo">

                                    </div>
                                </div>
                            </div>
                            <div class="Polaris-Page__Content">
                                <div class="Polaris-Card">
                                    <div class="Polaris-Card__Section">
                                        <div class="Polaris-FormLayout">
                                            <div class="Polaris-Card__Header">
                                                <h2 class="Polaris-Heading">Offer On Spend Block Settings</h2>
                                            </div>
                                            <div class="Polaris-Card__Section">
                                                <div class="Polaris-FormLayout">
                                                    <div role="group" class="Polaris-FormLayout--condensed">
                                                        <div class="Polaris-FormLayout__Items">
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="">
                                                                    <div class="Polaris-Labelled__LabelWrapper">
                                                                        <div class="Polaris-Label"><label for="font_family" class="Polaris-Label__Text">Font Family</label></div>
                                                                    </div>
                                                                    <div class="Polaris-Select">
                                                                        <select id="font_family" name="font_family" class="Polaris-Select__Input" aria-invalid="false">
                                                                            <option selected="" value="">Default</option>
                                                                            <?php
                                                                            $cls_font_familys = $cls_functions->get_font_family(CLS_TABLE_FONT_FAMILYS);
                                                                            if (isset($cls_font_familys) && $cls_font_familys->num_rows > 0) {
                                                                                while ($cls_font_family_obj = $cls_font_familys->get_object()) {
                                                                                    ?>
                                                                                    <option <?php echo ($cls_font_family == $cls_font_family_obj->name) ? 'selected=""' : ''; ?> value="<?php echo $cls_font_family_obj->name; ?>" style="font-family:<?php echo $cls_font_family_obj->name; ?>"><?php echo $cls_font_family_obj->name; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                        <div class="Polaris-Select__Icon">
                                                                            <span class="Polaris-Icon">
                                                                                <svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true">
                                                                                <path d="M13 8l-3-3-3 3h6zm-.1 4L10 14.9 7.1 12h5.8z" fill-rule="evenodd"></path>
                                                                                </svg>
                                                                            </span>
                                                                        </div>
                                                                        <div class="Polaris-Select__Backdrop"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                            
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Card__Header">
                                                <h2 class="Polaris-Heading">Product Title Settings</h2>
                                            </div>
                                            <div class="Polaris-Card__Section">
                                                <div class="Polaris-FormLayout">
                                                    <div role="group" class="Polaris-FormLayout--condensed">
                                                        <div class="Polaris-FormLayout__Items">
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="pt_font_size" class="Polaris-Label__Text">Font Size</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input id="pt_font_size" name="pt_font_size" class="Polaris-TextField__Input" aria-labelledby="TextField3Label" aria-invalid="false" placeholder="Enter font size e.g 1,2 etc." value="<?php echo $shopify_pt_font_size; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select">
                                                                                <select class="Polaris-Select__Input" disabled="" aria-invalid="false"><option value="px">Px</option></select>
                                                                                <div class="Polaris-Select__Backdrop"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="pt_text_color" class="Polaris-Label__Text">Text color</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input data-id="pt_text_color" name="pt_text_color" class="Polaris-TextField__Input" aria-labelledby="TextField3Label" aria-invalid="false" placeholder="#000" value="<?php echo $shopify_pt_text_color; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select my_background_color input-group-btn">
                                                                                <input type="text" data-id="pt_text_color" class="spectrum_color spectrumColor" value="<?php echo $shopify_pt_text_color; ?>" style="display: none;">
                                                                                <div class="Polaris-Select__Backdrop"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                            
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Card__Header">
                                                <h2 class="Polaris-Heading">Original Product charge Settings</h2>
                                            </div>
                                            <div class="Polaris-Card__Section">
                                                <div class="Polaris-FormLayout">
                                                    <div role="group" class="Polaris-FormLayout--condensed">
                                                        <div class="Polaris-FormLayout__Items">
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="ori_charge_font_size" class="Polaris-Label__Text">Font Size</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input id="ori_charge_font_size" name="ori_charge_font_size" class="Polaris-TextField__Input" aria-labelledby="TextField3Label" aria-invalid="false" placeholder="Enter font size e.g 1,2 etc." value="<?php echo $shopify_ori_charge_font_size; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select">
                                                                                <select class="Polaris-Select__Input" disabled="" aria-invalid="false"><option value="px">Px</option></select>
                                                                                <div class="Polaris-Select__Backdrop"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="ori_charge_color" class="Polaris-Label__Text">Text color</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input data-id="ori_charge_color" name="ori_charge_color" class="Polaris-TextField__Input" aria-labelledby="TextField3Label" aria-invalid="false" placeholder="#000" value="<?php echo $shopify_ori_charge_color; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select my_background_color input-group-btn">
                                                                                <input type="text" data-id="ori_charge_color" class="spectrum_color spectrumColor" value="<?php echo $shopify_ori_charge_color; ?>" style="display: none;">
                                                                                <div class="Polaris-Select__Backdrop"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>                                                                                            
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Card__Header">
                                                <h2 class="Polaris-Heading">Discounted Product charge Settings</h2>
                                            </div>
                                            <div class="Polaris-Card__Section">
                                                <div class="Polaris-FormLayout">
                                                    <div role="group" class="Polaris-FormLayout--condensed">
                                                        <div class="Polaris-FormLayout__Items">
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="dis_charge_font_size" class="Polaris-Label__Text">Font Size</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input id="dis_charge_font_size" name="dis_charge_font_size" class="Polaris-TextField__Input" aria-labelledby="TextField3Label" aria-invalid="false" placeholder="Enter font size e.g 1,2 etc." value="<?php echo $shopify_dis_charge_font_size; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select">
                                                                                <select class="Polaris-Select__Input" disabled="" aria-invalid="false"><option value="px">Px</option></select>
                                                                                <div class="Polaris-Select__Backdrop"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="dis_charge_color" class="Polaris-Label__Text">Text color</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input type="text" data-id="dis_charge_color" name="dis_charge_color" class="Polaris-TextField__Input" aria-describedby="basic-addon1" placeholder="#000" value="<?php echo $shopify_dis_charge_color; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select my_background_color input-group-btn">
                                                                                <input type="text" data-id="dis_charge_color" class="spectrum_color spectrumColor" value="<?php echo $shopify_dis_charge_color; ?>" style="display: none;">
                                                                                <div class="Polaris-Select__Backdrop"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>                                                                                            
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Card__Header">
                                                <h2 class="Polaris-Heading">Product Image Settings</h2>
                                            </div>
                                            <div class="Polaris-Card__Section">
                                                <div class="Polaris-FormLayout">
                                                    <div role="group" class="Polaris-FormLayout--condensed">
                                                        <div class="Polaris-FormLayout__Items">
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="image_width" class="Polaris-Label__Text">Image Width</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input id="image_width" name="image_width" class="Polaris-TextField__Input" aria-labelledby="TextField3Label" aria-invalid="false" placeholder="Enter font size e.g 1,2 etc." value="<?php echo $shopify_image_width; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select">
                                                                                <select class="Polaris-Select__Input" disabled="" aria-invalid="false"><option value="px">Px</option></select>
                                                                                <div class="Polaris-Select__Backdrop"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="image_height" class="Polaris-Label__Text">Image Height</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input id="image_height" name="image_height" class="Polaris-TextField__Input" aria-labelledby="TextField3Label" aria-invalid="false" placeholder="Enter font size e.g 1,2 etc." value="<?php echo $shopify_image_height; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select">
                                                                                <select class="Polaris-Select__Input" disabled="" aria-invalid="false"><option value="px">Px</option></select>
                                                                                <div class="Polaris-Select__Backdrop"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                                                            
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="Polaris-Card__Header">
                                                <h2 class="Polaris-Heading">None Of Above Message Settings</h2>
                                            </div>
                                            <div class="Polaris-Card__Section">
                                                <div class="Polaris-FormLayout">
                                                    <div role="group" class="Polaris-FormLayout--condensed">
                                                        <div class="Polaris-FormLayout__Items">
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="none_of_above_text" class="Polaris-Label__Text">Text Message</label></div>
                                                                </div>
                                                                <div class="Polaris-TextField">
                                                                    <input id="none_of_above_text" name="none_of_above_text" class="Polaris-TextField__Input" aria-labelledby="TextField3Label" aria-invalid="false" placeholder="Enter None of above message" value="<?php echo $shopify_none_of_above_text; ?>">
                                                                    <div class="Polaris-TextField__Backdrop"></div>
                                                                </div>
                                                            </div>
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="none_of_font_size" class="Polaris-Label__Text">Font Size</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input id="none_of_font_size" name="none_of_font_size" class="Polaris-TextField__Input" aria-labelledby="TextField3Label" aria-invalid="false" placeholder="Enter font size e.g 1,2 etc." value="<?php echo $shopify_none_of_font_size; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select">
                                                                                <select class="Polaris-Select__Input" disabled="" aria-invalid="false"><option value="px">Px</option></select>
                                                                                <div class="Polaris-Select__Backdrop"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="Polaris-Labelled__LabelWrapper">
                                                                    <div class="Polaris-Label"><label for="none_of_color" class="Polaris-Label__Text">Text color</label></div>
                                                                </div>
                                                                <div class="Polaris-Connected">
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                        <div class="Polaris-TextField">
                                                                            <input type="text" data-id="none_of_color" name="none_of_color" class="Polaris-TextField__Input" aria-describedby="basic-addon1" placeholder="#000" value="<?php echo $shopify_none_of_color; ?>">
                                                                            <div class="Polaris-TextField__Backdrop"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--connection">
                                                                        <div class="Polaris-Labelled--hidden">
                                                                            <div class="Polaris-Select Polaris-Select my_background_color input-group-btn">
                                                                                <input type="text" data-id="none_of_color" class="spectrum_color spectrumColor" value="<?php echo $shopify_none_of_color; ?>" style="display: none;">
                                                                                <div class="Polaris-Select__Backdrop"></div>
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
                            <div class="Polaris-PageActions" style="float: right;">
                                <div class="Polaris-ButtonGroup">
                                    <div class="Polaris-ButtonGroup__Item">
                                        <button type="submit" class="Polaris-Button Polaris-Button--primary"><span class="Polaris-Button__Content"><span class="save_loader_show">Save</span></span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="Polaris-PageActions">
                                <div class="Polaris-ButtonGroup">
                                    <div class="Polaris-ButtonGroup__Item"><button type="button" class="Polaris-Button loader_show" onclick="window.location = 'index.php?shop=<?php echo $shop; ?>'"><span class="Polaris-Button__Content"><span>Cancel</span></span></button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php include_once('cls_footer.php'); ?>