<?php
$hrPageTitle = 'HR Dashboard — SmartCare';
$hrExtraCss = ['shared/staff_dashboard_hero.css', 'hr/hr_dashboard.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

$totalCaretakers = (int) ($data['totalCaretakers'] ?? 0);
$activeServices = (int) ($data['activeServices'] ?? 0);
$pendingLeave = (int) ($data['pendingLeave'] ?? 0);
$pendingRequests = (int) ($data['pendingRequests'] ?? 0);

$recentLeaves = $data['recentLeaves'] ?? [];
$recentComplaints = $data['recentComplaints'] ?? [];
$recentBookings = $data['recentBookings'] ?? [];

$bc = $data['badgeCounts'] ?? [];
$bcBookings = (int) ($bc['bookings'] ?? 0);
$bcPayments = (int) ($bc['payments'] ?? 0);
$bcLeave = (int) ($bc['leave_requests'] ?? 0);
$bcChange = (int) ($bc['change_requests'] ?? 0);
$bcReschedule = (int) ($bc['reschedule_requests'] ?? 0);
$bcComplaints = (int) ($bc['complaints'] ?? 0);
$bcCaretaker = (int) ($bc['caretaker_requests'] ?? 0);

$bookingStatusLabels = $data['bookingStatusLabels'] ?? json_encode([]);
$bookingStatusCounts = $data['bookingStatusCounts'] ?? json_encode([]);
$bookingStatusColors = $data['bookingStatusColors'] ?? json_encode([]);
$performanceLabels = $data['performanceLabels'] ?? json_encode([]);
$performanceCounts = $data['performanceCounts'] ?? json_encode([]);
$performanceColors = $data['performanceColors'] ?? json_encode([]);
$hrDisplayName = trim((string) ($_SESSION['user']['username'] ?? ($_SESSION['user']['name'] ?? 'Manager')));
?>

<main class="main-content managerHome">
 <div class="managerHomeInner">

 <section class="staff-dashboard-hero staff-dashboard-hero--hr" aria-labelledby="hrDashboardHeroTitle">
 <div class="staff-dashboard-hero__content">
 <div class="staff-dashboard-hero__intro">
 <p class="staff-dashboard-hero__eyebrow">HR &amp; scheduling</p>
 <h1 id="hrDashboardHeroTitle" class="staff-dashboard-hero__title">
 <span class="staff-dashboard-hero__greeting">Welcome back,</span>
 <span class="staff-dashboard-hero__name"><?= htmlspecialchars($hrDisplayName, ENT_QUOTES, 'UTF-8') ?></span>
 </h1>
 <p class="staff-dashboard-hero__lead">Coordinate caregivers, approve leave, and keep client requests moving — your dashboard surfaces what needs action first.</p>

 <div class="staff-dashboard-hero__actions" role="group" aria-label="Primary HR actions">
 <a class="btn staff-dashboard-hero__btn-primary" href="<?= URLROOT ?>/public?url=hr/hr_pending_request">
 <i class="bx bx-inbox" aria-hidden="true"></i>
 <span>Pending requests</span>
 </a>
 <a class="btn secondary staff-dashboard-hero__btn-secondary" href="<?= URLROOT ?>/HRCaretakerCRUD/list">
 <i class="bx bx-group" aria-hidden="true"></i>
 <span>Caregiver roster</span>
 </a>
 </div>

 <div class="staff-dashboard-hero__highlights" aria-label="Jump to dashboard sections">
 <a class="staff-dashboard-hero__highlight-item staff-dashboard-hero__highlight-link" href="#hrReviewSection">
 <i class="bx bx-error-circle" aria-hidden="true"></i>
 <div>
 <p class="staff-dashboard-hero__highlight-value">Queues &amp; alerts</p>
 </div>
 </a>
 <a class="staff-dashboard-hero__highlight-item staff-dashboard-hero__highlight-link" href="#hrStatsSection">
 <i class="bx bx-grid-alt" aria-hidden="true"></i>
 <div>
 <p class="staff-dashboard-hero__highlight-value">Today's KPIs</p>
 </div>
 </a>
 <a class="staff-dashboard-hero__highlight-item staff-dashboard-hero__highlight-link" href="#hrOverviewSection">
 <i class="bx bx-pie-chart-alt-2" aria-hidden="true"></i>
 <div>
 <p class="staff-dashboard-hero__highlight-value">Pipeline charts</p>
 </div>
 </a>
 <a class="staff-dashboard-hero__highlight-item staff-dashboard-hero__highlight-item--support staff-dashboard-hero__highlight-link" href="#hrActivitySection">
 <i class="bx bx-time-five" aria-hidden="true"></i>
 <div>
 <p class="staff-dashboard-hero__highlight-value">Recent activity</p>
 </div>
 </a>
 </div>
 </div>
 </div>
 </section>

 <section id="hrReviewSection" class="reviewBlock staff-dashboard-scroll-target" aria-label="Quick review — needs attention">
 <h2 class="smallHeading">Quick review</h2>
 <div class="reviewGrid">
 <a href="<?= URLROOT ?>/public?url=hr/hr_pending_request" class="reviewTile blue">
 <span class="reviewLabel">Pending requests</span>
 <span class="reviewCount"><?= $bcBookings ?></span>
 <span class="reviewHint">Bookings awaiting action</span>
 <span class="reviewAction">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
 </a>
 <a href="<?= URLROOT ?>/public?url=hr/pendingPayments" class="reviewTile teal">
 <span class="reviewLabel">Pending payments</span>
 <span class="reviewCount"><?= $bcPayments ?></span>
 <span class="reviewHint">Advance &amp; balances</span>
 <span class="reviewAction">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
 </a>
 <a href="<?= URLROOT ?>/HrLeave/index" class="reviewTile purple">
 <span class="reviewLabel">Leave queue</span>
 <span class="reviewCount"><?= $bcLeave ?></span>
 <span class="reviewHint">Awaiting approval</span>
 <span class="reviewAction">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
 </a>
 <a href="<?= URLROOT ?>/public?url=hr/changeRequests" class="reviewTile amber">
 <span class="reviewLabel">Change requests</span>
 <span class="reviewCount"><?= $bcChange ?></span>
 <span class="reviewHint">Caregiver swaps</span>
 <span class="reviewAction">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
 </a>
 <a href="<?= URLROOT ?>/public?url=hr/rescheduleRequests" class="reviewTile rose">
 <span class="reviewLabel">Reschedule requests</span>
 <span class="reviewCount"><?= $bcReschedule ?></span>
 <span class="reviewHint">Date change queue</span>
 <span class="reviewAction">View <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
 </a>
 <a href="<?= URLROOT ?>/public/index.php?url=Complaint/index" class="reviewTile slate">
 <span class="reviewLabel">Open complaints</span>
 <span class="reviewCount"><?= $bcComplaints ?></span>
 <span class="reviewHint">Needs resolution</span>
 <span class="reviewAction">Browse <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
 </a>
 </div>
 </section>

 <?php if ($bcCaretaker > 0): ?>
 <section class="reviewBlock reviewFollow" aria-label="Caretaker approvals">
 <h2 class="smallHeading">Caregiver onboarding</h2>
 <div class="reviewGrid">
 <a href="<?= URLROOT ?>/HRCaretakerCRUD/list" class="reviewTile purple">
 <span class="reviewLabel">Caretaker approvals</span>
 <span class="reviewCount"><?= $bcCaretaker ?></span>
 <span class="reviewHint">Profiles pending verification</span>
 <span class="reviewAction">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
 </a>
 </div>
 </section>
 <?php endif; ?>

 <section class="shortcutSection" aria-label="HR shortcuts">
 <h2 class="smallHeading">Shortcuts</h2>
 <div class="shortcutGrid">
 <a href="<?= URLROOT ?>/public?url=hr/paymentMonitor" class="shortcutTile">
 <span class="shortcutGlyph"><i class="bx bx-line-chart" aria-hidden="true"></i></span>
 <span class="shortcutName">Payment monitor</span>
 <span class="shortcutNote">Track inflows</span>
 </a>
 <a href="<?= URLROOT ?>/hr/refunds" class="shortcutTile">
 <span class="shortcutGlyph"><i class="bx bx-receipt" aria-hidden="true"></i></span>
 <span class="shortcutName">Refunds</span>
 <span class="shortcutNote">Process returns</span>
 </a>
 <a href="<?= URLROOT ?>/public?url=hr/hr_feedback" class="shortcutTile">
 <span class="shortcutGlyph"><i class="bx bx-message-detail" aria-hidden="true"></i></span>
 <span class="shortcutName">Feedback</span>
 <span class="shortcutNote">Client ratings</span>
 </a>
 <a href="<?= URLROOT ?>/public?url=hr/hr_announcement" class="shortcutTile">
 <span class="shortcutGlyph"><i class="bx bxs-megaphone" aria-hidden="true"></i></span>
 <span class="shortcutName">Announcements</span>
 <span class="shortcutNote">Post updates</span>
 </a>
 <a href="<?= URLROOT ?>/public?url=hr/hr_logs" class="shortcutTile">
 <span class="shortcutGlyph"><i class="bx bx-history" aria-hidden="true"></i></span>
 <span class="shortcutName">Activity log</span>
 <span class="shortcutNote">HR actions</span>
 </a>
 <a href="<?= URLROOT ?>/public?url=hr/hr_settings" class="shortcutTile">
 <span class="shortcutGlyph"><i class="bx bx-cog" aria-hidden="true"></i></span>
 <span class="shortcutName">Settings</span>
 <span class="shortcutNote">Your profile</span>
 </a>
 </div>
 </section>

 <div id="hrStatsSection" class="stats-grid statsBand staff-dashboard-scroll-target">
 <div class="stat-card card-hover kpiTile blue">
 <div class="stat-card-icon"><i class="bx bx-user-circle" aria-hidden="true"></i></div>
 <div class="stat-card-label">Total caregivers</div>
 <div class="stat-card-value"><?= $totalCaretakers ?></div>
 <a href="<?= URLROOT ?>/HRCaretakerCRUD/list" class="stat-card-link">Manage caregivers</a>
 </div>
 <div class="stat-card card-hover kpiTile teal">
 <div class="stat-card-icon"><i class="bx bxs-bolt" aria-hidden="true"></i></div>
 <div class="stat-card-label">Active services today</div>
 <div class="stat-card-value"><?= $activeServices ?></div>
 <a href="<?= URLROOT ?>/public?url=hr/hr_schedule" class="stat-card-link">View schedule</a>
 </div>
 <div class="stat-card card-hover kpiTile violet">
 <div class="stat-card-icon"><i class="bx bx-time" aria-hidden="true"></i></div>
 <div class="stat-card-label">Pending leave</div>
 <div class="stat-card-value"><?= $pendingLeave ?></div>
 <a href="<?= URLROOT ?>/HrLeave/index" class="stat-card-link">Review leave</a>
 </div>
 <div class="stat-card card-hover kpiTile indigo">
 <div class="stat-card-icon"><i class="bx bxs-inbox" aria-hidden="true"></i></div>
 <div class="stat-card-label">Requested bookings</div>
 <div class="stat-card-value"><?= $pendingRequests ?></div>
 <a href="<?= URLROOT ?>/public?url=hr/hr_pending_request" class="stat-card-link">Open queue</a>
 </div>
 <div class="stat-card card-hover kpiTile ocean">
 <div class="stat-card-icon"><i class="bx bx-error-circle" aria-hidden="true"></i></div>
 <div class="stat-card-label">Open complaints</div>
 <div class="stat-card-value"><?= $bcComplaints ?></div>
 <a href="<?= URLROOT ?>/public/index.php?url=Complaint/index" class="stat-card-link">Resolve issues</a>
 </div>
 </div>

 <section id="hrOverviewSection" class="chartSection staff-dashboard-scroll-target" aria-label="Overview charts">
 <h2 class="smallHeading">Overview</h2>
 <div class="chartGrid twoCols">
 <div class="card insightCard">
 <div class="card-header">
 <h3 class="card-title">Bookings by status</h3>
 <p class="insightSub">Distribution in your pipeline</p>
 </div>
 <div class="card-body insightCanvas">
 <canvas id="bookingChart" aria-label="Bookings by status chart"></canvas>
 </div>
 </div>
 <div class="card insightCard">
 <div class="card-header">
 <h3 class="card-title">Caregiver performance</h3>
 <p class="insightSub">Ratings overview</p>
 </div>
 <div class="card-body insightCanvas">
 <canvas id="performanceChart" aria-label="Performance ratings chart"></canvas>
 </div>
 </div>
 </div>
 </section>

 <section id="hrActivitySection" class="activitySection staff-dashboard-scroll-target" aria-label="Recent activity">
 <h2 class="smallHeading">Recent activity</h2>
 <div class="activityGrid">
 <div class="card">
 <div class="card-header">
 <h3 class="card-title">Recent leave</h3>
 </div>
 <div class="card-body">
 <ul class="recentList">
 <?php if (empty($recentLeaves)): ?>
 <li class="recentEmpty">No pending leave rows.</li>
 <?php else: ?>
 <?php foreach ($recentLeaves as $l): ?>
 <?php
 $caretakerId = $l['user_id'] ?? '';
 $startDate = $l['start_date'] ?? '';
 $endDate = $l['end_date'] ?? '';
 ?>
 <li class="recentRow">
 <div>
 <div class="recentRowTitle">Caregiver #<?= htmlspecialchars((string) $caretakerId, ENT_QUOTES, 'UTF-8') ?></div>
 <div class="recentRowMeta"><?= htmlspecialchars((string) $startDate, ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars((string) $endDate, ENT_QUOTES, 'UTF-8') ?></div>
 </div>
 <a class="recentRowLink" href="<?= URLROOT ?>/HrLeave/index">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></a>
 </li>
 <?php endforeach; ?>
 <?php endif; ?>
 </ul>
 </div>
 </div>
 <div class="card">
 <div class="card-header">
 <h3 class="card-title">Recent complaints</h3>
 </div>
 <div class="card-body">
 <ul class="recentList">
 <?php if (empty($recentComplaints)): ?>
 <li class="recentEmpty">No open complaints in the feed.</li>
 <?php else: ?>
 <?php foreach ($recentComplaints as $c): ?>
 <?php
 $clientName = $c['client_name'] ?? '';
 $caretakerName = $c['caretaker_name'] ?? '';
 $category = $c['category'] ?? '';
 ?>
 <li class="recentRow">
 <div>
 <div class="recentRowTitle"><?= htmlspecialchars((string) $clientName, ENT_QUOTES, 'UTF-8') ?></div>
 <div class="recentRowMeta"><?= htmlspecialchars((string) $caretakerName, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars((string) $category, ENT_QUOTES, 'UTF-8') ?></div>
 </div>
 <a class="recentRowLink" href="<?= URLROOT ?>/public/index.php?url=Complaint/index">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></a>
 </li>
 <?php endforeach; ?>
 <?php endif; ?>
 </ul>
 </div>
 </div>
 <div class="card">
 <div class="card-header">
 <h3 class="card-title">Recent booking requests</h3>
 </div>
 <div class="card-body">
 <ul class="recentList">
 <?php if (empty($recentBookings)): ?>
 <li class="recentEmpty">No recent requested bookings.</li>
 <?php else: ?>
 <?php foreach ($recentBookings as $b): ?>
 <?php
 $bookingId = is_array($b) ? ($b['booking_id'] ?? ($b['id'] ?? '')) : ($b->booking_id ?? ($b->id ?? ''));
 $clientName = is_array($b) ? ($b['client_name'] ?? '') : ($b->client_name ?? '');
 $serviceType = is_array($b) ? ($b['service_type'] ?? '') : ($b->service_type ?? '');
 $date = is_array($b) ? ($b['booking_date'] ?? ($b['date'] ?? '')) : ($b->booking_date ?? ($b->date ?? ''));
 $time = is_array($b) ? ($b['preferred_time'] ?? ($b['time'] ?? '')) : ($b->preferred_time ?? ($b->time ?? ''));
 ?>
 <li class="recentRow">
 <div>
 <div class="recentRowTitle">#<?= htmlspecialchars((string) $bookingId, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string) $clientName, ENT_QUOTES, 'UTF-8') ?></div>
 <div class="recentRowMeta"><?= htmlspecialchars((string) $serviceType, ENT_QUOTES, 'UTF-8') ?><?= $date !== '' ? ' · ' . htmlspecialchars((string) $date, ENT_QUOTES, 'UTF-8') : '' ?><?= $time !== '' ? ' · ' . htmlspecialchars((string) $time, ENT_QUOTES, 'UTF-8') : '' ?></div>
 </div>
 <a class="recentRowLink" href="<?= URLROOT ?>/public?url=hr/hr_pending_request">View <i class="bx bx-chevron-right" aria-hidden="true"></i></a>
 </li>
 <?php endforeach; ?>
 <?php endif; ?>
 </ul>
 </div>
 </div>
 </div>
 </section>

 </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
 const bookingStatusLabels = <?= $bookingStatusLabels ?>;
 const bookingStatusCounts = <?= $bookingStatusCounts ?>;
 const bookingStatusColors = <?= $bookingStatusColors ?>;
 const performanceLabels = <?= $performanceLabels ?>;
 const performanceCounts = <?= $performanceCounts ?>;
 const performanceColors = <?= $performanceColors ?>;

 const bookingEl = document.getElementById('bookingChart');
 const perfEl = document.getElementById('performanceChart');
 if (!bookingEl || !perfEl) return;

 const bookingSum = (bookingStatusCounts || []).reduce(function (a, b) { return a + (Number(b) || 0); }, 0);
 const perfSum = (performanceCounts || []).reduce(function (a, b) { return a + (Number(b) || 0); }, 0);

 new Chart(bookingEl.getContext('2d'), {
 type: 'pie',
 data: {
 labels: bookingStatusLabels,
 datasets: [{
 data: bookingStatusCounts,
 backgroundColor: bookingStatusColors,
 borderColor: '#fff',
 borderWidth: 2
 }]
 },
 options: {
 responsive: true,
 maintainAspectRatio: false,
 plugins: {
 legend: { position: 'bottom', labels: { padding: 12, font: { size: 11, weight: '500' } } },
 tooltip: {
 callbacks: {
 label: function (ctx) {
 const v = ctx.parsed || 0;
 const sum = bookingSum || 1;
 const pct = Math.round((v / sum) * 100);
 return (ctx.label || '') + ': ' + v + ' (' + pct + '%)';
 }
 }
 }
 }
 }
 });

 new Chart(perfEl.getContext('2d'), {
 type: 'pie',
 data: {
 labels: performanceLabels,
 datasets: [{
 data: performanceCounts,
 backgroundColor: performanceColors,
 borderColor: '#fff',
 borderWidth: 2
 }]
 },
 options: {
 responsive: true,
 maintainAspectRatio: false,
 plugins: {
 legend: { position: 'bottom', labels: { padding: 12, font: { size: 11, weight: '500' } } },
 tooltip: {
 callbacks: {
 label: function (ctx) {
 const v = ctx.parsed || 0;
 const sum = perfSum || 1;
 const pct = Math.round((v / sum) * 100);
 return (ctx.label || '') + ': ' + v + ' (' + pct + '%)';
 }
 }
 }
 }
 }
 });
})();
</script>

<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
