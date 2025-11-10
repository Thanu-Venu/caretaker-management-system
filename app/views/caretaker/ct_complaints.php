<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare Dashboard</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_complaints.css">
</head>
<body>
    <div class="main-content">
  <h1>Register a Complaint</h1>

  <form id="complaintForm">
    <label for="clientName">Select Client</label>
    <select id="clientName" required>
      <option value="">-- Select Client --</option>
      <option value="mrs_johnson">Mrs Johnson</option>
      <option value="smith_family">The Smith Family</option>
      <option value="mr_davis">Mr Davis</option>
    </select>

    <label for="serviceType">Service Type</label>
    <select id="serviceType" required>
      <option value="">-- Select Service --</option>
      <option value="elder_care">Elder Care</option>
      <option value="maid_service">Maid Service</option>
      <option value="babysitting">Babysitting</option>
    </select>

    <label for="dateOfService">Date of Service</label>
    <input type="date" id="dateOfService" required>

    <label for="complaintDesc">Complaint Description</label>
    <textarea id="complaintDesc" placeholder="Describe the issue..." required></textarea>

    <button type="submit" class="btn-submit">Submit Complaint</button>
  </form>

  <h2>Past Complaints</h2>
  <table class="complaint-table">
    <thead>
      <tr>
        <th>Client</th>
        <th>Service</th>
        <th>Date</th>
        <th>Description</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody id="complaintTableBody">
      <!-- Populated dynamically -->
    </tbody>
  </table>
</div>
<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_complaints.js"></script>
</body>