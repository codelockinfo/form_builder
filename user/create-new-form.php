<!-- <!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/polaris_style1.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/customstyle3.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../assets/js/style3.js" type="text/javascript"></script>
    <script src="../assets/js/owl.carousel.js" type="text/javascript"></script>
    <script src="../assets/js/owl.carousel.min.js" type="text/javascript"></script>
    <script src="../assets/js/ckeditor.js" type="text/javascript"></script>
</head> -->
<?php

include_once('cls_header.php');

?>
<style>
/* Override Polaris checked checkbox color to match the primary button (#008060) */
.Polaris-Checkbox, 
.Polaris-Checkbox *,
.Polaris-ResourceList__CheckableButtonWrapper {
    --p-color-bg-interactive: #297eb0 !important;
    --p-color-bg-interactive-active: #297eb0 !important;
    --p-color-border-interactive: #297eb0 !important;
    --p-color-border-interactive-focus: #297eb0 !important;
    --p-interactive-active: #297eb0 !important;
}

/* Fallbacks for older Polaris versions that don't just use CSS variables */
.Polaris-Checkbox__Input:checked + .Polaris-Checkbox__Backdrop,
.Polaris-Checkbox__Input:indeterminate + .Polaris-Checkbox__Backdrop,
html[class*="Polaris-"] .Polaris-Checkbox__Input:checked + .Polaris-Checkbox__Backdrop {
    border-color: #297eb0 !important;
    background-color: transparent !important; /* Let ::before cover it */
}
.Polaris-Checkbox__Input:checked + .Polaris-Checkbox__Backdrop::before,
.Polaris-Checkbox__Input:indeterminate + .Polaris-Checkbox__Backdrop::before,
html[class*="Polaris-"] .Polaris-Checkbox__Input:checked + .Polaris-Checkbox__Backdrop::before {
    background-color: #297eb0 !important;
    border-color:  #297eb0 !important;
}

/* For the native header checkbox */
#headerCheckAllOuter[type=checkbox] {
    accent-color: #297eb0 !important;
}
</style>

<div class="Polaris-Page">
        <div class="Polaris-Page-Header Polaris-Page-Header--hasActionMenu Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
            <div class="Polaris-Page-Header__Row padding_all_">
                <div class="Polaris-Page-Header__TitleWrapper">
                    <h1 class="Polaris-Header-Title">Forms</h1>
                </div>
                <div class="Polaris-Page-Header__RightAlign">
                    <!-- <div class="Polaris-ActionMenu">
                        <div class="Polaris-ActionMenu-Actions__ActionsLayout">
                            <div class="Polaris-ButtonGroup Polaris-ButtonGroup--extraTight">
                                <div class="Polaris-ButtonGroup__Item">
                                    <span class="Polaris-ActionMenu-SecondaryAction">
                                        <button id="myBtn_import" class="Polaris-Button" type="button">
                                            <span class="Polaris-Button__Content">
                                                <span class="Polaris-Button__Icon">
                                                    <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path
                                                                d="M13.707 10.707a.999.999 0 1 0-1.414-1.414l-1.293 1.293v-7.586a1 1 0 1 0-2 0v7.586l-1.293-1.293a.999.999 0 1 0-1.414 1.414l3 3a.999.999 0 0 0 1.414 0l3-3zm-10.707 5.293a1 1 0 1 0 0 2h14a1 1 0 1 0 0-2h-14z"
                                                            ></path>
                                                        </svg>
                                                    </span>
                                                </span>
                                                <span class="Polaris-Button__Text">Import forms</span>
                                            </span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div id="myBtn_new" class="Polaris-Page-Header__PrimaryActionWrapper">
                        <button class="Polaris-Button Polaris-Button--primary" type="button">
                            <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Create new form</span></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="custom-form-tabs" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e1e3e5; padding: 12px 20px; background: #fff; border-radius: 8px 8px 0 0; margin-top: 20px;">
                <div class="form-tabs" style="display: flex; gap: 20px;">
                    <button class="form-tab-btn active" data-tab="all" style="background:none; border:none; font-weight:bold; color:#202223; padding: 6px 12px; border-radius: 6px; background-color: #f1f2f3; cursor:pointer;">All</button>
                    <button class="form-tab-btn" data-tab="active" style="background:none; border:none; font-weight:normal; color:#6d7175; padding: 6px 12px; border-radius: 6px; cursor:pointer;">Active</button>
                    <button class="form-tab-btn" data-tab="draft" style="background:none; border:none; font-weight:normal; color:#6d7175; padding: 6px 12px; border-radius: 6px; cursor:pointer;">Draft</button>
                </div>
                <div class="form-sort" style="position: relative; display: flex; gap: 10px;">
                    <button id="customSortForms" type="button" value="Newest" title="Toggle Sort Order" style="background: #fff; border: 1px solid #c9cccf; border-radius: 4px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; box-shadow: 0 1px 0 rgba(0,0,0,0.05);">
                        <svg viewBox="0 0 20 20" style="width: 16px; height: 16px; fill: #5c5f62;">
                            <path d="M7 4.293V14a1 1 0 1 1-2 0V4.293L3.707 5.586a1 1 0 0 1-1.414-1.414l3-3a1 1 0 0 1 1.414 0l3 3a1 1 0 0 1-1.414 1.414L7 4.293zm8 11.414V6a1 1 0 1 1 2 0v9.707l1.293-1.293a1 1 0 1 1 1.414 1.414l-3 3a1 1 0 0 1-1.414 0l-3-3a1 1 0 1 1 1.414-1.414L15 15.707z"></path>
                        </svg>
                    </button>
                </div>
            </div>
<div id="dynamicBulkActionsWrapper" class="Polaris-ResourceList__HeaderWrapper border-radi-botom-unset Polaris-ResourceList__HeaderWrapper--hasAlternateTool Polaris-ResourceList__HeaderWrapper--hasSelect Polaris-ResourceList__HeaderWrapper--isSticky" style="border-radius: 8px 8px 0 0; display: none; padding: 12px 20px; border-bottom: 1px solid #c9cccf; background: #fff;  flex-direction: row; justify-content: space-between; align-items: center; box-shadow: 0 -1px 0 rgba(0,0,0,0.05) inset; width: 100%; box-sizing: border-box;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <button id="checkAllBtnInner" style="background: #297eb0; border: none; border-radius: 4px; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0;">
            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg check-icon-svg" focusable="false" aria-hidden="true" style="fill: white; width: 24px; height: 24px; display: none;"><path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.437.437 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.641 0z"></path></svg>
            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg minus-icon-svg" focusable="false" aria-hidden="true" style="fill: white; width: 12px; height: 12px; display: block;"><path d="M15 9H5a1 1 0 1 0 0 2h10a1 1 0 1 0 0-2z"></path></svg>
        </button>
        <span class="Deselectcount" style="font-weight: 500; font-size: 14px; color: #202223;">0 selected</span>
    </div>
    <div style="display: flex; align-items: center; gap: 8px; position: relative;">
        <button class="Polaris-Button set-active-forms" type="button" style="padding: 5px 12px; border-radius: 4px; box-shadow: 0 1px 0 rgba(0,0,0,0.05); min-height: 28px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Set as active</span></span></button>
        <button class="Polaris-Button set-draft-forms" type="button" style="padding: 5px 12px; border-radius: 4px; box-shadow: 0 1px 0 rgba(0,0,0,0.05); min-height: 28px;"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Set as draft</span></span></button>
        <button class="Polaris-Button more-actions-btn" type="button" aria-controls="Polarispopover1" aria-owns="Polarispopover1" aria-expanded="false" style="padding: 5px 8px; border-radius: 4px; box-shadow: 0 1px 0 rgba(0,0,0,0.05); min-height: 28px;">
            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true" style="width: 16px; height: 16px; fill: #5c5f62;"><path d="M6 10a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm5.5 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm5.5 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"></path></svg>
        </button>
    </div>
</div>
            
            <div class="forms-list-responsive-wrapper">
                <div class="forms-list-container">
                    <div class="list-column-headers" id="listColumnHeaders" style="display: flex; padding: 12px 20px; font-weight: 600; color: #6d7175; font-size: 13px; border-bottom: 1px solid #e1e3e5; background: #fafbfc; justify-content: space-between;">
                        <div style="display: flex; gap: 15px; flex: 1; align-items: center;">
                            <div style="width: 32px;text-align:center;">
                                <input type="checkbox" id="headerCheckAllOuter" style="cursor: pointer; width: 16px; height: 16px; margin: 0; outline: none; border: 1px solid #c9cccf; border-radius: 4px;">
                            </div>
                            <div style="width: 80px;text-align:center;">Id</div>
                            <div style="width: 300px;text-align:center;">Title</div>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; width: 38%;">
                            <div style="width: 60px; text-align: center;">Status</div>
                            <div style="width: 150px; text-align: center;">Action</div>
                        </div>
                    </div>

                    <div class="set_all_form"></div>
                </div>
            </div>
<!--             
            <div class="DataRange">
  <div class="Polaris-Labelled__LabelWrapper">
    <div class="Polaris-Label">
      <label id=":R1n6:Label" for=":R1n6:" class="Polaris-Label__Text">Date range</label>
    </div>
  </div>
  <div class="Polaris-Select">
    <select id=":R1n6:" class="Polaris-Select__Input" aria-invalid="false">
      <option value="today" selected="">Today</option>
      <option value="yesterday">Yesterday</option>
      <option value="lastWeek">Last 7 days</option>
    </select>
    <div class="Polaris-Select__Content" aria-hidden="true">
      <span class="Polaris-Select__SelectedOption">Today</span>
      <span class="Polaris-Select__Icon">
        <span class="Polaris-Icon">
          <span class="Polaris-Text--root Polaris-Text--visuallyHidden">
          </span>
          <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
            <path d="M10.884 4.323a1.25 1.25 0 0 0-1.768 0l-2.646 2.647a.75.75 0 0 0 1.06 1.06l2.47-2.47 2.47 2.47a.75.75 0 1 0 1.06-1.06l-2.646-2.647Z">
            </path>
            <path d="m13.53 13.03-2.646 2.647a1.25 1.25 0 0 1-1.768 0l-2.646-2.647a.75.75 0 0 1 1.06-1.06l2.47 2.47 2.47-2.47a.75.75 0 0 1 1.06 1.06Z">
            </path>
          </svg>
        </span>
      </span>
    </div>
    <div class="Polaris-Select__Backdrop">
    </div>
  </div>
</div>



            <div class="Polaris-Layout">
                <div class="Polaris-Layout__Section Polaris-Layout__Section--oneHalf">
                    <div class="Polaris-Card card_pro">
                        <div class="Polaris-Card__Header">
                            <div class="Polaris-Stack Polaris-Stack--alignmentBaseline d-flex-jus-around">
                                <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><p class="dashboard-title dash-title">Submissions</p></div>
                                <div class="Polaris-Stack__Item">
                                    <div class="Polaris-ButtonGroup">
                                        <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                            <button class="Polaris-Button Polaris-Button--plain" type="button">
                                                <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">View submissions</span></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-Card__Section">
                            <div class="dashboard-chart">
                                <div class="chart-data">
                                    <span class="chart-large-size"><span class="Polaris-TextStyle--variationStrong">0</span></span>
                                </div>
                                <div class="dashboard-chart-body">
                                    <div id="chart-wrapper">
                                        <canvas id="chart"></canvas>
                                      </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Polaris-Layout__Section Polaris-Layout__Section--oneHalf">
                    <div class="Polaris-Card card_pro">
                        <div class="Polaris-Card__Header">
                            <div class="Polaris-Stack Polaris-Stack--alignmentBaseline d-flex-jus-around">
                                <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><p class="dashboard-title  dash-title">Customers</p></div>
                                <div class="Polaris-Stack__Item">
                                    <div class="Polaris-ButtonGroup">
                                        <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                            <button class="Polaris-Button Polaris-Button--plain" type="button">
                                                <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">View customers</span></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-Card__Section">
                            <div class="dashboard-chart">
                                <div class="chart-data">
                                    <span class="chart-large-size"><span class="Polaris-TextStyle--variationStrong">0</span></span>
                                </div>
                                <div class="dashboard-chart-body">
                                    <div id="chart-wrapper">
                                        <canvas id="chart2"></canvas>
                                      </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Polaris-Layout layout_card_">
                <div class="Polaris-Layout__Section Polaris-Layout__Section--oneHalf">
                    <div class="Polaris-Card">
                        <div class="Polaris-Card__Section">
                            <div class="dashboard-total-heading"><h2 class="Polaris-Heading">New customers</h2></div>
                            <div class="dashboard-total-customers">
                                <div class="dashboard-total-section">
                                    <h2 class="Polaris-Heading">Today</h2>
                                    <p>0</p>
                                </div>
                                <div class="dashboard-total-section">
                                    <h2 class="Polaris-Heading">This week</h2>
                                    <p>0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Polaris-Layout__Section Polaris-Layout__Section--oneHalf">
                    <div class="Polaris-Card">
                        <div class="Polaris-Card__Section">
                            <div class="dashboard-view-customers">
                                <div class="Polaris-Stack">
                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><p>0 pending customer(s) to review</p></div>
                                    <div class="Polaris-Stack__Item">
                                        <button class="Polaris-Button" type="button">
                                            <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">View customers</span></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-Card__Section">
                            <div class="dashboard-view-customers">
                                <div class="Polaris-Stack">
                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><p>0 customer(s) have been deny</p></div>
                                    <div class="Polaris-Stack__Item">
                                        <button class="Polaris-Button" type="button">
                                            <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">View customers</span></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
    

    <div id="myModal_new" class="modal">
        <form id="createNewForm" class="create_new_frm" name="create_new_form" method="POST"  enctype="multipart/form-data">
        <input type="hidden" class="selectedType" name="selectedType"  value="1">
        <input type="hidden" class="formnamehide" name="formnamehide"  value="Blank Form">
        <input type="hidden" class="formnamedescriptionhide" name="formdeswcriptionhide"  value="Leave your message and we'll get back to you shortly.">
            <div class="modal-content">
                <div class="Polaris-Modal-Dialog__Modal">
                    <div class="Polaris-Box" style="
                --pc-box-border-color: var(--p-color-border-subdued);
                --pc-box-border-style: solid;
                --pc-box-border-block-end-width: var(--p-border-width-1);
                --pc-box-padding-block-end-xs: var(--p-space-4);
                --pc-box-padding-block-start-xs: var(--p-space-4);
                --pc-box-padding-inline-start-xs: var(--p-space-5);
                --pc-box-padding-inline-end-xs: var(--p-space-5);
              ">
                        <div class="Polaris-HorizontalGrid" style="
                  --pc-horizontal-grid-grid-template-columns-xs: 1fr auto;
                  --pc-horizontal-grid-gap-xs: var(--p-space-4);
                ">
                            <div class="Polaris-HorizontalStack" style="
                    --pc-horizontal-stack-block-align: center;
                    --pc-horizontal-stack-wrap: wrap;
                    --pc-horizontal-stack-gap-xs: var(--p-space-4);
                  ">
                                <h2 class="Polaris-Text--root Polaris-Text--headingLg Polaris-Text--break" id=":R1n6:">
                                    Explore pre-built forms
                                </h2>
                            </div>
                            <button class="Polaris-Modal-CloseButton" aria-label="Close" type="button">
                                <span class="Polaris-Icon Polaris-Icon--colorBase Polaris-Icon--applyColor"><span
                                        class="Polaris-Text--root Polaris-Text--visuallyHidden"></span><svg
                                        viewBox="0 0 20 20" class="Polaris-Icon__Svg close_new" focusable="false"
                                        aria-hidden="true">
                                        <path
                                            d="m11.414 10 6.293-6.293a1 1 0 1 0-1.414-1.414l-6.293 6.293-6.293-6.293a1 1 0 0 0-1.414 1.414l6.293 6.293-6.293 6.293a1 1 0 1 0 1.414 1.414l6.293-6.293 6.293 6.293a.998.998 0 0 0 1.707-.707.999.999 0 0 0-.293-.707l-6.293-6.293z">
                                        </path>
                                    </svg></span>
                            </button>
                        </div>
                    </div>
                    <div class="Polaris-Modal__Body Polaris-Scrollable Polaris-Scrollable--vertical Polaris-Scrollable--horizontal"
                        data-polaris-scrollable="true">
                        <div class="Polaris-Modal-Section">
                            <section class="Polaris-Box" style="
                    --pc-box-padding-block-end-xs: var(--p-space-5);
                    --pc-box-padding-block-start-xs: var(--p-space-5);
                    --pc-box-padding-inline-start-xs: var(--p-space-5);
                    --pc-box-padding-inline-end-xs: var(--p-space-5);
                  ">
                                <div class="Polaris-TextContainer">
                                    <div class="d-flex-b">
                                        <div class="main_list_ active_form" data-val="1">
                                            <img src="../assets/images/form_img/build_from_scratch.jpg" alt="" />
                                            <p class="text_image_list firstone_ first_txt_image">Blank Form</p>
                                        </div>
                                        <div class="main_list_" data-val="2">
                                            <img src="../assets/images/form_img/Contact.png" alt="" />
                                            <p class="text_image_list">Contact Form</p>
                                        </div>
                                        <div class="main_list_" data-val="3">
                                            <img src="../assets/images/form_img/registration_form.png" alt="" />
                                            <p class="text_image_list">Shopify Registration Form</p>
                                        </div>
                                        <div class="main_list_" data-val="4">
                                            <img src="../assets/images/form_img/floating-form.png" alt="" />
                                            <p class="text_image_list">Floating contact form</p>
                                        </div>
                                        <!-- <div class="main_list_" data-val="5">
                                            <img src="../assets/images/form_img/multi_step_form.jpg" alt="" />
                                            <p class="text_image_list">Multi-Step Form</p>
                                        </div> -->
                                        <div class="main_list_" data-val="6">
                                            <img src="../assets/images/form_img/application.png" alt="" />
                                            <p class="text_image_list">Application form</p>
                                        </div>
                                        <div class="main_list_" data-val="7">
                                            <img src="../assets/images/form_img/refund1.png" alt="" />
                                            <p class="text_image_list">Refund Form</p>
                                        </div>
                                  
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="Polaris-HorizontalStack" style="
                --pc-horizontal-stack-block-align: center;
                --pc-horizontal-stack-wrap: wrap;
                --pc-horizontal-stack-gap-xs: var(--p-space-4);
              ">
                        <div class="Polaris-Box" style="
                  --pc-box-border-color: var(--p-color-border-subdued);
                  --pc-box-border-style: solid;
                  --pc-box-border-block-start-width: var(--p-border-width-1);
                  --pc-box-padding-block-end-xs: var(--p-space-4);
                  --pc-box-padding-block-start-xs: var(--p-space-4);
                  --pc-box-padding-inline-start-xs: var(--p-space-5);
                  --pc-box-padding-inline-end-xs: var(--p-space-5);
                  --pc-box-width: 100%;
                ">
                            <div class="Polaris-HorizontalStack" style="
                    --pc-horizontal-stack-align: space-between;
                    --pc-horizontal-stack-block-align: center;
                    --pc-horizontal-stack-wrap: wrap;
                    --pc-horizontal-stack-gap-xs: var(--p-space-4);
                  ">
                                <div class="Polaris-Box"></div>
                                <div class="Polaris-HorizontalStack" style="
                      --pc-horizontal-stack-wrap: wrap;
                      --pc-horizontal-stack-gap-xs: var(--p-space-2);
                    ">

                                    <button class="Polaris-Button Polaris-Button--primary btncreate_new" type="submit">
                                        <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Create
                                                Form</span></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="deleteConfirmationModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="Polaris-Modal-Dialog__Modal" style="height: auto !important;">
                <div class="Polaris-Box" style="--pc-box-border-color: var(--p-color-border-subdued); --pc-box-border-style: solid; --pc-box-border-block-end-width: var(--p-border-width-1); --pc-box-padding-block-end-xs: var(--p-space-4); --pc-box-padding-block-start-xs: var(--p-space-4); --pc-box-padding-inline-start-xs: var(--p-space-5); --pc-box-padding-inline-end-xs: var(--p-space-5);">
                    <div class="Polaris-HorizontalGrid" style="--pc-horizontal-grid-grid-template-columns-xs: 1fr auto; --pc-horizontal-grid-gap-xs: var(--p-space-4);">
                        <div class="Polaris-HorizontalStack" style="--pc-horizontal-stack-block-align: center; --pc-horizontal-stack-wrap: wrap; --pc-horizontal-stack-gap-xs: var(--p-space-4);">
                            <h2 class="Polaris-Text--root Polaris-Text--headingLg Polaris-Text--break">Delete form?</h2>
                        </div>
                        <button class="Polaris-Modal-CloseButton close-delete-modal" aria-label="Close" type="button">
                            <span class="Polaris-Icon Polaris-Icon--colorBase Polaris-Icon--applyColor">
                                <span class="Polaris-Text--root Polaris-Text--visuallyHidden"></span>
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path d="m11.414 10 6.293-6.293a1 1 0 1 0-1.414-1.414l-6.293 6.293-6.293-6.293a1 1 0 0 0-1.414 1.414l6.293 6.293-6.293 6.293a1 1 0 1 0 1.414 1.414l6.293-6.293 6.293 6.293a.998.998 0 0 0 1.707-.707.999.999 0 0 0-.293-.707l-6.293-6.293z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="Polaris-Modal__Body Polaris-Scrollable Polaris-Scrollable--vertical Polaris-Scrollable--horizontal" data-polaris-scrollable="true">
                    <div class="Polaris-Modal-Section">
                        <section class="Polaris-Box" style="--pc-box-padding-block-end-xs: var(--p-space-5); --pc-box-padding-block-start-xs: var(--p-space-5); --pc-box-padding-inline-start-xs: var(--p-space-5); --pc-box-padding-inline-end-xs: var(--p-space-5);">
                            <div class="Polaris-TextContainer">
                                <p id="deleteModalMessage">Are you sure you want to delete the selected form(s)? This action cannot be undone.</p>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="Polaris-HorizontalStack" style="--pc-horizontal-stack-block-align: center; --pc-horizontal-stack-wrap: wrap; --pc-horizontal-stack-gap-xs: var(--p-space-4);">
                    <div class="Polaris-Box" style="--pc-box-border-color: var(--p-color-border-subdued); --pc-box-border-style: solid; --pc-box-border-block-start-width: var(--p-border-width-1); --pc-box-padding-block-end-xs: var(--p-space-4); --pc-box-padding-block-start-xs: var(--p-space-4); --pc-box-padding-inline-start-xs: var(--p-space-5); --pc-box-padding-inline-end-xs: var(--p-space-5); --pc-box-width: 100%;">
                        <div class="Polaris-HorizontalStack" style="--pc-horizontal-stack-align: space-between; --pc-horizontal-stack-block-align: center; --pc-horizontal-stack-wrap: wrap; --pc-horizontal-stack-gap-xs: var(--p-space-4);">
                            <div class="Polaris-Box"></div>
                            <div class="Polaris-HorizontalStack" style="--pc-horizontal-stack-wrap: wrap; --pc-horizontal-stack-gap-xs: var(--p-space-2);">
                                <button class="Polaris-Button close-delete-modal" type="button">
                                    <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Cancel</span></span>
                                </button>
                                <button class="Polaris-Button Polaris-Button--primary Polaris-Button--toneCritical" type="button" id="confirmDeleteBtn">
                                    <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Delete</span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        //  const ctx = document.getElementById('chart').getContext('2d');
        //  const chart = new Chart(ctx, {
        //      type: 'bar',
        //       data: {
        //        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        //        datasets: [{
        //            label: '# of Votes',
        //            data: [12, 19, 3, 5, 2, 3],
        //            backgroundColor: [
        //                'rgba(255, 99, 132, 0.2)',
        //                'rgba(54, 162, 235, 0.2)',
        //                'rgba(255, 206, 86, 0.2)',
        //                'rgba(75, 192, 192, 0.2)',
        //                'rgba(153, 102, 255, 0.2)',
        //                'rgba(255, 159, 64, 0.2)'
        //            ],
        //            borderColor: [
        //                'rgba(255,99,132,1)',
        //                'rgba(54, 162, 235, 1)',
        //                'rgba(255, 206, 86, 1)',
        //                'rgba(75, 192, 192, 1)',
        //                'rgba(153, 102, 255, 1)',
        //                'rgba(255, 159, 64, 1)'
        //            ],
        //            borderWidth: 1
        //        }]
        //    },
        //      // Configuration options go here
        //      options: {
        //        responsive: true,
        //        scales: {
        //          xAxes: [{ticks: {beginAtZero: true}, gridLines: {zeroLineColor: 'red', drawBorder: true, display: true, zeroLineWidth: 5}}],
        //          // yAxes: [{ticks: {beginAtZero: true}, gridLines: {zeroLineColor: 'red', drawBorder: true, display: true, zeroLineWidth: 5}}]
                 
        //        }
        //      }
        //  });
        
        //  const ctx2 = document.getElementById('chart2').getContext('2d');
        //  const chart2 = new Chart(ctx2, {
        //      type: 'bar',
        //       data: {
        //        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        //        datasets: [{
        //            label: '# of Votes',
        //            data: [12, 19, 3, 5, 2, 3],
        //            backgroundColor: [
        //                'rgba(255, 99, 132, 0.2)',
        //                'rgba(54, 162, 235, 0.2)',
        //                'rgba(255, 206, 86, 0.2)',
        //                'rgba(75, 192, 192, 0.2)',
        //                'rgba(153, 102, 255, 0.2)',
        //                'rgba(255, 159, 64, 0.2)'
        //            ],
        //            borderColor: [
        //                'rgba(255,99,132,1)',
        //                'rgba(54, 162, 235, 1)',
        //                'rgba(255, 206, 86, 1)',
        //                'rgba(75, 192, 192, 1)',
        //                'rgba(153, 102, 255, 1)',
        //                'rgba(255, 159, 64, 1)'
        //            ],
        //            borderWidth: 1
        //        }]
        //    },
        //      // Configuration options go here
        //      options: {
        //        responsive: true,
        //        scales: {
        //          xAxes: [{ticks: {beginAtZero: true}, gridLines: {zeroLineColor: 'red', drawBorder: true, display: true, zeroLineWidth: 5}}],
        //          // yAxes: [{ticks: {beginAtZero: true}, gridLines: {zeroLineColor: 'red', drawBorder: true, display: true, zeroLineWidth: 5}}]
                 
        //        }
        //      }
        //  });
        $(document).ready(function() {
            getAllForm();
            
            // More actions button handler - handles both Polarispopover1 and Polarispopover21
            $(document).on('click', '[aria-controls^="Polarispopover"]', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var popover = $('#moreActionsPopover');
                if (popover.length === 0) {
                    // Create popover if it doesn't exist
                    var popoverHtml = '<div id="moreActionsPopover" style="position: fixed; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 220px; z-index: 99999; display: none;">' +
                        '<div style="padding: 8px 0;">' +
                        '<button type="button" class="duplicate-selected-forms" style="width: 100%; text-align: left; padding: 10px 16px; border: none; background: none; cursor: pointer; color: #202223; display: flex; align-items: center; gap: 8px;">' +
                        '<svg style="width: 16px; height: 16px; fill: currentcolor;" viewBox="0 0 20 20"><path d="M7 3a2 2 0 0 0-2 2v9a.999.999 0 0 0 1 1h.5v1.5a1.5 1.5 0 0 0 1.5 1.5h7a1.5 1.5 0 0 0 1.5-1.5v-10a1.5 1.5 0 0 0-1.5-1.5h-1.5v-.5a2 2 0 0 0-2-2h-3zm6.5 4h-5a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5v-10a.5.5 0 0 0-.5-.5h-2zm-2-2v2h-4.5v-5a1 1 0 0 1 1-1h3zm-3 6h4v1.5h-4v-1.5zm0 3h2v1.5h-2v-1.5z"></path></svg>' +
                        '<span>Duplicate selected form(s)</span>' +
                        '</button>' +
                        '<button type="button" class="delete-selected-forms" style="width: 100%; text-align: left; padding: 10px 16px; border: none; background: none; cursor: pointer; color: #d82c0d; display: flex; align-items: center; gap: 8px; border-top: 1px solid #e1e3e5; margin-top: 4px; padding-top: 12px;">' +
                        '<svg style="width: 16px; height: 16px; fill: currentcolor;" viewBox="0 0 20 20"><path d="M14 4h-2.5l-1-1h-1l-1 1h-2.5a.5.5 0 0 0 0 1h8a.5.5 0 0 0 0-1zm-6.5 2v9a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-9h-7z"></path></svg>' +
                        '<span>Delete selected form(s)</span>' +
                        '</button>' +
                        '</div>' +
                        '</div>';
                    $('body').append(popoverHtml);
                    popover = $('#moreActionsPopover');
                }
                
                // Position and toggle popover - right-align with button, clamp within viewport
                var buttonOffset = $(this).offset();
                var buttonRight = buttonOffset.left + $(this).outerWidth();
                var popoverWidth = 220;
                var viewportWidth = $(window).width();
                var scrollTop = $(window).scrollTop();
                
                // Right-align popover to button's right edge
                var popoverLeft = buttonRight - popoverWidth;
                
                // Clamp: don't go off left or right edge (8px margin)
                if (popoverLeft < 8) popoverLeft = 8;
                if (popoverLeft + popoverWidth > viewportWidth - 8) {
                    popoverLeft = viewportWidth - popoverWidth - 8;
                }
                
                popover.css({
                    top: buttonOffset.top - scrollTop + $(this).outerHeight() + 5,
                    left: popoverLeft
                });
                popover.toggle();
                $(this).attr('aria-expanded', popover.is(':visible'));
            });
            
            // Close popover when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#moreActionsPopover, [aria-controls^="Polarispopover"]').length) {
                    $('#moreActionsPopover').hide();
                    $('[aria-controls^="Polarispopover"]').attr('aria-expanded', 'false');
                }
            });
            
            // Duplicate selected forms handler
            $(document).on('click', '.duplicate-selected-forms', function() {
                var selectedIds = [];
                $('.selectedCheck:checked').each(function() {
                    var formId = $(this).closest('.Polaris-ResourceList__HeaderWrapper').find('.form_id_main').val();
                    if (formId) {
                        selectedIds.push(formId);
                    }
                });
                
                if (selectedIds.length === 0) {
                    alert('Please select at least one form to duplicate.');
                    return;
                }
                
                // Show loading state
                var duplicateCount = 0;
                var totalCount = selectedIds.length;
                
                // Duplicate each form
                selectedIds.forEach(function(formId, index) {
                    $.ajax({
                        url: "ajax_call.php",
                        type: "post",
                        dataType: "json",
                        data: { 
                            'routine_name': 'duplicateFormFunction', 
                            'form_id': formId,
                            store: store 
                        },
                        success: function (response) {
                            // Parse response if it's a string
                            if (typeof response === 'string') {
                                try {
                                    response = JSON.parse(response);
                                } catch(e) {
                                      
                                }
                            }
                            
                            duplicateCount++;
                            
                            // When all duplications are complete, reload the form list
                            if (duplicateCount === totalCount) {
                                // Reload the form list
                                getAllForm();
                                
                                // Uncheck all checkboxes
                                $('.selectedCheck').prop('checked', false);
                                
                                // Show success message using Polaris-style banner
                                var successBanner = '<div class="Polaris-Banner Polaris-Banner--statusSuccess" style="margin: 20px 0;">' +
                                    '<div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorSuccess">' +
                                    '<svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">' +
                                    '<path d="M0 10c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10-10-4.486-10-10zm15.696-2.804l-6.5 6.5c-.196.196-.512.196-.707 0l-3.5-3.5c-.196-.196-.196-.512 0-.707l.707-.707c.195-.195.511-.195.707 0l2.439 2.44 5.439-5.44c.195-.195.511-.195.707 0l.707.707c.196.196.196.512.001.707z"></path>' +
                                    '</svg></span></div>' +
                                    '<div class="Polaris-Banner__ContentWrapper">' +
                                    '<div class="Polaris-Banner__Content"><p>' + totalCount + ' form(s) duplicated successfully</p></div>' +
                                    '</div></div>';
                                
                                $('.set_all_form').prepend(successBanner);
                                
                                // Remove banner after 3 seconds
                                setTimeout(function() {
                                    $('.Polaris-Banner--statusSuccess').fadeOut(400, function() {
                                        $(this).remove();
                                    });
                                }, 3000);
                            }
                        },
                        error: function(xhr, status, error) {
                            duplicateCount++;
                            if (duplicateCount === totalCount) {
                                flashNotice('Some forms could not be duplicated. Check console for details.', 'inline-flash--error');
                                getAllForm();
                            }
                        }
                    });
                });
            });
            
            // Delete selected forms handler
            var formsToDelete = [];
            
            $(document).on('click', '.delete-selected-forms', function() {
                var selectedIds = [];
                $('.selectedCheck:checked').each(function() {
                    var formId = $(this).closest('.Polaris-ResourceList__HeaderWrapper').find('.form_id_main').val();
                    if (formId) {
                        selectedIds.push(formId);
                    }
                });
                
                if (selectedIds.length === 0) {
                    return;
                }
                
                formsToDelete = selectedIds;
                $('#deleteModalMessage').text('Are you sure you want to delete ' + selectedIds.length + ' form(s)? This action cannot be undone.');
                $('#moreActionsPopover').hide();
                $('#deleteConfirmationModal').show();
                if (window.centerModal) window.centerModal('deleteConfirmationModal');
            });
            
            // Confirm delete handler
            $(document).on('click', '#confirmDeleteBtn', function() {
                var selectedIds = formsToDelete;
                if (selectedIds.length === 0) return;
                
                $('#deleteConfirmationModal').hide();
                
                // Show loading state
                var deleteCount = 0;
                var totalCount = selectedIds.length;
                
                // Delete each form
                selectedIds.forEach(function(formId, index) {
                    $.ajax({
                        url: "ajax_call.php",
                        type: "post",
                        dataType: "json",
                        data: { 
                            'routine_name': 'deleteFormFunction', 
                            'form_id': formId,
                            store: store 
                        },
                        success: function (response) {
                        
                            // Parse response if it's a string
                            if (typeof response === 'string') {
                                try {
                                    response = JSON.parse(response);
                                } catch(e) {
                                  
                                }
                            }
                            
                            deleteCount++;
                            
                            // Check if deletion was successful
                            if (response && response.result === 'success') {
                             
                            } else {
                              
                            }
                            
                            // When all deletions are complete, reload the form list
                            if (deleteCount === totalCount) {
                                // Reload the form list
                                getAllForm();
                                
                                // Uncheck all checkboxes
                                $('.selectedCheck').prop('checked', false);
                                
                                // Show success message using Polaris-style banner
                                var successBanner = '<div class="Polaris-Banner Polaris-Banner--statusSuccess" style="margin: 20px 0;">' +
                                    '<div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorSuccess">' +
                                    '<svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">' +
                                    '<path d="M0 10c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10-10-4.486-10-10zm15.696-2.804l-6.5 6.5c-.196.196-.512.196-.707 0l-3.5-3.5c-.196-.196-.196-.512 0-.707l.707-.707c.195-.195.511-.195.707 0l2.439 2.44 5.439-5.44c.195-.195.511-.195.707 0l.707.707c.196.196.196.512.001.707z"></path>' +
                                    '</svg></span></div>' +
                                    '<div class="Polaris-Banner__ContentWrapper">' +
                                    '<div class="Polaris-Banner__Content"><p>' + totalCount + ' form(s) deleted successfully</p></div>' +
                                    '</div></div>';
                                
                                $('.set_all_form').prepend(successBanner);
                                
                                // Remove banner after 3 seconds
                                setTimeout(function() {
                                    $('.Polaris-Banner--statusSuccess').fadeOut(400, function() {
                                        $(this).remove();
                                    });
                                }, 3000);
                            }
                        },
                        error: function(xhr, status, error) {
                        
                            deleteCount++;
                            if (deleteCount === totalCount) {
                                flashNotice('Some forms could not be deleted. Check console for details.', 'inline-flash--error');
                                getAllForm();
                            }
                        }
                    });
                });
            });

            // Close modal handler
            $(document).on('click', '.close-delete-modal, #deleteConfirmationModal', function(e) {
                e.preventDefault();
                if (e.target === this || $(this).hasClass('close-delete-modal')) {
                    $('#deleteConfirmationModal').hide();
                }
            });

            // Filter and Sort logic
            function updateFormListDisplay() {
                var activeTab = $('.form-tab-btn.active').data('tab');
                var sortVal = $('#customSortForms').val();
                
                // Remove existing no-forms message if any
                $('.no-forms-message').remove();
                
                var $forms = $('.set_all_form > .Polaris-ResourceList__HeaderWrapper');
                var visibleCount = 0;
                
                // First filter
                $forms.each(function() {
                    var isActive = $(this).find('input[type="checkbox"][name="checkbox"]').is(':checked');
                    var shouldShow = false;
                    if (activeTab === 'all') {
                        shouldShow = true;
                    } else if (activeTab === 'active' && isActive) {
                        shouldShow = true;
                    } else if (activeTab === 'draft' && !isActive) {
                        shouldShow = true;
                    }
                    
                    if (shouldShow) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });
                
                // Then sort
                var formsArr = $forms.get();
                var sortKey = $('input[name="sortKey"]:checked').val() || 'created';
                
                formsArr.sort(function(a, b) {
                    if (sortKey === 'title') {
                        var titleA = $(a).find('.sp-font-size').text().trim().toLowerCase();
                        var titleB = $(b).find('.sp-font-size').text().trim().toLowerCase();
                        if (sortVal === 'Oldest') { // Ascending
                            return titleA.localeCompare(titleB);
                        } else { // Descending
                            return titleB.localeCompare(titleA);
                        }
                    } else {
                        var idA = parseInt($(a).find('.form_id_main').val()) || 0;
                        var idB = parseInt($(b).find('.form_id_main').val()) || 0;
                        if (sortVal === 'Oldest') {
                            return idA - idB; 
                        } else {
                            return idB - idA;
                        }
                    }
                });
                $('.set_all_form').append(formsArr);

                // Show message if visibleCount is 0
                if (visibleCount === 0) {
                    var message = "No forms found.";
                    if (activeTab === 'draft') {
                        message = "No form in the draft!";
                    } else if (activeTab === 'active') {
                        message = "No active forms found!.";
                    }
                    $('.set_all_form').append('<div class="no-forms-message" style="padding: 80px 40px; text-align: center; color: #202223; font-size: 16px; font-weight: bold; background:white; border-radius: 0 0 8px 8px; border: 1px solid #e1e3e5; border-top: none; margin-top: -1px;">' + message + '</div>');
                }
            }

            $('.form-tab-btn').click(function() {
                $('.form-tab-btn').removeClass('active').css({
                    'font-weight': 'normal', 
                    'color': '#6d7175', 
                    'background-color': 'transparent'
                });
                $(this).addClass('active').css({
                    'font-weight': 'bold', 
                    'color': '#202223', 
                    'background-color': '#f1f2f3'
                });
                updateFormListDisplay();
            });

            $('#customSortForms').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var popover = $('#sortformsPopover');
                if (popover.length === 0) {
                    var popoverHtml = '<div id="sortformsPopover" style="position: absolute; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px; z-index: 1000; display: none;">' +
                        '<div style="padding: 8px 0;">' +
                        '<button type="button" class="sort-direction-btn" data-val="Oldest" style="width: 100%; text-align: left; padding: 8px 16px; border: none; background: none; cursor: pointer; color: #202223; display: flex; align-items: center; gap: 8px; font-size: 14px;">' +
                        '<svg style="width: 16px; height: 16px; fill: currentcolor;" viewBox="0 0 20 20"><path d="M10 4.293V16a1 1 0 1 1-2 0V4.293L4.707 7.586a1 1 0 0 1-1.414-1.414l5-5a1 1 0 0 1 1.414 0l5 5a1 1 0 1 1-1.414 1.414L10 4.293z"></path></svg>' +
                        '<span>Oldest to newest</span>' +
                        '</button>' +
                        '<button type="button" class="sort-direction-btn active-sort" data-val="Newest" style="width: 100%; text-align: left; padding: 8px 16px; border: none; background: #2f89c7; cursor: pointer; color: #ffffff; display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 500;">' +
                        '<svg style="width: 16px; height: 16px; fill: currentcolor;" viewBox="0 0 20 20"><path d="M10 15.707V4a1 1 0 1 1 2 0v11.707l3.293-3.293a1 1 0 1 1 1.414 1.414l-5 5a1 1 0 0 1-1.414 0l-5-5a1 1 0 1 1 1.414-1.414L10 15.707z"></path></svg>' +
                        '<span>Newest to oldest</span>' +
                        '</button>' +
                        '</div>' +
                        '</div>';
                    $('body').append(popoverHtml);
                    popover = $('#sortformsPopover');

                    $(document).on('click', '.sort-direction-btn', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $('.sort-direction-btn').css({'background': 'none', 'font-weight': 'normal', 'color': '#202223'}).removeClass('active-sort');
                        $(this).css({'background': '#2f89c7', 'font-weight': '500', 'color': '#ffffff'}).addClass('active-sort');
                        $('#customSortForms').val($(this).data('val'));
                        updateFormListDisplay();
                        popover.hide();
                    });

                    $(document).on('change', 'input[name="sortKey"]', function(e) {
                        e.stopPropagation();
                        updateFormListDisplay();
                    });
                }
                
                var buttonOffset = $(this).offset();
                popover.css({
                    top: buttonOffset.top + $(this).outerHeight() + 5,
                    left: buttonOffset.left - popover.outerWidth() + $(this).outerWidth()
                });
                
                // Hide other popovers
                $('#moreActionsPopover').hide();
                popover.toggle();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#customSortForms').length && !$(e.target).closest('#sortformsPopover').length) {
                    $('#sortformsPopover').hide();
                }
            });

            $(document).on('change', 'input[type="checkbox"][name="checkbox"]', function() {
                var activeTab = $('.form-tab-btn.active').data('tab');
                if (activeTab !== 'all') {
                    // Update display slightly delayed to let users see the toggle animation
                    setTimeout(updateFormListDisplay, 400);
                }
            });

            function updateBulkActionsUI() {
                var checkedCount = $('.selectedCheck:checked').length;
                var totalCount = $('.selectedCheck').length;
                
                if (checkedCount > 0) {
                    if (checkedCount === totalCount && totalCount > 0) {
                        $('.Deselectcount').text('Select all form');
                    } else {
                        $('.Deselectcount').text(checkedCount + ' selected');
                    }
                    $('#dynamicBulkActionsWrapper').css('display', 'flex');
                    $('#listColumnHeaders').hide();
                    
                    if (checkedCount === totalCount && totalCount > 0) {
                        $('#headerCheckAllOuter').prop('checked', true);
                        $('#checkAllBtnInner .check-icon-svg').show();
                        $('#checkAllBtnInner .minus-icon-svg').hide();
                    } else {
                        $('#headerCheckAllOuter').prop('checked', false);
                        $('#checkAllBtnInner .check-icon-svg').hide();
                        $('#checkAllBtnInner .minus-icon-svg').show();
                    }
                } else {
                    $('#dynamicBulkActionsWrapper').hide();
                    $('#listColumnHeaders').show();
                    $('#headerCheckAllOuter').prop('checked', false);
                }
            }

            $(document).on('change', '.selectedCheck', function() {
                updateBulkActionsUI();
            });

            $(document).on('change', '#headerCheckAllOuter', function() {
                var isChecked = $(this).prop('checked');
                $('.selectedCheck').prop('checked', isChecked);
                updateBulkActionsUI();
            });

            $(document).on('click', '#checkAllBtnInner', function(e) {
                e.preventDefault();
                $('.selectedCheck').prop('checked', false);
                updateBulkActionsUI();
            });

            function bulkUpdateStatus(isActive) {
                var selectedIds = [];
                $('.selectedCheck:checked').each(function() {
                    var formId = $(this).closest('.Polaris-ResourceList__HeaderWrapper').find('.form_id_main').val();
                    if (formId) selectedIds.push(formId);
                });
                
                if (selectedIds.length === 0) return;
                
                var totalCount = selectedIds.length;
                var processedCount = 0;
                var ischecked_value = isActive ? 1 : 0;
                var actionText = isActive ? 'activated' : 'set to draft';
                
                selectedIds.forEach(function(formId) {
                    $.ajax({
                        url: "ajax_call.php",
                        type: "post",
                        dataType: "json",
                        data: {
                            'routine_name': 'change_form_status', 
                            store: window.store || store, 
                            "formid": formId, 
                            "ischecked_value": ischecked_value 
                        },
                        success: function(response) {
                            processedCount++;
                            if (processedCount === totalCount) {
                                getAllForm();
                                $('.selectedCheck').prop('checked', false);
                                var successBanner = '<div class="Polaris-Banner Polaris-Banner--statusSuccess" style="margin: 20px 0;">' +
                                    '<div class="Polaris-Banner__Ribbon"><span class="Polaris-Icon Polaris-Icon--colorSuccess">' +
                                    '<svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">' +
                                    '<path d="M0 10c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10-10-4.486-10-10zm15.696-2.804l-6.5 6.5c-.196.196-.512.196-.707 0l-3.5-3.5c-.196-.196-.196-.512 0-.707l.707-.707c.195-.195.511-.195.707 0l2.439 2.44 5.439-5.44c.195-.195.511-.195.707 0l.707.707c.196.196.196.512.001.707z"></path>' +
                                    '</svg></span></div>' +
                                    '<div class="Polaris-Banner__ContentWrapper">' +
                                    '<div class="Polaris-Banner__Content"><p>' + totalCount + ' form(s) successfully ' + actionText + '</p></div>' +
                                    '</div></div>';
                                
                                $('.set_all_form').prepend(successBanner);
                                setTimeout(function() {
                                    $('.Polaris-Banner--statusSuccess').fadeOut(400, function() {
                                        $(this).remove();
                                    });
                                }, 3000);
                            }
                        },
                        error: function(xhr, status, error) {
                            processedCount++;
                            if (processedCount === totalCount) {
                                getAllForm();
                                $('.selectedCheck').prop('checked', false);
                            }
                        }
                    });
                });
            }

            $(document).on('click', '.set-active-forms', function() {
                bulkUpdateStatus(true);
            });

            $(document).on('click', '.set-draft-forms', function() {
                bulkUpdateStatus(false);
            });

            $(document).ajaxComplete(function(event, xhr, settings) {
                if (settings.url && settings.url.indexOf('ajax_call.php') !== -1 && settings.data && settings.data.indexOf('routine_name=getAllFormFunction') !== -1) {
                    updateFormListDisplay();
                    updateBulkActionsUI();
                }
            });

        });
    </script>