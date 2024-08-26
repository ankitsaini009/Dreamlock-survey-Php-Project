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

function logSurveyEnd($hash_identifier, $status)
{
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

function calculateLOI($start_date)
{
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

function getRedirectLink($status, $project_code, $supplier_code, $hashidentifier)
{
    $conn = openConnection();
    $status = strtoupper($status);  // Ensure status is uppercase

    $sql = "SELECT complete_url, terminate_url, quality_term_url, survey_close_url, over_quota_url 
            FROM SMapping 
            WHERE project_code = ? AND supplier_code = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $project_code, $supplier_code);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        die("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();

    if (!$supplier) {
        error_log("No matching record found in SMapping for project_code: '$project_code' and supplier_code: '$supplier_code'");
        return null;
    }


    $redirect_links = [
        'C' => $supplier['complete_url'],
        'T' => $supplier['terminate_url'],
        'F' => $supplier['quality_term_url'],
        'SC' => $supplier['survey_close_url'],
        'Q' => $supplier['over_quota_url']
    ];

    $redirect_url = $redirect_links[$status] ?? null;

    if ($redirect_url) {

        // Replace [identifier] with $hashidentifier in the specific redirect URL
        if (strpos($redirect_url, "&uid=[identifier]") !== false) {
            $redirect_url = str_replace("[identifier]", $hashidentifier, $redirect_url);
        }

        $update_sql = "UPDATE `surveylog` SET `Status` = ? WHERE `Hash_Identifier` = ?";
        $update_stmt = $conn->prepare($update_sql);
        if ($update_stmt === false) {
            error_log("Prepare failed for update: " . $conn->error);
            die("Prepare failed for update: " . $conn->error);
        }
        $update_stmt->bind_param("ss", $status, $hashidentifier);
        // echo '<pre>';
        // print_r($update_stmt);
        // die;
        if (!$update_stmt->execute()) {
            error_log("Execute failed for update: " . $update_stmt->error);
            die("Execute failed for update: " . $update_stmt->error);
        }
        $update_stmt->close();
    }

    closeConnection($conn);
    return $redirect_url;
}

function getMessage($status)
{
    $messages = [
        'C' => 'Thanks for participating! You have completed this survey successfully. Your participation status will be updated soon!',
        'T' => 'Thanks for participating! However, you are not eligible for this survey.',
        'F' => 'Thanks for participating! However, you are not eligible for this survey.',
        'SC' => 'Thanks for participating! However, the survey has been closed. We look forward to your participation in other surveys.',
        'Q' => 'Thanks for participating! We have got the required number of responses. We look forward to your participation in other surveys.',
    ];

    return $messages[$status] ?? 'Invalid status';
}

if (isset($_GET['hash_identifier']) && isset($_GET['status'])) {
    $hash_identifier = $_GET['hash_identifier'];
    $status = strtoupper($_GET['status']); // Ensure status is uppercase

    $conn = openConnection();
    $sql = "SELECT project_code, supplier_code, Hash_Identifier FROM SurveyLog WHERE Hash_Identifier = ?";
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
        $project_code = $surveyLog['project_code'];
        $supplier_code = $surveyLog['supplier_code'];
        $hashidentifier = $surveyLog['Hash_Identifier'];

        $redirect_url = getRedirectLink($status, $project_code, $supplier_code, $hashidentifier);
        $message = getMessage($status);
    } else {
        $redirect_url = null;
        $message = 'Invalid identifier';
    }

    closeConnection($conn);

    // Output the redirect URL to the page for JavaScript to use
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
              <script>
                  setTimeout(function() {
                      window.location.href = '$redirect_url';
                  }, 500); // Redirect after 5 seconds
              </script>";
    } else {
        echo "<p>No redirection URL found for the given status and supplier.</p>";
    }

    echo "</div></div>
    </body>
    </html>";
    exit();
} else {
    error_log("Invalid request: Missing hash_identifier or status.");
    die("Invalid request: Missing hash_identifier or status.");
}
