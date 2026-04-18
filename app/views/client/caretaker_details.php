<?php
$clientPageTitle = 'Your Caregiver Has Been Changed';
$clientExtraCss = ['client/caretaker_details.css'];
include_once APPROOT . '/views/templates/client/client_layout_head.php';
include_once APPROOT . '/views/templates/client/c_header.php';
include_once APPROOT . '/views/templates/client/c_sidebar.php';
?>

<div class="page-wrapper">
    <main class="main-content">
        <!-- Alert Section -->
        <div class="alert-banner">
            <div class="alert-content">
                <div class="alert-icon">
                    <i class="bx bx-info-circle"></i>
                </div>
                <div class="alert-text">
                    <h1 class="alert-title">Your Caregiver Has Been Changed</h1>
                    <p class="alert-message">
                        Your caregiver has been reassigned for your service. 
                        <span class="highlight">New caregiver: <?= htmlspecialchars($data['caretaker']['name'] ?? 'Lakshmi Murugan') ?></span>
                        <br>
                        <span class="service-period">Service period: <?= htmlspecialchars($data['service_period']['start_date'] ?? '2026-04-23') ?> to <?= htmlspecialchars($data['service_period']['end_date'] ?? '2026-04-23') ?></span>
                        <br>
                        <span class="assurance">Your service will continue uninterrupted.</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Caretaker Profile Card -->
        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar-section">
                        <div class="avatar-wrapper">
                            <img src="<?= URLROOT ?>/public/images/profiles/<?= htmlspecialchars($data['caretaker']['profile_image'] ?? 'default.png') ?>" 
                                 alt="Caretaker Profile" class="avatar-image">
                            <div class="rating-indicator">
                                <i class="bx bx-star"></i>
                                <span><?= number_format((float)($data['caretaker']['rating'] ?? 4.5), 1) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="profile-info-section">
                        <div class="profile-name">
                            <h2><?= htmlspecialchars($data['caretaker']['name'] ?? 'Lakshmi Murugan') ?></h2>
                            <span class="badge active">Available</span>
                        </div>
                        <div class="profile-details">
                            <div class="detail-item">
                                <i class="bx bx-briefcase"></i>
                                <span><?= htmlspecialchars($data['caretaker']['service_type'] ?? 'Elder Care') ?> Specialist</span>
                            </div>
                            <div class="detail-item">
                                <i class="bx bx-map"></i>
                                <span><?= htmlspecialchars($data['caretaker']['location'] ?? 'Colombo') ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="bx bx-time"></i>
                                <span><?= htmlspecialchars($data['caretaker']['experience'] ?? '5+ years') ?> Experience</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-body">
                    <!-- Professional Info -->
                    <div class="info-card">
                        <h3 class="card-title">Professional Information</h3>
                        <div class="info-grid">
                            <div class="info-box">
                                <div class="info-label">Service Type</div>
                                <div class="info-value"><?= htmlspecialchars($data['caretaker']['service_type'] ?? 'Elder Care') ?></div>
                            </div>
                            <div class="info-box">
                                <div class="info-label">Location</div>
                                <div class="info-value"><?= htmlspecialchars($data['caretaker']['location'] ?? 'Colombo') ?></div>
                            </div>
                            <div class="info-box">
                                <div class="info-label">Rating</div>
                                <div class="info-value rating-value">
                                    <?php 
                                    $rating = (float)($data['caretaker']['rating'] ?? 4.5);
                                    for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bx bx-star <?= $i <= $rating ? 'filled' : 'empty' ?>"></i>
                                    <?php endfor; ?>
                                    <span class="rating-text"><?= number_format($rating, 1) ?>/5.0</span>
                                </div>
                            </div>
                            <div class="info-box">
                                <div class="info-label">Status</div>
                                <div class="info-value status-badge active">Active</div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Period -->
                    <div class="service-card">
                        <h3 class="card-title">Service Period</h3>
                        <div class="service-timeline">
                            <div class="timeline-item">
                                <div class="timeline-icon start">
                                    <i class="bx bx-calendar"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-label">Start Date</div>
                                    <div class="timeline-date"><?= date('M d, Y', strtotime($data['service_period']['start_date'] ?? '2026-04-23')) ?></div>
                                </div>
                            </div>
                            <div class="timeline-connector">
                                <div class="connector-line"></div>
                                <i class="bx bx-right-arrow-alt"></i>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-icon end">
                                    <i class="bx bx-calendar-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-label">End Date</div>
                                    <div class="timeline-date"><?= date('M d, Y', strtotime($data['service_period']['end_date'] ?? '2026-04-23')) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About Section -->
                    <?php if (!empty($data['caretaker']['about'])): ?>
                    <div class="about-card">
                        <h3 class="card-title">About This Caretaker</h3>
                        <div class="about-content">
                            <p><?= nl2br(htmlspecialchars($data['caretaker']['about'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Affected Bookings -->
                    <?php if (!empty($data['affected_bookings'])): ?>
                    <div class="bookings-card">
                        <h3 class="card-title">Affected Bookings</h3>
                        <div class="bookings-grid">
                            <?php foreach ($data['affected_bookings'] as $booking): ?>
                                <div class="booking-card-item">
                                    <div class="booking-header">
                                        <span class="booking-id">Booking #<?= (int)$booking['id'] ?></span>
                                        <span class="booking-status status-<?= strtolower($booking['status']) ?>"><?= htmlspecialchars($booking['status']) ?></span>
                                    </div>
                                    <div class="booking-info">
                                        <div class="booking-detail">
                                            <i class="bx bx-briefcase"></i>
                                            <span><?= htmlspecialchars($booking['service_type']) ?></span>
                                        </div>
                                        <div class="booking-detail">
                                            <i class="bx bx-calendar"></i>
                                            <span><?= htmlspecialchars($booking['booking_date']) ?></span>
                                        </div>
                                        <div class="booking-detail">
                                            <i class="bx bx-time"></i>
                                            <span><?= htmlspecialchars($booking['preferred_time']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

      
            </div>
        </div>
    </main>
</div>

<?php include_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
