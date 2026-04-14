<?php
$hrPageTitle = 'Payment Monitor — HR';
$hrExtraCss  = ['hr/hr_paymentMonitor.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

$summary = $data['summary'] ?? [];
$recurring = $data['recurring'] ?? [];
$recent = $data['recent'] ?? [];
$filters = $data['filters'] ?? [];

$clientFilter = htmlspecialchars((string) ($filters['client'] ?? ''), ENT_QUOTES, 'UTF-8');
$recurringStatusFilter = (string) ($filters['recurring_status'] ?? 'all');
$paymentStatusFilter = (string) ($filters['payment_status'] ?? 'all');
$fromDateFilter = htmlspecialchars((string) ($filters['from_date'] ?? ''), ENT_QUOTES, 'UTF-8');
$toDateFilter = htmlspecialchars((string) ($filters['to_date'] ?? ''), ENT_QUOTES, 'UTF-8');

$basisMap = ['Daily' => 'Day', 'Monthly' => 'Month', 'Hourly' => 'Hour', 'Weekly' => 'Week', 'Yearly' => 'Year'];

/**
 * @return string JSON for data-monitor-row (safe in HTML attribute).
 */
function hr_monitor_row_json(array $row, string $kind): string
{
    $row['monitor_kind'] = $kind;

    return htmlspecialchars(
        json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<main class="main-content">
    <header class="page-header">
        <h1 class="page-title">Payment Monitor</h1>
        <p class="page-subtitle">Track approvals, recurring dues, overdue balances, and payment activity.</p>
    </header>

    <div class="filter-bar">
        <form method="get" action="<?= URLROOT ?>/hr/paymentMonitor" class="filter-bar__form">
            <div class="filter-bar__grid">
                <div class="field">
                    <label for="client">Client name</label>
                    <input type="text" id="client" name="client" value="<?= $clientFilter ?>" placeholder="Search client…">
                </div>
                <div class="field">
                    <label for="recurring_status">Recurring status</label>
                    <select id="recurring_status" name="recurring_status">
                        <option value="all" <?= $recurringStatusFilter === 'all' ? 'selected' : '' ?>>All</option>
                        <option value="pending" <?= $recurringStatusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= $recurringStatusFilter === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="overdue" <?= $recurringStatusFilter === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                        <option value="cancelled" <?= $recurringStatusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="field">
                    <label for="payment_status">Payment status</label>
                    <select id="payment_status" name="payment_status">
                        <option value="all" <?= $paymentStatusFilter === 'all' ? 'selected' : '' ?>>All</option>
                        <option value="pending" <?= $paymentStatusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $paymentStatusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $paymentStatusFilter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="field">
                    <label for="from_date">From date</label>
                    <input type="date" id="from_date" name="from_date" value="<?= $fromDateFilter ?>">
                </div>
                <div class="field">
                    <label for="to_date">To date</label>
                    <input type="date" id="to_date" name="to_date" value="<?= $toDateFilter ?>">
                </div>
                <div class="filter-bar__actions field">
                    <label class="filter-bar__actions-label">&nbsp;</label>
                    <div class="filter-bar__buttons">
                        <button type="submit" class="btn primary">Apply</button>
                        <a class="reset-btn" href="<?= URLROOT ?>/hr/paymentMonitor">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <p class="result-count">Showing <?= count($recurring) ?> recurring records and <?= count($recent) ?> payment records.</p>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Pending approvals</h3>
            <p class="value"><?= (int) ($summary['pending_approvals'] ?? 0) ?></p>
            <small>Rs. <?= number_format((float) ($summary['amount_pending_approval'] ?? 0), 2) ?></small>
        </div>
        <div class="stat-card">
            <h3>Approved payments</h3>
            <p class="value"><?= (int) ($summary['approved_payments'] ?? 0) ?></p>
            <small>Rs. <?= number_format((float) ($summary['amount_approved'] ?? 0), 2) ?></small>
        </div>
        <div class="stat-card">
            <h3>Rejected payments</h3>
            <p class="value"><?= (int) ($summary['rejected_payments'] ?? 0) ?></p>
            <small>Needs client follow-up</small>
        </div>
        <div class="stat-card">
            <h3>Recurring pending</h3>
            <p class="value"><?= (int) ($summary['pending_recurring'] ?? 0) ?></p>
            <small>Awaiting due-date payment</small>
        </div>
        <div class="stat-card stat-card--warning">
            <h3>Recurring overdue</h3>
            <p class="value"><?= (int) ($summary['overdue_recurring'] ?? 0) ?></p>
            <small>Rs. <?= number_format((float) ($summary['amount_overdue'] ?? 0), 2) ?></small>
        </div>
        <div class="stat-card stat-card--success">
            <h3>Recurring paid</h3>
            <p class="value"><?= (int) ($summary['paid_recurring'] ?? 0) ?></p>
            <small>Installments closed</small>
        </div>
    </div>

    <section class="monitor-section">
        <h2 class="monitor-section__title">Recurring payment tracker</h2>
        <div class="table-container">
            <table class="table booking-table monitor-table" data-table-collapse="off">
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
                    <?php if (empty($recurring)): ?>
                        <tr>
                            <td colspan="7" class="empty">No recurring payment records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recurring as $row): ?>
                            <?php
                            $basis = $row['basis'] ?? '—';
                            $displayBasis = $basisMap[$basis] ?? ucfirst((string) $basis);
                            $durationText = htmlspecialchars((string) ($row['booking_duration'] ?? '—'), ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($displayBasis, ENT_QUOTES, 'UTF-8');
                            $st = strtolower((string) ($row['status'] ?? ''));
                            ?>
                            <tr>
                                <td><?= (int) ($row['client_id'] ?? 0) ?></td>
                                <td><?= (int) ($row['caretaker_id'] ?? 0) ?></td>
                                <td><?= htmlspecialchars((string) ($row['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= $durationText ?></td>
                                <td><span class="amount-cell">Rs <?= number_format((float) ($row['booking_total_payment'] ?? 0), 2) ?></span></td>
                                <td>
                                    <span class="status-pill" data-row-status="<?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars(ucfirst((string) ($row['status'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="actions monitor-row-actions">
                                    <button type="button"
                                        class="btn secondary btn-sm action-view-btn action-view-btn--icon js-monitor-detail"
                                        data-monitor-row="<?= hr_monitor_row_json($row, 'recurring') ?>"
                                        title="View details"
                                        aria-label="View recurring installment details">
                                        <i class="bx bx-show" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="monitor-section">
        <h2 class="monitor-section__title">Recent payment timeline</h2>
        <div class="table-container">
            <table class="table booking-table monitor-table" data-table-collapse="off">
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
                    <?php if (empty($recent)): ?>
                        <tr>
                            <td colspan="7" class="empty">No payment activity found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent as $row): ?>
                            <?php
                            $basis = $row['basis'] ?? '—';
                            $displayBasis = $basisMap[$basis] ?? ucfirst((string) $basis);
                            $durationText = htmlspecialchars((string) ($row['booking_duration'] ?? '—'), ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($displayBasis, ENT_QUOTES, 'UTF-8');
                            $pst = strtolower((string) ($row['status'] ?? ''));
                            ?>
                            <tr>
                                <td><?= (int) ($row['client_id'] ?? 0) ?></td>
                                <td><?= (int) ($row['caretaker_id'] ?? 0) ?></td>
                                <td><?= htmlspecialchars((string) ($row['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= $durationText ?></td>
                                <td><span class="amount-cell">Rs <?= number_format((float) ($row['total_booking_amount'] ?? 0), 2) ?></span></td>
                                <td>
                                    <span class="status-pill" data-row-status="<?= htmlspecialchars($pst, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars(ucfirst((string) ($row['status'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="actions monitor-row-actions">
                                    <button type="button"
                                        class="btn secondary btn-sm action-view-btn action-view-btn--icon js-monitor-detail"
                                        data-monitor-row="<?= hr_monitor_row_json($row, 'payment') ?>"
                                        title="View details"
                                        aria-label="View payment details">
                                        <i class="bx bx-show" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<div id="paymentMonitorDetailModal" class="modal admin-row-detail-modal" aria-hidden="true">
    <div class="modal-content admin-row-detail-modal__content monitor-detail-modal__content" role="dialog" aria-modal="true"
        aria-labelledby="paymentMonitorDetailTitle">
        <button type="button" class="modal-close admin-row-detail-modal__close" data-close-monitor-detail aria-label="Close">
            <i class="bx bx-x" aria-hidden="true"></i>
        </button>
        <header class="admin-row-detail-modal__header">
            <span class="admin-row-detail-modal__header-icon" aria-hidden="true"><i class="bx bx-show"></i></span>
            <h3 id="paymentMonitorDetailTitle" class="admin-row-detail-modal__title">Details</h3>
        </header>
        <dl class="admin-row-detail-modal__dl" id="paymentMonitorDetailDl"></dl>
    </div>
</div>

<script src="<?= URLROOT ?>/public/js/hr/hr_paymentMonitor.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
