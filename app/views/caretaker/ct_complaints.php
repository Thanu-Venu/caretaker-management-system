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

  <form id="complaintForm" action="<?php echo URLROOT; ?>/caretaker/saveComplaint" method="POST">
    <label for="clientName">Select Client</label>
   <select id="clientName" name="client_id" required>
    <option value="">-- Select Client --</option>

    <?php foreach ($data['clients'] as $client): ?>
        <option 
            value="<?= $client['client_id']; ?>" 
             data-booking-id="<?= $client['booking_id']; ?>"
            data-booking-date="<?= $client['booking_date']; ?>"
            data-time="<?= $client['preferred_time']; ?>"
            data-service="<?= $client['service_type']; ?>"
        >
            <?= htmlspecialchars($client['client_name']); ?>
        </option>
    <?php endforeach; ?>

</select>



    <label for="serviceType">Service Type</label>
    <select id="serviceType" name="service_type" required>
      <option value="">-- Select Service Type --</option>
       <option value="Elder Care">Elder Care</option>
       <option value="Maid">Maid Service</option>
       <option value="Babysitter">Babysitting</option>
    </select>

    </select>
  

    <label for="dateOfService">Date of Service</label>
    <input type="date" id="dateOfService" name="service_date" >

    <label for="complaintDesc">Complaint Description</label>
    <textarea id="complaintDesc" name="description" placeholder="Describe the issue..." required></textarea>

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
        <?php if (!empty($data['resolvedComplaints'])): ?>
            <?php foreach ($data['resolvedComplaints'] as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['client_name']) ?></td>
                    <td><?= htmlspecialchars($c['service_type']) ?></td>
                    <td><?= htmlspecialchars($c['service_date']) ?></td>
                    <td><?= htmlspecialchars($c['description']) ?></td>
                    <td><?= htmlspecialchars($c['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No resolved complaints yet</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>
<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_complaints.js"></script>
</body>