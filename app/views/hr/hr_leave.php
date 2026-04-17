<?php
$hrPageTitle = 'Leave management — HR';
$hrExtraCss  = ['hr/hr_leave.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

$leaves = $data['leaves'] ?? [];
$page = (int) ($data['page'] ?? 1);
$totalPages = (int) ($data['totalPages'] ?? 1);

function hr_leave_page_url(int $p): string
{
    return URLROOT . '/HrLeave/index?' . http_build_query(['page' => max(1, $p)]);
}
?>

<main class="main-content">
    <header class="page-header">
        <h1 class="page-title">Leave management</h1>
    </header>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success-message" role="status"><?= htmlspecialchars((string) $_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message" role="alert"><?= htmlspecialchars((string) $_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div id="hr-leave-endpoints"
        data-reject-url="<?= htmlspecialchars(URLROOT . '/HrLeave/reject_submit', ENT_QUOTES, 'UTF-8') ?>"
        hidden></div>

    <div class="hr-request-status-filter hr-leave-filters">
        <div class="hr-leave-filters__group">
            <label for="leave-type-filter">Type</label>
            <select id="leave-type-filter" class="form-input" onchange="window.hrLeaveFilterTable && window.hrLeaveFilterTable()">
                <option value="All">All</option>
                <option value="Vacation">Vacation</option>
                <option value="Sick Leave">Sick Leave</option>
                <option value="Personal Leave">Personal Leave</option>
                <option value="Maternity Leave">Maternity Leave</option>
            </select>
        </div>
        <div class="hr-leave-filters__group">
            <label for="leave-status-filter">Status</label>
            <select id="leave-status-filter" class="form-input" onchange="window.hrLeaveFilterTable && window.hrLeaveFilterTable()">
                <option value="All">All</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <table class="table booking-table leave-table" id="leaveTable" data-table-collapse="off">
            <thead>
                <tr>
                    <th>Caregiver name</th>
                    <th>Leave type</th>
                    <th>Start date</th>
                    <th>End date</th>
                    <th>Replacement</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($leaves)): ?>
                    <?php foreach ($leaves as $leave): ?>
                        <?php
                        $st = strtolower((string) ($leave['status'] ?? ''));
                        $pendingActions = (($leave['status'] ?? '') === 'Pending');
                        ?>
                        <tr class="<?= !empty($leave['replacement_required']) ? 'row-impact' : '' ?>">
                            <td><?= htmlspecialchars((string) ($leave['caretaker_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($leave['leave_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($leave['start_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($leave['end_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?php if (!empty($leave['replacement_required'])): ?>
                                    <span class="leave-replacement-pill leave-replacement-pill--required">Required</span>
                                <?php else: ?>
                                    <span class="leave-replacement-pill leave-replacement-pill--na">Not required</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-pill" data-leave-status="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars((string) ($leave['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="actions lvActions">
                                <button type="button"
                                    class="btn secondary btn-sm iconBtn lvView"
                                    data-leave-row="<?= htmlspecialchars(json_encode($leave, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>"
                                    title="View details"
                                    aria-label="View leave details">
                                    <i class="bx bx-show" aria-hidden="true"></i>
                                </button>
                                <?php if ($pendingActions): ?>
                                    <a href="<?= htmlspecialchars(URLROOT . '/HrLeave/approve_form/' . (int) ($leave['id'] ?? 0), ENT_QUOTES, 'UTF-8') ?>"
                                        class="btn secondary btn-sm iconBtn"
                                        title="Approve (assign replacement if needed)"
                                        aria-label="Approve leave request">
                                        <i class="bx bx-check" aria-hidden="true"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="btn secondary btn-sm iconBtn dead" title="Already processed" aria-disabled="true">
                                        <i class="bx bx-check" aria-hidden="true"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="leave-table-empty">No leave requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <nav class="leave-pagination" aria-label="Leave list pages">
                <a href="<?= $page <= 1 ? '#' : htmlspecialchars(hr_leave_page_url($page - 1), ENT_QUOTES, 'UTF-8') ?>"
                    class="<?= $page <= 1 ? 'disabled' : '' ?>">Prev</a>
                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                if ($start > 1) {
                    echo '<a href="' . htmlspecialchars(hr_leave_page_url(1), ENT_QUOTES, 'UTF-8') . '">1</a>';
                    if ($start > 2) {
                        echo '<span class="pagination-ellipsis">…</span>';
                    }
                }
                for ($i = $start; $i <= $end; $i++) {
                    $active = ($i === $page) ? 'active' : '';
                    echo '<a class="' . $active . '" href="' . htmlspecialchars(hr_leave_page_url($i), ENT_QUOTES, 'UTF-8') . '">' . $i . '</a>';
                }
                if ($end < $totalPages) {
                    if ($end < $totalPages - 1) {
                        echo '<span class="pagination-ellipsis">…</span>';
                    }
                    echo '<a href="' . htmlspecialchars(hr_leave_page_url($totalPages), ENT_QUOTES, 'UTF-8') . '">' . $totalPages . '</a>';
                }
                ?>
                <a href="<?= $page >= $totalPages ? '#' : htmlspecialchars(hr_leave_page_url($page + 1), ENT_QUOTES, 'UTF-8') ?>"
                    class="<?= $page >= $totalPages ? 'disabled' : '' ?>">Next</a>
            </nav>
        <?php endif; ?>
    </div>
</main>

<div id="leaveDetailModal" class="modal readModal" aria-hidden="true">
    <div class="modal-content readPanel readWide" role="dialog" aria-modal="true"
        aria-labelledby="leaveDetailTitle">
        <button type="button" class="modal-close readClose" data-close-leave-detail aria-label="Close">
            <i class="bx bx-x" aria-hidden="true"></i>
        </button>
        <header class="readHead">
            <span class="readIcon" aria-hidden="true"><i class="bx bx-calendar-x"></i></span>
            <h3 id="leaveDetailTitle" class="readTitle">Leave request</h3>
        </header>
        <dl class="pairList" id="leaveDetailDl"></dl>
    </div>
</div>

<div id="leaveRejectModal" class="modal" aria-hidden="true">
    <div class="modal-content leave-reject-modal__content" role="dialog" aria-modal="true" aria-labelledby="leaveRejectTitle">
        <h3 id="leaveRejectTitle">Reject leave</h3>
        <p class="leave-reject-intro">The caregiver will be notified. Please enter a short reason.</p>
        <label class="leave-reject-label" for="leaveRejectNote">Reason <span class="required-mark">*</span></label>
        <textarea id="leaveRejectNote" class="leave-reject-textarea" rows="4" required placeholder="Required…"></textarea>
        <div class="modal-buttons">
            <button type="button" class="btn ghost" id="leaveRejectCancel">Cancel</button>
            <button type="button" class="btn primary" id="leaveRejectSubmit">Reject</button>
        </div>
    </div>
</div>

<script src="<?= URLROOT ?>/public/js/hr/hr_leave.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
