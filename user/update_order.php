<?php
// update_order.php

if (isset($_POST['order'])) {
    $order = $_POST['order'];
    $formdataid = $_POST['id'];

    // Connect to your database (replace with your connection details)
    $conn = new mysqli('localhost', 'root', '', 'form_builder');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update the order in the database
    foreach ($formdataid as $position => $id) {
        echo "<pre>";
        print_r($id);
        $sql = "UPDATE form_data SET position = $position WHERE id = $id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }
    $stmt->close();
    $conn->close();
}
