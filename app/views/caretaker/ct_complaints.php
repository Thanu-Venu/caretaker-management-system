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
$complaintFilters = (isset($data['filters']) && is_array($data['filters'])) ? $data['filters'] : [];
$complaintServiceOptions = (isset($data['serviceTypeOptions']) && is_array($data['serviceTypeOptions'])) ? $data['serviceTypeOptions'] : [];
$complaintStatusOptions = (isset($data['statusOptions']) && is_array($data['statusOptions'])) ? $data['statusOptions'] : [];
$selectedComplaintService = trim((string) ($complaintFilters['service_type'] ?? ''));
$selectedComplaintStatus = trim((string) ($complaintFilters['status'] ?? ''));
?>
<main class="content complaint-container">
      <header class="page-header">
        <h1 class="page-title">Register a Complaint</h1>
      </header>

  <form id="complaintForm" action="<?php echo URLROOT; ?>/caretaker/saveComplaint" method="POST">
    <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($data['form_token'] ?? ''); ?>">
    <label for="clientName">Select Client</label>
   <select id="clientName" name="client_id" required>
    <option value="">-- Select Client --</option>

    <?php foreach ($data['clients'] as $client): ?>
        <option 
            value="<?= $client['client_id']; ?>" 
             data-booking-id="<?= $client['booking_id']; ?>"
            data-booking-date="<?= $client['booking_date']; ?>"
            data-time="<?= $client['preferred_time']; ?>"
            data-service="<?= htmlspecialchars($client['service_type']); ?>"
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
    <h2 class="complaints-past-heading">Past Complaints</h2>
    <form class="filter-section filters-inline ct-page-filters" method="get" action="<?= htmlspecialchars(URLROOT . '/public', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="url" value="caretaker/ct_complaints">
        <div class="filter-group">
            <label for="complaintStatusFilter">Status</label>
            <select id="complaintStatusFilter" name="complaint_status">
                <option value="">All statuses</option>
                <?php foreach ($complaintStatusOptions as $status): ?>
                    <option value="<?= htmlspecialchars((string) $status, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($selectedComplaintStatus, (string) $status) === 0 ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $status, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="complaintServiceFilter">Service</label>
            <select id="complaintServiceFilter" name="complaint_service">
                <option value="">All services</option>
                <?php foreach ($complaintServiceOptions as $service): ?>
                    <option value="<?= htmlspecialchars((string) $service, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($selectedComplaintService, (string) $service) === 0 ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $service, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group filter-group--actions">
            <button type="submit" class="btn primary">Apply</button>
            <a class="btn ghost" href="<?= htmlspecialchars(URLROOT . '/public?url=caretaker/ct_complaints', ENT_QUOTES, 'UTF-8') ?>">Reset</a>
        </div>
    </form>
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

<!-- Success Popup -->
<div id="successPopup" class="complaint-popup" style="display: none;">
    <div class="complaint-popup__card">
        <h3 class="complaint-popup__title">Success!</h3>
        <p class="complaint-popup__message">Your complaint has been submitted successfully.</p>
        <button class="complaint-popup__btn btn primary" onclick="closeSuccessPopup()">OK</button>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_complaints.js"></script>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
