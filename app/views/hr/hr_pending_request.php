<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Requests</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_pending_request.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-content">
    <h1>Pending Service Requests</h1>
    <div class="table-container">
    <table class="requests-table">
        <thead>
            <tr>
                <th>Client ID</th>
                <th>Client</th>
                <th>Service</th>
                <th>Preferred Caretaker</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
     <tbody>
<?php if (!empty($data['bookings'])): ?>
    <?php foreach ($data['bookings'] as $b): ?>
        <tr>
            <td><?= $b['booking_id'] ?></td>
            <td><?= htmlspecialchars($b['client_name']) ?></td>
            <td><?= htmlspecialchars($b['service_type']) ?></td>
            <td><?= htmlspecialchars($b['caretaker_name']) ?></td>
            <td><?= $b['booking_date'] ?> (<?= $b['preferred_time'] ?>)</td>
            <td>
                <?php 
                if ($b['status'] === 'Pending') {
                    echo $b['status'];
                } else {
                    echo "<span class='status-{$b['status']}'>{$b['status']}</span>";
                }
                ?>
            </td>
            <td>
    <?php if ($b['status'] === 'Pending'): ?>
        <form method="post" action="<?= URLROOT ?>/hr/updateBookingStatus">
            <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
            <button class="approve" name="action" value="accept">Approve</button>
            <button class="reject" name="action" value="reject">Reject</button>
        </form>
    <?php else: ?>
        <?= $b['status'] ?>
    <?php endif; ?>
</td>


        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="7">No bookings</td>
    </tr>
<?php endif; ?>
</tbody>


    </table>
</div>
</div>

<!-- Modal -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalText">Are you sure?</p>
        <div class="modal-actions">
            <button id="confirmYes" class="approve">Yes</button>
            <button id="confirmNo" class="reject">No</button>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/hr/hr_pending_reques.js"></script>
</body>
</html>
