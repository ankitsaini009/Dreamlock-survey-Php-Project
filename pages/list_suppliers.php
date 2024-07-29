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

// Function to fetch all suppliers from the database
function fetchSuppliers() {
    $conn = openConnection();
    $sql = "SELECT supplier_id, supplier_name, supplier_website, contact_number, email_id, country, panel_size, complete_url, terminate_url, quality_term_url, survey_close_url, over_quota_url, about_supplier FROM Suppliers";
    $result = $conn->query($sql);
    $suppliers = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $suppliers[] = $row;
        }
    } else {
        error_log("Error fetching suppliers: " . $conn->error);
        echo "Error fetching suppliers: " . $conn->error;
    }
    closeConnection($conn);
    return $suppliers;
}

// Function to delete a supplier from the database
function deleteSupplier($supplierId) {
    $conn = openConnection();
    $stmt = $conn->prepare("DELETE FROM Suppliers WHERE supplier_id = ?");
    if (!$stmt) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("i", $supplierId);
    if ($stmt->execute()) {
        echo "<p class='text-green-500'>Supplier deleted successfully.</p>";
    } else {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        echo "<p class='text-red-500'>ERROR: Could not delete the supplier. " . htmlspecialchars($stmt->error) . "</p>";
    }
    $stmt->close();
    closeConnection($conn);
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_supplier_id'])) {
    deleteSupplier($_POST['delete_supplier_id']);
}

$suppliers = fetchSuppliers();
?>
<?php
    include  '../include/header.php';
    include  '../include/navbar.php';
    include '../db2.php';
?>

    <div class="container mx-auto px-4">
        <break>
        <h1 class="text-2xl font-bold  my-4">Supplier List</h1>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Website</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($suppliers) > 0): ?>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td>
                                <a href="#" class="text-primary" data-toggle="modal" data-target="#supplierModal" data-supplier='<?= json_encode($supplier) ?>'>
                                    <?= htmlspecialchars($supplier['supplier_name']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($supplier['supplier_website']) ?></td>
                            <td><?= htmlspecialchars($supplier['contact_number']) ?></td>
                            <td><?= htmlspecialchars($supplier['email_id']) ?></td>
                            <td><?= htmlspecialchars($supplier['country']) ?></td>
                            <td>
                                <a href="edit_supplier.php?supplier_id=<?= $supplier['supplier_id'] ?>" class="text-blue-500 hover:underline">Edit</a>
                                <form action="list_suppliers.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_supplier_id" value="<?= $supplier['supplier_id'] ?>">
                                    <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="text-center" colspan="6">No suppliers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Supplier Details Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalLabel">Supplier Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Supplier Name:</strong> <span id="modal-supplier-name"></span></p>
                    <p><strong>Website:</strong> <span id="modal-supplier-website"></span></p>
                    <p><strong>Contact Number:</strong> <span id="modal-supplier-contact"></span></p>
                    <p><strong>Email:</strong> <span id="modal-supplier-email"></span></p>
                    <p><strong>Country:</strong> <span id="modal-supplier-country"></span></p>
                    <p><strong>Panel Size:</strong> <span id="modal-supplier-panel-size"></span></p>
                    <p><strong>Complete URL:</strong> <span id="modal-supplier-complete-url"></span></p>
                    <p><strong>Terminate URL:</strong> <span id="modal-supplier-terminate-url"></span></p>
                    <p><strong>Quality Term URL:</strong> <span id="modal-supplier-quality-term-url"></span></p>
                    <p><strong>Survey Close URL:</strong> <span id="modal-supplier-survey-close-url"></span></p>
                    <p><strong>Over Quota URL:</strong> <span id="modal-supplier-over-quota-url"></span></p>
                    <p><strong>About Supplier:</strong> <span id="modal-supplier-about"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Import jQuery and Bootstrap JS from CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $('#supplierModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var supplier = button.data('supplier'); // Extract info from data-* attributes

            // Update the modal's content with supplier details
            $('#modal-supplier-name').text(supplier.supplier_name);
            $('#modal-supplier-website').text(supplier.supplier_website);
            $('#modal-supplier-contact').text(supplier.contact_number);
            $('#modal-supplier-email').text(supplier.email_id);
            $('#modal-supplier-country').text(supplier.country);
            $('#modal-supplier-panel-size').text(supplier.panel_size);
            $('#modal-supplier-complete-url').text(supplier.complete_url);
            $('#modal-supplier-terminate-url').text(supplier.terminate_url);
            $('#modal-supplier-quality-term-url').text(supplier.quality_term_url);
            $('#modal-supplier-survey-close-url').text(supplier.survey_close_url);
            $('#modal-supplier-over-quota-url').text(supplier.over_quota_url);
            $('#modal-supplier-about').text(supplier.about_supplier);
        });
    </script>
</body>
</html>
