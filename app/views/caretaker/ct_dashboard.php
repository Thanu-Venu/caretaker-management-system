<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare Dashboard</title>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/ad_dashboard.css">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_dashboard.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_sidebar.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>

<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<?php
  $upcomingCount = count($data['upcoming'] ?? []);
  $pendingLeaveCount = 0;
  foreach ($data['leaves'] ?? [] as $leave) {
      if (strtolower($leave['status'] ?? '') === 'pending') {
          $pendingLeaveCount++;
      }
  }
  $profileRequestPending = !empty($data['latestProfileChangeRequest']) && (($data['latestProfileChangeRequest']['status'] ?? '') === 'Pending');
  $activeBookings = (int)($data['monthlyStats']['active_bookings'] ?? 0);
  $completedBookings = (int)($data['monthlyStats']['completed_bookings'] ?? 0);
  $workingDays = (int)($data['monthlyStats']['working_days'] ?? 0);
  $availability = !empty($data['monthlyStats']['is_available']);
  $rating = number_format((float)($data['monthlyStats']['rating'] ?? 0), 1);
  $availabilityLabel = $availability ? "Visible to clients" : "Hidden from clients";
?>

<div class="main-content admin-dashboard-page">
  <div class="dashboard-layout">

    <header class="page-header dashboard-page-header">
      <div class="dashboard-page-header__row">
        <div class="dashboard-page-header__text">
          <h1 class="page-title dashboard-page-header__title">
            <span class="dashboard-page-header__greeting">Welcome back,</span>
            <span class="dashboard-page-header__name"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Caregiver') ?></span>
          </h1>
        </div>
        <nav class="dashboard-breadcrumb" aria-label="Breadcrumb">
          <ol class="dashboard-breadcrumb__list">
            <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_dashboard">SmartCare</a></li>
            <li><span class="dashboard-breadcrumb__sep" aria-hidden="true">/</span> Caretaker</li>
            <li><span class="dashboard-breadcrumb__sep" aria-hidden="true">/</span> <span class="dashboard-breadcrumb__current">Dashboard</span></li>
          </ol>
        </nav>
      </div>
      <div class="dashboard-page-header__toolbar">
        <time class="dashboard-page-header__date" datetime="<?= date('c') ?>"><?= htmlspecialchars(date('l, j F Y')) ?></time>
        <nav class="dashboard-quick-actions" aria-label="Quick links">
          <a href="<?= URLROOT ?>/public?url=caretaker/ct_schedule" class="dashboard-quick-actions__link dashboard-quick-actions__link--primary"><i class="bx bx-calendar" aria-hidden="true"></i><span>Schedule</span></a>
          <a href="<?= URLROOT ?>/public?url=caretaker/ct_booking" class="dashboard-quick-actions__link"><i class="bx bx-book-alt" aria-hidden="true"></i><span>Bookings</span></a>
          <a href="<?= URLROOT ?>/public?url=caretaker/ct_leave" class="dashboard-quick-actions__link"><i class="bx bx-calendar-x" aria-hidden="true"></i><span>Leave</span></a>
          <a href="<?= URLROOT ?>/public?url=caretaker/ct_reports" class="dashboard-quick-actions__link"><i class="bx bx-bar-chart" aria-hidden="true"></i><span>Reports</span></a>
          <a href="<?= URLROOT ?>/public?url=caretaker/ct_announcement" class="dashboard-quick-actions__link"><i class="bx bxs-megaphone" aria-hidden="true"></i><span>Announcements</span></a>
        </nav>
      </div>
    </header>

    <section class="dashboard-review-badges" aria-label="Quick review — needs attention">
      <h2 class="dashboard-review-badges__heading">Quick review</h2>
      <div class="dashboard-review-badges__grid">
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_booking&tab=upcoming" class="dashboard-review-badge dashboard-review-badge--payments">
          <span class="dashboard-review-badge__label">Upcoming bookings</span>
          <span class="dashboard-review-badge__value"><?= $upcomingCount ?></span>
          <span class="dashboard-review-badge__hint">Bookings scheduled soon</span>
          <span class="dashboard-review-badge__cta">View <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_leave" class="dashboard-review-badge dashboard-review-badge--leave">
          <span class="dashboard-review-badge__label">Pending leave</span>
          <span class="dashboard-review-badge__value"><?= $pendingLeaveCount ?></span>
          <span class="dashboard-review-badge__hint">Awaiting decision</span>
          <span class="dashboard-review-badge__cta">Review <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_settings" class="dashboard-review-badge dashboard-review-badge--profiles">
          <span class="dashboard-review-badge__label">Profile updates</span>
          <span class="dashboard-review-badge__value"><?= $profileRequestPending ? 1 : 0 ?></span>
          <span class="dashboard-review-badge__hint">Admin review status</span>
          <span class="dashboard-review-badge__cta">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_schedule" class="dashboard-review-badge dashboard-review-badge--complaints">
          <span class="dashboard-review-badge__label">Active schedule</span>
          <span class="dashboard-review-badge__value"><?= $activeBookings ?></span>
          <span class="dashboard-review-badge__hint">Current appointments</span>
          <span class="dashboard-review-badge__cta">Open <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
        </a>
      </div>
    </section>

    <section class="dashboard-shortcut-tiles" aria-label="More caretaker shortcuts">
      <h2 class="dashboard-shortcut-tiles__heading">Shortcuts</h2>
      <div class="dashboard-shortcut-tiles__grid">
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_schedule" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-calendar"></i></span>
          <span class="dashboard-shortcut-tile__title">My schedule</span>
          <span class="dashboard-shortcut-tile__meta">View shift details</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_booking" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-book-alt"></i></span>
          <span class="dashboard-shortcut-tile__title">Bookings</span>
          <span class="dashboard-shortcut-tile__meta">Client appointments</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_leave" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-calendar-x"></i></span>
          <span class="dashboard-shortcut-tile__title">Leave request</span>
          <span class="dashboard-shortcut-tile__meta">Submit a request</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_announcement" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bxs-megaphone"></i></span>
          <span class="dashboard-shortcut-tile__title">Announcements</span>
          <span class="dashboard-shortcut-tile__meta">Latest updates</span>
        </a>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_settings" class="dashboard-shortcut-tile">
          <span class="dashboard-shortcut-tile__icon"><i class="bx bx-cog"></i></span>
          <span class="dashboard-shortcut-tile__title">Settings</span>
          <span class="dashboard-shortcut-tile__meta">Update profile</span>
        </a>
      </div>
    </section>

    <div class="stats-grid dashboard-stats">
      <div class="stat-card card-hover dashboard-stat dashboard-stat--blue">
        <div class="stat-card-icon"><i class='bx bx-check-circle'></i></div>
        <div class="stat-card-label">Upcoming bookings</div>
        <div class="stat-card-value"><?= $upcomingCount ?></div>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_booking" class="stat-card-link">View bookings</a>
      </div>
      <div class="stat-card card-hover dashboard-stat dashboard-stat--teal">
        <div class="stat-card-icon"><i class='bx bx-calendar-x'></i></div>
        <div class="stat-card-label">Pending leave</div>
        <div class="stat-card-value"><?= $pendingLeaveCount ?></div>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_leave" class="stat-card-link">Manage leave</a>
      </div>
      <div class="stat-card card-hover dashboard-stat dashboard-stat--indigo">
        <div class="stat-card-icon"><i class='bx bx-time-five'></i></div>
        <div class="stat-card-label">Working days</div>
        <div class="stat-card-value"><?= $workingDays ?></div>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_schedule" class="stat-card-link">View schedule</a>
      </div>
      <div class="stat-card card-hover dashboard-stat dashboard-stat--violet">
        <div class="stat-card-icon"><i class='bx bx-star'></i></div>
        <div class="stat-card-label">Average rating</div>
        <div class="stat-card-value"><?= $rating ?> ★</div>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_reviews" class="stat-card-link">See reviews</a>
      </div>
      <div class="stat-card card-hover dashboard-stat dashboard-stat--ocean">
        <div class="stat-card-icon"><i class='bx bx-show'></i></div>
        <div class="stat-card-label">Availability</div>
        <div class="stat-card-value"><?= $availability ? 'Visible' : 'Hidden' ?></div>
        <a href="<?= URLROOT ?>/public?url=caretaker/ct_schedule" class="stat-card-link">Toggle status</a>
      </div>
    </div>

    <section class="dashboard-overview-charts" aria-label="Caretaker overview">
      <h2 class="dashboard-overview-charts__heading">Overview</h2>
      <div class="dashboard-overview-charts__grid">
        <div class="card dashboard-chart-card">
          <div class="card-header">
            <h3 class="card-title">Upcoming Bookings</h3>
            <p class="dashboard-chart-card__sub">Your next appointments</p>
          </div>
          <div class="card-body dashboard-chart-card__body">
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Service</th>
                    <th>Location</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($data['upcoming'])): ?>
                    <tr>
                      <td colspan="4" style="text-align:center;">No upcoming bookings</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($data['upcoming'] as $booking): ?>
                      <tr>
                        <td><?= htmlspecialchars($booking['client_name']) ?></td>
                        <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                        <td><?= htmlspecialchars($booking['service_type']) ?></td>
                        <td><?= htmlspecialchars($booking['service_location']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="card dashboard-chart-card">
          <div class="card-header">
            <h3 class="card-title">Leave & availability</h3>
            <p class="dashboard-chart-card__sub">Leave status and schedule</p>
          </div>
          <div class="card-body dashboard-chart-card__body">
            <p><strong><?= $availabilityLabel ?></strong></p>
            <p>Completed bookings: <?= $completedBookings ?></p>
            <p>Pending leave requests: <?= $pendingLeaveCount ?></p>
            <div class="button-cont" style="margin-top: 18px;">
              <a class="btn-small" href="<?= URLROOT ?>/public?url=caretaker/ct_leave">Request leave</a>
              <a class="btn-small" href="<?= URLROOT ?>/public?url=caretaker/ct_schedule" style="margin-left: 12px;">View schedule</a>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</div>

<script>
  window.dashboardData = {
    workingDates: <?= json_encode($data['workingDates'] ?? []) ?>,
    calendarMonth: <?= (int)($data['calendarMonth'] ?? date('n')) ?>,
    calendarYear: <?= (int)($data['calendarYear'] ?? date('Y')) ?>,
    updateAvailabilityUrl: "<?= URLROOT ?>/caretaker/updateAvailabilityStatus"
  };
</script>
<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_dashboard.js"></script>
</body>

</html>
