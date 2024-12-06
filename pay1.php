<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Options</title>
<!--  icon -->
  <link rel="shortcut icon" href="img/logo2.jpg" type="image/x-icon">
  <link rel="stylesheet" href="css/style.css">
  <link rel="shortcut icon" href="img/logo2.jpg" type="image/x-icon">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Forum&display=swap" rel="stylesheet">

</head>
<body>

<div class="container mt-5 bg-primary">
  <h1 class="text-center mb-4 ">Choose Your Subscription Plan</h1>
  <div class="row">
    <!-- Plan 1: Ksh 1000 -->
    <div class="col-md-6 mb-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Basic Plan</h5>
          <h6 class="card-subtitle mb-2 text-muted">Ksh 1000/month</h6>
          <p class="card-text">✔️ Access to basic features and content.</p>
          <form action="pay1.php" method="POST">
            <input type="hidden" name="amount" value="1000">
            <input type="hidden" name="plan" value="basic">
            <button ><a href="payment1.php">Subscribe Now</a></button>
          </form>
        </div>
      </div>
    </div>

    <!-- Plan 2: Ksh 2000 -->
    <div class="col-md-6 mb-4">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Premium Plan</h5>
          <h6 class="card-subtitle mb-2 text-muted">Ksh 2000/month</h6>
          <p class="card-text">Unlimited access to all features and content.</p>
          <ul class="list-unstyled">
            <li>✔️ Unlimited Content Access</li>
            <li>✔️ Priority Customer Support</li>
            <li>✔️ Exclusive Offers and Discounts</li>
            <li>✔️ Early Access to New Features</li>
          </ul>
          <form action="payment2.php" method="POST">
            <input type="hidden" name="amount" value="2000">
            <input type="hidden" name="plan" value="premium">
            <button ><a href="payment2.php">Subscribe Now</a></button>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
