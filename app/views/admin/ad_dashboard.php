<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <!-- Legacy CSS (keep for now during migration) -->
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_dashboard.css">
  <!-- Design System Override (ensures consistency) -->
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/system/legacy-overrides.css">
</head>

<body>

  <!-- Main Content with new design system -->
  <div class="main-content">

    <!-- Page Header -->
    <div class="page-header">
      <h1 class="page-title">Welcome back, <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Admin') ?>!</h1>
    </div>

    <!-- Stats Grid with new design -->
    <div class="stats-grid">
      <div class="stat-card card-hover">
        <div class="stat-card-icon">
          <i class='bx bx-user-circle'></i>
        </div>
        <div class="stat-card-label">Total Caregivers</div>
        <div class="stat-card-value"><?= (int)$data['stats']['totalCaregivers'] ?></div>
      </div>

      <div class="stat-card card-hover">
        <div class="stat-card-icon">
          <i class='bx bx-user'></i>
        </div>
        <div class="stat-card-label">Total Clients</div>
        <div class="stat-card-value"><?= (int)$data['stats']['totalClients'] ?></div>
      </div>

      <div class="stat-card card-hover">
        <div class="stat-card-icon">
          <i class='bx bx-calendar'></i>
        </div>
        <div class="stat-card-label">Upcoming Bookings</div>
        <div class="stat-card-value"><?= (int)$data['stats']['upcomingBookings'] ?></div>
      </div>

      <div class="stat-card card-hover">
        <div class="stat-card-icon">
          <i class='bx bx-time'></i>
        </div>
        <div class="stat-card-label">Pending Leave Requests</div>
        <div class="stat-card-value"><?= (int)$data['stats']['pendingLeaves'] ?></div>
      </div>

      <div class="stat-card card-hover">
        <div class="stat-card-icon">
          <i class='bx bx-dollar-circle'></i>
        </div>
        <div class="stat-card-label">Monthly Payments</div>
        <!-- <div class="stat-card-value">Rs. <?= number_format((float)$data['stats']['monthlyPayments'], 2) ?></div> -->
        <div class="stat-card-value">Rs. --</div>
      </div>
    </div>


    <!-- Recent Activity -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Recent Activity</h3>
        <div class="card-actions">
          <div class="search-box">
            <i class='bx bx-search'></i>
            <input type="text" class="form-input" placeholder="Search activity..." style="height: 36px;">
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="table-container">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Activity</th>
                <th>Date</th>
                <th>User</th>
              </tr>
            </thead>
            <tbody id="activityTable">
              <?php if (!empty($data['recentLogs'])): ?>
                <?php foreach ($data['recentLogs'] as $log): ?>
                  <tr>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= htmlspecialchars($log['created_at']) ?></td>
                    <td><?= htmlspecialchars($log['username']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3" class="text-center text-muted">No recent activity found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>


    <!-- Analytics Section -->
    <div class="dashboard-grid">
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

  </div>
  <script>
    window.DASHBOARD_DATA = {
      bookingStats: <?= json_encode($data['bookingStats'] ?? ['labels' => [], 'values' => []]) ?>,
      engagement: <?= json_encode($data['engagement'] ?? ['labels' => [], 'values' => []]) ?>
    };
  </script>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Your custom JS file -->
  <script src="<?php echo URLROOT; ?>/public/js/admin/ad_dashboard.js"></script>

</body>

</html>