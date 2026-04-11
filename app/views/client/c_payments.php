<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Dashboard</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_payments.css">
</head>

<body>
    <?php
    $summary = $data['summary'] ?? [];
    $actionItems = $data['action_items'] ?? [];
    $bookingOverview = $data['booking_overview'] ?? [];
    $paymentHistory = $data['payment_history'] ?? [];
    $filters = $data['filters'] ?? [];

    $tab = $filters['tab'] ?? 'all';

    function statusClass($status)
    {
        $status = strtolower((string)$status);
        if (in_array($status, ['paid', 'approved'], true)) return 'status-paid';
        if (in_array($status, ['upcoming', 'pending'], true)) return 'status-upcoming';
        if (in_array($status, ['due_soon', 'advance_required'], true)) return 'status-due-soon';
        if ($status === 'overdue') return 'status-overdue';
        if (in_array($status, ['cancelled', 'rejected'], true)) return 'status-cancelled';
        return 'status-upcoming';
    }

    function statusLabel($row)
    {
        $status = strtolower((string)($row['payment_status'] ?? $row['status'] ?? ''));
        if ($status === 'advance_required') return 'Advance Required';
        if ($status === 'pending') {
            $days = (int)($row['days_delta'] ?? 99);
            if ($days <= 0) return 'Due Now';
            if ($days <= 7) return 'Due in ' . $days . ' day(s)';
            return 'Upcoming';
        }
        if ($status === 'overdue') {
            $days = abs((int)($row['days_delta'] ?? 0));
            return 'Overdue by ' . $days . ' day(s)';
        }
        if ($status === 'approved') return 'Completed';
        if ($status === 'paid') return 'Paid';
        if ($status === 'rejected') return 'Rejected';
        if ($status === 'cancelled') return 'Cancelled';
        return ucfirst($status);
    }
    ?>

    <div class="payments-page">
        <div class="page-title-wrap">
            <h1>Payment Dashboard</h1>
            <p>Manage all payment obligations across your service bookings.</p>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Pending Payments</h3>
                <p>LKR <?= number_format((float)($summary['pending_amount'] ?? 0), 2) ?></p>
            </div>
            <div class="summary-card">
                <h3>Payments Due This Week</h3>
                <p><?= (int)($summary['due_this_week_count'] ?? 0) ?></p>
            </div>
            <div class="summary-card">
                <h3>Overdue Payments</h3>
                <p><?= (int)($summary['overdue_count'] ?? 0) ?></p>
            </div>
            <div class="summary-card">
                <h3>Total Paid This Month</h3>
                <p>LKR <?= number_format((float)($summary['paid_this_month'] ?? 0), 2) ?></p>
            </div>
            <div class="summary-card">
                <h3>Active Bookings With Payments</h3>
                <p><?= (int)($summary['active_bookings_with_payments'] ?? 0) ?></p>
            </div>
        </div>

        <div class="tabs-wrap">
            <?php
            $tabs = [
                'all' => 'All Payments',
                'due' => 'Due',
                'upcoming' => 'Upcoming Payments',
                'history' => 'History'
            ];

            foreach ($tabs as $key => $label):
                $isActive = $tab === $key;
            ?>
                <a class="tab-link <?= $isActive ? 'active' : '' ?>" href="<?= URLROOT ?>/client/payments?tab=<?= urlencode($key) ?>">
                    <?= htmlspecialchars($label) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <form method="get" action="<?= URLROOT ?>/client/payments" class="filter-panel">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">

            <select name="service_type">
                <option value="all">All Services</option>
                <option value="Elder Care" <?= (($filters['service_type'] ?? '') === 'Elder Care') ? 'selected' : '' ?>>Elder Care</option>
                <option value="Babysitter" <?= (($filters['service_type'] ?? '') === 'Babysitter') ? 'selected' : '' ?>>Babysitter</option>
                <option value="Maid" <?= (($filters['service_type'] ?? '') === 'Maid') ? 'selected' : '' ?>>Maid</option>
            </select>

            <select name="booking_status">
                <option value="all">All Booking Status</option>
                <option value="Payment_Requested" <?= (($filters['booking_status'] ?? '') === 'Payment_Requested') ? 'selected' : '' ?>>Payment Requested</option>
                <option value="Advance_Paid" <?= (($filters['booking_status'] ?? '') === 'Advance_Paid') ? 'selected' : '' ?>>Advance Paid</option>
                <option value="Accepted" <?= (($filters['booking_status'] ?? '') === 'Accepted') ? 'selected' : '' ?>>Accepted</option>
                <option value="Completed" <?= (($filters['booking_status'] ?? '') === 'Completed') ? 'selected' : '' ?>>Completed</option>
            </select>

            <button type="submit">Apply</button>
            <a href="<?= URLROOT ?>/client/payments?tab=<?= urlencode($tab) ?>" class="reset-btn">Reset</a>
        </form>

        <?php if ($tab === 'history'): ?>
            <section class="section-card">
                <div class="section-header">
                    <h2>Payment History</h2>
                    <span><?= count($paymentHistory) ?> record(s)</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Service</th>
                                <th>Payment Date</th>
                                <th>Amount Paid</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($paymentHistory)): ?>
                                <tr>
                                    <td colspan="6">No payment history found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($paymentHistory as $h): ?>
                                    <tr>
                                        <td>#<?= (int)$h['booking_id'] ?></td>
                                        <td><?= htmlspecialchars($h['service_type']) ?> (<?= htmlspecialchars($h['basis']) ?>)</td>
                                        <td><?= htmlspecialchars($h['paid_at'] ?? '-') ?></td>
                                        <td>LKR <?= number_format((float)$h['amount'], 2) ?></td>
                                        <td><?= ucfirst(str_replace('_', ' ', (string)$h['payment_method'])) ?></td>
                                        <td>
                                            <span class="pill <?= statusClass($h['status'] ?? '') ?>">
                                                <?= htmlspecialchars(ucfirst((string)$h['status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php else: ?>
            <section class="section-card">
                <div class="section-header">
                    <h2>
                        <?php
                        if ($tab === 'due') {
                            echo 'Due Payments';
                        } elseif ($tab === 'upcoming') {
                            echo 'Upcoming Payments';
                        } else {
                            echo 'All Payments';
                        }
                        ?>
                    </h2>
                    <span><?= count($actionItems) ?> item(s)</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Service</th>
                                <th>Caretaker</th>
                                <th>Amount Due</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($actionItems)): ?>
                                <tr>
                                    <td colspan="7">No payments found for this tab.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($actionItems as $item): ?>
                                    <tr>
                                        <td>#<?= (int)$item['booking_id'] ?></td>
                                        <td><?= htmlspecialchars($item['service_type']) ?> (<?= htmlspecialchars($item['basis']) ?>)</td>
                                        <td><?= htmlspecialchars($item['caretaker_name']) ?></td>
                                        <td>LKR <?= number_format((float)$item['amount_due'], 2) ?></td>
                                        <td><?= htmlspecialchars($item['due_date'] ?? '-') ?></td>
                                        <td>
                                            <span class="pill <?= statusClass($item['payment_status'] ?? '') ?>">
                                                <?= htmlspecialchars(statusLabel($item)) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($item['can_pay_now'])): ?>
                                                <?php if (($item['source_type'] ?? '') === 'advance'): ?>
                                                    <a class="action-btn pay" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= (int)$item['booking_id'] ?>">Pay Now</a>
                                                <?php else: ?>
                                                    <a class="action-btn pay" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= (int)$item['booking_id'] ?>&recurring_payment_id=<?= (int)$item['recurring_payment_id'] ?>">Pay Now</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button class="action-btn disabled" disabled>Pay Not Available</button>
                                            <?php endif; ?>
                                            <a class="action-btn details" href="<?= URLROOT ?>/client/paymentDetails/<?= (int)$item['booking_id'] ?>">View Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </div>
</body>

</html>