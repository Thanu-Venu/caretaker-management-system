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
    <p><strong>Booking ID:</strong> <?= $data['booking']['booking_id'] ?></p>
    <p><strong>Caretaker:</strong> <?= htmlspecialchars($data['booking']['caretaker_name']) ?></p>
    <p><strong>Service:</strong> <?= htmlspecialchars($data['booking']['service_type']) ?></p>
    <p><strong>Basis:</strong> <?= htmlspecialchars($data['booking']['basis']) ?></p>
    <p><strong>Duration:</strong> <?= htmlspecialchars($data['booking']['duration']) ?> <?= htmlspecialchars($data['booking']['basis']) ?></p>
    <p><strong>Preferred Time:</strong> <?= htmlspecialchars($data['booking']['preferred_time']) ?></p>
    <p><strong>Booking Date:</strong> <?= date('d M Y', strtotime($data['booking']['booking_date'])) ?></p>
    <p><strong>District:</strong> <?= htmlspecialchars($data['booking']['district']) ?></p>
    <p><strong>Street:</strong> <?= htmlspecialchars($data['booking']['street']) ?></p>
    <p><strong>Address 1:</strong> <?= htmlspecialchars($data['booking']['address_line1']) ?></p>
    <?php if (!empty($data['booking']['address_line2'])): ?>
        <p><strong>Address 2:</strong> <?= htmlspecialchars($data['booking']['address_line2']) ?></p>
    <?php endif; ?>
    <?php if (!empty($data['booking']['postal_code'])): ?>
        <p><strong>Postal Code:</strong> <?= htmlspecialchars($data['booking']['postal_code']) ?></p>
    <?php endif; ?>
    <?php if (!empty($data['booking']['customization'])): ?>
        <p><strong>Customization Notes:</strong> <?= nl2br(htmlspecialchars($data['booking']['customization'])) ?></p>
    <?php endif; ?>
    <p><strong>Total Payment:</strong> Rs. <?= number_format($data['booking']['total_payment'],2) ?></p>
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