<?php
// Start session to access session variables
session_start();

// Include the database configuration file
include '..\DATABASE\db_connect.php';

// Check if user ID is set in session, or redirect if not logged in
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ..\user login\login.php');
//     exit;
// }

$user_id = $_SESSION['user_id'];

// Fetch leave requests for the logged-in user (assuming this is a manager view)
$query = "
    SELECT
        leave_id,
        user_id,
        department,
        startdate,
        end_date,
        leave_type,
        current_status,
        comments
    FROM
        TBL_LEAVE
    WHERE
        current_status = 0 AND approved_by IS NULL
";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$leave_requests = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="requests.css" rel="stylesheet">
    <title>Pending Requests</title>
    <style>
        /* Your existing CSS */
        .back-btn {
            display: block; /* Center the button */
            margin: 200px auto; /* Adjust margin to lower the button further */
        }
    </style>
</head>
<body>
    <nav class="navigation">
        <div class="nav">
            <img src="logo2.png" alt="logo">
            <h2 class="simpleav">SimpLeav</h2>
        </div>
    </nav>
    <div class="container">
        <h2>Pending Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Department</th>
                    <th>Leave Dates</th>
                    <th>Leave Type</th>
                    <th>Actions</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leave_requests as $request): ?>
                    <tr id="request-<?= $request['leave_id']; ?>">
                        <td><?= htmlspecialchars($request['user_id']); ?></td>
                        <td><?= htmlspecialchars($request['department']); ?></td>
                        <td><?= htmlspecialchars($request['startdate']) . ' - ' . htmlspecialchars($request['end_date']); ?></td>
                        <td><?= htmlspecialchars($request['leave_type']); ?></td>
                        <td>
                            <button class="btn btn-decline" onclick="updateRequest(<?= $request['leave_id']; ?>, 'decline')">Decline</button>
                            <button class="btn btn-approve" onclick="updateRequest(<?= $request['leave_id']; ?>, 'approve')">Approve</button>
                        </td>
                        <td>
                            <textarea class="comment-input" id="comment-<?= $request['leave_id']; ?>" placeholder="Add comment"><?= htmlspecialchars($request['comments']); ?></textarea>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="back-btn" onclick="window.location.href='dashboard.html'">HOME PAGE</button>
    </div>

    <script>
        function updateRequest(leaveId, action) {
            const comment = document.getElementById(`comment-${leaveId}`).value;

            const formData = new FormData();
            formData.append('leave_id', leaveId);
            formData.append('user_id', <?= json_encode($user_id); ?>);
            formData.append('action', action);
            formData.append('comment', comment);

            fetch('leave_manager.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(`Request ${action}d successfully!`);
                    document.getElementById(`request-${leaveId}`).style.display = 'none';
                } else {
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update request.');
            });
        }
    </script>
</body>
</html>
