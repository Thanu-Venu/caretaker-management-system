<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<?php
$payment = $data['payment'] ?? [];

function esc($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function money($value)
{
    return 'LKR ' . number_format((float)$value, 2);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Detail</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_payments.css">
</head>

<body>
    <div class="payments-page">
        <div class="payments-header">
            <div>
                <h1>Payment #<?php echo esc($payment['payment_id'] ?? ''); ?></h1>
                <p>Full payment and booking visibility for admin review.</p>
            </div>
            <div class="header-actions">
                <a class="btn ghost" href="<?php echo URLROOT; ?>/admin/ad_payments">Back to Payments</a>
            </div>
        </div>

        <section class="detail-grid">
            <article class="detail-card">
                <h2>Payment Info</h2>
                <dl class="detail-list static">
                    <dt>Payment ID</dt>
                    <dd>#<?php echo esc($payment['payment_id'] ?? ''); ?></dd>
                    <dt>Booking ID</dt>
                    <dd>#<?php echo esc($payment['booking_id'] ?? ''); ?></dd>
                    <dt>Payment Type</dt>
                    <dd><?php echo esc(ucfirst($payment['payment_type'] ?? '')); ?></dd>
                    <dt>Payment Method</dt>
                    <dd><?php echo esc(str_replace('_', ' ', (string)($payment['payment_method'] ?? ''))); ?></dd>
                    <dt>Status</dt>
                    <dd><?php echo esc(ucfirst($payment['status'] ?? '')); ?></dd>
                    <dt>Amount</dt>
                    <dd><?php echo esc(money($payment['amount'] ?? 0)); ?></dd>
                    <dt>Remaining Balance</dt>
                    <dd><?php echo esc(money($payment['remaining_balance'] ?? 0)); ?></dd>
                    <dt>Total Booking Amount</dt>
                    <dd><?php echo esc(money($payment['total_booking_amount'] ?? 0)); ?></dd>
                    <dt>Customization Price</dt>
                    <dd><?php echo esc(money($payment['customization_price'] ?? 0)); ?></dd>
                    <dt>Created At</dt>
                    <dd><?php echo esc($payment['created_at'] ?? '-'); ?></dd>
                    <dt>Paid Date</dt>
                    <dd><?php echo esc($payment['paid_date'] ?? '-'); ?></dd>
                    <dt>Approved At</dt>
                    <dd><?php echo esc($payment['approved_at'] ?? '-'); ?></dd>
                </dl>
            </article>

            <article class="detail-card">
                <h2>Client & Caretaker</h2>
                <dl class="detail-list static">
                    <dt>Client</dt>
                    <dd><?php echo esc($payment['client_name'] ?? ''); ?></dd>
                    <dt>Client Email</dt>
                    <dd><?php echo esc($payment['client_email'] ?? '-'); ?></dd>
                    <dt>Client Phone</dt>
                    <dd><?php echo esc($payment['client_phone'] ?? '-'); ?></dd>
                    <dt>Caretaker</dt>
                    <dd><?php echo esc($payment['caretaker_name'] ?? ''); ?></dd>
                </dl>

                <h2>Booking Context</h2>
                <dl class="detail-list static">
                    <dt>Service Type</dt>
                    <dd><?php echo esc($payment['service_type'] ?? ''); ?></dd>
                    <dt>Basis</dt>
                    <dd><?php echo esc($payment['basis'] ?? ''); ?></dd>
                    <dt>Duration</dt>
                    <dd><?php echo esc($payment['duration'] ?? ''); ?></dd>
                    <dt>Booking Status</dt>
                    <dd><?php echo esc($payment['booking_status'] ?? ''); ?></dd>
                    <dt>Booking Date</dt>
                    <dd><?php echo esc($payment['booking_date'] ?? '-'); ?></dd>
                    <dt>Service Start Date</dt>
                    <dd><?php echo esc($payment['service_start_date'] ?? '-'); ?></dd>
                    <dt>Preferred Time</dt>
                    <dd><?php echo esc($payment['preferred_time'] ?? '-'); ?></dd>
                    <dt>Address</dt>
                    <dd><?php echo esc(trim(($payment['address_line1'] ?? '') . ' ' . ($payment['address_line2'] ?? '') . ', ' . ($payment['street'] ?? '') . ', ' . ($payment['district'] ?? '') . ' ' . ($payment['postal_code'] ?? ''))); ?></dd>
                    <dt>Customization Notes</dt>
                    <dd><?php echo esc($payment['customization'] ?? '-'); ?></dd>
                </dl>
            </article>
        </section>
    </div>
</body>

</html>