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
    foreach ($order as $position => $id) {
        echo "UPDATE form_data SET position = ? WHERE id = $formdataid";
        $sql = "UPDATE form_data SET position = ? WHERE id = $formdataid";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $position, $id);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();
}
