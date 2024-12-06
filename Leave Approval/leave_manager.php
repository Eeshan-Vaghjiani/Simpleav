<?php
// Include the database configuration file
include 'config.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the action (approve or decline) from the request
    $action = $_POST['action'];

    // Ensure required data is present
    if (!isset($_POST['leave_id']) || !isset($_POST['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        exit;
    }

    $leave_id = $_POST['leave_id'];
    $manager_id = $_POST['user_id']; // ID of the manager approving or declining the request
    $comment = isset($_POST['comment']) ? $_POST['comment'] : null;

    if ($action === 'approve') {
        // Approve action: Update the leave record with approval details
        $stmt = $conn->prepare("
            UPDATE TBL_LEAVE
            SET current_status = 1,
                comments = ?,
                approved_by = ?,
                approval_timestamp = NOW()
            WHERE leave_id = ?
        ");
        $stmt->bind_param("sii", $comment, $manager_id, $leave_id);
    } elseif ($action === 'decline') {
        // Decline action: Delete the leave record from the database
        $stmt = $conn->prepare("DELETE FROM TBL_LEAVE WHERE leave_id = ?");
        $stmt->bind_param("i", $leave_id);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }

    if ($stmt->execute()) {
        // If the query was successful, respond with a success message
        echo json_encode([
            'status' => 'success',
            'message' => $action === 'approve' ? 'Leave approved' : 'Leave declined'
        ]);
    } else {
        // If there was an error with the query execution, respond with an error message
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update leave status'
        ]);
    }

    // Close the statement and the database connection
    $stmt->close();
    $conn->close();
} else {
    // If the request method is not POST, respond with an error
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
