<?php
$caretakerPageTitle = 'Dashboard - SmartCare';
$caretakerExtraCss = ['caretaker/ct_dashboard.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<?php
  $upcomingCount = count($data['upcoming'] ?? []);
  $pendingLeaveCount = 0;
  foreach ($data['leaves'] ?? [] as $leave) {
      if (strtolower($leave['status'] ?? '') === 'pending') {
          $pendingLeaveCount++;
      }
  }
  $profileRequestPending = !empty($data['latestProfileChangeRequest']) && (($data['latestProfileChangeRequest']['status'] ?? '') === 'Pending');
  $activeBookings = (int)($data['monthlyStats']['active_bookings'] ?? 0);
  $completedBookings = (int)($data['monthlyStats']['completed_bookings'] ?? 0);
  $workingDays = (int)($data['monthlyStats']['working_days'] ?? 0);
  $availability = !empty($data['monthlyStats']['is_available']);
  $rating = number_format((float)($data['monthlyStats']['rating'] ?? 0), 1);
  $availabilityLabel = $availability ? "Visible to clients" : "Hidden from clients";
?>

<main class="main-content admin-dashboard-page caretaker-dashboard-page">

    <div class="caretaker-dashboard">

        <!-- Welcome hero -->
        <section class="caretaker-dashboard-hero" aria-labelledby="caretakerDashboardHeroTitle">
            <div class="caretaker-dashboard-hero__content">
                <div class="caretaker-dashboard-hero__intro">
                    <h1 id="caretakerDashboardHeroTitle" class="caretaker-dashboard-hero__title">
                        <span class="caretaker-dashboard-hero__greeting">Welcome back,</span>
                        <span class="caretaker-dashboard-hero__name"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Caregiver', ENT_QUOTES, 'UTF-8') ?></span>
                    </h1>
                    <p class="caretaker-dashboard-hero__lead">Manage your schedule, bookings, and leave requests effortlessly.</p>

                    <div class="caretaker-dashboard-hero__actions" role="group" aria-label="Primary dashboard actions">
                        <a class="btn caretaker-dashboard-hero__btn-primary" href="<?= URLROOT ?>/public?url=caretaker/ct_schedule">
                            <i class='bx bx-calendar' aria-hidden="true"></i>
                            <span>My Schedule</span>
                        </a>
                        <a class="btn secondary caretaker-dashboard-hero__btn-secondary" href="<?= URLROOT ?>/public?url=caretaker/ct_booking">
                            <i class='bx bx-book-alt' aria-hidden="true"></i>
                            <span>Bookings</span>
                        </a>
                    </div>

                    <div class="caretaker-dashboard-hero__highlights" aria-label="Dashboard section shortcuts">
                        <a class="caretaker-dashboard-hero__highlight-item caretaker-dashboard-hero__highlight-link" href="#statsCardsSection">
                            <i class='bx bx-bar-chart-alt-2' aria-hidden="true"></i>
                            <div>
                                <p class="caretaker-dashboard-hero__highlight-value">Stats Overview</p>
                            </div>
                        </a>
                        <a class="caretaker-dashboard-hero__highlight-item caretaker-dashboard-hero__highlight-link" href="#quickActionsSection">
                            <i class='bx bx-grid-alt' aria-hidden="true"></i>
                            <div>
                                <p class="caretaker-dashboard-hero__highlight-value">Quick Actions</p>
                            </div>
                        </a>
                        <a class="caretaker-dashboard-hero__highlight-item caretaker-dashboard-hero__highlight-link" href="#upcomingBookingsSection">
                            <i class='bx bx-list-check' aria-hidden="true"></i>
                            <div>
                                <p class="caretaker-dashboard-hero__highlight-value">Upcoming</p>
                            </div>
                        </a>
                        <a class="caretaker-dashboard-hero__highlight-item caretaker-dashboard-hero__highlight-link" href="#leaveSection">
                            <i class='bx bx-calendar-x' aria-hidden="true"></i>
                            <div>
                                <p class="caretaker-dashboard-hero__highlight-value">Leave Status</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Cards -->
        <div id="statsCardsSection" class="stats-cards caretaker-dashboard-scroll-target">
            <h2>Status Cards</h2>
            <div class="card">
                <div class="action1">
                    <i class='bx bx-book'></i>
                </div>
                <h3><?= $upcomingCount ?></h3>
                <p>Upcoming Bookings</p>
            </div>
            <div class="card">
                <div class="action1">
                    <i class='bx bx-time-five'></i>
                </div>
                <h3><?= $workingDays ?></h3>
                <p>Working Days</p>
            </div>
            <div class="card">
                <div class="action1">
                    <i class='bx bx-calendar-x'></i>
                </div>
                <h3><?= $pendingLeaveCount ?></h3>
                <p>Pending Leave</p>
            </div>
            <div class="card">
                <div class="action1">
                    <i class='bx bx-star'></i>
                </div>
                <h3><?= $rating ?></h3>
                <p>Average Rating</p>
            </div>
            <div class="card">
                <div class="action1">
                    <i class='bx bx-show'></i>
                </div>
                <h3><?= $availability ? 'Visible' : 'Hidden' ?></h3>
                <p>Availability Status</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <section id="quickActionsSection" class="quick-actions caretaker-dashboard-scroll-target">
            <h2>Quick Actions</h2>
            <div class="actions">
                <div class="action">
                    <i class='bx bx-calendar'></i>
                    <h3>
                        <button class="main-btn" onclick="location.href='<?= URLROOT ?>/public?url=caretaker/ct_schedule'">
                            My Schedule
                        </button>
                    </h3>
                    <p>View shift details</p>
                </div>
                <div class="action">
                    <i class='bx bx-book-alt'></i>
                    <h3>
                        <button class="main-btn" onclick="location.href='<?= URLROOT ?>/public?url=caretaker/ct_booking'">
                            Bookings
                        </button>
                    </h3>
                    <p>Client appointments</p>
                </div>
                <div class="action">
                    <i class='bx bx-calendar-x'></i>
                    <h3>
                        <button class="main-btn" onclick="location.href='<?= URLROOT ?>/public?url=caretaker/ct_leave'">
                            Request Leave
                        </button>
                    </h3>
                    <p>Submit a request</p>
                </div>
                <div class="action">
                    <i class='bx bx-cog'></i>
                    <h3>
                        <button class="main-btn" onclick="location.href='<?= URLROOT ?>/public?url=caretaker/ct_settings'">
                            Settings
                        </button>
                    </h3>
                    <p>Update profile</p>
                </div>
            </div>
        </section>

        <!-- Upcoming / Overview Section -->
        <section id="upcomingBookingsSection" class="recent-bookings caretaker-dashboard-recent caretaker-dashboard-scroll-target">
            <h2>Upcoming Bookings</h2>
            
            <?php if (empty($data['upcoming'])): ?>
                <div class="booking">
                    <div>
                        <h3>No upcoming bookings</h3>
                        <p>You do not have any upcoming appointments scheduled at the moment.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($data['upcoming'], 0, 5) as $booking): ?>
                    <div class="booking">
                        <div class="booking-icon" style="width:40px; height:40px; border-radius:10px; background:#e3f2fd; display:flex; align-items:center; justify-content:center; color:#1e88e5; font-size:1.5rem; flex-shrink:0;">
                            <i class='bx bx-user'></i>
                        </div>
                        <div style="flex: 1;">
                            <h3><?= htmlspecialchars($booking['service_type']) ?> with <?= htmlspecialchars($booking['client_name']) ?></h3>
                            <p>
                                <i class='bx bx-calendar'></i> <?= date('m/d/Y', strtotime($booking['booking_date'])) ?>
                                • <i class='bx bx-current-location'></i> <?= htmlspecialchars($booking['service_location']) ?>
                            </p>
                        </div>
                        <span class="status accepted">Upcoming</span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Leave Status Section -->
        <section id="leaveSection" class="recent-bookings caretaker-dashboard-recent caretaker-dashboard-scroll-target" style="margin-top:20px;">
            <h2>Leave & Availability</h2>
            <div class="booking" style="align-items: center;">
                <div class="booking-icon" style="width:40px; height:40px; border-radius:10px; background:#fff3e0; display:flex; align-items:center; justify-content:center; color:#ff9800; font-size:1.5rem; flex-shrink:0;">
                   <i class='bx bx-calendar-x'></i>
                </div>
                <div style="flex:1;">
                    <h3 style="margin-bottom: 2px;">Availability Status</h3>
                    <p style="margin-bottom: 0;">You are currently <strong><?= $availabilityLabel ?></strong>. You have <strong><?= $completedBookings ?></strong> completed bookings and <strong><?= $pendingLeaveCount ?></strong> pending leave requests.</p>
                </div>
                <div style="flex-shrink:0; margin-left: auto;">
                    <a class="btn btn-sm" style="background:#1e88e5; color:#fff;" href="<?= URLROOT ?>/public?url=caretaker/ct_leave">Manage Leave</a>
                </div>
            </div>
        </section>

    </div>
</main>

<script>
  window.dashboardData = {
    workingDates: <?= json_encode($data['workingDates'] ?? []) ?>,
    calendarMonth: <?= (int)($data['calendarMonth'] ?? date('n')) ?>,
    calendarYear: <?= (int)($data['calendarYear'] ?? date('Y')) ?>,
    updateAvailabilityUrl: "<?= URLROOT ?>/caretaker/updateAvailabilityStatus"
  };
</script>
<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_dashboard.js"></script>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
