<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Management - SmartCare</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_complaint.css" />
  <link rel="stylesheet"  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<main class="main-content">
    <br>
    <h1>Complaints Management</h1>
    <p class="subtitle">Manage and resolve client complaints efficiently.</p>

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
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
<?php if(!empty($complaints) && is_array($complaints)): ?>
    <?php foreach($complaints as $c): ?>
        <tr>
            <td><?php echo $c['Id']; ?></td>
            <td><?php echo $c['client_name']; ?></td>
            <td><?php echo $c['caretaker_name']; ?></td>
            <td><?php echo $c['category']; ?></td>      
            <td><?php echo $c['details']; ?></td>
            <td><?php echo $c['complaint_date']; ?></td>
            <td><?php echo $c['status']; ?></td>
            <td>
                <a href="/CMA/public/index.php?url=Complaint/edit/<?php echo $c['Id']; ?>">Edit</a> |
                <a href="/CMA/public/index.php?url=Complaint/delete/<?php echo $c['Id']; ?>" 
                   onclick="return confirm('Are you sure you want to delete this complaint?');">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="8">No complaints found.</td></tr>
<?php endif; ?>
        </tbody>
      </table>
    </div>
</main>
</body>
</html>
