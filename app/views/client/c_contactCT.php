<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Your Caregiver</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_contactCT.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <div class="main-content">
    <div class="page-header">
      <h1><i class="fas fa-user-nurse"></i> Contact Your Caregiver</h1>
      <p class="subtitle">Get in touch with your assigned caregiver</p>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span><?php echo $_SESSION['error'];
              unset($_SESSION['error']); ?></span>
      </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span><?php echo $_SESSION['success'];
              unset($_SESSION['success']); ?></span>
      </div>
    <?php endif; ?>

    <?php $caretaker = $data['caretaker'] ?? null; ?>
    <?php if ($caretaker): ?>
      <div class="caretaker-profile">
        <div class="profile-header">
          <div class="avatar-wrapper">
            <img src="<?php echo URLROOT; ?>/public/images/find.png" alt="Caretaker" class="profile-avatar">
            <div class="status-badge">
              <i class="fas fa-circle"></i> Available
            </div>
          </div>
          <div class="profile-info">
            <h2 class="profile-name"><?= htmlspecialchars($caretaker['name'] ?? 'Caretaker') ?></h2>
            <div class="service-badge">
              <i class="fas fa-briefcase-medical"></i>
              <?= htmlspecialchars($caretaker['service_type'] ?? 'N/A') ?>
            </div>
            <?php if (!empty($caretaker['location'])): ?>
              <div class="location-info">
                <i class="fas fa-map-marker-alt"></i>
                <?= htmlspecialchars($caretaker['location']) ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="contact-section">
          <h3><i class="fas fa-address-card"></i> Contact Information</h3>
          <div class="contact-grid">
            <div class="contact-card">
              <div class="contact-icon phone-icon">
                <i class="fas fa-phone-alt"></i>
              </div>
              <div class="contact-details">
                <label>Phone Number</label>
                <a href="tel:<?= htmlspecialchars($caretaker['phone'] ?? '') ?>" class="contact-value">
                  <?= htmlspecialchars($caretaker['phone'] ?? 'N/A') ?>
                </a>
              </div>
            </div>

            <div class="contact-card">
              <div class="contact-icon email-icon">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="contact-details">
                <label>Email Address</label>
                <a href="mailto:<?= htmlspecialchars($caretaker['email'] ?? '') ?>" class="contact-value">
                  <?= htmlspecialchars($caretaker['email'] ?? 'N/A') ?>
                </a>
              </div>
            </div>
          </div>
        </div>

       

        <div class="navigation-section">
          <a href="<?= URLROOT ?>/client/c_dashboard" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
          </a>
          <a href="<?= URLROOT ?>/client/c_upcomingBookings" class="btn-bookings">
            <i class="fas fa-calendar-alt"></i> View Bookings
          </a>
        </div>
      </div>
    <?php else: ?>
      <div class="no-caretaker">
        <div class="empty-state">
          <i class="fas fa-user-slash"></i>
          <h3>No Caregiver Details Available</h3>
          <p>After the HR manager approves the payment, you will be able to view the caretaker details..</p>
          <a href="<?= URLROOT ?>/client/c_upcomingBookings" class="btn-primary">
            <i class="fas fa-calendar-check"></i> View My Bookings
          </a>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/client/c_contactCT.js"></script>
</body>

</html>