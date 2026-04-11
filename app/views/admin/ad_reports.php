<?php
$summary = $data['summary'] ?? [];
$bookingStatus = $data['bookingStatus'] ?? [];
$monthlyBookings = $data['monthlyBookings'] ?? [];
$serviceDistribution = $data['serviceDistribution'] ?? [];
$basisBreakdown = $data['basisBreakdown'] ?? [];
$monthlyRevenue = $data['monthlyRevenue'] ?? [];
$revenueByService = $data['revenueByService'] ?? [];
$paymentStatus = $data['paymentStatus'] ?? [];
$advanceVsFinal = $data['advanceVsFinal'] ?? [];
$refunds = $data['refunds'] ?? [];
$topCaretakersByBookings = $data['topCaretakersByBookings'] ?? [];
$topCaretakersByRevenue = $data['topCaretakersByRevenue'] ?? [];
$highestRated = $data['highestRated'] ?? [];
$caretakerStatus = $data['caretakerStatus'] ?? [];
$caretakerServices = $data['caretakerServices'] ?? [];
$topClientsByBookings = $data['topClientsByBookings'] ?? [];
$topClientsBySpending = $data['topClientsBySpending'] ?? [];
$clientLocations = $data['clientLocations'] ?? [];
$serviceRatings = $data['serviceRatings'] ?? [];
$lowRatedBookings = $data['lowRatedBookings'] ?? [];
$complaints = $data['complaints'] ?? [];
$fromDate = $data['fromDate'] ?? '';
$toDate = $data['toDate'] ?? '';

function esc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_reports.css">
    <!-- Design System Override (ensures consistency) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
    <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
    <div class="reports-container">
        <div class="reports-header">
            <div>
                <h2>Admin Reports</h2>
                <p>Financial and operational analytics overview</p>
            </div>

            <div class="header-actions">
                <div class="filters filters-inline">
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
                <span class="card-label">Total Revenue</span>
                <span class="card-value">LKR <?= esc($summary['totalRevenue'] ?? '0.00') ?></span>
            </article>
            <article class="card">
                <span class="card-label">Total Bookings</span>
                <span class="card-value"><?= esc($summary['totalBookings'] ?? 0) ?></span>
            </article>
            <article class="card">
                <span class="card-label">Active Caretakers</span>
                <span class="card-value"><?= esc($summary['activeCaretakers'] ?? 0) ?></span>
            </article>
            <article class="card">
                <span class="card-label">Total Clients</span>
                <span class="card-value"><?= esc($summary['totalClients'] ?? 0) ?></span>
            </article>
            <article class="card">
                <span class="card-label">Pending Payments</span>
                <span class="card-value"><?= esc($summary['pendingPayments'] ?? 0) ?></span>
            </article>
            <article class="card">
                <span class="card-label">Average Rating</span>
                <span class="card-value"><?= esc($summary['avgRating'] ?? '0.00') ?></span>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel chart-panel">
                <h3>Booking Status</h3>
                <canvas id="bookingStatusChart"></canvas>
            </article>
            <article class="panel chart-panel">
                <h3>Monthly Bookings</h3>
                <canvas id="monthlyBookingsChart"></canvas>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel chart-panel">
                <h3>Revenue by Service</h3>
                <canvas id="revenueByServiceChart"></canvas>
            </article>
            <article class="panel chart-panel">
                <h3>Monthly Revenue</h3>
                <canvas id="monthlyRevenueChart"></canvas>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel chart-panel">
                <h3>Payment Status</h3>
                <canvas id="paymentStatusChart"></canvas>
            </article>
            <article class="panel chart-panel">
                <h3>Service Type Distribution</h3>
                <canvas id="serviceTypeChart"></canvas>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel chart-panel">
                <h3>Booking Basis Breakdown</h3>
                <canvas id="basisBreakdownChart"></canvas>
            </article>
            <article class="panel chart-panel">
                <h3>Advance vs Final Payments</h3>
                <canvas id="advanceVsFinalChart"></canvas>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel">
                <h3>Top Caretakers by Bookings</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Bookings</th>
                                <th>Avg Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topCaretakersByBookings)): ?>
                                <tr>
                                    <td colspan="4" class="empty">No data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topCaretakersByBookings as $row): ?>
                                    <tr>
                                        <td><?= esc($row['name'] ?? '') ?></td>
                                        <td><?= esc($row['service_type'] ?? '') ?></td>
                                        <td><?= esc($row['total_bookings'] ?? 0) ?></td>
                                        <td><?= esc(number_format((float)($row['avg_rating'] ?? 0), 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel">
                <h3>Top Caretakers by Revenue</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Revenue</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topCaretakersByRevenue)): ?>
                                <tr>
                                    <td colspan="4" class="empty">No data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topCaretakersByRevenue as $row): ?>
                                    <tr>
                                        <td><?= esc($row['name'] ?? '') ?></td>
                                        <td><?= esc($row['service_type'] ?? '') ?></td>
                                        <td>LKR <?= esc(number_format((float)($row['total_revenue'] ?? 0), 2)) ?></td>
                                        <td><?= esc($row['total_bookings'] ?? 0) ?></td>
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
                <h3>Highest Rated Caretakers</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Avg Rating</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($highestRated)): ?>
                                <tr>
                                    <td colspan="4" class="empty">No data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($highestRated as $row): ?>
                                    <tr>
                                        <td><?= esc($row['name'] ?? '') ?></td>
                                        <td><?= esc($row['service_type'] ?? '') ?></td>
                                        <td><?= esc(number_format((float)($row['avg_rating'] ?? 0), 2)) ?></td>
                                        <td><?= esc($row['total_bookings'] ?? 0) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel">
                <h3>Top Clients by Spending</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Bookings</th>
                                <th>Total Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topClientsBySpending)): ?>
                                <tr>
                                    <td colspan="4" class="empty">No data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topClientsBySpending as $row): ?>
                                    <tr>
                                        <td><?= esc($row['name'] ?? '') ?></td>
                                        <td><?= esc($row['email'] ?? '') ?></td>
                                        <td><?= esc($row['total_bookings'] ?? 0) ?></td>
                                        <td>LKR <?= esc(number_format((float)($row['total_spent'] ?? 0), 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel">
                <h3>Service Ratings</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Avg Rating</th>
                                <th>Feedback Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($serviceRatings)): ?>
                                <tr>
                                    <td colspan="3" class="empty">No data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($serviceRatings as $row): ?>
                                    <tr>
                                        <td><?= esc($row['service_type'] ?? '') ?></td>
                                        <td><?= esc(number_format((float)($row['avg_rating'] ?? 0), 2)) ?></td>
                                        <td><?= esc($row['feedback_count'] ?? 0) ?></td>
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
                <h3>Top Clients by Bookings</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Total Bookings</th>
                                <th>Total Spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topClientsByBookings)): ?>
                                <tr>
                                    <td colspan="4" class="empty">No data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topClientsByBookings as $row): ?>
                                    <tr>
                                        <td><?= esc($row['name'] ?? '') ?></td>
                                        <td><?= esc($row['email'] ?? '') ?></td>
                                        <td><?= esc($row['total_bookings'] ?? 0) ?></td>
                                        <td>LKR <?= esc(number_format((float)($row['total_spent'] ?? 0), 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel chart-panel">
                <h3>Client Location Distribution</h3>
                <canvas id="clientLocationChart"></canvas>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel compact">
                <h3>Refund Status</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($refunds)): ?>
                                <tr>
                                    <td colspan="3" class="empty">No data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($refunds as $row): ?>
                                    <tr>
                                        <td><?= esc($row['status'] ?? '') ?></td>
                                        <td><?= esc($row['count'] ?? 0) ?></td>
                                        <td>LKR <?= esc(number_format((float)($row['total_amount'] ?? 0), 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel compact">
                <h3>Complaint Snapshot</h3>
                <div class="stat-list">
                    <div><span>Client Complaints</span><strong><?= esc($complaints['client_complaints'] ?? 0) ?></strong></div>
                    <div><span>Caretaker Complaints</span><strong><?= esc($complaints['caretaker_complaints'] ?? 0) ?></strong></div>
                    <div><span>Total Complaints</span><strong><?= esc($complaints['total_complaints'] ?? 0) ?></strong></div>
                    <div><span>New Clients (30d)</span><strong><?= esc($data['newClients'] ?? 0) ?></strong></div>
                </div>
            </article>
        </section>

        <section class="panel-grid two-col">
            <article class="panel">
                <h3>Low Rated Bookings (&lt; 3.0)</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Caretaker</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Rating</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lowRatedBookings)): ?>
                                <tr>
                                    <td colspan="6" class="empty">No data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($lowRatedBookings as $row): ?>
                                    <tr>
                                        <td>#<?= esc($row['booking_id'] ?? '-') ?></td>
                                        <td><?= esc($row['caretaker_name'] ?? '-') ?></td>
                                        <td><?= esc($row['client_name'] ?? '-') ?></td>
                                        <td><?= esc($row['service_type'] ?? '-') ?></td>
                                        <td><?= esc(number_format((float)($row['rating'] ?? 0), 2)) ?></td>
                                        <td><?= esc($row['booking_date'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel compact">
                <h3>Caretaker Snapshot</h3>
                <div class="stat-list">
                    <div><span>Active Caretakers</span><strong><?= esc($summary['activeCaretakers'] ?? 0) ?></strong></div>
                    <div><span>All Caretaker States</span><strong><?= esc(count($caretakerStatus)) ?></strong></div>
                    <div><span>Service Categories</span><strong><?= esc(count($caretakerServices)) ?></strong></div>
                    <div><span>Top Client Areas</span><strong><?= esc(count($clientLocations)) ?></strong></div>
                </div>
            </article>
        </section>
    </div>

    <script>
        const bookingStatusRows = <?= json_encode($bookingStatus) ?>;
        const monthlyBookingsRows = <?= json_encode($monthlyBookings) ?>;
        const monthlyRevenueRows = <?= json_encode($monthlyRevenue) ?>;
        const serviceDistributionRows = <?= json_encode($serviceDistribution) ?>;
        const revenueByServiceRows = <?= json_encode($revenueByService) ?>;
        const paymentStatusRows = <?= json_encode($paymentStatus) ?>;
        const basisBreakdownRows = <?= json_encode($basisBreakdown) ?>;
        const advanceVsFinalRows = <?= json_encode($advanceVsFinal) ?>;
        const clientLocationRows = <?= json_encode($clientLocations) ?>;
        const adminUrl = `${<?= json_encode(URLROOT) ?>}/admin/ad_reports`;

        function applyFilters() {
            const from = document.getElementById('fromDate').value;
            const to = document.getElementById('toDate').value;
            const params = new URLSearchParams();
            if (from) params.set('from', from);
            if (to) params.set('to', to);
            window.location.href = `${adminUrl}?${params.toString()}`;
        }

        function exportReport(format) {
            const from = document.getElementById('fromDate').value;
            const to = document.getElementById('toDate').value;
            const params = new URLSearchParams();
            params.set('export', '1');
            params.set('format', format);
            if (from) params.set('from', from);
            if (to) params.set('to', to);
            window.location.href = `${adminUrl}?${params.toString()}`;
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
            'bookingStatusChart',
            'doughnut',
            bookingStatusRows.map(r => r.status),
            bookingStatusRows.map(r => Number(r.count || 0)),
            ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#6366f1', '#64748b'],
            'Bookings'
        );

        makeChart(
            'monthlyBookingsChart',
            'line',
            monthlyBookingsRows.map(r => r.month_label),
            monthlyBookingsRows.map(r => Number(r.count || 0)),
            'rgba(59,130,246,0.2)',
            'Bookings'
        );

        makeChart(
            'monthlyRevenueChart',
            'line',
            monthlyRevenueRows.map(r => r.month_label),
            monthlyRevenueRows.map(r => Number(r.revenue || 0)),
            'rgba(16,185,129,0.2)',
            'Revenue'
        );

        makeChart(
            'serviceTypeChart',
            'bar',
            serviceDistributionRows.map(r => r.service_type),
            serviceDistributionRows.map(r => Number(r.count || 0)),
            ['#0ea5e9', '#22c55e', '#f97316', '#8b5cf6', '#ef4444', '#14b8a6'],
            'Services'
        );

        makeChart(
            'revenueByServiceChart',
            'bar',
            revenueByServiceRows.map(r => r.service_type),
            revenueByServiceRows.map(r => Number(r.revenue || 0)),
            ['#16a34a', '#84cc16', '#06b6d4', '#f59e0b', '#e11d48', '#0f766e'],
            'Revenue'
        );

        makeChart(
            'paymentStatusChart',
            'pie',
            paymentStatusRows.map(r => r.status),
            paymentStatusRows.map(r => Number(r.count || 0)),
            ['#22c55e', '#f59e0b', '#ef4444', '#3b82f6'],
            'Payments'
        );

        makeChart(
            'basisBreakdownChart',
            'bar',
            basisBreakdownRows.map(r => r.basis),
            basisBreakdownRows.map(r => Number(r.count || 0)),
            ['#0891b2', '#0284c7', '#7c3aed', '#be123c', '#65a30d'],
            'Bookings by Basis'
        );

        makeChart(
            'advanceVsFinalChart',
            'doughnut',
            advanceVsFinalRows.map(r => r.payment_type),
            advanceVsFinalRows.map(r => Number(r.total || 0)),
            ['#0ea5e9', '#16a34a', '#f59e0b', '#a855f7'],
            'Payment Amount'
        );

        makeChart(
            'clientLocationChart',
            'bar',
            clientLocationRows.map(r => r.district),
            clientLocationRows.map(r => Number(r.count || 0)),
            ['#0369a1', '#0d9488', '#f97316', '#84cc16', '#db2777', '#4338ca'],
            'Clients'
        );
    </script>
</body>

</html>
