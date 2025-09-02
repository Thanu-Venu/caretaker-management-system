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
      <div class="filter-section">
        <div class="filter-group">
          <label for="type">Type</label>
          <select id="type">
            <option>All</option>
            <option>Elder Care</option>
            <option>Babysitting</option>
            <option>Cleaning & Cooking</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="date">Date</label>
          <select id="date">
            <option>All</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="status">Status</label>
          <select id="status">
            <option>All</option>
            <option>Pending</option>
            <option>Ongoing</option>
            <option>Completed</option>
          </select>
        </div>
      </div>

      <div class="table-container">
        <table class="booking-table">
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
              <td>Babysitting</td>
              <td>2024-03-16</td>
              <td><span class="status ongoing">Ongoing</span></td>
            </tr>
            <tr>
              <td>#12347</td>
              <td>Sarah Johnson</td>
              <td>Cleaning and Cooking</td>
              <td>2024-03-17</td>
              <td><span class="status completed">Completed</span></td>
            </tr>
            <tr>
              <td>#12348</td>
              <td>Michael Brown</td>
              <td>Babysitting</td>
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
              <td>Babysitting</td>
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
              <td>Cleaning & Cooking</td>
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
</body>
</html>