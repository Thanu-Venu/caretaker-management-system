<?php  
include_once APPROOT . "/views/templates/hr/hr_header.php"; 
include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HR dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/caretaker_add.css">
</head>

<body>
<script src="<?php echo URLROOT; ?>/public/js/hr/caretaker_form.js"></script>
<main class="main-content">
    <section class="form-section">
        <h1>Add Caregiver</h1>
        
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

        <form method="POST" class="caretaker-form" enctype="multipart/form-data">

  <div class="form-grid">

    <div class="field">
      <label>Name <span style="color: red;">*</span></label>
      <input type="text" name="name" required placeholder="Enter full name">
    </div>

    <div class="field">
      <label>Email <span style="color: red;">*</span></label>
      <input type="email" name="email" required placeholder="Enter email (e.g. abc@gmail.com)">
    </div>

    <div class="field">
      <label>Password <span style="color: red;">*</span></label>
      <input type="password" name="password" required placeholder="Enter password (min 6 characters)">
    </div>

    <div class="field">
      <label>Phone <span style="color: red;">*</span></label>
      <input type="text" name="phone" required placeholder="Enter phone number (e.g. +94771234567 or 0771234567)">
    </div>

    <div class="field">
      <label>Experience <span style="color: red;">*</span></label>
      <input type="text" name="experience" required placeholder="Enter years of experience">
    </div>

    <div class="field">
      <label>Location <span style="color: red;">*</span></label>
      <input type="text" name="location" required placeholder="Enter location">
    </div>

    <div class="field full">
      <label>Qualifications <span style="color: red;">*</span></label>
      <input type="text" name="qualifications" required placeholder="Enter qualifications">
    </div>

    <div class="field">
      <label>Profile Picture</label>
      <input type="file" name="profile_image" accept="image/*">
    </div>

    <div class="field">
      <label>Service Type <span style="color: red;">*</span></label>
      <select name="service_type" required>
        <option value="">Select service</option>
        <option value="Elder Care">Elder Care</option>
        <option value="Maid">Maid</option>
        <option value="Babysitter">Babysitter</option>
      </select>
    </div>

    <div class="field">
      <label>Status</label>
      <select name="status">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>
    </div>

  </div>

  <div class="form-actions">
    <button type="submit" class="submit-btn">Add Caregiver</button>
    <a href="<?= URLROOT ?>/HRCaretakerCRUD/list" class="btn-cancel">Cancel</a>
  </div>

</form>

    </section>
</main>

</form>

    </section>
</main>
</body>

</html>