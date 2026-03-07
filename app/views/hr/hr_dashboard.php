<?php
include_once APPROOT . "/views/templates/hr/hr_header.php";
include_once APPROOT . "/views/templates/hr/hr_sidebar.php";

// Safe defaults
$totalCaretakers = $data['totalCaretakers'] ?? 0;
$activeServices  = $data['activeServices'] ?? 0;
$pendingLeave    = $data['pendingLeave'] ?? 0;
$pendingRequests = $data['pendingRequests'] ?? 0;

$recentLeaves     = $data['recentLeaves'] ?? [];
$recentComplaints = $data['recentComplaints'] ?? [];
$recentBookings   = $data['recentBookings'] ?? []; // prevent undefined variable
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HR Dashboard - SmartCare</title>

  <link rel="stylesheet" href="<?= URLROOT; ?>/public/css/hr/hr_dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
<div class="main-content">

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-title">
      <h1>HR Dashboard</h1>
      <p>Overview of caregivers, services, leaves, requests, and complaints</p>
    </div>
    <div class="header-chip">
      <i class="fa-regular fa-calendar"></i>
      <?= date('d M Y') ?>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="kpi-cards">

    <!-- Total Caregivers -->
    <div class="kpi-card">
      <div class="kpi-top">
        <div class="kpi-title">Total Caregivers</div>
        <div class="kpi-icon"><i class="fa-solid fa-user-nurse"></i></div>
      </div>
      <div class="kpi-value"><?= (int)$totalCaretakers ?></div>
      <div class="kpi-meta">
        <span class="kpi-note">Registered in system</span>
      </div>
    </div>

    <!-- Active Services -->
    <div class="kpi-card">
      <div class="kpi-top">
        <div class="kpi-title">Active Services Today</div>
        <div class="kpi-icon"><i class="fa-solid fa-bolt"></i></div>
      </div>
      <div class="kpi-value"><?= (int)$activeServices ?></div>
      <div class="kpi-meta">
        <span class="kpi-note">Ongoing / scheduled today</span>
      </div>
    </div>

    <!-- Pending Leaves -->
    <div class="kpi-card">
      <div class="kpi-top">
        <div class="kpi-title">Pending Leave Requests</div>
        <div class="kpi-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
      </div>
      <div class="kpi-value"><?= (int)$pendingLeave ?></div>
      <div class="kpi-meta">
        <span class="kpi-note">Needs approval</span>
        <a href="<?= URLROOT ?>/hr/hr_leave" class="view-link">View All</a>
      </div>
    </div>

    <!-- Pending Client Requests -->
    <div class="kpi-card">
      <div class="kpi-top">
        <div class="kpi-title">Pending Client Requests</div>
        <div class="kpi-icon"><i class="fa-solid fa-inbox"></i></div>
      </div>
      <div class="kpi-value"><?= (int)$pendingRequests ?></div>
      <div class="kpi-meta">
        <span class="kpi-note">Awaiting action</span>
        <a href="<?= URLROOT ?>/hr/hr_pending_request" class="view-link">View All</a>
      </div>
    </div>

  </div>

  <!-- Charts -->
  <div class="charts-section">

    <div class="chart-card">
      <div class="card-header">
        <div>
          <h3>Attendance Summary</h3>
          <div class="subtitle">Demo chart (connect DB later)</div>
        </div>
      </div>
      <div class="chart-wrap">
        <canvas id="attendanceChart"></canvas>
      </div>
    </div>

    <div class="chart-card">
      <div class="card-header">
        <div>
          <h3>Performance Ratings</h3>
          <div class="subtitle">Demo chart (connect DB later)</div>
        </div>
      </div>
      <div class="chart-wrap">
        <canvas id="performanceChart"></canvas>
      </div>
    </div>

  </div>

  <!-- Recent Widgets -->
  <div class="pending-section">

    <!-- Leaves -->
    <div class="pending-card">
      <h3>Recent Leave Requests</h3>

      <ul class="pending-list">
        <?php if (empty($recentLeaves)): ?>
          <li class="empty">No recent leave requests found.</li>
        <?php else: ?>
          <?php foreach ($recentLeaves as $l): ?>
            <?php
              $caretakerId = $l['user_id'] ?? '';
              $startDate   = $l['start_date'] ?? '';
              $endDate     = $l['end_date'] ?? '';
            ?>
            <li class="pending-item">
              <div class="info">
                <div class="title">Caretaker #<?= htmlspecialchars((string)$caretakerId) ?></div>
                <div class="desc"><?= htmlspecialchars((string)$startDate) ?> → <?= htmlspecialchars((string)$endDate) ?></div>
              </div>
              <a class="action" href="<?= URLROOT ?>/hr/hr_leave">
                Review <i class="fa-solid fa-arrow-right"></i>
              </a>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>

    <!-- Complaints -->
    <div class="pending-card">
      <h3>Recent Complaints</h3>

      <ul class="pending-list">
        <?php if (empty($recentComplaints)): ?>
          <li class="empty">No recent complaints found.</li>
        <?php else: ?>
          <?php foreach ($recentComplaints as $c): ?>
            <?php
              $clientName    = $c['client_name'] ?? '';
              $caretakerName = $c['caretaker_name'] ?? '';
              $category      = $c['category'] ?? '';
            ?>
            <li class="pending-item">
              <div class="info">
                <div class="title"><?= htmlspecialchars((string)$clientName) ?> (<?= htmlspecialchars((string)$caretakerName) ?>)</div>
                <div class="desc"><?= htmlspecialchars((string)$category) ?></div>
              </div>
              <a class="action" href="<?= URLROOT ?>/hr/hr_complaint">
                Resolve <i class="fa-solid fa-arrow-right"></i>
              </a>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>

    <div class="pending-card">
  <h3>Recent Bookings</h3>

  <ul class="pending-list">
    <?php if (empty($recentBookings)): ?>
      <li class="empty">No recent bookings found.</li>
    <?php else: ?>
      <?php foreach ($recentBookings as $b): ?>
        <?php
          // Support both array and object
          $bookingId   = is_array($b) ? ($b['booking_id'] ?? ($b['id'] ?? '')) : ($b->booking_id ?? ($b->id ?? ''));
          $clientName  = is_array($b) ? ($b['client_name'] ?? '') : ($b->client_name ?? '');
          $serviceType = is_array($b) ? ($b['service_type'] ?? '') : ($b->service_type ?? '');
          $date        = is_array($b) ? ($b['booking_date'] ?? ($b['date'] ?? '')) : ($b->booking_date ?? ($b->date ?? ''));
          $time        = is_array($b) ? ($b['preferred_time'] ?? ($b['time'] ?? '')) : ($b->preferred_time ?? ($b->time ?? ''));
          $status      = is_array($b) ? ($b['status'] ?? '') : ($b->status ?? '');
        ?>
        <li class="pending-item">
          <div class="info">
            <div class="title">
              Booking #<?= htmlspecialchars((string)$bookingId) ?> — <?= htmlspecialchars((string)$clientName) ?>
            </div>
            <div class="desc">
              <?= htmlspecialchars((string)$serviceType) ?>
              <?php if ($date !== '' || $time !== ''): ?>
                • <?= htmlspecialchars((string)$date) ?> <?= $time !== '' ? '• ' . htmlspecialchars((string)$time) : '' ?>
              <?php endif; ?>
              <?php if ($status !== ''): ?>
                • Status: <?= htmlspecialchars((string)$status) ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- Link to your existing page that shows bookings -->
          <a class="action" href="<?= URLROOT ?>/hr/hr_pending_request">
            View <i class="fa-solid fa-arrow-right"></i>
          </a>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
</div>

  </div>

</div>

<script src="<?= URLROOT; ?>/public/js/hr/hr_dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Demo charts (replace with DB data later)
  const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
  const performanceCtx = document.getElementById('performanceChart').getContext('2d');

  new Chart(attendanceCtx, {
    type: 'bar',
    data: {
      labels: ['Jane', 'John', 'Michael', 'Emily', 'David'],
      datasets: [{
        label: 'Days Present',
        data: [20, 18, 22, 19, 21],
        backgroundColor: '#1E88E5'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  });

  new Chart(performanceCtx, {
    type: 'pie',
    data: {
      labels: ['Excellent', 'Good', 'Average', 'Poor'],
      datasets: [{
        data: [5, 10, 7, 3],
        backgroundColor: ['#1E88E5', '#00BFA5', '#FFC107', '#F44336']
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  });
</script>

</body>
</html>