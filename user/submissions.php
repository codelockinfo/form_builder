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
            width: 100%;
            margin: 0 auto;
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
            table-layout: auto;
        }
        /* Calculate minimum width based on number of columns */
        .submissions-table.many-columns {
            min-width: 1200px;
        }
        .submissions-table thead th {
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            padding: 10px;
            text-align: left;
        }
        .submissions-table tbody td {
            white-space: nowrap;
            padding: 10px;
            vertical-align: top;
        }
        /* Ensure table cells don't wrap and maintain minimum width */
        .submissions-table tbody td,
        .submissions-table thead th {
            min-width: 100px;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Allow text wrapping for textarea and long text fields */
        .submissions-table tbody td:has(+ td),
        .submissions-table tbody td {
            white-space: normal;
            word-wrap: break-word;
            word-break: break-word;
        }
        /* Responsive: Show scrollbar hint on smaller screens */
        @media (max-width: 768px) {
            .submissions-table-container {
                border: 2px dashed #ccc;
                border-radius: 4px;
                padding: 10px;
            }
            .submissions-table-container::after {
                content: '← Scroll horizontally to see all columns →';
                display: block;
                text-align: center;
                color: #666;
                font-size: 12px;
                margin-top: 5px;
                font-style: italic;
            }
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
                    
                    // Debug: Log what we received
                    console.log('Submissions - displayFieldConfigs received:', displayFieldConfigs);
                    console.log('Submissions - Total fields:', displayFieldConfigs.length);
                    
                    // Log specific fields we're looking for
                    // Check by fieldNameBase first, then by element_id, and also by label to catch mismatches
                    var ratingFields = displayFieldConfigs.filter(function(f) { 
                        var label = (f.config && f.config.label) ? f.config.label.toLowerCase() : '';
                        return (f.fieldNameBase === 'rating-star' || 
                                (f.config && (f.config.element_id == '18' || f.config.element_id == 18)) ||
                                label.indexOf('rating') !== -1 || label.indexOf('star') !== -1);
                    });
                    var passwordFields = displayFieldConfigs.filter(function(f) { 
                        var label = (f.config && f.config.label) ? f.config.label.toLowerCase() : '';
                        var fieldName = (f.fieldName || '').toLowerCase();
                        return (f.fieldNameBase === 'password' || 
                                (f.config && (f.config.element_id == '8' || f.config.element_id == 8)) ||
                                label === 'password' || label.indexOf('password') !== -1 ||
                                fieldName.indexOf('password') !== -1 || fieldName.indexOf('pwd') !== -1 || fieldName.indexOf('pass') !== -1);
                    });
                    // Log all fields with full details
                    console.log('Submissions - All fields with details:', displayFieldConfigs.map(function(f) {
                        return {
                            fieldName: f.fieldName,
                            fieldNameBase: f.fieldNameBase,
                            element_id: (f.config && f.config.element_id) ? f.config.element_id : 'N/A',
                            label: (f.config && f.config.label) ? f.config.label : 'N/A',
                            fullConfig: f.config
                        };
                    }));
                    
                    // Specifically look for password-related fields
                    var allPasswordCandidates = displayFieldConfigs.filter(function(f) {
                        var label = (f.config && f.config.label) ? f.config.label.toLowerCase() : '';
                        var fieldName = (f.fieldName || '').toLowerCase();
                        var fieldNameBase = (f.fieldNameBase || '').toLowerCase();
                        var elementId = (f.config && f.config.element_id) ? String(f.config.element_id) : '';
                        return label.indexOf('password') !== -1 || 
                               label.indexOf('pwd') !== -1 || 
                               label.indexOf('pass') !== -1 ||
                               fieldName.indexOf('password') !== -1 ||
                               fieldName.indexOf('pwd') !== -1 ||
                               fieldName.indexOf('pass') !== -1 ||
                               fieldNameBase === 'password' ||
                               elementId === '8';
                    });
                    console.log('Submissions - Password candidates found:', allPasswordCandidates);
                    var urlFields = displayFieldConfigs.filter(function(f) { 
                        var label = (f.config && f.config.label) ? f.config.label.toLowerCase() : '';
                        return (f.fieldNameBase === 'url' || 
                                (f.config && (f.config.element_id == '5' || f.config.element_id == 5)) ||
                                label === 'url' || label === 'website' || label.indexOf('url') !== -1 || label.indexOf('website') !== -1 || label.indexOf('link') !== -1);
                    });
                    console.log('Submissions - Rating fields found:', ratingFields);
                    console.log('Submissions - Password fields found:', passwordFields);
                    console.log('Submissions - URL fields found:', urlFields);
                    
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
                    
                    // If still no field configs or missing fields, try to extract from ALL submissions' data
                    // This ensures we get ALL fields, even if backend config is incomplete
                    if (response.data && response.data.length > 0) {
                        try {
                            var allFieldKeysSet = {};
                            
                            // Collect all unique field keys from all submissions
                            $.each(response.data, function(subIdx, submission) {
                                try {
                                    var submissionData = JSON.parse(submission.submission_data);
                                    $.each(Object.keys(submissionData), function(keyIdx, fieldKey) {
                                        // Skip system fields
                                        if (fieldKey !== 'form_id' && fieldKey !== 'id' && fieldKey !== 'store' && fieldKey !== 'routine_name') {
                                            allFieldKeysSet[fieldKey] = true;
                                        }
                                    });
                                } catch(e) {
                                    console.error('Error parsing submission data:', e);
                                }
                            });
                            
                            // Create field configs for any missing fields
                            var existingFieldNames = {};
                            $.each(displayFieldConfigs, function(idx, fieldInfo) {
                                var fn = fieldInfo.fieldName || (fieldInfo.config && fieldInfo.config.field_name) || fieldInfo.uniqueKey;
                                if (fn) existingFieldNames[fn] = true;
                            });
                            
                            // Add missing fields from submission data
                            $.each(Object.keys(allFieldKeysSet), function(idx, fieldKey) {
                                if (!existingFieldNames[fieldKey]) {
                                    // Try to determine field type from field name
                                    var fieldNameBase = fieldKey;
                                    var elementType = null;
                                    
                                    if (fieldKey.toLowerCase().indexOf('rating') !== -1 || fieldKey.toLowerCase().indexOf('star') !== -1) {
                                        fieldNameBase = 'rating-star';
                                        elementType = '18';
                                    } else if (fieldKey.toLowerCase().indexOf('file') !== -1 || fieldKey.toLowerCase().indexOf('upload') !== -1 || fieldKey.toLowerCase().indexOf('image') !== -1) {
                                        fieldNameBase = 'file';
                                        elementType = '10';
                                    } else if (fieldKey.toLowerCase().indexOf('checkbox') !== -1) {
                                        fieldNameBase = 'checkbox';
                                        elementType = '11';
                                    } else if (fieldKey.toLowerCase().indexOf('radio') !== -1) {
                                        fieldNameBase = 'radio';
                                        elementType = '13';
                                    } else if (fieldKey.toLowerCase().indexOf('dropdown') !== -1 || fieldKey.toLowerCase().indexOf('select') !== -1) {
                                        fieldNameBase = 'dropdown';
                                        elementType = '20';
                                    } else if (fieldKey.toLowerCase().indexOf('country') !== -1) {
                                        fieldNameBase = 'country';
                                        elementType = '23';
                                    } else if (fieldKey.toLowerCase().indexOf('url') !== -1 || fieldKey.toLowerCase().indexOf('website') !== -1 || fieldKey.toLowerCase().indexOf('link') !== -1) {
                                        fieldNameBase = 'url';
                                        elementType = '5';
                                    } else if (fieldKey.toLowerCase().indexOf('password') !== -1 || fieldKey.toLowerCase().indexOf('pwd') !== -1 || fieldKey.toLowerCase().indexOf('pass') !== -1) {
                                        fieldNameBase = 'password';
                                        elementType = '8';
                                    } else if (fieldKey.toLowerCase().indexOf('textarea') !== -1) {
                                        fieldNameBase = 'textarea';
                                    } else if (fieldKey.toLowerCase().indexOf('email') !== -1) {
                                        fieldNameBase = 'email';
                                    } else if (fieldKey.toLowerCase().indexOf('phone') !== -1) {
                                        fieldNameBase = 'phone';
                                    }
                                    
                                    displayFieldConfigs.push({
                                        uniqueKey: fieldKey,
                                        fieldName: fieldKey,
                                        fieldNameBase: fieldNameBase,
                                        config: {
                                            label: fieldKey.replace(/-/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); }),
                                            field_name: fieldKey,
                                            element_type: elementType
                                        }
                                    });
                                }
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
                    
                    // Add class to table if there are many columns for better scrolling
                    var columnCount = displayFieldConfigs.length + 2; // +2 for # and Date columns
                    if (columnCount > 6) {
                        $('.submissions-table').addClass('many-columns');
                        // Adjust min-width based on column count (150px per column minimum)
                        var calculatedMinWidth = Math.max(800, columnCount * 150);
                        $('.submissions-table').css('min-width', calculatedMinWidth + 'px');
                    }
                    
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
                                
                                // Ensure we have all fields from actual submission data
                                // Add any missing fields from formData that aren't in displayFieldConfigs
                                var formDataKeys = Object.keys(formData);
                                var existingFieldNames = {};
                                $.each(displayFieldConfigs, function(idx, fieldInfo) {
                                    var fn = fieldInfo.fieldName || (fieldInfo.config && fieldInfo.config.field_name) || fieldInfo.uniqueKey;
                                    if (fn) existingFieldNames[fn] = true;
                                });
                                
                                // Add missing fields from this submission
                                $.each(formDataKeys, function(keyIdx, dataKey) {
                                    // Skip system fields
                                    if (dataKey === 'form_id' || dataKey === 'id' || dataKey === 'store' || dataKey === 'routine_name') {
                                        return;
                                    }
                                    
                                    // If this field is not in displayFieldConfigs, add it
                                    if (!existingFieldNames[dataKey]) {
                                        // Try to determine field type
                                        var fieldNameBase = dataKey;
                                        var elementType = null;
                                        
                                        if (dataKey.toLowerCase().indexOf('rating') !== -1 || dataKey.toLowerCase().indexOf('star') !== -1) {
                                            fieldNameBase = 'rating-star';
                                            elementType = '18';
                                        } else if (dataKey.toLowerCase().indexOf('file') !== -1 || dataKey.toLowerCase().indexOf('upload') !== -1 || dataKey.toLowerCase().indexOf('image') !== -1) {
                                            fieldNameBase = 'file';
                                            elementType = '10';
                                        } else if (dataKey.toLowerCase().indexOf('checkbox') !== -1) {
                                            fieldNameBase = 'checkbox';
                                            elementType = '11';
                                        } else if (dataKey.toLowerCase().indexOf('radio') !== -1) {
                                            fieldNameBase = 'radio';
                                            elementType = '13';
                                        } else if (dataKey.toLowerCase().indexOf('dropdown') !== -1 || dataKey.toLowerCase().indexOf('select') !== -1) {
                                            fieldNameBase = 'dropdown';
                                            elementType = '20';
                                        } else if (dataKey.toLowerCase().indexOf('country') !== -1) {
                                            fieldNameBase = 'country';
                                            elementType = '23';
                                        } else if (dataKey.toLowerCase().indexOf('address') !== -1 || dataKey.toLowerCase().indexOf('street') !== -1 || dataKey.toLowerCase().indexOf('city') !== -1 || dataKey.toLowerCase().indexOf('state') !== -1 || dataKey.toLowerCase().indexOf('zip') !== -1 || dataKey.toLowerCase().indexOf('postal') !== -1) {
                                            fieldNameBase = 'address';
                                            elementType = '21';
                                        } else if (dataKey.toLowerCase().indexOf('url') !== -1 || dataKey.toLowerCase().indexOf('website') !== -1 || dataKey.toLowerCase().indexOf('link') !== -1) {
                                            fieldNameBase = 'url';
                                            elementType = '5';
                                        } else if (dataKey.toLowerCase().indexOf('password') !== -1 || dataKey.toLowerCase().indexOf('pwd') !== -1 || dataKey.toLowerCase().indexOf('pass') !== -1) {
                                            fieldNameBase = 'password';
                                            elementType = '8';
                                        } else if (dataKey.toLowerCase().indexOf('number') !== -1 || dataKey.toLowerCase().indexOf('numeric') !== -1 || dataKey.toLowerCase().indexOf('num') !== -1) {
                                            fieldNameBase = 'number';
                                            elementType = '14';
                                        } else if (dataKey.toLowerCase().indexOf('accept') !== -1 || dataKey.toLowerCase().indexOf('terms') !== -1 || dataKey.toLowerCase().indexOf('agree') !== -1) {
                                            fieldNameBase = 'accept-terms';
                                            elementType = '16';
                                        } else if (dataKey.toLowerCase().indexOf('textarea') !== -1) {
                                            fieldNameBase = 'textarea';
                                        } else if (dataKey.toLowerCase().indexOf('email') !== -1) {
                                            fieldNameBase = 'email';
                                        } else if (dataKey.toLowerCase().indexOf('phone') !== -1) {
                                            fieldNameBase = 'phone';
                                        } else if (dataKey.toLowerCase().indexOf('date') !== -1 || dataKey.toLowerCase().indexOf('time') !== -1 || dataKey.toLowerCase().indexOf('datetime') !== -1) {
                                            fieldNameBase = 'date';
                                            elementType = '9';
                                        }
                                        
                                        displayFieldConfigs.push({
                                            uniqueKey: dataKey,
                                            fieldName: dataKey,
                                            fieldNameBase: fieldNameBase,
                                            config: {
                                                label: dataKey.replace(/-/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); }),
                                                field_name: dataKey,
                                                element_type: elementType
                                            }
                                        });
                                        existingFieldNames[dataKey] = true;
                                    }
                                });
                                
                                // Rebuild header if new fields were added (only for first row)
                                if (i === 0 && displayFieldConfigs.length > (response.display_field_configs ? response.display_field_configs.length : 0)) {
                                    var headerHtml = '<tr>';
                                    headerHtml += '<th style="text-align: left; padding: 10px; border-bottom: 1px solid #ccc;">#</th>';
                                    headerHtml += '<th style="text-align: left; padding: 10px; border-bottom: 1px solid #ccc;">Date</th>';
                                    
                                    $.each(displayFieldConfigs, function(idx, fieldInfo) {
                                        var fieldLabel = (fieldInfo.config && fieldInfo.config.label) || fieldInfo.uniqueKey || fieldInfo.fieldName || 'Field ' + (idx + 1);
                                        headerHtml += '<th style="text-align: left; padding: 10px; border-bottom: 1px solid #ccc;">' + 
                                                      $('<div>').text(fieldLabel).html() + '</th>';
                                    });
                                    
                                    headerHtml += '</tr>';
                                    $('#submissions_header').html(headerHtml);
                                    
                                    // Update table width if new columns were added
                                    var updatedColumnCount = displayFieldConfigs.length + 2;
                                    if (updatedColumnCount > 6) {
                                        $('.submissions-table').addClass('many-columns');
                                        var calculatedMinWidth = Math.max(800, updatedColumnCount * 150);
                                        $('.submissions-table').css('min-width', calculatedMinWidth + 'px');
                                    }
                                }
                                
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
                                    else if (fieldNameBase === 'file' || (fieldInfo.config && fieldInfo.config.element_id && fieldInfo.config.element_id.toString() === '10')) {
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
                                    else if (fieldNameBase === 'rating-star' || (fieldInfo.config && fieldInfo.config.element_id && (fieldInfo.config.element_id.toString() === '18' || fieldInfo.config.element_id == 18))) {
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
                                    else if (fieldNameBase === 'checkbox' || (fieldInfo.config && fieldInfo.config.element_id && (fieldInfo.config.element_id.toString() === '6' || fieldInfo.config.element_id.toString() === '13'))) {
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
                                    else if (fieldNameBase === 'radio' || (fieldInfo.config && fieldInfo.config.element_id && fieldInfo.config.element_id.toString() === '11')) {
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
                                    else if (fieldNameBase === 'dropdown' || fieldNameBase === 'select' || (fieldInfo.config && fieldInfo.config.element_id && fieldInfo.config.element_id.toString() === '20')) {
                                        // Try dropdown field names
                                        var dropdownFieldNames = ['dropdown', 'select', 'select-option'];
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
                                    // For country fields
                                    else if (fieldNameBase === 'country' || (fieldInfo.config && fieldInfo.config.element_id && fieldInfo.config.element_id.toString() === '23')) {
                                        // Try country field names
                                        var countryFieldNames = ['country', 'country-select', 'country-dropdown'];
                                        var foundCountry = false;
                                        for (var c = 0; c < countryFieldNames.length && !foundCountry; c++) {
                                            if (formData[countryFieldNames[c]] !== undefined) {
                                                fieldValue = formData[countryFieldNames[c]];
                                                foundCountry = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundCountry) {
                                            var countryLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (countryLabelSlug && formData[countryLabelSlug] !== undefined) {
                                                fieldValue = formData[countryLabelSlug];
                                                foundCountry = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundCountry && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundCountry = true;
                                        }
                                    }
                                    // For address fields (element_id 12, 21, 22)
                                    else if (fieldNameBase === 'address' || (fieldInfo.config && fieldInfo.config.element_id && (fieldInfo.config.element_id.toString() === '12' || fieldInfo.config.element_id.toString() === '21' || fieldInfo.config.element_id.toString() === '22'))) {
                                        // Try address field names
                                        var addressFieldNames = ['address', 'address-1', 'address-2', 'street', 'street-address', 'city', 'state', 'zip', 'postal-code', 'country'];
                                        var foundAddress = false;
                                        for (var a = 0; a < addressFieldNames.length && !foundAddress; a++) {
                                            if (formData[addressFieldNames[a]] !== undefined) {
                                                fieldValue = formData[addressFieldNames[a]];
                                                foundAddress = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundAddress) {
                                            var addressLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (addressLabelSlug && formData[addressLabelSlug] !== undefined) {
                                                fieldValue = formData[addressLabelSlug];
                                                foundAddress = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundAddress && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundAddress = true;
                                        }
                                    }
                                    // For URL fields
                                    else if (fieldNameBase === 'url' || fieldNameBase === 'website' || (fieldInfo.config && fieldInfo.config.element_id && (fieldInfo.config.element_id.toString() === '5' || fieldInfo.config.element_id == 5))) {
                                        // Try URL field names
                                        var urlFieldNames = ['url', 'website', 'website-url', 'link', 'web-url'];
                                        var foundUrl = false;
                                        for (var u = 0; u < urlFieldNames.length && !foundUrl; u++) {
                                            if (formData[urlFieldNames[u]] !== undefined) {
                                                fieldValue = formData[urlFieldNames[u]];
                                                foundUrl = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundUrl) {
                                            var urlLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (urlLabelSlug && formData[urlLabelSlug] !== undefined) {
                                                fieldValue = formData[urlLabelSlug];
                                                foundUrl = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundUrl && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundUrl = true;
                                        }
                                        // Format as clickable link if it's a valid URL
                                        if (fieldValue && (fieldValue.startsWith('http://') || fieldValue.startsWith('https://') || fieldValue.startsWith('www.'))) {
                                            var urlToDisplay = fieldValue;
                                            if (fieldValue.startsWith('www.')) {
                                                urlToDisplay = 'http://' + fieldValue;
                                            }
                                            fieldValue = '<a href="' + $('<div>').text(urlToDisplay).html() + '" target="_blank" style="color: #0066cc; text-decoration: underline;">' + $('<div>').text(fieldValue).html() + '</a>';
                                        }
                                    }
                                    // For number fields
                                    else if (fieldNameBase === 'number' || (fieldInfo.config && fieldInfo.config.element_id && fieldInfo.config.element_id.toString() === '14')) {
                                        // Try number field names
                                        var numberFieldNames = ['number', 'num', 'numeric'];
                                        var foundNumber = false;
                                        for (var n = 0; n < numberFieldNames.length && !foundNumber; n++) {
                                            if (formData[numberFieldNames[n]] !== undefined) {
                                                fieldValue = formData[numberFieldNames[n]];
                                                foundNumber = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundNumber) {
                                            var numberLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (numberLabelSlug && formData[numberLabelSlug] !== undefined) {
                                                fieldValue = formData[numberLabelSlug];
                                                foundNumber = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundNumber && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundNumber = true;
                                        }
                                    }
                                    // For accept terms fields
                                    else if (fieldNameBase === 'accept-terms' || (fieldInfo.config && fieldInfo.config.element_id && (fieldInfo.config.element_id.toString() === '16' || fieldInfo.config.element_id.toString() === '17' || fieldInfo.config.element_id.toString() === '19'))) {
                                        // Try accept terms field names
                                        var acceptTermsFieldNames = ['accept-terms', 'accept-terms-conditions', 'terms', 'terms-conditions', 'agree-terms', 'i-agree-terms-and-conditions'];
                                        var foundAcceptTerms = false;
                                        for (var at = 0; at < acceptTermsFieldNames.length && !foundAcceptTerms; at++) {
                                            if (formData[acceptTermsFieldNames[at]] !== undefined) {
                                                fieldValue = formData[acceptTermsFieldNames[at]];
                                                foundAcceptTerms = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundAcceptTerms) {
                                            var acceptTermsLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (acceptTermsLabelSlug && formData[acceptTermsLabelSlug] !== undefined) {
                                                fieldValue = formData[acceptTermsLabelSlug];
                                                foundAcceptTerms = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundAcceptTerms && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundAcceptTerms = true;
                                        }
                                        // Format accept terms display (usually "Yes" or "1" if checked)
                                        if (fieldValue === '1' || fieldValue === 1 || fieldValue === true || fieldValue === 'true' || fieldValue === 'yes' || fieldValue === 'Yes') {
                                            fieldValue = 'Yes';
                                        } else if (fieldValue === '0' || fieldValue === 0 || fieldValue === false || fieldValue === 'false' || fieldValue === 'no' || fieldValue === 'No' || !fieldValue) {
                                            fieldValue = 'No';
                                        }
                                    }
                                    // For password fields
                                    else if (fieldNameBase === 'password' || (fieldInfo.config && fieldInfo.config.element_id && fieldInfo.config.element_id.toString() === '8')) {
                                        // Try password field names
                                        var passwordFieldNames = ['password', 'pwd', 'pass'];
                                        var foundPassword = false;
                                        for (var p = 0; p < passwordFieldNames.length && !foundPassword; p++) {
                                            if (formData[passwordFieldNames[p]] !== undefined) {
                                                fieldValue = formData[passwordFieldNames[p]];
                                                foundPassword = true;
                                            }
                                        }
                                        // Try label-based name
                                        if (!foundPassword) {
                                            var passwordLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                            if (passwordLabelSlug && formData[passwordLabelSlug] !== undefined) {
                                                fieldValue = formData[passwordLabelSlug];
                                                foundPassword = true;
                                            }
                                        }
                                        // Try exact field name from config
                                        if (!foundPassword && fieldInfo.config && fieldInfo.config.field_name && formData[fieldInfo.config.field_name] !== undefined) {
                                            fieldValue = formData[fieldInfo.config.field_name];
                                            foundPassword = true;
                                        }
                                        // Mask password for security (show as asterisks)
                                        if (fieldValue) {
                                            fieldValue = '••••••••';
                                        }
                                    }
                                    // For other fields, try label-based name
                                    else {
                                        var otherLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                        if (otherLabelSlug && formData[otherLabelSlug] !== undefined) {
                                            fieldValue = formData[otherLabelSlug];
                                        } else if (fieldName && formData[fieldName] !== undefined) {
                                            fieldValue = formData[fieldName];
                                        } else {
                                            // Last resort: try to find by iterating through all formData keys
                                            // and matching by similarity to fieldName or label
                                            var bestMatch = null;
                                            var bestMatchScore = 0;
                                            
                                            $.each(Object.keys(formData), function(keyIdx, dataKey) {
                                                // Skip system fields
                                                if (dataKey === 'form_id' || dataKey === 'id' || dataKey === 'store' || dataKey === 'routine_name') {
                                                    return;
                                                }
                                                
                                                // Calculate similarity score
                                                var score = 0;
                                                var dataKeyLower = dataKey.toLowerCase();
                                                var fieldNameLower = (fieldName || '').toLowerCase();
                                                var labelLower = fieldLabel.toLowerCase();
                                                
                                                // Exact match
                                                if (dataKey === fieldName || dataKey === fieldInfo.uniqueKey) {
                                                    score = 100;
                                                }
                                                // Contains field name
                                                else if (fieldNameLower && dataKeyLower.indexOf(fieldNameLower) !== -1) {
                                                    score = 80;
                                                }
                                                // Contains label words
                                                else if (labelLower) {
                                                    var labelWords = labelLower.split(/[\s-]+/);
                                                    var matchCount = 0;
                                                    $.each(labelWords, function(wIdx, word) {
                                                        if (word.length > 2 && dataKeyLower.indexOf(word) !== -1) {
                                                            matchCount++;
                                                        }
                                                    });
                                                    if (matchCount > 0) {
                                                        score = 50 + (matchCount * 10);
                                                    }
                                                }
                                                
                                                if (score > bestMatchScore) {
                                                    bestMatchScore = score;
                                                    bestMatch = dataKey;
                                                }
                                            });
                                            
                                            if (bestMatch && bestMatchScore >= 50) {
                                                fieldValue = formData[bestMatch];
                                            }
                                        }
                                    }
                                    
                                    // Handle array values (like checkboxes, multiple selects)
                                    if(Array.isArray(fieldValue)) {
                                        fieldValue = fieldValue.join(', ');
                                    }
                                    
                                    // Handle file uploads - display as image or download link
                                    var isFileField = fieldNameBase === 'file' || (fieldInfo.config && fieldInfo.config.element_id && fieldInfo.config.element_id.toString() === '10');
                                    // Handle URL fields - already formatted as link above
                                    var isUrlField = fieldNameBase === 'url' || (fieldInfo.config && fieldInfo.config.element_id && (fieldInfo.config.element_id.toString() === '5' || fieldInfo.config.element_id == 5));
                                    // Handle password fields - already masked above
                                    var isPasswordField = fieldNameBase === 'password' || (fieldInfo.config && fieldInfo.config.element_id && fieldInfo.config.element_id.toString() === '8');
                                    
                                    if (isFileField && fieldValue) {
                                        // Check if fieldValue is a URL or file path
                                        var fileDisplay = '';
                                        
                                        // Handle array of files (multiple uploads)
                                        if (Array.isArray(fieldValue)) {
                                            var fileList = [];
                                            fieldValue.forEach(function(fileUrl) {
                                                if (fileUrl && (fileUrl.startsWith('http://') || fileUrl.startsWith('https://') || fileUrl.startsWith('/'))) {
                                                    var imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg', '.bmp'];
                                                    var isImage = false;
                                                    for (var ext = 0; ext < imageExtensions.length; ext++) {
                                                        if (fileUrl.toLowerCase().indexOf(imageExtensions[ext]) !== -1) {
                                                            isImage = true;
                                                            break;
                                                        }
                                                    }
                                                    if (isImage) {
                                                        fileList.push('<img src="' + $('<div>').text(fileUrl).html() + '" style="max-width: 100px; max-height: 100px; object-fit: contain; border: 1px solid #ccc; border-radius: 4px; margin: 5px; display: block;" alt="Uploaded image" />');
                                                    } else {
                                                        var fileName = fileUrl.split('/').pop() || 'Download file';
                                                        fileList.push('<a href="' + $('<div>').text(fileUrl).html() + '" target="_blank" style="color: #0066cc; text-decoration: underline; display: block; margin: 5px;">' + $('<div>').text(fileName).html() + '</a>');
                                                    }
                                                }
                                            });
                                            fileDisplay = fileList.join('');
                                        } else if (fieldValue.startsWith('http://') || fieldValue.startsWith('https://') || fieldValue.startsWith('/')) {
                                            // It's a URL - check if it's an image
                                            var imageExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg', '.bmp'];
                                            var isImage = false;
                                            for (var ext = 0; ext < imageExtensions.length; ext++) {
                                                if (fieldValue.toLowerCase().indexOf(imageExtensions[ext]) !== -1) {
                                                    isImage = true;
                                                    break;
                                                }
                                            }
                                            if (isImage) {
                                                fileDisplay = '<img src="' + $('<div>').text(fieldValue).html() + '" style="max-width: 100px; max-height: 100px; object-fit: contain; border: 1px solid #ccc; border-radius: 4px; display: block;" alt="Uploaded image" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';" /><a href="' + $('<div>').text(fieldValue).html() + '" target="_blank" style="color: #0066cc; text-decoration: underline; display: none;">View Image</a>';
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
                                    } else if (isUrlField && fieldValue && typeof fieldValue === 'string' && fieldValue.indexOf('<a href') !== -1) {
                                        // URL field already formatted as link
                                        html += '<td style="padding: 10px; vertical-align: top;">' + fieldValue + '</td>';
                                    } else if (isPasswordField) {
                                        // Password field already masked
                                        html += '<td style="padding: 10px; vertical-align: top;">' + (fieldValue || '') + '</td>';
                                    } else {
                                        // Escape HTML to prevent XSS (for non-file, non-url, non-password fields)
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
