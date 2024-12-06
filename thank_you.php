<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thank You for Subscribing</title>
  <link rel="stylesheet" href="css/style.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Font Awesome for the arrow icon -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    .logo {
      width: 150px; /* Adjust the size of the logo */
      height: 150px; /* Ensure the height matches the width for a circle */
      object-fit: cover; /* Ensures the image covers the circle */
    }

    h1 {
      color: #ffffff; /* White color for the heading */
    }

    p {
      font-size: 18px;
      margin: 20px 0;
      color: #ADD8E6;
    }

    .btn {
      display: inline-flex;
      align-items: center;
    }

    .btn i {
      margin-left: 5px; /* Space between text and icon */
    }
  </style>
</head>
<body>
<div class="container d-flex flex-column justify-content-center align-items-center vh-100">
  <div class="text-center">
    <img src="img/logo2.jpg" alt="SimpleAV Logo" class="logo rounded-circle mb-4"> <!-- Update with the correct path to your logo -->
    <h1>Thank You for Subscribing!</h1>

    <?php
    // Retrieve the name from the URL
    if (isset($_GET['card-name'])) {
      $name = htmlspecialchars($_GET['card-name']); // Sanitize the input
      echo "<p>Thank you, <strong>$name</strong>! A subscription prompt has been sent to you. We appreciate your support!</p>";
    } else {
      echo "<p>A subscription prompt has been sent to you. We appreciate your support!</p>";
    }
    ?>

    <a href="index.php" class="btn btn-light text-primary mt-3">
      Go to Home <i class="fas fa-arrow-right"></i> <!-- Font Awesome arrow icon -->
    </a>
  </div>
</div>

<!-- Bootstrap JS and dependencies (jQuery and Popper.js) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
