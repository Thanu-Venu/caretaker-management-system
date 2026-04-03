<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Complaints Management</title>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/hr/hr_complaint.css">
</head>

<body>
<main class="main-content">
<br>
<h1>Complaints Management</h1>
<br>
<div class="top">
  <button class="top-button active" onclick="switchTab('c_complaint', event)">Client Complaints</button>
  <button class="top-button" onclick="switchTab('ct_complaint', event)">Caretaker Complaints</button>
</div>
<!-- CLIENT COMPLAINTS -->
<section class="card tab-content active" id="c_complaint">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Client</th>
        <th>Caretaker</th>
        <th>Category</th>
        <th>Details</th>
        <th>Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($complaints)): ?>
        <?php foreach ($complaints as $c): ?>
          <tr>
            <td><?= $c['Id'] ?></td>
            <td><?= $c['client_name'] ?></td>
            <td><?= $c['caretaker_name'] ?></td>
            <td><?= $c['category'] ?></td>
            <td><?= $c['details'] ?></td>
            <td><?= $c['complaint_date'] ?></td>
            <td>
  <?php 
    $status = $c['status'];
    $badgeClass = strtolower(str_replace(' ', '-', $status));
  ?>

  <!-- Status Badge -->
  <span class="status-badge <?= $badgeClass ?>">
      <?= htmlspecialchars($status) ?>
  </span>

  <!-- Update Form -->
  <form method="POST" action="<?= URLROOT ?>/public/index.php?url=Complaint/updateStatus" style="margin-top:8px;">
    <input type="hidden" name="Id" value="<?= $c['Id'] ?>">
    <select name="status">
      <option value="Open" <?= $status=="Open"?'selected':'' ?>>Open</option>
      <option value="In Progress" <?= $status=="In Progress"?'selected':'' ?>>In Progress</option>
      <option value="Resolved" <?= $status=="Resolved"?'selected':'' ?>>Resolved</option>
    </select>
    <button class="btn-update">Update</button>
  </form>
</td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7">No client complaints</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</section>
<!-- CARETAKER COMPLAINTS -->
<section class="card tab-content" id="ct_complaint">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Caretaker</th>
        <th>Client</th>
        <th>Category</th>
        <th>Details</th>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
   <tbody>
<?php if (!empty($data['ct_complaints'])): ?>
    <?php foreach ($data['ct_complaints'] as $c): ?>
        <tr>
            <td><?= $c['complaint_id'] ?></td>
            <td><?= $c['caretaker_name'] ?></td>
            <td><?= $c['client_name'] ?></td>
            <td><?= $c['service_type'] ?></td>
            <td><?= $c['description'] ?></td>
            <td><?= $c['service_date'] ?></td>
            <?php $st = $c['status'] ?? ''; ?>
            <td><span class="status-badge <?= strtolower(str_replace(' ', '-', $st)) ?>"><?= htmlspecialchars($st) ?></span></td>
            <td>
                <form method="POST" action="<?= URLROOT ?>/Complaint/updateCaretakerComplaintStatus">
                    <input type="hidden" name="complaint_id" value="<?= $c['complaint_id'] ?>">
                    <select name="action">
                        <option value="Pending" <?= $c['status']=="Pending"?'selected':'' ?>>Pending</option>
                        <option value="In Progress" <?= $c['status']=="In Progress"?'selected':'' ?>>In Progress</option>
                        <option value="Resolved" <?= $c['status']=="Resolved"?'selected':'' ?>>Resolved</option>
                    </select>
                    <button class="btn-update">Update</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="8">No caretaker complaints</td>
    </tr>
<?php endif; ?>
   </tbody>
</table>
</section>



</main>

<script src="<?= URLROOT ?>/public/js/hr/hr_complaint.js"></script>
</body>
</html>
