<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking Confirmation</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_bookingConfirm.css">
</head>
<body>
  <div class="confirmation-container">
    <div class="card">
      <h2><i class='bx bx-check-square'></i>Booking Submitted Successfully!</h2>
      <p class="subtitle">Your caretaker booking request has been sent.</p>

     <div class="details">
    <p><strong>Booking ID:</strong> <span><?= $data['booking']['booking_id'] ?></span></p>
    <p><strong>Caretaker:</strong> <span><?= htmlspecialchars($data['booking']['caretaker_name']) ?></span></p>
    <p><strong>Service:</strong> <span><?= htmlspecialchars($data['booking']['service_type']) ?></span></p>
    <p><strong>Basis:</strong> <span><?= htmlspecialchars($data['booking']['basis']) ?></span></p>
    <p><strong>Duration:</strong> <span><?= htmlspecialchars($data['booking']['duration']) ?> <?= htmlspecialchars($data['booking']['basis']) ?></span></p>
    <p><strong>Preferred Time:</strong> <span><?= htmlspecialchars($data['booking']['preferred_time']) ?></span></p>
    <p><strong>Booking Date:</strong> <span><?= date('d M Y', strtotime($data['booking']['booking_date'])) ?></span></p>
    <p><strong>District:</strong> <span><?= htmlspecialchars($data['booking']['district']) ?></span></p>
    <p><strong>Street:</strong> <span><?= htmlspecialchars($data['booking']['street']) ?></span></p>
    <p><strong>Address 1:</strong> <span><?= htmlspecialchars($data['booking']['address_line1']) ?></span></p>
    <?php if (!empty($data['booking']['address_line2'])): ?>
      <p><strong>Address 2:</strong> <span><?= htmlspecialchars($data['booking']['address_line2']) ?></span></p>
    <?php endif; ?>
    <?php if (!empty($data['booking']['postal_code'])): ?>
      <p><strong>Postal Code:</strong> <span><?= htmlspecialchars($data['booking']['postal_code']) ?></span></p>
    <?php endif; ?>
    <?php if (!empty($data['booking']['customization'])): ?>
      <p><strong>Customization Notes:</strong> <span><?= nl2br(htmlspecialchars($data['booking']['customization'])) ?></span></p>
    <?php endif; ?>
    <p><strong>Total Payment:</strong> <span>Rs. <?= number_format($data['booking']['total_payment'],2) ?></span></p>
    <p><strong>Status:</strong> <span class="<?= strtolower($data['booking']['status']) ?>"><?= $data['booking']['status'] ?></span></p>
</div>


      <div class="actions">
        <button onclick="goToUpcoming()" class="viewbtn">View Upcoming Bookings</button>
        <button onclick="goHome()" class="secondary">Back to Home</button>
      </div>
    </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/client/c_bookingConfirm.js"></script>
</body>
</html>