<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>
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
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_settings.css">
</head>
<body>
<div class="main-content">
  <h1>Profile & Settings</h1>

  <div class="settings-container">

    <!-- Profile Picture & Info -->
    <section class="card profile">
      <h3>Profile Details</h3>
      <form id="edit-details-form" action="<?= URLROOT ?>/index.php?url=Caretaker/editCaretakerDetails" method="post" enctype="multipart/form-data" >
      <div class="profile-body">
      <img id="profileImg" 
     src="<?= URLROOT ?>/public/uploads/<?= $user['profile_image'] ?? 'default.png' ?>" 
     alt="Profile">

        <div class="pro-section">
          <label>Full Name
            <input type="text" id="name" name="name" placeholder="Sarah Johnson"  value="<?= htmlspecialchars($user['name']); ?>">
          </label><br>
          <label>Email
            <input type="email" id="email" name="email"  value="<?= htmlspecialchars($user['email']); ?>">
          </label><br>
          <label>Phone Number
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" placeholder="+94 712345678" >
          </label><br>
          <label>Experience
              <input type="text" id="experience" name="experience"
                    value="<?= htmlspecialchars($user['experience'] ?? ''); ?>"
                    placeholder="8 years">
            </label>

            <label>Qualifications
              <input type="text" id="qualifications" name="qualifications"
                    value="<?= htmlspecialchars($user['qualifications'] ?? ''); ?>"
                    placeholder="Certified Elder Care Specialist">
            </label><br>
          <label>Profile Picture
            <input type="file" id="profileFile" name="profile_image" accept="image/*">
          </label><br><br>
          <button id="saveProfile" type="submit" form="edit-details-form" class="btn-save">Save Profile</button>
        </div>
      </div>
      </form>
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

<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_setting.js"></script>
</body>
</html>
