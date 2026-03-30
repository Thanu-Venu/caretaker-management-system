<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Refund Management</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/hr/hr_tables.css">
    <style>
        .content {
            margin-left: 250px;
            padding: 30px;
            background: #f5f7fa;
            min-height: 100vh;
        }

        .content h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .success-msg,
        .error-msg {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideIn 0.3s ease-out;
        }

        .success-msg {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .error-msg {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .refund-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }

        .stat-card:nth-child(1) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-card h3 {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 12px;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .amount {
            font-size: 32px;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            background: white;
            padding: 8px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .filter-tabs a {
            padding: 12px 24px;
            text-decoration: none;
            color: #64748b;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .filter-tabs a:hover {
            background: #f1f5f9;
            color: #334155;
        }

        .filter-tabs a.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .table-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .data-table th {
            padding: 16px;
            text-align: left;
            color: white;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        .data-table tbody tr:last-child {
            border-bottom: none;
        }

        .data-table td {
            padding: 16px;
            color: #475569;
            font-size: 14px;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-pending {
            background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
            color: #856404;
            box-shadow: 0 2px 8px rgba(253, 203, 110, 0.3);
        }

        .status-approved {
            background: linear-gradient(135deg, #a8e6ff 0%, #74b9ff 100%);
            color: #0c5460;
            box-shadow: 0 2px 8px rgba(116, 185, 255, 0.3);
        }

        .status-declined {
            background: linear-gradient(135deg, #ff7675 0%, #d63031 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(214, 48, 49, 0.3);
        }

        .status-processed,
        .status-completed {
            background: linear-gradient(135deg, #55efc4 0%, #00b894 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 184, 148, 0.3);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-view {
            padding: 8px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            color: #94a3b8;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
    </style>
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