<?php
include('../db2.php'); // Adjust the path to correctly include db2.php

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $projectId = $data['id'];

    // Query to toggle the status of the project
    // Assuming there is a 'status' column in the 'ProjectDetails' table
    $sql = "UPDATE ProjectDetails SET status = NOT status WHERE id = $projectId";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>
