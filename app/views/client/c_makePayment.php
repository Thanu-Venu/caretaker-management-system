<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_makePayment.css">
</head>
<body>

<?php
$booking = $data['booking'] ?? [];
$calc = $data['payment_calc'] ?? null;

// Fallback: if controller didn't pass calc, try PaymentController (defensive)
if (!$calc && file_exists(APPROOT . '/controllers/PaymentController.php')) {
    require_once APPROOT . '/controllers/PaymentController.php';
    if (class_exists('PaymentController') && method_exists('PaymentController', 'calculateAdvanceFromBooking')) {
        $calc = PaymentController::calculateAdvanceFromBooking($booking);
    }
}

$advancePayment = isset($calc['advance']) ? $calc['advance'] : (!empty($booking['total_payment']) ? ($booking['total_payment'] * 0.5) : 0);
$remainingBalance = isset($calc['remaining']) ? $calc['remaining'] : (max(0, ($booking['total_payment'] ?? 0) - $advancePayment));
$advanceNotes = $calc['notes'] ?? '';
?>

<div class="payment-container">
    <h1>Payment Details</h1>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="booking-details">
        <h2>Booking Information</h2>
        <div class="detail-row">
            <span class="label">Caretaker Name:</span>
            <span class="value"><?= htmlspecialchars($booking['caretaker_name'] ?? 'N/A') ?></span>
        </div>

        <div class="detail-row">
            <span class="label">Service Type:</span>
            <span class="value"><?= htmlspecialchars($booking['service_type'] ?? 'N/A') ?></span>
        </div>

        <div class="detail-row">
            <span class="label">Booking Date:</span>
            <span class="value"><?= $booking['booking_date'] ?? 'N/A' ?></span>
        </div>

        <div class="detail-row">
            <span class="label">Preferred Time:</span>
            <span class="value"><?= htmlspecialchars($booking['preferred_time'] ?? 'N/A') ?></span>
        </div>

        <div class="detail-row">
            <span class="label">Duration:</span>
            <span class="value"><?= ($booking['duration'] ?? 0) . ' ' . htmlspecialchars($booking['basis'] ?? '') ?></span>
        </div>
    </div>

    <div class="cost-breakdown">
        <h2>Cost Breakdown</h2>

        <div class="cost-row">
            <span class="label">Total Cost:</span>
            <span class="value">Rs. <?= number_format($booking['total_payment'] ?? 0, 2) ?></span>
        </div>

        <div class="cost-row highlight">
            <span class="label">Advance Payment:</span>
            <span class="value">Rs. <?= number_format($advancePayment, 2) ?></span>
        </div>

        <div class="cost-row">
            <span class="label">Remaining Balance:</span>
            <span class="value">Rs. <?= number_format($remainingBalance, 2) ?></span>
        </div>

        <?php if (!empty($advanceNotes)): ?>
        <div class="cost-row">
            <span class="label">Note:</span>
            <span class="value"><?= htmlspecialchars($advanceNotes) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="button-container">
        <form method="get" action="<?= URLROOT ?>/client/c_payment">
            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?? $booking['booking_id'] ?? '' ?>">
            <button type="submit" class="btn btn-success">Make Payment</button>
        </form>
        <a href="<?= URLROOT ?>/client/c_upcomingBookings" class="btn btn-secondary">Cancel</a>
    </div>
</div>
</body>
</html>