<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Refund Management</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/hr/hr_tables.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/hr/hr_refunds.css">
</head>

<body>
    <main class="content">
        <h1>Refund Management</h1>

        <?php if (!empty($_SESSION['success'])): ?>
            <p class="success-msg"><?= htmlspecialchars($_SESSION['success']);
                                    unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <p class="error-msg"><?= htmlspecialchars($_SESSION['error']);
                                    unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <div class="refund-stats">
            <?php
            $pendingCount = 0;
            $approvedCount = 0;
            $totalRefundAmount = 0;

            foreach ($data['refunds'] as $r) {
                if ($r['status'] === 'pending') $pendingCount++;
                if ($r['status'] === 'approved' || $r['status'] === 'processed' || $r['status'] === 'completed') {
                    $approvedCount++;
                    $totalRefundAmount += $r['refund_amount'];
                }
            }
            ?>

            <div class="stat-card">
                <h3>Pending Refunds</h3>
                <div class="amount"><?= $pendingCount ?></div>
            </div>

            <div class="stat-card">
                <h3>Approved Refunds</h3>
                <div class="amount"><?= $approvedCount ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Refund Amount</h3>
                <div class="amount">LKR <?= number_format($totalRefundAmount, 2) ?></div>
            </div>
        </div>

        <div class="filter-tabs">
            <a href="<?= URLROOT ?>/hr/refunds?status=all" class="<?= $data['status_filter'] === 'all' ? 'active' : '' ?>">All</a>
            <a href="<?= URLROOT ?>/hr/refunds?status=pending" class="<?= $data['status_filter'] === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="<?= URLROOT ?>/hr/refunds?status=approved" class="<?= $data['status_filter'] === 'approved' ? 'active' : '' ?>">Approved</a>
            <a href="<?= URLROOT ?>/hr/refunds?status=completed" class="<?= $data['status_filter'] === 'completed' ? 'active' : '' ?>">Completed</a>
            <a href="<?= URLROOT ?>/hr/refunds?status=declined" class="<?= $data['status_filter'] === 'declined' ? 'active' : '' ?>">Declined</a>
        </div>

        <?php if (empty($data['refunds'])): ?>
            <p class="no-data">No refund records found.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Refund ID</th>
                            <th>Booking ID</th>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Cancellation Type</th>
                            <th>Total Paid</th>
                            <th>Refund Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['refunds'] as $refund): ?>
                            <tr>
                                <td><?= $refund['id'] ?></td>
                                <td><?= $refund['booking_id'] ?></td>
                                <td><?= htmlspecialchars($refund['client_name']) ?></td>
                                <td><?= htmlspecialchars($refund['service_type']) ?></td>
                                <td><?= ucwords(str_replace('_', ' ', $refund['cancellation_type'])) ?></td>
                                <td>LKR <?= number_format($refund['total_paid'], 2) ?></td>
                                <td>LKR <?= number_format($refund['refund_amount'], 2) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $refund['status'] ?>">
                                        <?= ucfirst($refund['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('Y-m-d H:i', strtotime($refund['created_at'])) ?></td>
                                <td class="action-buttons">
                                    <a href="<?= URLROOT ?>/hr/refundDetails?refund_id=<?= $refund['id'] ?>" class="btn-view">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>