<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        button { padding: 5px 10px; margin-right: 5px; cursor: pointer; }
    </style>
   
</head>
<body class="bg-gray-100">
    
        <?php
        include('db2.php'); // Include the db2.php file which already has $conn defined

        $sql = "SELECT * FROM ProjectDetails";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo '<table class="min-w-full bg-white">';
            echo '<thead><tr><th>Project Name</th><th>Project Code</th><th>Client Name</th><th>Country</th><th>Manager</th><th>PreScreen</th><th>Complete</th><th>Terminate</th><th>QF</th><th>Drop</th><th>F.LOI</th><th>Avg.IR</th><th>CNV</th><th>Drop%</th><th>Last Complete</th><th>Remaining Completes</th><th>CPI</th><th>Action</th></tr></thead>';
            echo '<tbody>';
            while($row = $result->fetch_assoc()) {
                $filters = $row['filters'];
                $prescreen = 'No';

                // Check if filters contain "PreScreen"
                if (strpos($filters, 'PreScreen') !== false) {
                    $prescreen = 'Yes';
                }

                // Get completes, terminates, and QF for the current project
                $project_code = $row['project_code'];
                $completes_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";
                $terminates_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Terminate'";
                $qf_sql = "SELECT COUNT(*) as count FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'QF'";
                $last_complete_sql = "SELECT MAX(time_stamp) as last_complete FROM SurveyLog WHERE project_code = '$project_code' AND Status = 'Complete'";

                $completes_result = $conn->query($completes_sql);
                $terminates_result = $conn->query($terminates_sql);
                $qf_result = $conn->query($qf_sql);
                $last_complete_result = $conn->query($last_complete_sql);

                $completes = $completes_result->fetch_assoc()['count'];
                $terminates = $terminates_result->fetch_assoc()['count'];
                $qf = $qf_result->fetch_assoc()['count'];
                $last_complete = $last_complete_result->fetch_assoc()['last_complete'];

                // Calculate IR (Incidence Rate) and CNV (Conversion Rate)
                $total_hits = $completes + $terminates + $qf;
                $ir = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;
                $cnv = $total_hits > 0 ? ($completes / $total_hits) * 100 : 0;

                // Drop% placeholder (assuming you have logic to calculate these)
                $drop_percentage = 0; // Placeholder for Drop%

                echo '<tr>';
                echo '<td>' . htmlspecialchars($row["project_name"]) . '</td>';
                echo '<td><a href="pages/project_details.php?project_code=' . htmlspecialchars($project_code) . '" class="text-blue-500 hover:underline">' . htmlspecialchars($project_code) . '</a></td>';
                echo '<td>' . htmlspecialchars($row["client"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["project_country"]) . '</td>';
                echo '<td>' . htmlspecialchars($row["project_manager"]) . '</td>';
                echo '<td>' . htmlspecialchars($prescreen) . '</td>';
                echo '<td>' . htmlspecialchars($completes) . '</td>';
                echo '<td>' . htmlspecialchars($terminates) . '</td>';
                echo '<td>' . htmlspecialchars($qf) . '</td>';
                echo '<td>' . htmlspecialchars($drop_percentage) . '</td>';
                echo '<td>' . htmlspecialchars($row["LOI"]) . '</td>';
                echo '<td>' . htmlspecialchars(number_format($ir, 2) . '%') . '</td>';
                echo '<td>' . htmlspecialchars(number_format($cnv, 2) . '%') . '</td>';
                echo '<td>' . htmlspecialchars($drop_percentage) . '</td>';
                echo '<td>' . htmlspecialchars($last_complete) . '</td>';
                echo '<td>' . htmlspecialchars($row["sample_size"] - $completes) . '</td>';
                echo '<td>' . htmlspecialchars($row["CPI"]) . '</td>';
                echo '<td> <div class="button-container">
        <button class="btn edit-btn">Edit</button>
        <button class="btn delete-btn">Delete</button>
    </div></td>';
                echo '</tr>';
               
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo "<p class='text-center text-red-500'>No results found.</p>";
        }
        $conn->close();
        ?>
   
    <script>
    function toggleStatus() {
        // Function to toggle the status of a project
    }
    function editProject() {
        // Function to edit project details
    }
    </script>
</body>
</html>
