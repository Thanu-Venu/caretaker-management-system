<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register a Complaint</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_contactCT.css">
</head>
<body></body>
<div class="main-content">
  <h2>Contact Your Caretaker</h2>

  <?php $caretaker = $data['caretaker'] ?? null; ?>
  <?php if ($caretaker): ?>
  <div class="caretaker-card">
    <img src="<?php echo URLROOT; ?>/public/images/find.png" alt="Caretaker">
    <div class="caretaker-info">
      <h3><?= htmlspecialchars($caretaker['name'] ?? 'Caretaker') ?></h3>
      <p><strong>Service:</strong> <?= htmlspecialchars($caretaker['service_type'] ?? 'N/A') ?></p>
      <p><strong>Contact:</strong> <?= htmlspecialchars($caretaker['phone'] ?? 'N/A') ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($caretaker['email'] ?? 'N/A') ?></p>
      <div class="buttons">
        <button class="btn-call1"><a href="<?= URLROOT ?>/client/c_dashboard">Back to home</a></button>
      </div>
    </div>
  </div>
  <?php else: ?>
    <p>No caretaker details available.</p>
  <?php endif; ?>
</div>

<script src="<?php echo URLROOT; ?>/public/js/client/c_contactCT.js"></script>
