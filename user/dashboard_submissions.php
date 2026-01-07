<?php 
include_once('cls_header.php'); 

if (isset($_GET['shop']) && $_GET['shop'] != '') {
    include_once('dashboard_header.php');
} else {
    echo "Store not found";die;
}
?>

<style>
/* Dashboard Submissions Page Styles - Shopify Polaris Design */
.dashboard-submissions-page {
    background-color: #f6f6f7;
    min-height: calc(100vh - 60px);
    padding: 20px;
}

.dashboard-submissions-page .Polaris-Page {
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-submissions-page .Polaris-Page-Header {
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    padding: 20px 24px;
    margin-bottom: 0;
    border-bottom: 1px solid #e1e3e5;
}

.dashboard-submissions-page .Polaris-Header-Title {
    font-size: 28px;
    font-weight: 600;
    color: #202223;
    margin: 0;
    letter-spacing: -0.02em;
}

.form-list-container {
    background: #ffffff;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
    overflow: visible;
    border: 1px solid #e1e3e5;
    border-top: none;
}

.form-list-header {
    background: #f9fafb;
    border-bottom: 1px solid #e1e3e5;
    padding: 12px 24px;
}

.form-list-header .Polaris-CheckableButton__Label {
    font-size: 13px;
    font-weight: 600;
    color: #202223;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.set_all_form_submissions {
    padding: 0;
}

/* Hide any checkboxes or toggle switches in submissions view */
.set_all_form_submissions .Polaris-Choice,
.set_all_form_submissions .svgicon,
.set_all_form_submissions .switch {
    display: none !important;
}

.set_all_form_submissions .Polaris-ResourceList__HeaderWrapper {
    border-bottom: 1px solid #e1e3e5;
    transition: background-color 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    overflow: visible;
    position: relative;
    background: #ffffff;
}

.set_all_form_submissions .Polaris-ResourceList__HeaderWrapper:last-child {
    border-bottom: none;
}

.set_all_form_submissions .Polaris-ResourceList__HeaderWrapper:hover {
    background-color: #f6f6f7;
}

.set_all_form_submissions .Polaris-ResourceList__HeaderContentWrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    flex: 1;
    gap: 20px;
    min-width: 0;
    overflow: visible;
}

.set_all_form_submissions .Polaris-ResourceList__CheckableButtonWrapper {
    flex: 1;
    min-width: 0;
}

.set_all_form_submissions .Polaris-CheckableButton {
    width: 100%;
}

.set_all_form_submissions .clsmain_form {
    display: flex;
    flex-direction: column;
    gap: 0px;
    margin: 0;
}

.set_all_form_submissions .sp-font-size {
    font-size: 15px;
    font-weight: 500;
    color: #202223;
    line-height: 1.5;
    margin: 0;
    letter-spacing: -0.01em;
}

.set_all_form_submissions .form-id-display {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    margin-top: 4px;
}

.set_all_form_submissions .form-id-display span:first-child {
    font-weight: 400;
    color: #6d7175;
    font-size: 13px;
}

.set_all_form_submissions .form-id-value {
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', 'Consolas', monospace;
    font-size: 12px;
    background: #f6f6f7;
    padding: 2px 8px;
    border-radius: 4px;
    color: #6d7175;
    cursor: pointer;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 400;
    border: 1px solid transparent;
}

.set_all_form_submissions .form-id-value:hover {
    background: #e1e3e5;
    color: #202223;
    border-color: #c9cccf;
}

.set_all_form_submissions .Polaris-ResourceList__AlternateToolWrapper {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-left: auto;
    flex-shrink: 0;
    justify-content: flex-end;
}

.set_all_form_submissions .main_right_ {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-left: auto;
    flex-shrink: 0;
    min-width: fit-content;
}

.set_all_form_submissions .indexButton {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
    white-space: nowrap;
    padding-right:35px;
}

.set_all_form_submissions .indexButton button {
    min-height: 36px;
    padding: 8px 16px;
    border: 1px solid #c9cccf;
    border-radius: 6px;
    background: #ffffff;
    color: #202223;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    flex-shrink: 0;
    min-width: fit-content;
    font-family: -apple-system, BlinkMacSystemFont, 'San Francisco', 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
}

.set_all_form_submissions .indexButton button:hover {
    background: #f6f6f7;
    border-color: #8c9196;
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
}

.set_all_form_submissions .indexButton button:active {
    background: #e1e3e5;
    box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
}

.set_all_form_submissions .indexButton button:hover {
    background: #f9fafb;
    border-color: #9ca3af;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.set_all_form_submissions .indexButton button a {
    color: #202223;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.set_all_form_submissions .indexButton button a:hover {
    color: #202223;
    text-decoration: none;
}

.set_all_form_submissions .indexButton button a:focus {
    outline: none;
}

/* Empty state */
.set_all_form_submissions:empty::before {
    content: "No forms found";
    display: block;
    padding: 40px 24px;
    text-align: center;
    color: #6b7280;
    font-size: 14px;
}

/* Loading state */
.form-list-loading {
    padding: 40px;
    text-align: center;
    color: #6b7280;
    font-size: 14px;
}

@media (max-width: 768px) {
    .dashboard-submissions-page {
        padding: 12px;
    }
    
    .dashboard-submissions-page .Polaris-Page-Header {
        padding: 16px;
    }
    
    .dashboard-submissions-page .Polaris-Header-Title {
        font-size: 24px;
    }
    
    .set_all_form_submissions .main_right_ {
        flex-direction: column;
        /* align-items: flex-start; */
        gap: 12px;
    }
    
    .set_all_form_submissions .indexButton {
        width: 100%;
    }
    
    .set_all_form_submissions .indexButton button {
        flex: 1;
    }
}
</style>

<div class="dashboard-submissions-page">
    <div class="Polaris-Page">
        <div class="Polaris-Page-Header Polaris-Page-Header--hasActionMenu Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
            <div class="Polaris-Page-Header__Row padding_all_">
                <div class="Polaris-Page-Header__TitleWrapper">
                    <h1 class="Polaris-Header-Title">Dashboard - Submissions</h1>
                </div>
            </div>
        </div>
        
        <div class="form-list-container">
            <div class="form-list-header">
                <div class="Polaris-ResourceList__CheckableButtonWrapper">
                    <span class="Polaris-CheckableButton__Label">Form List</span>
                </div>
            </div>
            
            <div class="set_all_form_submissions">
                <div class="form-list-loading">Loading forms...</div>
            </div>
        </div>
    </div>
</div>

<?php include_once('dashboard_footer.php'); ?>

<script>
    $(document).ready(function(){
        // Ensure store is defined or retrieved
        if(typeof store === 'undefined' || store === '') {
             var urlParams = new URLSearchParams(window.location.search);
             store = urlParams.get('shop');
        }
        console.log("Loading submissions for store:", store);
        
        // Load forms
        if (typeof getAllFormSubmissions === 'function') {
            getAllFormSubmissions();
        } else {
            // Fallback if function doesn't exist
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: { 'routine_name': 'getAllFormFunction', store: store, view_type: 'submissions_dashboard' },
                success: function (comeback) {
                    console.log("return set all elemnt submissions");
                    var comeback = JSON.parse(comeback);
                    if (comeback['code'] != undefined && comeback['code'] == '403') {
                        if (typeof redirect403 === 'function') {
                            redirect403();
                        }
                    } else {
                        $(".set_all_form_submissions").html(comeback['outcome'] || '<div class="form-list-loading">No forms found</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading forms:", error);
                    $(".set_all_form_submissions").html('<div class="form-list-loading">Error loading forms. Please try again.</div>');
                }
            });
        }
    });
</script>
