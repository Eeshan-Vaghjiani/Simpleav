<?php
// Include the database configuration file
include 'config.php';

// Fetch approved leave requests where approved_by is not null
$query = "
    SELECT leave_id, user_id, date_requested, leave_type, department, startdate, end_date, approved_by, approval_timestamp, comments
    FROM TBL_LEAVE
    WHERE approved_by IS NOT NULL
";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Requests</title>
    <style>
        body {
            background-image: url(background2.jpg);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-size: cover;
        }

    .nav{
    width: 100%;
    text-align: center;
    margin-bottom: 20px;
    }
    .nav img {
    width: 100px;
    height: 100px;
    size: 50px;
    background-color: transparent;
    margin-top: -500px;
    margin-bottom: 200px;
    margin-left: -20px;
        }
        .simpleav{
    text-decoration: none;
    color: rgba(50, 135, 214, 0.975);
    font-size: 36px;
    font-weight: 500;
    margin-top: -290px;
    margin-bottom: 200px;
    margin-left: 80px;
    height: 20px;

}
 .container {
            background-color: #fff;
            padding: 60px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .container h2{
            color: rgba(54, 152, 243, 0.975);
            text-align: center;
        }
        .container table thead tr th{
            color: rgba(54, 152, 243, 0.975);
        }
        table {
            width: 240px;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .back-btn {
            padding: 10px;
            color: white;
            background-color:rgba(54, 152, 243, 0.975) ;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            text-align: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <nav class="navigation">
        <div class="nav">
            <img src="logo2.png" alt="logo">
        </div>
    </nav>
    <div class="container">
        <h2>Approved Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Leave ID</th>
                    <th>User ID</th>
                    <th>Date Requested</th>
                    <th>Leave Type</th>
                    <th>Department</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Approved By</th>
                    <th>Approval Timestamp</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['leave_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_requested']); ?></td>
                            <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td><?php echo htmlspecialchars($row['startdate']); ?></td>
                            <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['approved_by']); ?></td>
                            <td><?php echo htmlspecialchars($row['approval_timestamp']); ?></td>
                            <td><?php echo htmlspecialchars($row['comments']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center;">No approved requests found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button class="back-btn" onclick="window.location.href='dashboard.html'">HOME PAGE</button>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
