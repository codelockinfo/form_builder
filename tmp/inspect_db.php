<?php
define('ABS_PATH', 'c:/wamp64/www/form_builder/');
include_once(ABS_PATH . 'append/connection.php');
include_once(ABS_PATH . 'collection/mongo_mysql/mysql/DB_Class.php');

$form_id = '37'; // Change this if you know a better ID
$sql = "SELECT top_header_data FROM globo_forms WHERE id = '$form_id' OR public_id = '$form_id'";
$res = mysqli_query($conn, $sql);
if ($res && $row = mysqli_fetch_assoc($res)) {
    echo "Raw DB data: " . $row['top_header_data'] . "\n";
    $data = unserialize($row['top_header_data']);
    echo "Unserialized data: " . print_r($data, true) . "\n";
} else {
    echo "No data found or query failed: " . mysqli_error($conn) . "\n";
}
?>
