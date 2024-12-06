<?php
include("connection.php"); // Ensure this file establishes a connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) { // Make sure this matches your session variable
    header('Location: login.php');
    exit;
}

// Use the existing connection from connection.php
$ID = $_SESSION['user_id']; // Assuming you set this in the login script

// Prepare the statement to fetch user details
$stmt = $conn->prepare('SELECT * FROM tbl_user WHERE user_id = ?'); // Check your actual table name
$stmt->bind_param('i', $ID);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user is found
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Close the statement (optional, but good practice)
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Staff Interface">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="shortcut icon" href="logo2.jpg" type="image/x-icon">
    <title>Staff Interface</title>
</head>
<body class="login">
    <nav class="navigation1">
        <div class="nav">
            <img src="logo2.png" alt="logo">
            <h2 class="simpleav">SimpLeav</h2>
        </div> 
        <ul>
            <li><a href="staffInterface.php"><b>Home</b></a></li>
            <li><a href="applyLeave.php"><b>Apply for Leave</b></a></li>
            <li><a href="leaveStatus.php"><b>Leave Status</b></a></li>
            <li><a href="profile.php"><b>My Profile</b></a></li>
            <li><a href="settings.php"><b>Settings</b></a></li>
            <li><a href="logout.php"><b>Logout</b></a></li>
        </ul>
    </nav>
    <div class="profile">
        <div class="profile1">
            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Picture"><br><br>
            <p><strong>Employee ID: <?php echo htmlspecialchars($user['user_id']); ?></strong></p>
            <p><strong>Name: <?php echo htmlspecialchars($user['username']); ?></strong></p> <!-- Changed from 'Name' to 'username' -->
            <p><strong>Email: <?php echo htmlspecialchars($user['email']); ?></strong></p> <!-- Changed from 'Email' to 'email' -->
            <p><strong>Role: <?php echo htmlspecialchars($user['role']); ?></strong></p> <!-- Changed from 'Role' to 'role' -->
            <p><strong>Position: <?php echo htmlspecialchars($user['position']); ?></strong></p> <!-- Changed from 'Position' to 'position' -->   
        </div>
    </div>
</body>
</html>
