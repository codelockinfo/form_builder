<?php
/**
 * Test script to verify database insert is working
 * Run this to test if form_submissions table insert works
 * Access via: http://localhost/form_builder/test_db_insert.php
 * 
 * NOTE: This script inserts TEST DATA. Do not use this for actual form submissions.
 * Actual form submissions go through ajax_call.php -> addformdata()
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once('append/connection.php');

// Require explicit parameter to prevent accidental execution
if (!isset($_GET['test']) || $_GET['test'] !== 'yes') {
    die("<h2>Test Script Protection</h2><p>This script inserts test data. To run it, add ?test=yes to the URL.</p><p>Example: test_db_insert.php?test=yes&form_id=35</p>");
}

// Get form_id from URL if provided
$form_id = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 35;

// Test data - THIS IS ONLY FOR TESTING
$submission_data = json_encode(array('name' => 'Test User', 'email' => 'test@example.com', 'message' => 'Test message'));
$created_at = date('Y-m-d H:i:s');
$ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
$status = 0;

echo "<h2>=== Testing Database Insert ===</h2>";
echo "<p><strong>Form ID:</strong> $form_id</p>";
echo "<p><strong>Submission Data:</strong> $submission_data</p>";
echo "<p><strong>Created At:</strong> $created_at</p>";
echo "<p><strong>IP Address:</strong> $ip_address</p>";
echo "<hr>";

// Method 1: Direct mysqli query
echo "<h3>Method 1: Direct mysqli Prepared Statement</h3>";
$sql = "INSERT INTO `form_submissions` (`form_id`, `submission_data`, `created_at`, `ip_address`, `status`) 
        VALUES (?, ?, ?, ?, ?)";

echo "<p>SQL: <code>$sql</code></p>";

$stmt = $GLOBALS['conn']->prepare($sql);
if ($stmt) {
    $stmt->bind_param("isssi", $form_id, $submission_data, $created_at, $ip_address, $status);
    if ($stmt->execute()) {
        $insert_id = $GLOBALS['conn']->insert_id;
        $affected_rows = $GLOBALS['conn']->affected_rows;
        echo "<p style='color: green;'><strong>✓ SUCCESS</strong> - Insert ID: $insert_id, Affected Rows: $affected_rows</p>";
        
        // Verify the insert
        $verify = $GLOBALS['conn']->query("SELECT * FROM `form_submissions` WHERE id = $insert_id");
        if ($verify && $verify->num_rows > 0) {
            $row = $verify->fetch_assoc();
            echo "<p style='color: green;'><strong>✓ VERIFIED</strong> - Record found in database:</p>";
            echo "<pre>" . print_r($row, true) . "</pre>";
        } else {
            echo "<p style='color: red;'><strong>✗ WARNING</strong> - Record not found after insert!</p>";
        }
    } else {
        echo "<p style='color: red;'><strong>✗ ERROR:</strong> " . $stmt->error . "</p>";
    }
    $stmt->close();
} else {
    echo "<p style='color: red;'><strong>✗ ERROR preparing statement:</strong> " . $GLOBALS['conn']->error . "</p>";
}

echo "<hr>";

// Method 2: Check table structure
echo "<h3>Method 2: Checking table structure</h3>";
$result = $GLOBALS['conn']->query("DESCRIBE `form_submissions`");
if ($result) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>✗ ERROR:</strong> " . $GLOBALS['conn']->error . "</p>";
}

echo "<hr>";

// Method 3: Check existing data
echo "<h3>Method 3: Checking existing submissions</h3>";
$result = $GLOBALS['conn']->query("SELECT * FROM `form_submissions` ORDER BY id DESC LIMIT 10");
if ($result) {
    echo "<p>Found <strong>" . $result->num_rows . "</strong> submissions:</p>";
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Form ID</th><th>Created At</th><th>IP Address</th><th>Status</th><th>Data Preview</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $data_preview = substr($row['submission_data'], 0, 50) . '...';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['form_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ip_address']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . htmlspecialchars($data_preview) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'><strong>⚠ No submissions found in database</strong></p>";
    }
} else {
    echo "<p style='color: red;'><strong>✗ ERROR:</strong> " . $GLOBALS['conn']->error . "</p>";
}

echo "<hr>";
echo "<h3>=== Test Complete ===</h3>";
echo "<p><a href='test_storefront.html'>← Back to Test Storefront</a></p>";
?>

