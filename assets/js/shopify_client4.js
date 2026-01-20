"use strict";
var CLS_LOADER = '<svg viewBox="0 0 20 20" class="Polaris-Spinner Polaris-Spinner--colorInkLightest Polaris-Spinner--sizeSmall" aria-label="Loading" role="status"><path d="M7.229 1.173a9.25 9.25 0 1 0 11.655 11.412 1.25 1.25 0 1 0-2.4-.698 6.75 6.75 0 1 1-8.506-8.329 1.25 1.25 0 1 0-.75-2.385z"></path></svg>';
var CLS_DELETE = '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M16 6a1 1 0 1 1 0 2h-1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8H4a1 1 0 1 1 0-2h12zM9 4a1 1 0 1 1 0-2h2a1 1 0 1 1 0 2H9zm2 12h2V8h-2v8zm-4 0h2V8H7v8z" fill="#000" fill-rule="evenodd"></path></svg>';
var CLS_MINUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 80 80" focusable="false" aria-hidden="true"><path d="M39.769,0C17.8,0,0,17.8,0,39.768c0,21.956,17.8,39.768,39.769,39.768   c21.965,0,39.768-17.812,39.768-39.768C79.536,17.8,61.733,0,39.769,0z M13.261,45.07V34.466h53.014V45.07H13.261z" fill-rule="evenodd" fill="#DE3618"></path></svg>';
var CLS_PLUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M17 9h-6V3a1 1 0 1 0-2 0v6H3a1 1 0 1 0 0 2h6v6a1 1 0 1 0 2 0v-6h6a1 1 0 1 0 0-2" fill-rule="evenodd"></path></svg>';
var CLS_CIRCLE_MINUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 80 80" focusable="false" aria-hidden="true"><path d="M39.769,0C17.8,0,0,17.8,0,39.768c0,21.956,17.8,39.768,39.769,39.768   c21.965,0,39.768-17.812,39.768-39.768C79.536,17.8,61.733,0,39.769,0z M13.261,45.07V34.466h53.014V45.07H13.261z" fill-rule="evenodd" fill="#DE3618"></path></svg>';
var CLS_CIRCLE_PLUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 510 510" focusable="false" aria-hidden="true"><path d="M255,0C114.75,0,0,114.75,0,255s114.75,255,255,255s255-114.75,255-255S395.25,0,255,0z M382.5,280.5h-102v102h-51v-102    h-102v-51h102v-102h51v102h102V280.5z" fill-rule="evenodd" fill="#3f4eae"></path></svg>';
var BACKTO = 0;

// Global object to store all element changes
var elementChangesTracker = {
    elements: {}, // Store element form data by formdataid
    designSettings: {}, // Store design settings by formdataid
    header: null, // Store header form data
    footer: null  // Store footer form data
};

// Function to save current element form data to tracker
function saveElementFormToTracker(formdataid) {
    if (!formdataid) return;

    var $form = $('form.add_elementdata[formdataid="' + formdataid + '"]');
    if ($form.length === 0) return;

    // Update CKEditor if exists
    if (typeof CKEDITOR !== 'undefined') {
        var $contentEditor = $form.find('textarea[name="contentparagraph"]');
        if ($contentEditor.length && CKEDITOR.instances['contentparagraph']) {
            CKEDITOR.instances['contentparagraph'].updateElement();
        }
    }

    var formData = new FormData($form[0]);
    var elementData = {};

    // Convert FormData to object
    for (var pair of formData.entries()) {
        var key = pair[0];
        var value = pair[1];

        if (key.endsWith('[]')) {
            key = key.replace('[]', '');
            if (!elementData[key]) {
                elementData[key] = [];
            }
            elementData[key].push(value);
        } else {
            elementData[key] = value;
        }
    }

    // Add file extension - always include, even if empty (to clear database when all are removed)
    var val = $form.closest('.form-control, .header, .Polaris-Card__Section, .Polaris-Card').find('.selectFile').select2('val');
    // Always set allowextention, even if empty array or null (to allow clearing from database)
    elementData['allowextention'] = val || [];

    // Store in tracker
    elementChangesTracker.elements[formdataid] = elementData;

    // Also save design settings to tracker
    var borderRadiusVal = $('.element-design-border-radius[data-formdataid="' + formdataid + '"]').val();
    var borderRadius = (borderRadiusVal !== '' && borderRadiusVal !== null && borderRadiusVal !== undefined) ? parseInt(borderRadiusVal) : 4;
    if (isNaN(borderRadius) || borderRadius < 0) {
        borderRadius = 4;
    }
    var designSettings = {
        fontSize: 16,
        labelFontSize: parseInt($('.element-design-label-font-size[data-formdataid="' + formdataid + '"]').val()) || 16,
        inputFontSize: parseInt($('.element-design-input-font-size[data-formdataid="' + formdataid + '"]').val()) || 16,
        fontWeight: $('.element-design-font-weight[data-formdataid="' + formdataid + '"]').val() || '400',
        color: $('.element-design-color-text[data-formdataid="' + formdataid + '"]').val() || '#000000',
        borderRadius: borderRadius,
        bgColor: $('.element-design-bg-color-text[data-formdataid="' + formdataid + '"]').val() || '',
        optionColor: $('.element-design-option-color-text[data-formdataid="' + formdataid + '"]').val() || '#000000',
        checkmarkColor: $('.element-design-checkmark-color-text[data-formdataid="' + formdataid + '"]').val() || '#000000'
    };
    elementChangesTracker.designSettings[formdataid] = designSettings;
}

// Fix for "store is not defined" error
if (typeof store === 'undefined' || store === "") {
    // Try to get from URL params
    var urlParams = new URLSearchParams(window.location.search);
    var store = urlParams.get('shop');

    // Fallback to window.Shopify object if available
    if (!store && window.Shopify && window.Shopify.shop) {
        store = window.Shopify.shop;
    }

    // Fallback to parent logic or empty string to prevent ReferenceError
    if (!store) {
        // Try getting from referrer if iframe
        if (window.location != window.parent.location) {
            var referrer = document.referrer;
            // Simple extraction attempt (might need refinement based on exact referrer format)
            if (referrer.indexOf('shop=') > -1) {
                var match = referrer.match(/shop=([^&]*)/);
                if (match) store = match[1];
            }
        }
    }

    if (!store) store = ""; // Define it as empty string at minimum
}

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
function removeFromTable(tableName, ID, id, pageno, tableId, api_name, is_delete) {
    var is_delete = (is_delete == undefined) ? 'Record' : is_delete;
    var Ajaxdelete = function Ajaxdelete() {
        var el = is_delete;
        $.ajax({
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            data: { routine_name: 'remove_from_table', store: store, table_name: tableName, id: id, ID: ID, api_name: api_name },
            beforeSend: function () {
                loading_show('.save_loader_show' + ID);
            },
            success: function (response) {
                loading_hide('save_loader_show' + ID, '', CLS_DELETE);
                if (response['result'] == 'success') {
                    if (pageno == undefined || pageno < 0 || response['total_record'] <= 0) {
                        setCookie('flash_message', response['message'], 2);
                        location.reload();
                    } else if (pageno > 0) {
                        $(is_delete).closest("tr").css("background", "tomato");
                        $(is_delete).closest("tr").fadeOut(800, function () {
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
// Function to update form count
function updateFormCount() {
    var count = $(".set_all_form .Polaris-ResourceList__HeaderWrapper").length;
    $('.dataAdded').html('Showing ' + count + (count === 1 ? ' form' : ' forms'));
}

$(document).ready(function () {
    // Update count after a short delay to ensure DOM is ready
    setTimeout(function () {
        updateFormCount();
    }, 200);

    // $('.myeditor').each(function(index,item){
    //     CKEDITOR.replace(item);
    // });
});

function btn_enable_disable(form_id) {
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'btn_enable_disable', store: store },
        success: function (comeback) {
            if (comeback['outcome']['data']['status'] != undefined && comeback['outcome']['data']['status'] == 0) {
                $("#register_frm_btn").attr('disabled', true);
                $(".app-setting-msg").show();
            } else {
                $("#register_frm_btn").attr('disabled', false);
            }
        }
    });
}

function seeting_enable_disable(form_id) {
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'btn_enable_disable', 'store': store, 'form_id': form_id },
        success: function (comeback) {
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

function get_api_data(routineName, shopify_api) {
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

$(document).on("click", ".enable-btn", function (event) {
    event.preventDefault();

    var $form_id = $('.form_id').val();
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
        data: { 'store': store, 'routine_name': 'enable_disable', 'btnval': btnval, 'form_id': $form_id },

        success: function (response) {
            if (response['code'] != undefined && response['code'] == '403') {
                redirect403();
            }
        }
    });
});

// start 014
$(document).on("click", ".btn_add_element", function (event) {
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
                setTimeout(function () {
                    if (typeof window.navigateToSlide === 'function') {
                        window.navigateToSlide(6); // data-owl="6" for Add element
                    } else {
                    }
                }, 200);
            } else {
                $carousel.owlCarousel({
                    items: 1,
                    loop: false,
                    margin: 10,
                    nav: false,
                    mouseDrag: false,
                });
                setTimeout(function () {
                    if (typeof window.navigateToSlide === 'function') {
                        window.navigateToSlide(6); // data-owl="6" for Add element
                    } else {
                    }
                }, 200);
            }
        } else {
            // Carousel is already initialized, use navigateToSlide for proper mapping
            if (typeof window.navigateToSlide === 'function') {
                window.navigateToSlide(6); // data-owl="6" for Add element
            } else {
                try {
                    $carousel.trigger('to.owl.carousel', [6, 40, true]);
                } catch (e) {
                }
            }
        }
    }

    // Then load the element data
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'get_all_element_fun', store: store },
        success: function (comeback) {
            var comeback = JSON.parse(comeback);
            if (comeback['code'] != undefined && comeback['code'] == '403') {
                redirect403();
            } else {
                $(".setvalue_element").html(comeback['outcome']);
                $(".setvalue_element_select").html(comeback['outcome2']);
                $(".setvalue_element_static").html(comeback['outcome3']);
                $(".setvalue_element_structure").html(comeback['outcome4']);
                $(".setvalue_element_customization").html(comeback['outcome5']);
            }
        }
    });
});

$(document).on("click", ".element_coppy_to", function (event) {
    event.preventDefault();
    var thisObj = $(this);
    var formid = $(".formid").val();
    var elementid = $(this).find(".get_element_hidden").val();

    // Validate formid and elementid
    if (!formid || formid === '') {
        return false;
    }

    if (!elementid || elementid === '') {
        return false;
    }

    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'set_element', store: store, 'get_element_hidden': elementid, 'formid': formid },
        success: function (comeback) {
            // Parse JSON if it's a string, otherwise use as-is
            var response = (typeof comeback === 'string') ? JSON.parse(comeback) : comeback;

            var formdata_id = response["last_id"] !== undefined ? response["last_id"] : "";

            if (response['code'] != undefined && response['code'] == '403') {
                redirect403();
            } else if (response['data'] === 'success') {
                // Navigate back to form builder view
                $('.owl-carousel').trigger('to.owl.carousel', [BACKTO, 40, true]);

                // Immediately refresh to show the new element
                // Use minimal delay and aggressive retry mechanism
                setTimeout(function () {
                    var retryCount = 0;
                    var maxRetries = 20; // Increased retries for better reliability
                    var retryDelay = 250; // Slightly longer delay between retries for better reliability

                    function refreshWithRetry() {
                        // Refresh elements and preview
                        // Store callback to check after refresh
                        var checkCallback = function () {
                            if (formdata_id) {
                                // Delay to ensure DOM is fully updated after get_selected_elements completes
                                setTimeout(function () {
                                    // Check if element appears in preview and sidebar
                                    // Try multiple selectors to be sure
                                    var $previewElement = $('.code-form-app [data-formdataid="' + formdata_id + '"]');
                                    var $previewElementAlt = $('.contact-form [data-formdataid="' + formdata_id + '"]');
                                    var $sidebarElement = $('.selected_element_set [data-formdataid="' + formdata_id + '"]');

                                    var foundInPreview = ($previewElement.length > 0 || $previewElementAlt.length > 0);
                                    var foundInSidebar = ($sidebarElement.length > 0);

                                    // If element not found and we haven't exceeded retries, try again
                                    if ((!foundInPreview || !foundInSidebar) && retryCount < maxRetries) {
                                        retryCount++;
                                        setTimeout(refreshWithRetry, retryDelay);
                                    } else {
                                        // Element found or max retries reached
                                        if (foundInSidebar && foundInPreview) {
                                            // Scroll the element into view
                                            $('html, body').animate({
                                                scrollTop: $sidebarElement.offset().top - 100
                                            }, 300);

                                            // Highlight the new element briefly
                                            $sidebarElement.css({
                                                'background-color': '#e3f2fd',
                                                'transition': 'background-color 0.5s'
                                            });
                                            setTimeout(function () {
                                                $sidebarElement.css('background-color', '');
                                            }, 2000);
                                        }
                                    }
                                }, 400); // Increased delay to ensure DOM is fully updated and rendered
                            }
                        };

                        get_selected_elements(formid, checkCallback);
                    }

                    // Start the refresh with retry immediately
                    refreshWithRetry();
                }, 400); // Increased delay to ensure database transaction is committed
            }
        },
        error: function (xhr, status, error) {
            // Silent error handling
        }
    });
});

function insertDefaultElements(form_id, selectedType) {
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'insertDefaultElements', store: store, "form_id": form_id, "form_type": selectedType },
        success: function (comeback) {
            var comeback = JSON.parse(comeback);
            if (comeback['code'] != undefined && comeback['code'] == '403') {
                redirect403();
            } else {
            }
        }
    });
}

function get_selected_elements(form_id, callback) {
    if (!form_id || form_id === '' || form_id === 0) {
        if (callback) callback();
        return;
    }

    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'get_selected_elements_fun', 'form_id': form_id, store: store, '_t': new Date().getTime() },
        success: function (comeback) {
            var response = (typeof comeback === 'string') ? JSON.parse(comeback) : comeback;

            // Count how many elements are in the outcome
            if (response['outcome']) {
                var elementCount = (response['outcome'].match(/data-formdataid=/g) || []).length;
            }

            if (response['code'] != undefined && response['code'] == '403') {
                redirect403();
                if (callback) callback();
                return;
            } else if (response['data'] === 'success') {
                if (response['form_type'] == "4") {
                    $(".preview-box").addClass("floting_form_main");
                }
                if (response['form_header_data'] && response['form_header_data']['0'] == 1) {
                    $(".headerData .showHeader").prop("checked", true);
                } else {
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
                setTimeout(function () {
                    if (typeof window.updateHeaderPreview === 'function') {
                        window.updateHeaderPreview();
                    }
                }, 300);

                // Set form name - prefer form_name from response, fallback to header title
                var formName = response['form_name'] || (response['form_header_data'] && response['form_header_data']['1'] ? response['form_header_data']['1'] : 'Blank Form');
                $(".form_name_form_design").val(formName);

                $(".selected_element_set").html(response['outcome'] || '');

                // For floating forms, insert directly and strip outer wrapper
                if (response['form_type'] == "4") {
                    try {
                        // Create a temporary div to parse the HTML
                        var formHtml = response['form_html'] || '';

                        // Basic sanitization - remove any script tags and malformed HTML
                        if (formHtml && typeof formHtml === 'string') {
                            // Remove script tags to prevent XSS and parsing errors
                            formHtml = formHtml.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                            // Remove any unclosed tags that might cause parsing errors
                            formHtml = formHtml.replace(/<if\b[^>]*>/gi, ''); // Remove any stray <if> tags
                        }

                        var $temp = $('<div>').html(formHtml);

                        // Find the actual form content (inside form-builder-wrapper)
                        var $formContent = $temp.find('.code-form-app');

                        // If we found the inner content, use it; otherwise use original
                        var contentToInsert = $formContent.length > 0 ? $formContent.html() : formHtml;

                        // Insert into .contact-form, replacing its content entirely
                        // Simple validation: only skip if content is clearly empty
                        if (contentToInsert && typeof contentToInsert === 'string' && contentToInsert.trim().length > 0) {
                            $(".preview-box .contact-form").html(contentToInsert);
                        }
                        // If contentToInsert is empty, don't update - keep existing content

                        // Add the floating class
                        $(".preview-box").addClass("floting_form_main");
                    } catch (e) {
                        // Don't clear on error - keep existing content to prevent losing all elements
                        // $(".preview-box .contact-form").html(''); // Commented out to prevent clearing
                    }

                } else {
                    // Regular form handling
                    try {
                        var formHtml = response['form_html'] || '';

                        // Basic sanitization - remove any script tags and malformed HTML
                        if (formHtml && typeof formHtml === 'string') {
                            // Remove script tags to prevent XSS and parsing errors
                            formHtml = formHtml.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                            // Remove any unclosed tags that might cause parsing errors
                            formHtml = formHtml.replace(/<if\b[^>]*>/gi, ''); // Remove any stray <if> tags
                        }

                        // Update preview when we have HTML from a successful response
                        // But only if it contains actual form elements (not just empty form structure)
                        if (formHtml && typeof formHtml === 'string' && formHtml.trim().length > 0) {
                            var elementCount = (formHtml.match(/data-formdataid=/g) || []).length;

                            // Check current preview to see if it has elements
                            var currentPreviewHtml = $(".code-form-app").html() || '';
                            var currentElementCount = (currentPreviewHtml.match(/data-formdataid=/g) || []).length;

                            // Only update if we have elements OR if this is a valid form structure with header/footer
                            // But be very strict: if current preview has elements and new HTML has 0, don't update
                            // This prevents clearing when backend returns stale data before delete is committed
                            if (elementCount > 0) {
                                // Has elements - always safe to update
                                $(".code-form-app").html(formHtml);
                                // Reset stale data flag and clear manually removed elements list
                                window._lastRefreshHadStaleData = false;
                                window._manuallyRemovedElements = [];
                            } else if (elementCount === 0 && currentElementCount > 0) {
                                // New HTML has 0 elements but current preview has elements
                                // This means backend returned stale/invalid data (likely before delete committed)
                                // Set flag to retry
                                if (callback) {
                                    window._lastRefreshHadStaleData = true;
                                }
                            } else if (elementCount === 0) {
                                // Reset flag if we got valid data
                                window._lastRefreshHadStaleData = false;
                                // Both have 0 elements - check if it's a valid form structure with header/footer
                                var hasHeader = formHtml.indexOf('formHeader') !== -1 || formHtml.indexOf('class="formHeader"') !== -1;
                                var hasFooter = formHtml.indexOf('forFooterAlign') !== -1 || formHtml.indexOf('class="forFooterAlign"') !== -1 || formHtml.indexOf('Submit') !== -1;

                                if (hasHeader || hasFooter) {
                                    // No elements but has header/footer - valid form structure (form with only header/footer)
                                    $(".code-form-app").html(formHtml);
                                }
                            }
                        }
                    } catch (e) {
                        // Don't clear on error - keep existing content to prevent losing all elements
                        // $(".code-form-app").html(''); // Commented out to prevent clearing
                    }
                }

                $(".footerData .form_id").val(response['form_id']);
                if (response['form_footer_data']) {
                    $('.footerData .myeditor').html(response['form_footer_data']['0'] || '');
                    $('.footerData .submitText').val(response['form_footer_data']['1'] || 'Submit');
                    if (response['form_footer_data']['2'] == 1) {
                        $(".footerData .resetButton").prop("checked", true);
                        $(".input_reset").removeClass("hidden");
                    } else {
                        $(".input_reset").addClass("hidden");
                        $(".footerData .resetButton").prop("checked", false);
                    }
                    $('.footerData .resetbuttonText').val(response['form_footer_data']['3'] || 'Reset');
                    if (response['form_footer_data']['4'] == 1) {
                        $(".footerData .fullFooterButton").prop("checked", true);
                    } else {
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
                    setTimeout(function () {
                        $(".forFooterAlign").removeClass("align-left align-center align-right").addClass(footerAlignment);
                    }, 100);

                    // Load button design settings (new format with 14 elements for reset button colors, or fallback to defaults)
                    var footerDataLength = response['form_footer_data'] ? response['form_footer_data'].length : 0;
                    if (footerDataLength >= 11) {
                        // Submit button settings
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

                        // Reset button settings (if available)
                        if (footerDataLength >= 14) {
                            var resetButtonTextColor = (response['form_footer_data'] && response['form_footer_data']['11']) ? response['form_footer_data']['11'] : '#ffffff';
                            var resetButtonBgColor = (response['form_footer_data'] && response['form_footer_data']['12']) ? response['form_footer_data']['12'] : '#EB1256';
                            var resetButtonHoverBgColor = (response['form_footer_data'] && response['form_footer_data']['13']) ? response['form_footer_data']['13'] : '#292929';

                            $('.footer-design-reset-button-text-color').val(resetButtonTextColor);
                            $('.footer-design-reset-button-text-color-text').val(resetButtonTextColor);
                            $('.footer-design-reset-button-bg-color').val(resetButtonBgColor);
                            $('.footer-design-reset-button-bg-color-text').val(resetButtonBgColor);
                            $('.footer-design-reset-button-hover-bg-color').val(resetButtonHoverBgColor);
                            $('.footer-design-reset-button-hover-bg-color-text').val(resetButtonHoverBgColor);
                        } else {
                            // Use defaults for reset button
                            $('.footer-design-reset-button-text-color').val('#ffffff');
                            $('.footer-design-reset-button-text-color-text').val('#ffffff');
                            $('.footer-design-reset-button-bg-color').val('#EB1256');
                            $('.footer-design-reset-button-bg-color-text').val('#EB1256');
                            $('.footer-design-reset-button-hover-bg-color').val('#292929');
                            $('.footer-design-reset-button-hover-bg-color-text').val('#292929');
                        }
                    } else {
                        // Old format: use defaults for both submit and reset buttons
                        $('.footer-design-button-text-size').val(16);
                        $('.footer-design-button-text-color').val('#ffffff');
                        $('.footer-design-button-text-color-text').val('#ffffff');
                        $('.footer-design-button-bg-color').val('#EB1256');
                        $('.footer-design-button-bg-color-text').val('#EB1256');
                        $('.footer-design-button-hover-bg-color').val('#C8104A');
                        $('.footer-design-button-hover-bg-color-text').val('#C8104A');
                        $('.footer-design-button-border-radius').val(4);

                        $('.footer-design-reset-button-text-color').val('#ffffff');
                        $('.footer-design-reset-button-text-color-text').val('#ffffff');
                        $('.footer-design-reset-button-bg-color').val('#EB1256');
                        $('.footer-design-reset-button-bg-color-text').val('#EB1256');
                        $('.footer-design-reset-button-hover-bg-color').val('#292929');
                        $('.footer-design-reset-button-hover-bg-color-text').val('#292929');
                    }

                    // Apply to preview (function defined in form_design.php)
                    setTimeout(function () {
                        if (typeof window.updateFooterButtonPreview === 'function') {
                            window.updateFooterButtonPreview();
                        }
                    }, 300);
                }
                if (response['publishdata']) {
                    if (response['publishdata']['0'] == 1) {
                        $('.required_login').prop("checked", true);
                    } else {
                        $('.required_login').prop("checked", false);
                    }
                    $('.login_message').html(response['publishdata']['1'] || '');
                    $('.embed_code').val('<div data-formid="' + (response['publishdata']['2'] || '') + '"></div>');
                }
                $(".selected_element_set").sortable({
                    opacity: 0.6,
                    cursor: 'move',
                    handle: '.softable', // Use the sort handle
                    update: function (event, ui) {
                        // Save position immediately when element is dragged
                        var formdataid = $(this).sortable("toArray", { attribute: "data-formdataid" });
                        $.ajax({
                            url: "ajax_call.php",
                            type: "POST",
                            data: {
                                routine_name: 'update_position',
                                store: store,
                                formdataid: formdataid
                            },
                            success: function (response) {
                            },
                            error: function (xhr, status, error) {
                            }
                        });
                    }
                });

                // Ensure owl carousel is initialized after content loads
                setTimeout(function () {
                    var $carousel = $('.owl-carousel');
                    if ($carousel.length > 0 && typeof $carousel.data('owl.carousel') === 'undefined') {
                        if (typeof initOwlCarousel === 'function') {
                            initOwlCarousel();
                        } else {
                            // Fallback initialization
                            $carousel.owlCarousel({
                                items: 1,
                                loop: false,
                                margin: 10,
                                nav: false,
                                mouseDrag: false,
                            });
                        }
                    }
                }, 200);

                // Initialize CKEditor for header and footer
                if (typeof CKEDITOR !== 'undefined') {
                    setTimeout(function () {
                        // Initialize header description editor
                        var $headerEditor = $('.headerData textarea[name="contentheader"]');
                        var headerContent = response['form_header_data'] && response['form_header_data']['2'] ? response['form_header_data']['2'] : '';

                        if ($headerEditor.length > 0 && !CKEDITOR.instances['contentheader']) {
                            // Set the textarea value first
                            $headerEditor.val(headerContent);
                            initializeCKEditor('contentheader', '.boxed-layout .formHeader .description');

                            // Wait for CKEditor to be ready, then set the data
                            setTimeout(function () {
                                if (CKEDITOR.instances['contentheader']) {
                                    CKEDITOR.instances['contentheader'].setData(headerContent);
                                }
                            }, 500);
                        } else if (CKEDITOR.instances['contentheader']) {
                            // If already exists, set the data
                            CKEDITOR.instances['contentheader'].setData(headerContent);
                        }

                        // Initialize footer description editor
                        var $footerEditor = $('.footerData textarea[name="contentfooter"]');
                        var footerContent = response['form_footer_data'] && response['form_footer_data']['0'] ? response['form_footer_data']['0'] : '';

                        if ($footerEditor.length > 0 && !CKEDITOR.instances['contentfooter']) {
                            // Set the textarea value first
                            $footerEditor.val(footerContent);
                            initializeCKEditor('contentfooter', '.footer .footer-data__footerdescription');

                            // Wait for CKEditor to be ready, then set the data
                            setTimeout(function () {
                                if (CKEDITOR.instances['contentfooter']) {
                                    CKEDITOR.instances['contentfooter'].setData(footerContent);
                                }
                            }, 500);
                        } else if (CKEDITOR.instances['contentfooter']) {
                            // If already exists, set the data
                            CKEDITOR.instances['contentfooter'].setData(footerContent);
                        }
                    }, 300);
                }

                // Call callback after all DOM updates are complete
                if (callback) {
                    // Delay to ensure DOM is fully updated
                    setTimeout(function () {
                        callback();
                    }, 300); // Increased delay to ensure DOM is fully updated and rendered
                }

                // Initialize star rating handlers after form HTML is loaded
                setTimeout(function () {
                    if (typeof window.initializeStarRatingHandlers === 'function') {
                        window.initializeStarRatingHandlers();
                    }
                }, 500);
            } else {
                var errorMsg = response['msg'] || response['message'] || 'Failed to load form data. Please refresh the page and try again.';
                // Call callback even on failure
                if (callback) callback();
            }
        },
        error: function (xhr, status, error) {
            // Call callback on error as well
            if (callback) callback();
        }
    });
}

function getFormTitle(form_id) {
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'getFormTitleFun', 'form_id': form_id, store: store },
        success: function (comeback) {
            var comeback = JSON.parse(comeback);
            if (comeback['code'] != undefined && comeback['code'] == '403') {
                redirect403();
            } else {
                $(".form_name_form_design").val(comeback['outcome'])
            }
        }
    });
}

$(document).on("click", ".btnFormSubmit", function (event) {
    var formid = $(".formid").val();
    var form_name = $(".form_name_form_design").val();
    event.preventDefault();
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'insertFormData', store: store, 'formid': formid, 'form_name': form_name },
        beforeSend: function () {
            loading_show('.save_loader_show');
        },
        success: function (comeback) {
            var comeback = JSON.parse(comeback);
            if (comeback['code'] != undefined && comeback['code'] == '403') {
                redirect403();
            } else {
                // $(".").html(comeback['outcome'])
            }
            loading_hide('.save_loader_show', 'Save');
        }
    });
});


function getAllForm() {
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'getAllFormFunction', store: store },
        success: function (comeback) {
            var comeback = JSON.parse(comeback);
            if (comeback['code'] != undefined && comeback['code'] == '403') {
                redirect403();
            } else {
                $(".set_all_form").html(comeback['outcome']);
                // Update form count after forms are loaded
                setTimeout(function () {
                    updateFormCount();
                }, 100);
            }
        }
    });
}

function getAllFormSubmissions() {
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'getAllFormFunction', store: store, view_type: 'submissions_dashboard' },
        success: function (comeback) {
            var comeback = JSON.parse(comeback);
            if (comeback['code'] != undefined && comeback['code'] == '403') {
                redirect403();
            } else {
                $(".set_all_form_submissions").html(comeback['outcome'])
            }
        }
    });
}

$(document).on("click", ".btncreate_new", function (event) {
    event.preventDefault();
    var selectedType = $(".selectedType").val();
    var form_data = $("#createNewForm")[0];
    var form_data = new FormData(form_data);
    form_data.append('store', store);
    form_data.append('routine_name', 'function_create_form');
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
            } else {
                $(".text_image_list").removeClass("first_txt_image");
                $(".firstone_").addClass("first_txt_image");
                insertDefaultElements(response["data"], selectedType);
                window.location.href = "form_design.php?form_id=" + response["data"] + "&shop=" + store;
            }
            loading_hide('.save_loader_show', 'Create Form');
        }
    });
});

function initializeCKEditor(editorName, targetElement) {
    // Try to find by ID first, then by name
    var editorElement = $('#' + editorName).length > 0 ? $('#' + editorName) : $('textarea[name="' + editorName + '"]');

    if (editorElement.length > 0) {
        // If it's already a CKEditor instance, check if we need to recreate it
        if (CKEDITOR.instances[editorName]) {
            try {
                // If it's already there and not read-only, just return the instance
                if (!CKEDITOR.instances[editorName].readOnly) {
                    return CKEDITOR.instances[editorName];
                }
                CKEDITOR.instances[editorName].destroy();
            } catch (e) {
            }
        }

        try {
            // Remove readonly/disabled attributes before initializing
            editorElement.removeAttr('readonly').removeAttr('disabled');

            var editorInstance = CKEDITOR.replace(editorName, {
                readOnly: false, // Explicitly set to false at initialization
                on: {
                    instanceReady: function (evt) {
                        // Double check readOnly state
                        if (evt.editor.readOnly) {
                            evt.editor.setReadOnly(false);
                        }

                        // Focus the editor to ensure it's interactive
                        // evt.editor.focus(); 

                        evt.editor.on('change', function () {
                            var editorData = evt.editor.getData();
                            if (editorName == "contentparagraph") {
                                var mainContainerClass = editorElement.closest(".container").attr("class");
                                var classArray = mainContainerClass.split(" ");
                                var containerClass = classArray.find(className => className.startsWith("container_"));
                                $(".block-container ." + containerClass).find(".globo-paragraph").html(editorData);
                            } else {
                                if (targetElement) {
                                    $(targetElement).html(editorData);
                                }
                            }
                        });

                        // Also update on blur
                        evt.editor.on('blur', function () {
                            var editorData = evt.editor.getData();
                            if (targetElement) {
                                $(targetElement).html(editorData);
                            }
                        });

                        // Also update on keyup for real-time preview
                        evt.editor.on('keyup', function () {
                            var editorData = evt.editor.getData();
                            if (targetElement) {
                                $(targetElement).html(editorData);
                            }
                        });
                    }
                }
            });
            return editorInstance;
        } catch (e) {
        }
    }
}

$(document).on("click", ".Polaris-Tabs__Panel .list-item", function () {
    var slideTo = $(this).data("owl");
    var elementId = $(this).data("elementid");
    var formId = $(this).closest(".clsselected_element").data("formid");
    var formdataId = $(this).closest(".clsselected_element").data("formdataid");

    // Save current form data to tracker before switching
    var currentFormdataid = $('form.add_elementdata').attr('formdataid');
    if (currentFormdataid) {
        saveElementFormToTracker(currentFormdataid);
    }

    $('.owl-carousel').trigger('to.owl.carousel', [slideTo, 40, true]);
    if (elementId != undefined) {
        $.ajax({
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            data: { 'routine_name': 'form_element_data_html', store: store, "elementid": elementId, "formid": formId, "formdataid": formdataId },
            success: function (comeback) {
                var comeback = JSON.parse(comeback);
                if (comeback['code'] != undefined && comeback['code'] == '403') {
                } else {
                    $(".elementAppend").html(comeback.outcome);
                    initializeCKEditor('contentparagraph', '');

                    // Initialize Select2 for file extensions dropdown (without placeholder)
                    setTimeout(function () {
                        var $selectFile = $('.selectFile');

                        // Read selected values from HTML BEFORE initializing Select2
                        // This ensures we capture the values that were set by PHP from database
                        var selectedValues = [];
                        $selectFile.find('option[selected]').each(function () {
                            var val = $(this).val();
                            if (val && val !== '') {
                                selectedValues.push(val);
                            }
                        });

                        // Initialize Select2
                        $selectFile.select2({
                            placeholder: '',
                            allowClear: false,
                            minimumResultsForSearch: Infinity
                        });

                        // Set the selected values after Select2 is initialized
                        if (selectedValues.length > 0) {
                            $selectFile.val(selectedValues).trigger('change');
                        }
                    }, 150);

                    // Restore saved data if exists in tracker
                    if (elementChangesTracker.elements[formdataId]) {
                        var savedData = elementChangesTracker.elements[formdataId];
                        var $newForm = $('form.add_elementdata[formdataid="' + formdataId + '"]');
                        if ($newForm.length) {
                            // Restore form values
                            for (var key in savedData) {
                                if (key === 'allowextention') continue; // Skip, handled separately
                                var $field = $newForm.find('[name="' + key + '"]');
                                if ($field.length) {
                                    if ($field.is(':checkbox') || $field.is(':radio')) {
                                        $field.prop('checked', savedData[key] == $field.val());
                                    } else if ($field.is('select')) {
                                        $field.val(savedData[key]);
                                        if ($field.hasClass('select2')) {
                                            $field.trigger('change');
                                        }
                                    } else {
                                        $field.val(savedData[key]);
                                        // Update CKEditor if it's a textarea
                                        if ($field.is('textarea') && CKEDITOR.instances[key]) {
                                            CKEDITOR.instances[key].setData(savedData[key]);
                                        }
                                    }
                                }
                            }

                            // Restore allowextention (file extensions) from tracker after Select2 is initialized
                            if (savedData['allowextention']) {
                                setTimeout(function () {
                                    var $selectFile = $newForm.find('.selectFile');
                                    if ($selectFile.length && savedData['allowextention']) {
                                        var extensions = Array.isArray(savedData['allowextention']) ? savedData['allowextention'] : [savedData['allowextention']];
                                        $selectFile.val(extensions).trigger('change');
                                    }
                                }, 250);
                            }
                        }
                    }

                    // Restore saved design settings if exists in tracker
                    if (elementChangesTracker.designSettings[formdataId]) {
                        setTimeout(function () {
                            var savedDesign = elementChangesTracker.designSettings[formdataId];

                            // Restore Label and Input font sizes
                            var labelFontSize = savedDesign.labelFontSize || savedDesign.fontSize || 16;
                            var inputFontSize = savedDesign.inputFontSize || savedDesign.fontSize || 16;

                            $('.element-design-label-font-size[data-formdataid="' + formdataId + '"]').val(labelFontSize);
                            $('.element-design-input-font-size[data-formdataid="' + formdataId + '"]').val(inputFontSize);

                            $('.element-design-font-weight[data-formdataid="' + formdataId + '"]').val(savedDesign.fontWeight || '400');
                            $('.element-design-color-text[data-formdataid="' + formdataId + '"]').val(savedDesign.color || '#000000');
                            $('.element-design-border-radius[data-formdataid="' + formdataId + '"]').val(savedDesign.borderRadius || 4);
                            if (savedDesign.bgColor) {
                                $('.element-design-bg-color-text[data-formdataid="' + formdataId + '"]').val(savedDesign.bgColor);
                            }
                            // Restore Option and Checkmark colors
                            if (savedDesign.optionColor) {
                                $('.element-design-option-color-text[data-formdataid="' + formdataId + '"]').val(savedDesign.optionColor);
                                $('.element-design-option-color[data-formdataid="' + formdataId + '"]').val(savedDesign.optionColor);
                            }
                            if (savedDesign.checkmarkColor) {
                                $('.element-design-checkmark-color-text[data-formdataid="' + formdataId + '"]').val(savedDesign.checkmarkColor);
                                $('.element-design-checkmark-color[data-formdataid="' + formdataId + '"]').val(savedDesign.checkmarkColor);
                            }
                            // Update preview immediately
                            if (typeof updateElementDesignPreview === 'function') {
                                updateElementDesignPreview(formdataId);
                            }
                        }, 100);

                        // Also update preview after a short delay to ensure DOM is ready
                        setTimeout(function () {
                            if (typeof updateElementDesignPreview === 'function') {
                                updateElementDesignPreview(formdataId);
                            }
                        }, 300);
                    } else {
                        // If not in tracker, read values from the newly loaded inputs (which have values from database)
                        // and apply them to preview. This handles the case after page reload.
                        setTimeout(function () {
                            var $labelFontSize = $('.element-design-label-font-size[data-formdataid="' + formdataId + '"]');
                            var $borderRadius = $('.element-design-border-radius[data-formdataid="' + formdataId + '"]');

                            if ($labelFontSize.length) {
                                // Read values from the input fields (they already have correct values from database)
                                var labelFontSize = parseInt($('.element-design-label-font-size[data-formdataid="' + formdataId + '"]').val()) || 16;
                                var inputFontSize = parseInt($('.element-design-input-font-size[data-formdataid="' + formdataId + '"]').val()) || 16;
                                var fontWeight = $('.element-design-font-weight[data-formdataid="' + formdataId + '"]').val() || '400';
                                var color = $('.element-design-color-text[data-formdataid="' + formdataId + '"]').val() || '#000000';

                                // Read border radius - check both .val() and the value attribute
                                var borderRadiusVal = $borderRadius.val();
                                // If value is empty or default (4), try reading from the value attribute directly
                                if (!borderRadiusVal || borderRadiusVal === '' || borderRadiusVal === null || borderRadiusVal === '4') {
                                    var attrValue = $borderRadius.attr('value');
                                    if (attrValue && attrValue !== '' && attrValue !== '4') {
                                        borderRadiusVal = attrValue;
                                    }
                                }

                                // If still default value (4), try to load from database via AJAX
                                var borderRadius = (borderRadiusVal !== '' && borderRadiusVal !== null && borderRadiusVal !== undefined && borderRadiusVal !== 'undefined') ? parseInt(borderRadiusVal) : 4;
                                if (isNaN(borderRadius) || borderRadius < 0) {
                                    borderRadius = 4;
                                }

                                // If we got the default value of 4, try loading from database
                                if (borderRadius === 4) {
                                    var formId = $('.formid').val();
                                    if (formId && store) {
                                        $.ajax({
                                            url: "ajax_call.php",
                                            type: "POST",
                                            dataType: "json",
                                            data: {
                                                routine_name: 'get_form_design_settings',
                                                store: store,
                                                form_id: formId
                                            },
                                            success: function (response) {
                                                try {
                                                    if (typeof response === 'string') {
                                                        response = JSON.parse(response);
                                                    }
                                                    if (response.result === 'success' && response.settings) {
                                                        var key = 'element_' + formdataId;
                                                        if (response.settings[key] && response.settings[key].borderRadius) {
                                                            var dbBorderRadius = parseInt(response.settings[key].borderRadius);
                                                            if (!isNaN(dbBorderRadius) && dbBorderRadius >= 0 && dbBorderRadius !== 4) {
                                                                $borderRadius.val(dbBorderRadius);
                                                                borderRadius = dbBorderRadius;

                                                                // Update tracker
                                                                if (!elementChangesTracker.designSettings[formdataId]) {
                                                                    elementChangesTracker.designSettings[formdataId] = {};
                                                                }
                                                                elementChangesTracker.designSettings[formdataId].borderRadius = dbBorderRadius;

                                                                // Apply to preview
                                                                if (typeof updateElementDesignPreview === 'function') {
                                                                    updateElementDesignPreview(formdataId);
                                                                }
                                                            }
                                                        }
                                                    }
                                                } catch (e) {
                                                    // Silent error handling
                                                }
                                            },
                                            error: function (xhr, status, error) {
                                                // Silent error handling
                                            }
                                        });
                                    }
                                }

                                var bgColor = $('.element-design-bg-color-text[data-formdataid="' + formdataId + '"]').val() || '';

                                // Save to tracker for future edits
                                if (!elementChangesTracker.designSettings[formdataId]) {
                                    elementChangesTracker.designSettings[formdataId] = {};
                                }
                                elementChangesTracker.designSettings[formdataId].fontSize = 16;
                                elementChangesTracker.designSettings[formdataId].labelFontSize = labelFontSize;
                                elementChangesTracker.designSettings[formdataId].inputFontSize = inputFontSize;
                                elementChangesTracker.designSettings[formdataId].fontWeight = fontWeight;
                                elementChangesTracker.designSettings[formdataId].color = color;
                                elementChangesTracker.designSettings[formdataId].borderRadius = borderRadius;
                                elementChangesTracker.designSettings[formdataId].borderRadius = borderRadius;
                                elementChangesTracker.designSettings[formdataId].bgColor = bgColor;
                                elementChangesTracker.designSettings[formdataId].optionColor = $('.element-design-option-color-text[data-formdataid="' + formdataId + '"]').val() || '#000000';
                                elementChangesTracker.designSettings[formdataId].checkmarkColor = $('.element-design-checkmark-color-text[data-formdataid="' + formdataId + '"]').val() || '#000000';

                                // Apply to preview
                                if (typeof updateElementDesignPreview === 'function') {
                                    updateElementDesignPreview(formdataId);
                                }
                            }
                        }, 200);

                        // Also update preview after a longer delay to ensure DOM is fully ready
                        setTimeout(function () {
                            // Re-read border radius value in case it was set after initial load
                            var $borderRadius = $('.element-design-border-radius[data-formdataid="' + formdataId + '"]');
                            if ($borderRadius.length) {
                                var borderRadiusVal = $borderRadius.val() || $borderRadius.attr('value');
                                if (borderRadiusVal && borderRadiusVal !== '' && borderRadiusVal !== '4') {
                                    if (typeof updateElementDesignPreview === 'function') {
                                        updateElementDesignPreview(formdataId);
                                    }
                                }
                            }
                        }, 500);
                    }

                    // Add event listeners to save design settings to tracker when they change
                    setTimeout(function () {
                        $('.element-design-label-font-size[data-formdataid="' + formdataId + '"], .element-design-input-font-size[data-formdataid="' + formdataId + '"], .element-design-font-weight[data-formdataid="' + formdataId + '"], .element-design-color-text[data-formdataid="' + formdataId + '"], .element-design-border-radius[data-formdataid="' + formdataId + '"], .element-design-bg-color-text[data-formdataid="' + formdataId + '"], .element-design-option-color-text[data-formdataid="' + formdataId + '"], .element-design-checkmark-color-text[data-formdataid="' + formdataId + '"]').off('change.designTracker').on('change.designTracker', function () {
                            var formdataid = $(this).data('formdataid');
                            if (formdataid) {
                                var borderRadiusVal = $('.element-design-border-radius[data-formdataid="' + formdataid + '"]').val();
                                var borderRadius = (borderRadiusVal !== '' && borderRadiusVal !== null && borderRadiusVal !== undefined) ? parseInt(borderRadiusVal) : 4;
                                if (isNaN(borderRadius) || borderRadius < 0) {
                                    borderRadius = 4;
                                }
                                var designSettings = {
                                    fontSize: 16,
                                    labelFontSize: parseInt($('.element-design-label-font-size[data-formdataid="' + formdataid + '"]').val()) || 16,
                                    inputFontSize: parseInt($('.element-design-input-font-size[data-formdataid="' + formdataid + '"]').val()) || 16,
                                    fontWeight: $('.element-design-font-weight[data-formdataid="' + formdataid + '"]').val() || '400',
                                    color: $('.element-design-color-text[data-formdataid="' + formdataid + '"]').val() || '#000000',
                                    borderRadius: borderRadius,
                                    bgColor: $('.element-design-bg-color-text[data-formdataid="' + formdataid + '"]').val() || '',
                                    optionColor: $('.element-design-option-color-text[data-formdataid="' + formdataid + '"]').val() || '#000000',
                                    checkmarkColor: $('.element-design-checkmark-color-text[data-formdataid="' + formdataid + '"]').val() || '#000000'
                                };
                                elementChangesTracker.designSettings[formdataid] = designSettings;
                            }
                        });
                    }, 200);
                }
            }
        });
    }
});

$(document).on("change", ".switch input[name='checkbox']", function () {
    var formId = $(this).closest(".Polaris-ResourceList__HeaderWrapper").find(".form_id_main").val();
    var ischecked = $(this).is(':checked');
    var ischecked_value = 1;
    if (!ischecked) {
        var ischecked_value = 0;
    }
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'change_form_status', store: store, "formid": formId, "ischecked_value": ischecked_value },
        success: function (comeback) {
            var comeback = JSON.parse(comeback);
        }
    });

});

$(document).on("click", "#checkAll", function () {

    var checked = $(this).is(":checked");
    if (checked) {
        $(".selectedCheck").each(function () {
            $(this).prop("checked", true);
        });
        $(".bultActionss").css("display", "block");
        $(".selectedshow").css("display", "none");
        $(".Polaris-Labelled--hidden").css("display", "none");
        setTimeout(function () {
            $('.Deselectcount').text('Select all form');
        }, 100);
    } else {
        $(".selectedCheck").each(function () {
            $(this).prop("checked", false);
        });
        $(".bultActionss").css("display", "none");
        $(".selectedshow,.sortBy").css("display", "block");
    }
});

$(document).on("click", ".selectedCheck", function () {
    if ($(".selectedCheck").length == $(".selectedCheck:checked").length) {
        $("#checkAll").prop("checked", true);
    } else {
        // $("#checkall").removeAttr("checked");
        $("#checkAll").prop("checked", false);
    }
});

$(document).on("click", ".selectedCheck", function () {
    if ($(this).is(":checked")) {
        $(".bultActionss").css("display", "block");
        $(".selectedshow").css("display", "none");
        $(".Polaris-Labelled--hidden").css("display", "none");
        setTimeout(function () {

            var mychecked = $('.selectedCheck:checked').length;
            $('.Deselectcount').text('Select all form');

        }, 100);
    }
    else {
        $(".bultActionss").css("display", "none");
        $(".selectedshow").css("display", "block");
        $(".Polaris-Labelled--hidden").css("display", "block");
    }

});

$(document).on("click", ".removeElement", function (event) {
    var thisObj = $(this);
    var formid = $(".form_id").val();
    var form_data = $(".add_elementdata")[0];
    var form_data = new FormData(form_data);
    form_data.append('store', store);
    form_data.append('routine_name', 'remove_form_field');
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        contentType: false,
        processData: false,
        data: form_data,
        success: function (response) {
            if (response['code'] != undefined && response['code'] == '403') {
                redirect403();
            } else if (response['result'] === 'success' || response['message']) {
                // Get the formdata_id that was removed from the form
                var $form = $(".add_elementdata");
                var formdata_id = $form.attr('formdataid') || $form.find('input[name="formdata_id"]').val() || '';

                // Navigate back to form builder view
                $(thisObj).closest(".polarisformcontrol").find(".backBtn").trigger("click");

                // Immediately remove from sidebar and preview for instant feedback
                if (formdata_id) {
                    $('.selected_element_set [data-formdataid="' + formdata_id + '"]').remove();
                    // Also remove from preview immediately
                    $('.code-form-app [data-formdataid="' + formdata_id + '"]').remove();
                    $('.contact-form [data-formdataid="' + formdata_id + '"]').remove();
                }

                // Reload page immediately to get fresh data from backend
                window.location.reload();
            }
        }
    });

});

$(document).on("click", ".saveForm", function (event) {
    event.preventDefault();
    event.stopPropagation();

    try {
        // Save current visible form to tracker before saving
        var currentFormdataid = $('form.add_elementdata').attr('formdataid');
        if (currentFormdataid) {
            saveElementFormToTracker(currentFormdataid);
        }

        // Save header and footer to tracker
        var $headerForm = $(".add_headerdata");
        if ($headerForm.length) {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances["contentheader"]) {
                CKEDITOR.instances["contentheader"].updateElement();
            }
            var formData = new FormData($headerForm[0]);
            var headerData = {};
            for (var pair of formData.entries()) {
                headerData[pair[0]] = pair[1];
            }
            elementChangesTracker.header = headerData;
        }

        var $footerForm = $(".add_footerdata");
        if ($footerForm.length) {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances["contentfooter"]) {
                CKEDITOR.instances["contentfooter"].updateElement();
            }
            var formData = new FormData($footerForm[0]);
            var footerData = {};
            for (var pair of formData.entries()) {
                footerData[pair[0]] = pair[1];
            }
            elementChangesTracker.footer = footerData;
        }

        // Now save everything sequentially to prevent race conditions
        saveposition();

        // Chain the saves: Properties -> Design -> Others
        saveAllElementProperties(function () {
            saveAllElementDesignSettings();
            saveheaderform();
            savefooterform();
            savepublishdata();

            // Show success message (final)
            if (typeof flashNotice === 'function') {
                flashNotice('All changes saved successfully!', 'inline-flash--success');
            }
        });
    } catch (error) {
        console.error("Save Error:", error);
    }

    return false;
});

function saveposition() {
    var storeName = $('#store_name').val() || window.store || "";
    var formdataid = $(".selected_element_set").sortable("toArray", { attribute: "data-formdataid" });
    if (formdataid && formdataid.length > 0) {
        $.ajax({
            url: "ajax_call.php",
            type: "POST",
            data: {
                routine_name: 'update_position',
                store: storeName,
                formdataid: formdataid
            },
            success: function (response) {
            },
            error: function (xhr, status, error) {
            }
        });
    } else {
    }
}

function saveAllElementProperties(onComplete) {
    var storeName = $('#store_name').val() || window.store || "";
    var formId = $('#form_id').val() || $('input[name="form_id"]').val() || $('[data-form-id]').first().data('form-id');

    if (!formId || !storeName) {
        console.error("saveAllElementProperties: ABORTING - Could not determine form_id or store. storeName: " + storeName);
        if (typeof onComplete === 'function') onComplete();
        return;
    }

    // Force sync current active form to tracker before processing
    var $activeForm = $('form.add_elementdata');
    if ($activeForm.length) {
        var activeFid = $activeForm.attr('formdataid');
        if (activeFid) {
            saveElementFormToTracker(activeFid);
        }
    }

    // Update CKEditor instances before collecting data
    if (typeof CKEDITOR !== 'undefined') {
        for (var instance in CKEDITOR.instances) {
            if (CKEDITOR.instances[instance]) {
                CKEDITOR.instances[instance].updateElement();
            }
        }
    }

    var allElementsData = [];
    var allFormdataids = Object.keys(elementChangesTracker.elements);

    // Also include elements found in the DOM that might not be in tracker yet (e.g. freshly added)
    $('.selected_element_set > li').each(function () {
        var fid = $(this).attr('data-formdataid');
        if (fid && allFormdataids.indexOf(fid) === -1) {
            allFormdataids.push(fid);
        }
    });

    console.log("saveAllElementProperties: IDs to save: ", allFormdataids);

    allFormdataids.forEach(function (formdataid) {
        var elementData = null;
        var $form = $('form.add_elementdata[formdataid="' + formdataid + '"]');

        if ($form.length) {
            var elementFormData = new FormData($form[0]);
            elementData = {};
            for (var pair of elementFormData.entries()) {
                var key = pair[0];
                var value = pair[1];
                if (key.endsWith('[]')) {
                    key = key.replace('[]', '');
                    if (!elementData[key]) elementData[key] = [];
                    elementData[key].push(value);
                } else {
                    elementData[key] = value;
                }
            }
            var val = $form.closest('.form-control, .header, .Polaris-Card__Section, .Polaris-Card').find('.selectFile').select2('val');
            elementData['allowextention'] = val || [];
        } else if (elementChangesTracker.elements[formdataid]) {
            elementData = elementChangesTracker.elements[formdataid];
        }

        if (elementData && elementData['formdata_id'] && elementData['element_id']) {
            elementData['form_id'] = elementData['form_id'] || formId;
            elementData['store'] = storeName;
            elementData['routine_name'] = 'saveform';
            allElementsData.push(elementData);
        }
    });

    // Also collect from preview elements that might have been edited
    $('.code-form-control[data-id^="element"][data-formdataid]').each(function () {
        var formdataid = $(this).data('formdataid');
        var dataId = $(this).attr('data-id');
        var elementid = dataId ? dataId.replace('element', '') : '';

        if (!formdataid || !elementid) {
            return;
        }

        // Check if we already have this formdataid
        var exists = allElementsData.some(function (item) {
            return item.formdata_id == formdataid;
        });

        if (!exists) {
            // Try to find the form for this element
            var $form = $('form.add_elementdata[formdataid="' + formdataid + '"]');
            if ($form.length) {
                var elementFormData = new FormData($form[0]);
                var elementData = {};

                for (var pair of elementFormData.entries()) {
                    var key = pair[0];
                    var value = pair[1];
                    if (key.endsWith('[]')) {
                        key = key.replace('[]', '');
                        if (!elementData[key]) {
                            elementData[key] = [];
                        }
                        elementData[key].push(value);
                    } else {
                        elementData[key] = value;
                    }
                }

                elementData['formdata_id'] = formdataid;
                elementData['element_id'] = elementid;
                elementData['form_id'] = formId;
                elementData['store'] = storeName;
                elementData['routine_name'] = 'saveform';

                allElementsData.push(elementData);
                processedFormdataids[formdataid] = true;
            }
        }
    });

    console.log("saveAllElementProperties: Final allElementsData to be sent:", allElementsData);

    // Save each element
    if (allElementsData.length > 0) {
        var saveCount = 0;
        var totalCount = allElementsData.length;

        allElementsData.forEach(function (elementData) {
            var formData = new FormData();
            for (var key in elementData) {
                if (Array.isArray(elementData[key])) {
                    // For allowextention, always append even if empty (to clear database)
                    if (key === 'allowextention' && elementData[key].length === 0) {
                        formData.append(key + '[]', '');
                    } else {
                        elementData[key].forEach(function (val) {
                            formData.append(key + '[]', val);
                        });
                    }
                } else {
                    formData.append(key, elementData[key]);
                }
            }

            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    console.log("saveAllElementProperties: Success response for element " + elementData['formdata_id'] + ":", response);
                    saveCount++;
                    if (saveCount === totalCount && typeof onComplete === 'function') {
                        onComplete();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("saveAllElementProperties: Error for element " + elementData['formdata_id'] + ":", error);
                    saveCount++;
                    if (saveCount === totalCount && typeof onComplete === 'function') {
                        onComplete();
                    }
                }
            });
        });
    } else {
        if (typeof onComplete === 'function') {
            onComplete();
        }
    }
}

function saveform() {
    // Keep this for backward compatibility, but saveAllElementProperties will be called instead
    var $content = CKEDITOR.instances["contentparagraph"];
    if ($content != undefined) {
        CKEDITOR.instances["contentparagraph"].updateElement();
    }
    var form_data = $(".add_elementdata")[0];
    if (!form_data) {
        return;
    }
    var val = $(".selectFile").select2("val");
    var form_data = new FormData(form_data);
    // Handle allowextention as array - always append, even if empty (to clear database)
    if (val && Array.isArray(val) && val.length > 0) {
        val.forEach(function (ext) {
            form_data.append("allowextention[]", ext);
        });
    } else {
        // If empty or null, append empty array to clear database
        form_data.append("allowextention[]", "");
    }
    form_data.append('store', store);
    form_data.append('routine_name', 'saveform');
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
            loading_hide('.save_loader_show', 'Save');
        }
    });
}

function saveheaderform() {
    // Save header data to tracker
    var $headerForm = $(".add_headerdata");
    if ($headerForm.length) {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances["contentheader"]) {
            CKEDITOR.instances["contentheader"].updateElement();
        }
        var formData = new FormData($headerForm[0]);
        var headerData = {};
        for (var pair of formData.entries()) {
            headerData[pair[0]] = pair[1];
        }
        elementChangesTracker.header = headerData;
    }

    // Use tracked data or current form
    var form_data = elementChangesTracker.header ? elementChangesTracker.header : {};
    var formDataObj = new FormData();

    for (var key in form_data) {
        formDataObj.append(key, form_data[key]);
    }

    // If no tracked data, use current form
    if (Object.keys(form_data).length === 0 && $headerForm.length) {
        formDataObj = new FormData($headerForm[0]);
    }

    formDataObj.append('store', store);
    formDataObj.append('routine_name', 'saveheaderform');

    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        contentType: false,
        processData: false,
        data: formDataObj,
        success: function (response) {
            var response = JSON.parse(response);
        }
    });
}

function savefooterform() {
    // Save footer data to tracker
    var $footerForm = $(".add_footerdata");
    if ($footerForm.length) {
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances["contentfooter"]) {
            CKEDITOR.instances["contentfooter"].updateElement();
        }
        var formData = new FormData($footerForm[0]);
        var footerData = {};
        for (var pair of formData.entries()) {
            footerData[pair[0]] = pair[1];
        }
        elementChangesTracker.footer = footerData;
    }

    // Use tracked data or current form
    var form_data = elementChangesTracker.footer ? elementChangesTracker.footer : {};
    var formDataObj = new FormData();

    for (var key in form_data) {
        formDataObj.append(key, form_data[key]);
    }

    // If no tracked data, use current form
    if (Object.keys(form_data).length === 0 && $footerForm.length) {
        formDataObj = new FormData($footerForm[0]);
    }

    formDataObj.append('store', store);
    formDataObj.append('routine_name', 'savefooterform');

    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        contentType: false,
        processData: false,
        data: formDataObj,
        success: function (response) {
            var response = JSON.parse(response);
        }
    });
}

function saveAllElementDesignSettings(onComplete) {
    var storeName = $('#store_name').val() || window.store || "";
    var formId = $('#form_id').val() || $('input[name="form_id"]').val() || $('.formid').val();

    if (!formId || !storeName) {
        console.warn("saveAllElementDesignSettings: Skipped due to missing formId or storeName");
        if (typeof onComplete === 'function') onComplete();
        return;
    }

    var allDesignData = [];
    console.log("saveAllElementDesignSettings: Starting save...", elementChangesTracker.designSettings);

    $('.builder-item-wrapper[data-formdataid]').each(function () {
        var $wrapper = $(this);
        var formdataid = $wrapper.attr('data-formdataid');
        var resId = $wrapper.attr('data-id');

        // Debug: log all data-id attributes in this wrapper
        var allDataIds = [];
        $wrapper.find('[data-id]').each(function () {
            allDataIds.push($(this).attr('data-id'));
        });
        console.log("DEBUG: Wrapper " + formdataid + " contains data-ids:", allDataIds);

        // Try multiple ways to get element_id
        var elementid = '';
        if (resId) {
            elementid = resId.replace('element', '');
        }

        // Fallback: look for data-id on the .code-form-control inside
        if (!elementid) {
            var $formControl = $wrapper.find('.code-form-control[data-id]').first();
            if ($formControl.length) {
                var dataId = $formControl.attr('data-id');
                if (dataId) {
                    elementid = dataId.replace('element', '');
                }
            }
        }

        // Another fallback: look for any element with data-id inside
        if (!elementid) {
            var $anyDataId = $wrapper.find('[data-id]').first();
            if ($anyDataId.length) {
                var dataId = $anyDataId.attr('data-id');
                if (dataId && dataId.indexOf('element') === 0) {
                    elementid = dataId.replace('element', '');
                }
            }
        }

        console.log("saveAllElementDesignSettings: Processing " + formdataid + ", wrapper data-id=" + resId + ", extracted element_id=" + elementid);

        if (elementChangesTracker.designSettings[formdataid]) {
            var settings = elementChangesTracker.designSettings[formdataid];
            console.log("saveAllElementDesignSettings: Found settings for " + formdataid, settings);
            allDesignData.push({
                formdata_id: formdataid,
                element_id: elementid,
                settings: settings
            });
        }
    });

    console.log("saveAllElementDesignSettings: Final payload to send:", allDesignData);

    if (allDesignData.length === 0) {
        if (typeof onComplete === 'function') onComplete();
        return;
    }

    $.ajax({
        url: 'ajax_call.php',
        type: 'POST',
        data: {
            routine_name: 'save_all_element_design_settings',
            store: storeName,
            form_id: formId,
            all_settings: JSON.stringify(allDesignData)
        },
        success: function (response) {
            if (typeof onComplete === 'function') onComplete();
        },
        error: function () {
            if (typeof onComplete === 'function') onComplete();
        }
    });
}

function savepublishdata() {
    var storeName = $('#store_name').val() || window.store || "";
    var form_data = $(".add_publishdata")[0];
    var form_data = new FormData(form_data);
    form_data.append('store', storeName);
    form_data.append('routine_name', 'savepublishdata');
    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        contentType: false,
        processData: false,
        data: form_data,
        success: function (response) {
            var response = JSON.parse(response);
        }
    });
}

$(document).on("click", ".submit.action", function (e) {
    e.preventDefault();
    var form_data = $(".get_selected_elements")[0];
    var form_data = new FormData(form_data);
    // form_data.append('images',$("#ImagePreview").attr("src"));
    form_data.append('store', store);
    form_data.append('routine_name', 'addformdata');
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
            loading_hide('save_loader_show', 'Save');
        }
    })
});

// File 
window.onload = (event) => {
    // File upload handlers - use data-formdataid to find elements
    $(document).on('click', '.file_button', function () {
        var formdataid = $(this).closest(".globo-form-input").attr('data-formdataid');
        if (formdataid) {
            $(this).closest(".globo-form-input").find('#fileimage-' + formdataid).click();
        } else {
            // Fallback for old format
            $(this).closest(".globo-form-input").find('input[type="file"]').click();
        }
    });

    $(document).on('click', '.close-button', function (event) {
        event.preventDefault();
        var $formInput = $(this).closest(".globo-form-input");
        var formdataid = $formInput.attr('data-formdataid');
        if ($formInput.find('.img-container').children().length === 0) {
            if (formdataid) {
                $formInput.find('#uploadText-' + formdataid).show();
                $formInput.find('#fileButton-' + formdataid).show();
                $formInput.find('#fileimage-' + formdataid).val('');
            } else {
                // Fallback for old format
                $formInput.find('#uploadText, .upload-p').show();
                $formInput.find('#fileButton, .file_button').show();
                $formInput.find('input[type="file"]').val('');
            }
            $formInput.find('.img-preview-wrapper').remove();
        }
    });

    $(document).on('change', 'input[type="file"][data-type="file"]', function (event) {
        event.preventDefault();
        var $thisObj = $(this);
        var formdataid = $(this).attr('data-formdataid');
        var $formInput = $(this).closest(".globo-form-input");

        // Clear previous previews
        if (formdataid) {
            $formInput.find('#imgContainer-' + formdataid).html('');
        } else {
            $formInput.find('.img-container, #imgContainer').html('');
        }

        const files = this.files;

        Array.from(files).forEach(file => {
            const formId = $('.form_id').val();
            const formDataId = $(this).closest('.globo-form-input').attr("data-formdataid");
            $.ajax({
                url: "ajax_call.php",
                type: "post",
                dataType: "json",
                data: { 'routine_name': 'get_fileallowextention', 'store': store, 'form_id': formId, 'formdata_id': formDataId },
                success: function (comeback) {
                    const data = JSON.parse(comeback);

                    if (data && data.data) {
                        let allowExtentions = data.data[4] || 'png,svg,gif,jpeg,jpg,pdf,webp';

                        const extensionArray = allowExtentions.split(',').map(ext => {
                            switch (ext) {
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
                                if (formdataid) {
                                    $formInput.find('#uploadText-' + formdataid).hide();
                                    $formInput.find('#fileButton-' + formdataid).hide();
                                } else {
                                    // Fallback for old format
                                    $formInput.find('#uploadText, .upload-p').hide();
                                    $formInput.find('#fileButton, .file_button').hide();
                                }
                            } else {
                                if (formdataid) {
                                    $formInput.find('#uploadText-' + formdataid).show();
                                    $formInput.find('#fileButton-' + formdataid).show();
                                } else {
                                    // Fallback for old format
                                    $formInput.find('#uploadText, .upload-p').show();
                                    $formInput.find('#fileButton, .file_button').show();
                                }
                            }

                            const reader = new FileReader();
                            reader.onload = function (event) {
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

                                // Append to correct container
                                if (formdataid) {
                                    $formInput.find('#imgContainer-' + formdataid).append(imgPreviewWrapper);
                                } else {
                                    $formInput.find('.img-container, #imgContainer').append(imgPreviewWrapper);
                                }
                            };

                            reader.readAsDataURL(file);
                        } else {
                        }
                    } else {
                    }
                }
            });
        });
    });
};

// Function to copy Form ID to clipboard (for form list)
window.copyFormId = function (formId, element) {
    // Handle both string and number types (for 6-digit IDs)
    const formIdText = String(formId);

    // Try modern clipboard API first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(formIdText).then(function () {
            showCopySuccessInList(element);
        }).catch(function (err) {
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
        }
    } catch (err) {
    }

    document.body.removeChild(textArea);
}

function showCopySuccessInList(element) {
    if (element && element.parentElement) {
        const successMsg = element.parentElement.querySelector('.copy-success');
        if (successMsg) {
            successMsg.style.display = 'inline';
            setTimeout(function () {
                successMsg.style.display = 'none';
            }, 2000);
        }
    }
}

// File

// Handle frontend form submission
$(document).on('submit', 'form.get_selected_elements', function (e) {
    e.preventDefault();
    var form = $(this);
    var formData = {};
    // Extract data from inputs
    form.find(':input').each(function () {
        var input = $(this);
        // Handle different input types
        var name = input.attr('name');
        if (name) {
            if (input.attr('type') == 'checkbox') {
                if (!formData[name]) {
                    formData[name] = [];
                }
                if (input.is(':checked')) {
                    formData[name].push(input.val());
                }
            } else if (input.attr('type') == 'radio') {
                if (input.is(':checked')) {
                    formData[name] = input.val();
                }
            } else {
                formData[name] = input.val();
            }
        }
    });

    // Clean up checkboxes (join array if needed or keep as array)
    // For now, simple JSON stringify is fine.

    var jsonFormData = JSON.stringify(formData);
    var form_id = form.find('.form_id').val();

    $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: { 'routine_name': 'submitFormFunction', 'store': store, 'form_id': form_id, 'form_data': jsonFormData },
        success: function (response) {
            if (response.result == 'success') {
                form[0].reset();
            } else {
            }
        },
        error: function () {
        }
    });
});

// FIX: Removed duplicate saveForm handler that was causing active conflicts.
// The primary handler is located around line 1538 and correctly handles sequential saving.

// Reset Button Live Preview Logic
$(document).on('change keyup input', '.footer-design-reset-button-text-color, .footer-design-reset-button-text-color-text, .footer-design-reset-button-bg-color, .footer-design-reset-button-bg-color-text, .footer-design-reset-button-hover-bg-color, .footer-design-reset-button-hover-bg-color-text', function () {
    var textColor = $('.footer-design-reset-button-text-color').val();
    var bgColor = $('.footer-design-reset-button-bg-color').val();
    var hoverBgColor = $('.footer-design-reset-button-hover-bg-color').val();

    // Sync Text/color inputs
    if ($(this).hasClass('footer-design-reset-button-text-color')) $('.footer-design-reset-button-text-color-text').val(textColor);
    if ($(this).hasClass('footer-design-reset-button-text-color-text')) {
        $('.footer-design-reset-button-text-color').val($(this).val());
        textColor = $(this).val();
    }

    if ($(this).hasClass('footer-design-reset-button-bg-color')) $('.footer-design-reset-button-bg-color-text').val(bgColor);
    if ($(this).hasClass('footer-design-reset-button-bg-color-text')) {
        $('.footer-design-reset-button-bg-color').val($(this).val());
        bgColor = $(this).val();
    }

    if ($(this).hasClass('footer-design-reset-button-hover-bg-color')) $('.footer-design-reset-button-hover-bg-color-text').val(hoverBgColor);
    if ($(this).hasClass('footer-design-reset-button-hover-bg-color-text')) {
        $('.footer-design-reset-button-hover-bg-color').val($(this).val());
        hoverBgColor = $(this).val();
    }

    // Apply to Preview Element
    var $resetBtn = $('.action.reset');
    if ($resetBtn.length) {
        $resetBtn.css({
            'color': textColor,
            'background-color': bgColor,
            'border-color': bgColor
        });


        // Update both standard data attribute and any other potential hooks
        $resetBtn.attr('data-hover-bg', hoverBgColor);
    }
});

// Submit Button Live Preview Logic (with safeguard for Reset Button)
$(document).on('change keyup input', '.footer-design-button-text-color, .footer-design-button-text-color-text, .footer-design-button-bg-color, .footer-design-button-bg-color-text, .footer-design-button-hover-bg-color, .footer-design-button-hover-bg-color-text', function () {
    var textColor = $('.footer-design-button-text-color').val();
    var bgColor = $('.footer-design-button-bg-color').val();
    var hoverBgColor = $('.footer-design-button-hover-bg-color').val();

    // Sync Text/color inputs
    if ($(this).hasClass('footer-design-button-text-color')) $('.footer-design-button-text-color-text').val(textColor);
    if ($(this).hasClass('footer-design-button-text-color-text')) {
        $('.footer-design-button-text-color').val($(this).val());
        textColor = $(this).val();
    }

    if ($(this).hasClass('footer-design-button-bg-color')) $('.footer-design-button-bg-color-text').val(bgColor);
    if ($(this).hasClass('footer-design-button-bg-color-text')) {
        $('.footer-design-button-bg-color').val($(this).val());
        bgColor = $(this).val();
    }

    if ($(this).hasClass('footer-design-button-hover-bg-color')) $('.footer-design-button-hover-bg-color-text').val(hoverBgColor);
    if ($(this).hasClass('footer-design-button-hover-bg-color-text')) {
        $('.footer-design-button-hover-bg-color').val($(this).val());
        hoverBgColor = $(this).val();
    }

    // Apply to Submit Button ONLY
    var $submitBtn = $('.action.submit');
    if ($submitBtn.length) {
        $submitBtn.css({
            'color': textColor,
            'background-color': bgColor,
            'border-color': bgColor
        });

        $submitBtn.attr('data-hover-bg', hoverBgColor);
    }

    // SAFEGUARD: Re-apply Reset Button styles to ensure they weren't overwritten
    var resetTextColor = $('.footer-design-reset-button-text-color').val();
    var resetBgColor = $('.footer-design-reset-button-bg-color').val();
    var resetHoverBgColor = $('.footer-design-reset-button-hover-bg-color').val();

    var $resetBtn = $('.action.reset');
    if ($resetBtn.length) {
        $resetBtn.css({
            'color': resetTextColor,
            'background-color': resetBgColor,
            'border-color': resetBgColor
        });
        $resetBtn.attr('data-hover-bg', resetHoverBgColor);
    }
});

// Color Picker Synchronization
$(document).on('input change', '.element-design-color, .element-design-option-color, .element-design-checkmark-color, .element-design-bg-color', function () {
    var $this = $(this);
    var formdataid = $this.data('formdataid');
    var val = $this.val();

    // Determine target text input class based on picker class
    var targetClass = '';
    var settingsKey = '';

    if ($this.hasClass('element-design-color')) {
        targetClass = '.element-design-color-text';
        settingsKey = 'color';
    } else if ($this.hasClass('element-design-option-color')) {
        targetClass = '.element-design-option-color-text';
        settingsKey = 'optionColor';
    } else if ($this.hasClass('element-design-checkmark-color')) {
        targetClass = '.element-design-checkmark-color-text';
        settingsKey = 'checkmarkColor';
    } else if ($this.hasClass('element-design-bg-color')) {
        targetClass = '.element-design-bg-color-text';
        settingsKey = 'bgColor';
    }

    if (targetClass) {
        var $target = $(targetClass + '[data-formdataid="' + formdataid + '"]');
        if ($target.length) {
            // Only trigger if value is different to avoid infinite loops
            if ($target.val() !== val) {
                $target.val(val).trigger('change');
            }
        }

        // Direct Tracker Update (Robustness Fix)
        if (formdataid && settingsKey && typeof elementChangesTracker !== 'undefined') {
            if (!elementChangesTracker.designSettings[formdataid]) {
                elementChangesTracker.designSettings[formdataid] = {};
            }
            elementChangesTracker.designSettings[formdataid][settingsKey] = val;
            console.log("Color Sync: Updated tracker for " + formdataid + " " + settingsKey + " = " + val);
        }
    }
});

// Reverse Sync (Text -> Picker)
$(document).on('input change', '.element-design-color-text, .element-design-option-color-text, .element-design-checkmark-color-text, .element-design-bg-color-text', function () {
    var $this = $(this);
    var formdataid = $this.data('formdataid');
    var val = $this.val();

    // Determine target picker class
    var targetClass = '';
    if ($this.hasClass('element-design-color-text')) targetClass = '.element-design-color';
    else if ($this.hasClass('element-design-option-color-text')) targetClass = '.element-design-option-color';
    else if ($this.hasClass('element-design-checkmark-color-text')) targetClass = '.element-design-checkmark-color';
    else if ($this.hasClass('element-design-bg-color-text')) targetClass = '.element-design-bg-color';

    // Only update picker if valid hex color
    if (targetClass && /^#[0-9A-F]{6}$/i.test(val)) {
        var $target = $(targetClass + '[data-formdataid="' + formdataid + '"]');
        if ($target.length) {
            $target.val(val);
        }
    }
});