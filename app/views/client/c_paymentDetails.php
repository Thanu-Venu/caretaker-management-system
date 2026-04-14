<?php
$clientPageTitle = 'Payment timeline — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_paymentDetails.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/client/partials/client_booking_status_helper.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$booking = $data['booking'] ?? [];
$timeline = $data['timeline_events'] ?? [];
$nextPayable = $data['next_payable'] ?? null;
$advancePayNow = (strtolower((string) ($booking['status'] ?? '')) === 'payment_requested');

function timelineStatusClass($status)
{
    $status = strtolower((string) $status);
    if (in_array($status, ['paid', 'approved'], true)) {
        return 'status-paid';
    }
    if ($status === 'overdue') {
        return 'status-overdue';
    }
    if (in_array($status, ['due_soon', 'pending', 'advance_required'], true)) {
        return 'status-due-soon';
    }
    if ($status === 'cancelled') {
        return 'status-cancelled';
    }

    return 'status-upcoming';
}

$rawStatus = (string) ($booking['status'] ?? '');
$statusDisplay = str_replace('_', ' ', $rawStatus);
$bkDate = (string) ($booking['booking_date'] ?? '');
?>

<main class="main-content admin-dashboard-page payment-details">
    <header class="page-header">
        <div>
            <h1 class="page-title">Payment timeline</h1>
            <p class="text-muted"><?= htmlspecialchars((string) ($booking['service_type'] ?? 'Booking')) ?> · <?= htmlspecialchars((string) ($booking['caretaker_name'] ?? '')) ?></p>
        </div>
        <div class="header-actions">
            <a class="btn secondary" href="<?= URLROOT ?>/client/payments">Back to payments</a>
        </div>
    </header>

    <section class="payment-details-card" aria-labelledby="pd-summary-heading">
        <h2 id="pd-summary-heading" class="payment-details-card__title">Booking summary</h2>
        <dl class="admin-row-detail-modal__dl payment-details-dl">
            <dt>Service</dt>
            <dd><?= htmlspecialchars((string) ($booking['service_type'] ?? '—')) ?></dd>
            <dt>Caregiver</dt>
            <dd><?= htmlspecialchars((string) ($booking['caretaker_name'] ?? '—')) ?></dd>
            <dt>Status</dt>
            <dd><span class="status <?= client_booking_status_class($rawStatus) ?>"><?= htmlspecialchars($statusDisplay) ?></span></dd>
            <dt>Service start</dt>
            <dd><?= htmlspecialchars((string) ($booking['service_start_date'] ?? '—')) ?></dd>
            <dt>Booking date</dt>
            <dd><?= $bkDate !== '' ? htmlspecialchars(date('Y-m-d', strtotime($bkDate))) : '—' ?></dd>
        </dl>
    </section>

    <?php if (!empty($nextPayable)): ?>
        <section class="payment-details-card payment-details-card--accent" aria-labelledby="pd-next-heading">
            <h2 id="pd-next-heading" class="payment-details-card__title">Next payable installment</h2>
            <p class="payment-details-lead"><strong>LKR <?= number_format((float) $nextPayable['amount'], 2) ?></strong> due <?= htmlspecialchars((string) $nextPayable['due_date']) ?>.</p>
            <a class="btn" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= (int) $booking['booking_id'] ?>&recurring_payment_id=<?= (int) $nextPayable['id'] ?>">Pay now</a>
        </section>
    <?php elseif ($advancePayNow): ?>
        <section class="payment-details-card payment-details-card--accent" aria-labelledby="pd-adv-heading">
            <h2 id="pd-adv-heading" class="payment-details-card__title">Advance payment</h2>
            <p class="payment-details-lead">Your booking is awaiting advance payment confirmation.</p>
            <a class="btn" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= (int) $booking['booking_id'] ?>">Pay advance</a>
        </section>
    <?php else: ?>
        <section class="payment-details-card payment-details-card--muted" aria-labelledby="pd-none-heading">
            <h2 id="pd-none-heading" class="payment-details-card__title">Pay now</h2>
            <p class="text-muted payment-details-lead">Payment can be made when due, within 7 days of due date, or while an overdue item is still in its grace period.</p>
        </section>
    <?php endif; ?>

    <section class="payment-details-card" aria-labelledby="pd-timeline-heading">
        <h2 id="pd-timeline-heading" class="payment-details-card__title">Timeline</h2>
        <div class="timeline-list">
            <?php if (empty($timeline)): ?>
                <p class="text-muted">No payment timeline entries found for this booking.</p>
            <?php else: ?>
                <?php foreach ($timeline as $entry): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot <?= timelineStatusClass($entry['status'] ?? '') ?>" aria-hidden="true"></div>
                        <div class="timeline-body">
                            <div class="timeline-head">
                                <h4><?= htmlspecialchars((string) ($entry['label'] ?? '—')) ?></h4>
                                <span class="timeline-status <?= timelineStatusClass($entry['status'] ?? '') ?>">
                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', (string) ($entry['status'] ?? '')))) ?>
                                </span>
                            </div>
                            <?php if (!empty($entry['date'])): ?>
                                <p class="timeline-date"><?= htmlspecialchars((string) $entry['date']) ?></p>
                            <?php endif; ?>
                            <p class="timeline-note"><?= htmlspecialchars((string) ($entry['note'] ?? '')) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
