<?php
include("config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: rgba(50, 135, 214, 0.975);
            background-image: url(background2.jpg);
            background-size: cover;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .container {
            max-width: 800px;
            width: 100%;
            max-height: 80vh;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
            color: rgba(50, 135, 214, 0.975);
            font-size: 24px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .leave-item {
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 18px;
            line-height: 1.6;
        }

        .leave-item:last-child {
            border-bottom: none;
        }

        .leave-item strong {
            font-weight: bolder;
            color: rgba(50, 135, 214, 0.975);
        }

        .leave-item p {
            margin: 5px 0;
            color: rgba(50, 135, 214, 0.975);
        }

        .no-records {
            text-align: center;
            color: rgba(50, 135, 214, 0.975);
            font-size: 18px;
            padding: 20px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Leave Details</h3>

    <?php
    $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

    $query = "SELECT leave_type, date_requested, startdate, end_date, comments FROM tbl_leave WHERE user_id = $user_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='leave-item'>";
            echo "<p><strong>Type:</strong> " . $row['leave_type'] . "</p>";
            echo "<p><strong>Requested On:</strong> " . $row['date_requested'] . "</p>";
            echo "<p><strong>Start Date:</strong> " . $row['startdate'] . "</p>";
            echo "<p><strong>End Date:</strong> " . $row['end_date'] . "</p>";
            if (!empty($row['comments'])) {
                echo "<p><strong>Comments:</strong> " . $row['comments'] . "</p>";
            }
            echo "</div>";
        }
    } else {
        echo "<p class='no-records'>No leave records found.</p>";
    }

    $conn->close();
    ?>
</div>

</body>
</html>
