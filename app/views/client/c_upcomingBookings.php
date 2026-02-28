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

    <?php if (!empty($_SESSION['success'])): ?>
        <p class="success-msg"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error-msg"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (empty($data['bookings'])): ?>
        <p class="no-bookings">You don’t have any upcoming bookings yet.</p>
    <?php else: ?>


<div class="table-wrapper">
<table class="bookings-table">
    <thead>
        <tr>
            <th>Caregiver</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Duration</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['bookings'] as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['caretaker_name']) ?></td>
                <td><?= htmlspecialchars($b['service_type']) ?></td>
                <td><?= date('Y-m-d', strtotime($b['booking_date'])) ?></td>
                <td><?= htmlspecialchars($b['preferred_time']) ?></td>
                <td><?= $b['duration'].' '.$b['basis'] ?></td>

                <td class="actions">
                    <button class="action-btn" id="cancel-btn"
                        onclick="openCancelModal(<?= $b['booking_id'] ?>)">
                        Cancel
                    </button>

                    <?php
                        $canReschedule = in_array($b['status'], ['Accepted','Advance_Paid','Payment_Requested']);
                        if ($b['status'] === 'Reschedule_Requested') {
                            $canReschedule = false;
                        }
                    ?>
                    <?php if ($canReschedule): ?>
                        <button class="action-btn" id="reschedule-btn"
                            onclick="openRescheduleModal(<?= $b['booking_id'] ?>)">
                            Reschedule
                        </button>
                    <?php endif; ?>
                </td>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php endif; ?>


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


    <label>Reason for rescheduling</label>
    <textarea name="reason" rows="3" placeholder="Optional (for HR)"></textarea>

    <button type="submit" class="reschedule-btn">Save Changes</button>
</form>
    </div>
</div>

<!-- ================= MINIMAL JS (ONLY FOR POPUPS) ================= -->

<script src="<?= URLROOT ?>/public/js/client/c_upcomingBookings.js"></script>



</body>
</html>
