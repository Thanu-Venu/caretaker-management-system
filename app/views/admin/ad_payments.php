<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payments & Billing</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_payments.css">
</head>
<body>
<div class="payments-container">
  <div class="payments-header">
    <h1>Payments & Billing</h1>
    <!-- Tabs -->
    <div class="tabs">
      <button class="tab-btn active" data-tab="history">Payment History</button>
      <button class="tab-btn" data-tab="pending">Pending Payments</button>
      <button class="tab-btn" data-tab="invoices">Invoices</button>
    </div>
  </div>

  <!-- Payment History -->
  <div id="historySection" class="tab-section">
    <h2>Payment History</h2>
    <div class="search-box">
      <i class='bx bx-search'></i>
      <input type="text" id="historySearch" placeholder="Search by client name or payment ID">
    </div>
    <div class="table-container">
      <table id="historyTable" class="payments-table">
        <thead>
          <tr>
            <th>Payment ID</th>
            <th>Client Name</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>PAY2024001</td>
            <td>Emily Carter</td>
            <td>LKR 75,582.22</td>
            <td>2025-07-20</td>
            <td><span class="status completed">Completed</span></td>
          </tr>
          <tr>
            <td>PAY2024002</td>
            <td>David Lee</td>
            <td>LKR 90,633.39</td>
            <td>2025-07-15</td>
            <td><span class="status completed">Completed</span></td>
          </tr>
          <tr>
            <td>PAY2024003</td>
            <td>Sarah Johnson</td>
            <td>LKR 60,402.26</td>
            <td>2025-07-10</td>
            <td><span class="status completed">Completed</span></td>
          </tr>
          <tr>
            <td>PAY2024004</td>
            <td>Micheal Brown</td>
            <td>LKR 84,563.16</td>
            <td>2025-07-05</td>
            <td><span class="status completed">Completed</span></td>
          </tr>
          <tr>
            <td>PAY2024005</td>
            <td>Jessica Wilson</td>
            <td>LKR 96,643.32</td>
            <td>2025-07-01</td>
            <td><span class="status completed">Completed</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pending Payments -->
  <div id="pendingSection" class="tab-section" style="display:none;">
    <h2>Pending Payments</h2>
    <div class="search-box">
      <i class='bx bx-search'></i>
      <input type="text" id="pendingSearch" placeholder="Search by client name or payment ID">
    </div>
    <div class="table-container">
      <table id="pendingTable" class="payments-table">
        <thead>
          <tr>
            <th>Payment ID</th>
            <th>Client Name</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>PAY2024007</td>
            <td>John</td>
            <td>LKR 15,100.57</td>
            <td>2025-07-22</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
          <tr>
            <td>PAY2024008</td>
            <td>Laxshan</td>
            <td>LKR 30,201.13</td>
            <td>2025-07-18</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
          <tr>
            <td>PAY2024009</td>
            <td>Vira Manson</td>
            <td>LKR 36,241.36</td>
            <td>2025-07-10</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
          <tr>
            <td>PAY2024010</td>
            <td>David Parade</td>
            <td>LKR 75,502.82</td>
            <td>2025-07-05</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
          <tr>
            <td>PAY2024011</td>
            <td>Michael</td>
            <td>LKR 60,402.26</td>
            <td>2025-07-21</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Invoices -->
  <div id="invoicesSection" class="tab-section" style="display:none;">
    <h2>Invoices</h2>
    <p>Invoices data will go here.</p>
  </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/admin/ad_payments.js"></script>
</body>
</html>
