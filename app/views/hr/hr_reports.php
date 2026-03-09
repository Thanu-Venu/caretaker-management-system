<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

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
$fromDate = $data['fromDate'] ?? '';
$toDate = $data['toDate'] ?? '';

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Reports</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_reports.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="reports-container">
        <div class="reports-header">
            <div>
                <h2>HR Reports</h2>
                <p>Operational visibility for workforce and scheduling</p>
            </div>

            <div class="header-actions">
                <div class="filters">
                    <label for="fromDate">From</label>
                    <input type="date" id="fromDate" value="<?= esc($fromDate) ?>">
                    <label for="toDate">To</label>
                    <input type="date" id="toDate" value="<?= esc($toDate) ?>">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">Apply</button>
                </div>

                <div class="export-buttons">
                    <button type="button" class="btn btn-secondary" onclick="exportReport('csv')">Export CSV</button>
                    <button type="button" class="btn btn-secondary" onclick="exportReport('pdf')">Export PDF</button>
                </div>
            </div>
        </div>

        <section class="summary-cards">
            <article class="card">
                <span class="card-label">Total Bookings</span>
                <span class="card-value"><?= esc($completionRate['total'] ?? 0) ?></span>
            </article>
            <article class="card">
                <span class="card-label">Active Caretakers</span>
                <span class="card-value"><?= esc($activeCaretakers) ?></span>
            </article>
            <article class="card">
                <span class="card-label">Pending Leaves</span>
                <span class="card-value"><?= esc($summary['pendingLeaves'] ?? 0) ?></span>
            </article>
            <article class="card">
                <span class="card-label">Pending Reschedules</span>
                <span class="card-value"><?= esc($summary['pendingReschedules'] ?? 0) ?></span>
            </article>
            <article class="card">
                <span class="card-label">Unassigned Bookings</span>
                <span class="card-value"><?= esc($assignmentStats['unassigned'] ?? 0) ?></span>
            </article>
            <article class="card">
                <span class="card-label">Avg Caretaker Rating</span>
                <span class="card-value"><?= esc(number_format($avgCaretakerRating, 2)) ?></span>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel chart-panel">
                <h3>Caretaker Status</h3>
                <canvas id="caretakerStatusChart"></canvas>
            </article>
            <article class="panel chart-panel">
                <h3>Caretaker Service Mix</h3>
                <canvas id="serviceMixChart"></canvas>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel chart-panel">
                <h3>Leave Requests by Status</h3>
                <canvas id="leaveStatusChart"></canvas>
            </article>
            <article class="panel chart-panel">
                <h3>Reschedule Requests by Status</h3>
                <canvas id="rescheduleStatusChart"></canvas>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel chart-panel">
                <h3>Caretaker Workload</h3>
                <canvas id="workloadChart"></canvas>
            </article>
            <article class="panel chart-panel">
                <h3>Assignment Distribution</h3>
                <canvas id="assignmentDistributionChart"></canvas>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel compact">
                <h3>Completion Rate</h3>
                <div class="stat-list">
                    <div><span>Total Bookings</span><strong><?= esc($completionRate['total'] ?? 0) ?></strong></div>
                    <div><span>Completed</span><strong><?= esc($completionRate['completed'] ?? 0) ?></strong></div>
                    <div><span>Completion Rate</span><strong><?= esc($completionRate['rate'] ?? '0.00') ?>%</strong></div>
                    <div><span>Payment Approvals Pending</span><strong><?= esc($data['paymentApprovalPending'] ?? 0) ?></strong></div>
                </div>
            </article>

            <article class="panel compact">
                <h3>Operational Snapshot</h3>
                <div class="stat-list">
                    <div><span>Total Caretakers</span><strong><?= esc($summary['totalCaretakers'] ?? 0) ?></strong></div>
                    <div><span>Active Today</span><strong><?= esc($summary['activeToday'] ?? 0) ?></strong></div>
                    <div><span>Pending Bookings</span><strong><?= esc($summary['pendingBookings'] ?? 0) ?></strong></div>
                    <div><span>Recent Complaints</span><strong><?= esc($summary['recentComplaints'] ?? 0) ?></strong></div>
                </div>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel">
                <h3>Pending Leave Requests</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Caretaker</th>
                                <th>Service</th>
                                <th>Leave Type</th>
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
                <h3>Pending Reschedule Requests</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Client</th>
                                <th>Current Date</th>
                                <th>Requested Date</th>
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
                <h3>Caretakers Currently on Leave</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Caretaker</th>
                                <th>Service</th>
                                <th>Leave Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Days Remaining</th>
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
                <h3>Unassigned Bookings</h3>
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

            <article class="panel">
                <h3>Upcoming Schedules</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Caretaker</th>
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
        </section>

        <section class="panel-grid two-col">
            <article class="panel">
                <h3>Awaiting Advance Payment (Operational)</h3>
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

            <article class="panel">
                <h3>Awaiting Final Payment (Operational)</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Client</th>
                                <th>Caretaker</th>
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
        </section>

        <section class="panel-grid two-col">
            <article class="panel">
                <h3>Caretaker Feedback Snapshot</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Avg Rating</th>
                                <th>Total Feedbacks</th>
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
                                        <td><?= esc(number_format((float)($row['avg_rating'] ?? 0), 2)) ?></td>
                                        <td><?= esc($row['total_feedbacks'] ?? 0) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel">
                <h3>Caretaker Complaints</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Caretaker</th>
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
        </section>

        <section class="panel-grid two-col">
            <article class="panel">
                <h3>Recent Approved Reschedules</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Client</th>
                                <th>Caretaker</th>
                                <th>Service</th>
                                <th>New Date</th>
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

            <article class="panel">
                <h3>Approved Leaves (This Month)</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Caretaker</th>
                                <th>Service</th>
                                <th>Leave Type</th>
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
    </div>

    <script>
        const caretakerStatusRows = <?= json_encode($caretakerStatus) ?>;
        const caretakersByServiceRows = <?= json_encode($caretakersByService) ?>;
        const caretakerWorkloadRows = <?= json_encode($caretakerWorkload) ?>;
        const assignmentDistributionRows = <?= json_encode($assignmentDistribution) ?>;
        const leaveRequestsRows = <?= json_encode($leaveRequests) ?>;
        const rescheduleRequestRows = <?= json_encode($rescheduleRequests) ?>;
        const hrUrl = `${<?= json_encode(URLROOT) ?>}/hr/hr_reports`;

        function applyFilters() {
            const from = document.getElementById('fromDate').value;
            const to = document.getElementById('toDate').value;
            const params = new URLSearchParams();
            if (from) params.set('from', from);
            if (to) params.set('to', to);
            window.location.href = `${hrUrl}?${params.toString()}`;
        }

        function exportReport(format) {
            const from = document.getElementById('fromDate').value;
            const to = document.getElementById('toDate').value;
            const params = new URLSearchParams();
            params.set('export', '1');
            params.set('format', format);
            if (from) params.set('from', from);
            if (to) params.set('to', to);
            window.location.href = `${hrUrl}?${params.toString()}`;
        }

        function makeChart(id, type, labels, values, colors, label) {
            const el = document.getElementById(id);
            if (!el) return;
            new Chart(el, {
                type,
                data: {
                    labels,
                    datasets: [{
                        label,
                        data: values,
                        backgroundColor: colors,
                        borderColor: '#1f2937',
                        borderWidth: type === 'line' ? 2 : 0,
                        fill: type === 'line'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: type === 'line' || type === 'bar' ? {
                        y: {
                            beginAtZero: true
                        }
                    } : {}
                }
            });
        }

        makeChart(
            'caretakerStatusChart',
            'doughnut',
            caretakerStatusRows.map(r => r.status),
            caretakerStatusRows.map(r => Number(r.count || 0)),
            ['#10b981', '#ef4444', '#3b82f6', '#a855f7'],
            'Caretakers'
        );

        makeChart(
            'serviceMixChart',
            'bar',
            caretakersByServiceRows.map(r => r.service_type),
            caretakersByServiceRows.map(r => Number(r.count || 0)),
            ['#0ea5e9', '#14b8a6', '#f97316', '#84cc16', '#8b5cf6'],
            'Caretakers'
        );

        makeChart(
            'leaveStatusChart',
            'pie',
            leaveRequestsRows.map(r => r.status),
            leaveRequestsRows.map(r => Number(r.count || 0)),
            ['#f59e0b', '#22c55e', '#ef4444', '#3b82f6'],
            'Leaves'
        );

        makeChart(
            'rescheduleStatusChart',
            'pie',
            rescheduleRequestRows.map(r => r.status),
            rescheduleRequestRows.map(r => Number(r.count || 0)),
            ['#06b6d4', '#22c55e', '#ef4444', '#a855f7'],
            'Reschedules'
        );

        makeChart(
            'workloadChart',
            'bar',
            caretakerWorkloadRows.slice(0, 10).map(r => r.name),
            caretakerWorkloadRows.slice(0, 10).map(r => Number(r.active_bookings || 0)),
            ['#0284c7', '#0d9488', '#65a30d', '#f59e0b', '#db2777', '#4f46e5'],
            'Active Bookings'
        );

        makeChart(
            'assignmentDistributionChart',
            'bar',
            assignmentDistributionRows.slice(0, 10).map(r => r.caretaker_name),
            assignmentDistributionRows.slice(0, 10).map(r => Number(r.assigned_bookings || 0)),
            ['#0369a1', '#16a34a', '#d97706', '#9333ea', '#e11d48', '#0891b2'],
            'Assigned Bookings'
        );
    </script>
</body>

</html>