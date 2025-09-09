<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

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
      <div class="profile-body">
        <img id="profileImg" src="<?php echo URLROOT; ?>/public/images/find.png" alt="Profile">
        <div class="pro-section">
          <label>Full Name
            <input type="text" id="name" placeholder="Sarah Johnson" required>
          </label><br>
          <label>Email
            <input type="email" id="email" placeholder="sarah@example.com" required>
          </label><br>
          <label>Phone Number
            <input type="text" id="phone" placeholder="+94 712345678" required>
          </label><br>
          <label>Experience
            <input type="text" id="experience" placeholder="8 years" required>
          </label><br>
          <label>Qualifications
            <input type="text" id="qualifications" placeholder="Certified Elder Care Specialist" required>
          </label><br>
          <label>Profile Picture
            <input type="file" id="profileFile" accept="image/*">
          </label><br><br>
          <button id="saveProfile" class="btn-save">Save Profile</button>
        </div>
      </div>
    </section>

    <!-- Password Settings -->
    <section class="card">
      <h3>Change Password</h3>
      <form id="passwordForm">
        <label>Current Password
          <input type="password" placeholder="Current password" required>
        </label>
        <label>New Password
          <input type="password" placeholder="New password" required>
        </label>
        <label>Confirm New Password
          <input type="password" placeholder="Confirm password" required>
        </label>
        <button type="submit" class="btn-save">Update Password</button>
      </form>
    </section>

    <!-- Notifications -->
    <section class="card">
      <h3>Notification Settings</h3>
      <div class="notif-option">
        <label>Booking Updates
          <input type="checkbox" checked>
        </label>
      </div>
      <div class="notif-option">
        <label>Leave Approval Updates
          <input type="checkbox" checked>
        </label>
      </div>
      <div class="notif-option">
        <label>Promotions / Announcements
          <input type="checkbox">
        </label>
      </div>
      <button class="btn-save">Save Notification Settings</button>
    </section>

  </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/client/c_settings.js"></script>
</body>
</html>
