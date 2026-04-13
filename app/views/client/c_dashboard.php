<?php
$clientPageTitle = 'Dashboard — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_dashboard.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/client/partials/client_booking_status_helper.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$servicePriceRates = [
    'Elder Care' => [
        'Monthly' => 50000,
        'Yearly'  => 550000,
    ],
    'Babysitter' => [
        'Daily'   => 2200,
        'Monthly' => 45000,
        'Yearly'  => 500000,
    ],
    'Maid' => [
        'Hourly'  => 500,
        'Daily'   => 2000,
        'Monthly' => 38000,
        'Yearly'  => 420000,
    ],
];

$timePriceModifier = [
    'Full Time (8am - 5pm)' => 1.0,
    'Morning (8am - 12pm)'  => 0.6,
    'Evening (1pm - 5pm)'   => 0.6,
    'Night (6pm - 10pm)'    => 1.2,
];

function moneyLKR($amount)
{
    return 'LKR ' . number_format((float) $amount, 0);
}

$pendingAdvanceList = $data['pendingAdvance'] ?? [];
$hasPendingAdvance  = !empty($pendingAdvanceList);
$notifications      = $data['notifications'] ?? [];
$unreadNotifications  = 0;
foreach ($notifications as $n) {
    if (empty($n['is_read']) || (int) $n['is_read'] === 0) {
        $unreadNotifications++;
    }
}
$assignedCt = $data['assignedCaretaker'] ?? null;
$clientHeroImage = URLROOT . '/public/images/banner-client2.webp';
$clientDisplayName = trim((string) ($_SESSION['user']['name'] ?? ($_SESSION['user']['username'] ?? '')));
if ($clientDisplayName === '') {
    $clientDisplayName = 'there';
}
?>

<div id="emergencyModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="emergencyModalTitle">
    <div class="modal-content">
        <button type="button" class="close" onclick="closeEmergencyModal()" aria-label="Close">&times;</button>
        <h3 id="emergencyModalTitle">Emergency support</h3>
        <p class="text-muted" style="margin-top:-8px;margin-bottom:16px;">Submit your emergency request or use quick dial.</p>

        <form method="POST" action="">
            <div class="field">
                <label for="emergencyType">Emergency type</label>
                <select id="emergencyType" name="type" required>
                    <option value="">Select type</option>
                    <option>Medical Emergency</option>
                    <option>Caretaker Not Responding</option>
                    <option>Accident / Injury</option>
                    <option>Other</option>
                </select>
            </div>
            <div class="field">
                <label for="emergencyDesc">Description</label>
                <textarea id="emergencyDesc" name="description" rows="3" placeholder="Describe the emergency..." required></textarea>
            </div>
            <div class="field">
                <label for="emergencyPhone">Contact number</label>
                <input id="emergencyPhone" type="text" name="phone" required>
            </div>
            <button type="submit" class="submit-btn">Send alert</button>
        </form>

        <div class="quick-call">
            <a href="tel:1990">Ambulance</a>
            <a href="tel:119">Police</a>
        </div>
    </div>
</div>

<?php if ($hasPendingAdvance): ?>
<div id="advanceModal" class="modal show" role="dialog" aria-modal="true" aria-labelledby="advanceModalTitle">
    <div class="modal-content" style="max-width:640px;">
        <button type="button" class="close" onclick="document.getElementById('advanceModal').classList.remove('show')" aria-label="Close">&times;</button>
        <h3 id="advanceModalTitle">Advance payments required</h3>
        <p class="text-muted">You have pending advance payments for the following bookings.</p>
        <div class="advance-modal-list">
            <?php require APPROOT . '/views/client/partials/advance_pending_booking_cards.php'; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<main class="main-content admin-dashboard-page">
    <div class="dashboard-layout">
    <?php if (!empty($_SESSION['flash_message'])): ?>
        <div class="flash success client-dashboard-flash"><?php echo htmlspecialchars((string) $_SESSION['flash_message']);
        unset($_SESSION['flash_message']); ?></div>
    <?php endif; ?>
    <div class="client-dashboard">
        <section class="client-dashboard-hero" aria-labelledby="client-dashboard-welcome-heading" style="--client-dashboard-hero-img: url('<?= htmlspecialchars($clientHeroImage, ENT_QUOTES, 'UTF-8') ?>');">
            <div class="client-dashboard-hero__content">
                <div class="client-dashboard-hero__intro">
                    <p class="client-dashboard-hero__eyebrow">SmartCare</p>
                    <h1 id="client-dashboard-welcome-heading" class="client-dashboard-hero__title">
                        <span class="client-dashboard-hero__greeting">Welcome back,</span>
                        <span class="client-dashboard-hero__name"><?= htmlspecialchars($clientDisplayName, ENT_QUOTES, 'UTF-8') ?></span>
                    </h1>
                    <p class="client-dashboard-hero__lead">Your visits, payments, and caregivers at a glance.</p>
                    <div class="client-dashboard-hero__actions">
                        <a class="btn client-dashboard-hero__btn-primary" href="<?= URLROOT ?>/public?url=client/c_find1"><i class="bx bx-search" aria-hidden="true"></i> Find a caregiver</a>
                        <a class="btn secondary client-dashboard-hero__btn-secondary" href="<?= URLROOT ?>/public?url=client/c_myBookings">My bookings</a>
                    </div>
                </div>
                <div class="client-dashboard-hero__toolbar" aria-label="Dashboard tools">
                    <nav class="dashboard-breadcrumb client-dashboard-hero__breadcrumb" aria-label="Breadcrumb">
                        <ol class="dashboard-breadcrumb__list">
                            <li><a href="<?= URLROOT ?>/public?url=client/c_dashboard">SmartCare</a></li>
                            <li><span class="dashboard-breadcrumb__sep" aria-hidden="true">/</span> Client</li>
                            <li><span class="dashboard-breadcrumb__sep" aria-hidden="true">/</span> <span class="dashboard-breadcrumb__current">Dashboard</span></li>
                        </ol>
                    </nav>
                    <div class="client-dashboard-hero__toolbar-right">
                        <time class="dashboard-page-header__date client-dashboard-hero__date" datetime="<?= date('c') ?>"><?= htmlspecialchars(date('l, j F Y')) ?></time>
                        <nav class="dashboard-quick-actions" aria-label="Quick links">
                            <a href="<?= URLROOT ?>/public?url=client/c_find1" class="dashboard-quick-actions__link dashboard-quick-actions__link--primary"><i class="bx bx-plus-circle" aria-hidden="true"></i><span>Book care</span></a>
                            <a href="<?= URLROOT ?>/public?url=client/c_myBookings" class="dashboard-quick-actions__link"><i class="bx bx-calendar" aria-hidden="true"></i><span>Bookings</span></a>
                            <a href="<?= URLROOT ?>/public?url=client/payments" class="dashboard-quick-actions__link"><i class="bx bx-wallet" aria-hidden="true"></i><span>Payments</span></a>
                            <a href="<?= URLROOT ?>/public?url=client/c_upcomingBookings" class="dashboard-quick-actions__link"><i class="bx bx-calendar-event" aria-hidden="true"></i><span>Upcoming</span></a>
                        </nav>
                    </div>
                </div>
            </div>
        </section>

        <section class="client-dashboard-stats" aria-label="Summary">
            <h2 class="dashboard-overview-charts__heading">Your numbers</h2>
            <div class="stats-grid dashboard-stats">
                <article class="stat-card card-hover dashboard-stat dashboard-stat--blue">
                    <div class="stat-card-icon" aria-hidden="true"><i class="bx bx-book"></i></div>
                    <div class="stat-card-label">Active bookings</div>
                    <div class="stat-card-value"><?= (int) ($data['activeBookings'] ?? 0) ?></div>
                    <a href="<?= URLROOT ?>/public?url=client/c_myBookings" class="stat-card-link">Open bookings hub</a>
                </article>
                <article class="stat-card card-hover dashboard-stat dashboard-stat--teal">
                    <div class="stat-card-icon" aria-hidden="true"><i class="bx bx-user"></i></div>
                    <div class="stat-card-label">Caregivers assigned</div>
                    <div class="stat-card-value"><?= (int) ($data['caretakers'] ?? 0) ?></div>
                    <a href="<?= URLROOT ?>/public?url=client/c_find1" class="stat-card-link">Browse caregivers</a>
                </article>
                <article class="stat-card card-hover dashboard-stat dashboard-stat--ocean">
                    <div class="stat-card-icon" aria-hidden="true"><i class="bx bx-money"></i></div>
                    <div class="stat-card-label">Total spent</div>
                    <div class="stat-card-value">LKR <?= number_format((float) ($data['totalSpent'] ?? 0), 0) ?></div>
                    <a href="<?= URLROOT ?>/public?url=client/payments" class="stat-card-link">Payment history</a>
                </article>
                <article class="stat-card card-hover dashboard-stat dashboard-stat--violet">
                    <div class="stat-card-icon" aria-hidden="true"><i class="bx bx-star"></i></div>
                    <div class="stat-card-label">Avg. rating you gave</div>
                    <div class="stat-card-value"><?= htmlspecialchars((string) ($data['avgRating'] ?? '0.0'), ENT_QUOTES, 'UTF-8') ?></div>
                    <a href="<?= URLROOT ?>/public?url=client/c_feedback" class="stat-card-link">Share feedback</a>
                </article>
                <article class="stat-card card-hover dashboard-stat <?= $hasPendingAdvance ? 'dashboard-stat--rejected' : 'dashboard-stat--indigo' ?>">
                    <div class="stat-card-icon" aria-hidden="true"><i class="bx bx-wallet-alt"></i></div>
                    <div class="stat-card-label">Pending advances</div>
                    <div class="stat-card-value"><?= count($pendingAdvanceList) ?></div>
                    <a href="<?= URLROOT ?>/public?url=client/payments" class="stat-card-link"><?= $hasPendingAdvance ? 'Pay now' : 'All clear' ?></a>
                </article>
            </div>
        </section>

        <?php if (is_array($assignedCt) && !empty($assignedCt['caretaker_name'])): ?>
        <aside class="client-assigned-highlight card" aria-label="Active caregiver">
            <div class="client-assigned-highlight__icon" aria-hidden="true"><i class="bx bx-user-check"></i></div>
            <div class="client-assigned-highlight__body">
                <p class="client-assigned-highlight__label">Caregiver on your latest active booking</p>
                <p class="client-assigned-highlight__name"><?= htmlspecialchars((string) $assignedCt['caretaker_name']) ?></p>
                <p class="client-assigned-highlight__meta"><?= htmlspecialchars((string) ($assignedCt['service_type'] ?? '')) ?> · <?= htmlspecialchars(str_replace('_', ' ', (string) ($assignedCt['status'] ?? ''))) ?></p>
            </div>
        </aside>
        <?php endif; ?>

        <?php if ($hasPendingAdvance || $unreadNotifications > 0): ?>
        <section class="dashboard-review-badges" aria-label="Needs your attention">
            <h2 class="dashboard-review-badges__heading">Needs your attention</h2>
            <div class="dashboard-review-badges__grid">
                <?php if ($hasPendingAdvance): ?>
                <a href="<?= URLROOT ?>/public?url=client/payments" class="dashboard-review-badge dashboard-review-badge--payments">
                    <span class="dashboard-review-badge__label">Advance payments</span>
                    <span class="dashboard-review-badge__value"><?= count($pendingAdvanceList) ?></span>
                    <span class="dashboard-review-badge__hint">Complete payment to keep bookings confirmed</span>
                    <span class="dashboard-review-badge__cta">Open payments <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
                </a>
                <?php endif; ?>
                <?php if ($unreadNotifications > 0): ?>
                <a href="<?= URLROOT ?>/public?url=client/c_announcement" class="dashboard-review-badge dashboard-review-badge--feedback">
                    <span class="dashboard-review-badge__label">Notifications</span>
                    <span class="dashboard-review-badge__value"><?= (int) $unreadNotifications ?></span>
                    <span class="dashboard-review-badge__hint">Unread updates from SmartCare</span>
                    <span class="dashboard-review-badge__cta">View <i class="bx bx-chevron-right" aria-hidden="true"></i></span>
                </a>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        

        <section class="dashboard-shortcut-tiles" aria-label="Shortcuts">
            <h2 class="dashboard-shortcut-tiles__heading">Shortcuts</h2>
            <div class="dashboard-shortcut-tiles__grid">
                <a href="<?= URLROOT ?>/public?url=client/c_myBookings" class="dashboard-shortcut-tile">
                    <span class="dashboard-shortcut-tile__icon"><i class="bx bx-calendar" aria-hidden="true"></i></span>
                    <span class="dashboard-shortcut-tile__title">Bookings hub</span>
                    <span class="dashboard-shortcut-tile__meta">Upcoming &amp; history</span>
                </a>
                <a href="<?= URLROOT ?>/public?url=client/payments" class="dashboard-shortcut-tile">
                    <span class="dashboard-shortcut-tile__icon"><i class="bx bx-wallet" aria-hidden="true"></i></span>
                    <span class="dashboard-shortcut-tile__title">Payments</span>
                    <span class="dashboard-shortcut-tile__meta">Balances &amp; receipts</span>
                </a>
                <a href="<?= URLROOT ?>/public?url=client/c_feedback" class="dashboard-shortcut-tile">
                    <span class="dashboard-shortcut-tile__icon"><i class="bx bx-star" aria-hidden="true"></i></span>
                    <span class="dashboard-shortcut-tile__title">Feedback</span>
                    <span class="dashboard-shortcut-tile__meta">Rate your experience</span>
                </a>
                <a href="<?= URLROOT ?>/public?url=client/c_announcement" class="dashboard-shortcut-tile">
                    <span class="dashboard-shortcut-tile__icon"><i class="bx bxs-megaphone" aria-hidden="true"></i></span>
                    <span class="dashboard-shortcut-tile__title">Announcements</span>
                    <span class="dashboard-shortcut-tile__meta">News from SmartCare</span>
                </a>
            </div>
        </section>

        <section class="price-overview" aria-labelledby="client-price-overview">
            <h2 id="client-price-overview" class="dashboard-overview-charts__heading">Service price overview</h2>
            <p class="client-section-lead">Base rates and time modifiers. Your final quote also depends on duration and the time slot you pick when you book.</p>

            <div class="price-grid">
                <article class="card price-card">
                    <div class="card-header">
                        <div class="price-head">
                            <div class="badge" aria-hidden="true"><i class="bx bx-plus-medical"></i></div>
                            <div>
                                <h3>Elder Care</h3>
                                <p>Monthly / Yearly</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="price-lines">
                            <div class="line"><span>Monthly</span><strong><?= moneyLKR($servicePriceRates['Elder Care']['Monthly']) ?></strong></div>
                            <div class="line"><span>Yearly</span><strong><?= moneyLKR($servicePriceRates['Elder Care']['Yearly']) ?></strong></div>
                        </div>
                        <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_find1">Book Elder Care</a>
                    </div>
                </article>

                <article class="card price-card">
                    <div class="card-header">
                        <div class="price-head">
                            <div class="badge" aria-hidden="true"><i class="bx bx-child"></i></div>
                            <div>
                                <h3>Babysitter</h3>
                                <p>Daily / Monthly / Yearly</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="price-lines">
                            <div class="line"><span>Daily</span><strong><?= moneyLKR($servicePriceRates['Babysitter']['Daily']) ?></strong></div>
                            <div class="line"><span>Monthly</span><strong><?= moneyLKR($servicePriceRates['Babysitter']['Monthly']) ?></strong></div>
                            <div class="line"><span>Yearly</span><strong><?= moneyLKR($servicePriceRates['Babysitter']['Yearly']) ?></strong></div>
                        </div>
                        <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_find1">Book Babysitter</a>
                    </div>
                </article>

                <article class="card price-card">
                    <div class="card-header">
                        <div class="price-head">
                            <div class="badge" aria-hidden="true"><i class="bx bx-home-heart"></i></div>
                            <div>
                                <h3>Maid</h3>
                                <p>Hourly / Daily / Monthly / Yearly</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="price-lines">
                            <div class="line"><span>Hourly</span><strong><?= moneyLKR($servicePriceRates['Maid']['Hourly']) ?></strong></div>
                            <div class="line"><span>Daily</span><strong><?= moneyLKR($servicePriceRates['Maid']['Daily']) ?></strong></div>
                            <div class="line"><span>Monthly</span><strong><?= moneyLKR($servicePriceRates['Maid']['Monthly']) ?></strong></div>
                            <div class="line"><span>Yearly</span><strong><?= moneyLKR($servicePriceRates['Maid']['Yearly']) ?></strong></div>
                        </div>
                        <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_find1">Book Maid</a>
                    </div>
                </article>
            </div>

            <article class="card modifier-card">
                <div class="card-body">
                    <div class="modifier-head">
                        <i class="bx bx-time-five" aria-hidden="true"></i>
                        <h3>Time modifiers</h3>
                    </div>
                    <div class="modifier-grid">
                        <div><span>Morning</span><strong><?= $timePriceModifier['Morning (8am - 12pm)'] ?>×</strong></div>
                        <div><span>Evening</span><strong><?= $timePriceModifier['Evening (1pm - 5pm)'] ?>×</strong></div>
                        <div><span>Full time</span><strong><?= $timePriceModifier['Full Time (8am - 5pm)'] ?>×</strong></div>
                        <div><span>Night</span><strong><?= $timePriceModifier['Night (6pm - 10pm)'] ?>×</strong></div>
                    </div>
                </div>
            </article>

            <article class="card advance-card">
                <div class="card-body">
                    <div class="modifier-head">
                        <i class="bx bx-wallet" aria-hidden="true"></i>
                        <h3>Advance payment rules</h3>
                    </div>
                    <div class="price-lines">
                        <div class="line"><span>Hourly</span><strong>Full payment required</strong></div>
                        <div class="line"><span>Daily (lead time)</span><strong>Within 15 days: full payment</strong></div>
                        <div class="line"><span>Daily</span><strong>15+ days: 50% advance</strong></div>
                        <div class="line"><span>Monthly</span><strong>1 month advance for &lt; 6 months; otherwise 3 months</strong></div>
                        <div class="line"><span>Yearly</span><strong>&lt; 1 year: 3 months; 1+ year: 6 months</strong></div>
                    </div>
                </div>
            </article>
        </section>

        <section class="quick-actions" aria-label="Quick actions">
            <h2 class="dashboard-overview-charts__heading">Quick actions</h2>
            <div class="actions">
                <div class="action">
                    <i class="bx bx-search" aria-hidden="true"></i>
                    <h3>Book a service</h3>
                    <p>Find and book a caregiver.</p>
                    <button type="button" class="btn" onclick="location.href='<?= URLROOT ?>/public?url=client/c_find1'">Book new service</button>
                </div>
                <div class="action">
                    <i class="bx bx-calendar-edit" aria-hidden="true"></i>
                    <h3>Upcoming bookings</h3>
                    <p>Reschedule or manage visits.</p>
                    <button type="button" class="btn secondary" onclick="location.href='<?= URLROOT ?>/public?url=client/c_upcomingBookings'">View bookings</button>
                </div>
                <div class="action">
                    <i class="bx bx-message-rounded-dots" aria-hidden="true"></i>
                    <h3>Messages</h3>
                    <p>Open a booking, then message your caregiver.</p>
                    <button type="button" class="btn secondary" onclick="location.href='<?= URLROOT ?>/public?url=client/c_myBookings'">Go to bookings</button>
                </div>
                <div class="action">
                    <i class="bx bx-support" aria-hidden="true"></i>
                    <h3>Emergency</h3>
                    <p>24/7 emergency assistance.</p>
                    <button type="button" class="btn secondary" onclick="openEmergencyModal()">Emergency support</button>
                </div>
            </div>
        </section>

        <section class="card client-dashboard-recent" aria-label="Recent bookings">
            <div class="card-header">
                <h3 class="card-title">Recent bookings</h3>
                <p class="client-card-sub">A short list of what changed last on your account.</p>
            </div>
            <div class="card-body client-dashboard-recent__body">
            <?php if (!empty($data['recentBookings'])): ?>
                <?php foreach ($data['recentBookings'] as $booking): ?>
                    <div class="booking">
                        <img src="<?= URLROOT ?>/public/images/find.png" alt="" width="52" height="52">
                        <div>
                            <h3><?= htmlspecialchars((string) $booking['service_type']) ?> with <?= htmlspecialchars((string) $booking['caretaker_name']) ?></h3>
                            <p>
                                <?= date('M j, Y', strtotime((string) $booking['booking_date'])) ?>
                                · <?= htmlspecialchars((string) $booking['preferred_time']) ?>
                                · <?= htmlspecialchars((string) $booking['duration']) ?> <?= htmlspecialchars((string) ($booking['basis'] ?? '')) ?>
                            </p>
                        </div>
                        <span class="status <?= client_booking_status_class($booking['status'] ?? '') ?>"><?= htmlspecialchars((string) ($booking['status'] ?? '')) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="booking booking--empty">
                    <div>
                        <h3>No recent bookings</h3>
                        <p>You do not have any recent bookings yet. Start with <strong>Find a caregiver</strong> above.</p>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </section>
    </div>
    </div>
</main>

<script src="<?= URLROOT ?>/public/js/client/c_dashboard.js"></script>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
