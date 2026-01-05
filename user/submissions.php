<?php 
include_once('cls_header.php'); 
$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : 0;
?>
<body style="padding: 20px;">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="margin-bottom: 20px;">
            <a href="index.php?shop=<?php echo $store; ?>" class="Polaris-Button">Back to Dashboard</a>
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
        $.ajax({
            url: "ajax_call.php",
            type: "post",
            dataType: "json",
            data: {'routine_name': 'getFormSubmissions', 'store': store, 'form_id': form_id},
            success: function (response) {
                if(response.result == 'success') {
                    var html = '';
                    if(response.data.length > 0) {
                        $.each(response.data, function(i, item){
                            var dataStr = '';
                            try {
                                var formData = JSON.parse(item.submission_data);
                                // Format JSON nicely or just list values
                                $.each(formData, function(j, field){
                                    // simple field name display
                                    dataStr += '<b>' + field.name + ':</b> ' + field.value + '<br>';
                                });
                            } catch(e) {
                                dataStr = item.submission_data;
                            }
                            
                            html += '<tr style="border-bottom: 1px solid #dfe3e8;">';
                            html += '<td style="padding: 10px; vertical-align: top;">' + item.id + '</td>';
                            html += '<td style="padding: 10px; vertical-align: top;">' + item.created_at + '</td>';
                            html += '<td style="padding: 10px; vertical-align: top;">' + dataStr + '</td>';
                            html += '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="3" style="padding: 10px; text-align: center;">No submissions found</td></tr>';
                    }
                    $('#submissions_list').html(html);
                }
            }
        });
    });
    </script>
</body>
</html>
