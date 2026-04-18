<?php
$summary = $data['monthlySummary'] ?? ['limit' => 5, 'used' => 0, 'remaining' => 5, 'percentage' => 0, 'label' => '0 / 5 days used'];
$success = $data['success'] ?? '';
$warning = $data['warning'] ?? '';
$leaveFilters = $data['filters'] ?? [];
$leaveTypeOptions = $data['leaveTypeOptions'] ?? [];
$selectedLeaveStatus = trim((string) ($leaveFilters['status'] ?? ''));
$selectedLeaveType = trim((string) ($leaveFilters['leave_type'] ?? ''));
?>
<?php
$caretakerPageTitle = 'Leave Management - SmartCare';
$caretakerExtraCss = ['caretaker/ct_leave.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
    <main class="content">
        <header class="page-header">
            <h1 class="page-title">Leave Requests</h1>
            <div class="header-actions">
                <?php if ((int)$summary['remaining'] <= 0): ?>
                    <button type="button" class="add-btn" onclick="alert('Your leave is finished for this month! You cannot request more leaves this month.');">
                        Request Leave
                    </button>
                <?php else: ?>
                    <button type="button" class="add-btn"
                        onclick="window.location.href='<?php echo URLROOT; ?>/leaveCRUD/add'">
                        Request Leave
                    </button>
                <?php endif; ?>
            </div>
        </header>
        <div class="booking">

            <div class="card">
                <div class="leave-summary-strip">
                    <div class="summary-item">
                        <span>Monthly Leave Limit</span><br>
                        <strong><?= (int)$summary['limit'] ?> days</strong>
                    </div>
                    <div class="summary-item">
                        <span>Used</span><br>
                        <strong><?= (int)$summary['used'] ?> days</strong>
                    </div>
                    <div class="summary-item">
                        <span>Remaining</span><br>
                        <strong><?= (int)$summary['remaining'] ?> days</strong>
                    </div>
                    <div class="summary-progress">
                        <span><?= htmlspecialchars($summary['label']) ?></span><br>
                        <div class="track">
                            <div class="fill" style="width: <?= (int)$summary['percentage'] ?>%"></div>
                        </div>
                    </div>
                </div>
<br>
                <?php if (!empty($warning)): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($warning) ?></div>
                <?php endif; ?>

                <form class="filter-section filters-inline ct-page-filters" method="get" action="<?= htmlspecialchars(URLROOT . '/public', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="url" value="caretaker/ct_leave">
                    <div class="filter-group">
                        <label for="leaveStatusFilter">Status</label>
                        <select id="leaveStatusFilter" name="leave_status">
                            <option value="">All statuses</option>
                            <?php foreach (['Pending', 'Approved', 'Rejected', 'Cancelled'] as $status): ?>
                                <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($selectedLeaveStatus, $status) === 0 ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="leaveTypeFilter">Leave type</label>
                        <select id="leaveTypeFilter" name="leave_type">
                            <option value="">All types</option>
                            <?php foreach ($leaveTypeOptions as $type): ?>
                                <option value="<?= htmlspecialchars((string) $type, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($selectedLeaveType, (string) $type) === 0 ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) $type, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group filter-group--actions">
                        <button type="submit" class="btn primary">Apply</button>
                        <a class="btn ghost" href="<?= htmlspecialchars(URLROOT . '/public?url=caretaker/ct_leave', ENT_QUOTES, 'UTF-8') ?>">Reset</a>
                    </div>
                </form>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Dates</th>
                                <th>Type</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['leaves'])): ?>
                                <?php foreach ($data['leaves'] as $leave): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($leave['start_date'] . " – " . $leave['end_date']); ?></td>
                                        <td><?php echo htmlspecialchars($leave['leave_type']); ?></td>
                                        <td><?php echo htmlspecialchars($leave['reason']); ?></td>
                                        <td><span class="status <?php echo strtolower($leave['status']); ?>"><?php echo $leave['status']; ?></span></td>
                                        <td>
                                            <?php if ($leave['status'] == 'Pending'): ?>
                                                <a href="<?php echo URLROOT; ?>/LeaveCRUD/edit/<?php echo $leave['id']; ?>">
                                                    <i class="bx bx-edit"></i>
                                                </a> |
                                                <a href="<?php echo URLROOT; ?>/LeaveCRUD/delete/<?php echo $leave['id']; ?>"
                                                    data-app-confirm="Cancel this leave request?">
                                                    <i class="bx bx-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <span style="color: gray; font-style: italic;">Locked</span>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No leave requests found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>