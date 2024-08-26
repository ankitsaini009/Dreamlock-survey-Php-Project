<?php
function openConnection()
{
    $hostname = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'pmtool_db';
    $port = 3306;

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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_code = $_POST['project_code']; // Assuming this is passed in the form or URL
    $supplier_identifier = $_POST['supplier_identifier']; // Assuming this is passed in the form or URL
    //print_r($_POST);die();
    $conn = openConnection();

    // Prepare the statement to insert feedback
    $sql = "INSERT INTO feedback (project_code, supplier_identifier, question_id, answer) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Loop through all question IDs
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_id_') === 0) {
            $question_index = str_replace('question_id_', '', $key);
            $question_id = $value;
            $answer_key = 'question_' . $question_index;
            $answer = isset($_POST[$answer_key]) ? (is_array($_POST[$answer_key]) ? json_encode($_POST[$answer_key]) : $_POST[$answer_key]) : '';

            // Bind parameters and execute
            $stmt->bind_param("ssis", $project_code, $supplier_identifier, $question_id, $answer);
            $stmt->execute();
        }
    }

    $stmt->close();
    closeConnection($conn);

    echo "Feedback submitted successfully!";
}
if (isset($_GET['project_code']) && isset($_GET['supplier_identifier'])) {
    $project_code = $_GET['project_code'];
    $supplier_identifier = $_GET['supplier_identifier'];

    $conn = openConnection();

    $query = "SELECT sq.*, q.question_text, q.options
              FROM survey_questions sq
              JOIN questionnaire q ON sq.question_id = q.id
              WHERE sq.project_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $project_code);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['options'] = json_decode($row['options'], true); // Decode options from JSON
            $questions[] = $row;
        }
    } else {
        echo "No records found.";
    }
    $stmt->close();
    closeConnection($conn);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Satisfaction Survey form Wizard by Ansonika.">
    <meta name="author" content="Ansonika">
    <title>Satisfyc | Satisfaction Survey form Wizard</title>
    <!-- Favicons -->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/apple-touch-icon-144x144-precomposed.png">
    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Caveat|Poppins:300,400,500,600,700&display=swap" rel="stylesheet">
    <!-- BASE CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/vendors.css" rel="stylesheet">
    <!-- YOUR CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">

    <style>
        .step {
            display: none;
            /* Hide all steps initially */
        }

        .step.active {
            display: block;
            /* Show only the active step */
        }

        label {
            color: #2e2e2e !important;
            font-weight: 500 !important;
        }
    </style>
</head>

<body class="style_2">
    <!-- Header and other content omitted for brevity -->

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                    <div class="row no-gutters">
                        <!-- Left Side: Fullscreen Image -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <img src="../assets/images/survey-img.png" alt="Survey Image" class="img-fluid h-100 w-100" style="object-fit: cover;">
                        </div>

                        <!-- Right Side: Form -->
                        <div class="col-lg-6 col-md-12 bg-light">
                            <div class="card-body p-4 p-md-5">
                                <h3 class="card-title text-center mb-4">Feedback Form</h3>

                                <form id="wrapped" method="POST" autocomplete="off" action="../Controller/feedback_save.php">
                                    <input id="website" name="website" type="text" value="" style="display:none;">
                                    <input type="hidden" name="project_code" value="<?php echo htmlspecialchars($project_code); ?>">
                                    <input type="hidden" name="supplier_identifier" value="<?php echo htmlspecialchars($supplier_identifier); ?>">
                                    <input type="hidden" name="survey_start_time" id="survey_start_time">
                                    <input type="hidden" name="survey_end_time" id="survey_end_time">
                                    <input type="hidden" name="time_spent" id="time_spent">
                                    <input type="hidden" name="device_type" id="device_type">
                                    <input type="hidden" name="browser_details" id="browser_details">
                                    <input type="hidden" name="ip_address" id="ip_address">
                                    <input type="hidden" name="country" id="country">


                                    <div id="wizard_container">
                                        <div id="top-wizard" class="mb-4">
                                            <div id="progressbar" class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <div id="middle-wizard">
                                            <?php if (!empty($questions)) {
                                                foreach ($questions as $index => $row) { ?>
                                                    <div class="step">
                                                        <h5 class="mb-3"><strong><?php echo ($index + 1) . ' of ' . count($questions); ?></strong> <?php echo htmlspecialchars($row['question_text']); ?></h5>
                                                        <input type="hidden" name="question_id_<?php echo $index; ?>" value="<?php echo htmlspecialchars($row['question_id']); ?>">

                                                        <?php if ($row['control_type'] == 'Text') { ?>
                                                            <div class="form-group">
                                                                <input type="text" name="question_<?php echo $index; ?>" class="form-control form-control-lg" placeholder="Enter your answer" required>
                                                            </div>
                                                        <?php } elseif ($row['control_type'] == 'DropDown') { ?>
                                                            <div class="form-group">
                                                                <select name="question_<?php echo $index; ?>" class="form-control form-control-lg" required>
                                                                    <option value="" disabled selected>Select an option</option>
                                                                    <?php foreach ($row['options'] as $option) { ?>
                                                                        <option value="<?php echo htmlspecialchars($option); ?>"><?php echo htmlspecialchars($option); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        <?php } elseif ($row['control_type'] == 'Radio') { ?>
                                                            <div class="form-group">
                                                                <?php foreach ($row['options'] as $option) {
                                                                    $option_id = "radio_" . $index . "_" . htmlspecialchars($option); ?>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio" id="<?php echo $option_id; ?>" name="question_<?php echo $index; ?>" value="<?php echo htmlspecialchars($option); ?>" required>
                                                                        <label class="form-check-label" for="<?php echo $option_id; ?>"><?php echo htmlspecialchars($option); ?></label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } elseif ($row['control_type'] == 'Checkbox') { ?>
                                                            <div class="form-group">
                                                                <?php foreach ($row['options'] as $option) {
                                                                    $option_id = "checkbox_" . $index . "_" . htmlspecialchars($option); ?>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" id="<?php echo $option_id; ?>" name="question_<?php echo $index; ?>[]" value="<?php echo htmlspecialchars($option); ?>">
                                                                        <label class="form-check-label" for="<?php echo $option_id; ?>"><?php echo htmlspecialchars($option); ?></label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                            <?php }
                                            } ?>
                                        </div>

                                        <div id="bottom-wizard" class="d-flex justify-content-between mt-4">
                                            <button type="button" name="backward" class="btn btn-outline-secondary btn-lg backward">Prev</button>
                                            <button type="button" name="forward" class="btn btn-outline-primary btn-lg forward">Next</button>
                                            <button type="submit" name="process" class="btn btn-success btn-lg submit">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /container_centering -->

    <footer id="home" class="clearfix">
        <p>© 2023 Satisfyc</p>
    </footer>

    <!-- COMMON SCRIPTS -->
    <script src="js/jquery-3.5.1.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const steps = document.querySelectorAll(".step");
            const submitButton = document.querySelector("button[name='process']");
            const nextButton = document.querySelector("button[name='forward']");
            let currentStep = 0;

            function showStep(index) {
                steps.forEach((step, i) => {
                    step.classList.toggle("active", i === index);
                });
                updateButtons();
            }

            function validateStep(stepIndex) {
                const inputs = steps[stepIndex].querySelectorAll("input, select");
                let valid = true;
                inputs.forEach(input => {
                    if (!input.checkValidity() || input.value.trim() === "") {
                        input.classList.add("is-invalid");
                        valid = false;
                    } else {
                        input.classList.remove("is-invalid");
                    }
                });
                return valid;
            }

            function updateButtons() {
                if (currentStep === steps.length - 1) {
                    nextButton.style.display = 'none';
                    submitButton.style.display = 'block';
                } else {
                    nextButton.style.display = 'block';
                    submitButton.style.display = 'none';
                }
            }

            document.querySelector("button[name='forward']").addEventListener("click", function() {
                if (validateStep(currentStep)) {
                    if (currentStep < steps.length - 1) {
                        currentStep++;
                        showStep(currentStep);
                    }
                }
            });

            document.querySelector("button[name='backward']").addEventListener("click", function() {
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            steps.forEach((step, index) => {
                const inputs = step.querySelectorAll("input, select");
                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        input.classList.remove("is-invalid");
                        updateButtons();
                    });
                });
            });

            showStep(currentStep);
            updateButtons();
        });


        function getIST() {
            // IST offset (5 hours 30 minutes)
            const istOffset = 5 * 60 * 60 * 1000 + 30 * 60 * 1000;

            // Current time in UTC
            const utc = new Date().getTime();

            // IST time
            const ist = new Date(utc + istOffset);

            // Formatting the date-time in ISO format
            return ist.toISOString();
        }

        window.onload = function() {
            // Set survey start time when page loads
            document.getElementById('survey_start_time').value = getIST();

            document.getElementById('wrapped').onsubmit = function(event) {
                event.preventDefault(); // Prevent default form submission for debugging

                // Fetch the user's IP address
                fetch('https://api.ipify.org?format=json')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('ip_address').value = data.ip;

                        // Set other hidden fields
                        var endTime = getIST();
                        var startTime = document.getElementById('survey_start_time').value;
                        var timeSpent = (new Date(endTime) - new Date(startTime)) / 1000; // seconds

                        document.getElementById('survey_end_time').value = endTime;
                        document.getElementById('time_spent').value = timeSpent;

                        // Set Browser details
                        document.getElementById('browser_details').value = navigator.userAgent;

                        // Set Device type
                        var deviceType = /Mobile|Tablet/.test(navigator.userAgent) ? 'Mobile' : 'Desktop';
                        document.getElementById('device_type').value = deviceType;

                        // Set Country (example is hardcoded; you may use an API for real-time data)
                        document.getElementById('country').value = 'India';

                        // Debugging: Log form data
                        console.log('Form Data:', new FormData(document.getElementById('wrapped')));

                        // Submit the form
                        document.getElementById('wrapped').submit();
                    })
                    .catch(error => {
                        console.error('Error fetching IP address:', error);
                        // Handle error, you might want to submit the form without IP address
                        document.getElementById('wrapped').submit();
                    });
            };
        };
    </script>
</body>

</html>