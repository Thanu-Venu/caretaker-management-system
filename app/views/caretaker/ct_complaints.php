<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare Dashboard</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_complaints.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/admin-ui.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_sidebar.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>
<?php
    $complaintSuccessMessage = isset($_SESSION['success']) ? (string)$_SESSION['success'] : '';
    unset($_SESSION['success'], $_SESSION['error']);
?>

<?php if ($complaintSuccessMessage): ?>
  <div id="successMessage" class="success-message" style="background-color: #e3f2fd; color: #1565c0; padding: 12px 20px; margin: 0 auto 16px auto; border-radius: 4px; font-weight: 500; width: fit-content; border-left: 4px solid #1e88e5;">
    <?= htmlspecialchars($complaintSuccessMessage) ?> HR will take action about the complaint.
  </div>
<?php endif; ?>

  <main class="content complaint-container">
      <header class="page-header" style="margin-bottom: 24px; margin-top: -16px;">
        <h1 class="page-title" style="color: #1e88e5; font-size: 30px; font-weight: 700; margin: 0; letter-spacing: -0.02em;">Register a Complaint</h1>
      </header>

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


    <label for="dateOfService">Date of Service</label>
    <input type="date" id="dateOfService" name="service_date" >

    <label for="complaintDesc">Complaint Description</label>
    <textarea id="complaintDesc" name="description" placeholder="Describe the issue..." required></textarea>

    <button type="submit" class="btn-submit">Submit Complaint</button>
  </form>

  <div class="card">
    <h2 class="page-title" style="color: #1e88e5; font-size: 24px; font-weight: 600; margin-bottom: 20px;">Past Complaints</h2>
    <div class="table-container">
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
                        <td>
                            <?php
                                $statusClass = 'status';
                                if ($c['status'] == 'Pending' || $c['status'] == 'Open') $statusClass .= ' pending';
                                elseif ($c['status'] == 'Resolved' || $c['status'] == 'Closed') $statusClass .= ' resolved';
                                elseif ($c['status'] == 'Rejected') $statusClass .= ' rejected';
                                elseif ($c['status'] == 'InProgress' || $c['status'] == 'In Progress') $statusClass .= ' InProgress';
                            ?>
                            <span class="<?= $statusClass ?>"><?= htmlspecialchars($c['status']) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--complaint-muted); padding: 24px;">No past complaints yet</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
  </div>

</main>
<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_complaints.js"></script>
</body>
</html>