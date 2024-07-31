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
  
        $tableCheckQuery = "SHOW TABLES LIKE 'survey_questions'";
        $result = $conn->query($tableCheckQuery);

        if ($result->num_rows == 0) {
  // if table not create in DB then first automatic create 
            $createTableQuery = "CREATE TABLE survey_questions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                question_id INT NOT NULL,
                control_type ENUM('Text', 'Radio', 'DropDown', 'Checkbox') NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (question_id) REFERENCES questionnaire(id) ON DELETE CASCADE
            )";

            if ($conn->query($createTableQuery) === TRUE) {
                $_SESSION['message'] = "Table 'survey_questions' created successfully.";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Error creating table: " . $conn->error;
                $_SESSION['message_type'] = 'error';
            }
        }


        if (!empty($question_id) && !empty($control_type)) {
    
            $stmt = $conn->prepare("INSERT INTO survey_questions (question_id, control_type) VALUES (?, ?)");
            $stmt->bind_param("is", $question_id, $control_type);


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

    // Close the database connection
    $conn->close();
}
