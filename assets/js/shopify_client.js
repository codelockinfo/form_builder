"use strict";
var CLS_LOADER = '<svg viewBox="0 0 20 20" class="Polaris-Spinner Polaris-Spinner--colorInkLightest Polaris-Spinner--sizeSmall" aria-label="Loading" role="status"><path d="M7.229 1.173a9.25 9.25 0 1 0 11.655 11.412 1.25 1.25 0 1 0-2.4-.698 6.75 6.75 0 1 1-8.506-8.329 1.25 1.25 0 1 0-.75-2.385z"></path></svg>';
var CLS_DELETE = '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20"><path d="M16 6a1 1 0 1 1 0 2h-1v9a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8H4a1 1 0 1 1 0-2h12zM9 4a1 1 0 1 1 0-2h2a1 1 0 1 1 0 2H9zm2 12h2V8h-2v8zm-4 0h2V8H7v8z" fill="#000" fill-rule="evenodd"></path></svg>';
var CLS_MINUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 80 80" focusable="false" aria-hidden="true"><path d="M39.769,0C17.8,0,0,17.8,0,39.768c0,21.956,17.8,39.768,39.769,39.768   c21.965,0,39.768-17.812,39.768-39.768C79.536,17.8,61.733,0,39.769,0z M13.261,45.07V34.466h53.014V45.07H13.261z" fill-rule="evenodd" fill="#DE3618"></path></svg>';
var CLS_PLUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M17 9h-6V3a1 1 0 1 0-2 0v6H3a1 1 0 1 0 0 2h6v6a1 1 0 1 0 2 0v-6h6a1 1 0 1 0 0-2" fill-rule="evenodd"></path></svg>';
var CLS_CIRCLE_MINUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 80 80" focusable="false" aria-hidden="true"><path d="M39.769,0C17.8,0,0,17.8,0,39.768c0,21.956,17.8,39.768,39.769,39.768   c21.965,0,39.768-17.812,39.768-39.768C79.536,17.8,61.733,0,39.769,0z M13.261,45.07V34.466h53.014V45.07H13.261z" fill-rule="evenodd" fill="#DE3618"></path></svg>';
var CLS_CIRCLE_PLUS = '<svg class="Polaris-Icon__Svg" viewBox="0 0 510 510" focusable="false" aria-hidden="true"><path d="M255,0C114.75,0,0,114.75,0,255s114.75,255,255,255s255-114.75,255-255S395.25,0,255,0z M382.5,280.5h-102v102h-51v-102    h-102v-51h102v-102h51v102h102V280.5z" fill-rule="evenodd" fill="#3f4eae"></path></svg>';
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
var loadShopifyAJAX= null;
var js_loadShopifyDATA = function js_loadShopifyDATA(listingID, pageno) {
    if (loadShopifyAJAX && loadShopifyAJAX.readyState != 4) {
        loadShopifyAJAX.abort();
    }
    var searchKEY = $("#" + listingID + "SearchKeyword").val();
    var searchKEYLEN = (searchKEY != undefined) ? searchKEY.length : 0;
    if (searchKEYLEN == 0 || searchKEYLEN >= 3) {
        var shopifyApi = $('#' + listingID).attr('data-apiName');  
        var limit = $("#" + listingID + "limit").val();
        var from = $('#' + listingID).data('from');
        var routineNAME = 'take_' + from + '_shopify_data';
        var fields = $('#' + listingID).attr('data-fields');
        fields = (fields != undefined) ? fields : '*';
        var searchFields = $('#' + listingID).attr('data-search');
        pageno = (pageno != undefined) ? pageno : 1;
        loadShopifyAJAX = $.ajax({  
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            data: {
                routine_name: routineNAME,
                shopify_api: shopifyApi,
                fields: fields,
                store: store,
                limit: limit,
                pageno: pageno,
                search_key: searchKEY,
                listing_id: listingID,
                search_fields: searchFields,
                pagination_method: js_loadShopifyDATA.name
            }, 
            beforeSend: function () {
                var listingTH = $('#' + listingID + ' thead tr th').length;
                 table_loader("table#" + listingID + " tbody", listingTH);
            },  
            success:function(comeback){
                if (comeback['code'] != undefined && comeback['code'] == '403') {
                    redirect403();
                } else if (comeback['outcome'] == 'true') {
                    $('table#' + listingID + ' tbody').html(comeback['html']);
                    $('#' + listingID + 'Pagination').html(comeback['pagination_html']);
                } else {
                }
            }
        });
    }
}
function OrderDetails(order_id,store){
    var store_id = store_id;
    var order_id = order_id;
    var shopifyApi = "orders";
    var routineNAME = "order_detail";
   $.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: {routine_name: routineNAME,shopify_api :shopifyApi , order_id : order_id , store : store},
        success: function ($return_arary) {
                if ($return_arary['code'] != undefined && $return_arary['code'] == '403') {
                    redirect403();
                } else if ($return_arary['outcome'] == 'true') {
                    $('.block2').append($return_arary["html"]["table"]);
                    $('.block1').append($return_arary["html"]["product"]);
                    $('.block3').append($return_arary["html"]["customer"]);
                } else {
                }
            }
    });
}
function replaceTableStatus(tableName, primaryKeyId, status, thisObj) {
    var class_name = 'listingTableLoader', button_name = '';
    $.ajax({
        url: "responce.php",
        type: "post",
        dataType: "json",
        data: {method_name: "replace_table_status", shop: shop, table_name: tableName, primary_key_id: primaryKeyId, status: status},
        success: function (response) {
            if (response['result'] == 'success') {
                flashNotice(response['message']);
                $(thisObj).attr('onclick', response['onclickfn'])
                        .find('.Polaris-custom-icon.Polaris-Icon.Polaris-Icon--hasBackdrop').html(response['svg_icon'])
                        .children('span').attr('data-hover', response['data_hover']);
            } else {
                flashNotice(response['message']);
            }
        },
        error: function () {
        }
    });
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
    $('#rating').rating({
        min: 0,
        max: 5,
        step: 1,
        size: 'xs',
        showClear: false
    });
    $(".star").hover(function () {
        $(".rate-our-app").show();
        $(".dismiss-button-rating").hide();
    });
    $('table[data-listing="true"]').each(function () {
        var tableId = $(this).attr('id');
        js_loadShopifyDATA(tableId);
    });
    var flashmessage = getCookie("flash_message");
    var flashClass = getCookie("flash_class");
    if (flashmessage != '') {
        flashClass = (flashClass == null || flashClass == undefined) ? '' : flashClass;
        setCookie('flash_message', '', -2);
        flashNotice(flashmessage, flashClass);
    }
    $(".spectrumColor").spectrum({
        showButtons: false
    });
    $(".spectrumColor").on('move.spectrum', function (e, color) {
        var id = $(this).data('id');
        var hexVal = color.toHexString();
        $("[data-id='" + id + "']").val(hexVal);
    });
    $(document).on("submit", "#addClientstore_settingFrm", function (e) {
        e.preventDefault();
        var frmData = $(this).serialize();
        frmData += '&' + $.param({"method_name": 'set_store_settings', 'shop': shop});
        $.ajax({
            url: "responce.php",
            type: "post",
            dataType: "json",
            data: frmData,
            beforeSend: function () {
                loading_show('save_loader_show');
            },
            success: function (response) {
                if (response['code'] != undefined && response['code'] == '403') {
                    redirect403();
                } else if (response['result'] == 'success') {
                    $("#errorsmsgBlock").hide();
                    flashNotice(response['message']);
                } else if (response['msg_contented'] != undefined && response['msg_manage'] != undefined) {
                    $("#errorBlockInfo").html(response['msg_contented']);
                    $("#errorBlockManage").html(response['msg_manage']);
                    $("#errorsmsgBlock").show();
                    $("html, body").animate({scrollTop: 0}, "slow");
                } else {
                    flashNotice(response['message']);
                }
                loading_hide('save_loader_show', 'Save');
            }
        });
    });
    $(document).on("submit", "#imageExmapleFrm", function (e) {
        e.preventDefault();
        var frmData = new FormData($(this)[0]);
        frmData.append('shop', shop);
        frmData.append('method_name', 'image_file_example');
        $.ajax({
            url: "responce.php",
            type: "post",
            dataType: "json",
            contentType: false,
            processData: false,
            data: frmData,
            beforeSend: function () {
                loading_show('save_loader_show');
            },
            success: function (response) {
                if (response['code'] != undefined && response['code'] == '403') {
                    redirect403();
                } else if (response['result'] == 'success') {
                    $("#errorsmsgBlock").hide();
                    flashNotice(response['message']);
                } else if (response['msg_contented'] != undefined && response['msg_manage'] != undefined) {
                    $("#errorBlockInfo").html(response['msg_contented']);
                    $("#errorBlockManage").html(response['msg_manage']);
                    $("#errorsmsgBlock").show();
                    $("html, body").animate({scrollTop: 0}, "slow");
                } else {
                    flashNotice(response['message']);
                }
                loading_hide('save_loader_show', 'Save');
            }
        });
    });
$(document).on("click", ".logout", function(event) {
     event.preventDefault();
        logout();
    });
function logout()
{
	$.ajax({
		url:"ajax_call.php",
		type:'POST',
		data:{
			action:'logout'
		},
		success:function(response)
		{
			if(response.trim() == 'success')
			{
				window.location.href = 'index.php';
			}
		}

	});
}
$(document).on("submit", "#register_frm", function (e) {
        e.preventDefault(); 
        var frmData = new FormData($(this)[0]);
        frmData.append('store',store); 
        frmData.append('routine_name','allbutton_details');  
        $.ajax({
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            contentType: false,
            processData: false,
            data: frmData, 
             beforeSend: function () { 
                loading_show('.save_loader_show');
             
            },
            success: function (response) {
                if (response['code'] != undefined && response['code'] == '403') {
                    redirect403();
                } 
                else{
                    if(response['for_data'] == 'blog'){
                    window.location.href = 'blog_post.php?store='+store;
                    }else if(response['for_data']== 'page'){
                    window.location.href = 'pages.php?store='+store;
                    }else if(response['for_data']== 'collections'){                        
                    window.location.href = 'collection.php?store='+store;
                    }else{
                    window.location.href = 'products.php?store='+store;
                    }
                     loading_hide('.save_loader_show','save');
                }
            }
        });
    });
});
function get_textarea_value(routine_name,store,id,for_data){
$.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: {'routine_name': routine_name , store: store, 'id'  : id,'for_data' : for_data},
        success: function (comeback) {
                if (comeback['code'] != undefined && comeback['code'] == '403') {
                      redirect403();
                }else if (comeback['outcome'] == 'true') {
                        $('#title').val(comeback['data']['title']);
                        $('#ImagePreview').attr("src",comeback['data']['image']);
                        $('.textdetails').val(comeback['data']['description']);
                } else {
                }
                  loading_hide('.save_loader_show','save');
            }
    });
}
function btn_enable_disable(){
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
function seeting_enable_disable(store){
$.ajax({
        url: "ajax_call.php",
        type: "post",
        dataType: "json",
        data: {'routine_name': 'btn_enable_disable' ,'store' : store},
        success: function (comeback) {
//            console.log(comeback['outcome']['data']['status']);
                if (comeback['outcome']['data']['status'] != undefined && comeback['outcome']['data']['status'] == 0) {
                    $(".app-setting-msg").show();
                     $(".enable-btn").html("Enable");
                     $(".enable-btn").val(1);
                      $(".red").html("Disable"); 
                } else {
                     $(".app-setting-msg").hide();
                    $(".enable-btn").html("Disable");
                    $(".enable-btn").val(0);
                     $(".red").html("Enable"); 
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


$(document).on("submit", "#addblog_frm", function (e) {
        e.preventDefault();       
        var form_data = $("#addblog_frm")[0];
        var form_data = new FormData(form_data);
        form_data.append('images',$("#ImagePreview").attr("src"));
        form_data.append('store',store); 
        form_data.append('routine_name','addblog');      
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
                } 
                else if(response['data'] == "fail"){
                    response["msg"]["title"] !== undefined ? $(".title").html (response["msg"]["title"]) : $(".title").html("");
                }else{
                    $(".title").html("");
                   window.location.href = "blog_post.php?store="+ store;
                }
                loading_hide('.save_loader_show','Save');
            }
        });
});
$(document).on("submit", "#addproduct_frm", function (e) {
        e.preventDefault();       
        var form_data = $("#addproduct_frm")[0];
        var form_data = new FormData(form_data);
        
        form_data.append('images',$("#ImagePreview").attr("src"));
        form_data.append('store',store); 
        form_data.append('routine_name','addproduct');      
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
                } 
                else if(response['data'] == "fail"){
                    response["msg"]["title"] !== undefined ? $(".title").html (response["msg"]["title"]) : $(".title").html("");
                }else{
                    $(".title").html("");
                   window.location.href = "products.php?store="+ store;
                }
                loading_hide('.save_loader_show', 'Save');
            }
        });
});
$(document).on("submit", "#addpages_frm", function (e) {
        e.preventDefault();       
        var form_data = $("#addpages_frm")[0];
        var form_data = new FormData(form_data);
        form_data.append('store',store); 
        form_data.append('routine_name','addpages');      
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
                } 
                else if(response['data'] == "fail"){
                    response["msg"]["title"] !== undefined ? $(".title").html (response["msg"]["title"]) : $(".title").html("");
                }else{
                    $(".title").html("");
                   window.location.href = "pages.php?store="+ store;
                }
                loading_hide('.save_loader_show', 'Save');
            }
        });
});
$(document).on("submit", "#addcollection_frm", function (e) {
        e.preventDefault();       
        var form_data = $("#addcollection_frm")[0];
        var form_data = new FormData(form_data);
        form_data.append('images',$("#ImagePreview").attr("src"));
        form_data.append('store',store); 
        form_data.append('routine_name','addcollections');      
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
                } 
                else if(response['data'] == "fail"){
                    response["msg"]["title"] !== undefined ? $(".title").html (response["msg"]["title"]) : $(".title").html("");
                }else{
                    $(".title").html("");
                   window.location.href = "collection.php?store="+ store;
                }
                loading_hide('.save_loader_show', 'Save');
            }
        });
});

     function readURL(input) {
        $(".imagesBlock").css("display","block");
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#ImagePreview').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }


function preview_image(selector,page)
{    $('.imagesBlock').css({display: 'block'});
    $('.imagesBlockEdit').css({display: 'block'});
    var product_id = $('#product_id').val();
    var id = 1, last_id = '', last_cid = '';
    var img_srcs = [];
    var img_titles = [];
                        
    $.each(selector.files, function (vpb_o_, file)
    {
        if (file.name.length > 0)
        {
            if (!file.type.match('image.*')) {
                return true;
            }
            else
            {
                var reader = new FileReader();
                if (page == 'add') {
                    reader.onload = function (e)
                    {
                        $('#multiImagePreview').append(
                                '<div class="column">' +
                                '<div class="image-box">' +
                                '<img class="btn-delete removeAddImage" src="http://cdn1.iconfinder.com/data/icons/diagona/icon/16/101.png">' +
                                '<img class="thumbnail multi-image-preview" src="' + e.target.result + '" \
                               title="' + escape(file.name) + '" /><input type="hidden" name="selected_imges[]" id="jsImg" value="' + file.name + '"></div></div>');
                    }
                } else {
                    reader.onload = function (e)
                    {
                        img_srcs.push(e.target.result);
                        img_titles.push(escape(file.name));
                    }
                }
                reader.readAsDataURL(file);
            }
        }
        else {
            return false;
        }
    });
    
    if(page == 'update'){
        setTimeout(function () {
            var old_images = $('#oldImages').val();
            var frmData = new FormData();
            $.each(img_srcs, function (index, value) {
                frmData.append('images[]', value);
                frmData.append('title[]', img_titles[index]);
            });
            frmData.append('shop', shop);
            frmData.append('product_id', product_id);
            frmData.append('image_ids', old_images);
            frmData.append('method_name', 'upload_product_images');

            if (frmData != '') {
                $.ajax({
                    url: "ajax_responce.php",
                    type: "post",
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    data: frmData,
                    beforeSend: function () {
                        loading_show('.image_loader');
                    },
                    success: function (response) {
                        var old_img_arr = JSON.parse(old_images);
                        if (response['result'] == 'success') {
                            $.each(response['images']['product']['images'], function (index, value) {
                                if ($.inArray(value.id, old_img_arr) == -1) {
                                    old_img_arr.push(value.id);
                                    $('#multiImagePreview').append(
                                            '<div class="column style="display:none;">' +
                                            '<div class="image-box-edit selectMainImage" data-id="' + value.id + '"><i class="btn-select-image"><input type="radio" name="select_main_image" class="select_main_image" value="' + value.id + '" style="display:none;"></i>' +
                                            '<img class="btn-delete-edit removeAddImage" data-id="' + value.id + '" src="http://cdn1.iconfinder.com/data/icons/diagona/icon/16/101.png">' +
                                            '<img class="thumbnail  multi-image-preview addedNewImage" src="' + value.src + '" \
                       title="' + value.alt + '" /><input type="hidden" name="selected_imges[]" id="jsImg" value="' + value.id + '"></div></div>');
                                }
                            });
                            old_img_arr.toString();
                            $('#oldImages').val("[" + old_img_arr + "]");
                            loading_hide('.image_loader', '');
                        } else {
                            flashNotice(response['msg']);
                        }
                        loading_hide('.image_loader', '');
                    }
                });
            }
        }, 1000);
    }


}
$(document).on("click", ".enable-btn", function(event) {
     event.preventDefault();
//       $(".enable-btn").toggle();
       var btnval = $(this).val();
       console.log(btnval);
       if (btnval == 0) {
        $(".app-setting-msg").show();
        $(".red").html("Disable");
        $(".enable-btn").val(1);
        $(".enable-btn").html("Enable");
    } else {
        $(".app-setting-msg").hide();
        $(".red").html("Enable");
        $(".enable-btn").val(0);
        $(".enable-btn").html("Disable");
    }
        $.ajax({
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            data: {'store': store,'routine_name' : 'enable_disable','btnval':btnval}, 
              beforeSend: function () {
                loading_show('.save_loader_show');
            },
            success: function (response) {
                 if (response['code'] != undefined && response['code'] == '403') {
                    redirect403();
                }
                loading_hide('.save_loader_show', 'Save');
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
            var formid=$(".formid").val();
            event.preventDefault();    
            var  get_val=$(this).find(".get_element_hidden").val();
            $.ajax({
                    url: "ajax_call.php",
                    type: "post",
                    dataType: "json",
                    data: {'routine_name': 'set_element' , store: store,'get_element_hidden':get_val,'formid':formid},
                    success: function (comeback) {
                       var comeback = JSON.parse(comeback);
                       if (comeback['code'] != undefined && comeback['code'] == '403') {
                          redirect403();
                      } else{
                        // location.reload(true);
                      }
                    }
                });
            });
            
                function insertDefaultElements(form_id){            
                    $.ajax({
                        url: "ajax_call.php",
                        type: "post",
                        dataType: "json",
                        data: {'routine_name': 'get_three_element_fun' , store: store, "form_id":form_id},
                        success: function (comeback) {
                           var comeback = JSON.parse(comeback);
                           if (comeback['code'] != undefined && comeback['code'] == '403') {
                              redirect403();
                          } else{
                          }
                        }
                    });
                }
                
                function set_all_element_selected(form_id){
                    $.ajax({
                            url: "ajax_call.php",
                            type: "post",
                            dataType: "json",
                            data: {'routine_name': 'set_all_element_selected_fun','form_id': form_id, store: store },
                            success: function (comeback) {
                               var comeback = JSON.parse(comeback);
                               if (comeback['code'] != undefined && comeback['code'] == '403') {
                                  redirect403();
                              } else{
                                console.log(comeback['msg']);
                                $(".selected_element_set").html(comeback['outcome']);

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
                                // $(".set_all_form").html(comeback['outcome'])
                              }
                              loading_hide('.save_loader_show', 'Save');
                           }
                        });
                    });
                    $(document).on("click", ".clsmain_form", function(event) {
                        event.preventDefault();
                        console.log("ssss data 1");
                        var formid=$(this).find(".form_id_main").val();
                        // var form_name=$(".form_name_form_design").val();
                       
                        $.ajax({
                            url: "ajax_call.php",
                            type: "post",
                            dataType: "json",
                            data: {'routine_name': 'mainForm', store: store,'formid':formid},
                            success: function (comeback) {
                               var comeback = JSON.parse(comeback);
                               if (comeback['code'] != undefined && comeback['code'] == '403') {
                                  redirect403();
                              } else{
                                console.log("return data 1");
                                window.location.href = "index.php?form_id="+formid+"&store="+store;
                                // $(".set_all_form").html(comeback['outcome'])
                              }
                            }
                        });
                    });
                    $(document).on("click", ".clsselected_element", function(event) {
                        var formid=$(".formid").val();
                        var element_id=$(this).find(".get_element_hidden").val();
                        var form_data_id=$(this).find(".form_data_id").val();
                       
                        event.preventDefault();  
                        $.ajax({
                            url: "ajax_call.php",
                            type: "post",
                            dataType: "json",
                            data: {'routine_name': 'getElementdetails', store: store,'formid':formid,'element_id':element_id },
                            beforeSend: function () {
                                loading_show('.save_loader_show');
                            },
                            success: function (comeback) {
                               var comeback = JSON.parse(comeback);
                               if (comeback['code'] != undefined && comeback['code'] == '403') {
                                  redirect403();
                              } else{  
                                $(".clsname_of_element").html(comeback['outcome']);
                                $(".clsname_of_element_val").val(comeback['outcome']);
                                $(".element_id_in_form").val(element_id);
                                $(".form_data_id_second").val(form_data_id);
                              }
                              loading_hide('.save_loader_show', 'Save');
                           }
                        });
                    });
                    $(document).on("click", ".remove_this_element", function(event) {
                        var formid=$(".formid").val();
                        var element_id=$(".element_id_in_form").val();
                        var form_data_id=$(".form_data_id_second").val();
                        event.preventDefault();  
                        $.ajax({
                            url: "ajax_call.php",
                            type: "post",
                            dataType: "json",
                            data: {'routine_name': 'deleteElement', store: store,'formid':formid,'element_id':element_id,'form_data_id':form_data_id },
                            beforeSend: function () {
                                loading_show('.save_loader_show');
                            },
                            success: function (comeback) {
                               var comeback = JSON.parse(comeback);
                               if (comeback['code'] != undefined && comeback['code'] == '403') {
                                  redirect403();
                              } else{
                                // location.reload();   
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
                    var form_data = $("#createNewForm")[0];
                    var form_data = new FormData(form_data);
                    form_data.append('selectedTypes',$(".selectedType").attr("data-val"));
                    form_data.append('formnamehiden',$(".formnamehide").attr("data-val"));
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
                                insertDefaultElements(response["data"]);
                                window.location.href = "index.php?form_id="+response["data"]+"&store="+store;
                            }
                            loading_hide('.save_loader_show', 'Create Form');
                        }
                    });
                });