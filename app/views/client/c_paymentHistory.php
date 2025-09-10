<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment History</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_paymentHistory.css">
</head>
<body>
  <div class="container">

    <!-- Title -->
    <div class="header">
      <h1>Payment History</h1>
      <p>Track and manage all your payment transactions.</p>
    </div>

    <!-- Summary Cards -->
    <div class="cards">
      <div class="card">
        <h3>Total Payments</h3>
        <p class="amount">LKR 208,855.00</p>
        <span>6 transactions</span>
      </div>
      <div class="card">
        <h3>Completed</h3>
        <p class="amount green">LKR 104,500.00</p>
        <span>4 successful</span>
      </div>
      <div class="card">
        <h3>Pending</h3>
        <p class="amount orange">LKR 104,355.00</p>
        <span>1 awaiting</span>
      </div>
      <div class="card">
        <h3>Total Hours</h3>
        <p class="amount blue">38</p>
        <span>care hours provided</span>
      </div>
    </div>

    <!-- Search & Filters -->
    <div class="filters">
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search by description or payment ID...">
      </div>
      <select id="statusFilter">
        <option value="">All Status</option>
        <option value="Completed">Completed</option>
        <option value="Pending">Pending</option>
        <option value="Failed">Failed</option>
      </select>
      <select id="serviceFilter">
        <option value="">All Services</option>
        <option value="BabySitter">BabySitter</option>
        <option value="Elder Care">Elder Care</option>
        <option value="Maid">Maid</option>
      </select>
    </div>

    <!-- Table -->
    <table id="paymentTable">
      <thead>
        <tr>
          <th>Payment ID</th>
          <th>Caretaker</th>
          <th>Service Type</th>
          <th>Hours</th>
          <th>Rate</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Service Date</th>
        </tr>
      </thead>
      <tbody>
        <tr data-status="Completed" data-service="BabySitter">
          <td>PAY-001</td>
          <td>Sarah Williams</td>
          <td>BabySitter</td>
          <td>8h</td>
          <td>LKR 7500.00/hr</td>
          <td>LKR 35000.00</td>
          <td><span class="status completed">Completed</span></td>
          <td>Jan 15, 2025</td>
        </tr>
        <tr data-status="Completed" data-service="Elder Care">
          <td>PAY-002</td>
          <td>Maria Rodriguez</td>
          <td>Elder Care</td>
          <td>6h</td>
          <td>LKR 8000.00/hr</td>
          <td>LKR 90000.00</td>
          <td><span class="status completed">Completed</span></td>
          <td>Jan 11, 2025</td>
        </tr>
        <tr data-status="Pending" data-service="Maid">
          <td>PAY-003</td>
          <td>Lisa Anderson</td>
          <td>Maid</td>
          <td>12h</td>
          <td>LKR 7000.00/hr</td>
          <td>LKR 50000.00</td>
          <td><span class="status pending">Pending</span></td>
          <td>Jan 09, 2025</td>
        </tr>
        <tr data-status="Failed" data-service="Elder Care">
          <td>PAY-004</td>
          <td>Amanda Garcia</td>
          <td>Elder Care</td>
          <td>5h</td>
          <td>LKR 8500.00/hr</td>
          <td>LKR 100000.00</td>
          <td><span class="status failed">Failed</span></td>
          <td>Jan 04, 2025</td>
        </tr>
        <tr data-status="Pending" data-service="Maid">
          <td>PAY-005</td>
          <td>Lisa Anderson</td>
          <td>Maid</td>
          <td>12h</td>
          <td>LKR 7000.00/hr</td>
          <td>LKR 50000.00</td>
          <td><span class="status pending">Pending</span></td>
          <td>Jan 01, 2025</td>
        </tr>
      </tbody>
    </table>

    <p id="noResults" style="display:none; text-align:center; margin-top:20px; color:red;">
      No matching records found.
    </p>

    <!-- Footer -->
    <div class="footer">
      <p>Showing 1 to 5 of 6 results</p>
      <div class="pagination">
        <button>&lt; Previous</button>
        <button>Next &gt;</button>
      </div>
    </div>

  </div>

  <!-- JavaScript -->
  <script src="<?php echo URLROOT; ?>/public/js/client/c_paymentHistory.js"></script>
</body>
</html>
