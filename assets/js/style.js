$(document).ready(function(){
    // mobile desktop icon box-shadow
        $(".view_icon li ").click(function(){
            $(".view_icon li ").removeClass("active");
            $(this).addClass("active");
            $(".preview-box").removeClass("mobile");
            $(".preview-box").removeClass("desktop");
            $(".preview-box").addClass($(this).data("id"));
        });
    

        $(".settingselect li .settingsbtn ").click(function(){
            $(".settingsbtn").removeClass("Polaris-Tabs__Tab--selected");
            $(this).addClass("Polaris-Tabs__Tab--selected");
        });

        $(".settingselect li").click( function() {
            var tabID = $(this).attr("data-tab");
            $("#tab-"+tabID).addClass("active").siblings().removeClass("active");
        });

        $("#required_login").change(function() {
            if(this.checked) {
                $(".required_message").removeClass("hidden");
            }
        else
            {
                $(".required_message").addClass("hidden");
            
            }
        });

        $('.selectval').hide();
        //show the first tab content
        $('#embedCode').show();
        $('#PolarisSelect18').change(function(){
            $('.selectval').hide();
            $('#' + $(this).val()).show();
            if($('#PolarisSelect18').val() == "lightbox"){
                    $("#lightbox2").removeClass("hidden");
            }
            else{
                    $("#lightbox2").addClass("hidden");
            }
        });

        $("#PolarisCheckbox27 ").change(function() {
            if(this.checked) {
                $(".shortcode").removeClass("hidden");
            } else
            {
                $(".shortcode").addClass("hidden");
            
            }
        });

        $('#PolarisSelect21').change(function(){
            $('.selectval').hide();
            $('#' + $(this).val()).show();
        });

        var input = $('.quentity');
        $('.plus').on('click', function () {
            var inputValue = input.val();
            input.val(parseInt(inputValue) + 1);
        });
        $('.min').on('click', function () {
            var inputValue = input.val();
            input.val(parseInt(inputValue) - 1);
        });

        var input = $('.hoursadd');
        $('.hoursadd').on('click', function () {
            var inputValue = input.val();
            input.val(parseInt(inputValue) + 1);
        });
        $('.houminus').on('click', function () {
            var inputValue = input.val();
            input.val(parseInt(inputValue) - 1);
        
        });

        var input = $('.weekadd');
        $('.weekplus').on('click', function () {
            var inputValue = input.val();
            input.val(parseInt(inputValue) + 1);
        });
        $('.weekminus').on('click', function () {
            var inputValue = input.val();
            input.val(parseInt(inputValue) - 1); 
        });
        $('.owl-carousel').owlCarousel({
            items:1,
            loop:false,
            margin:10,
            nav:false,
    	    mouseDrag: false,
        })

        $(document).on("click",".settingselect .Polaris-Tabs__TabContainer,.Polaris-Tabs__Panel .list-item",function(){
            console.log("HRRRRR");
            var slideTo = $(this).data("owl");
            console.log(slideTo);
            $('.owl-carousel').trigger('to.owl.carousel',  [slideTo, 40, true]);
        });
    
        $(document).on("click",".backBtn",function(){
            $('.owl-carousel').trigger('to.owl.carousel',  [0, 40, true]);
        });
        $('.editor').each(function(index,item){
            console.log(index);
            ClassicEditor.create(item)
            .catch( error => {
                console.error( error );
            } );
        
    });
        // Reset button  in footer element
        $("#PolarisCheckbox6 ").change(function() {	
            if(this.checked) {	
                $(".reset").removeClass("hidden");	
                $(".reset.classic-button").removeClass("hidden");	

            } 	
            else{	
                $(".reset").addClass("hidden");
                $(".reset.classic-button").addClass("hidden");		
            }	
                
        });
        // Width of footer button in footer element
        $("#PolarisCheckbox7 ").change(function() {	
            if(this.checked) {	
                $(".alignment").addClass("hidden");	
                $(".submit.classic-button").addClass("w100");	
            } 	
            else{	
                $(".alignment").removeClass("hidden");
                $(".submit.classic-button").removeClass("w100");	
            }		
        });

    // footer submit button alignment
        $(".chooseItem").click(function(event){
            event.preventDefault();
            $(".active").removeClass("active");
            $(this).addClass("active");
            $(".footer").removeClass("btnRight btnLeft btnCenter");
            var buttonClass = $(this).text();
            $(".footer").addClass("btn"+buttonClass);
        });

    //footer submit button text change
        $(document).on('keydown, keyup','#PolarisTextField17', function () {
            var addText = $(this).val();
            $(' .submit.classic-button').html(addText);
        }); 
        $(document).on("change "," #PolarisCheckbox13 " ,function() {	
            if(this.checked) {	
                $(".hideLabel").removeClass("hidden");	
            } 	
            else{	
                $(".hideLabel").addClass("hidden");	
            }	    
        });
        $(document).on("change "," #PolarisCheckbox15 " ,function() {	
            if(this.checked) {	
                $(".required_Content").removeClass("hidden");	

            } 	
            else{	
                $(".required_Content").addClass("hidden");	
            }	
                
        });

        $(document).click(".select_ICon" , function(){
            $(this).find(" .pickerList").addClass("show");
        });
        
        $(document).click(".close_icon" , function(){

            $(this).find(".pickerList").removeClass("show");
        });
        // Dropdown hide show checkbox
        $(document).on("change "," #PolarisCheckbox3 " ,function() {	
            if(this.checked) {	
                $(".hideLabel").removeClass("hidden");	

            } 	
            else{	
                $(".hideLabel").addClass("hidden");	
            }	
                
        });
        $(document).on("change "," #PolarisCheckbox5 " ,function() {	
            if(this.checked) {	
                $(".hideRequired").removeClass("hidden");	

            } 	
            else{	
                $(".hideRequired").addClass("hidden");	
            }	
                
        });
        // $(document).click(".select_ICon1" , function(){
        //     $(this).find(" .pickerList").addClass("show");
         // });
       
       
        $(document).on("click",".Polaris-Tabs__Panel .list-item",function(){
            setTimeout(function(){
                $('.selectFile').select2();
            },100);   
        });

        // dropdown select
     i=1;
    	$(document).on("click","#add",function(){
        	i++;
            console.log("clickee");
            var textarea_value = $(".mainskill").val();
            console.log(textarea_value);
            if(textarea_value.length >= 1){
             var inputValue = $('.mainskill').val(); 
             $('#optionText').append('<div id="main'+i+'" class="addskildy"> <div style="display:flex;margin-bottom: 5px;" > <input type="text" class="mainskill" style="width:85%;" id="Skill'+i+'" name="Skill[' + inputValue + ']" ><button type="button" name="remove" id="Skillremove'+i+'" class="btn_add11" style="width:15%;padding: 10px 20px;">X</button></div>   </div>');
            }
            else{
             alert("Skill does not value");
            }
            var optionHtml = '';
            $( ".mainskill" ).each(function( index ) {
                console.log("value");
                var optionval =$(this).val();
                console.log(optionval);
             
                    console.log("select");
                    console.log(optionval); 
                    optionHtml += "<option>"+optionval+"</option>";

        
            });
            $('#optionSelect').html(optionHtml);
    	});
        $(document).on('click', '.btn_add11', function(){
            console.log("bdbf");
                 var button_id = $(this).attr("id"); 
                 console.log(button_id);
               $(this).closest(".addskildy").remove();
                    
      
       
    });
       

        // $(document).on("click","#PolarisSelect2",function(){
        //     var textarea_value = $("#PolarisTextField6").val();
        //     var line = textarea_value.replace(')', ') <br>');
        //     console.log(textarea_value);
        //     console.log(line);
            
        //     $('#PolarisSelect2').html(new Option(textarea_value, line));
        // });
});


