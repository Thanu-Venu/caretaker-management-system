<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proceed Payment</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_paymentPage.css">
</head>

<body>

  <?php
  $booking = $data['booking'] ?? [];
  $calc = $data['payment_calc'] ?? null;
  $recurringPayment = $data['recurring_payment'] ?? null;

  // Fallback: if controller didn't pass calc, try PaymentController (defensive)
  if (!$calc && file_exists(APPROOT . '/controllers/PaymentController.php')) {
    require_once APPROOT . '/controllers/PaymentController.php';
    if (class_exists('PaymentController') && method_exists('PaymentController', 'calculatePaymentDetails')) {
      $calc = PaymentController::calculatePaymentDetails($booking);
      $calc['advance'] = $calc['advance_amount'] ?? 0;
      $calc['remaining'] = $calc['remaining_balance'] ?? 0;
      $calc['notes'] = $calc['description'] ?? '';
    }
  }

  $advancePayment = isset($calc['advance']) ? $calc['advance'] : (!empty($booking['total_payment']) ? ($booking['total_payment'] * 0.5) : 0);
  $finalAmount = $recurringPayment ? (float)$recurringPayment['amount'] : (float)$advancePayment;
  ?>

  <div class="content">
    <h1>Payment Details</h1>
    <?php if ($recurringPayment): ?>
      <p>Paying recurring installment for due date: <strong><?= htmlspecialchars($recurringPayment['due_date']) ?></strong></p>
    <?php endif; ?>

    <!-- Payment Form -->
    <form id="paymentForm" method="post" action="<?= URLROOT ?>/client/processPayment">
      <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? $booking['booking_id'] ?? '' ?>">
      <?php if (!empty($recurringPayment['id'])): ?>
        <input type="hidden" name="recurring_payment_id" value="<?= (int)$recurringPayment['id'] ?>">
      <?php endif; ?>
      <input type="hidden" name="client_id" value="<?= AuthSession::profileId() ?? '' ?>">
      <input type="hidden" name="amount" value="<?= $finalAmount ?>">


      <div class="form-group">
        <label for="payment_method">Payment Method</label>
        <select id="payment_method" name="payment_method" required>
          <option value="">Select Payment Method</option>
          <option value="credit_card">Credit Card</option>
          <option value="debit_card">Debit Card</option>
          <option value="mobile_wallet">Mobile Wallet</option>
          <option value="bank_transfer">Bank Transfer</option>
        </select>
      </div>

      <p>Card details will be entered securely on PayHere Sandbox after you continue.</p>
      <div class="form-actions">
        <button type="submit" class="pay-btn">Proceed to PayHere Sandbox</button>
        <button type="button" class="cancel-btn" onclick="window.location.href='?url=client/c_paymentHistory'">Cancel</button>
      </div>
    </form>
  </div>
</body>

</html>