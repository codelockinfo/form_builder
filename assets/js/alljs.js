         
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
                    $(".selectedType").attr("data-val",getval);
                    $(".formnamehide").attr("data-val",formname);
                    console.log(getval);

                });
         });

        //  var modal = document.getElementById("myModal_new");
        //  var btn = document.getElementById("myBtn_new");
        //  var span = document.getElementsByClassName("close_new")[0];
        //  var span2 = document.getElementsByClassName("close2_new")[0];
        //  btn.onclick = function () {
        //      modal.style.display = "block";
        //  };
        //  span.onclick = function () {
        //      modal.style.display = "none";
        //  };
        //  span2.onclick = function () {
        //      modal.style.display = "none";
        //  };
        //  window.onclick = function (event) {
        //      if (event.target == modal) {
        //          modal.style.display = "none";
        //      }
        //  };

   
        
    