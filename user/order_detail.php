<?php
include_once('cls_header.php');
include_once('dashboard_header.php'); 
if (isset($_GET['order_id']) && $_GET['order_id'] != '') {
    $order_id = $_GET['order_id'];
}
?>
 <style>
   .mce-notification{
   
       display:none;
   }
   
    </style>
<div class="Polaris-Page">
    <div class="Polaris-Page__Content">
        <div class="Polaris-Layout">
            <div class="Polaris-Layout__AnnotatedSection">
                <div class="Polaris-Layout__AnnotationWrapper">
                    <div class="Polaris-Layout__AnnotationContent">
                        <div class="Polaris-Card">
                            <div class="Polaris-Card__Section">
                                <div class="Polaris-Card__Header"></div>
                                    <div class="Polaris-Layout maincontainer">
<!--                                        <center><h2 class="Polaris-Heading">Order datils</h2></center>-->
                                        <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary">
                                            <div class="Polaris-Card block1">                                            
                                                 
                                            </div>
                                            <div class="Polaris-Card block2">                                            
                                               
                                            </div>
                                        </div>
                                        <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary block3">
                                          
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
<script>
$(document).ready(function(){
var order_id = "<?php echo $order_id; ?>";
OrderDetails(order_id);
var store = "<?php echo $store; ?>";
OrderDetails(order_id,store);

})
</script>
<?php  include_once('dashboard_footer.php'); ?>