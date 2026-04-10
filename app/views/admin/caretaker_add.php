<?php
include_once APPROOT . "/views/templates/admin/ad_header.php";
include_once APPROOT . "/views/templates/admin/ad_sidebar.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Caregiver</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/caretaker_add.css">
  <!-- Design System Override -->
</head>

<body>
  <main class="main-content">
    <div class="form-wrapper">
      <h1>Add Caregiver</h1>
      
      <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
          <?php echo htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message">
          <?php echo htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>
      
      <form method="POST" class="caretaker-form" enctype="multipart/form-data" action="<?php echo URLROOT; ?>/CaretakerCRUD/add">

        <div class="form-grid">

          <div class="field">
            <label>Name</label>
            <input type="text" name="name" required placeholder="Enter full name">
          </div>

          <div class="field">
            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter email">
          </div>

          <div class="field">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter password">
          </div>

          <div class="field">
            <label>Phone</label>
            <input type="text" name="phone" required placeholder="Enter phone number">
          </div>

          <div class="field">
            <label>Experience</label>
            <input type="text" name="experience" required placeholder="Enter experience">
          </div>

          <div class="field">
            <label>Location</label>
            <input type="text" name="location" required placeholder="Enter location">
          </div>

          <div class="field full">
            <label>Qualifications</label>
            <input type="text" name="qualifications" required placeholder="Enter qualifications">
          </div>

          <div class="field">
            <label>Profile Picture</label>
            <input type="file" name="profile_image" accept="image/*">
          </div>

          <div class="field">
            <label>Service Type</label>
            <select name="service_type" required>
              <option value="">Select service</option>
              <option value="Elder Care">Elder Care</option>
              <option value="Maid">Maid</option>
              <option value="Babysitter">Babysitter</option>
            </select>
          </div>

          <div class="field">
            <label>Status</label>
            <select name="status" required>
              <option value="Active" selected>Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

        </div>

        <div class="form-actions">
          <button type="submit" class="submit-btn">Add Caregiver</button>
          <a href="<?= URLROOT ?>/CaretakerCRUD/list" class="btn-cancel">Cancel</a>
        </div>

      </form>

    </div>
  </main>
