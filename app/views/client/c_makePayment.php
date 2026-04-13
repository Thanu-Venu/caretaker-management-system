<?php
$clientPageTitle = 'Payment details — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_makePayment.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/client/partials/client_booking_status_helper.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$booking = $data['booking'] ?? [];
$calc = $data['payment_calc'] ?? null;
$recurringPayment = $data['recurring_payment'] ?? null;

if (!$calc && file_exists(APPROOT . '/controllers/PaymentController.php')) {
    require_once APPROOT . '/controllers/PaymentController.php';
    if (class_exists('PaymentController') && method_exists('PaymentController', 'calculatePaymentDetails')) {
        $calc = PaymentController::calculatePaymentDetails($booking);
        $calc['advance'] = $calc['advance_amount'] ?? 0;
        $calc['remaining'] = $calc['remaining_balance'] ?? 0;
        $calc['notes'] = $calc['description'] ?? '';
    }
}

$advancePayment = isset($calc['advance']) ? $calc['advance'] : (!empty($booking['total_payment']) ? ($booking['total_payment'] * 0.5) : 0);
$remainingBalance = isset($calc['remaining']) ? $calc['remaining'] : (max(0, ($booking['total_payment'] ?? 0) - $advancePayment));
$advanceNotes = $calc['notes'] ?? '';

$paymentAmount = $recurringPayment ? (float) $recurringPayment['amount'] : (float) $advancePayment;
$paymentLabel = $recurringPayment ? 'Recurring installment' : 'Advance payment';
$paymentNote = $recurringPayment ? ('Due date: ' . htmlspecialchars((string) $recurringPayment['due_date'])) : $advanceNotes;

<<<<<<< HEAD
    <div class="payment-page">
    <div class="payment-container">
        <h1>Payment Details</h1>
=======
$bkDate = (string) ($booking['booking_date'] ?? '');
$svcStart = trim((string) ($booking['service_start_date'] ?? ''));
$showSvcStart = $svcStart !== '' && $svcStart !== '0000-00-00' && $svcStart !== $bkDate;
$district = trim((string) ($booking['district'] ?? ''));
$rawStatus = (string) ($booking['status'] ?? '');
$statusDisplay = str_replace('_', ' ', $rawStatus);
?>
>>>>>>> 460c591ac37c19b771f06fef405fbc1364db34c4

<main class="main-content admin-dashboard-page make-payment">
    <header class="page-header">
        <div>
            <h1 class="page-title">Payment details</h1>
            <p class="text-muted">Confirm the booking summary and amount before continuing to checkout.</p>
        </div>
        <div class="header-actions">
            <a class="btn secondary" href="<?= URLROOT ?>/client/payments">Back to payments</a>
            <a class="btn secondary" href="<?= URLROOT ?>/client/c_upcomingBookings">Upcoming bookings</a>
        </div>
    </header>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="flash error"><?= htmlspecialchars((string) $_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="make-payment-notice" role="status">
        <i class="bx bx-wallet" aria-hidden="true"></i>
        <div>
            <strong><?= htmlspecialchars($paymentLabel) ?>:</strong>
            LKR <?= number_format($paymentAmount, 2) ?>
            <?php if ($recurringPayment): ?>
                <span class="text-muted"> — installment for this booking.</span>
            <?php else: ?>
                <span class="text-muted"> — due now to confirm your booking.</span>
            <?php endif; ?>
            <?php if ($paymentNote !== ''): ?>
                <div class="make-payment-notice__sub"><?= $recurringPayment ? $paymentNote : htmlspecialchars($paymentNote) ?></div>
            <?php endif; ?>
        </div>
    </div>
<<<<<<< HEAD
    </div>
</body>
=======
>>>>>>> 460c591ac37c19b771f06fef405fbc1364db34c4

    <div class="make-payment-grid">
        <section class="make-payment-card" aria-labelledby="make-pay-booking-heading">
            <h2 id="make-pay-booking-heading" class="make-payment-card__title">Booking information</h2>
            <dl class="admin-row-detail-modal__dl make-payment-dl">
                <dt>Caregiver</dt>
                <dd><?= htmlspecialchars((string) ($booking['caretaker_name'] ?? '—')) ?></dd>
                <dt>Service</dt>
                <dd><?= htmlspecialchars((string) ($booking['service_type'] ?? '—')) ?></dd>
                <dt>Status</dt>
                <dd><span class="status <?= client_booking_status_class($rawStatus) ?>"><?= htmlspecialchars($statusDisplay) ?></span></dd>
                <dt>Booking date</dt>
                <dd><?= $bkDate !== '' ? htmlspecialchars(date('Y-m-d', strtotime($bkDate))) : '—' ?></dd>
                <?php if ($showSvcStart): ?>
                    <dt>Service start</dt>
                    <dd><?= htmlspecialchars(date('Y-m-d', strtotime($svcStart))) ?></dd>
                <?php endif; ?>
                <dt>Preferred time</dt>
                <dd><?= htmlspecialchars((string) ($booking['preferred_time'] ?? '—')) ?></dd>
                <dt>Duration</dt>
                <dd><?= htmlspecialchars((string) (($booking['duration'] ?? '') . ' ' . ($booking['basis'] ?? ''))) ?></dd>
                <?php if ($district !== ''): ?>
                    <dt>Service area</dt>
                    <dd><?= htmlspecialchars($district) ?></dd>
                <?php endif; ?>
            </dl>
        </section>

        <section class="make-payment-card" aria-labelledby="make-pay-cost-heading">
            <h2 id="make-pay-cost-heading" class="make-payment-card__title">Cost summary</h2>
            <dl class="admin-row-detail-modal__dl make-payment-dl">
                <dt>Total for booking</dt>
                <dd class="make-payment-money">LKR <?= number_format((float) ($booking['total_payment'] ?? 0), 2) ?></dd>
                <dt><?= htmlspecialchars($paymentLabel) ?></dt>
                <dd class="make-payment-money make-payment-money--accent">LKR <?= number_format($paymentAmount, 2) ?></dd>
                <dt>Remaining after this payment</dt>
                <dd class="make-payment-money">LKR <?= number_format((float) $remainingBalance, 2) ?></dd>
                <?php
                $advRecorded = (float) ($booking['advance_amount'] ?? 0);
                if (!$recurringPayment && $advRecorded > 0):
                    ?>
                    <dt>Advance already recorded</dt>
                    <dd class="make-payment-money">LKR <?= number_format($advRecorded, 2) ?></dd>
                <?php endif; ?>
            </dl>
        </section>
    </div>

    <div class="make-payment-actions">
        <form method="get" action="<?= URLROOT ?>/client/c_payment" class="make-payment-actions__form">
            <input type="hidden" name="booking_id" value="<?= (int) ($booking['id'] ?? $booking['booking_id'] ?? 0) ?>">
            <?php if (!empty($recurringPayment['id'])): ?>
                <input type="hidden" name="recurring_payment_id" value="<?= (int) $recurringPayment['id'] ?>">
            <?php endif; ?>
            <button type="submit" class="btn">Continue to checkout</button>
        </form>
        <a href="<?= URLROOT ?>/client/c_upcomingBookings" class="btn secondary">Cancel</a>
    </div>
</main>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
