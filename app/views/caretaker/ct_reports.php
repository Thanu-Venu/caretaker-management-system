<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare Dashboard</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_reports.css">
</head>
<body>
<div class="main-content">
  <h1>My Reports</h1>

  <!-- Monthly Summary -->
  <section class="report-summary">
    <h2>Monthly Summary</h2>
    <div class="summary-cards">
      <div class="card">
        <h3>Total Services</h3>
        <p id="totalServices">0</p>
      </div>
      <div class="card">
        <h3>Total Hours</h3>
        <p id="totalHours">0</p>
      </div>
      <div class="card">
        <h3>Total Earnings(LKR)</h3>
        <p id="totalEarnings">0</p>
      </div>
    </div>
  </section>

  <!-- Service Details Table -->
  <section class="report-table-section">
    <h2>Service Details</h2>
    <table class="report-table">
      <thead>
        <tr>
          <th>Client</th>
          <th>Service</th>
          <th>Date</th>
          <th>Hours</th>
          <th>Payment(LKR)</th>
        </tr>
      </thead>
      <tbody id="serviceTableBody">
        <!-- Populated dynamically -->
      </tbody>
    </table>

    <button id="downloadReport" class="btn-download">Download Report</button>
  </section>
</div>
    <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_reports.js"></script>
    </body>