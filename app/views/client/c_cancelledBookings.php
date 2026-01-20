<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelled Bookings</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_cancelledBookings.css">
</head>

<body>
<main class="content">
    <h1>My Cancelled Bookings</h1>

    <div class="cancelled-list">
<?php if (!empty($data['bookings'])): ?>
    <?php foreach ($data['bookings'] as $b): ?>
        <div class="cancelled-card">
            <h2><?= htmlspecialchars($b['caretaker_name']) ?></h2>
            <p><strong>Service:</strong> <?= htmlspecialchars($b['service_type']) ?></p>
            <p><strong>Date:</strong> <?= date('Y-m-d', strtotime($b['booking_date'])) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($b['preferred_time']) ?></p>
            <p><strong>Duration:</strong> <?= $b['duration'] . ' ' . $b['basis'] ?></p>
            <p><strong>Status:</strong> <span class="status cancelled">Cancelled</span></p>
            <p><strong>Reason:</strong> <?= htmlspecialchars($b['cancellation_reason']) ?></p>
            <p><strong>Cancelled At:</strong> <?= date('Y-m-d H:i', strtotime($b['cancelled_at'])) ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>You have no cancelled bookings.</p>
<?php endif; ?>
</div>

</main>

<script src="<?php echo URLROOT; ?>/public/js/client/c_cancelledBooking.js"></script>
</body>
</html>
