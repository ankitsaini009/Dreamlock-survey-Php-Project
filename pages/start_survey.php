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

function generateHashIdentifier()
{
    return bin2hex(random_bytes(16));
}

function logSurveyStart($project_code, $supplier_identifier, $supplier_code, $hash_identifier)
{
    $conn = openConnection();
    $sql = "INSERT INTO SurveyLog (project_code, status, time_stamp, Hash_Identifier, Supplier_Identifier, supplier_code) 
            VALUES (?, 'start', NOW(), ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $project_code, $hash_identifier, $supplier_identifier, $supplier_code);
    $stmt->execute();
    $stmt->close();
    closeConnection($conn);
}

if (isset($_GET['project_code']) && isset($_GET['supplier_identifier']) && isset($_GET['supplier_code'])) {
    $project_code = $_GET['project_code'];
    $supplier_identifier = $_GET['supplier_identifier'];
    $supplier_code = $_GET['supplier_code'];
    $hash_identifier = generateHashIdentifier();

    // Log the survey start with the unique hash identifier and supplier code
    logSurveyStart($project_code, $supplier_identifier, $supplier_code, $hash_identifier);

    $conn = openConnection();
    $sql = "SELECT links FROM ProjectDetails WHERE project_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $project_code);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
        $stmt->close();
        closeConnection($conn);

        // Decode links and get the client's survey URL
        $links = json_decode($project['links'], true);
        if (isset($links[0]['live'])) {
            $client_survey_url = $links[0]['live'];

            // Replace [identifier] with the unique hash identifier
            $client_survey_url = str_replace('[identifier]', $hash_identifier, $client_survey_url);

            // Append the supplier_code to the URL as a parameter
            $client_survey_url .= "&supplier_code=" . urlencode($supplier_code);

            // Redirect to the client's survey URL
            header("Location: $client_survey_url");
            exit();
        } else {
            die("Error: 'live' link not found in project details.");
        }
    } else {
        $stmt->close();
        closeConnection($conn);
        die("Error: Project code not found.");
    }
} else {
    die("Invalid request.");
}
