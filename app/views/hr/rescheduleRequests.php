<?php
$hrPageTitle = 'Reschedule requests — HR';
$hrExtraCss  = ['hr/hr_reschedule_requests.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

$rows = $data['reschedule_requests'] ?? [];
$pendingCount = (int) ($data['pending_count'] ?? 0);
$historyCount = (int) ($data['history_count'] ?? 0);
$statusFilter = (string) ($data['status_filter'] ?? 'all');
$totalRecords = $pendingCount + $historyCount;

$basisMap = ['Daily' => 'Day', 'Monthly' => 'Month', 'Hourly' => 'Hour', 'Weekly' => 'Week', 'Yearly' => 'Year'];
?>

<main class="main-content">
    <header class="page-header">
        <h1 class="page-title">Booking reschedule requests</h1>
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

    <div id="hr-reschedule-requests-endpoints"
        data-approve-url="<?= htmlspecialchars(URLROOT . '/hr/approveReschedule', ENT_QUOTES, 'UTF-8') ?>"
        data-reject-url="<?= htmlspecialchars(URLROOT . '/hr/rejectReschedule', ENT_QUOTES, 'UTF-8') ?>"
        hidden></div>

    <form method="get" action="<?= htmlspecialchars(URLROOT . '/hr/rescheduleRequests', ENT_QUOTES, 'UTF-8') ?>" class="hr-request-status-filter">
        <label for="reschedule-status-filter">Filter by status</label>
        <select name="status" id="reschedule-status-filter" class="form-input" onchange="this.form.submit()">
            <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All</option>
            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
    </form>

    <?php if (empty($rows)): ?>
        <p class="no-data"><?= $totalRecords > 0 && $statusFilter !== 'all'
            ? 'No reschedule requests match the selected status.'
            : 'No reschedule requests yet.' ?></p>
    <?php else: ?>
        <div class="table-container">
            <table class="table booking-table reschedule-requests-table" data-table-collapse="off">
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
                        $cgId = (int) ($req['booking_caretaker_id'] ?? 0);
                        ?>
                        <tr>
                            <td><?= (int) ($req['client_id'] ?? 0) ?></td>
                            <td><span class="cell-mono"><?= $cgId ?></span></td>
                            <td><?= htmlspecialchars((string) ($req['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $durationText ?></td>
                            <td><span class="amount-cell">LKR <?= number_format((float) ($req['total_payment'] ?? 0), 2) ?></span></td>
                            <td>
                                <span class="status-pill" data-reschedule-status="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars(ucfirst((string) ($req['status'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="actions rsActions">
                                <button type="button"
                                    class="btn secondary btn-sm iconBtn rsView"
                                    data-reschedule-row="<?= htmlspecialchars(json_encode($req, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>"
                                    title="View details"
                                    aria-label="View reschedule request details">
                                    <i class="bx bx-show" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                    class="btn secondary btn-sm iconBtn rsOK"
                                    data-request-id="<?= (int) ($req['request_id'] ?? 0) ?>"
                                    title="Approve reschedule"
                                    aria-label="Approve reschedule request"
                                    <?= $pendingActions ? '' : 'disabled' ?>>
                                    <i class="bx bx-check" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                    class="btn secondary btn-sm iconBtn rsStop"
                                    data-request-id="<?= (int) ($req['request_id'] ?? 0) ?>"
                                    title="Reject reschedule"
                                    aria-label="Reject reschedule request"
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

<div id="rescheduleDetailModal" class="modal readModal" aria-hidden="true">
    <div class="modal-content readPanel readWide" role="dialog" aria-modal="true"
        aria-labelledby="rescheduleDetailTitle">
        <button type="button" class="modal-close readClose" data-close-reschedule-detail aria-label="Close">
            <i class="bx bx-x" aria-hidden="true"></i>
        </button>
        <header class="readHead">
            <span class="readIcon" aria-hidden="true"><i class="bx bx-calendar-edit"></i></span>
            <h3 id="rescheduleDetailTitle" class="readTitle">Reschedule request</h3>
        </header>
        <dl class="pairList" id="rescheduleDetailDl"></dl>
    </div>
</div>

<div id="rescheduleApproveModal" class="modal" aria-hidden="true">
    <div class="modal-content reschedule-process-modal__content" role="dialog" aria-modal="true" aria-labelledby="rescheduleApproveTitle">
        <h3 id="rescheduleApproveTitle">Approve reschedule</h3>
        <p class="reschedule-modal-intro">The booking date will be updated to the requested date and the client and caregiver will be notified.</p>
        <label class="reschedule-modal-label" for="rescheduleApproveNote">HR note (optional, visible to client)</label>
        <textarea id="rescheduleApproveNote" class="reschedule-modal-textarea" rows="3" placeholder="Optional message…"></textarea>
        <div class="modal-buttons">
            <button type="button" class="btn ghost" id="rescheduleApproveCancel">Cancel</button>
            <button type="button" class="btn primary" id="rescheduleApproveSubmit">Approve</button>
        </div>
    </div>
</div>

<div id="rescheduleRejectModal" class="modal" aria-hidden="true">
    <div class="modal-content reschedule-process-modal__content" role="dialog" aria-modal="true" aria-labelledby="rescheduleRejectTitle">
        <h3 id="rescheduleRejectTitle">Reject reschedule</h3>
        <p class="reschedule-modal-intro">The booking keeps its current date. Please add a short note for the client.</p>
        <label class="reschedule-modal-label" for="rescheduleRejectNote">Reason / HR note <span class="required-mark">*</span></label>
        <textarea id="rescheduleRejectNote" class="reschedule-modal-textarea" rows="3" required placeholder="Required for rejection…"></textarea>
        <div class="modal-buttons">
            <button type="button" class="btn ghost" id="rescheduleRejectCancel">Cancel</button>
            <button type="button" class="btn primary" id="rescheduleRejectSubmit">Reject</button>
        </div>
    </div>
</div>

<script src="<?= URLROOT ?>/public/js/hr/hr_reschedule_requests.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
