<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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
    switch ($status) {
        case 'C':
            return 'https://pmt.dreamlockmr.com/complete';
        case 'T':
            return 'https://pmt.dreamlockmr.com/terminate';
        case 'QT':
            return 'https://pmt.dreamlockmr.com/quality_term';
        case 'Q':
            return 'https://pmt.dreamlockmr.com/over_quota';
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

if (isset($_GET['hash_identifier']) && isset($_GET['status'])) {
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

    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Survey End</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                color: #333;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                text-align: center;
            }
            .container {
                background-color: #fff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            h1 {
                color: #0073e6;
            }
            p {
                font-size: 18px;
                margin: 10px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>$message</h1>";
    
    if ($redirect_url) {
        echo "<p>Redirecting...</p>
            <meta http-equiv='refresh' content='5;url=$redirect_url'>";
    }
    
    echo "</div>
    </body>
    </html>";
    exit();
} else {
    error_log("Invalid request: Missing hash_identifier or status.");
    die("Invalid request: Missing hash_identifier or status.");
}
?>
