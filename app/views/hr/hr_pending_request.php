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
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
     <tbody>
<?php if (!empty($data['bookings'])): ?>
    <?php foreach ($data['bookings'] as $b): ?>
                <tr>
                    <?php
                        $rawStatus = $b['status'] ?? '';
                        $status = !empty($rawStatus) ? $rawStatus : 'Requested';
                    ?>
                        <td><?= $b['booking_id'] ?></td>
                        <td><?= htmlspecialchars($b['client_name']) ?></td>
                        <td><?= htmlspecialchars($b['service_type']) ?></td>
                        <td><?= htmlspecialchars($b['caretaker_name']) ?></td>
                        <td><?= $b['booking_date'] ?> (<?= $b['preferred_time'] ?>)</td>
                        <td>
                            <?php
                                $customText = trim($b['customization'] ?? '');
                                $customHours = (int) ($b['customization_hours'] ?? 0);
                            ?>
                            <?= $customText !== '' ? htmlspecialchars($customText) : '—' ?>
                            <?php if ($customHours > 0): ?>
                                <div><small>Extra hours: <?= $customHours ?></small></div>
                            <?php endif; ?>
                        </td>
                        <td>
                             <span class="status-<?= strtolower($status) ?>"><?= $status ?></span>
                        </td>
                        <td>
                <?php if (empty($rawStatus)): ?>
                <span class="badge badge-warning">Requested</span>
                <?php elseif ($status === 'Requested'): ?>
     <form method="post" action="<?= URLROOT ?>/hr/requestAdvancePayment" class="advance-payment-form">
    <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
    <input type="hidden" name="client_id" value="<?= $b['client_id'] ?>">
    <button type="submit" class="btn btn-primary">
        Request Advance Payment
    </button>
</form>

    <?php elseif ($status === 'Payment_Requested'): ?>
    <span class="badge badge-warning">Waiting for payment</span>
     <?php else: ?>
        <span class="status-<?= strtolower($status) ?>"><?= $status ?></span>
<?php endif; ?>

</td>


        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="8">No bookings</td>
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
        <p id="modalText">Are you sure you want to request advance payment?</p>
        <div class="modal-actions">
            <button id="confirmYes" class="approve">Yes</button>
            <button id="confirmNo" class="reject">No</button>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/hr/hr_pending_request.js"></script>
</body>
</html>