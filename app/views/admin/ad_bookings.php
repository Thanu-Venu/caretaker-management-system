<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
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
            <option value="AwaitingPayment">AwaitingPayment</option>
            <option value="Rejected">Rejected</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>

          </select>

          </select>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <table class="booking-table" id="bookingTable">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Client Name</th>
              <th>Caretaker Name</th>
              <th>Service Type</th>
              <th>Booking Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($data['bookings'])): ?>
              <?php foreach ($data['bookings'] as $b): ?>
                <tr>
                  <td><?php echo htmlspecialchars($b['booking_id']); ?></td>
                  <td><?php echo htmlspecialchars($b['client_name']); ?></td>
                  <td><?php echo htmlspecialchars($b['caretaker_name']); ?></td>
                  <td><?php echo htmlspecialchars($b['service_type']); ?></td>
                  <td><?php echo htmlspecialchars($b['booking_date']); ?></td>
                  <td>
                    <span class="status <?php echo strtolower($b['status']); ?>">
                      <?php echo htmlspecialchars($b['status']); ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align:center;">No bookings found</td>
              </tr>
            <?php endif; ?>
          </tbody>

        </table>
        
        <div class="pagination">
          <?php
          $current = $data['currentPage'];
          $total = $data['totalPages'];
          ?>

          <?php if ($current > 1): ?>
            <a href="<?= URLROOT ?>/admin/ad_bookings?page=<?= $current - 1 ?>">Prev</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $total; $i++): ?>
            <a href="<?= URLROOT ?>/admin/ad_bookings?page=<?= $i ?>" class="<?= ($i == $current) ? 'active' : '' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>

          <?php if ($current < $total): ?>
            <a href="<?= URLROOT ?>/admin/ad_bookings?page=<?= $current + 1 ?>">Next</a>
          <?php endif; ?>
        </div>

      </div>
    </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/admin/ad_bookings.js"></script>
</body>

</html>