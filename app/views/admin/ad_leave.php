<?php
$currentPage = $data['currentPage'] ?? 1;
$totalPages  = $data['totalPages'] ?? 1;
$selectedType = $data['selectedType'] ?? 'All';
$selectedStatus = $data['selectedStatus'] ?? 'All';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave Management</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_leave.css">
  <link href="<?= URLROOT ?>/public/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <!-- Design System Override (ensures consistency) -->

</head>

<body>
  <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
  <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
  <main class="content">
    <section>
      <div class="page-header">
        <h1 class="page-title">Leave Management</h1>
      </div>

      <!-- Filter Section -->
      <div class="filter-section">
        <div class="filter-group">
          <label for="type">Type</label>
          <select id="type" onchange="applyFilters()">
            <option value="All" <?= ($selectedType == "All") ? 'selected' : '' ?>>All</option>
            <option value="Vacation" <?= ($selectedType == "Vacation") ? 'selected' : '' ?>>Vacation</option>
            <option value="Sick Leave" <?= ($selectedType == "Sick Leave") ? 'selected' : '' ?>>Sick Leave</option>
            <option value="Personal Leave" <?= ($selectedType == "Personal Leave") ? 'selected' : '' ?>>Personal Leave</option>
            <option value="Maternity Leave" <?= ($selectedType == "Maternity Leave") ? 'selected' : '' ?>>Maternity Leave</option>
          </select>

        </div>
        <div class="filter-group">
          <label for="status">Status</label>
          <select id="status" onchange="applyFilters()">
            <option value="All" <?= ($selectedStatus == "All") ? 'selected' : '' ?>>All</option>
            <option value="Pending" <?= ($selectedStatus == "Pending") ? 'selected' : '' ?>>Pending</option>
            <option value="Approved" <?= ($selectedStatus == "Approved") ? 'selected' : '' ?>>Approved</option>
            <option value="Rejected" <?= ($selectedStatus == "Rejected") ? 'selected' : '' ?>>Rejected</option>
          </select>
        </div>
      </div>

      <!-- Leave Requests Table -->
      <div class="table-container">
        <table class="leave-table" id="leaveTable">
          <thead>
            <tr>
              <th>Caregiver Name</th>
              <th>Leave Type</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($data['leaves'])): ?>
              <?php foreach ($data['leaves'] as $leave): ?>
                <tr>
                  <td><?= htmlspecialchars($leave['caretaker_name']) ?></td>
                  <td><?= htmlspecialchars($leave['leave_type']) ?></td>
                  <td><?= htmlspecialchars($leave['start_date']) ?></td>
                  <td><?= htmlspecialchars($leave['end_date']) ?></td>
                  <td><span class="status <?= strtolower($leave['status']) ?>"><?= htmlspecialchars($leave['status']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5">No leave requests found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <div class="pagination">
          <?php
          $query = $_GET;
          ?>
          <?php if ($currentPage > 1): ?>
            <?php $query['page'] = $currentPage - 1; ?>
            <a href="<?= URLROOT ?>/admin/ad_leave?<?= http_build_query($query) ?>">Prev</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php $query['page'] = $i; ?>
            <a class="<?= ($i == $currentPage) ? 'active' : '' ?>"
              href="<?= URLROOT ?>/admin/ad_leave?<?= http_build_query($query) ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>

          <?php if ($currentPage < $totalPages): ?>
            <?php $query['page'] = $currentPage + 1; ?>
            <a href="<?= URLROOT ?>/admin/ad_leave?<?= http_build_query($query) ?>">Next</a>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <script>
    function applyFilters() {
      const typeEl = document.getElementById('type');
      const statusEl = document.getElementById('status');
      if (!typeEl || !statusEl) return;

      const params = new URLSearchParams(window.location.search);
      if (!params.get('url')) {
        params.set('url', 'admin/ad_leave');
      }
      params.set('page', '1');
      params.set('type', typeEl.value);
      params.set('status', statusEl.value);

      window.location = window.location.pathname + '?' + params.toString();
    }
  </script>

</body>

</html>
