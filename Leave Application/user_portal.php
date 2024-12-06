<?php
// user_portal.php

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database connection
require_once '../DATABASE/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Fetch user details from the session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'N/A';
$department = $_SESSION['department'] ?? 'N/A';


// Fetch additional user details from the database
$stmt_user = $conn->prepare("SELECT email, company, role, position, profile_image FROM tbl_user WHERE user_id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$stmt_user->bind_result($email, $company, $role, $position, $profile_image);
$stmt_user->fetch();
$stmt_user->close();

// Fetch leave applications
$leave_applications = array();
$stmt_applications = $conn->prepare("SELECT leave_id, leave_type, startdate, end_date, current_status, comments, approval_timestamp FROM tbl_leave WHERE user_id = ? ORDER BY date_requested DESC");
$stmt_applications->bind_param("i", $user_id);
$stmt_applications->execute();
$result_applications = $stmt_applications->get_result();

while ($row = $result_applications->fetch_assoc()) {
    $leave_applications[] = $row;
}

$stmt_applications->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimpLeav Employee Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional styling for portal layout */
        .portal-container {
            display: flex;
            flex-wrap: wrap;
            margin: 20px;
        }

        .user-details {
            flex: 1;
            min-width: 200px;
            margin-right: 20px;
            background-color: transparent;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid rgb(255, 255,255, .2);
            backdrop-filter: blur(15px);
            box-shadow: 0 0 10px rgb(0,0,0, .2);
            color: white;
        }

        .leave-table-container {
            width: 100%;
            margin-top: 30px;
        }
        .leave-table-container h3{
            color: white;
            text-align: center;
        }

        .leave-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leave-table th,
        .leave-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .leave-table th {
            background-color: rgba(50, 135, 214, 0.975);
            color: white;
        }

        .leave-table tr:nth-child(even) {
            background-color: #fffff1;
        }
        .leave-table tr:nth-child(odd) {
            background-color: #fffff9;
        }

        .leave-table tr:hover {
            background-color: #fffff3;
        }

        .nav-buttons {
            margin-top: 20px;
            text-align: center;
        }

        .nav-buttons button {
            padding: 10px 20px;
            margin: 5px;
            background-color: rgba(50, 135, 214, 0.975);
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .nav-buttons button:hover {
            background-color: rgba(50, 135, 214, 0.975);
        }

        @media (max-width: 768px) {
            .portal-container {
                flex-direction: column;
            }

            .user-details {
                margin-right: 0;
                margin-bottom: 20px;
                width: 100%;
            }
        }
    </style>
</head>
<body class="body1">
    <nav class="navigation1">
        <div class="nav">
            <img src="logo2.png" alt="logo">
            <h2 class="simpleav">SimpLeav</h2>
        </div>
                <ul>
                    <li><a href="UI.php">Home</a></li>
                    <li><a href="../User Interface/logout.php">Logout</a></li>
                </ul>
      </nav>
            <div class="profile">
                <img src="<?php echo htmlspecialchars($profile_image ?? '../img/Avatar.png'); ?>" alt="Profile Picture" class="profile-img">
            </div>
        </div>

    <div class="portal-container">
        <div class="user-details">
            <h1><?php echo htmlspecialchars($username ?? ''); ?></h1>
            <h2><strong>Email:</strong> <?php echo htmlspecialchars($email ?? ''); ?></h2>
            <h2><strong>Company:</strong> <?php echo htmlspecialchars($company ?? ''); ?></h2>
            <h2><strong>Department:</strong> <?php echo htmlspecialchars($department ?? ''); ?></h2>
            <h2><strong>Role:</strong> <?php echo htmlspecialchars($role ?? ''); ?></h2>
            <h2><strong>Position:</strong> <?php echo htmlspecialchars($position ?? ''); ?></h2>
        </div>
    </div>

    <div class="nav-buttons">
        <button onclick="window.location.href='leave_application.php'">Apply for Leave</button>
        <button onclick="window.location.href='user_portal.php'">Refresh</button>
    </div>

    <div class="leave-table-container">
        <h3 style="margin-left: 40px;">Past Leave Applications</h3>
        <table class="leave-table">
            <thead>
                <tr>
                    <th>Leave ID</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Approval Date</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($leave_applications)): ?>
                    <?php foreach ($leave_applications as $application): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($application['leave_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($application['leave_type'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(date("d-m-Y", strtotime($application['startdate']))); ?></td>
                            <td><?php echo htmlspecialchars(date("d-m-Y", strtotime($application['end_date']))); ?></td>
                            <td>
                                <?php
                                    $status = $application['current_status'];
                                    echo $status == 0 ? 'Pending' : ($status == 1 ? 'Approved' : 'Rejected');
                                ?>
                            </td>
                            <td>
    <?php
        if ($application['approval_timestamp']) {
            echo htmlspecialchars(date("d-m-Y H:i", strtotime($application['approval_timestamp'])));
        } else {
            echo "N/A"; // Display "N/A" if approval_timestamp is null
        }
    ?>
</td>
                            <td><?php echo htmlspecialchars($application['comments'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">No past leave applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
