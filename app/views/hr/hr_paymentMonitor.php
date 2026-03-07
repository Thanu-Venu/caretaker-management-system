<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Monitor</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_paymentMonitor.css">
</head>

<body>
    <?php
    $summary = $data['summary'] ?? [];
    $recurring = $data['recurring'] ?? [];
    $recent = $data['recent'] ?? [];
    $filters = $data['filters'] ?? [];

    $clientFilter = htmlspecialchars((string)($filters['client'] ?? ''));
    $recurringStatusFilter = (string)($filters['recurring_status'] ?? 'all');
    $paymentStatusFilter = (string)($filters['payment_status'] ?? 'all');
    $fromDateFilter = htmlspecialchars((string)($filters['from_date'] ?? ''));
    $toDateFilter = htmlspecialchars((string)($filters['to_date'] ?? ''));
    ?>

    <div class="payment-monitor-container">
        <h1>Payment Monitor</h1>
        <p class="subtitle">Track approvals, recurring dues, overdue balances, and payment activity.</p>

        <form method="get" action="<?= URLROOT ?>/hr/paymentMonitor" class="filter-form">
            <div class="filter-grid">
                <div class="filter-item">
                    <label for="client">Client Name</label>
                    <input type="text" id="client" name="client" value="<?= $clientFilter ?>" placeholder="Search client...">
                </div>
                <div class="filter-item">
                    <label for="recurring_status">Recurring Status</label>
                    <select id="recurring_status" name="recurring_status">
                        <option value="all" <?= $recurringStatusFilter === 'all' ? 'selected' : '' ?>>All</option>
                        <option value="pending" <?= $recurringStatusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= $recurringStatusFilter === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="overdue" <?= $recurringStatusFilter === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                        <option value="cancelled" <?= $recurringStatusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="payment_status">Payment Status</label>
                    <select id="payment_status" name="payment_status">
                        <option value="all" <?= $paymentStatusFilter === 'all' ? 'selected' : '' ?>>All</option>
                        <option value="pending" <?= $paymentStatusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $paymentStatusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $paymentStatusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="from_date">From Date</label>
                    <input type="date" id="from_date" name="from_date" value="<?= $fromDateFilter ?>">
                </div>
                <div class="filter-item">
                    <label for="to_date">To Date</label>
                    <input type="date" id="to_date" name="to_date" value="<?= $toDateFilter ?>">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="<?= URLROOT ?>/hr/paymentMonitor" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <p class="result-count">Showing <?= count($recurring) ?> recurring records and <?= count($recent) ?> payment records.</p>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Pending Approvals</h3>
                <p class="value"><?= (int)($summary['pending_approvals'] ?? 0) ?></p>
                <small>Rs. <?= number_format((float)($summary['amount_pending_approval'] ?? 0), 2) ?></small>
            </div>
            <div class="stat-card">
                <h3>Approved Payments</h3>
                <p class="value"><?= (int)($summary['approved_payments'] ?? 0) ?></p>
                <small>Rs. <?= number_format((float)($summary['amount_approved'] ?? 0), 2) ?></small>
            </div>
            <div class="stat-card">
                <h3>Rejected Payments</h3>
                <p class="value"><?= (int)($summary['rejected_payments'] ?? 0) ?></p>
                <small>Needs client follow-up</small>
            </div>
            <div class="stat-card">
                <h3>Recurring Pending</h3>
                <p class="value"><?= (int)($summary['pending_recurring'] ?? 0) ?></p>
                <small>Awaiting due-date payment</small>
            </div>
            <div class="stat-card warning">
                <h3>Recurring Overdue</h3>
                <p class="value"><?= (int)($summary['overdue_recurring'] ?? 0) ?></p>
                <small>Rs. <?= number_format((float)($summary['amount_overdue'] ?? 0), 2) ?></small>
            </div>
            <div class="stat-card success">
                <h3>Recurring Paid</h3>
                <p class="value"><?= (int)($summary['paid_recurring'] ?? 0) ?></p>
                <small>Installments closed</small>
            </div>
        </div>

        <section class="table-section">
            <h2>Recurring Payment Tracker</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Client</th>
                            <th>Caretaker</th>
                            <th>Service</th>
                            <th>Cycle</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Ref</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recurring)): ?>
                            <tr>
                                <td colspan="9">No recurring payment records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recurring as $row): ?>
                                <tr>
                                    <td>#<?= (int)$row['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($row['client_name']) ?></td>
                                    <td><?= htmlspecialchars($row['caretaker_name']) ?></td>
                                    <td><?= htmlspecialchars($row['service_type']) ?> (<?= htmlspecialchars($row['basis']) ?>)</td>
                                    <td>#<?= (int)$row['cycle_number'] ?> (<?= htmlspecialchars($row['cycle_type']) ?>)</td>
                                    <td><?= htmlspecialchars($row['due_date']) ?></td>
                                    <td>Rs. <?= number_format((float)$row['amount'], 2) ?></td>
                                    <td><span class="badge badge-<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></span></td>
                                    <td><?= !empty($row['payment_id']) ? ('#' . (int)$row['payment_id']) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="table-section">
            <h2>Recent Payment Timeline</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Booking</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Approved At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent)): ?>
                            <tr>
                                <td colspan="10">No payment activity found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent as $row): ?>
                                <tr>
                                    <td>#<?= (int)$row['id'] ?></td>
                                    <td>#<?= (int)$row['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($row['client_name']) ?></td>
                                    <td><?= ucfirst(str_replace('_', ' ', (string)$row['payment_type'])) ?></td>
                                    <td><?= ucfirst(str_replace('_', ' ', (string)$row['payment_method'])) ?></td>
                                    <td>Rs. <?= number_format((float)$row['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($row['due_date'] ?? '-') ?></td>
                                    <td><span class="badge badge-<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></span></td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                                    <td><?= htmlspecialchars($row['approved_at'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>

</html>