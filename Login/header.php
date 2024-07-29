<?php
session_start();

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: Login/login.php");
        exit();
    }
}

function getRole() {
    return $_SESSION['role'] ?? null;
}

checkLogin();
$role = getRole();
?>
