<?php
session_start();
require_once 'emailService.php';
require_once 'connection.php';

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
if ($attempts >= 3) {
    $current_time = time();
    if ($current_time < $wait_time) {
        $remaining_time = $wait_time - $current_time;
        $error = "Too many failed attempts. Please wait " . ceil($remaining_time / 60) . " minute(s) before trying again.";
        $disabled = "disabled"; // Disable input fields
        $countdown = ceil($remaining_time); // Set countdown timer
    } else {
        // Reset attempts after waiting period
        $_SESSION['login_attempts'] = 0;
        $disabled = ""; // Enable input fields
        $countdown = 0; // Reset countdown
    }
} else {
    $disabled = ""; // Enable input fields
    $countdown = 0; // No countdown
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $attempts < 3) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Ensure $conn is defined and connected
    if ($conn) {
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
                    // Redirect after a short delay to allow the loading screen to be visible
                    echo '<script>
                            document.getElementById("loadingOverlay").style.display = "none"; // Hide loading overlay
                            window.location.href = "verify2fa.php"; // Redirect to verify 2FA
                          </script>';
                    exit();
                } else {
                    $error = "Failed to send verification code. Please try again.";
                }
            } else {
                $attempts++;
                $_SESSION['login_attempts'] = $attempts;

                // Set wait time based on attempts
                if ($attempts >= 3) {
                    // Set wait time: 30 seconds, 60 seconds, 120 seconds, etc.
                    $wait_time_seconds = 30 * pow(2, $attempts - 3); // 30, 60, 120, 240, ...
                    $_SESSION['wait_time'] = time() + $wait_time_seconds; // Set the wait time
                    $error = "Incorrect password. You have reached the maximum number of attempts. Please wait " . $wait_time_seconds . " seconds before trying again.";
                } else {
                    $error = "Incorrect password. Attempt " . $attempts . " of 3.";
                }
            }
        } else {
            $error = "No user found with that email.";
        }
    } else {
        $error = "Database connection failed.";
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
    <style>
        /* Loading overlay styles */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 1000;
            display: none; /* Hidden by default */
            justify-content: center;
            align-items: center;
        }
        .loader {
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        let countdown = <?php echo $countdown; ?>;
        let countdownTimer;

        function startCountdown() {
            if (countdown > 0) {
                document.getElementById("loginForm").style.display = "none"; // Hide the form
                document.getElementById("countdown").innerText = "Please wait " + countdown + " seconds before trying again.";
                countdownTimer = setInterval(function() {
                    countdown--;
                    document.getElementById("countdown").innerText = "Please wait " + countdown + " seconds before trying again.";
                    if (countdown <= 0) {
                        clearInterval(countdownTimer);
                        document.getElementById("loginForm").style.display = "block"; // Show the form
                        document.getElementById("countdown").innerText = "";
                    }
                }, 1000);
            }
        }

        window.onload = function() {
            if (countdown > 0) {
                startCountdown();
            }
        };

        function showLoading() {
            document.getElementById("loadingOverlay").style.display = "flex"; // Show loading overlay
        }
    </script>
</head>
<body class="login">
    <div id="loadingOverlay" style="display: none;">
        <div class="loader"></div>
        <p>Sending 2FA code, please wait...</p>
    </div>
    <nav class="navigation">
        <div class="nav">
            <img src="logo2.png" alt="logo">
            <h2 class="simpleav">SimpLeav</h2>
        </div>
    </nav>
    <div class="container">
        <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" <?php echo $disabled; ?> onsubmit="showLoading();">
            <h1>Login</h1>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="color: #721c24;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
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
        <div id="countdown" style="color: red; font-weight: bold;"></div>
    </div>
</body>
</html>

