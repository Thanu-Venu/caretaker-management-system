<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<?php
$user = $data['user'] ?? [];
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
      <div class="profile-body">
        <img id="profileImg" src="<?php echo URLROOT; ?>/public/images/<?= !empty($user['profilePic']) ? htmlspecialchars($user['profilePic']) : 'find.png'; ?>" alt="Profile">
        <div class="pro-section">
          <form method="POST" action="<?php echo URLROOT; ?>/CaretakerProfileController/save" enctype="multipart/form-data">
            <label>Full Name
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name'] ?? ''); ?>" required>
            </label>

            <label>Email
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" readonly>
            </label>

            <label>Phone Number
                <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>" required>
            </label>

            <label>Experience
                <input type="text" name="experience" id="experience" value="<?= htmlspecialchars($user['experience'] ?? ''); ?>" required>
            </label>

            <label>Qualifications
                <input type="text" name="qualifications" id="qualifications" value="<?= htmlspecialchars($user['qualifications'] ?? ''); ?>" required>
            </label>

            <label>Profile Picture
                <input type="file" name="profileFile" id="profileFile" accept="image/*">
            </label>

            <button type="submit" class="btn-save">Save Profile</button>
          </form>
        </div>
      </div>
    </section>

    <!-- Password Settings -->
    <section class="card">
      <h3>Change Password</h3>
      <form id="passwordForm" method="POST" action="<?php echo URLROOT; ?>/CaretakerProfileController/changePassword">
        <label>Current Password
          <input type="password" name="current_password" placeholder="Current password" required>
        </label>
        <label>New Password
          <input type="password" name="new_password" placeholder="New password" required>
        </label>
        <label>Confirm New Password
          <input type="password" name="confirm_password" placeholder="Confirm password" required>
        </label>
        <button type="submit" class="btn-save">Update Password</button>
      </form>
    </section>

    <!-- Notifications -->
    <section class="card">
      <h3>Notification Settings</h3>
      <form id="notificationForm" method="POST" action="<?php echo URLROOT; ?>/CaretakerProfileController/saveNotifications">
        <div class="notif-option">
          <label>Booking Updates
            <input type="checkbox" name="booking_updates" <?= !empty($user['booking_updates']) ? 'checked' : ''; ?>>
          </label>
        </div>
        <div class="notif-option">
          <label>Leave Approval Updates
            <input type="checkbox" name="leave_updates" <?= !empty($user['leave_updates']) ? 'checked' : ''; ?>>
          </label>
        </div>
        <div class="notif-option">
          <label>Promotions / Announcements
            <input type="checkbox" name="promotions" <?= !empty($user['promotions']) ? 'checked' : ''; ?>>
          </label>
        </div>
        <button class="btn-save" type="submit">Save Notification Settings</button>
      </form>
    </section>

  </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_settings.js"></script>
</body>
</html>
