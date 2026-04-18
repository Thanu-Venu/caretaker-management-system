<?php
$clientPageTitle = 'Temporary caregiver contact — SmartCare';
$clientExtraCss  = ['client/c_contactCT.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$detail  = $data['detail'] ?? [];
$booking = $data['booking'] ?? [];
$bid     = (int) ($detail['booking_id'] ?? $booking['booking_id'] ?? 0);
$imgName = trim((string) ($detail['profile_image'] ?? ''));
$avatarSrc = $imgName !== ''
    ? URLROOT . '/public/uploads/' . htmlspecialchars($imgName, ENT_QUOTES, 'UTF-8')
    : URLROOT . '/public/images/find.png';
?>
  <main class="main-content">
    <div class="page-header">
      <h1><i class="fas fa-user-clock"></i> Your temporary caregiver</h1>
      <p class="subtitle">Contact details for the caregiver covering while your usual caregiver is on leave<?= $bid > 0 ? ' (booking #' . $bid . ')' : '' ?>.</p>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span><?php echo $_SESSION['error'];
              unset($_SESSION['error']); ?></span>
      </div>
    <?php endif; ?>

    <div class="caretaker-profile">
      <div class="profile-header">
        <div class="avatar-wrapper">
          <img src="<?= $avatarSrc ?>" alt="" class="profile-avatar">
          <div class="status-badge">
            <i class="fas fa-circle"></i> Cover period
          </div>
        </div>
        <div class="profile-info">
          <h2 class="profile-name"><?= htmlspecialchars((string) ($detail['name'] ?? 'Caregiver')) ?></h2>
          <div class="service-badge">
            <i class="fas fa-briefcase-medical"></i>
            <?= htmlspecialchars((string) ($detail['service_type'] ?? 'N/A')) ?>
          </div>
          <?php if (!empty($detail['location'])): ?>
            <div class="location-info">
              <i class="fas fa-map-marker-alt"></i>
              <?= htmlspecialchars((string) $detail['location']) ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="payment-note" style="margin-top: 1rem;">
        <i class="fas fa-info-circle"></i>
        <p>
          <strong>Cover dates:</strong>
          <?= htmlspecialchars((string) ($detail['cover_start_date'] ?? '')) ?>
          — <?= htmlspecialchars((string) ($detail['cover_end_date'] ?? '')) ?>.
          <?php if (!empty($detail['previous_caretaker_name'])): ?>
            Your usual caregiver (<strong><?= htmlspecialchars((string) $detail['previous_caretaker_name']) ?></strong>) remains assigned to your booking outside these dates.
          <?php endif; ?>
        </p>
        <?php
        $hrNote = trim((string) ($detail['hr_note'] ?? ''));
        if ($hrNote !== ''):
        ?>
          <p style="margin-top:0.5rem;"><strong>Note from our team:</strong> <?= nl2br(htmlspecialchars($hrNote)) ?></p>
        <?php endif; ?>
      </div>

      <div class="contact-section">
        <h3><i class="fas fa-address-card"></i> Contact information</h3>
        <div class="contact-grid">
          <div class="contact-card">
            <div class="contact-icon phone-icon">
              <i class="fas fa-phone-alt"></i>
            </div>
            <div class="contact-details">
              <label>Phone</label>
              <a href="tel:<?= htmlspecialchars((string) ($detail['phone'] ?? '')) ?>" class="contact-value">
                <?= htmlspecialchars((string) ($detail['phone'] ?? 'N/A')) ?>
              </a>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-icon email-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="contact-details">
              <label>Email</label>
              <a href="mailto:<?= htmlspecialchars((string) ($detail['email'] ?? '')) ?>" class="contact-value">
                <?= htmlspecialchars((string) ($detail['email'] ?? 'N/A')) ?>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="navigation-section">
        <a href="<?= URLROOT ?>/client/c_dashboard" class="btn-back">
          <i class="fas fa-arrow-left"></i> Back to dashboard
        </a>
        <a href="<?= URLROOT ?>/client/c_myBookings" class="btn-bookings">
          <i class="fas fa-calendar-alt"></i> My bookings
        </a>
      </div>
    </div>
  </main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
