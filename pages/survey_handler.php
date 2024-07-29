<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function openConnection() {
    $hostname = 'localhost';
    $username = 'pmtool_db';
    $password = 'Admin@123[];';
    $database = 'pmtool_db';
    $port = 3306;

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

function generateHashIdentifier() {
    return bin2hex(random_bytes(16));
}

function logSurveyStart($project_code, $supplier_identifier, $hash_identifier) {
    $conn = openConnection();
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $country = ''; // Retrieve country based on IP or other methods
    $browser_detail = $_SERVER['HTTP_USER_AGENT'];
    $device_type = ''; // Determine device type based on user agent
    $is_test_link = 0; // Set based on your logic

    $sql = "INSERT INTO SurveyLog (project_code, Supplier_Identifier, Hash_Identifier, Status, Survey_Start_Date, IP_Address, Country, Browser_Detail, Device_Type, Is_Test_Link, time_stamp)
            VALUES (?, ?, ?, 'start', NOW(), ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssssssss", $project_code, $supplier_identifier, $hash_identifier, $ip_address, $country, $browser_detail, $device_type, $is_test_link);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
    closeConnection($conn);
}

function logSurveyEnd($hash_identifier, $status) {
    $conn = openConnection();
    $sql = "UPDATE SurveyLog SET Status = ?, Survey_End_Date = NOW(), time_stamp = NOW() WHERE Hash_Identifier = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $status, $hash_identifier);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
    closeConnection($conn);
}

function calculateLOI($start_date) {
    if ($start_date) {
        $start_time = strtotime($start_date);
        $end_time = time();
        $duration = $end_time - $start_time;

        $minutes = floor($duration / 60);
        $seconds = $duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
    return '00:00';
}

function getRedirectLink($status, $supplier_id) {
    // Implement logic to get redirect URL based on status and supplier_id
    // Placeholder implementation
    switch ($status) {
        case 'C':
            return 'https://dreamlockmr.com/app.pmt/pages/complete';
        case 'T':
            return 'https://dreamlockmr.com/app.pmt/pages/terminate';
        case 'QT':
            return 'https://dreamlockmr.com/app.pmt/pages/quality_term';
        case 'Q':
            return 'https://dreamlockmr.com/app.pmt/pages/over_quota';
        default:
            return null;
    }
}

function getMessage($status) {
    $messages = [
        'null' => 'The username is not valid.',
        'RT' => 'The user does not exist.',
        'QC' => 'Duplicate user.',
        'OC' => 'The project is on hold, please try again later.',
        'OE' => 'The project is on hold, please try again later.',
        'QA' => 'Thanks for participating! However, you are not eligible for this survey.',
        'QE' => 'Thanks for participating! However, you are not eligible for this survey.',
        'PR' => 'Thanks for participating! However, you are not eligible for this survey.',
        'QD' => 'Thanks for participating! However, you are not eligible for this survey.',
        'SF' => 'Thanks for participating! However, you are not eligible for this survey.',
        'OB' => 'Thanks for participating! We have got the required number of responses. We look forward to your participation in other surveys.',
        'OH' => 'The survey is paused, please try again later.',
        'SC' => 'Thanks for participating! However, the survey has been closed. We look forward to your participation in other surveys.',
        'QP' => 'Thanks for participating! However, you are not eligible for this survey.',
        'OD' => 'Thanks for participating! We have got the required number of responses. We look forward to your participation in other surveys.',
        'FU' => 'Thanks for participating! However, you are not eligible for this survey.',
        'D' => 'You are qualified to participate in the survey. Please wait while the main survey loads.',
        'C' => 'Thanks for participating! You have completed this survey successfully. Your participation status will be updated soon!',
        'T' => 'Thanks for participating! However, you are not eligible for this survey.',
        'Q' => 'Thanks for participating! We have got the required number of responses. We look forward to your participation in other surveys.',
        'F' => 'Thanks for participating! However, you are not eligible for this survey.',
        'R' => 'Valid user rejected by the client, manual update in the database.',
    ];

    return $messages[$status] ?? 'Invalid status';
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'start' && isset($_GET['project_code']) && isset($_GET['supplier_identifier'])) {
        $project_code = $_GET['project_code'];
        $supplier_identifier = $_GET['supplier_identifier'];
        $hash_identifier = generateHashIdentifier();

        $conn = openConnection();
        $sql = "SELECT links FROM ProjectDetails WHERE project_code = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $project_code);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            die("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $project = $result->fetch_assoc();
            $stmt->close();
            closeConnection($conn);

            // Log the survey start with the unique hash identifier
            logSurveyStart($project_code, $supplier_identifier, $hash_identifier);

            // Decode links and get the client's survey URL
            $links = json_decode($project['links'], true);
            if (isset($links[0]['live'])) {
                $client_survey_url = $links[0]['live'];

                // Replace [identifier] with the unique hash identifier
                $client_survey_url = str_replace('[identifier]', $hash_identifier, $client_survey_url);

                // Redirect to the client's survey URL
                header("Location: $client_survey_url");
                exit();
            } else {
                error_log("Error: 'live' link not found in project details.");
                die("Error: 'live' link not found in project details.");
            }
        } else {
            $stmt->close();
            closeConnection($conn);
            error_log("Error: Project code not found.");
            die("Error: Project code not found.");
        }
    } elseif ($action == 'end' && isset($_GET['hash_identifier']) && isset($_GET['status'])) {
        $hash_identifier = $_GET['hash_identifier'];
        $status = $_GET['status'];

        $conn = openConnection();
        $sql = "SELECT * FROM SurveyLog WHERE Hash_Identifier = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $hash_identifier);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            die("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $surveyLog = $result->fetch_assoc();
        $stmt->close();

        if ($surveyLog) {
            $survey_start_date = $surveyLog['Survey_Start_Date'];
            $loi = calculateLOI($survey_start_date);

            // Update survey end details
            $sql = "UPDATE SurveyLog SET Status = ?, Survey_End_Date = NOW(), LOI = ?, time_stamp = NOW() WHERE Hash_Identifier = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                error_log("Prepare failed: " . $conn->error);
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("sss", $status, $loi, $hash_identifier);
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                die("Execute failed: " . $stmt->error);
            }
            $stmt->close();

            // Get the redirection link based on the status
            $redirect_url = getRedirectLink($status, $surveyLog['Supplier_Id']);
            $message = getMessage($status);
        } else {
            $redirect_url = null;
            $message = 'Invalid identifier';
        }

        closeConnection($conn);

        echo "<h1>$message</h1>";
        echo "<p>Status: $status</p>";
        echo "<p>Hash Identifier: $hash_identifier</p>";

        if ($redirect_url) {
            echo "<p>Redirecting...</p>";
            header("Refresh: 5; URL=$redirect_url");
            exit();
        } else {
            echo "<p>No redirection will occur.</p>";
        }
    } else {
        error_log("Invalid request: Missing parameters.");
        die("Invalid request: Missing parameters.");
    }
} else {
    error_log("Invalid request: Missing action.");
    die("Invalid request: Missing action.");
}
?>
