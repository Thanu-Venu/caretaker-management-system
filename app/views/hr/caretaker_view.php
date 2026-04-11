<?php  
include_once APPROOT . "/views/templates/hr/hr_header.php"; 
include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caregiver Details</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/caretaker_view.css">
</head>
<body>
<main class="main-content">
  <div class="view-container">
    <div class="view-header">
      <h1><?= htmlspecialchars($data['caretaker']['name'] ?? 'Caregiver') ?></h1>
      <span class="status-badge <?= ($data['caretaker']['status'] ?? '') === 'Active' ? 'active' : 'inactive' ?>">
        <?= htmlspecialchars($data['caretaker']['status'] ?? 'Unknown') ?>
      </span>
    </div>

    <div class="profile-section">
      <div class="profile-image">
        <?php 
          $profileImage = $data['caretaker']['profile_image'] ?? null;
          // Show profile image if it exists and it's not the default
          if ($profileImage && $profileImage !== 'default.png'): 
            $imagePath = URLROOT . '/public/uploads/' . $profileImage;
        ?>
          <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($data['caretaker']['name'] ?? 'Profile') ?>">
        <?php else: ?>
          <!-- Placeholder when no profile picture is uploaded -->
          <div class="profile-placeholder">
            <i class="bx bx-user"></i>
            <p>No Profile Picture</p>
          </div>
        <?php endif; ?>
      </div>

      <div class="profile-info">
        <div class="info-grid">
          <div class="info-item">
            <div class="info-label">Full Name</div>
            <div class="info-value"><?= htmlspecialchars($data['caretaker']['name'] ?? 'N/A') ?></div>
          </div>

          <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value"><?= htmlspecialchars($data['caretaker']['email'] ?? 'N/A') ?></div>
          </div>

          <div class="info-item">
            <div class="info-label">Phone</div>
            <div class="info-value"><?= htmlspecialchars($data['caretaker']['phone'] ?? 'N/A') ?></div>
          </div>

          <div class="info-item">
            <div class="info-label">Service Type</div>
            <div class="info-value"><?= htmlspecialchars($data['caretaker']['service_type'] ?? 'N/A') ?></div>
          </div>

          <div class="info-item">
            <div class="info-label">Location</div>
            <div class="info-value"><?= htmlspecialchars($data['caretaker']['location'] ?? 'N/A') ?></div>
          </div>

          <div class="info-item">
            <div class="info-label">Experience</div>
            <div class="info-value"><?= htmlspecialchars($data['caretaker']['experience'] ?? 'N/A') ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="details-section">
      <h2 class="section-title">Additional Details</h2>
      <div class="details-grid">
        <div class="info-item">
          <div class="info-label">Qualifications</div>
          <div class="info-value"><?= htmlspecialchars($data['caretaker']['qualifications'] ?? 'N/A') ?></div>
        </div>

        <div class="info-item">
          <div class="info-label">Member Since</div>
          <div class="info-value"><?= htmlspecialchars($data['caretaker']['created_at'] ?? 'N/A') ?></div>
        </div>
      </div>
    </div>

    <p class="caretaker-view-back">
      <a href="<?= URLROOT ?>/HRCaretakerCRUD/list">← Back to caregivers list</a>
    </p>
  </div>
</main>

</body>
</html>
