<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simpleav</title>
  <link rel="shortcut icon" href="img/logo2.jpg" type="image/x-icon">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="script.js"></script>
</head>
<body>

<section id="payment">
  <div class="payment-container bg-white">
    <div class="heading bg-primary">
      <h2>The Basic Plan</h2>
    </div>

    <?php
    // Display error messages if any
    if (isset($_GET['error'])) {
        $errorMessage = '';
        switch($_GET['error']) {
            case 'payment_failed':
                $errorMessage = 'Payment processing failed. ';
                if (isset($_GET['message'])) {
                    $errorMessage .= htmlspecialchars($_GET['message']);
                }
                break;
            case 'missing_phone':
                $errorMessage = 'Please provide a valid phone number.';
                break;
            default:
                $errorMessage = 'An error occurred. Please try again.';
        }
        echo '<div class="alert alert-danger" role="alert">' . $errorMessage . '</div>';
    }
    ?>

    <form id="payment-form" action="process_payment.php" method="POST">
      <input type="hidden" name="plan" value="basic">
      <div class="form-group">
        <label for="card-name">Name</label>
        <input type="text" id="card-name" name="card-name" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="phone-number">Phone Number</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">+254</span>
          </div>
          <input type="tel" id="phone-number" name="phone" class="form-control" placeholder="7XXXXXXXX or 1XXXXXXXX" required>
        </div>
        <small class="form-text text-muted">Enter your Safaricom number without leading 0 or 254</small>
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary">Pay Now</button>
      </div>
    </form>
  </div>
</section>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
  AOS.init();
</script>

<script>
document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const phoneInput = document.getElementById('phone-number');
    const phoneNumber = phoneInput.value.trim();

    // Regex pattern for valid Safaricom prefixes
    // Matches: 7xx, 1xx where xx are any digits
    const safaricomPattern = /^(7[0-9]{2}|11[0-5])[0-9]{6}$/;

    if (!safaricomPattern.test(phoneNumber)) {
        alert('Please enter a valid Safaricom number without leading 0 or 254.\n\nValid formats:\n- 7XXXXXXXX (for 07XX numbers)\n- 11XXXXXXX (for 011X numbers)');
        return false;
    }

    this.submit();
});
</script>

</body>
</html>
