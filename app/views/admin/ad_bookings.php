<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service Booking Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_bookings.css">
</head>
<body>
  <div class="main-content">
    <div class="booking-container">
      <div class="booking-header">
        <h1>Service Booking Management</h1>
      </div>

      <!-- Filter Section -->
      <div class="filter-section">
        <div class="filter-group">
          <label for="type">Type</label>
          <select id="type" onchange="filterTable()">
            <option value="All">All</option>
            <option value="Elder Care">Elder Care</option>
            <option value="BabySitter">BabySitter</option>
            <option value="Maid">Maid</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="dateFilter">Date</label>
          <input type="date" id="date" onchange="filterTable()">
        </div>
        <div class="filter-group">
          <label for="status">Status</label>
          <select id="status" onchange="filterTable()">
            <option value="All">All</option>
            <option value="Pending">Pending</option>
            <option value="Ongoing">Ongoing</option>
            <option value="Completed">Completed</option>
          </select>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <table class="booking-table" id="bookingTable">
          <thead>
            <tr>
              <th>Request ID</th>
              <th>Client Name</th>
              <th>Service Type</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#12345</td>
              <td>Emily Carter</td>
              <td>Elder Care</td>
              <td>2024-03-15</td>
              <td><span class="status pending">Pending</span></td>
            </tr>
            <tr>
              <td>#12346</td>
              <td>David Lee</td>
              <td>BabySitter</td>
              <td>2024-03-16</td>
              <td><span class="status ongoing">Ongoing</span></td>
            </tr>
            <tr>
              <td>#12347</td>
              <td>Sarah Johnson</td>
              <td>Maid</td>
              <td>2024-03-17</td>
              <td><span class="status completed">Completed</span></td>
            </tr>
            <tr>
              <td>#12348</td>
              <td>Michael Brown</td>
              <td>BabySitter</td>
              <td>2024-03-18</td>
              <td><span class="status pending">Pending</span></td>
            </tr>
            <tr>
              <td>#12349</td>
              <td>Jessica Davis</td>
              <td>Elder Care</td>
              <td>2024-03-19</td>
              <td><span class="status ongoing">Ongoing</span></td>
            </tr>
            <tr>
              <td>#12350</td>
              <td>Kevin Wilson</td>
              <td>BabySitter</td>
              <td>2024-03-20</td>
              <td><span class="status completed">Completed</span></td>
            </tr>
            <tr>
              <td>#12351</td>
              <td>Amanda Taylor</td>
              <td>Elder Care</td>
              <td>2024-03-21</td>
              <td><span class="status pending">Pending</span></td>
            </tr>
            <tr>
              <td>#12352</td>
              <td>Brian Clark</td>
              <td>Maid</td>
              <td>2024-03-22</td>
              <td><span class="status ongoing">Ongoing</span></td>
            </tr>
            <tr>
              <td>#12353</td>
              <td>Melissa White</td>
              <td>Elder Care</td>
              <td>2024-03-23</td>
              <td><span class="status completed">Completed</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/admin/ad_bookings.js"></script>
</body>
</html>
