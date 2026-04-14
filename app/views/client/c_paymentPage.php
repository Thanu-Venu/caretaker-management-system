<?php
$clientPageTitle = 'Payment details — SmartCare';
$clientExtraCss  = ['client/c_paymentPage.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';
?>

<main class="main-content">
    <header class="page-header">
        <div>
            <h1 class="page-title">Payment details</h1>
            <p class="text-muted">Demo card form (sandbox flow).</p>
        </div>
    </header>

    <div class="form-section form-section--wide">
        <form id="paymentForm">
            <div class="field">
                <label for="cardNumber">Card number</label>
                <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" required>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label for="expiry">Expiry</label>
                    <input type="text" id="expiry" placeholder="MM/YY" required>
                </div>
                <div class="field">
                    <label for="cvv">CVV</label>
                    <input type="password" id="cvv" placeholder="123" required>
                </div>
            </div>
            <div class="field">
                <label for="name">Cardholder name</label>
                <input type="text" id="name" placeholder="John Doe" required>
            </div>
            <div class="form-actions">
                <a class="btn" href="<?= URLROOT ?>/public?url=client/c_paymentSuccess">Pay now</a>
                <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_upcomingBookings">Cancel</a>
            </div>
        </form>
    </div>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
