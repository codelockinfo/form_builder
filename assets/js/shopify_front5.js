// Immediately define store variable to prevent "store is not defined" errors
// This must be at the very top before any other code runs
if (typeof window.store === 'undefined') {
    window.store = '';
}
if (typeof window.shop === 'undefined') {
    window.shop = '';
}

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

// Make shop and store available globally IMMEDIATELY
window.shop = shop;
window.store = store;

// Ensure they're accessible as global variables
// Use window properties directly to avoid scope issues

console.log("=== Form Builder Initialization ===");
console.log("Shop value:", shop);
console.log("Store value:", store);
console.log("Window.shop:", window.shop);
console.log("Window.store:", window.store);
console.log("jQuery available:", typeof jQuery !== 'undefined' ? 'YES' : 'NO');
console.log("$ available:", typeof $ !== 'undefined' ? 'YES' : 'NO');

// Wait for jQuery if not loaded
(function() {
    function initWhenReady() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            console.warn("jQuery not loaded yet, waiting...");
            setTimeout(initWhenReady, 100);
            return;
        }
        
        console.log("jQuery loaded, initializing form handlers...");
        initializeFormHandlers();
    }
    
    function initializeFormHandlers() {
        var $ = jQuery; // Use jQuery explicitly
        
        $(document).ready(function () {
            console.log("=== Document Ready - Setting up form handlers ===");
            
            // Update global variables if shop was found
            if (shop) {
                window.shop = shop;
                window.store = store;
                console.log("Shop/Store variables updated:", shop, store);
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
                var storeValue = shop || store || window.shop || window.store || '';
                if (!storeValue) {
                    console.warn("Store value not available for check_app_status");
                    return;
                }
                $.ajax({
                    url: "https://codelocksolutions.com/form_builder/user/ajax_call.php",
                    type: "POST",
                    dataType: 'json',
                    data: { 'routine_name': 'check_app_status', 'store': storeValue },
                    success: function (comeback) {
                        if (comeback && comeback.outcome == 'true') {
                            console.log("Formbuilder app status: " + (comeback.data == '0' ? 'Enabled' : 'Disabled'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error checking app status:", error);
                    }
                });
            }

            check_app_status();

            // Function to handle form submission
            function handleFormSubmission(e, $btn) {
                console.log("=== handleFormSubmission() CALLED ===");
                console.log("Event:", e);
                console.log("Button jQuery object:", $btn);
                console.log("Button length:", $btn ? $btn.length : 0);
                
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                console.log("=== STEP 1: Finding form ===");
                
                // Find the form
                var $form = null;
                if ($btn && $btn.length > 0) {
                    console.log("Trying to find form using button.closest('form')");
                    $form = $btn.closest("form");
                    console.log("Form found via closest:", $form.length);
                }
                
                if (!$form || $form.length === 0) {
                    console.log("Trying to find form using .get_selected_elements");
                    $form = $(".get_selected_elements");
                    console.log("Form found via selector:", $form.length);
                }
                
                if (!$form || $form.length === 0) {
                    console.log("Trying to find form using button.parents('form')");
                    if ($btn && $btn.length > 0) {
                        $form = $btn.parents("form");
                    }
                    console.log("Form found via parents:", $form ? $form.length : 0);
                }
                
                // Last resort: find any form on the page
                if (!$form || $form.length === 0) {
                    console.log("Trying to find ANY form on page");
                    $form = $("form").first();
                    console.log("First form found:", $form.length);
                }
                
                if (!$form || $form.length === 0) {
                    console.error("=== ERROR: Form not found ===");
                    console.error("Available forms on page:", $("form").length);
                    alert("Form not found. Please refresh the page.");
                    return false;
                }

                console.log("=== STEP 2: Form found successfully ===");
                console.log("Form element:", $form[0]);
                console.log("Form classes:", $form.attr('class'));
                console.log("Form ID:", $form.attr('id'));
                console.log("Form count:", $form.length);

                console.log("=== STEP 3: Getting form ID ===");
                
                // Get form ID
                var formId = $form.find('input[name="form_id"], input.form_id').val();
                console.log("Form ID from input field:", formId);
                
                if (!formId) {
                    console.log("Form ID not in input, checking data attributes");
                    formId = $form.attr('data-id') || $form.closest('.globo-formbuilder').attr('data-id') || $form.closest('.form-builder-container').attr('data-form-id');
                    console.log("Form ID from data attributes:", formId);
                }

                if (!formId) {
                    console.error("=== ERROR: Form ID not found ===");
                    console.error("Form HTML:", $form[0].outerHTML.substring(0, 500));
                    console.error("All inputs in form:", $form.find('input').map(function() { return $(this).attr('name') + '=' + $(this).val(); }).get());
                    alert("Form ID is missing. Please refresh the page.");
                    return false;
                }

                console.log("=== STEP 4: Form ID found ===");
                console.log("Form ID:", formId);

                console.log("=== STEP 5: Validating store value ===");
                
                // Ensure store variable is set
                var storeValue = shop || store || window.shop || window.store || '';
                console.log("Store value check:", {
                    shop: shop,
                    store: store,
                    window_shop: window.shop,
                    window_store: window.store,
                    final_value: storeValue
                });
                
                if (!storeValue) {
                    console.error("=== ERROR: Store value is empty! ===");
                    alert("Store information is missing. Please refresh the page.");
                    return false;
                }

                console.log("=== STEP 6: Creating FormData ===");
                console.log("Submitting form with store:", storeValue, "form_id:", formId);

                // Create FormData
                try {
                    var formData = new FormData($form[0]);
                    console.log("FormData created successfully");
                    
                    formData.append('store', storeValue);
                    formData.append('routine_name', 'addformdata');
                    formData.append('form_id', formId);
                    
                    console.log("=== STEP 7: Form data contents ===");
                    console.log("Form data being submitted:");
                    var formDataArray = [];
                    for (var pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                        formDataArray.push(pair[0] + '=' + pair[1]);
                    }
                    console.log("Total form fields:", formDataArray.length);
                } catch(error) {
                    console.error("=== ERROR creating FormData ===");
                    console.error("Error:", error);
                    alert("Error preparing form data: " + error.message);
                    return false;
                }

        // Show loading state
        if ($btn && $btn.length > 0) {
            $btn.prop('disabled', true);
            $btn.addClass('Button--loading');
            if ($btn.find('.spinner').length > 0) {
                $btn.find('.spinner').show();
            }
        }

                console.log("=== STEP 8: Making AJAX request ===");
                console.log("URL: https://codelocksolutions.com/form_builder/user/ajax_call.php");
                console.log("Method: POST");
                
                $.ajax({
                    url: "https://codelocksolutions.com/form_builder/user/ajax_call.php",
                    type: "POST",
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function(xhr) {
                        console.log("=== AJAX beforeSend ===");
                        console.log("Request headers:", xhr);
                    },
                    success: function (comeback) {
                        console.log("=== STEP 9: AJAX SUCCESS ===");
                        console.log("Submission response:", comeback);
                        console.log("Response type:", typeof comeback);
                        console.log("Response string:", JSON.stringify(comeback));
                        
                        // Hide loading state
                        if ($btn && $btn.length > 0) {
                            $btn.prop('disabled', false);
                            $btn.removeClass('Button--loading');
                            if ($btn.find('.spinner').length > 0) {
                                $btn.find('.spinner').hide();
                            }
                        }
                        
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
                                // Show a nice notification instead of alert
                                var $notification = $('<div style="position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 15px 20px; border-radius: 4px; z-index: 10000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-family: Arial, sans-serif;">' + successMsg + '</div>');
                                $('body').append($notification);
                                setTimeout(function() {
                                    $notification.fadeOut(300, function() { $(this).remove(); });
                                }, 3000);
                            }
                            
                            // Reset form - clear all input fields
                            try {
                                $form[0].reset();
                                
                                // Also manually clear any remaining values (for better compatibility)
                                $form.find('input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="url"], input[type="date"], input[type="time"], input[type="password"]').val('');
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
                        console.error("=== STEP 9: AJAX ERROR ===");
                        console.error("Error:", error);
                        console.error("Status:", status);
                        console.error("XHR:", xhr);
                        console.error("Status code:", xhr.status);
                        console.error("Status text:", xhr.statusText);
                        console.error("Response text:", xhr.responseText);
                        console.error("Ready state:", xhr.readyState);
                        
                        // Hide loading state
                        if ($btn && $btn.length > 0) {
                            $btn.prop('disabled', false);
                            $btn.removeClass('Button--loading');
                            if ($btn.find('.spinner').length > 0) {
                                $btn.find('.spinner').hide();
                            }
                        }
                        
                        var errorMsg = "An error occurred. Please check the console.";
                        if (xhr.responseText) {
                            try {
                                var errorResponse = JSON.parse(xhr.responseText);
                                console.error("Parsed error response:", errorResponse);
                                if (errorResponse.msg) {
                                    errorMsg = errorResponse.msg;
                                }
                            } catch(e) {
                                console.error("Could not parse error response:", e);
                                errorMsg = "Server error: " + xhr.status + " " + xhr.statusText;
                            }
                        }
                        alert(errorMsg);
                    },
                    complete: function(xhr, status) {
                        console.log("=== AJAX COMPLETE ===");
                        console.log("Status:", status);
                        console.log("XHR status:", xhr.status);
                    }
                });
                
                console.log("=== AJAX request sent ===");
                return false;
            }

            // Debug: Check for submit buttons on page
            console.log("=== Checking for submit buttons ===");
            var submitButtons = $(".submit.action, .footer-data__submittext, button.submit, .classic-button.submit, .submit");
            console.log("Found submit buttons:", submitButtons.length);
            submitButtons.each(function(index) {
                console.log("Submit button " + index + ":", $(this).attr('class'), "Type:", this.tagName);
            });
            
            // Debug: Check for forms on page
            console.log("=== Checking for forms ===");
            var forms = $("form.get_selected_elements, form[class*='get_selected_elements'], form");
            console.log("Found forms:", forms.length);
            forms.each(function(index) {
                console.log("Form " + index + ":", $(this).attr('class'), "ID:", $(this).attr('id'));
                var formId = $(this).find('input[name="form_id"], input.form_id').val();
                console.log("  Form ID in form:", formId);
            });

            // Handle button clicks with multiple selectors - use more specific selectors
            console.log("=== Attaching click handlers ===");
            
            // Try multiple selectors to catch all possible submit buttons
            var selectors = [
                ".submit.action",
                ".footer-data__submittext", 
                "button.submit",
                ".classic-button.submit",
                ".submit.classic-button",
                "button[class*='submit']",
                ".action.submit"
            ];
            
            selectors.forEach(function(selector) {
                $(document).on("click", selector, function (e) {
                    console.log("=== SUBMIT BUTTON CLICKED ===");
                    console.log("Selector matched:", selector);
                    console.log("Button element:", this);
                    console.log("Button classes:", $(this).attr('class'));
                    console.log("Button type:", $(this).attr('type'));
                    console.log("Event:", e);
                    handleFormSubmission(e, $(this));
                });
                console.log("Attached handler for selector:", selector);
            });

            // Also handle form submit events as fallback
            $(document).on("submit", "form.get_selected_elements, form[class*='get_selected_elements'], form", function (e) {
                console.log("=== FORM SUBMIT EVENT TRIGGERED ===");
                console.log("Form element:", this);
                console.log("Form classes:", $(this).attr('class'));
                var $form = $(this);
                var $btn = $form.find(".submit.action, .footer-data__submittext, button.submit, .classic-button.submit");
                if ($btn.length === 0) {
                    $btn = $form.find("button[type='submit'], input[type='submit']");
                }
                console.log("Found submit button in form:", $btn.length);
                handleFormSubmission(e, $btn);
            });
            
            console.log("=== Form handlers attached successfully ===");
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWhenReady);
    } else {
        initWhenReady();
    }
})();
