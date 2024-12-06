<?php
session_start();
include("connection.php");
require_once 'emailService.php';

// Redirect to login if no email in session
if (!isset($_SESSION['temp_email'])) {
    header("Location: login.php");
    exit();
}

$emailService = new EmailService();
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = trim($_POST['2fa_code']);
    $email = $_SESSION['temp_email'];

    $query = "SELECT * FROM tbl_user WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($user['two_factor_code'] === $code) {
            if (strtotime($user['two_factor_expires']) > time()) {
                // Valid code, log the user in
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];

                // Clear 2FA code
                $query = "UPDATE tbl_user SET two_factor_code = NULL, two_factor_expires = NULL WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user['user_id']);
                $stmt->execute();

                // Send login notification
                $emailService->sendLoginNotification($email);

                // Clear temporary session data
                unset($_SESSION['temp_email']);

                // Redirect based on user type
                if (isset($_SESSION['is_manager']) && $_SESSION['is_manager'] == 1) {
                    header("Location: ../Leave Approval/main.php");
                } else {
                    header("Location: ../Leave Application/user_portal.php");
                }
                exit();
            } else {
                $error = "Verification code has expired. Please login again.";
                unset($_SESSION['temp_email']);
                unset($_SESSION['is_manager']);
            }
        } else {
            $error = "Invalid verification code.";
        }
    } else {
        $error = "User not found.";
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
    <title>Verify Login - SimpLeav</title>
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
            <h1>Verify Login</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="color: #721c24;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div style="color: rgba(50, 135, 214, 0.975); text-align: center; margin-bottom: 10px;">
                Enter the verification code sent to your email
            </div>

            <div class="input">
                <input type="text"
                       class="form-control"
                       placeholder="000000"
                       name="2fa_code"
                       required
                       pattern="[0-9]{6}"
                       maxlength="6"
                       autocomplete="off"
                       style="color: black; letter-spacing: 8px; font-size: 20px; text-align: center;">
                <i class="fa fa-key" style="color: black;"></i>
            </div>

            <div class="remember-me">
                <a href="login.php">Back to Login</a>
            </div>

            <button type="submit" class="btn">Verify Code</button>
        </form>
    </div>
</body>
</html>
