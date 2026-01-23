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
    <p><strong>Date:</strong> <?= date('d M Y', strtotime($data['booking']['booking_date'])) ?></p>
    <p><strong>Time:</strong> <?= date('h:i A', strtotime($data['booking']['created_at'])) ?></p>
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
