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

?>

<div class="Polaris-Page">
        <div class="Polaris-Page-Header Polaris-Page-Header--hasActionMenu Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
            <div class="Polaris-Page-Header__Row padding_all_">
                <div class="Polaris-Page-Header__TitleWrapper">
                    <h1 class="Polaris-Header-Title">Forms</h1>
                </div>
                <div class="Polaris-Page-Header__RightAlign">
                    <div class="Polaris-ActionMenu">
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
                    </div>
                    <div id="myBtn_new" class="Polaris-Page-Header__PrimaryActionWrapper">
                        <button class="Polaris-Button Polaris-Button--primary" type="button">
                            <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Create new form</span></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="Polaris-ResourceList__HeaderWrapper border-radi-botom-unset Polaris-ResourceList__HeaderWrapper--hasAlternateTool Polaris-ResourceList__HeaderWrapper--hasSelect Polaris-ResourceList__HeaderWrapper--isSticky">
                <div class="Polaris-ResourceList__HeaderContentWrapper">
                    <!-- <div class="Polaris-ResourceList__HeaderTitleWrapper">Showing 3 form</div> -->
                    <div class="Polaris-ResourceList__CheckableButtonWrapper">
                        <div class="Polaris-CheckableButton Polaris-CheckableButton__CheckableButton--plain selectedshow">
                            <label class="Polaris-Choice">
                                <span class="Polaris-Choice__Control">
                                  <span class="Polaris-Checkbox">
                                    <input name="chekbox1" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="">
                                    <span class="Polaris-Checkbox__Backdrop">
                                    </span>
                                    <span class="Polaris-Checkbox__Icon">
                                      <span class="Polaris-Icon">
                                        <span class="Polaris-Text--root Polaris-Text--visuallyHidden">
                                        </span>
                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                          <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                          </path>
                                        </svg>
                                      </span>
                                    </span>
                                  </span>
                                </span>
                              </label>
                            <span class="Polaris-CheckableButton__Label dataAdded"></span>
                           
                        </div>
                        <div class="bultActionss">
                                <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented" data-buttongroup-segmented="true">
                                    <div class="Polaris-ButtonGroup__Item">
                                        <div class="Polaris-CheckableButton Polaris-CheckableButton__CheckableButton--selectMode Polaris-CheckableButton__CheckableButton--selected">
                                              
                                                <div class="Polaris-CheckableButton__Checkbox">
                                                <label class="Polaris-Choice">
                                                    <span class="Polaris-Choice__Control">
                                                    <span class="Polaris-Checkbox">
                                                        <input name="chekbox1" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" id="checkAll">
                                                        <span class="Polaris-Checkbox__Backdrop">
                                                        </span>
                                                        <span class="Polaris-Checkbox__Icon">
                                                        <span class="Polaris-Icon">
                                                            <span class="Polaris-Text--root Polaris-Text--visuallyHidden">
                                                            </span>
                                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                            </path>
                                                            </svg>
                                                        </span>
                                                        </span>
                                                    </span>
                                                    </span>
                                                    <span class="Polaris-Choice__Label Deselectcount"></span>
                                                </label>
                                                    <!-- <label class="Polaris-Choice Polaris-Choice--labelHidden" for="PolarisCheckbox2">
                                                        <span class="Polaris-Choice__Control">
                                                            <span class="Polaris-Checkbox">
                                                                <input id="checkAll" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="true" value="" >
                                                                <span class="Polaris-Checkbox__Backdrop"></span>
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
                                                    
                                                    </label> -->
                                                </div>
                                                <!-- <span class="Polaris-CheckableButton__Label">4 selected</span> -->
                                            </div>
                                   
                                    </div>
                                    <div class="Polaris-ButtonGroup__Item">
                                        <div class="Polaris-BulkActions__BulkActionButton">
                                            <button class="Polaris-Button" type="button">
                                                <span class="Polaris-Button__Content">
                                                    <span class="Polaris-Button__Text">Duplicate selected form(s)</span>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="Polaris-ButtonGroup__Item">
                                        <div>
                                            <div>
                                                <div class="Polaris-BulkActions__BulkActionButton">
                                                    <button class="Polaris-Button" type="button" tabindex="0" aria-controls="Polarispopover1" aria-owns="Polarispopover1" aria-expanded="false">
                                                        <span class="Polaris-Button__Content">
                                                            <span class="Polaris-Button__Text">More actions</span>
                                                            <span class="Polaris-Button__Icon">
                                                                <div class="">
                                                                    <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path>
                                                                        </svg>
                                                                    </span>
                                                                </div>
                                                            </span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="Polaris-ResourceList__AlternateToolWrapper">
                        <div style="display: flex;">
                            <div class="Polaris-Labelled--hidden">
                                <div class="Polaris-Labelled__LabelWrapper">
                                    <div class="Polaris-Label"><label id="PolarisSelect9Label" for="PolarisSelect9" class="Polaris-Label__Text">Sort by</label></div>
                                </div>
                                <div class="Polaris-Select">
                                    <select id="PolarisSelect9" class="Polaris-Select__Input" aria-invalid="false">
                                        <option value="DATE_CREATED_DESC">Newest</option>
                                        <option value="DATE_CREATED_ASC">Oldest</option>
                                    </select>
                                    <div class="Polaris-Select__Content" aria-hidden="true">
                                        <span class="Polaris-Select__InlineLabel">Sort by</span><span class="Polaris-Select__SelectedOption">Newest</span>
                                        <span class="Polaris-Select__Icon">
                                            <span class="Polaris-Icon">
                                                <span class="Polaris-VisuallyHidden"></span>
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path
                                                        d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"
                                                    ></path>
                                                </svg>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="Polaris-Select__Backdrop"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Polaris-ResourceList__BulkActionsWrapper">
                    <div>
                        <div class="Polaris-BulkActions__Group Polaris-BulkActions__Group--largeScreen Polaris-BulkActions__Group--exited">
                            <div class="Polaris-BulkActions__ButtonGroupWrapper">
                                <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented" data-buttongroup-segmented="true">
                                    <div class="Polaris-ButtonGroup__Item">
                                        <div class="Polaris-CheckableButton">
                                            <label class="Polaris-Choice">
                                                <span class="Polaris-Choice__Control">
                                                  <span class="Polaris-Checkbox">
                                                    <input name="chekbox2" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="">
                                                    <span class="Polaris-Checkbox__Backdrop">
                                                    </span>
                                                    <span class="Polaris-Checkbox__Icon">
                                                      <span class="Polaris-Icon">
                                                        <span class="Polaris-Text--root Polaris-Text--visuallyHidden">
                                                        </span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                          <path d="M14.723 6.237a.94.94 0 0 1 .053 1.277l-5.366 6.193a.834.834 0 0 1-.611.293.83.83 0 0 1-.622-.264l-2.927-3.097a.94.94 0 0 1 0-1.278.82.82 0 0 1 1.207 0l2.297 2.43 4.763-5.498a.821.821 0 0 1 1.206-.056Z">
                                                          </path>
                                                        </svg>
                                                      </span>
                                                    </span>
                                                  </span>
                                                </span>
                                              </label>
                                            <span class="Polaris-CheckableButton__Label">0 selected</span>
                                        </div>
                                    </div>
                                    <div class="Polaris-ButtonGroup__Item">
                                        <div class="Polaris-BulkActions__BulkActionButton">
                                            <button class="Polaris-Button" type="button">
                                                <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Duplicate selected form(s)</span></span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="Polaris-ButtonGroup__Item">
                                        <div>
                                            <div>
                                                <div class="Polaris-BulkActions__BulkActionButton">
                                                    <button class="Polaris-Button" type="button" aria-controls="Polarispopover21" aria-owns="Polarispopover21" aria-expanded="false">
                                                        <span class="Polaris-Button__Content">
                                                            <span class="Polaris-Button__Text">More actions</span>
                                                            <span class="Polaris-Button__Icon">
                                                                <div class="">
                                                                    <span class="Polaris-Icon">
                                                                        <span class="Polaris-VisuallyHidden"></span>
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="M13.098 8h-6.196c-.751 0-1.172.754-.708 1.268l3.098 3.432c.36.399 1.055.399 1.416 0l3.098-3.433c.464-.513.043-1.267-.708-1.267Z"></path>
                                                                        </svg>
                                                                    </span>
                                                                </div>
                                                            </span>
                                                        </span>
                                                    </button>
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
            <div class="set_all_form"></div>
            
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
            </div>
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
                            <button class="Polaris-Modal-CloseButton" aria-label="Close">
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
                                        <div class="main_list_" data-val="1">
                                            <img src="../assets/images/form_img/build_from_scratch.jpg" alt="" />
                                            <p class="text_image_list firstone_ first_txt_image">Blank Form</p>
                                        </div>
                                        <div class="main_list_" data-val="2">
                                            <img src="../assets/images/form_img/contact_us_form.jpg" alt="" />
                                            <p class="text_image_list">Contact Form</p>
                                        </div>
                                        <div class="main_list_" data-val="3">
                                            <img src="../assets/images/form_img/registration_form_create.png" alt="" />
                                            <p class="text_image_list">Shopify Registration Form</p>
                                        </div>
                                        <div class="main_list_" data-val="4">
                                            <img src="../assets/images/form_img/floating_contact_us_form.jpg" alt="" />
                                            <p class="text_image_list">Floating contact form</p>
                                        </div>
                                        <!-- <div class="main_list_" data-val="5">
                                            <img src="../assets/images/form_img/multi_step_form.jpg" alt="" />
                                            <p class="text_image_list">Multi-Step Form</p>
                                        </div> -->
                                        <div class="main_list_" data-val="6">
                                            <img src="../assets/images/form_img/application_form.jpg" alt="" />
                                            <p class="text_image_list">Application form</p>
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
                                    <button class="Polaris-Button" type="button">
                                        <span class="Polaris-Button__Content"><span
                                                class="Polaris-Button__Text close2_new">Close</span></span>
                                    </button>
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
    <script>
    const ctx = document.getElementById('chart').getContext('2d');
         const chart = new Chart(ctx, {
             // The type of chart we want to create
             type: 'bar',
              data: {
               labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
               datasets: [{
                   label: '# of Votes',
                   data: [12, 19, 3, 5, 2, 3],
                   backgroundColor: [
                       'rgba(255, 99, 132, 0.2)',
                       'rgba(54, 162, 235, 0.2)',
                       'rgba(255, 206, 86, 0.2)',
                       'rgba(75, 192, 192, 0.2)',
                       'rgba(153, 102, 255, 0.2)',
                       'rgba(255, 159, 64, 0.2)'
                   ],
                   borderColor: [
                       'rgba(255,99,132,1)',
                       'rgba(54, 162, 235, 1)',
                       'rgba(255, 206, 86, 1)',
                       'rgba(75, 192, 192, 1)',
                       'rgba(153, 102, 255, 1)',
                       'rgba(255, 159, 64, 1)'
                   ],
                   borderWidth: 1
               }]
           },
             // Configuration options go here
             options: {
               responsive: true,
               scales: {
                 xAxes: [{ticks: {beginAtZero: true}, gridLines: {zeroLineColor: 'red', drawBorder: true, display: true, zeroLineWidth: 5}}],
                 // yAxes: [{ticks: {beginAtZero: true}, gridLines: {zeroLineColor: 'red', drawBorder: true, display: true, zeroLineWidth: 5}}]
                 
               }
             }
         });
        
         const ctx2 = document.getElementById('chart2').getContext('2d');
         const chart2 = new Chart(ctx2, {
             // The type of chart we want to create
             type: 'bar',
              data: {
               labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
               datasets: [{
                   label: '# of Votes',
                   data: [12, 19, 3, 5, 2, 3],
                   backgroundColor: [
                       'rgba(255, 99, 132, 0.2)',
                       'rgba(54, 162, 235, 0.2)',
                       'rgba(255, 206, 86, 0.2)',
                       'rgba(75, 192, 192, 0.2)',
                       'rgba(153, 102, 255, 0.2)',
                       'rgba(255, 159, 64, 0.2)'
                   ],
                   borderColor: [
                       'rgba(255,99,132,1)',
                       'rgba(54, 162, 235, 1)',
                       'rgba(255, 206, 86, 1)',
                       'rgba(75, 192, 192, 1)',
                       'rgba(153, 102, 255, 1)',
                       'rgba(255, 159, 64, 1)'
                   ],
                   borderWidth: 1
               }]
           },
             // Configuration options go here
             options: {
               responsive: true,
               scales: {
                 xAxes: [{ticks: {beginAtZero: true}, gridLines: {zeroLineColor: 'red', drawBorder: true, display: true, zeroLineWidth: 5}}],
                 // yAxes: [{ticks: {beginAtZero: true}, gridLines: {zeroLineColor: 'red', drawBorder: true, display: true, zeroLineWidth: 5}}]
                 
               }
             }
         });
         $(document).ready(function() {
            getAllForm();
    });
    </script>