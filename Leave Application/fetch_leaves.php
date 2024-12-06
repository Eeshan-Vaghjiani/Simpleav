<?php
// fetch_leaves.php

// Start the session
session_start();

// Include the database connection
require_once '../DATABASE/db_connect.php';

/* 
    // The following section is commented out because login/registration is handled by another team.
    // Once available, you can uncomment this section to ensure only authenticated users can access the data.

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Return an error response
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
*/

// For now, we'll simulate a logged-in user.
// Remove or comment out the following lines once login is integrated.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Example user_id (ensure this exists in tbl_user)
}

// Retrieve user_id from session
$user_id = $_SESSION['user_id'];

// Prepare and execute the query
$stmt = $conn->prepare("SELECT leave_id, leave_type, startdate, end_date, reason, date_requested, current_status, comments FROM tbl_leave WHERE user_id = ? ORDER BY date_requested DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$leave_applications = [];

while ($row = $result->fetch_assoc()) {
    // Map status code to text
    switch ($row['current_status']) {
        case 0:
            $row['status_text'] = 'Pending';
            break;
        case 1:
            $row['status_text'] = 'Approved';
            break;
        case 2:
            $row['status_text'] = 'Rejected';
            break;
        default:
            $row['status_text'] = 'Unknown';
    }

    $leave_applications[] = $row;
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($leave_applications);

// Close the statement and connection
$stmt->close();
$conn->close();
?>
