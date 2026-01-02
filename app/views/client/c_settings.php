<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
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
  <title>Caretaker Settings & Profile</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_settings.css">
</head>

<body>
  <div class="main-content">
    <h1>Profile & Settings</h1>

    <div class="settings-container">

      <!-- Profile Picture & Info -->
      <section class="card profile">
        <h3>Profile Details</h3>
        <form id="edit-details-form" action="<?= URLROOT ?>/index.php?url=Client/editClientDetails" method="POST">

          <div class="profile-body">
            <img id="profileImg" src="<?php echo URLROOT; ?>/public/images/client.png" alt="Profile">
            <div class="pro-section">
              <label>Full Name
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']); ?>" required>
              </label><br>
              <label>Email
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']); ?>" required>
              </label><br>
              <label>Phone Number
                <input type="text" name="phone" id="phone" placeholder="+94 712345678"
                  value="<?= htmlspecialchars($user['phone']); ?>" required>
              </label><br>
              <label>Profile Picture
                <input type="file" id="profileFile" accept="image/*">
              </label><br><br>
              <button id="saveProfile" type="submit" form="edit-details-form" class="btn-save">Save Profile</button>
            </div>

          </div>
        </form>

      </section>

      <!-- Password Settings -->
      <section class="card">
        <h3>Change Password</h3>
        <form id="edit-password-form" action="<?= URLROOT ?>/index.php?url=Client/editPasswordDetails" method="POST">
          
          <label>New Password
            <input type="password" name="new-password" placeholder="New password" required>
          </label>
          <label>Confirm New Password
            <input type="password" name="confirm-password" placeholder="Confirm password" required>
          </label>
          <button type="submit" form="edit-password-form" class="btn-save">Update Password</button>
        </form>
      </section>

      <!-- Notifications -->
      <section class="card">
        <h3>Notification Settings</h3>
        <div class="notif-option">
          <label>Booking Details
            <input type="checkbox" checked>
          </label>
        </div>
        <div class="notif-option">
          <label>Payment Details
            <input type="checkbox" checked>
          </label>
        </div>
        <div class="notif-option">
          <label>Announcements
            <input type="checkbox">
          </label>
        </div>
        <button type="submit" form="edit-details-form" class="btn-save">Save Notification Settings</button>
      </section>

    </div>
  </div>


  <script src="<?php echo URLROOT; ?>/public/js/client/c_settings.js"></script>
</body>

</html>