<?php 
include_once('cls_header.php'); 

if (isset($_GET['shop']) && $_GET['shop'] != '') {
    include_once('dashboard_header.php');
} else {
    echo "Store not found";die;
}
?>

<div class="Polaris-Page">
    <div class="Polaris-Page-Header Polaris-Page-Header--hasActionMenu Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
        <div class="Polaris-Page-Header__Row padding_all_">
            <div class="Polaris-Page-Header__TitleWrapper">
                <h1 class="Polaris-Header-Title">Dashboard - Submissions</h1>
            </div>
        </div>
         <div class="Polaris-ResourceList__HeaderWrapper border-radi-botom-unset Polaris-ResourceList__HeaderWrapper--hasAlternateTool Polaris-ResourceList__HeaderWrapper--hasSelect Polaris-ResourceList__HeaderWrapper--isSticky">
            <div class="Polaris-ResourceList__HeaderContentWrapper">
                 <div class="Polaris-ResourceList__CheckableButtonWrapper">
                    <span class="Polaris-CheckableButton__Label" style="font-weight: 600; padding: 10px;">Form List</span>
                 </div>
            </div>
         </div>
         <div class="set_all_form_submissions"></div>
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
        getAllFormSubmissions();
    });
</script>
