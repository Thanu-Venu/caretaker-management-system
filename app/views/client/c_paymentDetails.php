<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Payment Details</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_paymentDetails.css">
</head>

<body>
    <?php
    $booking = $data['booking'] ?? [];
    $timeline = $data['timeline_events'] ?? [];
    $nextPayable = $data['next_payable'] ?? null;
    $advancePayNow = (strtolower((string)($booking['status'] ?? '')) === 'payment_requested');

    function timelineStatusClass($status)
    {
        $status = strtolower((string)$status);
        if (in_array($status, ['paid', 'approved'], true)) return 'status-paid';
        if ($status === 'overdue') return 'status-overdue';
        if (in_array($status, ['due_soon', 'pending', 'advance_required'], true)) return 'status-due-soon';
        if ($status === 'cancelled') return 'status-cancelled';
        return 'status-upcoming';
    }
    ?>

    <div class="payment-details-page">
        <div class="top-bar">
            <a class="back" href="<?= URLROOT ?>/client/payments">Back to Payments</a>
            <h1>Payment Timeline - Booking #<?= (int)($booking['booking_id'] ?? 0) ?></h1>
        </div>

        <div class="booking-summary">
            <p><strong>Service:</strong> <?= htmlspecialchars($booking['service_type'] ?? '-') ?> (<?= htmlspecialchars($booking['basis'] ?? '-') ?>)</p>
            <p><strong>Caretaker:</strong> <?= htmlspecialchars($booking['caretaker_name'] ?? '-') ?></p>
            <p><strong>Service Start:</strong> <?= htmlspecialchars($booking['booking_date'] ?? '-') ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($booking['status'] ?? '-') ?></p>
        </div>

        <?php if (!empty($nextPayable)): ?>
            <div class="next-payment-card">
                <h3>Next Payable Installment</h3>
                <p><strong>Amount:</strong> LKR <?= number_format((float)$nextPayable['amount'], 2) ?></p>
                <p><strong>Due Date:</strong> <?= htmlspecialchars($nextPayable['due_date']) ?></p>
                <a class="pay-now" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= (int)$booking['booking_id'] ?>&recurring_payment_id=<?= (int)$nextPayable['id'] ?>">Pay Now</a>
            </div>
        <?php elseif ($advancePayNow): ?>
            <div class="next-payment-card">
                <h3>Advance Payment Required</h3>
                <p>Your booking is awaiting advance payment confirmation.</p>
                <a class="pay-now" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= (int)$booking['booking_id'] ?>">Pay Now</a>
            </div>
        <?php else: ?>
            <div class="next-payment-card muted">
                <h3>Pay Now Not Available</h3>
                <p>Payment can be made when due, due within 7 days, or overdue within grace period.</p>
            </div>
        <?php endif; ?>

        <section class="timeline-card">
            <h2>Payment Timeline</h2>

            <div class="timeline-list">
                <?php if (empty($timeline)): ?>
                    <p>No payment timeline entries found for this booking.</p>
                <?php else: ?>
                    <?php foreach ($timeline as $entry): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot <?= timelineStatusClass($entry['status'] ?? '') ?>"></div>
                            <div class="timeline-body">
                                <div class="timeline-head">
                                    <h4><?= htmlspecialchars($entry['label'] ?? '-') ?></h4>
                                    <span class="timeline-status <?= timelineStatusClass($entry['status'] ?? '') ?>">
                                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', (string)($entry['status'] ?? '')))) ?>
                                    </span>
                                </div>
                                <?php if (!empty($entry['date'])): ?>
                                    <p class="timeline-date"><?= htmlspecialchars($entry['date']) ?></p>
                                <?php endif; ?>
                                <p class="timeline-note"><?= htmlspecialchars($entry['note'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>

</html>