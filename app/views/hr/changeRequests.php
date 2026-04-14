<?php
$hrPageTitle = 'Caregiver change requests — HR';
$hrExtraCss  = ['hr/hr_change_requests.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

$rows = $data['change_requests'] ?? [];
$pendingCount = (int) ($data['pending_count'] ?? 0);
$historyCount = (int) ($data['history_count'] ?? 0);
$statusFilter = (string) ($data['status_filter'] ?? 'all');
$totalRecords = $pendingCount + $historyCount;

$basisMap = ['Daily' => 'Day', 'Monthly' => 'Month', 'Hourly' => 'Hour', 'Weekly' => 'Week', 'Yearly' => 'Year'];
?>

<main class="main-content">
    <header class="page-header">
        <h1 class="page-title">Caregiver change requests</h1>
        <p class="page-subtitle">
            <span class="page-subtitle__counts"><?= (int) $pendingCount ?> pending</span>
            <span class="page-subtitle__sep">·</span>
            <span class="page-subtitle__counts"><?= (int) $historyCount ?> in history</span>
        </p>
    </header>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success-message" role="status"><?= htmlspecialchars((string) $_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message" role="alert"><?= htmlspecialchars((string) $_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div id="hr-change-requests-endpoints"
        data-approve-url="<?= htmlspecialchars(URLROOT . '/hr/approveChange', ENT_QUOTES, 'UTF-8') ?>"
        data-reject-url="<?= htmlspecialchars(URLROOT . '/hr/rejectChange', ENT_QUOTES, 'UTF-8') ?>"
        hidden></div>

    <form method="get" action="<?= htmlspecialchars(URLROOT . '/hr/changeRequests', ENT_QUOTES, 'UTF-8') ?>" class="hr-request-status-filter">
        <label for="change-request-status-filter">Filter by status</label>
        <select name="status" id="change-request-status-filter" class="form-input" onchange="this.form.submit()">
            <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All</option>
            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
    </form>

    <?php if (empty($rows)): ?>
        <p class="no-data"><?= $totalRecords > 0 && $statusFilter !== 'all'
            ? 'No change requests match the selected status.'
            : 'No change requests yet.' ?></p>
    <?php else: ?>
        <div class="table-container">
            <table class="table booking-table change-requests-table" data-table-collapse="off">
                <thead>
                    <tr>
                        <th>Client ID</th>
                        <th>Caregiver ID</th>
                        <th>Service type</th>
                        <th>Duration</th>
                        <th>Total amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $req): ?>
                        <?php
                        $st = strtolower((string) ($req['status'] ?? ''));
                        $pendingActions = ($st === 'pending');
                        $basis = $req['basis'] ?? '—';
                        $displayBasis = $basisMap[$basis] ?? ucfirst((string) $basis);
                        $durationText = htmlspecialchars((string) ($req['duration'] ?? '—'), ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($displayBasis, ENT_QUOTES, 'UTF-8');
                        $oldId = (int) ($req['old_caretaker_id'] ?? 0);
                        $newId = (int) ($req['new_caretaker_id'] ?? 0);
                        $cgIds = $oldId . ' → ' . $newId;
                        ?>
                        <tr>
                            <td><?= (int) ($req['client_id'] ?? 0) ?></td>
                            <td><span class="cell-mono"><?= htmlspecialchars($cgIds, ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td><?= htmlspecialchars((string) ($req['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $durationText ?></td>
                            <td><span class="amount-cell">LKR <?= number_format((float) ($req['total_payment'] ?? 0), 2) ?></span></td>
                            <td>
                                <span class="status-pill" data-change-status="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars(ucfirst((string) ($req['status'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="actions change-request-row-actions">
                                <button type="button"
                                    class="btn secondary btn-sm action-view-btn action-view-btn--icon js-change-detail"
                                    data-change-row="<?= htmlspecialchars(json_encode($req, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>"
                                    title="View details"
                                    aria-label="View change request details">
                                    <i class="bx bx-show" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                    class="btn secondary btn-sm action-view-btn action-view-btn--icon js-change-approve"
                                    data-request-id="<?= (int) ($req['request_id'] ?? 0) ?>"
                                    title="Approve request"
                                    aria-label="Approve caregiver change"
                                    <?= $pendingActions ? '' : 'disabled' ?>>
                                    <i class="bx bx-check" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                    class="btn secondary btn-sm action-view-btn action-view-btn--icon js-change-reject"
                                    data-request-id="<?= (int) ($req['request_id'] ?? 0) ?>"
                                    title="Reject request"
                                    aria-label="Reject caregiver change"
                                    <?= $pendingActions ? '' : 'disabled' ?>>
                                    <i class="bx bx-x" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<div id="changeRequestDetailModal" class="modal admin-row-detail-modal" aria-hidden="true">
    <div class="modal-content admin-row-detail-modal__content change-request-detail-modal__content" role="dialog" aria-modal="true"
        aria-labelledby="changeRequestDetailTitle">
        <button type="button" class="modal-close admin-row-detail-modal__close" data-close-change-detail aria-label="Close">
            <i class="bx bx-x" aria-hidden="true"></i>
        </button>
        <header class="admin-row-detail-modal__header">
            <span class="admin-row-detail-modal__header-icon" aria-hidden="true"><i class="bx bx-show"></i></span>
            <h3 id="changeRequestDetailTitle" class="admin-row-detail-modal__title">Change request</h3>
        </header>
        <dl class="admin-row-detail-modal__dl" id="changeRequestDetailDl"></dl>
    </div>
</div>

<div id="changeRequestApproveModal" class="modal" aria-hidden="true">
    <div class="modal-content change-request-process-modal__content" role="dialog" aria-modal="true" aria-labelledby="changeApproveTitle">
        <h3 id="changeApproveTitle">Approve change</h3>
        <p class="change-request-modal-intro">The booking caregiver will be updated and notifications sent.</p>
        <label class="change-request-modal-label" for="changeApproveNote">HR note (optional, visible to client)</label>
        <textarea id="changeApproveNote" class="change-request-modal-textarea" rows="3" placeholder="Optional message…"></textarea>
        <div class="modal-buttons">
            <button type="button" class="btn ghost" id="changeApproveCancel">Cancel</button>
            <button type="button" class="btn primary" id="changeApproveSubmit">Approve</button>
        </div>
    </div>
</div>

<div id="changeRequestRejectModal" class="modal" aria-hidden="true">
    <div class="modal-content change-request-process-modal__content" role="dialog" aria-modal="true" aria-labelledby="changeRejectTitle">
        <h3 id="changeRejectTitle">Reject change</h3>
        <p class="change-request-modal-intro">The booking stays with the current caregiver. Please add a short note for the client.</p>
        <label class="change-request-modal-label" for="changeRejectNote">Reason / HR note <span class="required-mark">*</span></label>
        <textarea id="changeRejectNote" class="change-request-modal-textarea" rows="3" required placeholder="Required for rejection…"></textarea>
        <div class="modal-buttons">
            <button type="button" class="btn ghost" id="changeRejectCancel">Cancel</button>
            <button type="button" class="btn primary" id="changeRejectSubmit">Reject</button>
        </div>
    </div>
</div>

<script src="<?= URLROOT ?>/public/js/hr/hr_change_requests.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
