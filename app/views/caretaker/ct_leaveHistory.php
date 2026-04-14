<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_leaveHistory.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/admin-ui.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_sidebar.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
</head>
<body>
<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>
<main class="content leavehistory-container">
  <h1>Leave History</h1>

  <table class="leave-history-table">
    <thead>
      <tr>
        <th>Leave Type</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Reason</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody id="leaveTableBody">
      <!-- Data will be injected dynamically via JS -->
    </tbody>
  </table>

  <div class="pagination">
    <button id="prevPage" class="btn">Previous</button>
    <span id="pageInfo"></span>
    <button id="nextPage" class="btn">Next</button>
  </div>
</div>
<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_leaveHistory.js"></script>
</body>