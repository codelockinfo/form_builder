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
            }else{
                $(".required_message").addClass("hidden");
            }
        });
        $(document).on("change",".confirmpass" ,function(){
            if(this.checked) {
                $(".conpass").removeClass("hidden");
            }else{   
                $(".conpass").addClass("hidden");
            }
        });

        // $(document).on("change",".pass_required" ,function(){
        //     if(this.checked) {
        //         $(".Requiredpass").removeClass("hidden");
        //     }
        //     else
        //     {   
        //         $(".Requiredpass").addClass("hidden");
        //     }
        // });

        // $(document).on("change",".passLabel" ,function(){
        //     if(this.checked) {
        //         $(".passhideLabel").removeClass("hidden");
        //     }
        //     else
        //     {   
        //         $(".passhideLabel").addClass("hidden");
        //     }
        // });

        // $(document).on("change",".passLimitcar" ,function(){
        //     $mainContainer = $(this).closest(".container").find(".limitCaracters");

        //     if(this.checked) {

        //         $mainContainer.removeClass("hidden");
        //     }
        //     else
        //     {   
        //         $mainContainer.addClass("hidden");
        //     }
        // });

        $('.selectval').hide();
        //show the first tab content
        $('#embedCode').show();

        $('#PolarisSelect18').change(function(){
            $('.selectval').hide();
            $('#' + $(this).val()).show();
            if($('#PolarisSelect18').val() == "lightbox"){
                $("#lightbox2").removeClass("hidden");
            }else{
                $("#lightbox2").addClass("hidden");
            }
        });

        $("#PolarisCheckbox27 ").change(function() {
            if(this.checked) {
                $(".shortcode").removeClass("hidden");
            }else{
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
            // console.log("HRRRRR");
            var slideTo = $(this).data("owl");
            // console.log(slideTo);
            $('.owl-carousel').trigger('to.owl.carousel',  [slideTo, 40, true]);
        });
    
        $(document).on("click",".backBtn",function(){
            $('.owl-carousel').trigger('to.owl.carousel',  [0, 40, true]);
        });
        $('.editor').each(function(index,item){
            // console.log(index);
            ClassicEditor.create(item)
            .catch( error => {
                console.error( error );
        });
        
    });
        // Reset button  in footer element
        // $("#PolarisCheckbox6 ").change(function() {	
        //     if(this.checked) {	
        //         $(".reset").removeClass("hidden");	
        //         $(".reset.classic-button").removeClass("hidden");	
        //     } 	
        //     else{	
        //         $(".reset").addClass("hidden");
        //         $(".reset.classic-button").addClass("hidden");		
        //     }	        
        // });

        // column width changes 
        
        // $(document).on('click','.chooseItems .chooseItem ', function(){
        //     $('.chooseItems .chooseItem ').removeClass('active');
        //     $(this).addClass('active');
        //     var content = $('.chooseItem.active').text();
        //     var additionalClassName = "choose" + content;
        //     $(".form_content").find(".contact-form  .g-container .code-form-control").addClass(additionalClassName);
        // });

        // Width of footer button in footer element
        // $("#PolarisCheckbox7 ").change(function() {	
        //     if(this.checked) {	
        //         $(".alignment").addClass("hidden");	
        //         $(".classic-button").addClass("w100");	
        //     } 	
        //     else{	
        //         $(".alignment").removeClass("hidden");
        //         $(".classic-button").removeClass("w100");	
        //     }		
        // });

    // footer submit button alignment
        // $(".chooseItem").click(function(event){
        //     event.preventDefault();
        //     $(".active").removeClass("active");
        //     $(this).addClass("active");
        //     $(".footer").removeClass("btnRight btnLeft btnCenter");
        //     var buttonClass = $(this).text();
        //     $(".footer").addClass("btn"+buttonClass);
        // });

    //footer submit button text change
        $(document).on('keydown, keyup','#PolarisTextField17', function () {
            var addText = $(this).val();
            $(' .submit.classic-button').html(addText);
        }); 
        $(document).on("change "," #PolarisCheckbox13 " ,function() {	
            if(this.checked) {	
                $(".hideLabel").removeClass("hidden");	
            }else{	
                $(".hideLabel").addClass("hidden");	
            }	    
        });
        $(document).on("change "," #PolarisCheckbox15 " ,function() {	
            if(this.checked) {	
                $(".required_Content").removeClass("hidden");	
            }else{	
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
            }else{	
                $(".hideLabel").addClass("hidden");	
            }	
                
        });
        $(document).on("change "," #PolarisCheckbox5 " ,function() {	
            if(this.checked) {	
                $(".hideRequired").removeClass("hidden");	
            }else{	
                $(".hideRequired").addClass("hidden");	
            }	
                
        });
       
        $(document).on("click",".Polaris-Tabs__Panel .list-item",function(){
            setTimeout(function(){
                $('.selectFile').select2();
            },100);   
        });

        // dropdown select
     i=1;
    	$(document).on("click","#add",function(){
        	i++;
            // console.log("clickee");
            var textarea_value = $(".mainskill").val();
            // console.log(textarea_value);
            if(textarea_value.length >= 1){
             var inputValue = $('.mainskill').val(); 
             $('#optionText').append('<div id="main'+i+'" class="addskildy"> <div style="display:flex;margin-bottom: 5px;" > <input type="text" class="mainskill" style="width:85%;" id="Skill'+i+'" name="Skill[' + inputValue + ']" ><button type="button" name="remove" id="Skillremove'+i+'" class="btn_add11" style="width:15%;padding: 10px 20px;">X</button></div>   </div>');
            }else{
             alert("Skill does not value");
            }
            var optionHtml = '';
            $( ".mainskill" ).each(function( index ) {
                // console.log("value");
                var optionval =$(this).val();
                optionHtml += "<option>"+optionval+"</option>";
            });
            $('#optionSelect').html(optionHtml);
    	});
        $(document).on('click', '.btn_add11', function(){
            var button_id = $(this).attr("id"); 
            $(this).closest(".addskildy").remove();
        });
});

// today

$(document).ready(function () {
    // $(".code-form-control").each(function (index) {           
    //     $(this).css("display", "none");
    // });
    // var formId = $(".element_start").find(".owl-item #tab-1 .tabContent .elementroot .selected_element_set").html();
    // console.log(formId); 
    // var newId1 = (formId - 1);
    // console.log(newId1); 
    // var formId1 = "element" + newId;
    // console.log(formId1);
    // var elementWithDataId = $(".preview-box").find(".g-container .code-form-control[data-id='" + formId1 + "']");
    // elementWithDataId.css("display", "block");     
    $(document).on("click", ".Polaris-Tabs__Panel .list-item", function () {
        var elementId = $(this).data("elementid");
        console.log(elementId); 
        var newId = (elementId - 1);
        console.log(newId); 
        var elementId1 = "element" + newId;
        console.log(elementId1);
        var elementWithDataId = $(".preview-box").find(".g-container .code-form-control[data-id='" + elementId1 + "']");
        elementWithDataId.css("display", "block");     
    });
});
// width change
$(document).on("click",".chooseItems .chooseItem-align",function(){
    $('.chooseItem-align').removeClass("active");
    $(this).addClass("active");
    $dataValue = $(this).attr("data-value"); 
    $(".forFooterAlign").removeClass("align-left align-center align-right").addClass($dataValue);
    $inputFormate = $(this).closest(".form-control").find(".footer-button__alignment");
    if($inputFormate.length > 0){
        $inputFormate.val($dataValue);
    }
});
$(document).on("click",".chooseItems .chooseItem-noperline",function(){
    $('.chooseItem-noperline').removeClass("active");
    $(this).addClass("active");
    $dataValue = $(this).attr("data-value"); 
    $mainContainer = $(this).closest(".container").attr("class");
    var classArray = $mainContainer.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $("."+containerClass).find(".input_no-perline").val($dataValue);
    $(".block-container").find("."+containerClass+" li").removeClass("option-1-column option-2-column option-3-column option-4-column option-5-column").addClass("option-"+$dataValue+"-column");
});
$(document).on("click",".chooseItems .chooseItem ",function(){
    $('.chooseItem').removeClass("active");
    $(this).addClass("active");
    $dataValue = $(this).attr("data-value");    
    $mainContainer = $(this).closest(".container").attr("class");
    var classArray = $mainContainer.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    
    $columnWidth = $(this).closest(".form-control").find("input");
    console.log($columnWidth.length);
    if($columnWidth.length > 0){
        console.log(containerClass);
        $columnWidth.val($dataValue);
        $(".block-container ."+containerClass).removeClass("layout-1-column layout-2-column layout-3-column").addClass("layout-" + $dataValue + "-column");
    }
    $inputFormate = $(this).closest(".form-control").find(".input_formate");
    if($inputFormate.length > 0){
        $inputFormate.val($dataValue);
    }
  }); 

$(document).on('keydown, keyup','.Polaris-TextField__Input', function () {
    console.log("keydown, keyup");
    $mainContainerClass = $(this).closest(".container");
    $classArray = $mainContainerClass.attr("class").split(" ");
    var containerClass = $classArray.find(className => className.startsWith("container_"));
    $mainContainer = $(".block-container");

    $inputVal = $(this).val();
    $attrName = $(this).attr('name');
    $nameExlode = "";
    if($attrName != undefined){
        $nameExlode = $attrName.split("__");
    }
    if($nameExlode[1] == "label"){
        $("."+$attrName).html($inputVal);
    }else if($nameExlode[1] == "placeholder"){
        if ($nameExlode[0].includes("file")) {
            $("."+$attrName).html($inputVal);
        }else if($nameExlode[0].includes("country")) {
            $mainContainer.find('.'+containerClass+' option[value=""]').html($inputVal);
        }else{
            $("."+$attrName).attr('placeholder', $inputVal);
        }
    }else if($nameExlode[1] == "description"){
        $("."+$attrName).html($inputVal);
    }else if($nameExlode[1] == "submittext"){
        $("."+$attrName).text($inputVal);
    }else if($nameExlode[1] == "resetbuttontext"){
        $("."+$attrName).text($inputVal);
    }else if($nameExlode[1] == "limitcharactervalue"){
        $("."+$nameExlode[0]+"__placeholder").attr("maxlength",$inputVal);
    }else if($nameExlode[1] == "html-code"){
        $("."+$attrName).html($inputVal);
    }else if($nameExlode[1] == "checkboxoption"){
        $preline = $mainContainerClass.find(".input_no-perline").val();
        var options = $inputVal.split(",");
        var htmlContent = "";
        options.forEach(function(option, index) {
            var optionValue = option.trim();
            htmlContent +=`<li class="globo-list-control option-${$preline}-column">
                                <div class="checkbox-wrapper">
                                    <input class="checkbox-input ${$nameExlode[0]}__checkbox" id="false-checkbox-${index + 1}-${optionValue}-" type="checkbox" data-type="checkbox" name="checkbox-${index + 1}[]" value="${optionValue}">
                                    <label class="checkbox-label globo-option ${$nameExlode[0]}__checkbox" for="false-checkbox-${index + 1}-${optionValue}-">${optionValue}</label>
                                </div>
                            </li>`;
        });
        $("."+$attrName).html(htmlContent); 
    }else if($nameExlode[1] == "radiooption"){
        $preline = $mainContainerClass.find(".input_no-perline").val();
        var options = $inputVal.split(",");
        var radioHtml = "";
        options.forEach(function(option, index) {
            var optionValue = option.trim();
            if(optionValue !== ""){
                radioHtml +=`
                <li class="globo-list-control option-${$preline}-column">
                    <div class="radio-wrapper">
                        <input class="radio-input  ${$nameExlode[0]}__radio" id="false-radio-${index + 1}-${optionValue}-" type="radio" data-type="radio" name="radio-1" value="${optionValue}">
                        <label class="radio-label globo-option ${$nameExlode[0]}__radio" for="false-radio-${index + 1}-${optionValue}-">${optionValue}</label>
                    </div>
                </li>`;
            }
        });
        $("."+$attrName).html(radioHtml); 
    }else if($nameExlode[1] == "title"){
        console.log("Header title");
        $(".formHeader .title").html($inputVal);
    }else if($nameExlode[1] == "buttontext"){
        $("."+$attrName).html($inputVal);
        $("."+$attrName).removeClass("hidden");
        if($inputVal == ""){
            $("."+$attrName).addClass("hidden");
        }
    }else if($nameExlode[1] == "dropoption"){
        console.log($attrName);
        var options = $inputVal.split(",");
        var dropdownHtml = "";
        options.forEach(function(option, index) {
            var optionValue = option.trim();
            if(optionValue !== ""){
                dropdownHtml +=`<option value="${optionValue}" >${optionValue}</option>`;
            }
        });
        $mainContainer.find('.'+containerClass+' select').html(dropdownHtml); 
    }
});

$(document).on("change ",".showHeader" ,function() {
  console.log("showHeader");
  $(".formHeader").addClass("hidden");
  if(this.checked) {	
    $(".formHeader").removeClass("hidden");
  }
});

$(document).on("change ",".resetButton" ,function() {
    if(this.checked) {	
        $(".reset").removeClass("hidden");	
        $(".reset.classic-button").removeClass("hidden");	  
    }else{	
        $(".reset").addClass("hidden");
        $(".reset.classic-button").addClass("hidden");		
    }	
});

$(document).on('keydown, keyup',".ck-content" ,function() {
    $inputVal = $(this).html();
    $footerData = $(this).closest(".tabContent").hasClass("footerData");

    if($footerData){
        $(".footer-data__footerdescription").html($inputVal);
    }
});
$(document).on("change",".passLimitcar" ,function(){
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));

    $mainContainer = $(this).closest(".container").find(".limitCaracters");
    $inputVal = $mainContainer.find(".Polaris-TextField__Input").val();
    if(this.checked) {
        $mainContainer.removeClass("hidden");
        $(".block-container").find("."+containerClass + " .classic-input").attr("maxlength",$inputVal);
    }else{   
        $mainContainer.addClass("hidden");
        $(".block-container").find("."+containerClass + " .classic-input").attr("maxlength",'');
    }
});

$(document).on("change",".hideLabel" ,function(){
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $classExlode = containerClass.split("_");

    $mainContainer = $(".block-container");
    $requiredCheckbox = $("input[name='" + $classExlode[1] + "__required']");
    $showRequiredLabel = $("input[name='" + $classExlode[1] + "__required-hidelabel']");
    if(this.checked) {
        $(".passhideLabel").removeClass("hidden");
        $mainContainer.find("." +containerClass +" .label-content").addClass("hidden");
        $mainContainer.find("." +containerClass +" .text-smaller").addClass("hidden");
        if($showRequiredLabel.prop('checked') && $requiredCheckbox.prop('checked')) {
            $mainContainer.find("." +containerClass +" .text-smaller").removeClass("hidden");
        }
    }else{   
        $(".passhideLabel").addClass("hidden");
        $mainContainer.find("." +containerClass +" .label-content").removeClass("hidden");
        if($requiredCheckbox.prop('checked')) {
            $mainContainer.find("." +containerClass+" .text-smaller").removeClass("hidden");
        }
    }
});

$(document).on("change",".keePositionLabel" ,function(){
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));

    if(this.checked) {
        $(".block-container").find("."+ containerClass + " .classic-label").addClass("position--label");
    }else{
        $(".block-container").find("."+ containerClass + " .classic-label").removeClass("position--label");
    }
});

$(document).on("change",".requiredCheck" ,function(){
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $classExlode = containerClass.split("_");
    console.log(containerClass);

    $mainContainer = $(".block-container");
    $showRequiredLabel = $("input[name='" + $classExlode[1] + "__required-hidelabel']");
    $hideLabel = $("input[name='" + $classExlode[1] + "__hidelabel']");

    if(this.checked) {
        if($hideLabel.prop('checked')) {
            if($showRequiredLabel.prop('checked')) {
                $mainContainer.find("." +containerClass +" .text-smaller").removeClass("hidden");
            }
        }else{
            $mainContainer.find("." +containerClass +" .text-smaller").removeClass("hidden");
        }
        $(this).closest("."+containerClass).find(".Requiredpass").removeClass("hidden");
    }else{   
        $mainContainer.find("." +containerClass +" .text-smaller").addClass("hidden");
        $(this).closest("."+containerClass).find(".Requiredpass").addClass("hidden");
    }
});

$(document).on("change",".showRequireHideLabel" ,function(){
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $classExlode = containerClass.split("_");
    console.log(containerClass);

    $mainContainer = $(".block-container");
    $requiredCheckbox = $("input[name='" + $classExlode[1] + "__required']");
    $hideLabel = $("input[name='" + $classExlode[1] + "__hidelabel']");
    if(this.checked) {
        if($requiredCheckbox.prop('checked')) {
            $mainContainer.find("." +containerClass +" .text-smaller").removeClass("hidden");
        }
    }else{
        if($hideLabel.prop('checked')){
            $(".block-container").find("." +containerClass +" .text-smaller").addClass("hidden");
        }
    }
});


$(document).on("change",".fullFooterButton" ,function(){
    if(this.checked) {
        $(".footer .classic-button").addClass("w100");	
    }else{
        $(".footer .classic-button").removeClass("w100");	 
    }
});
$(document).on("change ",".defaultSelectAcceptterms" ,function() {
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $classExlode = containerClass.split("_");

    $mainContainer = $(".block-container");
    $mainContainer.find("." + $classExlode[1] +"__acceptterms").prop("checked", false);	 
    if(this.checked) {
        $mainContainer.find("." + $classExlode[1] +"__acceptterms").prop("checked", true);	 
    }
});
$(document).on("change",".selectDefaultCountry" ,function(){
    console.log("change country");
    $selectVal = $(this).val();
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $mainContainer = $(".block-container");
    $mainContainer.find('.'+containerClass+' select').val($selectVal).change();

});
$(document).on('keydown, keyup',".dropdownDefaultOption" ,function() {
    console.log("CHNAGE KEYUP KEY DOWN");
    $inputVal = $(this).val();
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $mainContainer = $(".block-container");
    if($inputVal == ""){
        $mainContainer.find('.'+containerClass+' select').val("").change();
    }else{
        $mainContainer.find('.'+containerClass+' select').val($inputVal).change();
    }

});
$(document).on('keydown, keyup',".checkboxDefaultOption" ,function() {
    console.log("CHNAGE KEYUP KEY DOWN");
    $inputVal = $(this).val();
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $mainContainer = $(".block-container");
    $mainContainer.find("."+containerClass + " input").prop('checked', false);
    var $valuesToCheck = $mainContainer.find("."+containerClass + " li");
    $valuesToCheck.each(function() {
        $(this).find('input[value="' + $inputVal + '"]').prop('checked', true);

    });
});
$(document).on("change",".allowMultipleCheckbox" ,function(){
    console.log("...allowMultipleCheckbox");
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $mainContainer = $(".block-container");

    if(this.checked) {
       $mainContainer.find('.'+containerClass+' input').attr('name','files[]');
       $mainContainer.find('.'+containerClass+' input').attr('multiple','multiple');
    }else{
        $mainContainer.find('.'+containerClass+' input').removeAttr('name');
        $mainContainer.find('.'+containerClass+' input').removeAttr('multiple');
    }
});
// File 

window.onload = (event) => {
    const fileButton = document.getElementById('fileButton');
    const fileInput = document.getElementById('fileimage');
    const imgContainer = document.getElementById('imgContainer');
    const uploadText = document.getElementById('uploadText');

    fileButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        imgContainer.innerHTML = ''; // Clear previous previews
        const files = this.files;
        if (files.length > 0) {
            uploadText.style.display = 'none';
            fileButton.style.display = 'none';
        } else {
            uploadText.style.display = 'block';
            fileButton.style.display = 'block';
        }
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const imgPreviewWrapper = document.createElement('div');
                imgPreviewWrapper.classList.add('img-preview-wrapper');

                const imgPreview = document.createElement('img');
                imgPreview.classList.add('img-preview');
                if (file.type.startsWith('image/')) {
                    imgPreview.src = event.target.result;
                } else {
                    imgPreview.src = "https://pngfre.com/wp-content/uploads/Folder-1.png";
                }

                const closeButton = document.createElement('button');
                closeButton.classList.add('close-button');
                closeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                closeButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    imgContainer.removeChild(imgPreviewWrapper);
                    if (imgContainer.children.length === 0) {
                        uploadText.style.display = 'block';
                        fileButton.style.display = 'block';
                        fileInput.value = '';
                    }
                });

                const fileName = document.createElement('p');
                fileName.classList.add('file-name');
                fileName.textContent = file.name;

                imgPreviewWrapper.appendChild(imgPreview);
                imgPreviewWrapper.appendChild(closeButton);
                imgPreviewWrapper.appendChild(fileName);
                imgContainer.appendChild(imgPreviewWrapper);
            }
            reader.readAsDataURL(file);
        });
    });
};

// File 