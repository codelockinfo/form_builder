<?php 
include_once('cls_header.php'); 
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : 0;
?>
<body style="padding: 20px;">
    <style>
        .submissions-table-container {
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            max-width: 100%;
            position: relative;
        }
        .submissions-table-container::-webkit-scrollbar {
            height: 12px;
        }
        .submissions-table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }
        .submissions-table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 6px;
        }
        .submissions-table-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .submissions-table-wrapper {
            min-width: 100%;
            display: inline-block;
        }
        .submissions-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        .submissions-table thead th {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
    <div style="max-width: 100%; margin: 0 auto;">
        <div style="margin-bottom: 20px;">
            <a href="dashboard_submissions.php?shop=<?php echo $store; ?>" class="Polaris-Button">Back to Dashboard</a>
            <h2>Form Submissions</h2>
        </div>
        
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section submissions-table-container">
                <div class="submissions-table-wrapper">
                    <table class="Polaris-DataTable__Table submissions-table">
                        <thead id="submissions_header">
                            <!-- Populated by JS -->
                        </thead>
                        <tbody id="submissions_list">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function(){
        var form_id = "<?php echo $form_id; ?>";
        
        $.ajax({
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            data: {'routine_name': 'getFormSubmissions', 'store': store, 'form_id': form_id},
            success: function (response) {
                
                // Handle case where response might be a string (double-encoded JSON)
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch(e) {
                        $('#submissions_list').html('<tr><td colspan="3" style="padding: 10px; text-align: center; color: #d32f2f;">Error: Invalid response from server</td></tr>');
                        return;
                    }
                }
                
                if(response.result == 'success') {
                    // Use display_field_configs from backend (new format)
                    var displayFieldConfigs = response.display_field_configs || [];
                    
                    // If old format (form_fields), convert it
                    if (displayFieldConfigs.length === 0 && response.form_fields) {
                        var formFields = response.form_fields || {};
                        var fieldOrder = Object.keys(formFields);
                        
                        $.each(fieldOrder, function(idx, uniqueKey) {
                            var fieldConfig = formFields[uniqueKey] || {};
                            displayFieldConfigs.push({
                                uniqueKey: uniqueKey,
                                fieldName: fieldConfig.field_name || uniqueKey,
                                fieldNameBase: fieldConfig.field_name_base || fieldConfig.field_name || uniqueKey,
                                config: fieldConfig
                            });
                        });
                    }
                    
                    // If still no field configs, try to extract from first submission's data
                    if (displayFieldConfigs.length === 0 && response.data && response.data.length > 0) {
                        try {
                            var firstSubmission = JSON.parse(response.data[0].submission_data);
                            var allFieldKeys = Object.keys(firstSubmission);
                            
                            // Create field configs from submission data keys
                            $.each(allFieldKeys, function(idx, fieldKey) {
                                // Skip system fields
                                if (fieldKey === 'form_id' || fieldKey === 'id' || fieldKey === 'store') {
                                    return;
                                }
                                
                                displayFieldConfigs.push({
                                    uniqueKey: fieldKey,
                                    fieldName: fieldKey,
                                    fieldNameBase: fieldKey,
                                    config: {
                                        label: fieldKey.replace(/-/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); }),
                                        field_name: fieldKey
                                    }
                                });
                            });
                        } catch(e) {
                            console.error('Error extracting fields from submission data:', e);
                        }
                    }
                    
                    
                    // Build table header with dynamic columns
                    var headerHtml = '<tr>';
                    headerHtml += '<th style="text-align: left; padding: 10px; border-bottom: 1px solid #ccc;">#</th>'; // Custom ID
                    headerHtml += '<th style="text-align: left; padding: 10px; border-bottom: 1px solid #ccc;">Date</th>';
                    
                    // Add column headers for each form field (in order)
                    $.each(displayFieldConfigs, function(idx, fieldInfo) {
                        var fieldLabel = (fieldInfo.config && fieldInfo.config.label) || fieldInfo.uniqueKey || fieldInfo.fieldName || 'Field ' + (idx + 1);
                        headerHtml += '<th style="text-align: left; padding: 10px; border-bottom: 1px solid #ccc;">' + 
                                      $('<div>').text(fieldLabel).html() + '</th>';
                    });
                    
                    headerHtml += '</tr>';
                    $('#submissions_header').html(headerHtml);
                    
                    var html = '';
                    if(response.data && response.data.length > 0) {
                        
                        $.each(response.data, function(i, item){
                            // Custom ID (sequential number starting from 1)
                            var customId = i + 1;
                            
                            html += '<tr style="border-bottom: 1px solid #dfe3e8;">';
                            html += '<td style="padding: 10px; vertical-align: top;">' + customId + '</td>';
                            // Convert date to Indian Standard Time (IST)
                            var dateStr = item.created_at || 'N/A';
                            if (dateStr !== 'N/A' && dateStr) {
                                try {
                                    // Parse MySQL datetime string (YYYY-MM-DD HH:MM:SS)
                                    // Assume it's stored in UTC or server time, convert to IST
                                    var date = new Date(dateStr.replace(' ', 'T') + 'Z'); // Add Z to indicate UTC
                                    
                                    // IST is UTC+5:30
                                    var istOffset = 5.5 * 60 * 60 * 1000; // 5.5 hours in milliseconds
                                    var utcTime = date.getTime();
                                    var istTime = new Date(utcTime + istOffset);
                                    
                                    // Format: YYYY-MM-DD HH:MM:SS IST
                                    var year = istTime.getUTCFullYear();
                                    var month = String(istTime.getUTCMonth() + 1).padStart(2, '0');
                                    var day = String(istTime.getUTCDate()).padStart(2, '0');
                                    var hours = String(istTime.getUTCHours()).padStart(2, '0');
                                    var minutes = String(istTime.getUTCMinutes()).padStart(2, '0');
                                    var seconds = String(istTime.getUTCSeconds()).padStart(2, '0');
                                    
                                    dateStr = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds + ' IST';
                                } catch(e) {
                                    // If conversion fails, show original date
                                }
                            }
                            html += '<td style="padding: 10px; vertical-align: top;">' + dateStr + '</td>';
                            
                            try {
                                var formData = JSON.parse(item.submission_data);
                                
                                // Track which field values we've used for fields that appear multiple times
                                var usedFieldValues = {};
                                
                                // Display each field in its own column (in order)
                                $.each(displayFieldConfigs, function(idx, fieldInfo) {
                                    var fieldValue = '';
                                    // Handle both old format (with fieldName property) and new format (with fieldName in config)
                                    var fieldName = fieldInfo.fieldName || (fieldInfo.config && fieldInfo.config.field_name) || '';
                                    var fieldNameBase = fieldInfo.fieldNameBase || (fieldInfo.config && fieldInfo.config.field_name_base) || '';
                                    var fieldLabel = (fieldInfo.config && fieldInfo.config.label) || '';
                                    
                                    // First, try the exact field name from config (this handles dynamic names like "first-name", "last-name", etc.)
                                    if (fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                        fieldValue = formData[fieldInfo.config.field_name];
                                    }
                                    // Try expected field name (generated from label)
                                    else if (fieldInfo.config && fieldInfo.config.expected_field_name && formData[fieldInfo.config.expected_field_name] !== undefined) {
                                        fieldValue = formData[fieldInfo.config.expected_field_name];
                                    }
                                    // Try fieldName directly
                                    else if (fieldName && formData[fieldName] !== undefined) {
                                        fieldValue = formData[fieldName];
                                       
                                    }
                                    // For text fields, try legacy names
                                    else if (fieldNameBase === 'text') {
                                        // Try "name" first if label contains "name"
                                        if ((fieldLabel.toLowerCase().indexOf('name') !== -1 || (fieldInfo.config && fieldInfo.config.can_use_name)) && formData['name'] !== undefined) {
                                            fieldValue = formData['name'];
                                        } 
                                        // Then try "text" field
                                        else if (formData['text'] !== undefined) {
                                            fieldValue = formData['text'];
                                        }
                                        // Try matching by label slug (for dynamic names like "first-name")
                                        else {
                                            var labelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (labelSlug && formData[labelSlug] !== undefined) {
                                                fieldValue = formData[labelSlug];
                                            }
                                        }
                                    } 
                                    // For email fields, try both "email" and label-based names
                                    else if (fieldNameBase === 'email') {
                                        if (formData['email'] !== undefined) {
                                            fieldValue = formData['email'];
                                        } else {
                                            var emailLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (emailLabelSlug && formData[emailLabelSlug] !== undefined) {
                                                fieldValue = formData[emailLabelSlug];
                                            }
                                        }
                                    }
                                    // For phone fields, try both "phone-1" and label-based names
                                    else if (fieldNameBase === 'phone') {
                                        if (formData['phone-1'] !== undefined) {
                                            fieldValue = formData['phone-1'];
                                        } else if (formData['phone'] !== undefined) {
                                            fieldValue = formData['phone'];
                                        } else {
                                            var phoneLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (phoneLabelSlug && formData[phoneLabelSlug] !== undefined) {
                                                fieldValue = formData[phoneLabelSlug];
                                            }
                                        }
                                    }
                                    // For textarea fields
                                    else if (fieldNameBase === 'textarea') {
                                        if (formData['textarea'] !== undefined) {
                                            fieldValue = formData['textarea'];
                                        } else {
                                            var textareaLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (textareaLabelSlug && formData[textareaLabelSlug] !== undefined) {
                                                fieldValue = formData[textareaLabelSlug];
                                            }
                                        }
                                    }
                                    // For file upload fields
                                    else if (fieldNameBase === 'file' || (fieldInfo.config && fieldInfo.config.element_type && fieldInfo.config.element_type.toString() === '10')) {
                                        // Try various file field name patterns
                                        var fileFieldNames = ['file', 'fileimage', 'file-upload', 'upload'];
                                        var foundFile = false;
                                        for (var f = 0; f < fileFieldNames.length && !foundFile; f++) {
                                            if (formData[fileFieldNames[f]] !== undefined) {
                                                fieldValue = formData[fileFieldNames[f]];
                                                foundFile = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundFile) {
                                            var fileLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (fileLabelSlug && formData[fileLabelSlug] !== undefined) {
                                                fieldValue = formData[fileLabelSlug];
                                                foundFile = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundFile && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundFile = true;
                                        }
                                    }
                                    // For rating star fields
                                    else if (fieldNameBase === 'rating-star' || (fieldInfo.config && fieldInfo.config.element_type && fieldInfo.config.element_type.toString() === '15')) {
                                        // Try rating field names
                                        var ratingFieldNames = ['rating', 'rating-star', 'star-rating', 'rating-1'];
                                        var foundRating = false;
                                        for (var r = 0; r < ratingFieldNames.length && !foundRating; r++) {
                                            if (formData[ratingFieldNames[r]] !== undefined) {
                                                fieldValue = formData[ratingFieldNames[r]];
                                                foundRating = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundRating) {
                                            var ratingLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (ratingLabelSlug && formData[ratingLabelSlug] !== undefined) {
                                                fieldValue = formData[ratingLabelSlug];
                                                foundRating = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundRating && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundRating = true;
                                        }
                                        // Format rating display (e.g., "5 stars" or just "5")
                                        if (fieldValue && !isNaN(fieldValue)) {
                                            fieldValue = fieldValue + ' star' + (parseInt(fieldValue) !== 1 ? 's' : '');
                                        }
                                    }
                                    // For checkbox fields
                                    else if (fieldNameBase === 'checkbox' || (fieldInfo.config && fieldInfo.config.element_type && fieldInfo.config.element_type.toString() === '11')) {
                                        // Try checkbox field names
                                        var checkboxFieldNames = ['checkbox', 'checkbox-option'];
                                        var foundCheckbox = false;
                                        for (var c = 0; c < checkboxFieldNames.length && !foundCheckbox; c++) {
                                            if (formData[checkboxFieldNames[c]] !== undefined) {
                                                fieldValue = formData[checkboxFieldNames[c]];
                                                foundCheckbox = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundCheckbox) {
                                            var checkboxLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (checkboxLabelSlug && formData[checkboxLabelSlug] !== undefined) {
                                                fieldValue = formData[checkboxLabelSlug];
                                                foundCheckbox = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundCheckbox && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundCheckbox = true;
                                        }
                                    }
                                    // For radio fields
                                    else if (fieldNameBase === 'radio' || (fieldInfo.config && fieldInfo.config.element_type && fieldInfo.config.element_type.toString() === '13')) {
                                        // Try radio field names
                                        var radioFieldNames = ['radio', 'radio-option'];
                                        var foundRadio = false;
                                        for (var rd = 0; rd < radioFieldNames.length && !foundRadio; rd++) {
                                            if (formData[radioFieldNames[rd]] !== undefined) {
                                                fieldValue = formData[radioFieldNames[rd]];
                                                foundRadio = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundRadio) {
                                            var radioLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (radioLabelSlug && formData[radioLabelSlug] !== undefined) {
                                                fieldValue = formData[radioLabelSlug];
                                                foundRadio = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundRadio && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundRadio = true;
                                        }
                                    }
                                    // For dropdown/select fields
                                    else if (fieldNameBase === 'dropdown' || fieldNameBase === 'select' || (fieldInfo.config && fieldInfo.config.element_type && (fieldInfo.config.element_type.toString() === '9' || fieldInfo.config.element_type.toString() === '20'))) {
                                        // Try dropdown field names
                                        var dropdownFieldNames = ['dropdown', 'select', 'country'];
                                        var foundDropdown = false;
                                        for (var d = 0; d < dropdownFieldNames.length && !foundDropdown; d++) {
                                            if (formData[dropdownFieldNames[d]] !== undefined) {
                                                fieldValue = formData[dropdownFieldNames[d]];
                                                foundDropdown = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundDropdown) {
                                            var dropdownLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (dropdownLabelSlug && formData[dropdownLabelSlug] !== undefined) {
                                                fieldValue = formData[dropdownLabelSlug];
                                                foundDropdown = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundDropdown && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundDropdown = true;
                                        }
                                    }
                                    // For other fields, try label-based name
                                    else {
                                        var otherLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                        if (otherLabelSlug && formData[otherLabelSlug] !== undefined) {
                                            fieldValue = formData[otherLabelSlug];
                                        } else if (fieldName && formData[fieldName] !== undefined) {
                                            fieldValue = formData[fieldName];
                                        }
                                    }
                                    
                                    // Handle array values (like checkboxes, multiple selects)
                                    if(Array.isArray(fieldValue)) {
                                        fieldValue = fieldValue.join(', ');
                                    }
                                    
                                    // Handle file uploads - display as image or download link
                                    var isFileField = fieldNameBase === 'file' || (fieldInfo.config && fieldInfo.config.element_type && fieldInfo.config.element_type.toString() === '10');
                                    if (isFileField && fieldValue) {
                                        // Check if fieldValue is a URL or file path
                                        var fileDisplay = '';
                                        if (fieldValue.startsWith('http://') || fieldValue.startsWith('https://') || fieldValue.startsWith('/')) {
                                            // It's a URL - check if it's an image
                                            var imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'];
                                            var isImage = false;
                                            for (var ext = 0; ext < imageExtensions.length; ext++) {
                                                if (fieldValue.toLowerCase().indexOf(imageExtensions[ext]) !== -1) {
                                                    isImage = true;
                                                    break;
                                                }
                                            }
                                            if (isImage) {
                                                fileDisplay = '<img src="' + $('<div>').text(fieldValue).html() + '" style="max-width: 150px; max-height: 150px; object-fit: contain; border: 1px solid #ccc; border-radius: 4px;" alt="Uploaded image" />';
                                            } else {
                                                // Show as download link
                                                var fileName = fieldValue.split('/').pop() || 'Download file';
                                                fileDisplay = '<a href="' + $('<div>').text(fieldValue).html() + '" target="_blank" style="color: #0066cc; text-decoration: underline;">' + $('<div>').text(fileName).html() + '</a>';
                                            }
                                        } else if (fieldValue) {
                                            // Just show the filename or value
                                            fileDisplay = $('<div>').text(fieldValue).html();
                                        }
                                        html += '<td style="padding: 10px; vertical-align: top;">' + fileDisplay + '</td>';
                                    } else {
                                        // Escape HTML to prevent XSS (for non-file fields)
                                        var safeFieldValue = $('<div>').text(fieldValue || '').html();
                                        html += '<td style="padding: 10px; vertical-align: top;">' + safeFieldValue + '</td>';
                                    }
                                });
                            } catch(e) {
                                // Fill empty cells for error case
                                $.each(displayFieldConfigs, function(idx, fieldInfo) {
                                    html += '<td style="padding: 10px; vertical-align: top; color: #999;">-</td>';
                                });
                            }
                            
                            html += '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="' + (2 + displayFieldConfigs.length) + '" style="padding: 10px; text-align: center; color: #999;">No submissions found for this form</td></tr>';
                    }
                    $('#submissions_list').html(html);
                } else {
                    $('#submissions_list').html('<tr><td colspan="3" style="padding: 10px; text-align: center; color: #d32f2f;">Error: ' + (response.msg || 'Failed to load submissions') + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                $('#submissions_list').html('<tr><td colspan="3" style="padding: 10px; text-align: center; color: #d32f2f;">Error: Failed to load submissions. Check console for details.</td></tr>');
            }
        });
    });
    </script>
</body>
</html>
