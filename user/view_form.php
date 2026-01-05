<?php 
include_once('cls_header.php'); 
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : 0;
?>
<body style="padding: 20px;">
    <div class="form-container" style="max-width: 800px; margin: 0 auto;">
         <div class="preview-box">
            <div class="contact-form">
               <div class="code-form-app boxed-layout">
                   <!-- Form will be loaded here via JS -->
               </div>
            </div>
         </div>
    </div>
    
    <input type="hidden" class="formid" value="<?php echo $form_id; ?>">
    <script>
        $(document).ready(function(){
             var form_id = "<?php echo $form_id; ?>";
             if(form_id > 0){
                 get_selected_elements(form_id);
             }
        });
    </script>
</body>
</html>
