<?php
include_once APPROOT . "/views/templates/admin/ad_header.php";
include_once APPROOT . "/views/templates/admin/ad_sidebar.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Add User</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/user_add.css">
  <!-- Design System Override -->
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/system/legacy-overrides.css">
</head>

<body>
  <main class="main-content">
    <div class="form-wrapper">
      <h1>Add User</h1>
      <form method="POST" class="user-form">

        <div class="form-grid">

          <div class="field">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Enter username">
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
            <label>Role</label>
            <select name="role" required>
              <option value="">Select Role</option>
              <option value="Admin">Admin</option>
              <option value="Manager">Manager</option>
            </select>
          </div>

          <div class="field">
            <label>Phone</label>
            <input type="text" name="phone" required placeholder="Enter phone number">
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
          <button type="submit" class="submit-btn">Add User</button>
          <a href="<?= URLROOT ?>/UserCRUD/list" class="btn-cancel">Cancel</a>
        </div>

      </form>

    </div>
  </main>
</body>

</html>