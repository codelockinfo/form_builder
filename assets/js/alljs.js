         
         $(document).ready(function(){
                  // code for create new form start
            $("#myBtn_new").click(function(){
                $("#myModal_new").css("display","block");
            });
            $(".close_new").click(function(){
                $("#myModal_new").css("display","none");
            });
            $(".close2_new").click(function(){
                $("#myModal_new").css("display","none");
            });
                  // code for create new form end
           $(".main_list_").click(function () {
                $(".first_txt_image").removeClass("first_txt_image");
                $(this).find(".text_image_list").addClass("first_txt_image");
                var getval = $(this).data("val");
                var formname = $(this).find(".text_image_list").html();
                $(".selectedType").val(getval);
                $(".formnamehide").val(formname);
            });
         });

   
        
    