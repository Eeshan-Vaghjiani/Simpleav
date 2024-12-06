<?php
include("connection.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['ID'])) {
    header('Location: login.php');
    exit;
    
}
$conn = new mysqli('localhost', 'root', '', 'simpleav');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ID = $_SESSION['ID'];
$stmt = $conn->prepare('SELECT * FROM users WHERE ID = ?');
$stmt->bind_param('i', $ID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close the database connection
$conn->close();
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
    <title>Manager Interface</title>
</head>
<body class="login">
    <nav class="navigation1">
        <div class="nav"><a href=""></a>
            <img src="logo2.png"><h2 class="simpleav">SimpLeav</h2>
        </div> 
        <ul>
            <li><a href="managerInterface.php"><b>Home</b></a></li>
            <li><a href=""><b>Leave Approval</b></a></li>
            <li><a href=""><b>Report and Analytics</b></a></li>
            <li><a href=""><b>Calendar</b></a></li>
            <li><a href=""><b>My Staff</b></a></li>
            <li><a href="Settings.php"><b>Settings</b></a>
            </li>
        </ul>
        
    </nav>
    <div class="profile">
        <div class="profile1">
            <img src="Jane Doe.jpg"><br><br>
            <p><strong>Employee ID:<?php echo $user['ID']; ?></strong></p>
                <p><strong>Name: <?php echo $user['Name']; ?></strong></p>
                <p><strong>Email: <?php echo $user['Email']; ?></strong></p>
                <p><strong>Role: <?php echo $user['Role']; ?></strong></p>
                <p><strong>Position: <?php echo $user['Position']; ?></strong></p>    
        </div>
        

    </div>
    
</body>
</html>