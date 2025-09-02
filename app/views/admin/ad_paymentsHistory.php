<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payments & Billing</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_paymentsHistory.css">
</head>
<body>
  <div class="payments-container">
    <div class="payments-header">
      <h1>Payments & Billing</h1>
      <div class="tabs">
        <a href="#" class="tab active">Payment History</a>
        <a href="#" class="tab">Pending Payments</a>
      </div>
    </div>

    <div class="search-section">
      <h3>Payment History</h3>
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
            <td>Michael Brown</td>
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
</body>
</html>