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

                // Clear previous error messages
                $form.find('.messages').each(function() {
                    const $el = $(this);
                    if (!$el.data('orig')) $el.data('orig', $el.text());
                    $el.text($el.data('orig')).removeClass('has-error');
                });

                // Email validation helper
                var emailRegexFront = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

                // Required field validation
                let hasError = false;
                let firstErrorField = null;
                const requiredFields = $form.find('[required]');
                
                requiredFields.each(function() {
                    const $input = $(this);
                    let value = $input.val();
                    
                    // Specialized handling for radio and checkboxes
                    if (($input.attr('type') === 'checkbox' || $input.attr('type') === 'radio') && !$input.is(':checked')) {
                        const name = $input.attr('name');
                        if (name) {
                            const groupCount = $form.find('input[name="' + name + '"]:checked').length;
                            if (groupCount === 0) value = "";
                            else value = "checked";
                        } else {
                            value = "";
                        }
                    }

                    if (!value || (typeof value === 'string' && value.trim() === '')) {
                        hasError = true;
                        let fieldLabel = $input.closest('.code-form-control').find('label').first().text().replace('*', '').trim();
                        if (!fieldLabel) fieldLabel = $input.attr('placeholder') || $input.attr('name') || 'This field';
                        
                        const errorMessage = fieldLabel + ' is required.';
                        
                        // Inline error message via has-error class (red color from CSS)
                        const $msgEl = $input.closest('.code-form-control').find('.messages');
                        if ($msgEl.length) {
                            $msgEl.text(errorMessage).addClass('has-error');
                        }

                        if (!firstErrorField) {
                            firstErrorField = $input;
                            // No toast/popup — sirf inline error
                        }
                    }
                });

                if (hasError) {
                    if (firstErrorField) firstErrorField.focus();
                    return false;
                }

                // Email format validation (after required check)
                var $emailInput = $form.find('input[type="email"]').first();
                if ($emailInput.length && $emailInput.val().trim()) {
                    if (!emailRegexFront.test($emailInput.val().trim())) {
                        var $emailMsg = $emailInput.closest('.code-form-control').find('.messages');
                        if ($emailMsg.length) {
                            $emailMsg.text('Please enter a valid email address (e.g. name@example.com)').addClass('has-error');
                        }
                        $emailInput.focus();
                        return false;
                    }
                }
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
                            var successMsg = response.msg || "Form submitted successfully!";
                            var position = $form.attr('data-success-message-position') || 'popup';
                            
                            if (position === 'popup') {
                                // Show animated right-side toast popup
                                (function showToast(msg, type) {
                                    var isSuccess = type === 'success';
                                    var n = document.createElement('div');
                                    Object.assign(n.style, {
                                        position: 'fixed',
                                        top: '24px',
                                        right: '-400px',
                                        background: isSuccess
                                            ? 'linear-gradient(135deg,#16a34a,#22c55e)'
                                            : 'linear-gradient(135deg,#dc2626,#ef4444)',
                                        color: '#fff',
                                        padding: '14px 20px 14px 16px',
                                        borderRadius: '10px',
                                        boxShadow: '0 8px 24px rgba(0,0,0,0.18)',
                                        zIndex: '999999',
                                        cursor: 'pointer',
                                        fontFamily: 'system-ui,sans-serif',
                                        fontSize: '14px',
                                        fontWeight: '500',
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '10px',
                                        minWidth: '260px',
                                        maxWidth: '340px',
                                        transition: 'right 0.4s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease',
                                        opacity: '0'
                                    });
                                    var icon = isSuccess ? '✓' : '✕';
                                    n.innerHTML = '<span style="font-size:18px;font-weight:700;flex-shrink:0">' + icon + '</span><span>' + msg + '</span>';
                                    document.body.appendChild(n);
                                    requestAnimationFrame(function() {
                                        requestAnimationFrame(function() {
                                            n.style.right = '24px';
                                            n.style.opacity = '1';
                                        });
                                    });
                                    var dismiss = function() {
                                        n.style.right = '-400px';
                                        n.style.opacity = '0';
                                        setTimeout(function() { n.remove(); }, 400);
                                    };
                                    setTimeout(dismiss, 4000);
                                    n.onclick = dismiss;
                                })(successMsg, 'success');
                            } else {
                                // Inline success message
                                // Remove any existing inline messages first
                                $form.find('.form-success-message-inline').remove();
                                
                                var $msgDiv = $('<div class="form-success-message-inline" style="background: #e7f9ed; color: #1e7a3d; padding: 12px 16px; border-radius: 6px; border: 1px solid #c3e6cb; margin: 15px 0; font-weight: 500; display: flex; align-items: center; gap: 10px; width: 100%;">' +
                                                '<span style="font-size: 18px; line-height: 1;">✓</span>' +
                                                '<span style="line-height: 1.4;">' + successMsg + '</span>' +
                                                '</div>');
                                
                                if (position === 'above_submit') {
                                    // Find submit button container or fallback to bottom
                                    var $footer = $form.find(".footer, .action, .submit").first();
                                    if ($footer.length) {
                                        $msgDiv.insertBefore($footer);
                                    } else {
                                        $form.append($msgDiv);
                                    }
                                } else {
                                    // above_form
                                    $form.prepend($msgDiv);
                                }
                                
                                // Scroll to message
                                try {
                                    $msgDiv[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                                } catch(e) {}
                                
                                // Auto-remove after 8 seconds
                                setTimeout(function() { $msgDiv.fadeOut(function() { $(this).remove(); }); }, 8000);
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
            
            // Real-time email validation on blur
            var emailValidRegex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
            $(document).on('blur', 'input[type="email"]', function() {
                var $input = $(this);
                var val = $input.val().trim();
                var $msgEl = $input.closest('.code-form-control').find('.messages');
                if (!$msgEl.length) return;
                if (!$msgEl.data('orig')) $msgEl.data('orig', $msgEl.text());
                if (val && !emailValidRegex.test(val)) {
                    $msgEl.text('Please enter a valid email address (e.g. name@example.com)').addClass('has-error');
                } else {
                    $msgEl.text($msgEl.data('orig')).removeClass('has-error');
                }
            });
            // Clear error as soon as user types valid email
            $(document).on('input', 'input[type="email"]', function() {
                var $input = $(this);
                var val = $input.val().trim();
                var $msgEl = $input.closest('.code-form-control').find('.messages');
                if ($msgEl.length && $msgEl.hasClass('has-error') && emailValidRegex.test(val)) {
                    $msgEl.text($msgEl.data('orig') || '').removeClass('has-error');
                }
            });

            // Real-time error clearing for ALL required fields (text, number, tel, textarea)
            $(document).on('input', 'input[type="text"], input[type="number"], input[type="tel"], input[type="url"], input[type="date"], input[type="time"], input[type="password"], textarea', function() {
                var $input = $(this);
                var $msgEl = $input.closest('.code-form-control').find('.messages');
                if ($msgEl.length && $msgEl.hasClass('has-error') && $input.val().trim() !== '') {
                    if (!$msgEl.data('orig')) $msgEl.data('orig', '');
                    $msgEl.text($msgEl.data('orig')).removeClass('has-error');
                }
            });

            // Real-time error clearing for select dropdowns
            $(document).on('change', 'select', function() {
                var $input = $(this);
                var $msgEl = $input.closest('.code-form-control').find('.messages');
                if ($msgEl.length && $msgEl.hasClass('has-error') && $input.val()) {
                    if (!$msgEl.data('orig')) $msgEl.data('orig', '');
                    $msgEl.text($msgEl.data('orig')).removeClass('has-error');
                }
            });

            // Real-time error clearing for checkboxes and radios
            $(document).on('change', 'input[type="checkbox"], input[type="radio"]', function() {
                var $input = $(this);
                var name = $input.attr('name');
                var $msgEl = $input.closest('.code-form-control').find('.messages');
                if (!$msgEl.length) return;
                var isChecked = name
                    ? $('input[name="' + name + '"]:checked').length > 0
                    : $input.is(':checked');
                if (isChecked && $msgEl.hasClass('has-error')) {
                    if (!$msgEl.data('orig')) $msgEl.data('orig', '');
                    $msgEl.text($msgEl.data('orig')).removeClass('has-error');
                }
            });

            // File upload preview logic with close button
            $(document).on('change', 'input[type="file"]', function(e) {
                var $input = $(this);
                var formdataid = $input.data('formdataid');
                var $container = $('#imgContainer-' + formdataid);
                
                if (!$container.length) {
                    // Try finding by the wrapper if ID doesn't match perfectly
                    $container = $input.closest('.upload-area').find('.img-container');
                }
                
                if (!$container.length) return;
                
                $container.empty();
                
                if (this.files && this.files.length > 0) {
                    // Increase container visibility if it was hidden
                    $container.css('display', 'flex').css('flex-wrap', 'wrap').css('margin-top', '10px');
                    
                    Array.from(this.files).forEach(function(file) {
                        if (file.type.indexOf('image/') === 0) {
                            var reader = new FileReader();
                            reader.onload = function(event) {
                                var $previewWrapper = $('<div class="img-preview-wrapper" style="position: relative; display: inline-block; margin: 10px 10px 5px 0; border: 1px solid #ddd; border-radius: 4px; padding: 4px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"></div>');
                                var $img = $('<img src="' + event.target.result + '" style="width: 80px; height: 80px; object-fit: cover; display: block; border-radius: 2px;">');
                                
                                // Close button at top-right
                                var $closeBtn = $('<div class="img-remove-btn" style="position: absolute; top: -8px; right: -8px; background: #ff4d4f; color: white; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2); z-index: 20; border: 2px solid #fff;">×</div>');
                                
                                $closeBtn.on('click', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    $previewWrapper.remove();
                                    // Reset the file input to allow re-selection of same file if needed
                                    $input.val('');
                                });
                                
                                $previewWrapper.append($img).append($closeBtn);
                                $container.append($previewWrapper);
                            };
                            reader.readAsDataURL(file);
                        } else {
                            // Non-image file support: show file name and icon
                            var $fileWrapper = $('<div class="file-preview-wrapper" style="display: flex; align-items: center; gap: 8px; margin: 5px 5px 5px 0; padding: 6px 10px; background: #f8f9fa; border-radius: 4px; border: 1px solid #e9ecef; font-size: 12px; color: #495057;">' +
                                                '<span style="font-size: 16px;">📄</span>' +
                                                '<span style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' + file.name + '</span>' +
                                                '</div>');
                            $container.append($fileWrapper);
                        }
                    });
                }
            });
            
            // Floating Form Toggle Logic (Global Delegation as backup for innerHTML injection)
            $(document).on("click", ".floating-form-icon", function() {
                var $icon = $(this);
                var idAttr = $icon.attr("id") || "";
                var formId = idAttr.replace("floating-form-icon-", "");
                var $overlay = $("#floating-form-overlay-" + formId);
                if ($overlay.length) {
                    $overlay.css("display", "flex");
                    setTimeout(function() {
                        $overlay.addClass("active");
                    }, 10);
                    $("body").css("overflow", "hidden").addClass("floating-form-active");
                }
            });

            $(document).on("click", ".floating-form-close", function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $overlay = $(this).closest(".floating-form-overlay");
                $overlay.removeClass("active");
                setTimeout(function() {
                    $overlay.css("display", "none");
                }, 300);
                $("body").css("overflow", "").removeClass("floating-form-active");
            });

            $(document).on("click", ".floating-form-overlay", function(e) {
                if (e.target === this) {
                    var $overlay = $(this);
                    $overlay.removeClass("active");
                    setTimeout(function() {
                        $overlay.css("display", "none");
                    }, 300);
                    $("body").css("overflow", "").removeClass("floating-form-active");
                }
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
