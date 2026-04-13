<?php
$summary = $data['summary'] ?? [];
$bookingStatus = $data['bookingStatus'] ?? [];
$monthlyTrends = $data['monthlyTrends'] ?? [];
$serviceDistribution = $data['serviceDistribution'] ?? [];
$basisBreakdown = $data['basisBreakdown'] ?? [];
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
$rangeLabel = ($fromDate !== '' && $toDate !== '') ? 'Selected date range' : 'Last 6 months';

$reportsBaseUrl = URLROOT . '/public?url=admin/ad_reports';
$reportQuery = ['url' => 'admin/ad_reports'];
if ($fromDate !== '') {
    $reportQuery['from'] = $fromDate;
}
if ($toDate !== '') {
    $reportQuery['to'] = $toDate;
}
$exportCsvUrl = URLROOT . '/public?' . http_build_query(array_merge($reportQuery, ['export' => '1', 'format' => 'csv']));
$exportPdfUrl = URLROOT . '/public?' . http_build_query(array_merge($reportQuery, ['export' => '1', 'format' => 'pdf']));

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
</head>

<body class="admin-reports-page">
    <?php include_once APPROOT . '/views/templates/admin/ad_header.php'; ?>
    <?php include_once APPROOT . '/views/templates/admin/ad_sidebar.php'; ?>

    <div class="main-content reports-container">
        <div class="page-header reports-page-header">
            <div class="page-header-main">
                <h1 class="page-title">Reports</h1>
                <p class="page-subtitle">Financial and operational metrics from live booking and payment data.</p>
            </div>
            <div class="header-actions reports-header-actions">
                <form class="filter-section filters-inline reports-filters" method="get" action="<?php echo esc(URLROOT . '/public'); ?>">
                    <input type="hidden" name="url" value="admin/ad_reports">
                    <div class="filter-group">
                        <label for="fromDate">From</label>
                        <input type="date" id="fromDate" name="from" value="<?php echo esc($fromDate); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="toDate">To</label>
                        <input type="date" id="toDate" name="to" value="<?php echo esc($toDate); ?>">
                    </div>
                    <div class="filter-group filter-group--actions">
                        <button type="submit" class="btn primary">Apply</button>
                        <a class="btn ghost" href="<?php echo esc(URLROOT . '/public?url=admin/ad_reports'); ?>">Reset</a>
                    </div>
                </form>
                <div class="export-buttons">
                    <a class="btn secondary" href="<?php echo esc($exportCsvUrl); ?>">Export CSV</a>
                    <a class="btn secondary" href="<?php echo esc($exportPdfUrl); ?>">Export PDF</a>
                </div>
            </div>
        </div>

        <section class="summary-cards" aria-label="Summary">
            <article class="card">
                <span class="card-label">Total revenue</span>
                <span class="card-value">LKR <?php echo esc($summary['totalRevenue'] ?? '0.00'); ?></span>
            </article>
            <article class="card">
                <span class="card-label">Total bookings</span>
                <span class="card-value"><?php echo esc($summary['totalBookings'] ?? 0); ?></span>
            </article>
            <article class="card">
                <span class="card-label">Active caretakers</span>
                <span class="card-value"><?php echo esc($summary['activeCaretakers'] ?? 0); ?></span>
            </article>
            <article class="card">
                <span class="card-label">Total clients</span>
                <span class="card-value"><?php echo esc($summary['totalClients'] ?? 0); ?></span>
            </article>
            <article class="card">
                <span class="card-label">Pending payments</span>
                <span class="card-value"><?php echo esc($summary['pendingPayments'] ?? 0); ?></span>
            </article>
            <article class="card">
                <span class="card-label">Average rating</span>
                <span class="card-value"><?php echo esc($summary['avgRating'] ?? '0.00'); ?></span>
            </article>
        </section>

        <h2 class="reports-section-title">Charts</h2>
        <p class="reports-section-hint">Booking and payment mix use your date filter. The trend chart uses the same range when both dates are set; otherwise it shows the last six months.</p>

        <section class="panel-grid reports-charts-grid">
            <article class="panel chart-panel">
                <h3>Booking status</h3>
                <div class="chart-panel__body" id="wrap-bookingStatusChart">
                    <canvas id="bookingStatusChart" aria-label="Booking status chart"></canvas>
                    <p class="chart-empty" id="empty-bookingStatusChart" hidden>No bookings in this range.</p>
                </div>
            </article>
            <article class="panel chart-panel">
                <h3>Payment status</h3>
                <div class="chart-panel__body" id="wrap-paymentStatusChart">
                    <canvas id="paymentStatusChart" aria-label="Payment status chart"></canvas>
                    <p class="chart-empty" id="empty-paymentStatusChart" hidden>No payments in this range.</p>
                </div>
            </article>
            <article class="panel chart-panel chart-panel--span2">
                <h3>Bookings &amp; revenue by month</h3>
                <p class="panel-meta"><?php echo esc($rangeLabel); ?></p>
                <div class="chart-panel__body chart-panel__body--tall" id="wrap-monthlyTrendsChart">
                    <canvas id="monthlyTrendsChart" aria-label="Monthly trends chart"></canvas>
                    <p class="chart-empty" id="empty-monthlyTrendsChart" hidden>No monthly data for this range.</p>
                </div>
            </article>
        </section>

        <h2 class="reports-section-title">Breakdowns</h2>

        <section class="panel-grid two-col">
            <article class="panel">
                <h3>Revenue by service</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Revenue (LKR)</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($revenueByService)): ?>
                                <tr><td colspan="3" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($revenueByService as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['service_type'] ?? ''); ?></td>
                                        <td><?php echo esc(number_format((float) ($row['revenue'] ?? 0), 2)); ?></td>
                                        <td><?php echo esc($row['bookings'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="panel">
                <h3>Bookings by service type</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($serviceDistribution)): ?>
                                <tr><td colspan="2" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($serviceDistribution as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['service_type'] ?? ''); ?></td>
                                        <td><?php echo esc($row['count'] ?? 0); ?></td>
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
                <h3>Booking basis</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Basis</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($basisBreakdown)): ?>
                                <tr><td colspan="2" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($basisBreakdown as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['basis'] ?? ''); ?></td>
                                        <td><?php echo esc($row['count'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="panel">
                <h3>Advance vs final payments</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Count</th>
                                <th>Total (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($advanceVsFinal)): ?>
                                <tr><td colspan="3" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($advanceVsFinal as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['payment_type'] ?? ''); ?></td>
                                        <td><?php echo esc($row['count'] ?? 0); ?></td>
                                        <td><?php echo esc(number_format((float) ($row['total'] ?? 0), 2)); ?></td>
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
                <h3>Top districts (clients)</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>District</th>
                                <th>Clients</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clientLocations)): ?>
                                <tr><td colspan="2" class="empty">No location data</td></tr>
                            <?php else: ?>
                                <?php foreach ($clientLocations as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['district'] ?? ''); ?></td>
                                        <td><?php echo esc($row['count'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="panel compact">
                <h3>Refund status</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Total (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($refunds)): ?>
                                <tr><td colspan="3" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($refunds as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['status'] ?? ''); ?></td>
                                        <td><?php echo esc($row['count'] ?? 0); ?></td>
                                        <td><?php echo esc(number_format((float) ($row['total_amount'] ?? 0), 2)); ?></td>
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
                <h3>Top caretakers by bookings</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Bookings</th>
                                <th>Avg rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topCaretakersByBookings)): ?>
                                <tr><td colspan="4" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($topCaretakersByBookings as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['name'] ?? ''); ?></td>
                                        <td><?php echo esc($row['service_type'] ?? ''); ?></td>
                                        <td><?php echo esc($row['total_bookings'] ?? 0); ?></td>
                                        <td><?php echo esc(number_format((float) ($row['avg_rating'] ?? 0), 2)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="panel">
                <h3>Top caretakers by revenue</h3>
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
                                <tr><td colspan="4" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($topCaretakersByRevenue as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['name'] ?? ''); ?></td>
                                        <td><?php echo esc($row['service_type'] ?? ''); ?></td>
                                        <td>LKR <?php echo esc(number_format((float) ($row['total_revenue'] ?? 0), 2)); ?></td>
                                        <td><?php echo esc($row['total_bookings'] ?? 0); ?></td>
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
                <h3>Highest rated caretakers</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Service</th>
                                <th>Avg rating</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($highestRated)): ?>
                                <tr><td colspan="4" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($highestRated as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['name'] ?? ''); ?></td>
                                        <td><?php echo esc($row['service_type'] ?? ''); ?></td>
                                        <td><?php echo esc(number_format((float) ($row['avg_rating'] ?? 0), 2)); ?></td>
                                        <td><?php echo esc($row['total_bookings'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="panel">
                <h3>Top clients by spending</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Bookings</th>
                                <th>Total spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topClientsBySpending)): ?>
                                <tr><td colspan="4" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($topClientsBySpending as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['name'] ?? ''); ?></td>
                                        <td><?php echo esc($row['email'] ?? ''); ?></td>
                                        <td><?php echo esc($row['total_bookings'] ?? 0); ?></td>
                                        <td>LKR <?php echo esc(number_format((float) ($row['total_spent'] ?? 0), 2)); ?></td>
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
                <h3>Top clients by bookings</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Bookings</th>
                                <th>Total spent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topClientsByBookings)): ?>
                                <tr><td colspan="4" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($topClientsByBookings as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['name'] ?? ''); ?></td>
                                        <td><?php echo esc($row['email'] ?? ''); ?></td>
                                        <td><?php echo esc($row['total_bookings'] ?? 0); ?></td>
                                        <td>LKR <?php echo esc(number_format((float) ($row['total_spent'] ?? 0), 2)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="panel">
                <h3>Service ratings</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Avg rating</th>
                                <th>Feedback count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($serviceRatings)): ?>
                                <tr><td colspan="3" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($serviceRatings as $row): ?>
                                    <tr>
                                        <td><?php echo esc($row['service_type'] ?? ''); ?></td>
                                        <td><?php echo esc(number_format((float) ($row['avg_rating'] ?? 0), 2)); ?></td>
                                        <td><?php echo esc($row['feedback_count'] ?? 0); ?></td>
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
                <h3>Low rated bookings (&lt; 3.0)</h3>
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
                                <tr><td colspan="6" class="empty">No data</td></tr>
                            <?php else: ?>
                                <?php foreach ($lowRatedBookings as $row): ?>
                                    <tr>
                                        <td>#<?php echo esc($row['booking_id'] ?? '-'); ?></td>
                                        <td><?php echo esc($row['caretaker_name'] ?? '-'); ?></td>
                                        <td><?php echo esc($row['client_name'] ?? '-'); ?></td>
                                        <td><?php echo esc($row['service_type'] ?? '-'); ?></td>
                                        <td><?php echo esc(number_format((float) ($row['rating'] ?? 0), 2)); ?></td>
                                        <td><?php echo esc($row['booking_date'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
            <article class="panel compact">
                <h3>Complaints &amp; snapshot</h3>
                <div class="stat-list">
                    <div><span>Client complaints</span><strong><?php echo esc($complaints['client_complaints'] ?? 0); ?></strong></div>
                    <div><span>Caretaker complaints</span><strong><?php echo esc($complaints['caretaker_complaints'] ?? 0); ?></strong></div>
                    <div><span>Total complaints</span><strong><?php echo esc($complaints['total_complaints'] ?? 0); ?></strong></div>
                    <div><span>New clients (30d)</span><strong><?php echo esc($data['newClients'] ?? 0); ?></strong></div>
                    <div><span>Caretaker status groups</span><strong><?php echo esc(count($caretakerStatus)); ?></strong></div>
                    <div><span>Active service categories</span><strong><?php echo esc(count($caretakerServices)); ?></strong></div>
                </div>
            </article>
        </section>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var bookingStatusRows = <?php echo json_encode($bookingStatus, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var paymentStatusRows = <?php echo json_encode($paymentStatus, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        var monthlyTrendsRows = <?php echo json_encode($monthlyTrends, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

        function showChartEmpty(canvasId, empty) {
            var canvas = document.getElementById(canvasId);
            var wrap = document.getElementById('wrap-' + canvasId);
            var msg = document.getElementById('empty-' + canvasId);
            if (canvas) canvas.hidden = empty;
            if (wrap && msg) {
                msg.hidden = !empty;
                if (empty) wrap.classList.add('chart-panel__body--empty');
                else wrap.classList.remove('chart-panel__body--empty');
            }
        }

        function doughnutOrPie(id, type, rows, labelKey, valueKey, colors, datasetLabel) {
            var labels = (rows || []).map(function (r) { return String(r[labelKey] != null ? r[labelKey] : ''); });
            var values = (rows || []).map(function (r) { return Number(r[valueKey] || 0); });
            var sum = values.reduce(function (a, b) { return a + b; }, 0);
            if (!sum) {
                showChartEmpty(id, true);
                return null;
            }
            showChartEmpty(id, false);
            var el = document.getElementById(id);
            if (!el || typeof Chart === 'undefined') return null;
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

        doughnutOrPie('bookingStatusChart', 'doughnut', bookingStatusRows, 'status', 'count',
            ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#6366f1', '#64748b'], 'Bookings');
        doughnutOrPie('paymentStatusChart', 'pie', paymentStatusRows, 'status', 'count',
            ['#22c55e', '#f59e0b', '#ef4444', '#3b82f6', '#94a3b8'], 'Payments');

        var trendLabels = (monthlyTrendsRows || []).map(function (r) { return String(r.month_label || ''); });
        var trendBookings = (monthlyTrendsRows || []).map(function (r) { return Number(r.bookings || 0); });
        var trendRevenue = (monthlyTrendsRows || []).map(function (r) { return Number(r.revenue || 0); });
        var trendHas = trendLabels.length > 0 && (trendBookings.some(function (n) { return n > 0; }) || trendRevenue.some(function (n) { return n > 0; }));

        if (!trendHas) {
            showChartEmpty('monthlyTrendsChart', true);
        } else {
            showChartEmpty('monthlyTrendsChart', false);
            var ctx = document.getElementById('monthlyTrendsChart');
            if (ctx && typeof Chart !== 'undefined') {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: trendLabels,
                        datasets: [
                            {
                                type: 'bar',
                                label: 'Bookings',
                                data: trendBookings,
                                backgroundColor: 'rgba(30, 136, 229, 0.35)',
                                borderColor: 'rgba(30, 136, 229, 0.9)',
                                borderWidth: 1,
                                yAxisID: 'y'
                            },
                            {
                                type: 'line',
                                label: 'Revenue (LKR)',
                                data: trendRevenue,
                                borderColor: 'rgba(5, 150, 105, 1)',
                                backgroundColor: 'rgba(5, 150, 105, 0.08)',
                                borderWidth: 2,
                                tension: 0.25,
                                fill: true,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            y: {
                                type: 'linear',
                                position: 'left',
                                beginAtZero: true,
                                title: { display: true, text: 'Bookings' }
                            },
                            y1: {
                                type: 'linear',
                                position: 'right',
                                beginAtZero: true,
                                grid: { drawOnChartArea: false },
                                title: { display: true, text: 'Revenue (LKR)' }
                            }
                        },
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        }
    });
    </script>
</body>

</html>
