<?php  include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php  include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>
<?php
if (isset($data['user'])) {
    $user = $data['user'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_settings.css">
</head>
<body>
    <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings</title>
  <link rel="stylesheet" href="settings.css">
</head>
<body>
  <div class="settings-container">

    <!-- Page Header -->
    <div class="settings-header">
      <h2>Account Settings</h2>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <button class="tab-link active" onclick="openTab(event, 'profile')">Profile</button>
      <button class="tab-link" onclick="openTab(event, 'password')">Password</button>
    </div>

    <!-- Profile Settings -->
    <div id="profile" class="tab-content active">
      <form id="profileForm">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="fullname" placeholder="Enter full name">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="Enter email">
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" name="phone" placeholder="Enter phone number">
        </div>
        <div class="form-group">
          <label>Profile Picture</label>
          <input type="file" name="profilePic" accept="image/*">
        </div>
        <button type="submit" class="btn">Save Changes</button>
      </form>
    </div>

    <!-- Password Settings -->
    <div id="password" class="tab-content">
      <form id="passwordForm">
        <div class="form-group">
          <label>Current Password</label>
          <input type="password" name="currentPassword" placeholder="Enter current password">
        </div>
        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="newPassword" placeholder="Enter new password">
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="confirmPassword" placeholder="Confirm new password">
        </div>
        <button type="submit" class="btn">Change Password</button>
      </form>
    </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/admin/ad_settings.js"></script>
</body>
</html>