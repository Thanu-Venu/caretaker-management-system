<?php
$hrPageTitle = 'HR Dashboard — SmartCare';
$hrExtraCss  = ['admin/ad_dashboard.css', 'hr/hr_dashboard.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

$totalCaretakers = (int) ($data['totalCaretakers'] ?? 0);
$activeServices  = (int) ($data['activeServices'] ?? 0);
$pendingLeave    = (int) ($data['pendingLeave'] ?? 0);
$pendingRequests = (int) ($data['pendingRequests'] ?? 0);

$recentLeaves     = $data['recentLeaves'] ?? [];
$recentComplaints = $data['recentComplaints'] ?? [];
$recentBookings   = $data['recentBookings'] ?? [];

$bc = $data['badgeCounts'] ?? [];
$bcBookings   = (int) ($bc['bookings'] ?? 0);
$bcPayments   = (int) ($bc['payments'] ?? 0);
$bcLeave      = (int) ($bc['leave_requests'] ?? 0);
$bcChange     = (int) ($bc['change_requests'] ?? 0);
$bcReschedule = (int) ($bc['reschedule_requests'] ?? 0);
$bcComplaints = (int) ($bc['complaints'] ?? 0);
$bcCaretaker = (int) ($bc['caretaker_requests'] ?? 0);

$bookingStatusLabels = $data['bookingStatusLabels'] ?? json_encode([]);
$bookingStatusCounts = $data['bookingStatusCounts'] ?? json_encode([]);
$bookingStatusColors = $data['bookingStatusColors'] ?? json_encode([]);
$performanceLabels = $data['performanceLabels'] ?? json_encode([]);
$performanceCounts = $data['performanceCounts'] ?? json_encode([]);
$performanceColors = $data['performanceColors'] ?? json_encode([]);
?>

<main class="main-content admin-dashboard-page">
  <div class="dashboard-layout">

    <header class="page-header dashboard-page-header">
      <div class="dashboard-page-header__row">
        <div class="dashboard-page-header__text">
          <h1 class="page-title dashboard-page-header__title">
            <span class="dashboard-page-header__greeting">Welcome back,</span>
            <span class="dashboard-page-header__name"><?= htmlspecialchars($_SESSION['user']['username'] ?? 'Manager', ENT_QUOTES, 'UTF-8') ?></span>
          </h1>
        </div>
        <nav class="dashboard-breadcrumb" aria-label="Breadcrumb">
          <ol class="dashboard-breadcrumb__list">
            <li><a href="<?= URLROOT ?>/public?url=hr/hr_dashboard">SmartCare</a></li>
            <li><span class="dashboard-breadcrumb__sep" aria-hidden="true">/</span> HR</li>
            <li><span class="dashboard-breadcrumb__sep" aria-hidden="true">/</span> <span class="dashboard-breadcrumb__current">Dashboard</span></li>
          </ol>
        </nav>
      </div>
      <div class="dashboard-page-header__toolbar">
        <time class="dashboard-page-header__date" datetime="<?= date('c') ?>"><?= htmlspecialchars(date('l, j F Y'), ENT_QUOTES, 'UTF-8') ?></time>
        <nav class="dashboard-quick-actions" aria-label="Quick links">
          <a href="<?= URLROOT ?>/public?url=hr/hr_pending_request" class="dashboard-quick-actions__link dashboard-quick-actions__link--primary"><i class="bx bx-inbox" aria-hidden="true"></i><span>Pending requests</span></a>
          <a href="<?= URLROOT ?>/HRCaretakerCRUD/list" class="dashboard-quick-actions__link"><i class="bx bx-group" aria-hidden="true"></i><span>Caregivers</span></a>
          <a href="<?= URLROOT ?>/public?url=hr/pendingPayments" class="dashboard-quick-actions__link"><i class="bx bx-wallet" aria-hidden="true"></i><span>Payments</span></a>
          <a href="<?= URLROOT ?>/HrLeave/index" class="dashboard-quick-actions__link"><i class="bx bx-calendar-x" aria-hidden="true"></i><span>Leave</span></a>
          <a href="<?= URLROOT ?>/public?url=hr/hr_reports" class="dashboard-quick-actions__link"><i class="bx bx-bar-chart" aria-hidden="true"></i><span>Reports</span></a>
          <a href="<?= URLROOT ?>/public?url=hr/hr_schedule" class="dashboard-quick-actions__link"><i class="bx bx-calendar" aria-hidden="true"></i><span>Schedule</span></a>
        </nav>
      </div>
    </header>

    <section class="dashboard-review-badges" aria-label="Quick review — needs attention">
      <h2 class="dashboard-review-badges__heading">Quick review</h2>
      <div class="dashboard-review-badges__grid">
        <a href="<?= URLROOT ?>/public?url=hr/hr_pending_request" class="dashboard-review-badge dashboard-review-badge--payments">
          <span class="dashboard-review-badge__label">Pending requests</span>
          <span class="dashboard-review-badge__value"><?= $bcBookings ?></span>
          <span class="dashboard-review-badge__hint">Bookings awaiting action</span>
          <span class="dashboard-review-badge__cta">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=hr/pendingPayments" class="dashboard-review-badge dashboard-review-badge--leave">
          <span class="dashboard-review-badge__label">Pending payments</span>
          <span class="dashboard-review-badge__value"><?= $bcPayments ?></span>
          <span class="dashboard-review-badge__hint">Advance &amp; balances</span>
          <span class="dashboard-review-badge__cta">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/HrLeave/index" class="dashboard-review-badge dashboard-review-badge--profiles">
          <span class="dashboard-review-badge__label">Leave queue</span>
          <span class="dashboard-review-badge__value"><?= $bcLeave ?></span>
          <span class="dashboard-review-badge__hint">Awaiting approval</span>
          <span class="dashboard-review-badge__cta">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=hr/changeRequests" class="dashboard-review-badge dashboard-review-badge--complaints">
          <span class="dashboard-review-badge__label">Change requests</span>
          <span class="dashboard-review-badge__value"><?= $bcChange ?></span>
          <span class="dashboard-review-badge__hint">Caregiver swaps</span>
          <span class="dashboard-review-badge__cta">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=hr/rescheduleRequests" class="dashboard-review-badge dashboard-review-badge--rejected">
          <span class="dashboard-review-badge__label">Reschedule requests</span>
          <span class="dashboard-review-badge__value"><?= $bcReschedule ?></span>
          <span class="dashboard-review-badge__hint">Date change queue</span>
          <span class="dashboard-review-badge__cta">View <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public/index.php?url=Complaint/index" class="dashboard-review-badge dashboard-review-badge--feedback">
          <span class="dashboard-review-badge__label">Open complaints</span>
          <span class="dashboard-review-badge__value"><?= $bcComplaints ?></span>
          <span class="dashboard-review-badge__hint">Needs resolution</span>
          <span class="dashboard-review-badge__cta">Browse <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
      </div>
    </section>

    <?php if ($bcCaretaker > 0): ?>
    <section class="dashboard-review-badges dashboard-review-badges--single" aria-label="Caretaker approvals">
      <h2 class="dashboard-review-badges__heading">Caregiver onboarding</h2>
      <div class="dashboard-review-badges__grid">
        <a href="<?= URLROOT ?>/HRCaretakerCRUD/list" class="dashboard-review-badge dashboard-review-badge--profiles">
          <span class="dashboard-review-badge__label">Caretaker approvals</span>
          <span class="dashboard-review-badge__value"><?= $bcCaretaker ?></span>
          <span class="dashboard-review-badge__hint">Profiles pending verification</span>
          <span class="dashboard-review-badge__cta">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
      </div>
    </section>
    <?php endif; ?>

    <section class="dashboard-shortcut-tiles" aria-label="HR shortcuts">
      <h2 class="dashboard-shortcut-tiles__heading">Shortcuts</h2>
      <div class="dashboard-shortcut-tiles__grid">
        <a href="<?= URLROOT ?>/public?url=hr/paymentMonitor" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-line-chart" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Payment monitor</span>
          <span class="dashboard-shortcut-tile__meta">Track inflows</span>
        </a>
        <a href="<?= URLROOT ?>/hr/refunds" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-receipt" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Refunds</span>
          <span class="dashboard-shortcut-tile__meta">Process returns</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=hr/hr_feedback" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-message-detail" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Feedback</span>
          <span class="dashboard-shortcut-tile__meta">Client ratings</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=hr/hr_announcement" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bxs-megaphone" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Announcements</span>
          <span class="dashboard-shortcut-tile__meta">Post updates</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=hr/hr_logs" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-history" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Activity log</span>
          <span class="dashboard-shortcut-tile__meta">HR actions</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=hr/hr_settings" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-cog" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Settings</span>
          <span class="dashboard-shortcut-tile__meta">Your profile</span>
        </a>
      </div>
    </section>

    <div class="stats-grid dashboard-stats">
      <div class="stat-card card-hover dashboard-stat dashboard-stat--blue">
        <div class="stat-card-icon"><i class="bx bx-user-circle" aria-hidden="true"></i></div>
        <div class="stat-card-label">Total caregivers</div>
        <div class="stat-card-value"><?= $totalCaretakers ?></div>
        <a href="<?= URLROOT ?>/HRCaretakerCRUD/list" class="stat-card-link">Manage caregivers</a>
      </div>
      <div class="stat-card card-hover dashboard-stat dashboard-stat--teal">
        <div class="stat-card-icon"><i class="bx bx-bolt" aria-hidden="true"></i></div>
        <div class="stat-card-label">Active services today</div>
        <div class="stat-card-value"><?= $activeServices ?></div>
        <a href="<?= URLROOT ?>/public?url=hr/hr_schedule" class="stat-card-link">View schedule</a>
      </div>
      <div class="stat-card card-hover dashboard-stat dashboard-stat--violet">
        <div class="stat-card-icon"><i class="bx bx-time" aria-hidden="true"></i></div>
        <div class="stat-card-label">Pending leave</div>
        <div class="stat-card-value"><?= $pendingLeave ?></div>
        <a href="<?= URLROOT ?>/HrLeave/index" class="stat-card-link">Review leave</a>
      </div>
      <div class="stat-card card-hover dashboard-stat dashboard-stat--indigo">
        <div class="stat-card-icon"><i class="bx bx-inbox" aria-hidden="true"></i></div>
        <div class="stat-card-label">Requested bookings</div>
        <div class="stat-card-value"><?= $pendingRequests ?></div>
        <a href="<?= URLROOT ?>/public?url=hr/hr_pending_request" class="stat-card-link">Open queue</a>
      </div>
      <div class="stat-card card-hover dashboard-stat dashboard-stat--ocean">
        <div class="stat-card-icon"><i class="bx bx-error-circle" aria-hidden="true"></i></div>
        <div class="stat-card-label">Open complaints</div>
        <div class="stat-card-value"><?= $bcComplaints ?></div>
        <a href="<?= URLROOT ?>/public/index.php?url=Complaint/index" class="stat-card-link">Resolve issues</a>
      </div>
    </div>

    <section class="dashboard-overview-charts" aria-label="Overview charts">
      <h2 class="dashboard-overview-charts__heading">Overview</h2>
      <div class="dashboard-overview-charts__grid dashboard-hr-overview-charts__grid">
        <div class="card dashboard-chart-card">
          <div class="card-header">
            <h3 class="card-title">Bookings by status</h3>
            <p class="dashboard-chart-card__sub">Distribution in your pipeline</p>
          </div>
          <div class="card-body dashboard-chart-card__body">
            <canvas id="bookingChart" aria-label="Bookings by status chart"></canvas>
          </div>
        </div>
        <div class="card dashboard-chart-card">
          <div class="card-header">
            <h3 class="card-title">Caregiver performance</h3>
            <p class="dashboard-chart-card__sub">Ratings overview</p>
          </div>
          <div class="card-body dashboard-chart-card__body">
            <canvas id="performanceChart" aria-label="Performance ratings chart"></canvas>
          </div>
        </div>
      </div>
    </section>

    <section class="dashboard-hr-recents" aria-label="Recent activity">
      <h2 class="dashboard-overview-charts__heading">Recent activity</h2>
      <div class="dashboard-analytics-grid dashboard-hr-recents__grid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Recent leave</h3>
          </div>
          <div class="card-body">
            <ul class="hr-recent-list">
              <?php if (empty($recentLeaves)): ?>
                <li class="hr-recent-list__empty">No pending leave rows.</li>
              <?php else: ?>
                <?php foreach ($recentLeaves as $l): ?>
                  <?php
                  $caretakerId = $l['user_id'] ?? '';
                  $startDate   = $l['start_date'] ?? '';
                  $endDate     = $l['end_date'] ?? '';
                  ?>
                  <li class="hr-recent-list__item">
                    <div>
                      <div class="hr-recent-list__title">Caregiver #<?= htmlspecialchars((string) $caretakerId, ENT_QUOTES, 'UTF-8') ?></div>
                      <div class="hr-recent-list__meta"><?= htmlspecialchars((string) $startDate, ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars((string) $endDate, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <a class="hr-recent-list__link" href="<?= URLROOT ?>/HrLeave/index">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></a>
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
            <ul class="hr-recent-list">
              <?php if (empty($recentComplaints)): ?>
                <li class="hr-recent-list__empty">No open complaints in the feed.</li>
              <?php else: ?>
                <?php foreach ($recentComplaints as $c): ?>
                  <?php
                  $clientName    = $c['client_name'] ?? '';
                  $caretakerName = $c['caretaker_name'] ?? '';
                  $category      = $c['category'] ?? '';
                  ?>
                  <li class="hr-recent-list__item">
                    <div>
                      <div class="hr-recent-list__title"><?= htmlspecialchars((string) $clientName, ENT_QUOTES, 'UTF-8') ?></div>
                      <div class="hr-recent-list__meta"><?= htmlspecialchars((string) $caretakerName, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars((string) $category, ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <a class="hr-recent-list__link" href="<?= URLROOT ?>/public/index.php?url=Complaint/index">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></a>
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
            <ul class="hr-recent-list">
              <?php if (empty($recentBookings)): ?>
                <li class="hr-recent-list__empty">No recent requested bookings.</li>
              <?php else: ?>
                <?php foreach ($recentBookings as $b): ?>
                  <?php
                  $bookingId   = is_array($b) ? ($b['booking_id'] ?? ($b['id'] ?? '')) : ($b->booking_id ?? ($b->id ?? ''));
                  $clientName  = is_array($b) ? ($b['client_name'] ?? '') : ($b->client_name ?? '');
                  $serviceType = is_array($b) ? ($b['service_type'] ?? '') : ($b->service_type ?? '');
                  $date        = is_array($b) ? ($b['booking_date'] ?? ($b['date'] ?? '')) : ($b->booking_date ?? ($b->date ?? ''));
                  $time        = is_array($b) ? ($b['preferred_time'] ?? ($b['time'] ?? '')) : ($b->preferred_time ?? ($b->time ?? ''));
                  ?>
                  <li class="hr-recent-list__item">
                    <div>
                      <div class="hr-recent-list__title">#<?= htmlspecialchars((string) $bookingId, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string) $clientName, ENT_QUOTES, 'UTF-8') ?></div>
                      <div class="hr-recent-list__meta"><?= htmlspecialchars((string) $serviceType, ENT_QUOTES, 'UTF-8') ?><?= $date !== '' ? ' · ' . htmlspecialchars((string) $date, ENT_QUOTES, 'UTF-8') : '' ?><?= $time !== '' ? ' · ' . htmlspecialchars((string) $time, ENT_QUOTES, 'UTF-8') : '' ?></div>
                    </div>
                    <a class="hr-recent-list__link" href="<?= URLROOT ?>/public?url=hr/hr_pending_request">View <i class="bx bx-chevron-right" aria-hidden="true"></i></a>
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
