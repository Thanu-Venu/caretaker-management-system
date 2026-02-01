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
    <form method="get" action="<?= URLROOT ?>/hr/hr_pending_request" class="filter-bar">
    <label for="status">Filter by Status:</label>
    <select name="status" id="status" onchange="this.form.submit()">
        <?php
        $statuses = ['All','Pending','Accepted','Rejected','Cancelled','Completed'];
        foreach ($statuses as $s):
        ?>
            <option value="<?= $s ?>"
                <?= ($data['status'] === $s) ? 'selected' : '' ?>>
                <?= $s ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

    <table class="requests-table">
        <thead>
            <tr>
                <th>Client ID</th>
                <th>Client</th>
                <th>Service</th>
                <th>Preferred Caretaker</th>
                <th>Date & Time</th>
                <th>Customization</th>
                <th>Total Payment</th>
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
            <td><?= nl2br(htmlspecialchars($b['customization'])) ?></td>
            <td>LKR <?= number_format($b['total_payment'] ?? 0, 2) ?></td>
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
                <form method="post" action="<?= URLROOT ?>/hr/updateBookingStatus" class="action-form">
                    <input type="hidden" name="booking_id" value="<?= (int) $b['booking_id'] ?>">
        
                    <?php $hasCustomization = !empty(trim($b['customization'] ?? '')); ?>
        
                    <?php if ($hasCustomization): ?>
                        <div class="fee-box">
                            <label class="fee-label">Customization Fee (LKR)</label>
                            <input type="number" name="customization_fee" class="fee-input" min="0" step="0.01" value="0" required>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="customization_fee" value="0">
                    <?php endif; ?>
        
                    <button class="approve" name="action" value="accept">
                        Approve<?= $hasCustomization ? ' + Fee' : '' ?>
                    </button>
        
                    <button class="reject" name="action" value="reject">Reject</button>
                </form>
            <?php else: ?>
                <?= htmlspecialchars($b['status']) ?>
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
    <?php if ($data['totalPages'] > 1): ?>
<div class="pagination">
    <?php
        $current = $data['page'];
        $status = urlencode($data['status']);
    ?>

    <?php if ($current > 1): ?>
        <a href="<?= URLROOT ?>/hr/hr_pending_request?page=<?= $current-1 ?>&status=<?= $status ?>">&laquo;</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
        <a class="<?= ($i === $current) ? 'active' : '' ?>"
           href="<?= URLROOT ?>/hr/hr_pending_request?page=<?= $i ?>&status=<?= $status ?>">
           <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($current < $data['totalPages']): ?>
        <a href="<?= URLROOT ?>/hr/hr_pending_request?page=<?= $current+1 ?>&status=<?= $status ?>">&raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>


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
