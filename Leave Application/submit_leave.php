<?php
// submit_leave.php

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database connection
require_once '../DATABASE/db_connect.php';

// Include the email service
require_once '../User Interface/emailService.php';

// For now, we'll simulate a logged-in user.
// Remove the following lines once login is integrated.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Example user_id (ensure this exists in tbl_user)
    $_SESSION['username'] = "John Doe"; // Example username
    $_SESSION['department'] = "Engineering"; // Example department
}

// Initialize variables
$error = "";
$success = "";

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $leave_type = trim($_POST['leave_type']);
    $startdate = $_POST['startdate'];
    $end_date = $_POST['end_date'];
    $reason = trim($_POST['reason']);
    $user_id = intval($_SESSION['user_id']);

    // Basic validation
    if (empty($leave_type) || empty($startdate) || empty($end_date) || empty($reason)) {
        $error = "All fields are required.";
    } elseif ($startdate > $end_date) {
        $error = "Start date cannot be after end date.";
    } else {
        // Calculate the number of leave days (inclusive)
        $startdate_obj = DateTime::createFromFormat('Y-m-d', $startdate);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $end_date);
        $interval = $startdate_obj->diff($end_date_obj);
        $leave_days = $interval->days + 1; // +1 to include both start and end dates

        // Optional: Check if leave dates overlap with existing approved leaves for the employee
        $check_stmt = $conn->prepare("SELECT * FROM tbl_leave WHERE user_id = ? AND current_status = 1 AND (
            (startdate <= ? AND end_date >= ?) OR
            (startdate <= ? AND end_date >= ?) OR
            (startdate >= ? AND end_date <= ?)
        )");
        $check_stmt->bind_param("issssss", $user_id, $startdate, $startdate, $end_date, $end_date, $startdate, $end_date);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "You have overlapping approved leave dates.";
        } else {
            // Proceed to insert
        }

        $check_stmt->close();

        if (empty($error)) {
            // Assume maximum leave days per type (for days_remaining calculation)
            $max_allowed_days = [
                "Sick Leave" => 14,
                "Casual Leave" => 10,
                "Annual Leave" => 30,
                "Maternity Leave" => 90,
                "Paternity Leave" => 15,
                "Bereavement Leave" => 7
            ];

            // Check if the requested days exceed the maximum allowed for the leave type
            if ($leave_days > $max_allowed_days[$leave_type]) {
                $error = "You have requested $leave_days days for $leave_type, which exceeds the maximum allowed of " . $max_allowed_days[$leave_type] . " days.";
            }

            if (empty($error)) {
                // Proceed with the database insertion if validation passes
                $stmt = $conn->prepare("INSERT INTO tbl_leave (user_id, date_requested, leave_type, department, used_leave, days_remaining, current_status, startdate, end_date, comments)
                VALUES (?, NOW(), ?, ?, ?, ?, 'Pending', ?, ?, ?)");
                if ($stmt === false) {
                    $error = "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                } else {
                    $department = $_SESSION['department']; // Use 'department' from session
                    $used_leave = $leave_days; // Number of days requested
                    $days_remaining = $max_allowed_days[$leave_type] - $used_leave; // Calculate remaining days

                    $stmt->bind_param('issiisss', $user_id, $leave_type, $department, $used_leave, $days_remaining, $startdate, $end_date, $reason);
                    $stmt->execute();

                    $success = "Leave application submitted successfully.";
                }
                $stmt->close();
            }
        }
    }

    // After successful submission, prepare leave details for email
    $start = new DateTime($_POST['startdate']);
    $end = new DateTime($_POST['end_date']);
    $duration = $end->diff($start)->days + 1;

    $leaveDetails = [
        'employee_name' => $_POST['employee_name'],
        'department' => $_POST['department'],
        'leave_type' => $_POST['leave_type'],
        'start_date' => $start->format('d M Y'),
        'end_date' => $end->format('d M Y'),
        'duration' => $duration,
        'reason' => $_POST['reason']
    ];

    // Get user's email from database
    $stmt = $conn->prepare("SELECT email FROM tbl_user WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Send confirmation email
        $emailService = new EmailService();
        $emailService->sendLeaveApplicationConfirmation($user['email'], $leaveDetails);
    }

    // Continue with your existing redirect...
    header('Location: user_portal.php?status=success');
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Application Submission</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="navbar">
            <div class="logo">SIMPLEAV</div>
            <nav>
                <ul>
                    <li><a href="user_portal.php">Home</a></li>
                    <li><a href="#">Leave Requests</a></li>
                    <li><a href="#">Help</a></li>
                </ul>
            </nav>
            <div class="profile">
                <img src="img/avatar.png" alt="Profile Picture" class="profile-img">
            </div>
        </div>
    </header>

    <div class="form-container">
        <h1>Leave Application Submission</h1>

        <?php if ($error): ?>
            <div class="error">
                <p><?php echo htmlspecialchars($error); ?></p>
                <a href="leave_application.php">Go Back</a>
            </div>
        <?php elseif ($success): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($success); ?></p>
                <a href="user_portal.php">Return to Home</a>
            </div>
        <?php else: ?>
            <div class="error">
                <p>Invalid access.</p>
                <a href="leave_application.php">Go to Leave Application Form</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
