<?php
if (isset($data['user'])) {
  $user = $data['user'];
}

$isProfileRequestPending = !empty($data['latestProfileChangeRequest']) &&
  (($data['latestProfileChangeRequest']['status'] ?? '') === 'Pending');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Settings & Profile</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_settings.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/admin-ui.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_sidebar.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>
  <div class="main-content">
    <section class="page-header settings-page-header">
      <h1 class="page-title" style="color: #1e88e5; font-size: 28px; font-weight: 600; margin-bottom: 20px;">Profile &amp; Settings</h1>
    </section>

    <?php if (!empty($data['latestProfileChangeRequest'])): 
        $status = $data['latestProfileChangeRequest']['status'] ?? '';
        $cssClass = '';
        if ($status === 'Approved') $cssClass = 'success';
        elseif ($status === 'Deleted' || $status === 'Rejected') $cssClass = 'error';
        elseif ($status === 'Pending') $cssClass = 'pending';
        
        $bgClass = $status === 'Pending' ? 'background: #fff3cd; color: #856404; border: 1px solid #ffeeba;' : ($cssClass === 'success' ? 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;');
    ?>
      <div class="flash-message <?= $cssClass ?>" style="<?= $bgClass ?>">
        Latest profile update request status: <?= htmlspecialchars($status) ?>
      </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="flash-message success" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="flash-message error" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="settings-container">

      <!-- Profile Picture & Info -->
      <section class="card profile">
        <h3>Profile Details</h3>
        <div class="profile-body">
            <img
            src="<?= URLROOT ?>/public/uploads/<?= $user['profile_image'] ?: 'default.jpg' ?>"
            alt="Profile"
            onerror="this.src='<?= URLROOT ?>/public/uploads/default.jpg';">
            
            <form id="edit-details-form" action="<?= URLROOT ?>/index.php?url=Caretaker/editCaretakerDetails" method="post" enctype="multipart/form-data">
            <div class="pro-section">
                <div class="field">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Sarah Johnson" value="<?= htmlspecialchars($user['name']); ?>" <?= $isProfileRequestPending ? 'readonly' : '' ?>>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" <?= $isProfileRequestPending ? 'readonly' : '' ?>>
                </div>

                <div class="field">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" <?= $isProfileRequestPending ? 'readonly' : '' ?>>
                </div>

                <div class="field">
                    <label for="experience">Experience</label>
                    <input type="text" id="experience" name="experience" value="<?= htmlspecialchars($user['experience'] ?? ''); ?>" <?= $isProfileRequestPending ? 'readonly' : '' ?>>
                </div>

                <div class="field">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?= htmlspecialchars($user['location'] ?? ''); ?>" <?= $isProfileRequestPending ? 'readonly' : '' ?>>
                </div>

                <div class="field">
                    <label for="qualifications">Qualifications</label>
                    <input type="text" id="qualifications" name="qualifications" value="<?= htmlspecialchars($user['qualifications'] ?? ''); ?>" <?= $isProfileRequestPending ? 'readonly' : '' ?>>
                </div>

                <div class="form-actions">
                    <button id="saveProfile" type="submit" form="edit-details-form" class="btn-save" <?= $isProfileRequestPending ? 'disabled' : '' ?>>
                        <?= $isProfileRequestPending ? 'Request Pending' : 'Send Update Request' ?>
                    </button>
                </div>
            </div>
            </form>
        </div>
      </section>

      <!-- Password Settings -->
      <section class="card">
        <h3>Change Password</h3>
        <form id="passwordForm"
          action="<?= URLROOT ?>/index.php?url=Caretaker/editPasswordDetails"
          method="post">
          <div class="field">
              <label for="current-password">Current Password</label>
              <input type="password" id="current-password" name="current-password" placeholder="Current password" required>
          </div>
          <div class="field">
              <label for="new-password">New Password</label>
              <input type="password" id="new-password" name="new-password" placeholder="New password" required>
          </div>
          <div class="field">
              <label for="confirm-password">Confirm New Password</label>
              <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm password" required>
          </div>
          
          <div class="form-actions">
              <button type="submit" class="btn-save">Update Password</button>
          </div>
        </form>
      </section>

    </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_setting.js"></script>
</body>

</html>