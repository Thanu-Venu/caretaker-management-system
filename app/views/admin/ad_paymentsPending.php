<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payments & Billing</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_paymentsPending.css">
</head>
<body>
  <div class="payments-container">
    <div class="payments-header">
      <h1>Payments & Billing</h1>
      <div class="tabs">
        <a href="#" class="tab">Payment History</a>
        <a href="#" class="tab active">Pending Payments</a>
      </div>
    </div>

    <div class="search-section">
      <h3>Pending Payments</h3>
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search by client name or payment ID">
      </div>
    </div>

    <div class="table-container">
      <table class="payments-table">
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
</body>
</html>