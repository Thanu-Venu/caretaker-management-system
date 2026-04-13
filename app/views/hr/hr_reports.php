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
    <div id="reportContent">
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
                <span class="card-label">Active Caregivers</span>
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
                <span class="card-label">Avg Caregiver Rating</span>
                <span class="card-value"><?= esc(number_format($avgCaretakerRating, 2)) ?></span>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel chart-panel">
                <h3>Caregiver Status</h3>
                <canvas id="caretakerStatusChart"></canvas>
            </article>
            <article class="panel chart-panel">
                <h3>Caregiver Service Mix</h3>
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
                    <div><span>Total Caregivers</span><strong><?= esc($summary['totalCaretakers'] ?? 0) ?></strong></div>
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
                                <th>Caregiver</th>
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
                <h3>Caregivers Currently on Leave</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Caregiver</th>
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
        </section>

        <section class="panel-grid two-col">
            <article class="panel">
                <h3>Caregiver Feedback Snapshot</h3>
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
                <h3>Caregiver Complaints</h3>
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
                                <th>Caregiver</th>
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
                                <th>Caregiver</th>
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
    </div>

    <script>
  const URLROOT = "<?= URLROOT ?>";

  const caretakerStatusRows = <?= json_encode($caretakerStatus) ?>;
  const caretakersByServiceRows = <?= json_encode($caretakersByService) ?>;
  const caretakerWorkloadRows = <?= json_encode($caretakerWorkload) ?>;
  const assignmentDistributionRows = <?= json_encode($assignmentDistribution) ?>;
  const leaveRequestsRows = <?= json_encode($leaveRequests) ?>;
  const rescheduleRequestRows = <?= json_encode($rescheduleRequests) ?>;
</script>

<script src="<?= URLROOT ?>/public/js/hr/hr_reports.js"></script>
</body>

</html>