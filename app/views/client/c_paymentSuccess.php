<?php
$clientPageTitle = 'Payment successful — SmartCare';
$clientExtraCss  = ['client/c_paymentSuccess.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$paymentId = $data['payment_id'] ?? null;
?>
<main class="main-content payment-success-page">
    <div class="payment-success">
        <div class="check-icon">
            <i class="bx bx-check-circle" aria-hidden="true"></i>
        </div>
        <h1 class="page-title">Payment successful</h1>
        <p class="text-muted">Your payment was processed successfully.</p>
        <div class="header-actions payment-success-actions">
            <a class="btn" href="<?= URLROOT ?>/client/c_contactCT<?= $paymentId ? ('?payment_id=' . urlencode((string) $paymentId)) : '' ?>">Continue</a>
        </div>
    </div>
</main>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
