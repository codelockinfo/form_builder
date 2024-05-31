"use strict";
var CLS_LOADER = '<svg viewBox="0 0 20 20" class="Polaris-Spinner Polaris-Spinner--colorInkLightest Polaris-Spinner--sizeSmall" aria-label="Loading" role="status"><path d="M7.229 1.173a9.25 9.25 0 1 0 11.655 11.412 1.25 1.25 0 1 0-2.4-.698 6.75 6.75 0 1 1-8.506-8.329 1.25 1.25 0 1 0-.75-2.385z"></path></svg>';
var CLS_DELETE = '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M16 6a1 1 0 1 1 0 2h-1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8H4a1 1 0 1 1 0-2h12zM9 4a1 1 0 1 1 0-2h2a1 1 0 1 1 0 2H9zm2 12h2V8h-2v8zm-4 0h2V8H7v8z" fill="#000" fill-rule="evenodd"></path></svg>';
var CLS_MINUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 80 80" focusable="false" aria-hidden="true"><path d="M39.769,0C17.8,0,0,17.8,0,39.768c0,21.956,17.8,39.768,39.769,39.768   c21.965,0,39.768-17.812,39.768-39.768C79.536,17.8,61.733,0,39.769,0z M13.261,45.07V34.466h53.014V45.07H13.261z" fill-rule="evenodd" fill="#DE3618"></path></svg>';
var CLS_PLUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M17 9h-6V3a1 1 0 1 0-2 0v6H3a1 1 0 1 0 0 2h6v6a1 1 0 1 0 2 0v-6h6a1 1 0 1 0 0-2" fill-rule="evenodd"></path></svg>';
var CLS_CIRCLE_MINUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 80 80" focusable="false" aria-hidden="true"><path d="M39.769,0C17.8,0,0,17.8,0,39.768c0,21.956,17.8,39.768,39.769,39.768   c21.965,0,39.768-17.812,39.768-39.768C79.536,17.8,61.733,0,39.769,0z M13.261,45.07V34.466h53.014V45.07H13.261z" fill-rule="evenodd" fill="#DE3618"></path></svg>';
var CLS_CIRCLE_PLUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 510 510" focusable="false" aria-hidden="true"><path d="M255,0C114.75,0,0,114.75,0,255s114.75,255,255,255s255-114.75,255-255S395.25,0,255,0z M382.5,280.5h-102v102h-51v-102    h-102v-51h102v-102h51v102h102V280.5z" fill-rule="evenodd" fill="#3f4eae"></path></svg>';
var BACKTO = 0;
// var store = (window.location != window.parent.location)
//             ? document.referrer
//             : document.location.href; 
// //             var url = store.split("?");
// //             var store = url[2];
        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }
        function getCookie(cname) {
            cname = (cname != undefined) ? cname : 'flash_message';
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) { 
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
        function flashNotice($message, $class) {
            $class = ($class != undefined) ? $class : '';

            var flashmessageHtml = '<div class="inline-flash-wrapper animated bounceInUp inline-flash-wrapper--is-visible ourFlashmessage"><div class="inline-flash ' + $class + '  "><p class="inline-flash__message">' + $message + '</p></div></div>';

            if ($('.ourFlashmessage').length) {
                $('.ourFlashmessage').remove();
            }
            $("body").append(flashmessageHtml);

            setTimeout(function () {
                if ($('.ourFlashmessage').length) {
                    $('.ourFlashmessage').remove();
                }
            }, 3000);
        }
        function changeTab(evt, id) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("Polaris-Tabs_tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("Polaris-Tabs__Tab");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace("Polaris-Tabs__Tab--selected", "");
            }
            document.getElementById(id).style.display = "block";
            evt.currentTarget.className += " Polaris-Tabs__Tab--selected";
        }
        function loading_show($selector) {
            $($selector).addClass("Polaris-Button--loading").html('<span class="Polaris-Button__Content"><span class="Polaris-Button__Spinner">' + CLS_LOADER + '</span><span>Loading</span></span>').fadeIn('fast').attr('disabled', 'disabled');
        }
        function loading_hide($selector, $buttonName, $buttonIcon) {
            if ($buttonIcon != undefined) {
                $buttonIcon = '<span class="Polaris-Button__Icon"><span class="Polaris-Icon">' + $buttonIcon + '</span></span>'
            } else {
                $buttonIcon = '';
            }
            $($selector).removeClass("Polaris-Button--loading").html('<span class="Polaris-Button__Content">' + $buttonIcon + '<span>' + $buttonName + '</span></span>').removeAttr("disabled");
        }
        function table_loader(selector, colSpan) {
            $(selector).html('<tr><td colspan="' + colSpan + '" style="text-align:center;"><div class="loader-spinner"><svg viewBox="0 0 44 44" class="Polaris-Spinner Polaris-Spinner--colorTeal Polaris-Spinner--sizeLarge" role="status"><path d="M15.542 1.487A21.507 21.507 0 0 0 .5 22c0 11.874 9.626 21.5 21.5 21.5 9.847 0 18.364-6.675 20.809-16.072a1.5 1.5 0 0 0-2.904-.756C37.803 34.755 30.473 40.5 22 40.5 11.783 40.5 3.5 32.217 3.5 22c0-8.137 5.3-15.247 12.942-17.65a1.5 1.5 0 1 0-.9-2.863z"></path></svg></div></td></tr>')
        }
        function redirect403() {
            window.location = "https://www.shopify.com/admin/apps";
        }
        function removeFromTable(tableName,ID,id,pageno, tableId,api_name ,is_delete) {
            var is_delete = (is_delete == undefined) ? 'Record' : is_delete;
            var Ajaxdelete = function Ajaxdelete() {
                    var el = is_delete;
                $.ajax({
                    url: "ajax_call.php",
                    type: "post",
                    dataType: "json",
                    data: {routine_name: 'remove_from_table', store: store, table_name: tableName, id: id,ID : ID,api_name :api_name},
                    beforeSend: function () { 
                        loading_show('.save_loader_show' + ID);   
                    },
                    success: function (response) {
                            loading_hide('save_loader_show'+ID,'',CLS_DELETE);
                        if (response['result'] == 'success') {
                            if (pageno == undefined || pageno < 0 || response['total_record'] <= 0) {
                                setCookie('flash_message', response['message'], 2);
                                location.reload();
                            } else if (pageno > 0) {
                                $(is_delete).closest("tr").css("background", "tomato");
                                $(is_delete).closest("tr").fadeOut(800, function() {
                                        $(this).remove();
                                    });
                                flashNotice(response['message']);
                            }
                            if (response['total_method'] != undefined) {
                                $('#totalShippingMethod').html(response['total_method']);
                            }
                        } else {
                            window.location = 'index.php?store=' + store;
                            setCookie('flash_message', response['message'], 2);
                        }
                    
                            
                    }
                });
            }
        //   if (mode == 'live') {
        //        ShopifyApp.Modal.confirm({
        //            title: "Delete " + is_delete + " ?",
        //            message: "Are you sure want to delete the " + is_delete + " ? This action cannot be reversed.",
        //            okButton: "Delete " + is_delete,
        //            cancelButton: "Cancel",
        //            style: "danger"
        //        }, function (result) {
        //            if (result) {
        //                $('.ui-button.close-modal.btn-destroy-no-hover').addClass("ui-button ui-button--destructive js-btn-loadable is-loading disabled");
        //                Ajaxdelete();
        //            }
        //        });
        //    } else {
                var r = confirm("Are you sure want to delete!");
                if (r == true) {
                    Ajaxdelete();
        //        }
            }

        }
        $.urlParam = function (name) {
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            if (results == null) {
                return null;
            }
            else {
                return results[1] || 0;
            }
        }
        $(document).ready(function () {
            setTimeout(function () {
                var count = $(".set_all_form .Polaris-ResourceList__HeaderWrapper").length;
                $('.dataAdded').append('Showing '+count+'  form');
            }, 100);

            // $('.myeditor').each(function(index,item){
            //     CKEDITOR.replace(item);
            // });
        });

        function btn_enable_disable(form_id){
        $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: {'routine_name': 'btn_enable_disable' , store: store},
                success: function (comeback) {
                    console.log(comeback +  "status");
                        if (comeback['outcome']['data']['status'] != undefined && comeback['outcome']['data']['status'] == 0) {
                            $("#register_frm_btn").attr('disabled',true);
                            $(".app-setting-msg").show();
                        } else {
                            $("#register_frm_btn").attr('disabled',false);
                        }
                    }
            });
        }

        function seeting_enable_disable(form_id){
        $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: {'routine_name': 'btn_enable_disable' ,'store' : store, 'form_id':form_id},
                success: function (comeback) {
                    console.log("---------------");
                   console.log(comeback['outcome']['data']['status']);
                        if (comeback['outcome']['data']['status'] != undefined && comeback['outcome']['data']['status'] == 0) {
                            $(".app-setting-msg").show();
                            $(".enable-btn").html("Enable");
                            $(".enable-btn").addClass("Polaris-Button--primary");
                        } else {
                            $(".app-setting-msg").hide();
                            $(".enable-btn").html("Disable");
                            $(".enable-btn").addClass("Polaris-Button--destructive");
                        }
                    }
            });
        }

        function get_api_data(routineName,shopify_api){
            var routineName = routineName;
            var shopify_api = shopify_api;
            $.ajax({
                url: "ajax_call.php",
                type: "POST",
                dataType: "json",
                data: {
                    routine_name: routineName,
                    shopify_api: shopify_api,
                    store: store,
                },
                success: function ($response) {
                        if ($response['code'] != undefined && $response['code'] == '403') {
                            redirect403();
                        } else if ($response['data'] == 'true') {
                            $('.numberConvertBlog').html($response["total_record_blog"]);
                            $('.numberConvertCollection').html($response["total_record_collection"]);
                            $('.numberConvertProduct').html($response["total_record_product"]);
                            $('.numberConvertPages').html($response["total_record_pages"]);
                        } else {
                        }
                    }
            })
        }
        
        $(document).on("click", ".enable-btn", function(event) {
            event.preventDefault();
                            
            var $form_id  = $('.form_id').val();
            var btnval = $(this).val();
            if (btnval == 0) {
                $(".enable-btn").val(1);
                $(".enable-btn").html("Enable");
                $('.enable-btn').removeClass('Polaris-Button--destructive');
                $('.enable-btn').addClass('Polaris-Button--primary');
            } else {
                $(".enable-btn").val(0);
                $(".enable-btn").html("Disable");
                $('.enable-btn').removeClass('Polaris-Button--primary');
                $('.enable-btn').addClass('Polaris-Button--destructive');
            }
                $.ajax({
                    url: "ajax_call.php",
                    type: "post",
                    dataType: "json",
                    data: {'store': store,'routine_name' : 'enable_disable','btnval':btnval,'form_id': $form_id}, 
                    
                    success: function (response) {
                        if (response['code'] != undefined && response['code'] == '403') {
                            redirect403();
                        }
                    }
                });
        });

         // start 014
        $(document).on("click", ".btn_add_element", function(event) {
            event.preventDefault();    
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: {'routine_name': 'get_all_element_fun' , store: store},
                success: function (comeback) {
                   var comeback = JSON.parse(comeback);
                   if (comeback['code'] != undefined && comeback['code'] == '403') {
                      redirect403();
                  } else{
                    $(".setvalue_element").html(comeback['outcome']);
                    $(".setvalue_element_select").html(comeback['outcome2']);
                    $(".setvalue_element_static").html(comeback['outcome3']);
                    $(".setvalue_element_structure").html(comeback['outcome4']);
                    $(".setvalue_element_customization").html(comeback['outcome5']);
                  }
                }
            });
        });

        $(document).on("click", ".element_coppy_to", function(event) {
            event.preventDefault();  
            var thisObj = $(this);
            var formid= $(".formid").val();
            var elementid= $(this).find(".get_element_hidden").val();
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: {'routine_name': 'set_element' , store: store,'get_element_hidden':elementid,'formid':formid},
                success: function (comeback) {
                    var comeback = JSON.parse(comeback);
                    var formdata_id = comeback["last_id"] !== undefined ? comeback["last_id"] : "";
                    if (comeback['code'] != undefined && comeback['code'] == '403') {
                        redirect403();
                    } else{
                        $('.owl-carousel').trigger('to.owl.carousel',  [BACKTO, 40, true]);
                        get_selected_elements(formid);
                        // get_selected_element_preview(formid,elementid,formdata_id);
                    }
                }
            });
        });

        function insertDefaultElements(form_id,selectedType){   
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: {'routine_name': 'insertDefaultElements' , store: store, "form_id":form_id, "form_type":selectedType},
                success: function (comeback) {
                    var comeback = JSON.parse(comeback);
                    if (comeback['code'] != undefined && comeback['code'] == '403') {
                        redirect403();
                    } else{
                    }
                }
            });
        }      

        function get_selected_elements(form_id){
            $.ajax({
                    url: "ajax_call.php",
                    type: "post",
                    dataType: "json",
                    data: {'routine_name': 'get_selected_elements_fun','form_id': form_id, store: store},
                    success: function (comeback) { 
                        if (comeback['code'] != undefined && comeback['code'] == '403') {
                            redirect403();
                        } else{
                            console.log(comeback['form_footer_data']);
                            if(comeback['form_type'] == "4"){
                                    $(".preview-box").addClass("floting_form_main");
                            }
                            if(comeback['form_header_data']['0'] == 1){
                                $(".headerData .showHeader").prop("checked", true);
                            }else{
                                $(".headerData .showHeader").prop("checked", false);
                            }
                            $(".headerData .form_id").val(comeback['form_id']);
                            $(".headerData .headerTitle").val(comeback['form_header_data']['1']);
                            $('.headerData .myeditor').html(comeback['form_header_data']['2']);
                            $(".form_name_form_design").val(comeback['form_header_data']['1'])
                            $(".selected_element_set").html(comeback['outcome']);
                            $(".code-form-app").html(comeback['form_html']);
                            
                            $(".footerData .form_id").val(comeback['form_id']);
                            $('.footerData .myeditor').html(comeback['form_footer_data']['0']);
                            $('.footerData .submitText').val(comeback['form_footer_data']['1']);
                            if(comeback['form_footer_data']['2'] == 1){
                                $(".footerData .resetButton").prop("checked", true);
                                $(".input_reset").removeClass("hidden");
                            }else{
                                $(".input_reset").addClass("hidden");
                                $(".footerData .resetButton").prop("checked", false);
                            }
                            $('.footerData .resetbuttonText').val(comeback['form_footer_data']['3']);
                            if(comeback['form_footer_data']['4'] == 1){
                                $(".footerData .fullFooterButton").prop("checked", true);
                            }else{
                                $(".footerData .fullFooterButton").prop("checked", false);
                            }
                            $( ".footerData .chooseItem-align" ).each(function() {
                                    if(comeback['form_footer_data']['5'] == $(this).data('value')){
                                        $(".footerData .chooseItem-align").removeClass("active");
                                        $(this).addClass("active");
                                    }
                            });
                            if(comeback['publishdata']['0'] == 1){
                                $('.required_login').prop("checked");
                            }
                            $('.login_message').html(comeback['publishdata']['1']);
                            $('.embed_code').val('<div data-formid="'+comeback['publishdata']['2']+'"></div>');
                         
                        }
                    }
                });
        }

        function getFormTitle(form_id){
            $.ajax({
                    url: "ajax_call.php",
                    type: "post",
                    dataType: "json",
                    data: {'routine_name': 'getFormTitleFun','form_id': form_id, store: store },
                    success: function (comeback) {
                        var comeback = JSON.parse(comeback);
                        if (comeback['code'] != undefined && comeback['code'] == '403') {
                            redirect403();
                        } else{
                        $(".form_name_form_design").val(comeback['outcome'])
                        }
                    }
                });
        }

        $(document).on("click", ".btnFormSubmit", function(event) {
            var formid=$(".formid").val();
            var form_name=$(".form_name_form_design").val();
            event.preventDefault();  
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: {'routine_name': 'insertFormData', store: store,'formid':formid,'form_name':form_name },
                beforeSend: function () {
                    loading_show('.save_loader_show');
                },
                success: function (comeback) {
                    var comeback = JSON.parse(comeback);
                    if (comeback['code'] != undefined && comeback['code'] == '403') {
                        redirect403();
                    } else{
                    // $(".").html(comeback['outcome'])
                    }
                    loading_hide('.save_loader_show', 'Save');
                }
            });
        });

        function getAllForm(){
            $.ajax({
                    url: "ajax_call.php",
                    type: "post",
                    dataType: "json",
                    data: {'routine_name': 'getAllFormFunction', store: store },
                    success: function (comeback) {
                        console.log("return set all elemnt");
                        var comeback = JSON.parse(comeback);
                        if (comeback['code'] != undefined && comeback['code'] == '403') {
                            redirect403();
                        } else{
                        $(".set_all_form").html(comeback['outcome'])
                        }
                    }
                });
        }  
        
        $(document).on("click", ".btncreate_new", function(event) {
            event.preventDefault(); 
            var selectedType = $(".selectedType").val();  
            var form_data = $("#createNewForm")[0];
            var form_data = new FormData(form_data);
            form_data.append('store',store); 
            form_data.append('routine_name','function_create_form');      
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: form_data, 
                    beforeSend: function () {
                    loading_show('.save_loader_show');
                },
                success: function (response) {
                    var response = JSON.parse(response);
                    if (response['code'] != undefined && response['code'] == '403') {
                        redirect403();
                    } else{
                        $(".text_image_list").removeClass("first_txt_image");
                        $(".firstone_").addClass("first_txt_image");
                        insertDefaultElements(response["data"],selectedType);
                        window.location.href = "form_design.php?form_id="+response["data"]+"&store="+store;
                    }
                    loading_hide('.save_loader_show', 'Create Form');
                }
            });
        });

        function initializeCKEditor(editorName, targetElement) {
            console.log("initializeCKEditor")
            var editorElement = $('textarea[name="' + editorName + '"]');
            console.log(editorElement.length);
            console.log(CKEDITOR.instances[editorName]);
            if (editorElement.length > 0 && CKEDITOR.instances[editorName]){
                console.log("DESTROY");
                console.log(CKEDITOR.instances);
                console.log(CKEDITOR.instances["contentparagraph"]);
                // if (CKEDITOR.instances['contentparagraph']) {
                //     CKEDITOR.instances['contentparagraph'].destroy();
                // } else {
                //     console.error("CKEditor instance for contentparagraph is not found.");
                // }
            }
            if (editorElement.length > 0 && !CKEDITOR.instances[editorName]) {
                CKEDITOR.replace(editorName, {
                    on: {
                        instanceReady: function(evt) {
                            evt.editor.on('change', function() {
                                var editorData = evt.editor.getData();
                                if(editorName == "contentparagraph"){
                                    var mainContainerClass = editorElement.closest(".container").attr("class");
                                    var classArray = mainContainerClass.split(" ");
                                    var containerClass = classArray.find(className => className.startsWith("container_"));
                                    $(".block-container ."+containerClass).find(".globo-paragraph").html(editorData);
                                }else{
                                    $(targetElement).html(editorData);
                                }
                            });
                        }
                    }
                });
            }
        }

        $(document).on("click",".Polaris-Tabs__Panel .list-item",function(){
            var slideTo = $(this).data("owl");
            var elementId = $(this).data("elementid");
            var formId = $(this).closest(".clsselected_element").data("formid");
            var formdataId = $(this).closest(".clsselected_element").data("formdataid");
            $('.owl-carousel').trigger('to.owl.carousel',  [slideTo, 40, true]);
            if(elementId != undefined){
                $.ajax({
                    url: "ajax_call.php",
                    type: "post",
                    dataType: "json",
                    data: {'routine_name': 'form_element_data_html', store: store,"elementid":elementId, "formid":formId ,"formdataid":formdataId },
                    success: function (comeback) {
                        var comeback = JSON.parse(comeback);
                        if (comeback['code'] != undefined && comeback['code'] == '403') {
                        } else{
                            $(".elementAppend").html(comeback.outcome);
                            initializeCKEditor('contentparagraph', '');
                            // $('.myeditor').each(function(index,item){
                            //     CKEDITOR.replace(item);
                            // });
                        }
                    }
                });
            }
        });   

        $(document).on("change",".switch input[name='checkbox']",function(){
            console.log("Input checkboc table");
            var formId = $(this).closest(".Polaris-ResourceList__HeaderWrapper").find(".form_id_main").val();
            var ischecked= $(this).is(':checked');
            var ischecked_value = 1;
            if(!ischecked){
                var ischecked_value = 0;
            }
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: {'routine_name': 'change_form_status', store: store,"formid":formId, "ischecked_value":ischecked_value },
                success: function (comeback) {
                    var comeback = JSON.parse(comeback);
                }
            });

        });

        $(document).on("click","#checkAll",function(){
            
            var checked = $(this).is(":checked");
            if (checked) {
                $(".selectedCheck").each(function() {
                $(this).prop("checked", true);
                });
            } else {
                $(".selectedCheck").each(function() {
                $(this).prop("checked", false);
                });
                $(".bultActionss").css("display","none");
                $(".selectedshow,.sortBy").css("display","block");
            }
        });

        $(document).on("click",".selectedCheck",function() {
        if ($(".selectedCheck").length == $(".selectedCheck:checked").length) {
            $("#checkAll").prop("checked", true);
        } else {
            // $("#checkall").removeAttr("checked");
            $("#checkAll").prop("checked", false);
        }
        });

        $(document).on("click",".selectedCheck",function () {
            if ($(this).is(":checked")) {
                $(".bultActionss").css("display","block");
                $(".selectedshow").css("display","none");
                $(".Polaris-Labelled--hidden").css("display","none");
                setTimeout(function () {
                    
                        var    mychecked = $('.selectedCheck:checked').length;
                        $('.Deselectcount').text('Deselect all '+ mychecked + '  form'); 
                    
                }, 100);  
            } 
            else {
                $(".bultActionss").css("display","none");
                $(".selectedshow").css("display","block");
                $(".Polaris-Labelled--hidden").css("display","block");
            }
           
        });
        
        $(document).on("click", ".removeElement", function(event) {
            console.log("delete form element  .....");
            var thisObj = $(this);
            var formid=$(".form_id").val();
            var form_data = $(".add_elementdata")[0];
            var form_data = new FormData(form_data);
            form_data.append('store',store); 
            form_data.append('routine_name','remove_form_field');   
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: form_data, 
                success: function (response) {
                    console.log(response['result']);
                    if (response['code'] != undefined && response['code'] == '403') {
                        redirect403();
                    } else{
                        $(thisObj).closest(".polarisformcontrol").find(".backBtn").trigger("click");
                        get_selected_elements(formid); 
                    }
                }
            });
            
        });

        $(document).on("click", ".saveForm", function(event) {
            console.log("savform .....");
            saveform();
            saveheaderform();
            savefooterform();
            savepublishdata();
        });

        function  saveform(){
            var $content = CKEDITOR.instances["content"];
            if($content != undefined){
                CKEDITOR.instances["content"].updateElement();
            }
            var form_data = $(".add_elementdata")[0]; 
            var val = $(".selectFile").select2("val");
            var form_data = new FormData(form_data);
            form_data.append("allowextention", val);
            form_data.append('store',store); 
            form_data.append('routine_name','saveform');  
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: form_data, 
                    beforeSend: function () {
                    loading_show('.save_loader_show');
                },
                success: function (response) {
                    var response = JSON.parse(response);
                  console.log(response + "..............");
                    loading_hide('.save_loader_show', 'Save');
                }
            });
        }
        
        function saveheaderform(){
            var $contentheader =  CKEDITOR.instances["contentheader"];
            if($contentheader != undefined){
                CKEDITOR.instances["contentheader"].updateElement();
            }
            var form_data = $(".add_headerdata")[0]; 
            var form_data = new FormData(form_data);
            form_data.append('store',store); 
            form_data.append('routine_name','saveheaderform'); 
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: form_data, 
                success: function (response) {
                    var response = JSON.parse(response);
                  console.log(response + "..............");
                }
            });
        }

        function savefooterform(){
            var $contentfooter = CKEDITOR.instances["contentfooter"];
            if($contentfooter != undefined){
                CKEDITOR.instances["contentfooter"].updateElement();
            }
            var form_data = $(".add_footerdata")[0];
            var form_data = new FormData(form_data);
            form_data.append('store',store); 
            form_data.append('routine_name','savefooterform');  
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: form_data, 
                success: function (response) {
                    var response = JSON.parse(response);
                  console.log(response + "..............");
                }
            });
        }
        
        function savepublishdata(){
            var form_data = $(".add_publishdata")[0];
            var form_data = new FormData(form_data);
            form_data.append('store',store); 
            form_data.append('routine_name','savepublishdata');  
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: form_data, 
                success: function (response) {
                    var response = JSON.parse(response);
                  console.log(response + "..............");
                }
            });
        }

        $(document).on("click",".submit.action",function (e) {
            e.preventDefault();
            console.log("HII");
            var form_data = $(".get_selected_elements")[0];
            var form_data = new FormData(form_data);
            // form_data.append('images',$("#ImagePreview").attr("src"));
            form_data.append('store',store); 
            form_data.append('routine_name','addformdata');      
            $.ajax({
                url: "ajax_call.php",
                type: "POST",
                dataType: 'json',
                contentType: false,
                processData: false,
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

        // File 
        window.onload = (event) => {
            const fileButton = $('#fileButton');
            const fileInput = $('#fileimage');
            const imgContainer = $('#imgContainer');
            const uploadText = $('#uploadText');
            $(document).on('click', '#fileButton', function() {
                $(this).closest(".globo-form-input").find('#fileimage').click();
            });
                                     
            $(document).on('click', '.close-button', function(event) {
                event.preventDefault();
                if ($(this).closest(".globo-form-input").find('.imgContainer').children().length === 0) {
                    $(this).closest(".globo-form-input").find('#uploadText').show();
                    $(this).closest(".globo-form-input").find('#fileButton').show();
                    $(this).closest(".globo-form-input").find('#fileimage').val('');
                    $(this).closest(".globo-form-input").find('.img-preview-wrapper').remove();
                }
            });

            $(document).on('change', '#fileimage', function(event) {
                event.preventDefault();
                var $thisObj = $(this);
                $(this).closest(".globo-form-input").find('#imgContainer').html(''); // Clear previous previews
                const files = this.files;

                Array.from(files).forEach(file => {
                    const formId = $('.form_id').val();
                    const formDataId = $(this).closest('.globo-form-input').attr("data-formdataid");
                    $.ajax({
                        url: "ajax_call.php",
                        type: "post",
                        dataType: "json",
                        data: {'routine_name': 'get_fileallowextention', 'store': store, 'form_id': formId ,'formdata_id': formDataId },
                        success: function(comeback) {
                            const data = JSON.parse(comeback);

                            if (data && data.data) {
                                let allowExtentions = data.data[4] || 'png,svg,gif,jpeg,jpg,pdf,webp';
                                console.log(allowExtentions);
                                const extensionArray = allowExtentions.split(',').map(ext => {
                                    switch(ext) {
                                        case 'jpg': return 'image/jpeg';
                                        case 'jpeg': return 'image/jpeg';
                                        case 'png': return 'image/png';
                                        case 'gif': return 'image/gif';
                                        case 'svg': return 'image/svg+xml';
                                        case 'webp': return 'image/webp';
                                        default: return ext;
                                    }
                                });

                                if (extensionArray.includes(file.type)) {
                                    if (files.length > 0) {
                                        $thisObj.closest(".globo-form-input").find('#uploadText').hide();
                                        $thisObj.closest(".globo-form-input").find('#fileButton').hide();
                                    } else {
                                        $thisObj.closest(".globo-form-input").find('#uploadText').show();
                                        $thisObj.closest(".globo-form-input").find('#fileButton').show();
                                    }

                                    const reader = new FileReader();
                                    reader.onload = function(event) {
                                        const imgPreviewWrapper = $('<div>').addClass('img-preview-wrapper');
                                        const imgPreview = $('<img>').addClass('img-preview');
                                        const fileName = $('<p>').addClass('file-name').text(file.name);
                                        const closeButton = $('<button>').addClass('close-button').text('×');

                                        if (file.type.startsWith('image/')) {
                                            imgPreview.attr('src', event.target.result);
                                        } else {
                                            imgPreview.attr('src', 'https://pngfre.com/wp-content/uploads/Folder-1.png');
                                        }

                                        imgPreviewWrapper.append(imgPreview).append(closeButton).append(fileName);
                                        $thisObj.closest(".globo-form-input").find('#imgContainer').append(imgPreviewWrapper);
                                    };

                                    reader.readAsDataURL(file);
                                } else {
                                    alert('File not supported');
                                }
                            } else {
                                console.log('ALL FILE SET');
                            }
                        }
                    });
                });
            });
        };

        // File 