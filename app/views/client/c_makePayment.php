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
    <div class="container">
        <div class="main-content">
            <div class="booking-card">
                <h2>Book Sarah Johnson</h2>
                <div class="booking-details">
                    <div class="detail-row">
                        <span class="label">Service:</span>
                        <span class="value">Elder Care</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Duration:</span>
                        <span class="value">3 Months</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Availability:</span>
                        <span class="value">Morning</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Rate:</span>
                        <span class="value">Rs. 15000</span>
                    </div>
                    <div class="detail-row total-cost">
                        <span class="label">Total Cost:</span>
                        <span class="value">Rs. 45000</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Advance Payment:</span>
                        <span class="value">Rs. 22500</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Remaining Balance:</span>
                        <span class="value">Rs. 22500</span>
                    </div>
                </div>
                <button class="confirm-btn"onclick="window.location.href='?url=client/c_paymentPage'">Make Payment</button>
            </div>
        </div>
    </div>
</body>
</html>