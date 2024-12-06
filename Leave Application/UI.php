<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "simpleav");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the username from the session
$username = $_SESSION['username'];

// Fetch user_id and gender from TBL_USER based on username
$sql = "SELECT user_id, gender FROM TBL_USER WHERE username = '$username'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
    $gender = $row['gender'];
} else {
    die("User not found or invalid username.");
}

// Leave types for male or female
$leaveTypes = ["Annual", "Casual", "Sick", "Compassionate"];
if ($gender === 'male') {
    $leaveTypes[] = "Paternity";
} else {
    $leaveTypes[] = "Maternity";
}

// Max leave days for each leave type
$maxLeaveDays = [
    "Annual" => 30,
    "Casual" => 10,
    "Sick" => 10,
    "Compassionate" => 5,
    "Paternity" => 10,
    "Maternity" => 90,  // Only applies to females
];

// Fetch leave data based on leave types and user_id
$leaveData = [];
foreach ($leaveTypes as $type) {
    $sql = "SELECT SUM(days_remaining) AS remaining FROM TBL_LEAVE WHERE leave_type = '$type' AND user_id = '$user_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $leaveData[$type] = isset($row['remaining']) ? $row['remaining'] : 0;  // Default to 0 if no data
}

// Debugging echo for leave data and max leave days
echo "<pre>";
echo "User Leave Details for User ID $user_id (Gender: $gender):\n";
print_r($leaveData);
echo "Max Leave Days: ";
print_r($maxLeaveDays);
echo "</pre>";

// Fetch monthly leave data for Team Leave Track chart
$monthlyLeaves = [];
for ($i = 1; $i <= 12; $i++) {
    $sql = "SELECT COUNT(*) AS total FROM TBL_LEAVE WHERE MONTH(startdate) = $i AND user_id = '$user_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $monthlyLeaves[] = isset($row['total']) ? $row['total'] : 0;  // Default to 0 if no data
}

// Output the data as JSON for use in JavaScript
echo "<script>
        var leaveData = " . json_encode($leaveData) . ";
        var monthlyLeaves = " . json_encode($monthlyLeaves) . ";
        var maxLeaveDays = " . json_encode($maxLeaveDays) . ";
      </script>";

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Leave Dashboard</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .leave-charts {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 10px;
            margin-top: 20px;
        }
        .chart-container {
            width: 150px;
            height: 150px;
            position: relative;
        }
        .chart-container canvas {
            width: 100% !important;
            height: 100% !important;
        }
        .team-leave-track-container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
        }
    </style>
</head>

<body class="body2">
    <div class="nav">
        <img src="logo2.png" alt="logo">
        <h2 class="simpleav">SimpLeav</h2>
    </div> 
    <div class="container">
        
        <aside class="sidebar">
            <ul>
                <div class="color">
                    <li><a href="#" >Dashboard</a></li>
                    <li><a href="#">Leave Requests</a></li>
                    <li><a href="#">Analytics</a></li>
                    <li><a href="#">Budgets</a></li>
                    <li><a href="#">Settings</a></li>
                </div>
            </ul>
        </aside>

        <main class="main-content">
            <header>
                <nav>
                    <ul>
                        <li><a href="#">HOME</a></li>
                        <li><a href="#">ABOUT US</a></li>
                    </ul>
                </nav>
            </header>

            <section class="leave-dashboard">
                <h2>Leave Availability</h2>
                <div class="leave-charts">
                    <div class="chart-container"><canvas id="AnnualChart"></canvas></div>
                    <div class="chart-container"><canvas id="CasualChart"></canvas></div>
                    <div class="chart-container"><canvas id="SickChart"></canvas></div>
                    <?php if ($gender === 'male'): ?>
                        <div class="chart-container"><canvas id="PaternityChart"></canvas></div>
                    <?php else: ?>
                        <div class="chart-container"><canvas id="MaternityChart"></canvas></div>
                    <?php endif; ?>
                    <div class="chart-container"><canvas id="CompassionateChart"></canvas></div>
                </div>

                <h2>Team Leave Track</h2>
                <div class="team-leave-track-container">
                    <canvas id="teamLeaveChart"></canvas>
                </div>
            </section>

        </main>
    </div>

    <script>
        // Pie Charts Configuration
        function createChart(elementId, title, remaining, maxLeave) {
            const ctx = document.getElementById(elementId).getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Used', 'Remaining'],
                    datasets: [{
                        data: [maxLeave - remaining, remaining],  // Used = maxLeave - remaining
                        backgroundColor: ['#FF6384', '#36A2EB'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: title
                        }
                    }
                }
            });
        }

        // Create charts for each leave type
        createChart('AnnualChart', 'Annual Leave', leaveData.Annual, maxLeaveDays.Annual);
        createChart('CasualChart', 'Casual Leave', leaveData.Casual, maxLeaveDays.Casual);
        createChart('SickChart', 'Sick Leave', leaveData.Sick, maxLeaveDays.Sick);

        if (leaveData.Paternity !== undefined) {
            createChart('PaternityChart', 'Paternity Leave', leaveData.Paternity, maxLeaveDays.Paternity);
        } else if (leaveData.Maternity !== undefined) {
            createChart('MaternityChart', 'Maternity Leave', leaveData.Maternity, maxLeaveDays.Maternity);
        }

        createChart('CompassionateChart', 'Compassionate Leave', leaveData.Compassionate, maxLeaveDays.Compassionate);

        // Team Leave Track Bar Chart
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        const teamLeaveCtx = document.getElementById('teamLeaveChart').getContext('2d');
        new Chart(teamLeaveCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Leaves',
                    data: monthlyLeaves,
                    backgroundColor: 'rgba(54, 162, 235, 0.85)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Team Leave Track'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
