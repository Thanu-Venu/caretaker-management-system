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
<h1>Welcomeback Admin!</h1>
  <!-- Top Stats Cards -->
  <div class="stats-cards">
    <div class="card">
      <h3>Total Caregivers</h3>
      <p class="value">120</p>
      <span class="change positive">+10%</span>
    </div>
    <div class="card">
      <h3>Total Clients</h3>
      <p class="value">350</p>
      <span class="change positive">+15%</span>
    </div>
    <div class="card">
      <h3>Upcoming Bookings</h3>
      <p class="value">45</p>
      <span class="change positive">+5%</span>
    </div>
    <div class="card">
      <h3>Pending Leave Requests</h3>
      <p class="value">5</p>
      <span class="change negative">-2%</span>
    </div>
    <div class="card">
      <h3>Monthly Payments</h3>
      <p class="value">Rs.25,000</p>
      <span class="change positive">+8%</span>
    </div>
  </div>


  <!-- Recent Activity -->
  <div class="recent-activity">
    <h2>Recent Activity</h2>
    <div class="search-bar">
    <input type="text" placeholder="     Search...">
    <i class="fas fa-search"></i>
  </div>
    
    <table>
      <thead>
        <tr>
          <th>Activity</th>
          <th>Date</th>
          <th>User</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>New caregiver added</td>
          <td>2025-07-26</td>
          <td>Admin</td>
        </tr>
        <tr>
          <td>Booking confirmed</td>
          <td>2025-07-25</td>
          <td>HR</td>
        </tr>
        <tr>
          <td>Payment received</td>
          <td>2025-07-24</td>
          <td>HR</td>
        </tr>
        <tr>
          <td>Leave request approved</td>
          <td>2025-07-23</td>
          <td>HR</td>
        </tr>
        <tr>
          <td>Client profile updated</td>
          <td>2025-07-22</td>
          <td>Admin</td>
        </tr>
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
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Your custom JS file -->
<script src="<?php echo URLROOT; ?>/public/js/admin/ad_dashboard.js"></script>

</body>
</html>
