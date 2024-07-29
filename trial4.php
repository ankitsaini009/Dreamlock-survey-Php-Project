<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'pmtool_db';

// Create connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for total projects completed in a single day
$day_sql = "SELECT DATE(time_stamp) AS day, COUNT(*) AS total_projects 
            FROM surveylog 
            WHERE status = 'COMPLETE' 
            GROUP BY day";
$day_result = $conn->query($day_sql);
$day_data = [];
while ($row = $day_result->fetch_assoc()) {
    $day_data[] = $row;
}

// Fetch data for total projects completed in a single week
$week_sql = "SELECT YEAR(time_stamp) AS year, WEEK(time_stamp) AS week, COUNT(*) AS total_projects 
             FROM surveylog 
             WHERE status = 'COMPLETE' 
             GROUP BY year, week";
$week_result = $conn->query($week_sql);
$week_data = [];
while ($row = $week_result->fetch_assoc()) {
    $week_data[] = $row;
}

// Fetch data for total projects completed in a single month
$month_sql = "SELECT YEAR(time_stamp) AS year, MONTH(time_stamp) AS month, COUNT(*) AS total_projects 
              FROM surveylog 
              WHERE status = 'COMPLETE' 
              GROUP BY year, month";
$month_result = $conn->query($month_sql);
$month_data = [];
while ($row = $month_result->fetch_assoc()) {
    $month_data[] = $row;
}

// Ensure all months are present
$all_months = [];
for ($i = 1; $i <= 12; $i++) {
    $all_months[sprintf("%02d", $i)] = 0;
}
foreach ($month_data as $data) {
    $all_months[sprintf("%02d", $data['month'])] = $data['total_projects'];
}
$month_data = [];
foreach ($all_months as $month => $count) {
    $month_data[] = [
        'month' => $month,
        'total_projects' => $count
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Completion Data</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 80%;
            margin: 0 auto;
        }
        .buttons {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="buttons">
        <button onclick="showChart('day')">Daily Data</button>
        <button onclick="showChart('week')">Weekly Data</button>
        <button onclick="showChart('month')">Monthly Data</button>
    </div>
    <div class="chart-container">
        <canvas id="projectChart"></canvas>
    </div>

    <script>
        const dayData = <?php echo json_encode($day_data); ?>;
        const weekData = <?php echo json_encode($week_data); ?>;
        const monthData = <?php echo json_encode($month_data); ?>;

        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        const ctx = document.getElementById('projectChart').getContext('2d');
        let projectChart;

        const createChart = (labels, data, label) => {
            if (projectChart) {
                projectChart.destroy();
            }
            projectChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        };

        const showChart = (type) => {
            let labels = [];
            let data = [];
            let chartLabel = '';

            if (type === 'day') {
                labels = dayData.map(item => item.day);
                data = dayData.map(item => item.total_projects);
                chartLabel = 'Daily Projects';
            } else if (type === 'week') {
                labels = weekData.map(item => `Year ${item.year} Week ${item.week}`);
                data = weekData.map(item => item.total_projects);
                chartLabel = 'Weekly Projects';
            } else if (type === 'month') {
                labels = monthData.map(item => monthNames[parseInt(item.month) - 1]);
                data = monthData.map(item => item.total_projects);
                chartLabel = 'Monthly Projects';
            }

            createChart(labels, data, chartLabel);
        };

        // Show daily data by default
        showChart('day');
    </script>
</body>
</html>
