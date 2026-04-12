<?php  
// unpack the data array into $caretaker so you can use it easily
$caretaker = $data['caretaker'];

$hrPageTitle = 'Edit caregiver — HR';
$hrExtraCss  = ['hr/caretaker_edit.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main class="main-content">
  <section class="form-section">
    <h1>Edit Caregiver</h1>
    
    <!-- Error Messages Container -->
    <div id="errorContainer"></div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <strong>Error:</strong> <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message">
            <strong>Success:</strong> <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <form action="<?php echo URLROOT; ?>/HRCaretakerCRUD/edit/<?php echo $caretaker['id']; ?>" 
      method="POST" enctype="multipart/form-data" class="caretaker-form">

  <div class="form-grid">

    <div class="field">
      <label>Name</label>
      <input type="text" name="name"
        value="<?= htmlspecialchars($caretaker['name']); ?>" required>
    </div>

    <div class="field">
      <label>Email</label>
      <input type="email" name="email"
        value="<?= htmlspecialchars($caretaker['email']); ?>" required>
    </div>

    <div class="field">
      <label>Phone</label>
      <input type="text" name="phone"
        value="<?= htmlspecialchars($caretaker['phone']); ?>" required>
    </div>

    <div class="field">
      <label>Experience</label>
      <input type="text" name="experience"
        value="<?= htmlspecialchars($caretaker['experience']); ?>" required>
    </div>

    <div class="field">
      <label>Location</label>
      <input type="text" name="location"
        value="<?= htmlspecialchars($caretaker['location']); ?>" required>
    </div>

    <div class="field full">
      <label>Qualifications</label>
      <input type="text" name="qualifications"
        value="<?= htmlspecialchars($caretaker['qualifications']); ?>" required>
    </div>

    <div class="field">
      <label>Profile Picture</label>
      <input type="file" name="profile_image" accept="image/*">
    </div>

    <div class="field">
      <label>Service Type</label>
      <select name="service_type" required>
        <option value="Elder Care" <?= $caretaker['service_type']=='Elder Care' ? 'selected' : '' ?>>Elder Care</option>
        <option value="Maid" <?= $caretaker['service_type']=='Maid' ? 'selected' : '' ?>>Maid</option>
        <option value="Babysitter" <?= $caretaker['service_type']=='Babysitter' ? 'selected' : '' ?>>Babysitter</option>
      </select>
    </div>

    <div class="field">
      <label>Status</label>
      <select name="status" required>
        <option value="Active" <?= $caretaker['status']=='Active' ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= $caretaker['status']=='Inactive' ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>

  </div>

  <div class="form-actions">
    <button type="submit" class="submit-btn btn">Update Caregiver</button>
    <a href="<?= URLROOT ?>/HRCaretakerCRUD/list" class="btn-cancel btn">Cancel</a>
  </div>

</form>

  </section>
</main>
<script src="<?php echo URLROOT; ?>/public/js/hr/caretaker_form.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
