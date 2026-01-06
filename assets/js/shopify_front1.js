
console.log("shopify front file loaded");

// Define WOW if it doesn't exist to prevent errors from injected HTML
if (typeof WOW === 'undefined') {
    window.WOW = function () {
        return { init: function () { } };
    };
}

$(document).ready(function () {
    // Determine shop domain
    var shop = '';
    if (typeof Shopify !== 'undefined' && Shopify.shop) {
        shop = Shopify.shop;
    } else {
        // Try to get from URL params
        var params = new URLSearchParams(window.location.search);
        shop = params.get('shop') || '';
    }

    if (!shop) {
        console.error("Shopify domain not found");
        return;
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
                if (comeback.result == 'success') {
                    if (typeof flashNotice === 'function') {
                        flashNotice(comeback.msg || "Form submitted successfully!");
                    } else {
                        alert(comeback.msg || "Form submitted successfully!");
                    }
                    $form[0].reset();
                } else {
                    alert(comeback.msg || "Something went wrong. Please try again.");
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
