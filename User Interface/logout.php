<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Clear any other cookies if you have them
// Example:
// setcookie('remember_me', '', time() - 3600, '/');

// Redirect to login page with a logout message
header("Location: login.php?logout=success");
exit();
?>
