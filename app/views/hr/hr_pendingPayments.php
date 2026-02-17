<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Payments</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_pendingPayments.css">
</head>
<body>

<div class="container">
    <h1>Payment Management</h1>

    <?php if (!empty($data['payments'])): ?>
    <table class="payments-table">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Client Name</th>
                <th>Caretaker</th>
                <th>Service Type</th>
                <th>Booking Date</th>
                <th>Full Payment</th>
                <th>Advance Payment</th>
                <th>Remaining Balance</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Action</th>
               
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['payments'] as $payment): ?>
            <tr>
                <td>#<?= $payment['booking_id'] ?></td>
                <td><?= htmlspecialchars($payment['client_name']) ?></td>
                <td><?= htmlspecialchars($payment['caretaker_name']) ?></td>
                <td><?= htmlspecialchars($payment['service_type']) ?></td>
                <td><?= $payment['booking_date'] ?></td>
                <td>Rs. <?= number_format($payment['total_booking_amount'], 2) ?></td>
                <td>Rs. <?= number_format($payment['amount'], 2) ?></td>
                <td>Rs. <?= number_format($payment['remaining_balance'], 2) ?></td>
                <td><?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?></td>
                <td>
                    <span class="status-badge status-<?= strtolower($payment['status']) ?>">
                        <?= ucfirst($payment['status']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($payment['status'] === 'pending'): ?>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                        <button type="submit" formaction="<?= URLROOT ?>/hr/approvePayment" class="btn btn-success">Approve</button>
                    </form>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                        <input type="text" name="reason" placeholder="Reason" style="width: 100px; padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                        <button type="submit" formaction="<?= URLROOT ?>/hr/rejectPayment" class="btn btn-danger">Reject</button>
                    </form>
                    <?php else: ?>
                    <span class="no-action">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="no-data">No payments at this time.</p>
    <?php endif; ?>
</div>

</body>
</html>