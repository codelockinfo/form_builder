// Immediately define store variable to prevent "store is not defined" errors
// This must be at the very top before any other code runs
if (typeof window.store === 'undefined') {
    window.store = '';
}
if (typeof window.shop === 'undefined') {
    window.shop = '';
}


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


// Wait for jQuery if not loaded
(function() {
    function initWhenReady() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            setTimeout(initWhenReady, 100);
            return;
        }
        
        initializeFormHandlers();
    }
    
    function initializeFormHandlers() {
        var $ = jQuery; // Use jQuery explicitly
        
        $(document).ready(function () {
            
            // Update global variables if shop was found
            if (shop) {
                window.shop = shop;
                window.store = store;
            } else {
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
                    return;
                }
                $.ajax({
                    url: "https://codelocksolutions.com/form_builder/user/ajax_call.php",
                    type: "POST",
                    dataType: 'json',
                    data: { 'routine_name': 'check_app_status', 'store': storeValue },
                    success: function (comeback) {
                        if (comeback && comeback.outcome == 'true') {
                        }
                    },
                    error: function(xhr, status, error) {
                    }
                });
            }

            check_app_status();

            // Function to handle form submission
            function handleFormSubmission(e, $btn) {
                
                if (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                
                // Find the form
                var $form = null;
                if ($btn && $btn.length > 0) {
                    $form = $btn.closest("form");
                }
                
                if (!$form || $form.length === 0) {
                    $form = $(".get_selected_elements");
                }
                
                if (!$form || $form.length === 0) {
                    if ($btn && $btn.length > 0) {
                        $form = $btn.parents("form");
                    }
                }
                
                // Last resort: find any form on the page
                if (!$form || $form.length === 0) {
                    $form = $("form").first();
                }
                
                if (!$form || $form.length === 0) {
                    
                    return false;
                }


                
                // Get form ID
                var formId = $form.find('input[name="form_id"], input.form_id').val();
                
                if (!formId) {
                    formId = $form.attr('data-id') || $form.closest('.globo-formbuilder').attr('data-id') || $form.closest('.form-builder-container').attr('data-form-id');
                }

                if (!formId) {
                    return false;
                }


                
                // Ensure store variable is set
                var storeValue = shop || store || window.shop || window.store || '';
                
                if (!storeValue) {
                    return false;
                }


                // Create FormData
                try {
                    var formData = new FormData($form[0]);
                    
                    formData.append('store', storeValue);
                    formData.append('routine_name', 'addformdata');
                    formData.append('form_id', formId);
                    
                    var formDataArray = [];
                    for (var pair of formData.entries()) {
                        formDataArray.push(pair[0] + '=' + pair[1]);
                    }
                } catch(error) {
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

                
                $.ajax({
                    url: "https://codelocksolutions.com/form_builder/user/ajax_call.php",
                    type: "POST",
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function(xhr) {
                    },
                    success: function (comeback) {
                        
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
                                response = { result: 'fail', msg: 'Invalid response from server' };
                            }
                        }
                        
                        if (response.result == 'success') {
                            // Show success message
                            var successMsg = response.msg || "Form submitted successfully!";
                            if (typeof flashNotice === 'function') {
                                flashNotice(successMsg);
                            } else {
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
                                
                            } catch(e) {
                                // Fallback: manually clear form
                                $form.find('input, textarea, select').val('');
                                $form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
                            }
                        } else {
                            var errorMsg = response.msg || "Something went wrong. Please try again.";
                        }
                    },
                    error: function (xhr, status, error) {
                        
                        // Hide loading state
                        if ($btn && $btn.length > 0) {
                            $btn.prop('disabled', false);
                            $btn.removeClass('Button--loading');
                            if ($btn.find('.spinner').length > 0) {
                                $btn.find('.spinner').hide();
                            }
                        }
                        
                        var errorMsg = "An error occurred. Please try again.";
                        if (xhr.responseText) {
                            try {
                                var errorResponse = JSON.parse(xhr.responseText);
                                if (errorResponse.msg) {
                                    errorMsg = errorResponse.msg;
                                }
                            } catch(e) {
                                errorMsg = "Server error: " + xhr.status + " " + xhr.statusText;
                            }
                        }
                    },
                    complete: function(xhr, status) {
                    }
                });
                
                return false;
            }

            // Debug: Check for submit buttons on page
            var submitButtons = $(".submit.action, .footer-data__submittext, button.submit, .classic-button.submit, .submit");
            submitButtons.each(function(index) {
            });
            
            // Debug: Check for forms on page
            var forms = $("form.get_selected_elements, form[class*='get_selected_elements'], form");
            forms.each(function(index) {
                var formId = $(this).find('input[name="form_id"], input.form_id').val();
            });

            // Handle button clicks with multiple selectors - use more specific selectors
            
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
                    handleFormSubmission(e, $(this));
                });
            });

            // Also handle form submit events as fallback
            $(document).on("submit", "form.get_selected_elements, form[class*='get_selected_elements'], form", function (e) {
                var $form = $(this);
                var $btn = $form.find(".submit.action, .footer-data__submittext, button.submit, .classic-button.submit");
                if ($btn.length === 0) {
                    $btn = $form.find("button[type='submit'], input[type='submit']");
                }
                handleFormSubmission(e, $btn);
            });
            
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWhenReady);
    } else {
        initWhenReady();
    }
})();
