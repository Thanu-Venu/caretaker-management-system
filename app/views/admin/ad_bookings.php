<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service Booking Management</title>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/vendor/font-awesome/css/all.min.css">
  <link href="<?= URLROOT ?>/public/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_bookings.css">
  <!-- Design System Override (ensures consistency) -->
</head>

<body>
  <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
  <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
  <div class="main-content">
    <div class="booking-header page-header">
      <h1 class="page-title">Service Booking Management</h1>
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
          <option value="Requested">Requested</option>
          <option value="Pending">Pending</option>
          <option value="Payment_Requested">Payment requested</option>
          <option value="AwaitingPayment">Awaiting payment</option>
          <option value="Advance_Paid">Advance paid</option>
          <option value="Accepted">Accepted</option>
          <option value="Reschedule_Requested">Reschedule requested</option>
          <option value="Change_Requested">Change requested</option>
          <option value="Rejected">Rejected</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>
    </div>

    <!-- Table Section -->
    <div class="table-container">
      <table class="booking-table no-table-collapse" id="bookingTable">
        <thead>
          <tr>
            <th>Client Name</th>
            <th>Caretaker Name</th>
            <th>Service Type</th>
            <th>Booking Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($data['bookings'])): ?>
            <?php foreach ($data['bookings'] as $b): ?>
              <?php
              $bookingDetail = [
                'booking_id' => (string)($b['booking_id'] ?? ''),
                'client_name' => (string)($b['client_name'] ?? ''),
                'caretaker_name' => (string)($b['caretaker_name'] ?? ''),
                'service_type' => (string)($b['service_type'] ?? ''),
                'booking_date' => (string)($b['booking_date'] ?? ''),
                'status' => (string)($b['status'] ?? ''),
              ];
              foreach ($b as $key => $val) {
                if (!array_key_exists($key, $bookingDetail) && $val !== null && $val !== '') {
                  $bookingDetail[$key] = is_scalar($val) ? (string)$val : json_encode($val);
                }
              }
              $bookingJson = htmlspecialchars(json_encode($bookingDetail, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
              ?>
              <tr data-booking-detail="<?= $bookingJson ?>">
                <td><?php echo htmlspecialchars($b['client_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['caretaker_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['service_type'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($b['booking_date'] ?? ''); ?></td>
                <td>
                  <?php
                  $statusRaw = (string)($b['status'] ?? '');
                  $statusSlug = strtolower(trim($statusRaw));
                  $statusSlug = preg_replace('/[^a-z0-9]+/', '_', $statusSlug);
                  $statusSlug = trim($statusSlug, '_');
                  if ($statusSlug === '') {
                      $statusSlug = 'unknown';
                  }
                  ?>
                  <span class="booking-status-pill booking-status-pill--<?= htmlspecialchars($statusSlug, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($statusRaw) ?>
                  </span>
                </td>
                <td class="actions"></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" style="text-align:center;">No bookings found</td>
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

  <script src="<?php echo URLROOT; ?>/public/js/admin/ad_bookings.js"></script>
</body>

</html>