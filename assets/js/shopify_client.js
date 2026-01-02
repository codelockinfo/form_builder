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
                            window.location = 'index.php?shop=' + store;
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
            // Prevent default first to stop any other handlers
            event.preventDefault();
            event.stopPropagation();
            
            // Navigate to the add element panel (data-owl="6")
            // Use the navigateToSlide function which handles mapping correctly
            var $carousel = $('.owl-carousel');
            if ($carousel.length > 0) {
                // Ensure carousel is initialized
                if (typeof $carousel.data('owl.carousel') === 'undefined') {
                    // Use the global initOwlCarousel function if available, otherwise initialize directly
                    if (typeof window.initOwlCarousel === 'function') {
                        window.initOwlCarousel();
                        // Wait for carousel to be ready before navigating
                        setTimeout(function() {
                            if (typeof window.navigateToSlide === 'function') {
                                window.navigateToSlide(6); // data-owl="6" for Add element
                            } else {
                                console.warn('navigateToSlide function not available');
                            }
                        }, 200);
                    } else {
                        $carousel.owlCarousel({
                            items:1,
                            loop:false,
                            margin:10,
                            nav:false,
                            mouseDrag: false,
                        });
                        setTimeout(function() {
                            if (typeof window.navigateToSlide === 'function') {
                                window.navigateToSlide(6); // data-owl="6" for Add element
                            } else {
                                console.warn('navigateToSlide function not available');
                            }
                        }, 200);
                    }
                } else {
                    // Carousel is already initialized, use navigateToSlide for proper mapping
                    if (typeof window.navigateToSlide === 'function') {
                        window.navigateToSlide(6); // data-owl="6" for Add element
                    } else {
                        console.warn('navigateToSlide function not available, falling back to direct navigation');
                        try {
                            $carousel.trigger('to.owl.carousel', [6, 40, true]);
                        } catch(e) {
                            console.error('Error navigating to Add element panel:', e);
                        }
                    }
                }
            }
            
            // Then load the element data
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
            
            // Validate formid and elementid
            if (!formid || formid === '') {
                alert('Form ID is missing. Please refresh the page and try again.');
                return false;
            }
            
            if (!elementid || elementid === '') {
                alert('Element ID is missing. Please try selecting the element again.');
                return false;
            }
            
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: {'routine_name': 'set_element' , store: store,'get_element_hidden':elementid,'formid':formid},
                success: function (comeback) {
                    console.log('=== Add Element AJAX Response ===');
                    console.log('Raw comeback:', comeback);
                    console.log('Type of comeback:', typeof comeback);
                    
                    // Parse JSON if it's a string, otherwise use as-is
                    var response = (typeof comeback === 'string') ? JSON.parse(comeback) : comeback;
                    
                    console.log('Parsed response:', response);
                    console.log('Response data:', response['data']);
                    console.log('Response msg:', response['msg']);
                    console.log('Response last_id:', response['last_id']);
                    console.log('=== End Response Debug ===');
                    
                    var formdata_id = response["last_id"] !== undefined ? response["last_id"] : "";
                    
                    if (response['code'] != undefined && response['code'] == '403') {
                        redirect403();
                    } else if (response['data'] === 'success') {
                        console.log('Element added successfully! ID:', formdata_id);
                        $('.owl-carousel').trigger('to.owl.carousel',  [BACKTO, 40, true]);
                        get_selected_elements(formid);
                        // get_selected_element_preview(formid,elementid,formdata_id);
                    } else {
                        var errorMsg = response['msg'] || response['message'] || 'Failed to add element. Please try again.';
                        console.error('Error adding element:', response);
                        alert('Error: ' + errorMsg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('=== AJAX Error Adding Element ===');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Response Status:', xhr.status);
                    console.error('=== End Error Debug ===');
                    alert('An error occurred while adding the element.\n\nStatus: ' + status + '\nError: ' + error + '\n\nPlease check the console for details.');
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
            if (!form_id || form_id === '' || form_id === 0) {
                console.error('Form ID is missing or invalid');
                alert('Form ID is missing. Please refresh the page and try again.');
                return;
            }
            
            $.ajax({
                    url: "ajax_call.php",
                    type: "post",
                    dataType: "json",
                    data: {'routine_name': 'get_selected_elements_fun','form_id': form_id, store: store},
                    success: function (comeback) {
                        // Debug: Log raw response
                        console.log('=== get_selected_elements AJAX Response ===');
                        console.log('Raw comeback:', comeback);
                        console.log('Type of comeback:', typeof comeback);
                        
                        // Parse JSON if it's a string, otherwise use as-is
                        var response = (typeof comeback === 'string') ? JSON.parse(comeback) : comeback;
                        
                        console.log('Parsed response:', response);
                        console.log('Response data:', response['data']);
                        console.log('Response form_id:', response['form_id']);
                        console.log('Response form_name:', response['form_name']);
                        console.log('Response form_header_data:', response['form_header_data']);
                        console.log('Response form_footer_data:', response['form_footer_data']);
                            console.log('Response outcome:', response['outcome']);
                            console.log('Response outcome length:', response['outcome'] ? response['outcome'].length : 0);
                            console.log('Response outcome (first 500 chars):', response['outcome'] ? response['outcome'].substring(0, 500) : 'empty');
                            console.log('Response form_html:', response['form_html'] ? 'exists (' + response['form_html'].length + ' chars)' : 'missing');
                            
                            // Count how many elements are in the outcome
                            if (response['outcome']) {
                                var elementCount = (response['outcome'].match(/data-formdataid=/g) || []).length;
                                console.log('Number of elements in outcome:', elementCount);
                            }
                            
                            // Show debug info from server
                            if (response['debug']) {
                                console.log('=== Server Debug Info ===');
                                console.log('Elements found by query:', response['debug']['elements_found']);
                                console.log('Elements in HTML:', response['debug']['elements_in_html']);
                                console.log('Elements processed:', response['debug']['elements_processed']);
                                console.log('=== End Server Debug ===');
                            }
                            console.log('=== End Response Debug ===');
                        
                        if (response['code'] != undefined && response['code'] == '403') {
                            redirect403();
                        } else if (response['data'] === 'success') {
                            console.log('Form data loaded successfully');
                            console.log('Form footer data:', response['form_footer_data']);
                            if(response['form_type'] == "4"){
                                    $(".preview-box").addClass("floting_form_main");
                            }
                            if(response['form_header_data'] && response['form_header_data']['0'] == 1){
                                $(".headerData .showHeader").prop("checked", true);
                            }else{
                                $(".headerData .showHeader").prop("checked", false);
                            }
                            $(".headerData .form_id").val(response['form_id']);
                            $(".headerData .headerTitle").val(response['form_header_data'] && response['form_header_data']['1'] ? response['form_header_data']['1'] : '');
                            
                            // Set header description content - use val() for textarea, and also set in CKEditor if it exists
                            var headerDescription = response['form_header_data'] && response['form_header_data']['2'] ? response['form_header_data']['2'] : '';
                            $('.headerData textarea[name="contentheader"]').val(headerDescription);
                            // Also set in CKEditor if it's already initialized
                            if (CKEDITOR.instances['contentheader']) {
                                CKEDITOR.instances['contentheader'].setData(headerDescription);
                            }
                            
                            // Load saved settings
                            // Backward compatibility: if old format (only 6 elements), use single values for both
                            var headerDataLength = response['form_header_data'] ? response['form_header_data'].length : 0;
                            
                            if (headerDataLength >= 8) {
                                // New format: separate heading and sub-heading settings
                                var headingFontSize = (response['form_header_data'] && response['form_header_data']['3']) ? parseInt(response['form_header_data']['3']) : 24;
                                var headerTextAlign = (response['form_header_data'] && response['form_header_data']['4']) ? response['form_header_data']['4'] : 'center';
                                var headingTextColor = (response['form_header_data'] && response['form_header_data']['5']) ? response['form_header_data']['5'] : '#000000';
                                var subheadingFontSize = (response['form_header_data'] && response['form_header_data']['6']) ? parseInt(response['form_header_data']['6']) : 16;
                                var subheadingTextColor = (response['form_header_data'] && response['form_header_data']['7']) ? response['form_header_data']['7'] : '#000000';
                                
                                $('.header-design-heading-font-size').val(headingFontSize);
                                $('.header-design-heading-text-color').val(headingTextColor);
                                $('.header-design-heading-text-color-text').val(headingTextColor);
                                
                                $('.header-design-subheading-font-size').val(subheadingFontSize);
                                $('.header-design-subheading-text-color').val(subheadingTextColor);
                                $('.header-design-subheading-text-color-text').val(subheadingTextColor);
                            } else {
                                // Old format: use same values for both heading and sub-heading
                                var headerFontSize = (response['form_header_data'] && response['form_header_data']['3']) ? parseInt(response['form_header_data']['3']) : 24;
                                var headerTextAlign = (response['form_header_data'] && response['form_header_data']['4']) ? response['form_header_data']['4'] : 'center';
                                var headerTextColor = (response['form_header_data'] && response['form_header_data']['5']) ? response['form_header_data']['5'] : '#000000';
                                
                                $('.header-design-heading-font-size').val(headerFontSize);
                                $('.header-design-heading-text-color').val(headerTextColor);
                                $('.header-design-heading-text-color-text').val(headerTextColor);
                                
                                $('.header-design-subheading-font-size').val(16); // Default for sub-heading
                                $('.header-design-subheading-text-color').val(headerTextColor);
                                $('.header-design-subheading-text-color-text').val(headerTextColor);
                            }
                            
                            // Update alignment buttons (new segmented control)
                            $('.headerData .chooseItem-align').removeClass('active');
                            $('.headerData .chooseItem-align[data-value="' + headerTextAlign + '"]').addClass('active');
                            $('.header-text-align-input').val(headerTextAlign);
                            
                            // Also update old select dropdown if it exists (for backward compatibility)
                            $('.header-design-text-align').val(headerTextAlign);
                            
                            // Apply alignment to preview immediately
                            $(".formHeader").removeClass("align-left align-center align-right").addClass(headerTextAlign);
                            
                            // Apply to preview (function defined in form_design.php)
                            setTimeout(function() {
                                if (typeof window.updateHeaderPreview === 'function') {
                                    window.updateHeaderPreview();
                                }
                            }, 300);
                            
                            // Set form name - prefer form_name from response, fallback to header title
                            var formName = response['form_name'] || (response['form_header_data'] && response['form_header_data']['1'] ? response['form_header_data']['1'] : 'Blank Form');
                            $(".form_name_form_design").val(formName);
                            
                            $(".selected_element_set").html(response['outcome'] || '');
                            $(".code-form-app").html(response['form_html'] || '');
                            
                            $(".footerData .form_id").val(response['form_id']);
                            if (response['form_footer_data']) {
                                $('.footerData .myeditor').html(response['form_footer_data']['0'] || '');
                                $('.footerData .submitText').val(response['form_footer_data']['1'] || 'Submit');
                                if(response['form_footer_data']['2'] == 1){
                                    $(".footerData .resetButton").prop("checked", true);
                                    $(".input_reset").removeClass("hidden");
                                }else{
                                    $(".input_reset").addClass("hidden");
                                    $(".footerData .resetButton").prop("checked", false);
                                }
                                $('.footerData .resetbuttonText').val(response['form_footer_data']['3'] || 'Reset');
                                if(response['form_footer_data']['4'] == 1){
                                    $(".footerData .fullFooterButton").prop("checked", true);
                                }else{
                                    $(".footerData .fullFooterButton").prop("checked", false);
                                }
                                // Load footer alignment
                                var footerAlignment = (response['form_footer_data'] && response['form_footer_data']['5']) ? response['form_footer_data']['5'] : 'align-left';
                                
                                // Convert old numeric values to new format (backward compatibility)
                                if (footerAlignment === '1' || footerAlignment === 1) {
                                    footerAlignment = 'align-left';
                                } else if (footerAlignment === '2' || footerAlignment === 2) {
                                    footerAlignment = 'align-center';
                                } else if (footerAlignment === '3' || footerAlignment === 3) {
                                    footerAlignment = 'align-right';
                                }
                                
                                // Ensure alignment is valid
                                if (!footerAlignment || !['align-left', 'align-center', 'align-right'].includes(footerAlignment)) {
                                    footerAlignment = 'align-left';
                                }
                                
                                // Update alignment buttons
                                $(".footerData .chooseItem-align").removeClass("active");
                                $(".footerData .chooseItem-align[data-value='" + footerAlignment + "']").addClass("active");
                                
                                // Update hidden input
                                $(".footerData .footer-button__alignment").val(footerAlignment);
                                
                                // Apply alignment to preview immediately
                                setTimeout(function() {
                                    $(".forFooterAlign").removeClass("align-left align-center align-right").addClass(footerAlignment);
                                }, 100);
                                
                                // Load button design settings (new format with 11 elements, or fallback to defaults)
                                var footerDataLength = response['form_footer_data'] ? response['form_footer_data'].length : 0;
                                if (footerDataLength >= 11) {
                                    var buttonTextSize = (response['form_footer_data'] && response['form_footer_data']['6']) ? parseInt(response['form_footer_data']['6']) : 16;
                                    var buttonTextColor = (response['form_footer_data'] && response['form_footer_data']['7']) ? response['form_footer_data']['7'] : '#ffffff';
                                    var buttonBgColor = (response['form_footer_data'] && response['form_footer_data']['8']) ? response['form_footer_data']['8'] : '#EB1256';
                                    var buttonHoverBgColor = (response['form_footer_data'] && response['form_footer_data']['9']) ? response['form_footer_data']['9'] : '#C8104A';
                                    var borderRadius = (response['form_footer_data'] && response['form_footer_data']['10']) ? parseInt(response['form_footer_data']['10']) : 4;
                                    
                                    $('.footer-design-button-text-size').val(buttonTextSize);
                                    $('.footer-design-button-text-color').val(buttonTextColor);
                                    $('.footer-design-button-text-color-text').val(buttonTextColor);
                                    $('.footer-design-button-bg-color').val(buttonBgColor);
                                    $('.footer-design-button-bg-color-text').val(buttonBgColor);
                                    $('.footer-design-button-hover-bg-color').val(buttonHoverBgColor);
                                    $('.footer-design-button-hover-bg-color-text').val(buttonHoverBgColor);
                                    $('.footer-design-button-border-radius').val(borderRadius);
                                } else {
                                    // Old format: use defaults
                                    $('.footer-design-button-text-size').val(16);
                                    $('.footer-design-button-text-color').val('#ffffff');
                                    $('.footer-design-button-text-color-text').val('#ffffff');
                                    $('.footer-design-button-bg-color').val('#EB1256');
                                    $('.footer-design-button-bg-color-text').val('#EB1256');
                                    $('.footer-design-button-hover-bg-color').val('#C8104A');
                                    $('.footer-design-button-hover-bg-color-text').val('#C8104A');
                                    $('.footer-design-button-border-radius').val(4);
                                }
                                
                                // Apply to preview (function defined in form_design.php)
                                setTimeout(function() {
                                    if (typeof window.updateFooterButtonPreview === 'function') {
                                        window.updateFooterButtonPreview();
                                    }
                                }, 300);
                            }
                            if (response['publishdata']) {
                                if(response['publishdata']['0'] == 1){
                                    $('.required_login').prop("checked", true);
                                } else {
                                    $('.required_login').prop("checked", false);
                                }
                                $('.login_message').html(response['publishdata']['1'] || '');
                                $('.embed_code').val('<div data-formid="'+(response['publishdata']['2'] || '')+'"></div>');
                            }
                            $(".selected_element_set").sortable({ 
                                opacity: 0.6, 
                                cursor: 'move',
                                handle: '.softable', // Use the sort handle
                                update: function(event, ui) {
                                    // Save position immediately when element is dragged
                                    var formdataid = $(this).sortable("toArray", { attribute: "data-formdataid" });
                                    console.log("Position changed, saving positions:", formdataid);
                                    $.ajax({
                                        url: "ajax_call.php",
                                        type: "POST",
                                        data: { 
                                            routine_name: 'update_position', 
                                            store: store, 
                                            formdataid: formdataid 
                                        },
                                        success: function(response) {
                                            console.log("Position saved successfully:", response);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error("Error saving position:", error);
                                        }
                                    });
                                }
                            });
                            
                            // Ensure owl carousel is initialized after content loads
                            setTimeout(function() {
                                var $carousel = $('.owl-carousel');
                                if ($carousel.length > 0 && typeof $carousel.data('owl.carousel') === 'undefined') {
                                    console.log('Initializing owl carousel after form data load');
                                    if (typeof initOwlCarousel === 'function') {
                                        initOwlCarousel();
                                    } else {
                                        // Fallback initialization
                                        $carousel.owlCarousel({
                                            items:1,
                                            loop:false,
                                            margin:10,
                                            nav:false,
                                            mouseDrag: false,
                                        });
                                    }
                                }
                            }, 200);
                            
                            // Initialize CKEditor for header and footer
                            if (typeof CKEDITOR !== 'undefined') {
                                setTimeout(function() {
                                    // Initialize header description editor
                                    var $headerEditor = $('.headerData textarea[name="contentheader"]');
                                    var headerContent = response['form_header_data'] && response['form_header_data']['2'] ? response['form_header_data']['2'] : '';
                                    
                                    if ($headerEditor.length > 0 && !CKEDITOR.instances['contentheader']) {
                                        console.log('Initializing CKEditor for contentheader');
                                        // Set the textarea value first
                                        $headerEditor.val(headerContent);
                                        initializeCKEditor('contentheader', '.boxed-layout .formHeader .description');
                                        
                                        // Wait for CKEditor to be ready, then set the data
                                        setTimeout(function() {
                                            if (CKEDITOR.instances['contentheader']) {
                                                CKEDITOR.instances['contentheader'].setData(headerContent);
                                                console.log('Header description content set in CKEditor');
                                            }
                                        }, 500);
                                    } else if (CKEDITOR.instances['contentheader']) {
                                        // If already exists, set the data
                                        CKEDITOR.instances['contentheader'].setData(headerContent);
                                        console.log('Header description content updated in existing CKEditor');
                                    }
                                    
                                    // Initialize footer description editor
                                    var $footerEditor = $('.footerData textarea[name="contentfooter"]');
                                    var footerContent = response['form_footer_data'] && response['form_footer_data']['0'] ? response['form_footer_data']['0'] : '';
                                    
                                    if ($footerEditor.length > 0 && !CKEDITOR.instances['contentfooter']) {
                                        console.log('Initializing CKEditor for contentfooter');
                                        // Set the textarea value first
                                        $footerEditor.val(footerContent);
                                        initializeCKEditor('contentfooter', '.footer .footer-data__footerdescription');
                                        
                                        // Wait for CKEditor to be ready, then set the data
                                        setTimeout(function() {
                                            if (CKEDITOR.instances['contentfooter']) {
                                                CKEDITOR.instances['contentfooter'].setData(footerContent);
                                                console.log('Footer description content set in CKEditor');
                                            }
                                        }, 500);
                                    } else if (CKEDITOR.instances['contentfooter']) {
                                        // If already exists, set the data
                                        CKEDITOR.instances['contentfooter'].setData(footerContent);
                                        console.log('Footer description content updated in existing CKEditor');
                                    }
                                }, 300);
                            }
                        } else {
                            console.error('=== Failed to load form data ===');
                            console.error('Response:', response);
                            console.error('Response structure:', JSON.stringify(response, null, 2));
                            var errorMsg = response['msg'] || response['message'] || 'Failed to load form data. Please refresh the page and try again.';
                            console.error('Error message:', errorMsg);
                            alert('Error: ' + errorMsg + '\n\nCheck console for details.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('=== AJAX Error Loading Form Data ===');
                        console.error('Status:', status);
                        console.error('Error:', error);
                        console.error('Response Text:', xhr.responseText);
                        console.error('Response Status:', xhr.status);
                        console.error('Response Headers:', xhr.getAllResponseHeaders());
                        console.error('=== End Error Debug ===');
                        alert('An error occurred while loading the form data.\n\nStatus: ' + status + '\nError: ' + error + '\n\nPlease check the console for details.');
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
                        window.location.href = "form_design.php?form_id="+response["data"]+"&shop="+store;
                    }
                    loading_hide('.save_loader_show', 'Create Form');
                }
            });
        });

        function initializeCKEditor(editorName, targetElement) {
            console.log("initializeCKEditor called for:", editorName);
            // Try to find by ID first, then by name
            var editorElement = $('#' + editorName).length > 0 ? $('#' + editorName) : $('textarea[name="' + editorName + '"]');
            console.log("Found textarea elements:", editorElement.length);
            console.log("CKEditor instance exists:", !!CKEDITOR.instances[editorName]);
            
            if (editorElement.length > 0 && CKEDITOR.instances[editorName]){
                console.log("CKEditor instance already exists, destroying old instance");
                try {
                    CKEDITOR.instances[editorName].destroy();
                    console.log("Old instance destroyed");
                } catch(e) {
                    console.error("Error destroying old instance:", e);
                }
            }
            
            if (editorElement.length > 0 && !CKEDITOR.instances[editorName]) {
                try {
                    var editorInstance;
                    // Use ID/name string if available (CKEditor's preferred method)
                    if (editorElement.attr('id')) {
                        console.log("Initializing CKEditor using ID:", editorElement.attr('id'));
                        // Remove readonly/disabled attributes before initializing
                        editorElement.removeAttr('readonly').removeAttr('disabled');
                        editorInstance = CKEDITOR.replace(editorElement.attr('id'), {
                            readOnly: false, // Explicitly set to false
                            on: {
                                instanceReady: function(evt) {
                                    console.log('CKEditor instance ready for:', editorName);
                                    // Ensure editor is not read-only
                                    if (evt.editor.readOnly) {
                                        console.log('Editor was read-only, setting to editable');
                                        evt.editor.setReadOnly(false);
                                    }
                                    console.log('Editor readOnly status:', evt.editor.readOnly);
                                    evt.editor.on('change', function() {
                                        var editorData = evt.editor.getData();
                                        console.log('CKEditor content changed for:', editorName);
                                        if(editorName == "contentparagraph"){
                                            var mainContainerClass = editorElement.closest(".container").attr("class");
                                            var classArray = mainContainerClass.split(" ");
                                            var containerClass = classArray.find(className => className.startsWith("container_"));
                                            $(".block-container ."+containerClass).find(".globo-paragraph").html(editorData);
                                        }else{
                                            if (targetElement) {
                                                $(targetElement).html(editorData);
                                            }
                                        }
                                    });
                                    
                                    // Also update on blur
                                    evt.editor.on('blur', function() {
                                        var editorData = evt.editor.getData();
                                        if(editorName == "contentparagraph"){
                                            var mainContainerClass = editorElement.closest(".container").attr("class");
                                            var classArray = mainContainerClass.split(" ");
                                            var containerClass = classArray.find(className => className.startsWith("container_"));
                                            $(".block-container ."+containerClass).find(".globo-paragraph").html(editorData);
                                        }else{
                                            if (targetElement) {
                                                $(targetElement).html(editorData);
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    } else {
                        // Fallback to DOM element
                        console.log("Initializing CKEditor using DOM element");
                        // Remove readonly/disabled attributes before initializing
                        editorElement.removeAttr('readonly').removeAttr('disabled');
                        var domElement = editorElement[0];
                        editorInstance = CKEDITOR.replace(domElement, {
                            readOnly: false, // Explicitly set to false
                            on: {
                                instanceReady: function(evt) {
                                    console.log('CKEditor instance ready for:', editorName);
                                    // Ensure editor is not read-only
                                    if (evt.editor.readOnly) {
                                        console.log('Editor was read-only, setting to editable');
                                        evt.editor.setReadOnly(false);
                                    }
                                    console.log('Editor readOnly status:', evt.editor.readOnly);
                                    evt.editor.on('change', function() {
                                        var editorData = evt.editor.getData();
                                        if(editorName == "contentparagraph"){
                                            var mainContainerClass = editorElement.closest(".container").attr("class");
                                            var classArray = mainContainerClass.split(" ");
                                            var containerClass = classArray.find(className => className.startsWith("container_"));
                                            $(".block-container ."+containerClass).find(".globo-paragraph").html(editorData);
                                        }else{
                                            if (targetElement) {
                                                $(targetElement).html(editorData);
                                            }
                                        }
                                    });
                                    
                                    // Also update on blur
                                    evt.editor.on('blur', function() {
                                        var editorData = evt.editor.getData();
                                        if(editorName == "contentparagraph"){
                                            var mainContainerClass = editorElement.closest(".container").attr("class");
                                            var classArray = mainContainerClass.split(" ");
                                            var containerClass = classArray.find(className => className.startsWith("container_"));
                                            $(".block-container ."+containerClass).find(".globo-paragraph").html(editorData);
                                        }else{
                                            if (targetElement) {
                                                $(targetElement).html(editorData);
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    }
                    console.log("CKEditor initialized successfully for:", editorName, "Instance:", editorInstance);
                } catch(e) {
                    console.error("Error initializing CKEditor for", editorName, ":", e);
                    console.error("Error stack:", e.stack);
                }
            } else if (!editorElement.length) {
                console.warn("Cannot initialize CKEditor for", editorName, "- textarea not found");
                console.warn("Searched for: #" + editorName + " and textarea[name='" + editorName + "']");
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
            saveposition();
            saveform();
            saveheaderform();
            savefooterform();
            savepublishdata();
        });

        function saveposition(){
            var formdataid = $(".selected_element_set").sortable("toArray", { attribute: "data-formdataid" });
            console.log("Saving positions on form save:", formdataid);
            if (formdataid && formdataid.length > 0) {
                $.ajax({
                    url: "ajax_call.php",
                    type: "POST",
                    data: { 
                        routine_name: 'update_position', 
                        store: store, 
                        formdataid: formdataid 
                    },
                    success: function(response) {
                        console.log("Position saved successfully:", response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error saving position:", error, xhr.responseText);
                    }
                });
            } else {
                console.warn("No elements found to save positions");
            }
        }

        function  saveform(){
            var $content = CKEDITOR.instances["contentparagraph"];
            if($content != undefined){
                CKEDITOR.instances["contentparagraph"].updateElement();
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

        // Function to copy Form ID to clipboard (for form list)
        window.copyFormId = function(formId, element) {
            // Handle both string and number types (for 6-digit IDs)
            const formIdText = String(formId);
            
            // Try modern clipboard API first
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(formIdText).then(function() {
                    showCopySuccessInList(element);
                }).catch(function(err) {
                    console.error('Failed to copy:', err);
                    fallbackCopyFormId(formIdText, element);
                });
            } else {
                // Fallback for older browsers
                fallbackCopyFormId(formIdText, element);
            }
        };
        
        function fallbackCopyFormId(text, element) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopySuccessInList(element);
                } else {
                    alert('Failed to copy Form ID. Please copy manually: ' + text);
                }
            } catch (err) {
                console.error('Fallback copy failed:', err);
                alert('Failed to copy Form ID. Please copy manually: ' + text);
            }
            
            document.body.removeChild(textArea);
        }
        
        function showCopySuccessInList(element) {
            if (element && element.parentElement) {
                const successMsg = element.parentElement.querySelector('.copy-success');
                if (successMsg) {
                    successMsg.style.display = 'inline';
                    setTimeout(function() {
                        successMsg.style.display = 'none';
                    }, 2000);
                }
            }
        }

        // File  