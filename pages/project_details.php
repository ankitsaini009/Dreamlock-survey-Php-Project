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

    function openConnection() {
        $hostname = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'pmtool_db';
        $port     = 3306;

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
        $client_code = $result->fetch_assoc()['client_code'] ?? '';
        $stmt->close();
        return $client_code;
    }

    function getLastComplete($conn, $project_code) {
        $sql = "SELECT MAX(time_stamp) as last_complete FROM SurveyLog WHERE project_code = ? AND Status = 'Complete'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $project_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $last_complete = $result->fetch_assoc()['last_complete'] ?? '';
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

        // Parse the links JSON safely
        $links = json_decode($project['links'], true)[0] ?? ['live' => '', 'test' => ''];
        $live_link = $links['live'];
        $test_link = $links['test'];

        // Decode the filters JSON
        $filters = json_decode($project['filters'], true) ?? [];
    } else {
        die("Project code not provided.");
    }

    // Handle form submission for updating project details
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $conn = openConnection();

        $project_code = $_POST['project_code'];
        $project_name = $_POST['project_name'];
        $project_description = $_POST['project_description'];
        $client = $_POST['client'];
        $project_country = $_POST['project_country'];
        $LOI = $_POST['LOI'];
        $sample_size = $_POST['sample_size'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $IR = $_POST['IR'];
        $respondent_click_quota = $_POST['respondent_click_quota'];
        $CPI = $_POST['CPI'];
        $filters_json = json_encode($_POST['filters']);
        $links_json = json_encode([[
            'live' => $_POST['live_link'],
            'test' => $_POST['test_link']
        ]]);

        $sql = "UPDATE ProjectDetails SET project_name=?, project_description=?, client=?, project_country=?, LOI=?, sample_size=?, start_date=?, end_date=?, IR=?, respondent_click_quota=?, CPI=?, filters=?, links=? WHERE project_code=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssssissss", $project_name, $project_description, $client, $project_country, $LOI, $sample_size, $start_date, $end_date, $IR, $respondent_click_quota, $CPI, $filters_json, $links_json, $project_code);

        if ($stmt->execute()) {
            echo '<script>alert("Project updated successfully")</script>';
        } else {
            echo "<p class='text-red-500'>ERROR: Could not execute the query. " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
        closeConnection($conn);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../nav/css/style.css">
    <style>
        .details-grid { display: grid; grid-template-columns: 1fr 3fr; }
        .edit-button { position: absolute; top: 1rem; right: 1rem; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="wrapper d-flex align-items-stretch">
        <div class="main-content">  
            <div class="container mx-auto px-4 py-8">
                    <div class="relative ">
                        <div class="flex">
                        <h1 class="text-2xl font-bold ml-2 cursor-pointer bg-purple-500 text-white px-4 py-1 rounded-t-xl">Project Details</h1>
                        <a href="prescreen.php?project_code=<?= safeOutput($project_code) ?>" class="text-purple-500">
                            <h1 class="text-2xl font-bold ml-2 cursor-pointer px-4 py-1">PreScreen</h1>
                        </a>
                        <a href="project_mapping.php?project_code=<?= safeOutput($project_code) ?>" class="text-purple-500">
                            <h1 class="text-2xl font-bold ml-2 cursor-pointer px-4 py-1">Project Mapping</h1>
                        </a>
                    </div>
                    <div class="absolute top-0 right-3">
                        <button class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-t-xl" id="editButton" onclick="toggleEdit()">Edit</button>
                    </div>                
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
                                <div><input type="text" name="live_link" value="<?= safeOutput($live_link) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Test Link:</div>
                                <div><input type="text" name="test_link" value="<?= safeOutput($test_link) ?>" class="w-full border rounded p-2" readonly></div>
                            </div>
                        </div>
                        
                        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold mb-4">Survey Start Link</h2>
                            <div class="details-grid mb-4">
                                <div class="font-semibold">Start Survey:</div>
                                <div>
                                    <a href="start_survey.php?project_code=<?= safeOutput($project_code) ?>&status=start" target="_blank" class="text-purple-500 underline">Start Survey</a>
                                </div>
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
                                foreach ($filter_options as $key => $label)  {
                                
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
                            <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" id="saveButton" style="display:none;">Save</button>
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