<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_settings.css">
</head>

<body>
<main class="content">
  <h1>Account Settings</h1>

  <!-- Profile Settings -->
  <section class="settings-section">
    <h2>Profile Information</h2>
    <form id="profileForm">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" value="John Doe" required>
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" value="johndoe@example.com" required>
      </div>
      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" value="+94 77 123 4567" required>
      </div>
      <div class="form-group">
        <label for="address">Address</label>
        <textarea id="address" rows="3">Colombo, Sri Lanka</textarea>
      </div>
      <button type="submit" class="save-btn">Save Changes</button>
    </form>
  </section>

  <!-- Password Settings -->
  <section class="settings-section">
    <h2>Change Password</h2>
    <form id="passwordForm">
      <div class="form-group">
        <label for="currentPassword">Current Password</label>
        <input type="password" id="currentPassword" required>
      </div>
      <div class="form-group">
        <label for="newPassword">New Password</label>
        <input type="password" id="newPassword" required>
      </div>
      <div class="form-group">
        <label for="confirmPassword">Confirm New Password</label>
        <input type="password" id="confirmPassword" required>
      </div>
      <button type="submit" class="save-btn">Update Password</button>
    </form>
  </section>

  <!-- Notifications -->
  <section class="settings-section">
    <h2>Notification Preferences</h2>
    <form id="notificationsForm">
      <div class="form-check">
        <input type="checkbox" id="emailNotifs" checked>
        <label for="emailNotifs">Email Notifications</label>
      </div>
      <div class="form-check">
        <input type="checkbox" id="smsNotifs">
        <label for="smsNotifs">SMS Notifications</label>
      </div>
      <button type="submit" class="save-btn">Save Preferences</button>
    </form>
  </section>
</main>

<script src="<?php echo URLROOT; ?>/public/js/client/c_settings.js"></script>
</body>
</html>
