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
    
    <label for="clientName">Select Client</label>
   <select id="clientName" name="client_id" required>
    <option value="">-- Select Client --</option>

    <?php if (isset($clients) && is_array($clients)): ?>
        <?php foreach ($clients as $client): ?>
            <option 
                value="<?= $client['client_id']; ?>" 
                data-booking-id="<?= $client['booking_id']; ?>"
                data-booking-date="<?= $client['booking_date']; ?>"
                data-time="<?= $client['preferred_time']; ?>"
                data-service="<?= $client['service_type']; ?>"
                <?= isset($complaint['client_id']) && $complaint['client_id'] == $client['client_id'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($client['client_name']); ?>
            </option>
        <?php endforeach; ?>
    <?php endif; ?>

</select>

    <label for="serviceType">Service Type</label>
    <select id="serviceType" name="service_type" required>
      <option value="">-- Select Service Type --</option>
       <option value="Elder Care" <?= isset($complaint['service_type']) && $complaint['service_type'] == 'Elder Care' ? 'selected' : '' ?>>Elder Care</option>
       <option value="Maid" <?= isset($complaint['service_type']) && $complaint['service_type'] == 'Maid' ? 'selected' : '' ?>>Maid Service</option>
       <option value="Babysitter" <?= isset($complaint['service_type']) && $complaint['service_type'] == 'Babysitter' ? 'selected' : '' ?>>Babysitting</option>
    </select>
  
    <label for="dateOfService">Date of Service</label>
    <input type="date" id="dateOfService" name="service_date" value="<?= isset($complaint['service_date']) ? htmlspecialchars($complaint['service_date']) : '' ?>" required>

    <label for="complaintDesc">Complaint Description</label>
    <textarea id="complaintDesc" name="description" placeholder="Describe the issue..." required><?= isset($complaint['description']) ? htmlspecialchars($complaint['description']) : '' ?></textarea>

    <div class="form-actions">
        <button type="submit" class="btn-submit">Update Complaint</button>
        <a href="<?= URLROOT ?>/public/index.php?url=caretaker/ct_complaints" class="btn-cancel">Cancel</a>
    </div>
  </form>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clientSelect = document.getElementById('clientName');
    const serviceTypeSelect = document.getElementById('serviceType');
    const dateOfServiceInput = document.getElementById('dateOfService');
    
    clientSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const serviceType = selectedOption.getAttribute('data-service');
        const bookingDate = selectedOption.getAttribute('data-booking-date');
        const bookingTime = selectedOption.getAttribute('data-time');
        
        // Auto-fill service type
        if (serviceType) {
            serviceTypeSelect.value = serviceType;
        }
        
        // Auto-fill date of service
        if (bookingDate) {
            dateOfServiceInput.value = bookingDate;
        }
    });
});
</script>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
