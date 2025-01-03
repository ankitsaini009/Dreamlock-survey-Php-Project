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
        .adjustWidth {width:1425px;}
        .inputWidth {width:400px}
        .table_wrapper{display: block;max-height: 80vh; overflow-x: auto;overflow-y: auto;white-space: nowrap;}
        .table_feild{"width:15px;font-size:12px;"}
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
                        <a href="prescreen.php?project_code=<?= safeOutput($project_code) ?>" class="text-purple-500">
                            <h1 class="text-2xl font-bold ml-2 cursor-pointer px-4 py-1 ">PreScreen</h1>
                        </a>
                        <h1 class="text-2xl font-bold ml-2 cursor-pointer bg-purple-500 text-white rounded-t-xl px-4 py-1">Project Mapping</h1>
                    </div>
                    <div class="absolute top-0 right-3 ">
                    <a href="project_mapping_add_supplier.php?project_code=<?= safeOutput($project_code) ?>" >
                        <button class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-t-xl" id="editButton" >Add</button>   </a>
                    </div>                
                </div> 
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title font-bold text-2xl">Add Supplier</h4>
                    <form  action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="forms-sample">
                        <div class="flex form-group">
                            <div  class="mr-4" style="width:600px;">
                                <label for="client">Supplier</label>
                                <label for="currency">Supplier</label>
                                    <select name="currency" id="currency" required class="form-select" id="currency" required class="form-control" >
                                            <option value="">Select Supplier</option><?php
                                            foreach ($suppliers as $supplier):
                                        
                                            echo '<option value="USD">'. $supplier['contact_number']. ' - '. $supplier['supplier_name']. '</option>';
                                        endforeach;?>
                                        
                                    </select>
                            </div>
                            <div  class="ml-4" style="width:600px;">
                                <label for="project_manager">Supplier Quota</label>
                                <input type="number" name="project_manager" id="project_manager" required class="form-control" >
                            </div>
                        </div>

                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                            <label for="project_name">Click Quota </label>
                            <input type="number" name="project_name" id="project_name" required class="form-control" >
                            </div>
                            <div class="mr-4" style="width:600px;">
                            <label for="project_name">CPI</label>
                            <input type="number" name="project_name" id="project_name" required class="form-control" >
                            </div>
                           
                        </div>

                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                            <label for="LOI">Supplier Dynamic Redirection Link
                            Complete</label>
                            <input  type="url" name="LOI" id="LOI" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                            <label for="respondent_click_quota">Supplier Dynamic Redirection Link</label>
                            <input  type="url" name="respondent_click_quota" id="respondent_click_quota" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                            <label for="IR">Terminate</label>
                            <input  type="url" name="IR" id="IR" required class="form-control" >
                            </div>
                        </div>

                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                            <label for="sample_size">Over Quota</label>
                            <input  type="url" name="sample_size" id="sample_size" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                            <label for="CPI">Quality Term</label>
                            <input  type="url" name="CPI" id="CPI" required class="form-control" >
                            </div>
                            <div class="ml-4" style="width:600px;">
                            <label for="CPI">Survey Close</label>
                            <input  type="url" name="CPI" id="CPI" required class="form-control" >
                            </div>
                        </div>

                       
                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                            <label for="project_name">Post Back Url</label>
                            <input type="url" name="project_name" id="project_name" required class="form-control" >
                            </div>
                            <div class="mr-4" style="width:600px;">
                            <label for="project_name">Post Back Active Url</label>
                            <input type="url" name="project_name" id="project_name" required class="form-control" >
                            </div>
                           
                        </div>
                        <div>
                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <button class="btn btn-light">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
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
                saveButtonyle.display = 'inline-block';
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

