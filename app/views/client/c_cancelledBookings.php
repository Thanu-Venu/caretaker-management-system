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
        <p class="no-bookings">You have no cancelled bookings.</p>
    <?php else: ?>

    <div class="table-wrapper">
        <table class="bookings-table">
            <thead>
                <tr>
                    <th>Caretaker</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Cancelled At</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($data['bookings'] as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['caretaker_name']) ?></td>
                        <td><?= htmlspecialchars($b['service_type']) ?></td>
                        <td><?= date('Y-m-d', strtotime($b['booking_date'])) ?></td>
                        <td><?= htmlspecialchars($b['preferred_time']) ?></td>
                        <td><?= (int)$b['duration'] . ' ' . htmlspecialchars($b['basis']) ?></td>
                        <td>
                            <span class="status cancelled">Cancelled</span>
                        </td>
                        <td class="reason-cell">
                            <?= htmlspecialchars($b['cancellation_reason']) ?>
                        </td>
                        <td>
                            <?= date('Y-m-d H:i', strtotime($b['cancelled_at'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>
</main>


<script src="<?php echo URLROOT; ?>/public/js/client/c_cancelledBooking.js"></script>
</body>
</html>
