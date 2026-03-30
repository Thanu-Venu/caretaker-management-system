<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Refund Details</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/hr/hr_tables.css">
    <style>
        main.content {
            padding: 30px;
            background: #f5f7fa;
            min-height: 100vh;
        }

        .refund-details-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .refund-details-container h1 {
            color: #2c3e50;
            font-size: 28px;
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

        .detail-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            transition: box-shadow 0.3s ease;
        }

        .detail-section:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .detail-section h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #2c3e50;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-section h2::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 18px 24px;
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: 600;
            color: #64748b;
            font-size: 14px;
        }

        .detail-value {
            color: #334155;
            font-size: 14px;
            line-height: 1.6;
        }

        .calculation-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 24px;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            margin-top: 20px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .calculation-box p {
            margin: 10px 0;
            font-size: 14px;
            color: #475569;
        }

        .calculation-box p strong {
            color: #334155;
        }

        .calculation-box .formula {
            background: white;
            padding: 16px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 12px 0;
            border: 1px solid #dee2e6;
            color: #495057;
            font-size: 13px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .refund-amount-highlight {
            font-size: 36px;
            font-weight: 700;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 20px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

        .action-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .action-section h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #2c3e50;
            font-weight: 600;
        }

        .action-form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-approve {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .btn-approve:hover {
            box-shadow: 0 4px 12px rgba(67, 233, 123, 0.4);
        }

        .btn-decline {
            background: linear-gradient(135deg, #ff7675 0%, #d63031 100%);
            color: white;
        }

        .btn-decline:hover {
            box-shadow: 0 4px 12px rgba(214, 48, 49, 0.4);
        }

        .btn-complete {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-complete:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-back {
            background: linear-gradient(135deg, #636e72 0%, #2d3436 100%);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .message-box {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 16px 20px;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
            margin: 15px 0;
            color: #1565c0;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(33, 150, 243, 0.15);
        }
    </style>
</head>

<body>
    <main class="content">
        <div class="refund-details-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1>Refund Details #<?= $data['refund']['id'] ?></h1>
                <a href="<?= URLROOT ?>/hr/refunds" class="btn btn-back">← Back to List</a>
            </div>

            <?php if (!empty($_SESSION['success'])): ?>
                <p class="success-msg"><?= htmlspecialchars($_SESSION['success']);
                                        unset($_SESSION['success']); ?></p>
            <?php endif; ?>

            <?php if (!empty($_SESSION['error'])): ?>
                <p class="error-msg"><?= htmlspecialchars($_SESSION['error']);
                                        unset($_SESSION['error']); ?></p>
            <?php endif; ?>

            <?php $refund = $data['refund']; ?>
            <?php $calc = $refund['calculation_details']; ?>

            <!-- Basic Information -->
            <div class="detail-section">
                <h2>Basic Information</h2>
                <div class="detail-grid">
                    <div class="detail-label">Refund ID:</div>
                    <div class="detail-value"><?= $refund['id'] ?></div>

                    <div class="detail-label">Booking ID:</div>
                    <div class="detail-value"><?= $refund['booking_id'] ?></div>

                    <div class="detail-label">Client:</div>
                    <div class="detail-value"><?= htmlspecialchars($refund['client_name']) ?> (<?= htmlspecialchars($refund['client_email']) ?>)</div>

                    <div class="detail-label">Service Type:</div>
                    <div class="detail-value"><?= htmlspecialchars($refund['service_type']) ?></div>

                    <div class="detail-label">Service Basis:</div>
                    <div class="detail-value"><?= ucfirst($refund['basis']) ?> (<?= $refund['duration'] ?> <?= $refund['basis'] ?>)</div>

                    <div class="detail-label">Booking Date:</div>
                    <div class="detail-value"><?= date('Y-m-d', strtotime($refund['booking_date'])) ?></div>

                    <div class="detail-label">Cancellation Type:</div>
                    <div class="detail-value"><?= ucwords(str_replace('_', ' ', $refund['cancellation_type'])) ?></div>

                    <div class="detail-label">Status:</div>
                    <div class="detail-value">
                        <span class="status-badge status-<?= $refund['status'] ?>">
                            <?= ucfirst($refund['status']) ?>
                        </span>
                    </div>

                    <div class="detail-label">Created:</div>
                    <div class="detail-value"><?= date('Y-m-d H:i', strtotime($refund['created_at'])) ?></div>
                </div>
            </div>

            <!-- Refund Calculation -->
            <div class="detail-section">
                <h2>Refund Calculation</h2>

                <div class="calculation-box">
                    <p><strong>Scenario:</strong> <?= htmlspecialchars($calc['scenario'] ?? 'N/A') ?></p>

                    <?php if (isset($calc['calculation'])): ?>
                        <p><strong>Calculation:</strong></p>
                        <div class="formula"><?= htmlspecialchars($calc['calculation']) ?></div>
                    <?php endif; ?>

                    <?php if (isset($calc['message'])): ?>
                        <div class="message-box">
                            <?= htmlspecialchars($calc['message']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="detail-grid" style="margin-top: 12px; margin-bottom: 0;">
                        <?php if (isset($calc['advance_paid'])): ?>
                            <div class="detail-label">Advance Paid:</div>
                            <div class="detail-value">LKR <?= number_format((float)$calc['advance_paid'], 2) ?></div>
                        <?php endif; ?>

                        <?php if (isset($calc['total_paid'])): ?>
                            <div class="detail-label">Total Paid:</div>
                            <div class="detail-value">LKR <?= number_format((float)$calc['total_paid'], 2) ?></div>
                        <?php endif; ?>

                        <?php if (isset($calc['approved_payments']) || isset($calc['pending_payments'])): ?>
                            <!-- Payment Breakdown -->
                            <?php if (isset($calc['approved_payments'])): ?>
                                <div class="detail-label">└ Approved Payments:</div>
                                <div class="detail-value" style="color: #28a745;">LKR <?= number_format((float)$calc['approved_payments'], 2) ?></div>
                            <?php endif; ?>

                            <?php if (isset($calc['pending_payments']) && $calc['pending_payments'] > 0): ?>
                                <div class="detail-label">└ Pending Payments:</div>
                                <div class="detail-value" style="color: #ffa500; font-weight: 600;">
                                    LKR <?= number_format((float)$calc['pending_payments'], 2) ?>
                                    <span style="font-size: 11px; background: #fff3cd; padding: 2px 8px; border-radius: 4px; color: #856404;">UNAPPROVED</span>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (isset($calc['monthly_rate']) && $calc['monthly_rate'] !== null): ?>
                            <div class="detail-label">Monthly Rate:</div>
                            <div class="detail-value">LKR <?= number_format((float)$calc['monthly_rate'], 2) ?></div>
                        <?php endif; ?>

                        <?php if (isset($calc['months_used']) && $calc['months_used'] !== null): ?>
                            <div class="detail-label">Months Charged as Used:</div>
                            <div class="detail-value"><?= (int)$calc['months_used'] ?></div>
                        <?php endif; ?>

                        <?php if (isset($calc['daily_rate'])): ?>
                            <div class="detail-label">Daily Rate:</div>
                            <div class="detail-value">LKR <?= number_format((float)$calc['daily_rate'], 2) ?></div>
                        <?php endif; ?>

                        <?php if (isset($calc['days_used'])): ?>
                            <div class="detail-label">Days Charged as Used:</div>
                            <div class="detail-value"><?= (int)$calc['days_used'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-grid" style="margin-top: 20px;">
                    <div class="detail-label">Total Paid:</div>
                    <div class="detail-value">LKR <?= number_format($refund['total_paid'], 2) ?></div>

                    <div class="detail-label">Service Used Amount:</div>
                    <div class="detail-value">LKR <?= number_format($refund['service_used_amount'], 2) ?></div>

                    <div class="detail-label">Cancellation Fee:</div>
                    <div class="detail-value">LKR <?= number_format($refund['cancellation_fee'], 2) ?></div>
                </div>

                <div style="text-align: center;">
                    <div class="detail-label">Refund Amount:</div>
                    <div class="refund-amount-highlight">LKR <?= number_format($refund['refund_amount'], 2) ?></div>
                </div>
            </div>

            <!-- Approval Information (if approved) -->
            <?php if ($refund['approved_by']): ?>
                <div class="detail-section">
                    <h2>Approval Information</h2>
                    <div class="detail-grid">
                        <div class="detail-label">Approved By:</div>
                        <div class="detail-value"><?= htmlspecialchars($refund['approved_by_name'] ?? 'N/A') ?></div>

                        <div class="detail-label">Approved At:</div>
                        <div class="detail-value"><?= date('Y-m-d H:i', strtotime($refund['approved_at'])) ?></div>

                        <?php if ($refund['admin_notes']): ?>
                            <div class="detail-label">Admin Notes:</div>
                            <div class="detail-value"><?= nl2br(htmlspecialchars($refund['admin_notes'])) ?></div>
                        <?php endif; ?>

                        <?php if ($refund['refund_method']): ?>
                            <div class="detail-label">Refund Method:</div>
                            <div class="detail-value"><?= htmlspecialchars($refund['refund_method']) ?></div>
                        <?php endif; ?>

                        <?php if ($refund['refund_reference']): ?>
                            <div class="detail-label">Reference:</div>
                            <div class="detail-value"><?= htmlspecialchars($refund['refund_reference']) ?></div>
                        <?php endif; ?>

                        <?php if ($refund['processed_at']): ?>
                            <div class="detail-label">Processed At:</div>
                            <div class="detail-value"><?= date('Y-m-d H:i', strtotime($refund['processed_at'])) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Section -->
            <?php if ($refund['status'] === 'pending'): ?>
                <div class="action-section">
                    <h2>Process Refund</h2>
                    <form method="POST" action="<?= URLROOT ?>/hr/processRefund" class="action-form">
                        <input type="hidden" name="refund_id" value="<?= $refund['id'] ?>">

                        <div class="form-group">
                            <label>Notes (optional)</label>
                            <textarea name="notes" placeholder="Add any notes about this decision..."></textarea>
                        </div>

                        <div class="button-group">
                            <button type="submit" name="action" value="approve" class="btn btn-approve">
                                ✓ Approve Refund
                            </button>
                            <button type="submit" name="action" value="decline" class="btn btn-decline">
                                ✗ Decline Refund
                            </button>
                        </div>
                    </form>
                </div>
            <?php elseif ($refund['status'] === 'approved'): ?>
                <div class="action-section">
                    <h2>Complete Refund Processing</h2>
                    <form method="POST" action="<?= URLROOT ?>/hr/completeRefund" class="action-form">
                        <input type="hidden" name="refund_id" value="<?= $refund['id'] ?>">

                        <div class="form-group">
                            <label>Refund Method <span style="color: red;">*</span></label>
                            <select name="refund_method" required>
                                <option value="">Select method...</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="Check">Check</option>
                                <option value="Card Reversal">Card Reversal</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Transaction Reference</label>
                            <input type="text" name="refund_reference" placeholder="e.g., TXN123456789">
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" placeholder="Add any processing notes..."></textarea>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn btn-complete">
                                Mark as Completed
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>