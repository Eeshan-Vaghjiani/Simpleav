<?php
session_start();

// Verify we have a phone number
if (!isset($_SESSION['payment_phone'])) {
    header("Location: payment1.php");
    exit();
}

// Determine which STK push to use based on the submitted form
$stkpush_file = ($_SESSION['plan_type'] === 'basic') ? 'stkpush1.php' : 'stkpush2.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - SimpleAV</title>
    <link rel="shortcut icon" href="img/logo2.jpg" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .loader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/background.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }

        .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .spinner-border {
            width: 4rem;
            height: 4rem;
            color: #fff;
            margin: 1.5rem 0;
        }

        h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .logo {
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="loader-container">
        <div class="loader-content">
            <img src="img/logo2.jpg" alt="SimpleAV Logo" class="logo">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <h3>Processing Your Payment</h3>
            <p>Please wait while we initiate your payment...</p>
        </div>
    </div>

    <script>
        // Redirect to appropriate STK push based on the payment type
        setTimeout(function() {
            window.location.href = '<?php echo $stkpush_file; ?>';
        }, 2000);
    </script>
</body>
</html>
