<!-- project_details.php -->
<?php
include  '../include/header.php';
include  '../include/navbar.php';
include '../db2.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function loadEnv($file)
{
    if (file_exists($file)) {
        $lines = file($file);
        foreach ($lines as $line) {
            // Remove comments and whitespace
            $line = trim($line);
            if (empty($line) || $line[0] === '#') {
                continue;
            }
            // Split the line into key and value
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load the .env file
loadEnv(__DIR__ . '../../.env');

function openConnection()
{
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

function closeConnection($conn)
{
    $conn->close();
}

function safeOutput($string)
{
    return htmlspecialchars($string ?? '');
}

function getClientCode($conn, $client)
{
    $sql = "SELECT client_code FROM Clientele WHERE Client_Name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $client);
    $stmt->execute();
    $result = $stmt->get_result();
    $client_code = $result->fetch_assoc()['client_code'] ?? '';
    $stmt->close();
    return $client_code;
}

function getLastComplete($conn, $project_code)
{
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

function fetchSuppliers()
{
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

$suppliers = fetchSuppliers();

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
        echo "<p class='text-green-500'>Project updated successfully.</p>";
    } else {
        echo "<p class='text-red-500'>ERROR: Could not execute the query. " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
}
if (isset($_GET['project_code'])) {
    $project_code_query = $_GET['project_code'];

    $conn = openConnection();

    if ($conn === false) {
        die("Database connection failed.");
    }

    $sql_query = "SELECT filters FROM ProjectDetails WHERE project_code = ?";
    $stmt_query = $conn->prepare($sql_query);

    if ($stmt_query === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameter as string
    $stmt_query->bind_param("s", $project_code_query);
    $stmt_query->execute();
    $result_query = $stmt_query->get_result();

    if ($result_query === false) {
        die("Query failed: " . $stmt_query->error);
    }

    $project_data = $result_query->fetch_assoc();
    if ($project_data === null) {
        die("No project found.");
    }

    // Decode the filters JSON
    $filters_json = json_decode($project_data['filters'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $filters_json = [];
        echo "JSON decoding error: " . json_last_error_msg() . "<br>";
    }

    // Check if 'pre_screen' key is available
    // if (isset($filters_json['pre_screen'])) {
    //     echo "pre_screen is available. Value: " . htmlspecialchars($filters_json['pre_screen']);
    // } else {
    //     echo "pre_screen is not available.";
    // }

    $stmt_query->close();
    closeConnection($conn);
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../nav/css/style.css">
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

        .adjustWidth {
            width: 1425px;
        }

        .inputWidth {
            width: 400px
        }

        .table_wrapper {
            display: block;
            max-height: 80vh;
            overflow-x: auto;
            overflow-y: auto;
            white-space: nowrap;
        }

        /* .table_feild{"width:15px;font-size:12px;"} */
    </style>
</head>

<body class="bg-gray-100">
    <div class="wrapper d-flex align-items-stretch">
        <div class="main-content">
            <div class="container mx-auto px-4 py-8 adjustWidth">

                <div class="relative ">
                    <div class="flex">
                        <a href="project_details.php?project_code=<?= safeOutput($project_code) ?>" class="text-purple-500">
                            <h1 class="text-2xl font-bold ml-2 cursor-pointer px-4 py-1">Project Details</h1>
                        </a>
                        <?php
                        if (isset($filters['pre_screen']) && $filters['pre_screen'] == 1) {
                        ?>
                            <a href="prescreen.php?project_code=<?= safeOutput($project_code) ?>" class="text-purple-500">
                                <h1 class="text-2xl font-bold ml-2 cursor-pointer px-4 py-1 ">PreScreen</h1>
                            </a>
                        <?php
                        }
                        ?>
                        <h1 class="text-2xl font-bold ml-2 cursor-pointer bg-purple-500 text-white rounded-t-xl px-4 py-1">Project Mapping</h1>
                    </div>
                    <div class="absolute top-0 right-3 ">
                        <button class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-t-xl" id="redirectButton" onclick="redirectToProjectDetails()">Add</button>
                    </div>
                </div>
                <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                    <table class="table table-hover table_wrapper">

                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600">S.No.</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600">Supplier Name</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600">Live Link</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-gray-600">Test Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $base_url = getenv('PROJECT_URL');
                            foreach ($supplier_mappings as $index => $mapping) :
                            ?>
                                <tr>
                                    <td class="px-6 py-4 border-b border-gray-300"><?= $index + 1 ?></td>
                                    <td class="px-6 py-4 border-b border-gray-300"><?= safeOutput($mapping['supplier_name']) ?></td>
                                    <?php
                                    if (isset($filters['pre_screen']) && $filters['pre_screen'] == 1) {
                                    ?>
                                        <td class="px-6 py-4 border-b border-gray-300"><a target="_blank" href="<?= " demographics.php?project_code=" . safeOutput($project_code) . "&supplier_identifier=" . safeOutput($mapping['Supplier_Identifier']) ?>"><?= $base_url."demographics.php?project_code=". safeOutput($project_code) . "&supplier_identifier=" . safeOutput($mapping['Supplier_Identifier']) ?></a></td>
                                    <?php
                                    } else {
                                    ?>
                                        <td class="px-6 py-4 border-b border-gray-300"><a target="_blank" href="<?= " start_survey.php?project_code=" . safeOutput($project_code) . "&supplier_identifier=" . safeOutput($mapping['Supplier_Identifier']) ?>"><?= $base_url."start_survey.php?project_code=" . safeOutput($project_code) . "&supplier_identifier=" . safeOutput($mapping['Supplier_Identifier']) ?></a></td>
                                    <?php } ?>
                                    <td class="px-6 py-4 border-b border-gray-300"><?= $base_url . "?project_code=" . safeOutput($project_code) . "&supplier_identifier=" . safeOutput($mapping['Supplier_Identifier']) . "&status=test" ?></td>
                                </tr>
                            <?php
                            endforeach;
                            ?>


                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function redirectToProjectDetails() {
            var projectCode = "<?php echo htmlspecialchars($project_code); ?>"; // Use the PHP variable directly
            window.location.href = 'supplier_mapping.php?project_code=' + encodeURIComponent(projectCode);
        }

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