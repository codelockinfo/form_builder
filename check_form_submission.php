<?php
/**
 * Diagnostic script to check what data is being received from form submissions
 * This will help identify if form data is being sent correctly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Form Submission Diagnostic</h2>";
echo "<p>This script shows what data would be received from a form submission.</p>";
echo "<hr>";

// Check if this is a POST request (simulating form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>POST Data Analysis:</h3>";
    echo "<ul>";
    echo "<li><strong>Total POST fields:</strong> " . count($_POST) . "</li>";
    echo "<li><strong>POST keys:</strong> " . implode(", ", array_keys($_POST)) . "</li>";
    
    // Check for system fields
    $system_fields = array('routine_name', 'store', 'form_id', 'id');
    $form_fields = array();
    foreach ($_POST as $key => $value) {
        if (!in_array($key, $system_fields)) {
            $form_fields[$key] = $value;
        }
    }
    
    echo "<li><strong>Form fields (excluding system fields):</strong> " . count($form_fields) . "</li>";
    echo "<li><strong>Form field names:</strong> " . implode(", ", array_keys($form_fields)) . "</li>";
    
    // Check for test data
    $has_test_data = false;
    if (isset($_POST['name']) && $_POST['name'] === 'Test User') {
        $has_test_data = true;
        echo "<li style='color: red;'><strong>⚠ WARNING: Test data detected!</strong></li>";
    }
    if (isset($_POST['email']) && $_POST['email'] === 'test@example.com') {
        $has_test_data = true;
        echo "<li style='color: red;'><strong>⚠ WARNING: Test email detected!</strong></li>";
    }
    
    if (!$has_test_data && !empty($form_fields)) {
        echo "<li style='color: green;'><strong>✓ Real form data detected</strong></li>";
    }
    
    echo "</ul>";
    
    // Show what would be saved
    $submission_data = $_POST;
    unset($submission_data['routine_name']);
    unset($submission_data['store']);
    unset($submission_data['form_id']);
    unset($submission_data['id']);
    
    echo "<h3>What Would Be Saved to Database:</h3>";
    echo "<pre>";
    echo "Submission Data (JSON):\n";
    echo json_encode($submission_data, JSON_PRETTY_PRINT);
    echo "</pre>";
    
} else {
    echo "<h3>Instructions:</h3>";
    echo "<ol>";
    echo "<li>Submit your form from the test storefront</li>";
    echo "<li>Check the browser console for the form data being sent</li>";
    echo "<li>Check the server error logs for 'addformdata' entries</li>";
    echo "<li>Verify the latest entry in the database</li>";
    echo "</ol>";
    
    echo "<h3>Check Latest Database Entry:</h3>";
    include_once('append/connection.php');
    
    $result = $GLOBALS['conn']->query("SELECT * FROM `form_submissions` ORDER BY id DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<p><strong>Latest Entry (ID: " . $row['id'] . "):</strong></p>";
        echo "<pre>";
        echo "Form ID: " . $row['form_id'] . "\n";
        echo "Created: " . $row['created_at'] . "\n";
        echo "IP: " . $row['ip_address'] . "\n";
        echo "Status: " . $row['status'] . "\n";
        echo "\nSubmission Data:\n";
        echo $row['submission_data'];
        echo "\n\nParsed JSON:\n";
        $parsed = json_decode($row['submission_data'], true);
        print_r($parsed);
        echo "</pre>";
        
        // Check if it's test data
        $parsed = json_decode($row['submission_data'], true);
        if (isset($parsed['name']) && $parsed['name'] === 'Test User') {
            echo "<p style='color: red;'><strong>⚠ This is TEST DATA from test_db_insert.php</strong></p>";
            echo "<p>To prevent test data insertion, the test script now requires ?test=yes parameter.</p>";
        } else {
            echo "<p style='color: green;'><strong>✓ This appears to be real form data</strong></p>";
        }
    } else {
        echo "<p>No entries found in database.</p>";
    }
}

echo "<hr>";
echo "<p><a href='test_storefront.html'>← Back to Test Storefront</a></p>";
?>

