<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'pmtool_db';
$port = 3306;

// Create a connection
$conn = new mysqli($hostname, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $project_code = isset($_POST['project_code']) ? $conn->real_escape_string($_POST['project_code']) : '';
    $supplier_identifier = isset($_POST['supplier_identifier']) ? $conn->real_escape_string($_POST['supplier_identifier']) : '';
    // $project_code = htmlspecialchars($_POST['project_code']);
    // $supplier_identifier = htmlspecialchars($_POST['supplier_identifier']);
    $survey_start_time = htmlspecialchars($_POST['survey_start_time']);
    $survey_end_time = htmlspecialchars($_POST['survey_end_time']);
    $time_spent = htmlspecialchars($_POST['time_spent']);
    $device_type = htmlspecialchars($_POST['device_type']);
    $browser_details = htmlspecialchars($_POST['browser_details']);
    $ip_address = htmlspecialchars($_POST['ip_address']);
    $country = htmlspecialchars($_POST['country']);
    $status = 'C'; 
    $is_test_link = 0;
    $time_stamp = date('Y-m-d H:i:s'); 
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO surveylog (project_code, Supplier_Identifier, Survey_Start_Date, Survey_End_Date, LOI, IP_Address, Country, Browser_Detail, Device_Type, Status, Is_Test_Link, time_stamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $project_code, $supplier_identifier, $survey_start_time, $survey_end_time, $time_spent, $ip_address, $country, $browser_details, $device_type, $status, $is_test_link, $time_stamp);
    
    // Execute the query
    if ($stmt->execute()) {
        // echo "Record saved successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    // Fetch valid IDs based on question_ids
    $question_ids = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_id_') === 0) {
            $question_ids[] = $conn->real_escape_string($value);
        }
    }

    if (!empty($question_ids)) {
        $question_ids_str = implode(',', $question_ids);
        $check_query = "SELECT id, question_id FROM survey_questions WHERE question_id IN ($question_ids_str)";
        $result = $conn->query($check_query);

        // Prepare mapping from question_id to id
        $id_map = [];
        while ($row = $result->fetch_assoc()) {
            $id_map[$row['question_id']] = $row['id']; // Map question_id to its corresponding id
        }

        // Prepare the statement to insert feedback
        $sql = "INSERT INTO feedback (project_code, supplier_identifier, question_id, answer) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'question_id_') === 0) {
                    $question_index = str_replace('question_id_', '', $key);
                    $question_id = $value; // This is the question_id
                    $answer_key = 'question_' . $question_index;
                    $answer = isset($_POST[$answer_key]) ? (is_array($_POST[$answer_key]) ? json_encode($_POST[$answer_key]) : $_POST[$answer_key]) : '';

                    // Check if the question_id exists in the map and get the corresponding id
                    if (isset($id_map[$question_id])) {
                        $valid_question_id = $id_map[$question_id]; // Get the valid id for the feedback
                        // Bind parameters and execute
                        $stmt->bind_param("ssis", $project_code, $supplier_identifier, $valid_question_id, $answer);
                        $stmt->execute();
                    } else {
                        error_log("Invalid question_id: $question_id");
                    }
                }
            }
            $stmt->close();
        } else {
            error_log("Statement preparation failed: " . $conn->error);
            die("Statement preparation failed: " . $conn->error);
        }
    }

    $conn->close();
    header("Location: ../pages/start_survey.php?project_code=" . urlencode($project_code) . "&supplier_identifier=" . urlencode($supplier_identifier));
    exit();;
}
?>
