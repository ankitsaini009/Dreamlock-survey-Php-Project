<?php
// Redirect to login/login.php when accessing https://tool.dreamlockmr.com

// Define the URL of login/login.php
$login_url = "login.php";

// Perform the redirection
header("Location: $login_url");
exit;
?>
