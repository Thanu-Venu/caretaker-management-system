<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <!-- Legacy CSS (keep for now during migration) -->
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_dashboard.css">
  <!-- Design System Override (ensures consistency) -->
</head>

<body>
  <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
  <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

  <?php
  $review = $data['review'] ?? [];
  $rp = (int) ($review['pendingPayments'] ?? 0);
  $rr = (int) ($review['rejectedPayments'] ?? 0);
  $rl = (int) ($review['pendingLeaves'] ?? 0);
  $rpr = (int) ($review['pendingProfileRequests'] ?? 0);
  $roc = (int) ($review['openComplaints'] ?? 0);
  $rsc = (int) ($review['staffCount'] ?? 0);
  $rfc = (int) ($review['feedbackCount'] ?? 0);
  $totalCollected = (float) (($data['stats']['totalCollected'] ?? 0));
  ?>

  <!-- Main Content with new design system -->
  <div class="main-content admin-dashboard-page">

    <div class="dashboard-layout">

    <!-- Page header: title + breadcrumb (Velonic-style toolbar) -->
    <header class="page-header dashboard-page-header">
      <div class="dashboard-page-header__row">
        <div class="dashboard-page-header__text">
          <h1 class="page-title dashboard-page-header__title">
            <span class="dashboard-page-header__greeting">Welcome back,</span>
            <span class="dashboard-page-header__name"><?= htmlspecialchars($_SESSION['user']['username'] ?? 'Admin') ?></span>
          </h1>
        </div>
        <nav class="dashboard-breadcrumb" aria-label="Breadcrumb">
          <ol class="dashboard-breadcrumb__list">
            <li><a href="<?= URLROOT ?>/public?url=admin/ad_dashboard">SmartCare</a></li>
            <li><span class="dashboard-breadcrumb__sep" aria-hidden="true">/</span> Admin</li>
            <li><span class="dashboard-breadcrumb__sep" aria-hidden="true">/</span> <span class="dashboard-breadcrumb__current">Dashboard</span></li>
          </ol>
        </nav>
      </div>
      <div class="dashboard-page-header__toolbar">
        <time class="dashboard-page-header__date" datetime="<?= date('c') ?>"><?= htmlspecialchars(date('l, j F Y')) ?></time>
        <nav class="dashboard-quick-actions" aria-label="Quick links">
          <a href="<?= URLROOT ?>/public?url=admin/ad_bookings" class="dashboard-quick-actions__link dashboard-quick-actions__link--primary"><i class="bx bx-calendar" aria-hidden="true"></i><span>Bookings</span></a>
          <a href="<?= URLROOT ?>/public?url=admin/ad_clients" class="dashboard-quick-actions__link"><i class="bx bx-group" aria-hidden="true"></i><span>Clients</span></a>
          <a href="<?= URLROOT ?>/CaretakerCRUD/list" class="dashboard-quick-actions__link"><i class="bx bx-user-circle" aria-hidden="true"></i><span>Caregivers</span></a>
          <a href="<?= URLROOT ?>/admin/ad_payments" class="dashboard-quick-actions__link"><i class="bx bx-wallet" aria-hidden="true"></i><span>Payments</span></a>
          <a href="<?= URLROOT ?>/public?url=admin/ad_leave" class="dashboard-quick-actions__link"><i class="bx bx-calendar-x" aria-hidden="true"></i><span>Leave</span></a>
          <a href="<?= URLROOT ?>/public?url=admin/ad_reports" class="dashboard-quick-actions__link"><i class="bx bx-bar-chart" aria-hidden="true"></i><span>Reports</span></a>
        </nav>
      </div>
    </header>

    <!-- HR-style review badges: counts that need attention -->
    <section class="dashboard-review-badges" aria-label="Quick review — needs attention">
      <h2 class="dashboard-review-badges__heading">Quick review</h2>
      <div class="dashboard-review-badges__grid">
        <a href="<?= URLROOT ?>/admin/ad_payments?status=pending" class="dashboard-review-badge dashboard-review-badge--payments">
          <span class="dashboard-review-badge__label">Pending payments</span>
          <span class="dashboard-review-badge__value"><?= $rp ?></span>
          <span class="dashboard-review-badge__hint">Awaiting approval</span>
          <span class="dashboard-review-badge__cta">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=admin/ad_leave" class="dashboard-review-badge dashboard-review-badge--leave">
          <span class="dashboard-review-badge__label">Pending leave</span>
          <span class="dashboard-review-badge__value"><?= $rl ?></span>
          <span class="dashboard-review-badge__hint">Needs a decision</span>
          <span class="dashboard-review-badge__cta">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=admin/ad_profile_requests" class="dashboard-review-badge dashboard-review-badge--profiles">
          <span class="dashboard-review-badge__label">Profile requests</span>
          <span class="dashboard-review-badge__value"><?= $rpr ?></span>
          <span class="dashboard-review-badge__hint">Caretaker updates</span>
          <span class="dashboard-review-badge__cta">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=admin/ad_feedback" class="dashboard-review-badge dashboard-review-badge--complaints">
          <span class="dashboard-review-badge__label">Open complaints</span>
          <span class="dashboard-review-badge__value"><?= $roc ?></span>
          <span class="dashboard-review-badge__hint">Feedback &amp; complaints</span>
          <span class="dashboard-review-badge__cta">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/admin/ad_payments?status=rejected" class="dashboard-review-badge dashboard-review-badge--rejected">
          <span class="dashboard-review-badge__label">Rejected payments</span>
          <span class="dashboard-review-badge__value"><?= $rr ?></span>
          <span class="dashboard-review-badge__hint">Follow up if needed</span>
          <span class="dashboard-review-badge__cta">View <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=admin/ad_feedback" class="dashboard-review-badge dashboard-review-badge--feedback">
          <span class="dashboard-review-badge__label">Feedback entries</span>
          <span class="dashboard-review-badge__value"><?= $rfc ?></span>
          <span class="dashboard-review-badge__hint">Client ratings</span>
          <span class="dashboard-review-badge__cta">Browse <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
      </div>
    </section>

    <!-- Extra shortcut tiles -->
    <section class="dashboard-shortcut-tiles" aria-label="More admin shortcuts">
      <h2 class="dashboard-shortcut-tiles__heading">Shortcuts</h2>
      <div class="dashboard-shortcut-tiles__grid">
        <a href="<?= URLROOT ?>/userCRUD/list" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-shield-quarter" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Staff &amp; roles</span>
          <span class="dashboard-shortcut-tile__meta"><?= $rsc ?> accounts</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=admin/ad_announcement" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bxs-megaphone" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Announcements</span>
          <span class="dashboard-shortcut-tile__meta">Post updates</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=admin/ad_settings" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-cog" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Settings</span>
          <span class="dashboard-shortcut-tile__meta">Your profile</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=admin/ad_history" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-history" aria-hidden="true"></i></span>
          <span class="dashboard-shortcut-tile__title">Activity log</span>
          <span class="dashboard-shortcut-tile__meta">Audit trail</span>
        </a>
      </div>
    </section>

    <!-- KPI strip — SmartCare palette (Velonic-style coloured tiles) -->
    <div class="stats-grid dashboard-stats">
      <div class="stat-card card-hover dashboard-stat dashboard-stat--blue">
        <div class="stat-card-icon">
          <i class='bx bx-user-circle' aria-hidden="true"></i>
        </div>
        <div class="stat-card-label">Total Caregivers</div>
        <div class="stat-card-value"><?= (int)$data['stats']['totalCaregivers'] ?></div>
        <a href="<?= URLROOT ?>/CaretakerCRUD/list" class="stat-card-link">Manage caregivers</a>
      </div>

      <div class="stat-card card-hover dashboard-stat dashboard-stat--teal">
        <div class="stat-card-icon">
          <i class='bx bx-user' aria-hidden="true"></i>
        </div>
        <div class="stat-card-label">Total Clients</div>
        <div class="stat-card-value"><?= (int)$data['stats']['totalClients'] ?></div>
        <a href="<?= URLROOT ?>/public?url=admin/ad_clients" class="stat-card-link">View clients</a>
      </div>

      <div class="stat-card card-hover dashboard-stat dashboard-stat--indigo">
        <div class="stat-card-icon">
          <i class='bx bx-calendar' aria-hidden="true"></i>
        </div>
        <div class="stat-card-label">Upcoming Bookings</div>
        <div class="stat-card-value"><?= (int)$data['stats']['upcomingBookings'] ?></div>
        <a href="<?= URLROOT ?>/public?url=admin/ad_bookings" class="stat-card-link">Open bookings</a>
      </div>

      <div class="stat-card card-hover dashboard-stat dashboard-stat--violet">
        <div class="stat-card-icon">
          <i class='bx bx-time' aria-hidden="true"></i>
        </div>
        <div class="stat-card-label">Pending Leave Requests</div>
        <div class="stat-card-value"><?= (int)$data['stats']['pendingLeaves'] ?></div>
        <a href="<?= URLROOT ?>/public?url=admin/ad_leave" class="stat-card-link">Review leave</a>
      </div>

      <div class="stat-card card-hover dashboard-stat dashboard-stat--ocean">
        <div class="stat-card-icon">
          <i class='bx bx-dollar-circle' aria-hidden="true"></i>
        </div>
        <div class="stat-card-label">Payments collected</div>
        <div class="stat-card-value">LKR <?= number_format($totalCollected, 0) ?></div>
        <a href="<?= URLROOT ?>/admin/ad_payments" class="stat-card-link">Payment summary</a>
      </div>
    </div>


    <!-- Overview charts (replaces recent activity table) -->
    <section class="dashboard-overview-charts" aria-label="Overview charts">
      <h2 class="dashboard-overview-charts__heading">Overview</h2>
      <div class="dashboard-overview-charts__grid">
        <div class="card dashboard-chart-card">
          <div class="card-header">
            <h3 class="card-title">Payments by status</h3>
            <p class="dashboard-chart-card__sub">All payment records</p>
          </div>
          <div class="card-body dashboard-chart-card__body">
            <p class="dashboard-chart-empty" id="paymentStatusChartEmpty" hidden>No payment records yet.</p>
            <canvas id="paymentStatusChart" aria-label="Payments by status chart"></canvas>
          </div>
        </div>
        <div class="card dashboard-chart-card">
          <div class="card-header">
            <h3 class="card-title">Bookings by status</h3>
            <p class="dashboard-chart-card__sub">Current distribution</p>
          </div>
          <div class="card-body dashboard-chart-card__body">
            <p class="dashboard-chart-empty" id="bookingStatusPieEmpty" hidden>No bookings yet.</p>
            <canvas id="bookingStatusPieChart" aria-label="Bookings by status chart"></canvas>
          </div>
        </div>
        <div class="card dashboard-chart-card">
          <div class="card-header">
            <h3 class="card-title">Caregivers by status</h3>
            <p class="dashboard-chart-card__sub">Active vs inactive</p>
          </div>
          <div class="card-body dashboard-chart-card__body">
            <p class="dashboard-chart-empty" id="caretakerStatusChartEmpty" hidden>No caregivers yet.</p>
            <canvas id="caretakerStatusChart" aria-label="Caregivers by status chart"></canvas>
          </div>
        </div>
      </div>
    </section>

    <!-- Analytics Section -->
    <div class="dashboard-analytics-grid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Booking Statistics</h3>
        </div>
        <div class="card-body">
          <canvas id="bookingChart"></canvas>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Client Engagement</h3>
        </div>
        <div class="card-body">
          <canvas id="engagementChart"></canvas>
        </div>
      </div>
    </div>

    </div><!-- .dashboard-layout -->

  </div>
  <script>
    window.DASHBOARD_DATA = {
      bookingStats: <?= json_encode($data['bookingStats'] ?? ['labels' => [], 'values' => []]) ?>,
      engagement: <?= json_encode($data['engagement'] ?? ['labels' => [], 'values' => []]) ?>,
      paymentStatus: <?= json_encode($data['chartPaymentStatus'] ?? ['labels' => [], 'values' => []]) ?>,
      bookingStatusPie: <?= json_encode($data['chartBookingStatus'] ?? ['labels' => [], 'values' => []]) ?>,
      caretakerStatus: <?= json_encode($data['chartCaretakerStatus'] ?? ['labels' => [], 'values' => []]) ?>
    };
  </script>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Your custom JS file -->
  <script src="<?php echo URLROOT; ?>/public/js/admin/ad_dashboard.js"></script>

</body>

</html>
