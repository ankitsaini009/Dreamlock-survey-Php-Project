<?php
// Database configuration local server settings 
    $hostname = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'pmtool_db';
    $port = 3306;

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//echo "Connected successfully"; // This is for testing; you might want to remove this line in production.

// You can close the connection when done, but usually, it's done at the end of the script.
// $conn->close();



////Live account connections 
// $db = array(
//     $username = 'pmtool_db';
//     $password = 'Admin@123[];';
//     $database = 'pmtool_db';
//     $port = 3306;               // Optional: The default MySQL port is 3306
//     // 'dbdriver' => 'mysqli',              // Database driver
//     // 'dbprefix' => '',                    // Table prefix
//     // 'pconnect' => FALSE,                 // Persistent connection
//     // 'db_debug' => TRUE,                  // Database debug mode (set FALSE in production)
//     // 'cache_on' => FALSE,                 // Enable/disable query caching
//     // 'cachedir' => '',                    // Cache directory
//     // 'char_set' => 'utf8',                // Character set
//     // 'dbcollat' => 'utf8_general_ci',     // Collation
// );

// Create connection using mysqli
// $conn = new mysqli(
//     $db['hostname'], 
//     $db['username'], 
//     $db['password'], 
//     $db['database'],
//     $db['port']
// );

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
?>
