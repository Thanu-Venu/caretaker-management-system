<?php
$hrPageTitle = 'Refund details — HR';
$hrExtraCss  = ['hr/hr_tables.css', 'hr/hr_refund_details.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main class="main-content">
    <div class="hr-refund-details-inner">
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
                                <div class="detail-label">Approved Payments:</div>
                                <div class="detail-value" style="color: #00cf30; font-weight: 600;">
                                    LKR <?= number_format((float)$calc['approved_payments'], 2) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($calc['pending_payments']) && $calc['pending_payments'] > 0): ?>
                                <div class="detail-label">Pending Payments:</div>
                                <div class="detail-value" style="color: #f09c00; font-weight: 600;">
                                    LKR <?= number_format((float)$calc['pending_payments'], 2) ?>
                                    <span style="font-size: 13px; background: #fff3cd; padding: 4px 10px; border-radius: 4px; color: #856404;">UNAPPROVED</span>
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
    </div>
</main>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
