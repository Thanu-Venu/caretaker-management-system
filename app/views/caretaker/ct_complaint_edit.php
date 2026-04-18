<?php
$caretakerPageTitle = 'Edit Complaint - SmartCare';
$caretakerExtraCss = ['caretaker/ct_complaints.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<main class="content complaint-container">
      <header class="page-header">
        <h1 class="page-title">Edit Complaint</h1>
      </header>

  <form id="complaintEditForm" action="<?= URLROOT ?>/public/index.php?url=caretaker/updateComplaint" method="POST">
    <input type="hidden" name="complaint_id" value="<?= isset($complaint['complaint_id']) ? htmlspecialchars($complaint['complaint_id']) : '' ?>">
    
    <label for="clientName">Client Name</label>
    <input type="text" id="clientName" name="client_name" value="<?= isset($complaint['client_name']) ? htmlspecialchars($complaint['client_name']) : '' ?>" disabled>
    <input type="hidden" name="client_id" value="<?= isset($complaint['client_id']) ? htmlspecialchars($complaint['client_id']) : '' ?>">

    <label for="serviceType">Service Type</label>
    <input type="text" id="serviceType" name="service_type" value="<?= isset($complaint['service_type']) ? htmlspecialchars($complaint['service_type']) : '' ?>" disabled>
  
    <label for="dateOfService">Date of Service</label>
    <input type="date" id="dateOfService" name="service_date" value="<?= isset($complaint['service_date']) ? htmlspecialchars($complaint['service_date']) : '' ?>" disabled>

    <label for="complaintDesc">Complaint Description</label>
    <textarea id="complaintDesc" name="complaint" placeholder="Describe the issue..." required><?= isset($complaint['description']) ? htmlspecialchars($complaint['description']) : '' ?></textarea>

    <div class="form-actions">
        <button type="submit" class="btn-submit">Update Complaint</button>
        <a href="<?= URLROOT ?>/public/index.php?url=caretaker/ct_complaints" class="btn-cancel">Cancel</a>
    </div>
  </form>

</main>


<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
