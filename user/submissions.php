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
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #ccc;">ID</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #ccc;">Date</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #ccc;">Data</th>
                        </tr>
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
        console.log('Loading submissions for form_id:', form_id);
        console.log('Store:', store);
        
        $.ajax({
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            data: {'routine_name': 'getFormSubmissions', 'store': store, 'form_id': form_id},
            success: function (response) {
                console.log('Submissions response:', response);
                
                // Handle case where response might be a string (double-encoded JSON)
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch(e) {
                        console.error('Error parsing response:', e);
                        $('#submissions_list').html('<tr><td colspan="3" style="padding: 10px; text-align: center; color: #d32f2f;">Error: Invalid response from server</td></tr>');
                        return;
                    }
                }
                
                if(response.result == 'success') {
                    var html = '';
                    if(response.data && response.data.length > 0) {
                        console.log('Found', response.data.length, 'submissions');
                        $.each(response.data, function(i, item){
                            var dataStr = '';
                            try {
                                var formData = JSON.parse(item.submission_data);
                                // Format JSON - submission_data is a flat object with field names as keys
                                $.each(formData, function(fieldName, fieldValue){
                                    // Skip system fields
                                    if(fieldName !== 'routine_name' && fieldName !== 'store' && fieldName !== 'form_id' && fieldName !== 'id') {
                                        // Handle array values (like checkboxes)
                                        if(Array.isArray(fieldValue)) {
                                            fieldValue = fieldValue.join(', ');
                                        }
                                        // Escape HTML to prevent XSS
                                        var safeFieldName = $('<div>').text(fieldName).html();
                                        var safeFieldValue = $('<div>').text(fieldValue).html();
                                        dataStr += '<b>' + safeFieldName + ':</b> ' + safeFieldValue + '<br>';
                                    }
                                });
                            } catch(e) {
                                console.error('Error parsing submission data:', e, item);
                                dataStr = '<span style="color: #999;">Unable to parse submission data</span>';
                            }
                            
                            if(!dataStr) {
                                dataStr = '<span style="color: #999;">No data available</span>';
                            }
                            
                            html += '<tr style="border-bottom: 1px solid #dfe3e8;">';
                            html += '<td style="padding: 10px; vertical-align: top;">' + item.id + '</td>';
                            html += '<td style="padding: 10px; vertical-align: top;">' + (item.created_at || 'N/A') + '</td>';
                            html += '<td style="padding: 10px; vertical-align: top;">' + dataStr + '</td>';
                            html += '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="3" style="padding: 10px; text-align: center; color: #999;">No submissions found for this form</td></tr>';
                    }
                    $('#submissions_list').html(html);
                } else {
                    console.error('Error loading submissions:', response.msg || 'Unknown error');
                    $('#submissions_list').html('<tr><td colspan="3" style="padding: 10px; text-align: center; color: #d32f2f;">Error: ' + (response.msg || 'Failed to load submissions') + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                console.error('Response:', xhr.responseText);
                $('#submissions_list').html('<tr><td colspan="3" style="padding: 10px; text-align: center; color: #d32f2f;">Error: Failed to load submissions. Check console for details.</td></tr>');
            }
        });
    });
    </script>
</body>
</html>
