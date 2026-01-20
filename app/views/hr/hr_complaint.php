<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Complaints Management - SmartCare</title>

  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_complaint.css" />
</head>

<body>
  <main class="main-content">
    <h1>Complaints Management</h1>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Complaint ID</th>
            <th>Client Name</th>
            <th>Caretaker Name</th>
            <th>Category</th>
            <th>Details</th>
            <th>Complaint_Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($complaints) && is_array($complaints)): ?>
            <?php foreach ($complaints as $c): ?>
              <tr>
                <td><?php echo $c['Id']; ?></td>
                <td><?php echo $c['client_name']; ?></td>
                <td><?php echo $c['caretaker_name']; ?></td>
                <td><?php echo $c['category']; ?></td>
                <td><?php echo $c['details']; ?></td>
                <td><?php echo $c['complaint_date']; ?></td>
                <td>
                  <form method="POST" action="<?= URLROOT ?>/public/index.php?url=Complaint/updateStatus">
                    <input type="hidden" name="Id" value="<?= $c['Id']; ?>">

                    <select name="status">
                      <option value="Open" <?= $c['status'] == 'Open' ? 'selected' : '' ?>>Open</option>
                      <option value="In Progress" <?= $c['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                      <option value="Resolved" <?= $c['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                    </select>

                <button class="btn-update">
    <i class="bx bx-edit"></i> Update
</button>


                </form>
                </td>


              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8">No complaints found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>

</html>