<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Upcoming Bookings</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/client/c_upcomingBookings.css">
</head>

<body>
<main class="content">
    <h1>My Upcoming Bookings</h1>

    <?php if (empty($data['bookings'])): ?>
        <p class="no-bookings">You don’t have any upcoming bookings yet.</p>
    <?php else: ?>

    <div class="bookings-list">
        <?php foreach ($data['bookings'] as $b): ?>
            <div class="booking-card">

                <h2><?= htmlspecialchars($b['caretaker_name']) ?></h2>
                <p><strong>Service:</strong> <?= htmlspecialchars($b['service_type']) ?></p>
                <p><strong>Date:</strong> <?= date('Y-m-d', strtotime($b['booking_date'])) ?></p>
                <p><strong>Time:</strong> <?= htmlspecialchars($b['preferred_time']) ?></p>
                <p><strong>Duration:</strong> <?= $b['duration'].' '.$b['basis'] ?></p>

                <div class="card-actions">
                    <!-- CANCEL -->
                    <button class="cancel-btn"
                        onclick="openCancelModal(<?= $b['booking_id'] ?>)">
                        Cancel Booking
                    </button>

                    <!-- RESCHEDULE -->
                    <button class="reschedule-btn"
                        onclick="openRescheduleModal(<?= $b['booking_id'] ?>)">
                        Reschedule Booking
                    </button>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</main>

<!-- ================= CANCEL MODAL ================= -->
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCancelModal()">&times;</span>
        <h2>Cancel Booking</h2>

        <form method="POST" action="<?= URLROOT ?>/client/cancelBooking">
    <input type="hidden" name="booking_id" id="cancelBookingId">

    <label>Reason for cancellation</label>
    <textarea name="reason" rows="3" placeholder="Enter reason" required></textarea>

    <button type="submit" class="cancel1-btn">Confirm Cancel</button>
    </form>
    </div>
</div>

<!-- ================= RESCHEDULE MODAL ================= -->
<div id="rescheduleModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeRescheduleModal()">&times;</span>
        <h2>Reschedule Booking</h2>

 <form method="POST" action="<?= URLROOT ?>/client/rescheduleBooking">
    <input type="hidden" name="booking_id" id="rescheduleBookingId">

    <label>New Date</label>
    <input type="date" name="new_date" required>

    <label>New Time</label>
<select name="new_time" required>
    <option value="">Select</option>
    <option value="Full Time (8am - 5pm)">Full Time (8am - 5pm)</option>
    <option value="Morning (8am - 12pm)">Morning (8am - 12pm)</option>
    <option value="Evening (1pm - 5pm)">Evening (1pm - 5pm)</option>
    <option value="Night (6pm - 10pm)">Night (6pm - 10pm)</option>
</select>


    <label>New Duration</label>
    <input type="number" name="new_duration" min="1" required>

    <button type="submit" class="reschedule-btn">Save Changes</button>
</form>
    </div>
</div>

<!-- ================= MINIMAL JS (ONLY FOR POPUPS) ================= -->

<script src="<?= URLROOT ?>/public/js/client/c_upcomingBookings.js"></script>



</body>
</html>
