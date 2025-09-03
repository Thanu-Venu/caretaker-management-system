<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment History</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_payment.css">
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
        <input type="text" placeholder="Search by description or payment ID...">
      </div>
      <select>
        <option>All Status</option>
        <option>Completed</option>
        <option>Pending</option>
        <option>Failed</option>
      </select>
      <select>
        <option>All Services</option>
        <option>BabySitters</option>
        <option>Elder Care</option>
        <option>Maids</option>
      </select>
    </div>

    <!-- Table -->
    <table>
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
        <tr>
          <td>PAY-001</td>
          <td>Sarah Williams</td>
          <td>BabySitters</td>
          <td>8h</td>
          <td>LKR 7500.00/hr</td>
          <td>LKR 35000.00</td>
          <td><span class="status completed">Completed</span></td>
          <td>Jan 15, 2025</td>
        </tr>
        <tr>
          <td>PAY-002</td>
          <td>Maria Rodriguez</td>
          <td>Elder Care</td>
          <td>6h</td>
          <td>LKR 8000.00/hr</td>
          <td>LKR 90000.00</td>
          <td><span class="status completed">Completed</span></td>
          <td>Jan 11, 2025</td>
        </tr>
        <tr>
          <td>PAY-003</td>
          <td>Lisa Anderson</td>
          <td>Maids</td>
          <td>12h</td>
          <td>LKR 7000.00/hr</td>
          <td>LKR 50000.00</td>
          <td><span class="status pending">Pending</span></td>
          <td>Jan 09, 2025</td>
        </tr>
        <tr>
          <td>PAY-004</td>
          <td>Amanda Garcia</td>
          <td>Elder Care</td>
          <td>5h</td>
          <td>LKR 8500.00/hr</td>
          <td>LKR 100000.00</td>
          <td><span class="status failed">Failed</span></td>
          <td>Jan 04, 2025</td>
        </tr>
        <tr>
          <td>PAY-005</td>
          <td>Lisa Anderson</td>
          <td>Maids</td>
          <td>12h</td>
          <td>LKR 7000.00/hr</td>
          <td>LKR 50000.00</td>
          <td><span class="status pending">Pending</span></td>
          <td>Jan 01, 2025</td>
        </tr>
      </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
      <p>Showing 1 to 5 of 6 results</p>
      <div class="pagination">
        <button>&lt; Previous</button>
        <button>Next &gt;</button>
      </div>
    </div>

  </div>
</body>
</html>
