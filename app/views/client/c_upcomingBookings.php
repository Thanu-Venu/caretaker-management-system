<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Caretaker</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_upcomingBookings.css">
</head>

<body>
<main class="content">
    <h1>My Upcoming Bookings</h1>

    <p id="noBookings" class="no-bookings" style="display: none;">
        You don’t have any upcoming bookings yet.
    </p>

    <!-- Bookings List -->
    <div class="bookings-list">
    <?php if (!empty($data['bookings'])): ?>
        <?php foreach ($data['bookings'] as $b): ?>
            <div class="booking-card">
                <h2><?= htmlspecialchars($b['caretaker_name']) ?></h2>
                <p><strong>Service:</strong> <?= htmlspecialchars($b['service_type']) ?></p>
                <p><strong>Date:</strong> <?= date('Y-m-d', strtotime($b['booking_date'])) ?></p>
                <p><strong>Time:</strong> <?= htmlspecialchars($b['preferred_time']) ?></p>
                <p><strong>Duration:</strong> <?= $b['duration'] . ' ' . $b['basis'] ?></p>
                <p><strong>Status:</strong> <span class="status <?= strtolower($b['status']) ?>"><?= $b['status'] ?></span></p>
                <div class="card-actions">
                    <button class="cancel-btn" data-booking-id="<?= $b['booking_id'] ?>">Cancel Booking</button>
                    <button class="reschedule-btn" data-booking-id="<?= $b['booking_id'] ?>">Reschedule</button>
                    <?php if ($b['status'] == 'Pending'): ?>
                        <button class="payment-btn" data-booking-id="<?= $b['booking_id'] ?>">Proceed to Payment</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p id="noBookings" class="no-bookings">
            You don’t have any upcoming bookings yet.
        </p>
    <?php endif; ?>
</div>


    <!-- Reschedule Modal -->
<div id="rescheduleModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Reschedule Booking</h2>
    <form id="rescheduleForm">
      <div class="form-group">
        <label for="newDate">New Date</label>
        <input type="date" id="newDate" required>
      </div>
      <div class="form-group">
        <label for="newTime">New Time</label>
        <select id="newTime" required>
          <option value="">-- Select Time --</option>
          <option value="Morning">Morning</option>
          <option value="Afternoon">Afternoon</option>
          <option value="Evening">Evening</option>
        </select>
      </div>
      <div class="form-group">
        <label for="newDuration">New Duration</label>
        <input type="number" id="newDuration" min="1" placeholder="Hours/Days" required>
      </div>

       <!-- ⚠️ Warning Message -->
      <p class="payment-warning">
        ⚠️ Note: Rescheduling may affect your total cost. Payment adjustments will be handled accordingly.
      </p>
      <button type="submit" class="save-btn">Save Changes</button>
    </form>
  </div>
</div>

<!-- Cancel Confirmation Modal -->
<div id="cancelModal" class="modal">
  <div class="modal-content">
    <span class="close cancel-close">&times;</span>
    <h2>Confirm Cancellation</h2>
    <p>Are you sure you want to cancel this booking?</p>
    <form id="cancelForm">
      <div class="form-group">
        <label for="cancelReason">Reason for cancellation (optional):</label>
        <textarea id="cancelReason" rows="3" placeholder="Enter reason"></textarea>
      </div>
      <div class="modal-actions">
        <button type="button" id="cancelNo" class="cancel1-btn-secondary">No, Keep Booking</button>
        <button type="submit" class="cancel1-btn">Yes, Cancel Booking</button>
      </div>
    </form>
  </div>
</div>
</main>
<script src="<?php echo URLROOT; ?>/public/js/client/c_upcomingBookings.js"></script>
</body>