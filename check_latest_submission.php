<?php
/**
 * Check the latest form submission in the database
 * This helps verify if submissions are being saved
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('append/connection.php');

echo "<h2>Latest Form Submissions</h2>";

// Get the 5 most recent submissions
$result = $GLOBALS['conn']->query("SELECT * FROM `form_submissions` ORDER BY id DESC LIMIT 5");

if ($result && $result->num_rows > 0) {
    echo "<p>Found <strong>" . $result->num_rows . "</strong> recent submissions:</p>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr>";
    echo "<th>ID</th><th>Form ID</th><th>Created At</th><th>IP Address</th><th>Status</th><th>Submission Data</th>";
    echo "</tr>";
    
    while ($row = $result->fetch_assoc()) {
        $submission_data = json_decode($row['submission_data'], true);
        $data_preview = '';
        
        if (is_array($submission_data)) {
            $data_parts = array();
            foreach ($submission_data as $key => $value) {
                if (!in_array($key, array('routine_name', 'store', 'form_id', 'id'))) {
                    $data_parts[] = "<strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars(substr($value, 0, 50));
                }
            }
            $data_preview = implode("<br>", $data_parts);
        } else {
            $data_preview = htmlspecialchars(substr($row['submission_data'], 0, 100));
        }
        
        // Check if it's test data
        $is_test_data = false;
        if (is_array($submission_data)) {
            if (isset($submission_data['name']) && $submission_data['name'] === 'Test User') {
                $is_test_data = true;
            }
            if (isset($submission_data['email']) && $submission_data['email'] === 'test@example.com') {
                $is_test_data = true;
            }
        }
        
        $row_color = $is_test_data ? '#ffe6e6' : '#e6ffe6';
        
        echo "<tr style='background-color: $row_color;'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['form_id'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "<td>" . $row['ip_address'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $data_preview . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Show full JSON for the latest entry
    $latest = $GLOBALS['conn']->query("SELECT * FROM `form_submissions` ORDER BY id DESC LIMIT 1");
    if ($latest && $latest->num_rows > 0) {
        $latest_row = $latest->fetch_assoc();
        echo "<h3>Latest Entry (Full JSON):</h3>";
        echo "<pre style='background: #f5f5f5; padding: 15px; overflow-x: auto;'>";
        echo htmlspecialchars($latest_row['submission_data']);
        echo "</pre>";
        
        $parsed = json_decode($latest_row['submission_data'], true);
        echo "<h3>Parsed Data:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 15px;'>";
        print_r($parsed);
        echo "</pre>";
    }
    
} else {
    echo "<p style='color: red;'>No submissions found in database.</p>";
}

echo "<hr>";
echo "<p><a href='test_storefront.html'>← Back to Test Storefront</a></p>";
echo "<p><a href='check_form_submission.php'>Check Form Submission Diagnostic</a></p>";
?>

