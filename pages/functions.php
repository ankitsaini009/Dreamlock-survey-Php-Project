<?php
include 'functions.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function openConnection() {
    $hostname = 'localhost';            // Database server
    $username = 'pmtool_db';            // Database username
    $password = 'Admin@123[];';         // Database password
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

function safeOutput($string) {
    return htmlspecialchars($string ?? '');
}

function getClientCode($conn, $client) {
    $sql = "SELECT client_code FROM Clientele WHERE Client_Name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $client);
    $stmt->execute();
    $result = $stmt->get_result();
    $client_code = $result->fetch_assoc()['client_code'];
    $stmt->close();
    return $client_code;
}

function getLastComplete($conn, $project_code) {
    $sql = "SELECT MAX(time_stamp) as last_complete FROM SurveyLog WHERE project_code = ? AND Status = 'Complete'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $project_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $last_complete = $result->fetch_assoc()['last_complete'];
    $stmt->close();
    return $last_complete;
}

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

    $client_code = getClientCode($conn, $project['client']);
    $last_complete = getLastComplete($conn, $project_code);
    closeConnection($conn);

    $links = json_decode($project['links'], true);
} else {
    die("Project code not provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../nav/css/style.css">
</head>
<body>
    <div class="wrapper d-flex align-items-stretch">
        <?php includeSidebar(); ?>
        <div class="main-content">
            <header>
                <a href="supplier_mapping.php">Project Mapping</a>
            </header>
            <!-- Page-specific content here -->
            <div class="container mx-auto px-4 py-8">
                <!-- Project details content -->
                <div class="relative">
                    <h1 class="text-2xl font-bold text-center mb-8">Project Details</h1>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded edit-button" id="editButton" onclick="toggleEdit()">Edit</button>
                </div>
                <?php if ($project): ?>
                    <form action="project_details.php?project_code=<?= safeOutput($project_code) ?>" method="POST" id="projectForm">
                        <input type="hidden" name="project_code" value="<?= safeOutput($project['project_code']) ?>">
                        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Project Code:</div>
                                <div><?= safeOutput($project['project_code']) ?></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Project Name:</div>
                                <div><input type="text" name="project_name" value="<?= safeOutput($project['project_name']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Description:</div>
                                <div><input type="text" name="project_description" value="<?= safeOutput($project['project_description']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Client Code:</div>
                                <div><?= safeOutput($client_code) ?></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Client Name:</div>
                                <div><input type="text" name="client" value="<?= safeOutput($project['client']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Country:</div>
                                <div><input type="text" name="project_country" value="<?= safeOutput($project['project_country']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">LOI (Min):</div>
                                <div><input type="text" name="LOI" value="<?= safeOutput($project['LOI']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Sample Size:</div>
                                <div><input type="text" name="sample_size" value="<?= safeOutput($project['sample_size']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Start Date:</div>
                                <div><input type="date" name="start_date" value="<?= safeOutput($project['start_date']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">End Date:</div>
                                <div><input type="date" name="end_date" value="<?= safeOutput($project['end_date']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">IR (%):</div>
                                <div><input type="text" name="IR" value="<?= safeOutput($project['IR']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Click Quota:</div>
                                <div><input type="text" name="respondent_click_quota" value="<?= safeOutput($project['respondent_click_quota']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">CPI:</div>
                                <div><input type="text" name="CPI" value="<?= safeOutput($project['CPI']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Last Complete:</div>
                                <div><?= safeOutput($last_complete) ?></div>
                            </div>
                        </div>

                        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold mb-4">Links</h2>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Live Link:</div>
                                <div><input type="text" name="live_link" value="<?= safeOutput($links[0]['live']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Test Link:</div>
                                <div><input type="text" name="test_link" value="<?= safeOutput($links[0]['test']) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                        </div>

                        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold mb-4">Project Filters</h2>
                            <div class="details-grid mb-4">
                                <?php
                                $filter_options = [
                                    "geo_location" => "Geo Location",
                                    "unique_ip_count" => "Unique IP",
                                    "proxy_vpn" => "Proxy/VPN",
                                    "tsign" => "TSign",
                                    "captcha" => "Captcha",
                                    "pre_screen" => "Prescreening",
                                    "url_protection" => "Url Protection",
                                    "dynamic_thanks_url" => "Dynamic Thanks Url"
                                ];
                                foreach ($filter_options as $key => $label) {
                                    $checked = $filters[$key] ? 'checked' : '';
                                    echo "<div class='flex items-center mb-2'>
                                            <input type='checkbox' name='filters[$key]' value='1' $checked class='mr-2' disabled>
                                            <span>$label</span>
                                          </div>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold mb-4">Device Filters</h2>
                            <div class="details-grid mb-4">
                                <?php
                                $device_options = [
                                    "mobile_study" => "Mobile Study",
                                    "tablet_study" => "Tablet Study",
                                    "desktop_study" => "Desktop Study"
                                ];
                                foreach ($device_options as $key => $label) {
                                    $checked = $filters[$key] ? 'checked' : '';
                                    echo "<div class='flex items-center mb-2'>
                                            <input type='checkbox' name='filters[$key]' value='1' $checked class='mr-2' disabled>
                                            <span>$label</span>
                                          </div>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="bg-white shadow-md rounded-lg p-6">
                            <h2 class="text-xl font-bold mb-4">Project Field Data</h2>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Field LOI:</div>
                                <div>0</div> <!-- Placeholder for Field LOI -->
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Field DropRate:</div>
                                <div>6.45%</div> <!-- Placeholder for Field DropRate -->
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Field IR:</div>
                                <div>0.00%</div> <!-- Placeholder for Field IR -->
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Field Conversion:</div>
                                <div>0.00%</div> <!-- Placeholder for Field Conversion -->
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Remaining Complete:</div>
                                <div>10000</div> <!-- Placeholder for Remaining Complete -->
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" id="saveButton" style="display:none;">Save</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="text-center text-red-500">No project details found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        function toggleEdit() {
            var form = document.getElementById('projectForm');
            var inputs = form.querySelectorAll('input[type="text"], input[type="date"]');
            var checkboxes = form.querySelectorAll('input[type="checkbox"]');
            var saveButton = document.getElementById('saveButton');
            var editButton = document.getElementById('editButton');

            if (editButton.innerText === 'Edit') {
                inputs.forEach(input => input.removeAttribute('readonly'));
                checkboxes.forEach(checkbox => checkbox.removeAttribute('disabled'));
                saveButton.style.display = 'inline-block';
                editButton.innerText = 'Cancel';
            } else {
                inputs.forEach(input => input.setAttribute('readonly', true));
                checkboxes.forEach(checkbox => checkbox.setAttribute('disabled', true));
                saveButton.style.display = 'none';
                editButton.innerText = 'Edit';
            }
        }
    </script>
    <script src="../nav/js/jquery.min.js"></script>
    <script src="../nav/js/popper.js"></script>
    <script src="../nav/js/bootstrap.min.js"></script>
    <script src="../nav/js/main.js"></script>
</body>
</html>
