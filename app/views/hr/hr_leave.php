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
              <th>Status</th>
              <th>Actions</th>
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
                  <td><span
                      class="status <?= strtolower($leave['status']) ?>"><?= htmlspecialchars($leave['status']) ?></span>
                  </td>
                  <td>
                    <?php if ($leave['status'] == 'Pending'): ?>
                      <a href="<?= URLROOT ?>/HrLeave/approve_form/<?= $leave['id'] ?>"
   class="approve-btn">
   <i class='bx bx-check-circle' style="color:green;"></i>
</a>

<a href="<?= URLROOT ?>/HrLeave/reject/<?= $leave['id'] ?>"
   onclick="return confirm('Reject this leave?')"
   class="reject-btn">
   <i class='bx bx-x-circle' style="color:red;"></i>
</a>

                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No leave requests found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <script>
    function filterTable() {
      const typeFilter = document.getElementById('type').value.toLowerCase();
      const statusFilter = document.getElementById('status').value.toLowerCase();

      document.querySelectorAll('#leaveTable tbody tr').forEach(row => {
        const type = row.cells[1].innerText.toLowerCase();
        const status = row.cells[4].innerText.toLowerCase();

        const typeMatch = typeFilter === 'all' || type === typeFilter;
        const statusMatch = statusFilter === 'all' || status === statusFilter;

        row.style.display = (typeMatch && statusMatch) ? '' : 'none';
      });
    }
  </script>
</body>

</html>