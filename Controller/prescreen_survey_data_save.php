<?php
session_start();
include '../db2.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $question_id = isset($_POST['question_id']) ? htmlspecialchars($_POST['question_id']) : '';
    $control_type = isset($_POST['control_type']) ? htmlspecialchars($_POST['control_type']) : '';
    $project_code = isset($_POST['project_code']) ? htmlspecialchars($_POST['project_code']) : '';

    $errorMessage = "";
    if (empty($question_id)) {
        $errorMessage .= "Question ID is required.<br>";
    }
    if (empty($control_type)) {
        $errorMessage .= "Control Type is required.<br>";
    }
    if (!empty($errorMessage)) {

        $_SESSION['message'] = $errorMessage;
        $_SESSION['message_type'] = 'error';
    } else {

        if (!empty($question_id) && !empty($control_type)) {

            $stmt = $conn->prepare("INSERT INTO survey_questions (question_id, project_code, control_type) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $question_id, $project_code, $control_type);


            if ($stmt->execute()) {
                $_SESSION['message'] = "Survey question successfully added!";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['message_type'] = 'error';
            }


            $stmt->close();
        }
    }


    header("Location: ../pages/prescreen.php?project_code=" . urlencode($project_code));
    exit();
    $conn->close();


  
}



if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $project_code = htmlspecialchars($_GET['project_code']);
    $stmt = $conn->prepare("DELETE FROM survey_questions WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Survey question deleted successfully!";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        $_SESSION['message_type'] = 'error';
    }


 
    header("Location: ../pages/prescreen.php?project_code=" . urlencode($project_code));
    exit();
    $stmt->close();
} else {
    echo "Invalid ID";
}
