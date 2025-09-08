<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Bookings</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_pastBookings.css">
</head>

<body>
<main class="content">
    <h1>My Past Bookings</h1>

    <p id="noPastBookings" class="no-bookings" style="display: none;">
        You don’t have any past bookings yet.
    </p>

    <div class="bookings-list">
        <!-- Completed Booking -->
        <div class="booking-card completed">
            <h2>Jane Doe</h2>
            <p><strong>Service:</strong> Babysitting</p>
            <p><strong>Date:</strong> 2025-08-15</p>
            <p><strong>Time:</strong> Morning</p>
            <p><strong>Duration:</strong> 3 Hours</p>
            <p><strong>Status:</strong> <span class="status completed">Completed</span></p>
            <button class="feedback-btn">Give Feedback</button>
        </div>

        <!-- Cancelled Booking -->
        <div class="booking-card cancelled">
            <h2>Sam Silva</h2>
            <p><strong>Service:</strong> Elder Care</p>
            <p><strong>Date:</strong> 2025-08-10</p>
            <p><strong>Time:</strong> Evening</p>
            <p><strong>Duration:</strong> 2 Days</p>
            <p><strong>Status:</strong> <span class="status cancelled">Cancelled</span></p>
        </div>
    </div>

    <!-- Feedback Modal -->
<div id="feedbackModal" class="modal">
  <div class="modal-content">
    <span class="close feedback-close">&times;</span>
    <h2>Give Feedback</h2>

    <form id="feedbackForm">
      <input type="hidden" id="bookingId">

      <!-- Star Rating -->
      <div class="form-group">
        <label>Rating:</label>
        <div class="stars">
          <span data-value="1">★</span>
          <span data-value="2">★</span>
          <span data-value="3">★</span>
          <span data-value="4">★</span>
          <span data-value="5">★</span>
        </div>
        <p id="ratingText">0/5 stars</p>
      </div>

      <!-- Feedback Text -->
      <div class="form-group">
        <label for="feedback">Your Feedback:</label>
        <textarea id="feedback" rows="4" placeholder="Write your feedback..." required></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="save-btn">Submit Feedback</button>
        <button type="button" id="cancelFeedback" class="cancel-btn-secondary">Cancel</button>
      </div>
    </form>
  </div>
</div>


</main>
</body>
</html>
<script src="<?php echo URLROOT; ?>/public/js/client/c_pastBookings.js"></script>