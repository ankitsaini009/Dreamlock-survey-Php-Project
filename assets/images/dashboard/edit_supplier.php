<?php 
    include  '../include/header.php';
    include  '../include/navbar.php';
    include '../db2.php';
?>
<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function openConnection() {
    $hostname = 'localhost';            // Database server
    $username = 'root';            // Database username
    $password = '';         // Database password
    $database = 'pmtool_db';            // Database name
    $port     = 3306;                   // The default MySQL port is 3306

    // Create connection
    $conn = new mysqli($hostname, $username, $password, $database, $port);
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function closeConnection($conn) {
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = openConnection();

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE Suppliers SET supplier_name = ?, supplier_website = ?, contact_number = ?, email_id = ?, country = ?, panel_size = ?, complete_url = ?, terminate_url = ?, quality_term_url = ?, survey_close_url = ?, over_quota_url = ?, about_supplier = ? WHERE supplier_id = ?");
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ssssisssssssi", $supplier_name, $supplier_website, $contact_number, $email_id, $country, $panel_size, $complete_url, $terminate_url, $quality_term_url, $survey_close_url, $over_quota_url, $about_supplier, $supplier_id);

    // Set parameters and execute
    $supplier_name = $_POST['supplier_name'];
    $supplier_website = $_POST['supplier_website'];
    $contact_number = $_POST['contact_number'];
    $email_id = $_POST['email_id'];
    $country = $_POST['country'];
    // $panel_size = $_POST['panel_size'];
    $complete_url = $_POST['complete_url'];
    $terminate_url = $_POST['terminate_url'];
    $quality_term_url = $_POST['quality_term_url'];
    $survey_close_url = $_POST['survey_close_url'];
    $over_quota_url = $_POST['over_quota_url'];
    $about_supplier = $_POST['about_supplier'];
    $supplier_id = $_POST['supplier_id'];

    if ($stmt->execute()) {
        echo "<script> alert('Supplier updated successfully.')</script> <a href='list_suppliers.php'></a>";
    } else {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        echo "<p class='text-red-500'>ERROR: Could not execute the query. " . htmlspecialchars($stmt->error) . "</p>";
    }

    // Close statement and connection
    $stmt->close();
    closeConnection($conn);
} else {
    // Fetch supplier data for editing
    if (isset($_GET['supplier_id'])) {
        $conn = openConnection();
        $stmt = $conn->prepare("SELECT supplier_name, supplier_website, contact_number, email_id, country, panel_size, complete_url, terminate_url, quality_term_url, survey_close_url, over_quota_url, about_supplier FROM Suppliers WHERE supplier_id = ?");
        if (!$stmt) {
            error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("i", $supplier_id);
        $supplier_id = $_GET['supplier_id'];
        $stmt->execute();
        $stmt->bind_result($supplier_name, $supplier_website, $contact_number, $email_id, $country, $panel_size, $complete_url, $terminate_url, $quality_term_url, $survey_close_url, $over_quota_url, $about_supplier);
        $stmt->fetch();
        $stmt->close();
        closeConnection($conn);
    } else {
        die("Supplier ID not provided.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
    <!-- Import Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4">
        <h1 class="text-xl font-bold text-start my-4">Edit Supplier</h1>
        <form action="edit_supplier.php" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <input type="hidden" name="supplier_id" value="<?= htmlspecialchars($supplier_id) ?>">
            <div class="mb-4">
                <label for="supplier_name" class="block text-gray-700 text-sm font-bold mb-2">Supplier Name:</label>
                <input type="text" name="supplier_name" id="supplier_name" value="<?= htmlspecialchars($supplier_name) ?>" required class="form-control">
            </div>
            <div class="mb-4">
                <label for="supplier_website" class="block text-gray-700 text-sm font-bold mb-2">Supplier Website:</label>
                <input type="text" name="supplier_website" id="supplier_website" value="<?= htmlspecialchars($supplier_website) ?>" required class="form-control">
            </div>
            <div class="mb-4">
                <label for="contact_number" class="block text-gray-700 text-sm font-bold mb-2">Contact Number:</label>
                <input type="text" name="contact_number" id="contact_number" value="<?= htmlspecialchars($contact_number) ?>" class="form-control">
            </div>
            <div class="mb-4">
                <label for="email_id" class="block text-gray-700 text-sm font-bold mb-2">Email ID:</label>
                <input type="email" name="email_id" id="email_id" value="<?= htmlspecialchars($email_id) ?>" class="form-control">
            </div>
<div class="mb-4">
                <label for="country" class="block text-gray-700 text-sm font-bold mb-2">Country:</label>
                <select name="country" id="country" required class="form-control">
                    <option value="">-- Select Country --</option>
                    <option value=""> Multi Country </option>
                </select>
            </div>
            <div class="mb-4">
                <label for="complete_url" class="block text-gray-700 text-sm font-bold mb-2">Complete URL:</label>
                <input type="text" name="complete_url" id="complete_url" value="<?= htmlspecialchars($complete_url) ?>" class="form-control">
            </div>
            <div class="mb-4">
                <label for="terminate_url" class="block text-gray-700 text-sm font-bold mb-2">Terminate URL:</label>
                <input type="text" name="terminate_url" id="terminate_url" value="<?= htmlspecialchars($terminate_url) ?>" class="form-control">
            </div>
            <div class="mb-4">
                <label for="quality_term_url" class="block text-gray-700 text-sm font-bold mb-2">Quality Term URL:</label>
                <input type="text" name="quality_term_url" id="quality_term_url" value="<?= htmlspecialchars($quality_term_url) ?>" class="form-control">
            </div>
            <div class="mb-4">
                <label for="survey_close_url" class="block text-gray-700 text-sm font-bold mb-2">Survey Close URL:</label>
                <input type="text" name="survey_close_url" id="survey_close_url" value="<?= htmlspecialchars($survey_close_url) ?>" class="form-control">
            </div>
            <div class="mb-4">
                <label for="over_quota_url" class="block text-gray-700 text-sm font-bold mb-2">Over Quota URL:</label>
                <input type="text" name="over_quota_url" id="over_quota_url" value="<?= htmlspecialchars($over_quota_url) ?>" class="form-control">
            </div>
            <div class="mb-4">
                <label for="about_supplier" class="block text-gray-700 text-sm font-bold mb-2">About Supplier:</label>
                <textarea name="about_supplier" id="about_supplier" class="form-control"><?= htmlspecialchars($about_supplier) ?></textarea>
            </div>
            <div class="flex items-center justify-between">
                 <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Supplier</button>
                <a href="list_suppliers.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cancel</a>
            </div>
        </form>
    </div>
      <script>
        $(document).ready(function() {
            // Fetch countries and populate the dropdown
            $.getJSON('https://restcountries.com/v3.1/all', function(data) {
                var options = '';
                data.forEach(function(country) {
                    options += `<option value="${country.cca2}">${country.name.common}</option>`;
                });
                $('#country').append(options);
            }).fail(function() {
                console.log("An error occurred while loading country data.");
            });
        });
    </script>
</body>
</html>
