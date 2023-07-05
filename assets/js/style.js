$(document).ready(function(){
    // mobile desktop icon box-shadow
    $(".view_icon li ").click(function(){
        $(".view_icon li ").removeClass("active");
        $(this).addClass("active");
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
    
        $(".backBtn").click(function(){
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
        } 	
        else{	
            $(".reset").addClass("hidden");	
        }	
        	
    });
    // Width of footer button in footer element
    $("#PolarisCheckbox7 ").change(function() {	
        if(this.checked) {	
            $(".alignment").addClass("hidden");	
        } 	
        else{	
            $(".alignment").removeClass("hidden");	
        }	
        	
    });
       
});