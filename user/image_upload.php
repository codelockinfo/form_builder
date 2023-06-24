<?php
include_once('cls_header.php');
?>
<div class="Polaris-Page">
    <div class="Polaris-Page__Header Polaris-Page__Header--hasBreadcrumbs Polaris-Page__Header--hasSecondaryActions Polaris-Page__Header--hasSeparator">
        <div class="Polaris-Page__MainContent">
            <div class="Polaris-Page__TitleAndActions">
                <div class="Polaris-Page__Title">
                    <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">File Upload</h1>
                </div>
            </div>
        </div>
    </div>
    <form method="post" name="uploadExmaple" id="imageExmapleFrm" enctype="multipart/form-data">
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
                                                <h2 class="Polaris-Heading">File Upload</h2>
                                            </div>
                                            <div class="Polaris-Card__Section">
                                                <div class="Polaris-FormLayout">
                                                    <div role="group" class="Polaris-FormLayout--condensed">
                                                        <div class="Polaris-FormLayout__Items">
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="">
                                                                    <div class="Polaris-Labelled__LabelWrapper">
                                                                        <div class="Polaris-Label"><label for="font_family" class="Polaris-Label__Text">Caption</label></div>
                                                                    </div>
                                                                    <div class="Polaris-Select">
                                                                        <input type="text" id="caption" name="caption[]" class="Polaris-TextField__Input" aria-labelledby="TextField10Label" aria-invalid="false" value="">
                                                                        <input type="text" id="caption" name="caption[]" class="Polaris-TextField__Input" aria-labelledby="TextField10Label" aria-invalid="false" value="">
                                                                        <div class="Polaris-Select__Backdrop"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="Polaris-FormLayout__Items">
                                                            <div class="Polaris-FormLayout__Item">
                                                                <div class="">
                                                                    <div class="Polaris-Labelled__LabelWrapper">
                                                                        <div class="Polaris-Label"><label for="font_family" class="Polaris-Label__Text">File</label></div>
                                                                    </div>
                                                                    <div class="Polaris-Select">
                                                                        <input type="file" id="uploadedFile" name="upload_file" class="Polaris-TextField__Input" aria-labelledby="TextField10Label" aria-invalid="false">
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