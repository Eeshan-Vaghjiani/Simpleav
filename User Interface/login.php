<?php
session_start();
require_once 'emailService.php';

// Set session timeout duration
$timeout_duration = 900; // 15 minutes

// Check if the user is inactive for more than the timeout duration
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset(); // Unset session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

// Initialize EmailService
$emailService = new EmailService();
$error = "";
$attempts = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] : 0;
$wait_time = isset($_SESSION['wait_time']) ? $_SESSION['wait_time'] : 0;

// Check if user is locked out
if ($attempts >= 4) {
    $current_time = time();
    if ($current_time < $wait_time) {
        $error = "Too many failed attempts. Please wait before trying again.";
    } else {
        // Reset attempts after waiting period
        $_SESSION['login_attempts'] = 0;
    }
}

if (isset($_POST['submit']) && $attempts < 4) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $query = "SELECT * FROM tbl_user WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            // Reset attempts on successful login
            $_SESSION['login_attempts'] = 0;

            // Store user type for later redirect
            $_SESSION['is_manager'] = $user['is_manager'];

            // Generate 2FA code
            $code = sprintf("%06d", random_int(0, 999999));
            $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Store code in database
            $query = "UPDATE tbl_user SET two_factor_code = ?, two_factor_expires = ? WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $code, $expires, $user['user_id']);

            if ($stmt->execute() && $emailService->send2FACode($email, $code)) {
                $_SESSION['temp_email'] = $email;
                header("Location: verify2fa.php");
                exit();
            } else {
                $error = "Failed to send verification code. Please try again.";
            }
        } else {
            $attempts++;
            $_SESSION['login_attempts'] = $attempts;

            // Set wait time based on attempts
            $_SESSION['wait_time'] = time() + (30 * pow(2, $attempts - 1)); // Exponential backoff
            $error = "Incorrect password. Attempt $attempts of 4.";
        }
    } else {
        $error = "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo2.jpg" type="image/x-icon">
    <title>Login</title>
</head>
<body class="login">
    <nav class="navigation">
        <div class="nav">
            <img src="logo2.png" alt="logo">
            <h2 class="simpleav">SimpLeav</h2>
        </div>
    </nav>
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <h1>Login</h1>
            <div class="input">
                 <input style="color: black;" type="email" class="form-control" placeholder="Email Address" name="email" required>
                <i style="color: black;" class="fa fa-envelope"></i>
            </div>
            <div class="input">
                <input style="color: black;" type="password" class="form-control" placeholder="Password" name="password" required>
                <i style="color: black;" class="fa fa-lock"></i>
            </div>
            <div class="remember-me">
                <b><a href="#">Forgot Password?</a></b>
              </div>
            <button type="submit" name="submit" class="btn">Sign In</button>
        </form>
    </div>
</body>
</html>

