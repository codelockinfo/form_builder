
console.log("shopify front file loaded");

// Define WOW if it doesn't exist to prevent errors from injected HTML
if (typeof WOW === 'undefined') {
    window.WOW = function () {
        return { init: function () { } };
    };
}

// Define shop/store variable globally to prevent "store is not defined" errors
var shop = '';
var store = ''; // For backward compatibility

// Determine shop domain - try multiple methods
if (typeof Shopify !== 'undefined' && Shopify.shop) {
    shop = Shopify.shop;
} else {
    // Try to get from URL params
    try {
        var params = new URLSearchParams(window.location.search);
        shop = params.get('shop') || '';
    } catch(e) {
        // Fallback for older browsers
        var urlParams = window.location.search.substring(1).split('&');
        for (var i = 0; i < urlParams.length; i++) {
            var param = urlParams[i].split('=');
            if (param[0] === 'shop') {
                shop = decodeURIComponent(param[1] || '');
                break;
            }
        }
    }
}

// Try to get from shop domain in URL
if (!shop) {
    var hostname = window.location.hostname;
    if (hostname.indexOf('.myshopify.com') > -1) {
        shop = hostname;
    }
}

// Set store variable for backward compatibility
store = shop;

// Make shop and store available globally
window.shop = shop;
window.store = store;

$(document).ready(function () {
    // Update global variables if shop was found
    if (shop) {
        window.shop = shop;
        window.store = store;
    } else {
        console.error("Shopify domain not found");
        // Don't return early - still allow form functionality
    }

    // Add CSS if not present
    if ($('link[href*="custom_front.css"]').length === 0) {
        var cssUrl = 'https://codelocksolutions.com/form_builder/assets/css/custom_front.css';
        $('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', cssUrl));
    }

    function loading_show($selector) {
        $($selector).addClass("Button--loading").attr('disabled', 'disabled');
    }

    function loading_hide($selector) {
        $($selector).removeClass("Button--loading").removeAttr('disabled');
    }

    function check_app_status() {
        $.ajax({
            url: "https://codelocksolutions.com/form_builder/user/ajax_call.php",
            type: "POST",
            dataType: 'json',
            data: { 'routine_name': 'check_app_status', 'store': shop },
            success: function (comeback) {
                if (comeback && comeback.outcome == 'true') {
                    console.log("Formbuilder app status: " + (comeback.data == '0' ? 'Enabled' : 'Disabled'));
                }
            }
        });
    }

    check_app_status();

    $(document).on("click", ".submit.action", function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $form = $btn.closest("form");

        if ($form.length === 0) {
            $form = $(".get_selected_elements");
        }

        if ($form.length === 0) {
            console.error("Form not found");
            return;
        }

        var formData = new FormData($form[0]);
        formData.append('store', shop);
        formData.append('routine_name', 'addformdata');

        // Find form ID if not already in form
        if (!formData.has('form_id')) {
            var formId = $form.attr('data-id') || $form.closest('.globo-formbuilder').attr('data-id');
            if (formId) {
                formData.append('form_id', formId);
            }
        }

        $.ajax({
            url: "https://codelocksolutions.com/form_builder/user/ajax_call.php",
            type: "POST",
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                loading_show($btn);
            },
            success: function (comeback) {
                console.log("Submission response:", comeback);
                loading_hide($btn);
                
                // Handle response - check if it's already parsed or needs parsing
                var response = comeback;
                if (typeof comeback === 'string') {
                    try {
                        response = JSON.parse(comeback);
                    } catch(e) {
                        console.error("Error parsing response:", e);
                        response = { result: 'fail', msg: 'Invalid response from server' };
                    }
                }
                
                if (response.result == 'success') {
                    // Show success message
                    var successMsg = response.msg || "Form submitted successfully!";
                    if (typeof flashNotice === 'function') {
                        flashNotice(successMsg);
                    } else {
                        // Try to show a nice notification instead of alert
                        var $notification = $('<div style="position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 15px 20px; border-radius: 4px; z-index: 10000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' + successMsg + '</div>');
                        $('body').append($notification);
                        setTimeout(function() {
                            $notification.fadeOut(300, function() { $(this).remove(); });
                        }, 3000);
                    }
                    
                    // Reset form - clear all input fields
                    try {
                        $form[0].reset();
                        
                        // Also manually clear any remaining values (for better compatibility)
                        $form.find('input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="url"], input[type="date"], input[type="time"]').val('');
                        $form.find('textarea').val('');
                        $form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
                        $form.find('select').prop('selectedIndex', 0);
                        
                        // Remove any validation error classes/styles
                        $form.find('.error, .invalid, .has-error').removeClass('error invalid has-error');
                        $form.find('.error-message').remove();
                        
                        console.log("Form reset successfully");
                    } catch(e) {
                        console.error("Error resetting form:", e);
                        // Fallback: manually clear form
                        $form.find('input, textarea, select').val('');
                        $form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
                    }
                } else {
                    var errorMsg = response.msg || "Something went wrong. Please try again.";
                    alert(errorMsg);
                }
            },
            error: function (xhr, status, error) {
                console.error("Submission error:", error);
                loading_hide($btn);
                alert("An error occurred. Please check the console.");
            }
        });
    });
});
