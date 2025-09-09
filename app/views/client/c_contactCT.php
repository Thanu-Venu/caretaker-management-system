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

  <div class="caretaker-card">
    <img src="<?php echo URLROOT; ?>/public/images/find.png" alt="Caretaker">
    <div class="caretaker-info">
      <h3>Sarah Johnson</h3>
      <p><strong>Service:</strong> Elder Care</p>
      <p><strong>Contact:</strong> +94 77 123 4567</p>
      <p><strong>Email:</strong> sarah.johnson@example.com</p>
      <p><strong>Available:</strong> Mon-Fri, 9 AM - 5 PM</p>
      <div class="buttons">
        <button class="btn-call">Call</button>
        <button class="btn-msg">Message</button>
        <button class="btn-call1"><a href="http://localhost/CMA/public/?url=client/c_dashboard">Back to home</a></button>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_contact_caretaker.css">
<script src="<?php echo URLROOT; ?>/public/js/client/c_contactCT.js"></script>
