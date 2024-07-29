<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function openConnection() {
    $hostname = 'localhost';
    $username = 'pmtool_db';
    $password = 'Admin@123[];';
    $database = 'pmtool_db';
    $port = 3306;

    // Create connection
    $conn = new mysqli($hostname, $username, $password, $database, $port);
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

if (isset($_GET['supplier_id'])) {
    $supplier_id = $_GET['supplier_id'];
    $conn = openConnection();

    $sql = "SELECT complete_url, terminate_url, quality_term_url, survey_close_url, over_quota_url FROM Suppliers WHERE Supplier_Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $urls = $result->fetch_assoc();
    $stmt->close();
    closeConnection($conn);

    echo json_encode($urls);
} else {
    echo json_encode([]);
}
?>
