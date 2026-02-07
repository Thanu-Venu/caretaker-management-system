<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment History</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_paymentPage.css">
</head>
<body></body>
<div class="content">
  <h1>Payment Details</h1>

  <!-- Payment Form -->
  <form id="paymentForm">

    <div class="form-group">
      <label for="cardNumber">Card Number</label>
      <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" required>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="expiry">Expiry Date</label>
        <input type="text" id="expiry" placeholder="MM/YY" required>
      </div>
      <div class="form-group">
        <label for="cvv">CVV</label>
        <input type="password" id="cvv" placeholder="123" required>
      </div>
    </div>

    <div class="form-group">
      <label for="name">Cardholder Name</label>
      <input type="text" id="name" placeholder="John Doe" required>
    </div>
    <div class="form-actions">
    <button type="submit" class="pay-btn" onclick="window.location.href='?url=client/c_paymentSuccess'">Pay Now</button>
    <button type="button" class="cancel-btn" onclick="window.location.href='?url=client/c_upcomingBookings'">Cancel</button>
    </div>
  </form>
</div>
