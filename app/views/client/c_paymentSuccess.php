<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare - Payment Successful</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_paymentSuccess.css">
</head>
<body>
    <div class="main-content">
        <div class="payment-success">
            <div class="check-icon">
                <i class='bx bx-check-circle'></i>
            </div>
            <h1>Payment Successful!</h1>
            <p>The payment was made successfully!</p>
            <?php $paymentId = $data['payment_id'] ?? null; ?>
            <button class="ok-btn"><a href="<?= URLROOT ?>/client/c_contactCT<?= $paymentId ? ('?payment_id=' . urlencode($paymentId)) : '' ?>">OK</a></button>
        </div>
    </div>
</body>
</html>
