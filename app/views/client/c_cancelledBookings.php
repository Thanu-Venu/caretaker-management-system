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

    <?php if (empty($data['bookings'])): ?>
        <p id="noCancelled" class="no-cancelled">You don’t have any cancelled bookings yet.</p>
    <?php else: ?>
        <div class="cancelled-list">
            <?php foreach ($data['bookings'] as $booking): ?>
                <div class="cancelled-card">
                    <h2><?= htmlspecialchars($booking['caretaker_name']) ?></h2>
                    <p><strong>Service:</strong> <?= htmlspecialchars($booking['service_type']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
                    <p><strong>Time:</strong> <?= htmlspecialchars($booking['preferred_time']) ?></p>
                    <p><strong>Duration:</strong> <?= htmlspecialchars($booking['duration'] . ' ' . $booking['basis']) ?></p>
                    <p><strong>Status:</strong> <span class="status cancelled"><?= htmlspecialchars($booking['status']) ?></span></p>
                    <?php if (!empty($booking['cancellation_reason'])): ?>
                        <p class="cancel-reason"><strong>Reason:</strong> <?= htmlspecialchars($booking['cancellation_reason']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script src="<?php echo URLROOT; ?>/public/js/client/c_cancelledBookings.js"></script>
</body>
</html>
