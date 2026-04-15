<?php
$summary = $data['summary'] ?? [];
$caretakerStatus = $data['caretakerStatus'] ?? [];
$newCaretakers = $data['newCaretakers'] ?? [];
$caretakersByService = $data['caretakersByService'] ?? [];
$caretakerAvailability = $data['caretakerAvailability'] ?? [];
$caretakerWorkload = $data['caretakerWorkload'] ?? [];
$leaveRequests = $data['leaveRequests'] ?? [];
$pendingLeaves = $data['pendingLeaves'] ?? [];
$approvedLeavesThisMonth = $data['approvedLeavesThisMonth'] ?? [];
$caretakersOnLeave = $data['caretakersOnLeave'] ?? [];
$assignmentStats = $data['assignmentStats'] ?? [];
$unassignedBookings = $data['unassignedBookings'] ?? [];
$upcomingSchedules = $data['upcomingSchedules'] ?? [];
$assignmentDistribution = $data['assignmentDistribution'] ?? [];
$rescheduleRequests = $data['rescheduleRequests'] ?? [];
$pendingReschedules = $data['pendingReschedules'] ?? [];
$recentApprovedReschedules = $data['recentApprovedReschedules'] ?? [];
$awaitingAdvancePayment = $data['awaitingAdvancePayment'] ?? [];
$awaitingFinalPayment = $data['awaitingFinalPayment'] ?? [];
$caretakerFeedback = $data['caretakerFeedback'] ?? [];
$caretakerComplaints = $data['caretakerComplaints'] ?? [];
$completionRate = $data['completionRate'] ?? [];
$fromDate = (string) ($data['fromDate'] ?? '');
$toDate = (string) ($data['toDate'] ?? '');

$activeCaretakers = 0;
foreach ($caretakerStatus as $statusRow) {
    if (strtolower((string) ($statusRow['status'] ?? '')) === 'active') {
        $activeCaretakers = (int) ($statusRow['count'] ?? 0);
        break;
    }
}

$avgCaretakerRating = 0.0;
if (!empty($caretakerFeedback)) {
    $ratingSum = 0.0;
    $ratingCount = 0;
    foreach ($caretakerFeedback as $feedbackRow) {
        $ratingSum += (float) ($feedbackRow['avg_rating'] ?? 0);
        $ratingCount++;
    }
    $avgCaretakerRating = $ratingCount > 0 ? ($ratingSum / $ratingCount) : 0.0;
}

function esc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$rangeLabel = ($fromDate !== '' && $toDate !== '') ? 'Selected date range' : 'Default reporting window';

$reportQuery = ['url' => 'hr/hr_reports'];
if ($fromDate !== '') {
    $reportQuery['from'] = $fromDate;
}
if ($toDate !== '') {
    $reportQuery['to'] = $toDate;
}
$exportCsvUrl = URLROOT . '/public?' . http_build_query(array_merge($reportQuery, ['export' => '1', 'format' => 'csv']));
$exportPdfUrl = URLROOT . '/public?' . http_build_query(array_merge($reportQuery, ['export' => '1', 'format' => 'pdf']));

$hrPageTitle = 'HR Reports — SmartCare';
$hrExtraCss  = ['hr/hr_reports.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main class="main-content reports-container">
    <div class="page-header reports-page-header">
        <div class="page-header-main">
            <h1 class="page-title">HR Reports</h1>
            <p class="page-subtitle">Operational visibility for workforce and scheduling.</p>
        </div>
        <div class="header-actions reports-header-actions">
            <form class="filter-section filters-inline reports-filters" method="get" action="<?= esc(URLROOT . '/public') ?>">
                <input type="hidden" name="url" value="hr/hr_reports">
                <div class="filter-group">
                    <label for="fromDate">From</label>
                    <input type="date" id="fromDate" name="from" value="<?= esc($fromDate) ?>">
                </div>
                <div class="filter-group">
                    <label for="toDate">To</label>
                    <input type="date" id="toDate" name="to" value="<?= esc($toDate) ?>">
                </div>
                <div class="filter-group filter-group--actions">
                    <button type="submit" class="btn primary">Apply</button>
                    <a class="btn ghost" href="<?= esc(URLROOT . '/public?url=hr/hr_reports') ?>">Reset</a>
                </div>
            </form>
            <div class="export-buttons">
                <a class="btn secondary" href="<?= esc($exportCsvUrl) ?>">Export CSV</a>
                <a class="btn secondary" href="<?= esc($exportPdfUrl) ?>">Export PDF</a>
            </div>
        </div>
    </div>

    <section class="summary-cards" aria-label="Summary">
        <article class="card">
            <span class="card-label">Total bookings</span>
            <span class="card-value"><?= esc($completionRate['total'] ?? 0) ?></span>
        </article>
        <article class="card">
            <span class="card-label">Active caregivers</span>
            <span class="card-value"><?= esc($activeCaretakers) ?></span>
        </article>
        <article class="card">
            <span class="card-label">Pending leaves</span>
            <span class="card-value"><?= esc($summary['pendingLeaves'] ?? 0) ?></span>
        </article>
        <article class="card">
            <span class="card-label">Pending reschedules</span>
            <span class="card-value"><?= esc($summary['pendingReschedules'] ?? 0) ?></span>
        </article>
        <article class="card">
            <span class="card-label">Unassigned bookings</span>
            <span class="card-value"><?= esc($assignmentStats['unassigned'] ?? 0) ?></span>
        </article>
        <article class="card">
            <span class="card-label">Avg caregiver rating</span>
            <span class="card-value"><?= esc(number_format($avgCaretakerRating, 2)) ?></span>
        </article>
    </section>

    <h2 class="reports-section-title">Charts</h2>
    <p class="reports-section-hint">Workforce and scheduling charts use your date filter where the underlying data supports it. <?= esc($rangeLabel) ?>.</p>

    <section class="panel-grid reports-charts-grid">
        <article class="panel chart-panel">
            <h3>Caregiver status</h3>
            <div class="chart-panel__body" id="wrap-caretakerStatusChart">
                <canvas id="caretakerStatusChart" aria-label="Caregiver status chart"></canvas>
                <p class="chart-empty" id="empty-caretakerStatusChart" hidden>No status data in this range.</p>
            </div>
        </article>
        <article class="panel chart-panel">
            <h3>Caregiver service mix</h3>
            <div class="chart-panel__body" id="wrap-serviceMixChart">
                <canvas id="serviceMixChart" aria-label="Caregiver service mix chart"></canvas>
                <p class="chart-empty" id="empty-serviceMixChart" hidden>No service mix data in this range.</p>
            </div>
        </article>
    </section>

    <section class="panel-grid reports-charts-grid">
        <article class="panel chart-panel">
            <h3>Leave requests by status</h3>
            <div class="chart-panel__body" id="wrap-leaveStatusChart">
                <canvas id="leaveStatusChart" aria-label="Leave requests chart"></canvas>
                <p class="chart-empty" id="empty-leaveStatusChart" hidden>No leave data in this range.</p>
            </div>
        </article>
        <article class="panel chart-panel">
            <h3>Reschedule requests by status</h3>
            <div class="chart-panel__body" id="wrap-rescheduleStatusChart">
                <canvas id="rescheduleStatusChart" aria-label="Reschedule requests chart"></canvas>
                <p class="chart-empty" id="empty-rescheduleStatusChart" hidden>No reschedule data in this range.</p>
            </div>
        </article>
    </section>

    <h2 class="reports-section-title">Operational snapshot</h2>

    <section class="panel-grid two-col">
        <article class="panel compact">
            <h3>Completion rate</h3>
            <div class="stat-list">
                <div><span>Total bookings</span><strong><?= esc($completionRate['total'] ?? 0) ?></strong></div>
                <div><span>Completed</span><strong><?= esc($completionRate['completed'] ?? 0) ?></strong></div>
                <div><span>Completion rate</span><strong><?= esc($completionRate['rate'] ?? '0.00') ?>%</strong></div>
                <div><span>Payment approvals pending</span><strong><?= esc($data['paymentApprovalPending'] ?? 0) ?></strong></div>
            </div>
        </article>

        <article class="panel compact">
            <h3>Workforce snapshot</h3>
            <div class="stat-list">
                <div><span>Total caregivers</span><strong><?= esc($summary['totalCaretakers'] ?? 0) ?></strong></div>
                <div><span>Active today</span><strong><?= esc($summary['activeToday'] ?? 0) ?></strong></div>
                <div><span>Pending bookings</span><strong><?= esc($summary['pendingBookings'] ?? 0) ?></strong></div>
                <div><span>Recent complaints</span><strong><?= esc($summary['recentComplaints'] ?? 0) ?></strong></div>
            </div>
        </article>
    </section>

    <h2 class="reports-section-title">Tables</h2>
    <p class="reports-section-hint">Detailed queues and operational lists for the selected period where applicable.</p>

    <section class="panel-grid two-col">
        <article class="panel">
            <h3>Pending leave requests</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Caregiver</th>
                            <th>Service</th>
                            <th>Leave type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingLeaves)): ?>
                            <tr>
                                <td colspan="6" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendingLeaves as $row): ?>
                                <tr>
                                    <td><?= esc($row['caretaker_name'] ?? $row['name'] ?? '-') ?></td>
                                    <td><?= esc($row['service_type'] ?? '-') ?></td>
                                    <td><?= esc($row['leave_type'] ?? '-') ?></td>
                                    <td><?= esc($row['from_date'] ?? $row['start_date'] ?? '-') ?></td>
                                    <td><?= esc($row['to_date'] ?? $row['end_date'] ?? '-') ?></td>
                                    <td><?= esc($row['status'] ?? 'Pending') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <h3>Pending reschedule requests</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Client</th>
                            <th>Current date</th>
                            <th>Requested date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingReschedules)): ?>
                            <tr>
                                <td colspan="5" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendingReschedules as $row): ?>
                                <tr>
                                    <td>#<?= esc($row['booking_id'] ?? '-') ?></td>
                                    <td><?= esc($row['client_name'] ?? '-') ?></td>
                                    <td><?= esc($row['old_date'] ?? $row['current_date'] ?? '-') ?></td>
                                    <td><?= esc($row['new_date'] ?? $row['requested_date'] ?? '-') ?></td>
                                    <td><?= esc($row['status'] ?? 'Pending') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="panel-grid two-col">
        <article class="panel">
            <h3>Caregivers currently on leave</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Caregiver</th>
                            <th>Service</th>
                            <th>Leave type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($caretakersOnLeave)): ?>
                            <tr>
                                <td colspan="6" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($caretakersOnLeave as $row): ?>
                                <tr>
                                    <td><?= esc($row['name'] ?? '-') ?></td>
                                    <td><?= esc($row['service_type'] ?? '-') ?></td>
                                    <td><?= esc($row['leave_type'] ?? '-') ?></td>
                                    <td><?= esc($row['from_date'] ?? '-') ?></td>
                                    <td><?= esc($row['to_date'] ?? '-') ?></td>
                                    <td><?= esc($row['days_remaining'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <h3>Unassigned bookings</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>District</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($unassignedBookings)): ?>
                            <tr>
                                <td colspan="5" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($unassignedBookings as $row): ?>
                                <tr>
                                    <td>#<?= esc($row['booking_id'] ?? $row['id'] ?? '-') ?></td>
                                    <td><?= esc($row['client_name'] ?? '-') ?></td>
                                    <td><?= esc($row['service_type'] ?? '-') ?></td>
                                    <td><?= esc($row['booking_date'] ?? '-') ?></td>
                                    <td><?= esc($row['district'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="panel-grid two-col">
        <article class="panel">
            <h3>Upcoming schedules</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Caregiver</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($upcomingSchedules)): ?>
                            <tr>
                                <td colspan="5" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($upcomingSchedules as $row): ?>
                                <tr>
                                    <td>#<?= esc($row['booking_id'] ?? $row['id'] ?? '-') ?></td>
                                    <td><?= esc($row['caretaker_name'] ?? '-') ?></td>
                                    <td><?= esc($row['client_name'] ?? '-') ?></td>
                                    <td><?= esc($row['booking_date'] ?? '-') ?></td>
                                    <td><?= esc($row['preferred_time'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <h3>Awaiting advance payment (operational)</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($awaitingAdvancePayment)): ?>
                            <tr>
                                <td colspan="4" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($awaitingAdvancePayment as $row): ?>
                                <tr>
                                    <td>#<?= esc($row['booking_id'] ?? $row['id'] ?? '-') ?></td>
                                    <td><?= esc($row['client_name'] ?? '-') ?></td>
                                    <td><?= esc($row['service_type'] ?? '-') ?></td>
                                    <td><?= esc($row['status'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="panel-grid two-col">
        <article class="panel">
            <h3>Awaiting final payment (operational)</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Client</th>
                            <th>Caregiver</th>
                            <th>Service</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($awaitingFinalPayment)): ?>
                            <tr>
                                <td colspan="5" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($awaitingFinalPayment as $row): ?>
                                <tr>
                                    <td>#<?= esc($row['booking_id'] ?? $row['id'] ?? '-') ?></td>
                                    <td><?= esc($row['client_name'] ?? '-') ?></td>
                                    <td><?= esc($row['caretaker_name'] ?? '-') ?></td>
                                    <td><?= esc($row['service_type'] ?? '-') ?></td>
                                    <td><?= esc($row['status'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <h3>Caregiver feedback snapshot</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Service</th>
                            <th>Avg rating</th>
                            <th>Total feedbacks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($caretakerFeedback)): ?>
                            <tr>
                                <td colspan="4" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($caretakerFeedback as $row): ?>
                                <tr>
                                    <td><?= esc($row['name'] ?? '-') ?></td>
                                    <td><?= esc($row['service_type'] ?? '-') ?></td>
                                    <td><?= esc(number_format((float) ($row['avg_rating'] ?? 0), 2)) ?></td>
                                    <td><?= esc($row['total_feedbacks'] ?? 0) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="panel-grid two-col">
        <article class="panel">
            <h3>Caregiver complaints</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Caregiver</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($caretakerComplaints)): ?>
                            <tr>
                                <td colspan="5" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($caretakerComplaints as $row): ?>
                                <tr>
                                    <td><?= esc($row['client_name'] ?? '-') ?></td>
                                    <td><?= esc($row['caretaker_name'] ?? '-') ?></td>
                                    <td><?= esc($row['service_type'] ?? '-') ?></td>
                                    <td><?= esc($row['status'] ?? '-') ?></td>
                                    <td><?= esc($row['created_at'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <h3>Recent approved reschedules</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Client</th>
                            <th>Caregiver</th>
                            <th>Service</th>
                            <th>New date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentApprovedReschedules)): ?>
                            <tr>
                                <td colspan="5" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentApprovedReschedules as $row): ?>
                                <tr>
                                    <td>#<?= esc($row['booking_id'] ?? '-') ?></td>
                                    <td><?= esc($row['client_name'] ?? '-') ?></td>
                                    <td><?= esc($row['caretaker_name'] ?? '-') ?></td>
                                    <td><?= esc($row['service_type'] ?? '-') ?></td>
                                    <td><?= esc($row['new_date'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <section class="panel-grid two-col">
        <article class="panel">
            <h3>Approved leaves (this month)</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Caregiver</th>
                            <th>Service</th>
                            <th>Leave type</th>
                            <th>From</th>
                            <th>To</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($approvedLeavesThisMonth)): ?>
                            <tr>
                                <td colspan="5" class="empty">No data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($approvedLeavesThisMonth as $row): ?>
                                <tr>
                                    <td><?= esc($row['caretaker_name'] ?? '-') ?></td>
                                    <td><?= esc($row['service_type'] ?? '-') ?></td>
                                    <td><?= esc($row['leave_type'] ?? '-') ?></td>
                                    <td><?= esc($row['from_date'] ?? '-') ?></td>
                                    <td><?= esc($row['to_date'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    var caretakerStatusRows = <?= json_encode($caretakerStatus, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    var caretakersByServiceRows = <?= json_encode($caretakersByService, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    var leaveRequestsRows = <?= json_encode($leaveRequests, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    var rescheduleRequestRows = <?= json_encode($rescheduleRequests, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    function showChartEmpty(canvasId, empty) {
        var canvas = document.getElementById(canvasId);
        var wrap = document.getElementById('wrap-' + canvasId);
        var msg = document.getElementById('empty-' + canvasId);
        if (canvas) {
            canvas.hidden = empty;
        }
        if (wrap && msg) {
            msg.hidden = !empty;
            if (empty) {
                wrap.classList.add('chart-panel__body--empty');
            } else {
                wrap.classList.remove('chart-panel__body--empty');
            }
        }
    }

    function doughnutOrPie(id, type, rows, labelKey, valueKey, colors, datasetLabel) {
        var labels = (rows || []).map(function (r) {
            return String(r[labelKey] != null ? r[labelKey] : '');
        });
        var values = (rows || []).map(function (r) {
            return Number(r[valueKey] || 0);
        });
        var sum = values.reduce(function (a, b) {
            return a + b;
        }, 0);
        if (!sum) {
            showChartEmpty(id, true);
            return null;
        }
        showChartEmpty(id, false);
        var el = document.getElementById(id);
        if (!el || typeof Chart === 'undefined') {
            return null;
        }
        return new Chart(el, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: datasetLabel,
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 1,
                    borderColor: '#e2e8f0'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    function barServiceMix(id, rows, labelKey, valueKey, colors, datasetLabel) {
        var labels = (rows || []).map(function (r) {
            return String(r[labelKey] != null ? r[labelKey] : '');
        });
        var values = (rows || []).map(function (r) {
            return Number(r[valueKey] || 0);
        });
        var sum = values.reduce(function (a, b) {
            return a + b;
        }, 0);
        if (!sum) {
            showChartEmpty(id, true);
            return null;
        }
        showChartEmpty(id, false);
        var el = document.getElementById(id);
        if (!el || typeof Chart === 'undefined') {
            return null;
        }
        var bg = labels.map(function (_, i) {
            return colors[i % colors.length];
        });
        return new Chart(el, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: datasetLabel,
                    data: values,
                    backgroundColor: bg,
                    borderColor: '#e2e8f0',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    function init() {
        doughnutOrPie('caretakerStatusChart', 'doughnut', caretakerStatusRows, 'status', 'count',
            ['#00bfa5', '#ef4444', '#3b82f6', '#a855f7', '#94a3b8'], 'Caregivers');
        barServiceMix('serviceMixChart', caretakersByServiceRows, 'service_type', 'count',
            ['#1e40af', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd'], 'Caregivers');
        doughnutOrPie('leaveStatusChart', 'pie', leaveRequestsRows, 'status', 'count',
            ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444', '#94a3b8'], 'Leaves');
        doughnutOrPie('rescheduleStatusChart', 'pie', rescheduleRequestRows, 'status', 'count',
            ['#f59e0b', '#22c55e', '#ef4444', '#a855f7', '#94a3b8'], 'Reschedules');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
