<?php
include  '../include/header.php';
include  '../include/navbar.php';
include '../db2.php';
?>

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title font-bold text-2xl">Add New Project</h4>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="forms-sample">
                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="client">Client</label>
                                <input type="text" name="client" id="client" required class="form-control">
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="project_manager">Project Manager:</label>
                                <input type="text" name="project_manager" id="project_manager" required class="form-control">
                            </div>
                        </div>

                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="project_name">Project Name:</label>
                                <input type="text" name="project_name" id="project_name" required class="form-control">
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="exampleSelectGender">Country:</label>
                                <select id="project_country" name="project_country" class="form-select" id="exampleSelectGender" required>
                                    <option value="">Select a Country</option>
                                    <option>Multi Country</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="LOI">Length of Interview (Minutes):</label>
                                <input type="number" name="LOI" id="LOI" required class="form-control">
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="IR">Incidence Rate (%):</label>
                                <input type="number" name="IR" id="IR" required class="form-control">
                            </div>
                        </div>

                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="sample_size">Sample Size:</label>
                                <input type="number" name="sample_size" id="sample_size" required class="form-control">
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="CPI">Cost Per Interview (CPI):</label>
                                <input type="number" name="CPI" id="CPI" required class="form-control">
                            </div>
                        </div>

                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="currency">Currency:</label>
                                <select name="currency" id="currency" required class="form-select" id="currency" required class="form-control">
                                    <option value="">Select Currency</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">Euro</option>
                                    <option value="GBP">British Pound</option>
                                </select>
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="respondent_click_quota">Quota:</label>
                                <input type="number" name="respondent_click_quota" id="respondent_click_quota" required class="form-control">
                            </div>
                        </div>

                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="project_link_type">Project Link Type:</label>
                                <select name="project_link_type" id="project_link_type" class="form-select" onchange="toggleLinkFields()">
                                    <option value="Single">Select Link Type</option>
                                    <option value="Single">Single Link</option>
                                    <option value="Multi">Multi Link</option>
                                </select>
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="project_description">Project Description:</label>
                                <textarea name="project_description" id="project_description" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="flex form-group">
                            <div id="link_fields" style="display: none;">
                                <div class="mr-4" style="width:600px;">
                                    <label for="test_link" class="block text-gray-700 text-sm font-bold mb-2">Test Link:</label>
                                    <input type="url" name="test_link" id="test_link" class="form-control">
                                    <label for="live_link" class="block text-gray-700 text-sm font-bold mb-2">Live Link:</label>
                                    <input type="url" name="live_link" id="live_link" class="form-control">
                                </div>
                                <div class="mr-4" style="width:600px;" style="display: none;">
                                    <button type="button" class="btn btn-gradient-primary mt-2" onclick="addLink()">Add More Links</button>
                                </div>
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="status">Status:</label>
                                <select name="status" id="status" required class="form-select">
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="InActive">InActive</option>
                                    <option value="Archived">Archived </option>
                                    <option value="Invoiced">Invoiced</option>
                                    <option value="Closed">Closed</option>
                                    <option value="Archived">Archived </option>
                                    <!-- Add other currencies as needed -->
                                </select>
                            </div>
                        </div>

                        <div class="flex form-group">
                            <div class="mr-4" style="width:600px;">
                                <label for="start_date">Start Date:</label>
                                <input type="date" name="start_date" id="start_date" required class="form-control">
                            </div>
                            <div class="ml-4" style="width:600px;">
                                <label for="end_date">End Date:</label>
                                <input type="date" name="end_date" id="end_date" required class="form-control">
                            </div>
                        </div>

                        <div>
                            <label for="filters" class="block text-gray-700 text-sm font-bold mb-2">Project Filters:</label>
                        </div>
                        <div class="flex justify-between form-group mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="mb-4"><input type="checkbox" name="geo_location" value="1"> Geo Location</label><br>
                                    <label class="mb-4"><input type="checkbox" name="tsign" value="1"> TSign</label><br>
                                    <label class="mb-4"><input type="checkbox" name="captcha" value="1"> Captcha</label><br>
                                    <label class="mb-4"><input type="checkbox" name="pre_screen" value="1"> PreScreen</label>
                                    <!-- Second Column -->
                                    <!-- Unique IP and Speeder with Input Fields -->
                                    <div class="col-md-8">
                                        <div class="mb-2">
                                            <label><input type="checkbox" name="unique_ip" value="1" id="unique_ip_checkbox">Unique IP</label>
                                            <input type="number" name="unique_ip_count" id="unique_ip_count" placeholder="IP Count" class=" shadow appearance-none border rounded py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" disabled>
                                        </div>
                                        <br>
                                        <div class="mb-4">
                                            <label><input type="checkbox" name="speeder" value="1" id="speeder_checkbox"> Speeder</label>
                                            <input type="number" name="speeder_threshold" id="speeder_threshold" placeholder="Speed Threshold" class=" shadow appearance-none border rounded py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" disabled>
                                        </div>
                                    </div>
                                </div>
                                <!-- <br> -->
                            </div>
                            <!-- Third Column -->
                            <div class="col-md-6">
                                <label class="mb-4"><input type="checkbox" name="exclude" value="1"> Exclude</label><br>
                                <label class="mb-4"><input type="checkbox" name="proxy_vpn" value="1"> Proxy/VPN</label><br>
                                <label class="mb-4"><input type="checkbox" name="url_protection" value="1"> Url Protection</label><br>
                                <label class="mb-4"><input type="checkbox" name="dynamic_thanks_url" value="1"> Dynamic Thanks Url</label><br>
                                <label class="mb-4"><input type="checkbox" name="mobile_study" value="1"> Mobile Study</label><br>
                                <label class="mb-4"><input type="checkbox" name="tablet_study" value="1"> Tablet Study</label><br>
                                <label class="mb-4"><input type="checkbox" name="desktop_study" value="1"> Desktop Study</label>
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

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../db2.php';  // Ensure database connection is configured correctly

    // Function to generate the next project code
    function generateProjectCode($conn)
    {
        $prefix = "DLM060";
        $query = "SELECT project_code FROM ProjectDetails ORDER BY project_id DESC LIMIT 1";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $last_code = $row['project_code'];
            $numeric_part = intval(substr($last_code, 6)) + 1;
            return $prefix . str_pad($numeric_part, 1, "0", STR_PAD_LEFT);
        } else {
            return $prefix . '0';  // Start from 'DLM0600000' if no projects are present
        }
    }

    // Generate the new project code
    $project_code = generateProjectCode($conn);

    // Escape user inputs for security
    $client = mysqli_real_escape_string($conn, $_POST['client']);
    $project_manager = mysqli_real_escape_string($conn, $_POST['project_manager']);
    $project_name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $project_country = mysqli_real_escape_string($conn, $_POST['project_country']);
    $LOI = mysqli_real_escape_string($conn, $_POST['LOI']);
    $IR = mysqli_real_escape_string($conn, $_POST['IR']);
    $sample_size = mysqli_real_escape_string($conn, $_POST['sample_size']);
    $CPI = mysqli_real_escape_string($conn, $_POST['CPI']);
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $project_link_type = mysqli_real_escape_string($conn, $_POST['project_link_type']);
    $project_description = mysqli_real_escape_string($conn, $_POST['project_description']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $respondent_click_quota = mysqli_real_escape_string($conn, $_POST['respondent_click_quota']);

    // Handle link inputs
    $links = [];

    if ($project_link_type == 'Single') {
        $links[] = [
            'test' => mysqli_real_escape_string($conn, $_POST['test_link']),
            'live' => mysqli_real_escape_string($conn, $_POST['live_link'])
        ];
    } else if ($project_link_type == 'Multi') {
        foreach ($_POST['multi_test_link'] as $index => $testLink) {
            $testLink = mysqli_real_escape_string($conn, $testLink);
            $liveLink = mysqli_real_escape_string($conn, $_POST['multi_live_link'][$index]);
            $links[] = ['test' => $testLink, 'live' => $liveLink];
        }
    }
    $linksJson = mysqli_real_escape_string($conn, json_encode($links));


    // Handle additional filters and checkbox values
    $filters = [
        'geo_location' => isset($_POST['geo_location']) ? 1 : 0,
        'tsign' => isset($_POST['tsign']) ? 1 : 0,
        'captcha' => isset($_POST['captcha']) ? 1 : 0,
        'pre_screen' => isset($_POST['pre_screen']) ? 1 : 0,
        'exclude' => isset($_POST['exclude']) ? 1 : 0,
        'proxy_vpn' => isset($_POST['proxy_vpn']) ? 1 : 0,
        'url_protection' => isset($_POST['url_protection']) ? 1 : 0,
        'dynamic_thanks_url' => isset($_POST['dynamic_thanks_url']) ? 1 : 0,
        'mobile_study' => isset($_POST['mobile_study']) ? 1 : 0,
        'tablet_study' => isset($_POST['tablet_study']) ? 1 : 0,
        'desktop_study' => isset($_POST['desktop_study']) ? 1 : 0,
        'unique_ip_count' => isset($_POST['unique_ip']) ? (int) $_POST['unique_ip_count'] : NULL,
        'speeder_threshold' => isset($_POST['speeder']) ? (int) $_POST['speeder_threshold'] : NULL
    ];

    $filtersJson = mysqli_real_escape_string($conn, json_encode($filters));

    // Insert project details along with filters
    $sql = "INSERT INTO ProjectDetails (project_code, client, project_manager, project_name, project_country, LOI, IR, sample_size, CPI, currency, project_link_type, project_description, status, start_date, end_date, respondent_click_quota, filters, links) VALUES ('$project_code', '$client', '$project_manager', '$project_name', '$project_country', '$LOI', '$IR', '$sample_size', '$CPI', '$currency', '$project_link_type', '$project_description','$status', '$start_date', '$end_date', '$respondent_click_quota', '$filtersJson','$linksJson')";


    if (mysqli_query($conn, $sql)) {
        //echo "<p class='text-green-500'>Records added successfully with Project Code: $project_code.</p>";
        //echo "<script>alert('Records added successfully with Project Code: $project_code.');</script>";
        ///header( "Location: pages/project_details.php" );
        echo "<p class='text-green-500'>Add Project saved successfully.</p>";
        echo "<script type='text/javascript'>
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 3000); // Redirect after 1 second
          </script>";
        exit;
    } else {
        //echo "<p class='text-red-500'> " . mysqli_error($conn) . "</p>";
        //echo "<script>alert('ERROR: Could not execute $sql. " . mysqli_error($conn) .');</script>";
        echo "<script>alert('ERROR: Could not execute $sql.');</script>";
    }

    // Close connection
    mysqli_close($conn);
}
?>

<script>
    function toggleLinkFields() {
        var linkType = document.getElementById('project_link_type').value;
        var linkFields = document.getElementById('link_fields');
        var singleLinkField = document.querySelector('.single_link_field');
        var multiLinkField = document.querySelector('.multi_link_field');

        if (linkType === 'Single') {
            linkFields.style.display = 'block';
            singleLinkField.style.display = 'block';
            multiLinkField.style.display = 'none';
        } else if (linkType === 'Multi') {
            linkFields.style.display = 'block';
            singleLinkField.style.display = 'block';
            multiLinkField.style.display = 'block';
        } else {
            linkFields.style.display = 'none';
        }
    }

    function addLink() {
        var newLink = document.createElement('div');
        newLink.innerHTML = `<label for="multi_test_link" class="block text-gray-700 text-sm font-bold mb-2">Test Link:</label>
            <input type="url" name="multi_test_link[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <label for="multi_live_link" class="block text-gray-700 text-sm font-bold mb-2">Live Link:</label>
            <input type="url" name="multi_live_link[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">`;
        document.querySelector('.multi_link_field').appendChild(newLink);
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const uniqueIPCheckbox = document.getElementById('unique_ip_checkbox');
        const uniqueIPCount = document.getElementById('unique_ip_count');
        const speederCheckbox = document.getElementById('speeder_checkbox');
        const speederThreshold = document.getElementById('speeder_threshold');

        uniqueIPCheckbox.addEventListener('change', function() {
            uniqueIPCount.disabled = !this.checked;
            if (!this.checked) uniqueIPCount.value = ''; // Clear input if disabled
        });

        speederCheckbox.addEventListener('change', function() {
            speederThreshold.disabled = !this.checked;
            if (!this.checked) speederThreshold.value = ''; // Clear input if disabled
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
        $(document).ready(function() {
            $.getJSON('https://restcountries.com/v3.1/all', function(data) {
                // Sort the countries alphabetically by name
                data.sort((a, b) => {
                    let nameA = a.name.common.toUpperCase(); // ignore upper and lowercase
                    let nameB = b.name.common.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) {
                        return -1;
                    }
                    if (nameA > nameB) {
                        return 1;
                    }
                    return 0;
                });

                // Create the options
                var options = '';
                data.forEach(function(country) {
                    options += `<option value="${country.cca2}">${country.name.common}</option>`;
                });

                // Append the options to the dropdown
                $('#project_country').append(options);
            });
        });
    </script>

<?php include '../include/footer.php'; ?>