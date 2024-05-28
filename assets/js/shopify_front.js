
console.log("shopify front filefff");
$(document).ready(function(){
    $('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', 'https://codelocksolutions.com/form_builder/assets/css/custom_front.css') );
    $('head').append( $('<script  type="text/javascript"/>').attr('src','https://codelocksolutions.com/form_builder/assets/css/jquery3.6.4.min.js') );
    var shop = Shopify.shop;
    function loading_show($selector) {
        $($selector).addClass("Button--loading").html("").attr('disabled', 'disabled');
    }
    function check_app_status(){
          $.ajax({
                url: "https://codelocksolutions.com/form_builder/user/ajax_call.php",
                type: "POST",
                dataType: 'json',
                data: {'routine_name': 'check_app_status' ,'store': shop},
                beforeSend: function () {
                },
                success: function (comeback) {
                    console.log(comeback);
                    if (comeback['outcome'] == 'true') {
                      if(comeback['data'] == '1'){
                        console.log("Formbuilder app is disabled");
                      }else{
                        console.log("Formbuilder app is disabled");
                      }
                    } else {
                        console.log("Something went wrong with Formbuilder app");       
                    } 
               }
           });
    }
    
    check_app_status();
  
  
    $(document).on("click",".submit.action",function (e) {
            e.preventDefault();
            console.log("HII");
            var form_data = $(".get_selected_elements")[0];
            var form_data = new FormData(form_data);
            // form_data.append('images',$("#ImagePreview").attr("src"));
            form_data.append('store',store); 
            form_data.append('routine_name','addformdata');      
            $.ajax({
                url: "https://codelocksolutions.com/form_builder/user/ajax_call.php",
                type: "POST",
                dataType: 'json',
                data: form_data,
                beforeSend: function () {
                    loading_show('.save_loader_show');
                },
                success: function (comeback) {
                    console.log(comeback);
                    loading_hide('save_loader_show', 'Save');
                }
            })
    });
});       
             
