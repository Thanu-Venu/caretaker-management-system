<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave Management</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_leave.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
  <main class="content">
    <section>
      <h1>Leave Management</h1>

      <!-- Filter Section -->
      <div class="filter-section">
        <div class="filter-group">
          <label for="type">Type</label>
          <select id="type" onchange="filterTable()">
            <option value="All">All</option>
            <option value="Vacation">Vacation</option>
            <option value="Sick Leave">Sick Leave</option>
            <option value="Personal Leave">Personal Leave</option>
            <option value="Maternity Leave">Maternity Leave</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="status">Status</label>
          <select id="status" onchange="filterTable()">
            <option value="All">All</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
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
              <th>Total Days</th>
              <th>Monthly Usage</th>
              <th>Affected Bookings</th>
              <th>Replacement</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($data['leaves'])): ?>
              <?php foreach ($data['leaves'] as $leave): ?>
                <tr class="<?= !empty($leave['replacement_required']) ? 'row-impact' : '' ?>">
                  <td><?= htmlspecialchars($leave['caretaker_name']) ?></td>
                  <td><?= htmlspecialchars($leave['leave_type']) ?></td>
                  <td><?= htmlspecialchars($leave['start_date']) ?></td>
                  <td><?= htmlspecialchars($leave['end_date']) ?></td>
                  <td><?= (int)($leave['request_days'] ?? 0) ?> day(s)</td>
                  <td>
                    <?= (int)($leave['monthly_used_after_request'] ?? 0) ?> / <?= (int)($leave['monthly_limit'] ?? 5) ?>
                  </td>
                  <td><?= (int)($leave['affected_booking_count'] ?? 0) ?></td>
                  <td>
                    <?php if (!empty($leave['replacement_required'])): ?>
                      <span class="replacement-badge needed">Required</span>
                    <?php else: ?>
                      <span class="replacement-badge not-needed">Not Required</span>
                    <?php endif; ?>
                  </td>
                  <td><span
                      class="status <?= strtolower($leave['status']) ?>"><?= htmlspecialchars($leave['status']) ?></span>
                  </td>
                  <td>
                    <?php if ($leave['status'] == 'Pending'): ?>
                      <a href="<?= URLROOT; ?>/public/?url=HrLeave/approve_form/<?= $leave['id'] ?>" class="approve-btn">
                        <i class='bx bx-check-circle' style="color:green;"></i>
                      </a>

                      <a href="<?= URLROOT; ?>/public/?url=HrLeave/reject/<?= $leave['id'] ?>"
                        onclick="return confirm('Reject this leave?')" class="reject-btn">
                        <i class='bx bx-x-circle' style="color:red;"></i>
                      </a>


                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="10">No leave requests found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>


        <?php
        $page = $data['page'] ?? 1;
        $totalPages = $data['totalPages'] ?? 1;

        function pageUrl($p)
        {
          $params = $_GET;
          $params['page'] = $p;
          $params['url']  = 'HrLeave/index';
          return URLROOT . "/public/?" . http_build_query($params);
        }

        ?>
        <?php if ($totalPages > 1): ?>

          <div class="pagination">
            <!-- Prev -->
            <a class="pg <?= ($page <= 1) ? 'disabled' : '' ?>"
              href="<?= ($page <= 1) ? '#' : pageUrl($page - 1) ?>">Prev</a>

            <?php
            // show compact pages like: 1 2 3 4 ... last
            $start = max(1, $page - 2);
            $end   = min($totalPages, $page + 2);

            if ($start > 1) {
              echo '<a class="pg" href="' . pageUrl(1) . '">1</a>';
              if ($start > 2) echo '<span class="dots">...</span>';
            }

            for ($i = $start; $i <= $end; $i++) {
              $active = ($i == $page) ? 'active' : '';
              echo '<a class="pg ' . $active . '" href="' . pageUrl($i) . '">' . $i . '</a>';
            }

            if ($end < $totalPages) {
              if ($end < $totalPages - 1) echo '<span class="dots">...</span>';
              echo '<a class="pg" href="' . pageUrl($totalPages) . '">' . $totalPages . '</a>';
            }
            ?>

            <!-- Next -->
            <a class="pg <?= ($page >= $totalPages) ? 'disabled' : '' ?>"
              href="<?= ($page >= $totalPages) ? '#' : pageUrl($page + 1) ?>">Next</a>
          </div>
        <?php endif; ?>

      </div>
    </section>
  </main>

  <script>
    function filterTable() {
      const typeFilter = document.getElementById('type').value.toLowerCase();
      const statusFilter = document.getElementById('status').value.toLowerCase();

      document.querySelectorAll('#leaveTable tbody tr').forEach(row => {
        const type = row.cells[1].innerText.toLowerCase();
        const status = row.cells[8].innerText.toLowerCase();

        const typeMatch = typeFilter === 'all' || type === typeFilter;
        const statusMatch = statusFilter === 'all' || status === statusFilter;

        row.style.display = (typeMatch && statusMatch) ? '' : 'none';
      });
    }
  </script>
</body>

</html>