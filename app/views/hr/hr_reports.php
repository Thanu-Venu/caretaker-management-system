<?php  include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php  include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Announcement</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_reports.css">
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
   
  <div class="reports-container">

    <!-- Page Header -->
    <div class="reports-header">
      <h2>Reports</h2>
      <div class="filters">
        <label for="from">From:</label>
        <input type="date" id="fromDate">
         <label for="from">To:</label>
        <input type="date" id="toDate">
        <button onclick="applyFilters()" class="apply-btn">Apply</button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="card">Total Caretakers <span id="caretakersCount">25</span></div>
      <div class="card">Total Clients <span id="clientsCount">40</span></div>
      <div class="card">Ongoing Services <span id="ongoingCount">12</span></div>
      <div class="card">Total Revenue <span id="revenueCount">LKR 150,000</span></div>
      <div class="card">Pending Requests <span id="pendingCount">5</span></div>
    </div>

    <!-- Per Service Engagement Table -->
    <div class="table-section">
      <h3>Per Service Engagement</h3>
      <table>
        <thead>
          <tr>
            <th>Caretaker</th>
            <th>Client</th>
            <th>Service Type</th>
            <th>Service Basis</th>
            <th>Location</th>
            <th>Hours Worked</th>
            <th>Rating</th>
            <th>Earnings</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="serviceEngagementBody">
          <!-- Data filled via JS -->
        </tbody>
      </table>
    </div>

    <!-- Caretaker Monthly Summary Table -->
    <div class="table-section">
      <h3>Caretaker Monthly Summary</h3>
      <table>
        <thead>
          <tr>
            <th>Caretaker</th>
            <th>Total Hours</th>
            <th>Total Earnings</th>
            <th>No. of Clients</th>
            <th>Average Rating</th>
          </tr>
        </thead>
        <tbody id="summaryBody">
          <!-- Data filled via JS -->
        </tbody>
      </table>
    </div>

    <!-- Charts -->
    <div class="chart-section">
      <h3>Revenue by Service Type</h3>
      <canvas id="revenueChart"></canvas>
    </div>

    <div class="chart-section">
      <h3>Monthly Bookings Trend</h3>
      <canvas id="bookingsChart"></canvas>
    </div>
    <button id="downloadReport" class="btn-download">Download Report</button>

  </div>
  <script src="<?php echo URLROOT; ?>/public/js/hr/hr_reports.js"></script>
</body>
</html>
