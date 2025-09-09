<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Management</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_dashboard.css">
  <link rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
   <!-- Main Content Container -->
<div class="main-content">

  <!-- Dashboard Header -->
  <div class="dashboard-header">
    <h1>HR Manager Dashboard</h1>
  </div>

  <!-- KPI Cards -->
  <div class="kpi-cards">
    <div class="kpi-card">
      <h3>Total Caretakers</h3>
      <p id="total-caretakers">25</p>
    </div>
    <div class="kpi-card">
      <h3>Active Services Today</h3>
      <p id="active-services">12</p>
    </div>
    <div class="kpi-card">
      <h3>Pending Leave Requests</h3>
      <p id="pending-leave">3</p>
      <a href="leave.php" class="view-link">View All</a>
    </div>
    <div class="kpi-card">
      <h3>Pending Client Requests</h3>
      <p id="pending-requests">5</p>
      <a href="client-requests.php" class="view-link">View All</a>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="charts-section">
    <div class="chart-card">
      <h3>Attendance Summary</h3>
      <canvas id="attendanceChart"></canvas>
    </div>
    <div class="chart-card">
      <h3>Caretaker Performance Ratings</h3>
      <canvas id="performanceChart"></canvas>
    </div>
  </div>

  <!-- Pending Items Quick View -->
  <div class="pending-section">
    <div class="pending-card">
      <h3>Recent Leave Requests</h3>
      <ul>
        <li>John Smith: 2025-09-10 to 2025-09-12 <a href="leave.php">Approve/Decline</a></li>
        <li>Jane Doe: 2025-09-11 to 2025-09-13 <a href="leave.php">Approve/Decline</a></li>
      </ul>
    </div>
    <div class="pending-card">
      <h3>Recent Complaints</h3>
      <ul>
        <li>Michael Lee: Late arrival <a href="complaints.php">Resolve</a></li>
        <li>Emily Brown: Service delay <a href="complaints.php">Resolve</a></li>
      </ul>
    </div>
  </div>

</div>

<script src="<?php echo URLROOT; ?>/public/js/hr/hr_dashboard.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
    responsive: true
  }
});
</script>

</body>