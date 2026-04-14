<?php
$clientPageTitle = 'Booking submitted — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_bookingConfirm.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$b = $data['booking'] ?? [];
$statusClass = strtolower((string) ($b['status'] ?? 'requested'));
$statusClass = preg_replace('/[^a-z0-9_-]/', '', $statusClass);
?>
<main class="main-content client-booking-confirm-page">
    <div class="dashboard-layout">
        <header class="page-header client-booking-confirm-page__header">
            
        </header>

        <article class="card client-booking-confirm-card" aria-labelledby="booking-confirm-title">
            <div class="client-booking-confirm-card__success">
                <span class="client-booking-confirm-card__icon" aria-hidden="true"><i class="bx bx-check-circle"></i></span>
                <div>
                    <h2 id="booking-confirm-title" class="client-booking-confirm-card__title">Booking submitted successfully</h2>
                    <p class="client-booking-confirm-card__subtitle">Your caregiver booking request has been sent.</p>
                </div>
            </div>

            <dl class="client-booking-confirm-details">
                <div class="client-booking-confirm-details__row">
                    <dt>Booking ID</dt>
                    <dd><?= htmlspecialchars((string) ($b['booking_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>Caretaker</dt>
                    <dd><?= htmlspecialchars((string) ($b['caretaker_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>Service</dt>
                    <dd><?= htmlspecialchars((string) ($b['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>Basis</dt>
                    <dd><?= htmlspecialchars((string) ($b['basis'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>Duration</dt>
                    <dd><?= htmlspecialchars((string) ($b['duration'] ?? ''), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string) ($b['basis'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>Preferred time</dt>
                    <dd><?= htmlspecialchars((string) ($b['preferred_time'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>Booking date</dt>
                    <dd><?= !empty($b['booking_date']) ? htmlspecialchars(date('d M Y', strtotime((string) $b['booking_date'])), ENT_QUOTES, 'UTF-8') : '—' ?></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>District</dt>
                    <dd><?= htmlspecialchars((string) ($b['district'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>Street</dt>
                    <dd><?= htmlspecialchars((string) ($b['street'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>Address line 1</dt>
                    <dd><?= htmlspecialchars((string) ($b['address_line1'] ?? ''), ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php if (!empty($b['address_line2'])): ?>
                <div class="client-booking-confirm-details__row">
                    <dt>Address line 2</dt>
                    <dd><?= htmlspecialchars((string) $b['address_line2'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                <?php if (!empty($b['postal_code'])): ?>
                <div class="client-booking-confirm-details__row">
                    <dt>Postal code</dt>
                    <dd><?= htmlspecialchars((string) $b['postal_code'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <?php endif; ?>
                <?php if (!empty($b['customization'])): ?>
                <div class="client-booking-confirm-details__row client-booking-confirm-details__row--block">
                    <dt>Customization notes</dt>
                    <dd><?= nl2br(htmlspecialchars((string) $b['customization'], ENT_QUOTES, 'UTF-8')) ?></dd>
                </div>
                <?php endif; ?>
                <div class="client-booking-confirm-details__row">
                    <dt>Total payment</dt>
                    <dd><strong class="client-booking-confirm-details__amount">LKR <?= number_format((float) ($b['total_payment'] ?? 0), 2) ?></strong></dd>
                </div>
                <div class="client-booking-confirm-details__row">
                    <dt>Status</dt>
                    <dd><span class="client-booking-confirm-status client-booking-confirm-status--<?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) ($b['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></dd>
                </div>
            </dl>

            <div class="client-booking-confirm-actions">
                <button type="button" class="btn" id="bookingConfirmUpcoming">View upcoming bookings</button>
                <button type="button" class="btn-secondary" id="bookingConfirmHome">Back to dashboard</button>
            </div>
        </article>
    </div>
</main>

<script>
window.URLROOT = <?= json_encode(URLROOT) ?>;
</script>
<script src="<?= URLROOT ?>/public/js/client/c_bookingConfirm.js"></script>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
