<?php
$_SERVER['SERVER_NAME'] = 'localhost';
include_once 'append/connection.php';
$conn = $GLOBALS['conn'];

$res = mysqli_query($conn, "SELECT count(*) as total FROM forms WHERE form_header_data IS NOT NULL AND form_header_data != ''");
$row = mysqli_fetch_assoc($res);
echo "ROWS WITH HEADER DATA: " . $row['total'] . "\n";

$res = mysqli_query($conn, "SELECT count(*) as total FROM forms WHERE top_header_data IS NOT NULL AND top_header_data != ''");
$row = mysqli_fetch_assoc($res);
echo "ROWS WITH TOP HEADER DATA: " . $row['total'] . "\n";

// Show one form and its IDs
$res = mysqli_query($conn, "SELECT id, public_id FROM forms LIMIT 1");
$row = mysqli_fetch_assoc($res);
print_r($row);
