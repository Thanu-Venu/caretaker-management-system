<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register a Complaint</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_complaintReg.css">
</head>
<body>
  <div class="main-wrapper">
  <div class="container">
    <h2>Register a Complaint</h2>
    <form id="complaintForm">
      <label for="caretaker">Select Caretaker</label>
      <select id="caretaker" required>
        <option value="">Choose a caretaker</option>
        <option value="John">John</option>
        <option value="Sarah">Sarah</option>
        <option value="Mike">Mike</option>
      </select>

      <label for="serviceDate">Service Date</label>
      <input type="date" id="serviceDate" required>

      <label for="category">Complaint Category</label>
      <select id="category" required>
        <option value="">Choose a category</option>
        <option value="Service Quality">Service Quality</option>
        <option value="Late Arrival">Late Arrival</option>
        <option value="Unprofessional">Unprofessional</option>
      </select>

      <label for="description">Complaint Description</label>
      <textarea id="description" rows="4"></textarea>

      <button type="submit">Submit Complaint</button>
    </form>
  </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/client/c_complaintReg.js"></script>
</body>
</html>
