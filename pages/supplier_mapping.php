<!-- project_details.php -->
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

function openConnection()
{
    $hostname = 'localhost';
    $username = 'root';
    $password = '';
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

function closeConnection($conn)
{
    $conn->close();
}

function safeOutput($string)
{
    return htmlspecialchars($string ?? '');
}

function generateHashIdentifier()
{
    return bin2hex(random_bytes(16));
}

function generateEndPageLinks($hash_identifier)
{
    $base_url = 'http://localhost/app.dreamlock/pages/end_survey.php';
    $links = [
        'complete' => "$base_url?hash_identifier=$hash_identifier&status=complete",
        'terminate' => "$base_url?hash_identifier=$hash_identifier&status=terminate",
        'quality_term' => "$base_url?hash_identifier=$hash_identifier&status=quality_term",
        'survey_close' => "$base_url?hash_identifier=$hash_identifier&status=survey_close",
        'over_quota' => "$base_url?hash_identifier=$hash_identifier&status=over_quota",
    ];
    return $links;
}

// Handle form submission for adding/updating supplier mappings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = openConnection();

    $project_code = $_POST['project_code'];
    $supplier_id = $_POST['supplier_id'];
    $supplier_quota = $_POST['supplier_quota'];
    $click_quota = $_POST['click_quota'];
    $CPI = $_POST['CPI'];
    $complete_url = $_POST['complete_url'];
    $terminate_url = $_POST['terminate_url'];
    $quality_term_url = $_POST['quality_term_url'];
    $survey_close_url = $_POST['survey_close_url'];
    $over_quota_url = $_POST['over_quota_url'];
    $supplier_identifier = $_POST['supplier_identifier'];
    $hash_identifier = generateHashIdentifier();

    $sql = "INSERT INTO SurveyLog (project_code, Supplier_Id, Supplier_Identifier, Hash_Identifier, Status, LOI, IP_Address, Country, Browser_Detail, Device_Type, Is_Test_Link)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $status = "Complete"; // Placeholder, should be dynamic
    $loi = 0; // Placeholder, should be dynamic
    $ip_address = ""; // Placeholder, should be dynamic
    $country = ""; // Placeholder, should be dynamic
    $browser_detail = ""; // Placeholder, should be dynamic
    $device_type = ""; // Placeholder, should be dynamic
    $is_test_link = 0; // Placeholder, should be dynamic

    $stmt->bind_param("ssssssisssi", $project_code, $supplier_id, $supplier_identifier, $hash_identifier, $status, $loi, $ip_address, $country, $browser_detail, $device_type, $is_test_link);

    if ($stmt->execute()) {
        $project_code = htmlspecialchars($_POST['project_code']);
        echo "<p class='text-green-500'>Supplier mapping saved successfully.</p>";
        echo "<script type='text/javascript'>
            setTimeout(function() {
                window.location.href = 'project_details.php?project_code=" . urlencode($project_code) . "';
            }, 1000); // Redirect after 1 second
          </script>";
        exit;
    } else {
        echo "<p class='text-red-500'>ERROR: Could not execute the query. " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
    closeConnection($conn);
}

// Fetch project details and existing supplier mappings
if (isset($_GET['project_code'])) {
    $project_code = $_GET['project_code'];
    $conn = openConnection();

    $sql = "SELECT * FROM ProjectDetails WHERE project_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $project_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
    $stmt->close();

    $sql = "SELECT sl.*, s.supplier_name FROM SurveyLog sl
            JOIN Suppliers s ON sl.Supplier_Id = s.supplier_id
            WHERE sl.project_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $project_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier_mappings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    closeConnection($conn);
} else {
    die("Project code not provided.");
}

// Fetch suppliers for the dropdown
$conn = openConnection();
$sql = "SELECT supplier_id, supplier_name, complete_url, terminate_url, quality_term_url, survey_close_url, over_quota_url FROM Suppliers";
$result = $conn->query($sql);
$suppliers = $result->fetch_all(MYSQLI_ASSOC);
closeConnection($conn);
?>



<style>
    .details-grid {
        display: grid;
        grid-template-columns: 1fr 3fr;
    }

    .edit-button {
        position: absolute;
        top: 1rem;
        right: 1rem;
    }
</style>

<div class="container mx-auto px-4 py-8">
    <break>
        <h1 class="text-2xl font-bold text-center mb-8">Supplier Mapping</h1>
        <break>

            <div class="bg-white shadow-md rounded-lg p-6 mb-8">

                <h2 class="text-xl font-bold mb-4">Project Details</h2>
                <div class="details-grid mb-4">
                    <div class="font-semibold">Project Code:</div>
                    <div><?= safeOutput($project['project_code']) ?></div>
                </div>
                <div class="details-grid mb-4">
                    <div class="font-semibold">Project Name:</div>
                    <div><?= safeOutput($project['project_name']) ?></div>
                </div>
                <div class="details-grid mb-4">
                    <div class="font-semibold">Description:</div>
                    <div><?= safeOutput($project['project_description']) ?></div>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">Supplier Mappings</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600">S.No.</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600">Supplier Name</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600">Live Link</th>
                            <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600">Test Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($supplier_mappings as $index => $mapping) : ?>
                            <tr>
                                <td class="px-6 py-4 border-b border-gray-300"><?= $index + 1 ?></td>
                                <td class="px-6 py-4 border-b border-gray-300"><?= safeOutput($mapping['supplier_name']) ?></td>
                                <td class="px-6 py-4 border-b border-gray-300"><?= "http://localhost/app.dreamlock/pages/start_survey.php?project_code=" . safeOutput($project_code) . "&supplier_identifier=" . safeOutput($mapping['Supplier_Identifier']) ?></td>
                                <td class="px-6 py-4 border-b border-gray-300"><?= "http://localhost/app.dreamlock/pages/start_survey.php?project_code=" . safeOutput($project_code) . "&supplier_identifier=" . safeOutput($mapping['Supplier_Identifier']) . "&status=test" ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">Add / Update Supplier Mapping</h2>
                <form action="supplier_mapping.php?project_code=<?= safeOutput($project_code) ?>" method="POST" id="supplierForm">
                    <input type="hidden" name="project_code" value="<?= safeOutput($project_code) ?>">
                    <div class="mb-4">
                        <label for="supplier_id" class="block text-gray-700">Supplier:</label>
                        <select name="supplier_id" id="supplier_id" class="w-full border rounded p-2" onchange="fetchSupplierUrls()">
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $supplier) : ?>
                                <option value="<?= safeOutput($supplier['supplier_id']) ?>"><?= safeOutput($supplier['supplier_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="supplier_quota" class="block text-gray-700">Supplier Quota:</label>
                        <input type="text" name="supplier_quota" id="supplier_quota" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label for="click_quota" class="block text-gray-700">Click Quota:</label>
                        <input type="text" name="click_quota" id="click_quota" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label for="CPI" class="block text-gray-700">CPI:</label>
                        <input type="text" name="CPI" id="CPI" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label for="complete_url" class="block text-gray-700">Complete URL:</label>
                        <input type="text" name="complete_url" id="complete_url" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label for="terminate_url" class="block text-gray-700">Terminate URL:</label>
                        <input type="text" name="terminate_url" id="terminate_url" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label for="quality_term_url" class="block text-gray-700">Quality Term URL:</label>
                        <input type="text" name="quality_term_url" id="quality_term_url" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label for="survey_close_url" class="block text-gray-700">Survey Close URL:</label>
                        <input type="text" name="survey_close_url" id="survey_close_url" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label for="over_quota_url" class="block text-gray-700">Over Quota URL:</label>
                        <input type="text" name="over_quota_url" id="over_quota_url" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label for="supplier_identifier" class="block text-gray-700">Supplier Identifier:</label>
                        <input type="text" name="supplier_identifier" id="supplier_identifier" class="w-full border rounded p-2">
                    </div>
                    <div class="text-left">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save</button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">End Page Links</h2>
                <?php
                $hash_identifier = generateHashIdentifier();
                $end_links = generateEndPageLinks($hash_identifier);
                ?>
                <ul>
                    <?php foreach ($end_links as $link) : ?>
                        <li><a href="<?= htmlspecialchars($link) ?>" class="text-blue-500"><?= htmlspecialchars($link) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
</div>
<script>
    function fetchSupplierUrls() {
        var suppliers = <?= json_encode($suppliers) ?>;
        var supplierId = document.getElementById('supplier_id').value;
        var supplier = suppliers.find(s => s.supplier_id === supplierId);

        if (supplier) {
            document.getElementById('complete_url').value = supplier.complete_url || '';
            document.getElementById('terminate_url').value = supplier.terminate_url || '';
            document.getElementById('quality_term_url').value = supplier.quality_term_url || '';
            document.getElementById('survey_close_url').value = supplier.survey_close_url || '';
            document.getElementById('over_quota_url').value = supplier.over_quota_url || '';
        }
    }
</script>
<script src="nav/js/jquery.min.js"></script>
<script src="nav/js/popper.js"></script>
<script src="nav/js/bootstrap.min.js"></script>
<script src="nav/js/main.js"></script>
</body>

</html>
<?php include '../include/footer.php'; ?>