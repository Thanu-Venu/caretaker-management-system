<?php
$hrPageTitle = 'Refund Management — HR';
$hrExtraCss  = ['hr/hr_refunds.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

$refunds = $data['refunds'] ?? [];
$statusFilter = $data['status_filter'] ?? 'all';

/**
 * @return string JSON for data-refund-row (safe in HTML attribute).
 */
function hr_refund_row_json(array $refund): string
{
    $out = $refund;
    $raw = $refund['refund_calculation'] ?? '';
    if (is_string($raw) && $raw !== '') {
        $decoded = json_decode($raw, true);
        $out['_calculation'] = is_array($decoded) ? $decoded : ['_raw' => $raw];
    } else {
        $out['_calculation'] = [];
    }

    return htmlspecialchars(
        json_encode($out, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
        ENT_QUOTES,
        'UTF-8'
    );
}

$pendingCount = 0;
$approvedCount = 0;
$totalRefundAmount = 0;
foreach ($refunds as $r) {
    if (($r['status'] ?? '') === 'pending') {
        $pendingCount++;
    }
    if (in_array($r['status'] ?? '', ['approved', 'processed', 'completed'], true)) {
        $approvedCount++;
        $totalRefundAmount += (float) ($r['refund_amount'] ?? 0);
    }
}

$basisMap = ['Daily' => 'Day', 'Monthly' => 'Month', 'Hourly' => 'Hour', 'Weekly' => 'Week', 'Yearly' => 'Year'];
?>

<main class="main-content">
    <header class="page-header">
        <h1 class="page-title">Refund Management</h1>
        <p class="page-subtitle">Refund lifecycle: <strong>Pending</strong> (request) → <strong>Approved</strong> (HR) → <strong>Completed</strong> (payout). <strong>Declined</strong> closes the case.</p>
    </header>

    <div class="refund-flow-legend" role="list">
        <div class="refund-flow-legend__step refund-flow-legend__step--1">
            <span class="refund-flow-legend__num">1</span>
            <span class="refund-flow-legend__label">Request</span>
            <small>Pending review</small>
        </div>
        <span class="refund-flow-legend__arrow" aria-hidden="true">→</span>
        <div class="refund-flow-legend__step refund-flow-legend__step--2">
            <span class="refund-flow-legend__num">2</span>
            <span class="refund-flow-legend__label">Approved</span>
            <small>Ready to pay out</small>
        </div>
        <span class="refund-flow-legend__arrow" aria-hidden="true">→</span>
        <div class="refund-flow-legend__step refund-flow-legend__step--3">
            <span class="refund-flow-legend__num">3</span>
            <span class="refund-flow-legend__label">Completed</span>
            <small>Funds recorded</small>
        </div>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success-message" role="status"><?= htmlspecialchars((string) $_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message" role="alert"><?= htmlspecialchars((string) $_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div id="hr-refunds-endpoints"
        data-process-url="<?= htmlspecialchars(URLROOT . '/hr/processRefund', ENT_QUOTES, 'UTF-8') ?>"
        data-complete-url="<?= htmlspecialchars(URLROOT . '/hr/completeRefund', ENT_QUOTES, 'UTF-8') ?>"
        hidden></div>

    <div class="refund-stats">
        <div class="stat-card">
            <h3>Pending refunds</h3>
            <p class="value"><?= (int) ($data['pending_count'] ?? $pendingCount) ?></p>
            <small>Awaiting HR decision</small>
        </div>
        <div class="stat-card">
            <h3>Approved / completed</h3>
            <p class="value"><?= (int) $approvedCount ?></p>
            <small>In this filtered list</small>
        </div>
        <div class="stat-card stat-card--accent">
            <h3>Refund total (filtered)</h3>
            <p class="value">LKR <?= number_format($totalRefundAmount, 2) ?></p>
            <small>Sum of refund amounts</small>
        </div>
    </div>

    <div class="filter-tabs" role="tablist">
        <a class="tab-button <?= $statusFilter === 'all' ? 'active' : '' ?>" href="<?= URLROOT ?>/hr/refunds?status=all">All</a>
        <a class="tab-button <?= $statusFilter === 'pending' ? 'active' : '' ?>" href="<?= URLROOT ?>/hr/refunds?status=pending">Pending</a>
        <a class="tab-button <?= $statusFilter === 'approved' ? 'active' : '' ?>" href="<?= URLROOT ?>/hr/refunds?status=approved">Approved</a>
        <a class="tab-button <?= $statusFilter === 'completed' ? 'active' : '' ?>" href="<?= URLROOT ?>/hr/refunds?status=completed">Completed</a>
        <a class="tab-button <?= $statusFilter === 'declined' ? 'active' : '' ?>" href="<?= URLROOT ?>/hr/refunds?status=declined">Declined</a>
    </div>

    <?php if (empty($refunds)): ?>
        <p class="no-data">No refund records found.</p>
    <?php else: ?>
        <div class="table-container">
            <table class="table booking-table refunds-table" data-table-collapse="off">
                <thead>
                    <tr>
                        <th>Client ID</th>
                        <th>Caregiver</th>
                        <th>Service type</th>
                        <th>Duration</th>
                        <th>Total paid</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($refunds as $refund): ?>
                        <?php
                        $st = strtolower((string) ($refund['status'] ?? ''));
                        $basis = $refund['basis'] ?? '—';
                        $displayBasis = $basisMap[$basis] ?? ucfirst((string) $basis);
                        $durationText = htmlspecialchars((string) ($refund['duration'] ?? '—'), ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($displayBasis, ENT_QUOTES, 'UTF-8');
                        $pendingActions = ($st === 'pending');
                        $canComplete = ($st === 'approved');
                        $caretakerLabel = htmlspecialchars((string) ($refund['caretaker_name'] ?? '—'), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr>
                            <td><?= (int) ($refund['client_id'] ?? 0) ?></td>
                            <td>
                                <span class="cell-muted">ID: —</span><br>
                                <span class="cell-strong"><?= $caretakerLabel ?></span>
                            </td>
                            <td><?= htmlspecialchars((string) ($refund['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $durationText ?></td>
                            <td><span class="amount-cell">LKR <?= number_format((float) ($refund['total_paid'] ?? 0), 2) ?></span></td>
                            <td>
                                <span class="status-pill" data-refund-status="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars(ucfirst((string) ($refund['status'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="actions refund-row-actions">
                                <button type="button"
                                    class="btn secondary btn-sm action-view-btn action-view-btn--icon js-refund-detail"
                                    data-refund-row="<?= hr_refund_row_json($refund) ?>"
                                    title="View details"
                                    aria-label="View refund and booking details">
                                    <i class="bx bx-show" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                    class="btn secondary btn-sm action-view-btn action-view-btn--icon js-refund-approve"
                                    data-refund-id="<?= (int) ($refund['id'] ?? 0) ?>"
                                    title="Approve refund"
                                    aria-label="Approve refund"
                                    <?= $pendingActions ? '' : 'disabled' ?>>
                                    <i class="bx bx-check" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                    class="btn secondary btn-sm action-view-btn action-view-btn--icon js-refund-decline"
                                    data-refund-id="<?= (int) ($refund['id'] ?? 0) ?>"
                                    title="Decline refund"
                                    aria-label="Decline refund"
                                    <?= $pendingActions ? '' : 'disabled' ?>>
                                    <i class="bx bx-x" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                    class="btn secondary btn-sm action-view-btn action-view-btn--icon js-refund-complete"
                                    data-refund-id="<?= (int) ($refund['id'] ?? 0) ?>"
                                    title="Mark completed (payout)"
                                    aria-label="Mark refund completed"
                                    <?= $canComplete ? '' : 'disabled' ?>>
                                    <i class="bx bx-check-double" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<!-- Detail modal (admin row-detail style) -->
<div id="refundDetailModal" class="modal admin-row-detail-modal" aria-hidden="true">
    <div class="modal-content admin-row-detail-modal__content refund-detail-modal__content" role="dialog" aria-modal="true"
        aria-labelledby="refundDetailTitle">
        <button type="button" class="modal-close admin-row-detail-modal__close" data-close-refund-modal aria-label="Close">
            <i class="bx bx-x" aria-hidden="true"></i>
        </button>
        <header class="admin-row-detail-modal__header">
            <span class="admin-row-detail-modal__header-icon" aria-hidden="true"><i class="bx bx-show"></i></span>
            <h3 id="refundDetailTitle" class="admin-row-detail-modal__title">Refund details</h3>
        </header>
        <dl class="admin-row-detail-modal__dl" id="refundDetailDl"></dl>
    </div>
</div>

<!-- Approve / decline confirm -->
<div id="refundProcessModal" class="modal" aria-hidden="true">
    <div class="modal-content refund-process-modal__content" role="dialog" aria-modal="true" aria-labelledby="refundProcessTitle">
        <h3 id="refundProcessTitle">Confirm</h3>
        <p id="refundProcessText"></p>
        <textarea id="refundProcessNotes" class="refund-modal-textarea" rows="3" placeholder="Notes (optional)"></textarea>
        <div class="modal-buttons">
            <button type="button" class="btn ghost" id="refundProcessCancel">Cancel</button>
            <button type="button" class="btn primary" id="refundProcessSubmit">Confirm</button>
        </div>
    </div>
</div>

<!-- Complete refund (payout) -->
<div id="refundCompleteModal" class="modal" aria-hidden="true">
    <div class="modal-content refund-complete-modal__content" role="dialog" aria-modal="true" aria-labelledby="refundCompleteTitle">
        <h3 id="refundCompleteTitle">Complete refund</h3>
        <p class="refund-complete-intro">Record how the refund was paid to the client.</p>
        <div class="field">
            <label for="refundCompleteMethod">Refund method <span class="required-mark">*</span></label>
            <select id="refundCompleteMethod" required>
                <option value="">Select method…</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cash">Cash</option>
                <option value="Check">Check</option>
                <option value="Card Reversal">Card Reversal</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="field">
            <label for="refundCompleteReference">Transaction reference</label>
            <input type="text" id="refundCompleteReference" placeholder="e.g. TXN123456789">
        </div>
        <div class="field">
            <label for="refundCompleteNotes">Notes</label>
            <textarea id="refundCompleteNotes" class="refund-modal-textarea" rows="2" placeholder="Optional processing notes"></textarea>
        </div>
        <div class="modal-buttons">
            <button type="button" class="btn ghost" id="refundCompleteCancel">Cancel</button>
            <button type="button" class="btn primary" id="refundCompleteSubmit">Mark completed</button>
        </div>
    </div>
</div>

<script src="<?= URLROOT ?>/public/js/hr/hr_refunds.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
