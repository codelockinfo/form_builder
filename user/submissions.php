<?php 
include_once('cls_header.php'); 
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : 0;
?>
<body style="padding: 20px;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="margin-bottom: 20px;">
            <a href="dashboard_submissions.php?shop=<?php echo $store; ?>" class="Polaris-Button">Back to Dashboard</a>
            <h2>Form Submissions</h2>
        </div>
        
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <table class="Polaris-DataTable__Table" style="width: 100%; border-collapse: collapse;">
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
                                    // For other fields, try label-based name
                                    else {
                                        var otherLabelSlug = fieldLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                                        if (otherLabelSlug && formData[otherLabelSlug] !== undefined) {
                                            fieldValue = formData[otherLabelSlug];
                                        } else if (fieldName && formData[fieldName] !== undefined) {
                                            fieldValue = formData[fieldName];
                                        }
                                    }
                                    
                                    // Handle array values (like checkboxes)
                                    if(Array.isArray(fieldValue)) {
                                        fieldValue = fieldValue.join(', ');
                                    }
                                    
                                    // Escape HTML to prevent XSS
                                    var safeFieldValue = $('<div>').text(fieldValue || '').html();
                                    html += '<td style="padding: 10px; vertical-align: top;">' + safeFieldValue + '</td>';
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
