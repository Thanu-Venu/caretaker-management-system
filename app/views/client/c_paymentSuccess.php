<?php
$clientPageTitle = 'Payment successful — SmartCare';
$clientExtraCss  = ['client/c_paymentSuccess.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$paymentId = $data['payment_id'] ?? null;
?>
<main class="main-content payment-success-page">
    <section class="payment-success" aria-labelledby="paymentSuccessTitle">
        <div class="payment-success__badge" aria-hidden="true">
            <i class="bx bx-check-circle"></i>
        </div>

        <p class="payment-success__eyebrow">Payment confirmed</p>
        <h1 id="paymentSuccessTitle" class="page-title payment-success__title">Your payment was successful</h1>
        <p class="payment-success__lead text-muted">Thank you. We have received your payment and updated your booking status.</p>

        <div class="payment-success__meta" role="status" aria-live="polite">
            <p class="payment-success__meta-note">The Continue button will show your current or before-caretaker details for now. After HR approves this payment, you can view the updated caretaker details.</p>
        </div>

        <ul class="payment-success__next-steps" aria-label="What happens next">
            <li>Our team will continue processing your booking workflow.</li>
            <li>You can review full payment details and due dates at any time.</li>
            <li>Need assistance? Use Continue to view the before-caregiver contact details until HR approval is completed.</li>
        </ul>

        <div class="header-actions payment-success-actions">
            <a class="btn" href="<?= URLROOT ?>/client/c_upcomingBookings">Continue</a>
            <a class="btn secondary" href="<?= URLROOT ?>/client/payments">View payments</a>
            <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_dashboard">Back to dashboard</a>
        </div>
    </section>
</main>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
