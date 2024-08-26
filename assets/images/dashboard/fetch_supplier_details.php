<?php
if (isset($_GET['supplier_id'])) {
    $supplier_id = $_GET['supplier_id'];

    $conn = new mysqli('localhost', 'pmtool_db', 'Admin@123[];', 'pmtool_db', 3306);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT complete_url, terminate_url, quality_term_url, survey_close_url, over_quota_url FROM Suppliers WHERE supplier_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier_details = $result->fetch_assoc();
    $stmt->close();

    $conn->close();

    echo json_encode($supplier_details);
}
?>
