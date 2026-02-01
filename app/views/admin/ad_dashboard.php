<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_dashboard.css">
</head>
<body>

<div class="admin-dashboard">
<h1>Welcome back, <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Admin') ?>!</h1>

<div class="stats-cards">
  <div class="card">
    <h3>Total Caregivers</h3>
    <p class="value"><?= (int)$data['stats']['totalCaregivers'] ?></p>
  </div>

  <div class="card">
    <h3>Total Clients</h3>
    <p class="value"><?= (int)$data['stats']['totalClients'] ?></p>
  </div>

  <div class="card">
    <h3>Upcoming Bookings</h3>
    <p class="value"><?= (int)$data['stats']['upcomingBookings'] ?></p>
  </div>

  <div class="card">
    <h3>Pending Leave Requests</h3>
    <p class="value"><?= (int)$data['stats']['pendingLeaves'] ?></p>
  </div>

  <div class="card">
    <h3>Monthly Payments</h3>
    <!-- <p class="value">Rs. <?= number_format((float)$data['stats']['monthlyPayments'], 2) ?></p> -->
     <p class="value">Rs. --</p>
  </div>
</div>


  <!-- Recent Activity -->
 <div class="recent-activity">
  <div class="activity-top">
    <h2>Recent Activity</h2>

    <div class="search-bar">
      <i class='bx bx-search'></i>
      <input type="text" placeholder="Search activity...">
    </div>
  </div>

  <table>
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
          <td colspan="3">No recent activity found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>


  <!-- Analytics Section -->
  <div class="analytics">
    <div class="chart-box">
      <h3>Booking Statistics</h3>
      <canvas id="bookingChart"></canvas>
    </div>
    <div class="chart-box">
      <h3>Client Engagement</h3>
      <canvas id="engagementChart"></canvas>
    </div>
  </div>

</div>
<script>
  window.DASHBOARD_DATA = {
    bookingStats: <?= json_encode($data['bookingStats'] ?? ['labels'=>[], 'values'=>[]]) ?>,
    engagement: <?= json_encode($data['engagement'] ?? ['labels'=>[], 'values'=>[]]) ?>
  };
</script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Your custom JS file -->
<script src="<?php echo URLROOT; ?>/public/js/admin/ad_dashboard.js"></script>

</body>
</html>
