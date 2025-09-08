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
        <p><strong>Booking ID:</strong> <span id="bookingId">BK12345</span></p>
        <p><strong>Caretaker:</strong> <span id="caretakerName">John Doe</span></p>
        <p><strong>Date:</strong> <span id="date">12 Sep 2025</span></p>
        <p><strong>Time:</strong> <span id="time">10:00 AM</span></p>
        <p><strong>Status:</strong> <span class="pending">Pending Confirmation</span></p>
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
