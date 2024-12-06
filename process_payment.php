<?php
session_start();

if (isset($_POST['phone']) && !empty($_POST['phone'])) {
    $phone = trim($_POST['phone']);

    // Validate Safaricom number format
    if (preg_match('/^(7[0-9]{2}|11[0-5])[0-9]{6}$/', $phone)) {
        $_SESSION['payment_phone'] = $phone;

        // Store the plan type in session
        $_SESSION['plan_type'] = $_POST['plan'] ?? 'basic';

        header("Location: payment_processing.php");
        exit();
    } else {
        $return_page = ($_POST['plan'] === 'premium') ? 'payment2.php' : 'payment1.php';
        header("Location: " . $return_page . "?error=invalid_phone");
        exit();
    }
} else {
    $return_page = ($_POST['plan'] === 'premium') ? 'payment2.php' : 'payment1.php';
    header("Location: " . $return_page . "?error=missing_phone");
    exit();
}
