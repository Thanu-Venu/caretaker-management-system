<?php
$clientPageTitle = 'Book a service — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_book.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$ct = $data['caretaker'] ?? [];
$prefill = $data['prefill'] ?? [];
$serviceOptions = $data['serviceOptions'] ?? [];
$total_payment = $data['total_payment'] ?? 0;

$serviceType = $ct['service_type'] ?? '';
$bases = $serviceOptions[$serviceType] ?? [];
$lockSchedulePrefill = trim((string) ($prefill['date'] ?? '')) !== '';

$timeOptions = [
    'Elder Care' => ['Full Time (8am - 5pm)', 'Morning (8am - 12pm)', 'Evening (1pm - 5pm)', 'Night (6pm - 10pm)'],
    'Babysitter' => ['Full Time (8am - 5pm)', 'Morning (8am - 12pm)', 'Evening (1pm - 5pm)'],
    'Maid' => ['Full Time (8am - 5pm)', 'Morning (8am - 12pm)', 'Evening (1pm - 5pm)', 'Night (6pm - 10pm)'],
    'Disability Support' => ['Full Time (8am - 5pm)', 'Morning (8am - 12pm)', 'Evening (1pm - 5pm)', 'Night (6pm - 10pm)'],
];
?>

<main class="main-content admin-dashboard-page book-service-page">
    <header class="page-header">
        <div>
            <h1 class="page-title">Book your caregiver</h1>
            <p class="text-muted">Review details, confirm schedule, and submit your booking request.</p>
        </div>
        <div class="header-actions">
            <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_find1">Back to browse</a>
        </div>
    </header>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="flash error"><?php echo htmlspecialchars((string) $_SESSION['error']);
        unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="flash success"><?php echo htmlspecialchars((string) $_SESSION['success']);
        unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <section class="caretaker-summary" aria-label="Caregiver summary">
        <h2 id="ctName"><?= htmlspecialchars((string) ($ct['name'] ?? 'N/A')) ?></h2>
        <p><strong>Service:</strong> <span id="ctService"><?= htmlspecialchars($serviceType !== '' ? $serviceType : 'N/A') ?></span></p>
        <p><strong>Location:</strong> <span id="ctLocation"><?= htmlspecialchars((string) ($ct['location'] ?? 'N/A')) ?></span></p>
        <p><strong>Rating:</strong> <span id="ctRating"><?= htmlspecialchars((string) ($ct['rating'] ?? 'N/A')) ?></span></p>
    </section>

    <div class="card" style="margin-bottom:var(--admin-section-gap);">
        <div class="card-body">
            <p class="text-muted" style="margin:0 0 8px;">Pricing preview</p>
            <p style="margin:0;font-size:14px;color:var(--admin-text-secondary);"><strong>Base price:</strong> <span id="basePrice">Select a basis to see price</span></p>
            <p class="text-muted" style="margin:8px 0 0;font-size:13px;">Final price depends on duration and preferred time.</p>
        </div>
    </div>

    <section class="booking-form">
        <div class="form-section form-section--wide form-section--book-full">
            <h2 class="book-form-section-title">Booking details</h2>
            <form id="bookingForm" method="POST" action="<?= URLROOT ?>/client/bookCaretaker">
                <input type="hidden" name="caretaker_id" id="caretaker_id" value="<?= (int) ($ct['id'] ?? 0) ?>">
                <input type="hidden" name="service_type" id="service_type" value="<?= htmlspecialchars($serviceType, ENT_QUOTES) ?>">
                <input type="hidden" name="total_payment" id="total_payment" value="<?= htmlspecialchars((string) $total_payment, ENT_QUOTES) ?>">

                <div class="form-grid">
                 <div class="field">
                  <label for="basis">Basis <span class="required-mark" aria-hidden="true">*</span></label>
                    <select id="basis" required disabled>
                      <option value="">— Select —</option>
                        <?php if (!empty($bases)): ?>
                          <?php foreach ($bases as $basis): ?>
                            <option value="<?= htmlspecialchars($basis, ENT_QUOTES) ?>"
                              <?= (($prefill['basis'] ?? '') === $basis) ? 'selected' : '' ?>>
                               <?= htmlspecialchars($basis) ?>
                            </option>
                          <?php endforeach; ?>
                         <?php else: ?>
                          <option value="">No basis options available</option>
                         <?php endif; ?>
                        </select>
                        <input type="hidden" name="basis" id="basis_hidden" value="<?= htmlspecialchars($prefill['basis'] ?? '', ENT_QUOTES) ?>">
                    </div>

                    <div class="field">
                        <label for="duration">Duration <span class="required-mark" aria-hidden="true">*</span></label>
                        <input type="number" id="duration" name="duration" min="1" required readonly
                            value="<?= htmlspecialchars((string) ($prefill['duration'] ?? 1), ENT_QUOTES) ?>">
                        <small id="durationHint">Number of booking units</small>
                    </div>

                    <div class="field">
                        <label for="date">Preferred date <span class="required-mark" aria-hidden="true">*</span></label>
                        <input type="date" id="date" name="booking_date" required readonly
                            value="<?= htmlspecialchars($prefill['date'] ?? '', ENT_QUOTES) ?>">
                    </div>

                    <div class="field">
                        <label for="preferredTime" id="preferredTimeLabel">Preferred time <span class="required-mark" aria-hidden="true">*</span></label>
                        <div id="timeContainer">
                            <select id="preferredTime" required disabled>
                                <option value="">Select time</option>
                                <?php
                                $times = $timeOptions[$serviceType] ?? [];
                                foreach ($times as $time): ?>
                                    <option value="<?= htmlspecialchars($time, ENT_QUOTES) ?>"
                                        <?= (($prefill['time'] ?? '') === $time) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($time) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="preferred_time" id="preferred_time_hidden"
                            value="<?= htmlspecialchars($prefill['time'] ?? '', ENT_QUOTES) ?>">
                    </div>

                    <h3 class="field full book-address-heading">Service address</h3>
                    <div class="field">
                        <label for="districtField">District <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="districtField" type="text" name="district" value="<?= htmlspecialchars($ct['location'] ?? '', ENT_QUOTES) ?>" readonly>
                    </div>
                    <div class="field">
                        <label for="streetField">Street <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="streetField" type="text" name="street" required placeholder="e.g., Galle Road">
                    </div>
                    <div class="field">
                        <label for="addr1">Address line 1 <span class="required-mark" aria-hidden="true">*</span></label>
                        <input id="addr1" type="text" name="address_line1" required placeholder="No, Street, Area">
                    </div>
                    <div class="field">
                        <label for="addr2">Address line 2 <span class="text-muted">(optional)</span></label>
                        <input id="addr2" type="text" name="address_line2" placeholder="Landmark / Apartment">
                    </div>
                    <div class="field">
                        <label for="postal">Postal code <span class="text-muted">(optional)</span></label>
                        <input id="postal" type="text" name="postal_code" placeholder="e.g., 10300">
                    </div>

                    <div class="field full">
                        <label for="customization_hours">Customization (extra hours)</label>
                        <input type="hidden" id="customization_apply" name="customization_apply" value="per_unit">
                        <input type="number" id="customization_hours" name="customization_hours" min="0" max="8"
                            value="<?= htmlspecialchars((string) ($prefill['customization_hours'] ?? 0), ENT_QUOTES) ?>">
                        <small>Extra hours are charged at LKR 100 per hour (full duration).</small>
                    </div>
                    <div class="field full">
                        <label for="customization">Customization notes</label>
                        <textarea id="customization" name="customization" rows="3"
                            placeholder="Preferred time changes or extra hours"></textarea>
                    </div>
                </div>

                <div class="price-box">
                    <p>
                        <span>Base price</span>
                        <span class="price-value"><span id="basePriceAmount">0</span> <small>LKR</small></span>
                    </p>
                    <p>
                        <span>Customization</span>
                        <span class="price-value"><span id="customizationPrice">0</span> <small>LKR</small></span>
                    </p>
                    <p>
                        <span>Estimated total</span>
                        <span class="price-value"><span id="price"><?= number_format((float) $total_payment, 2) ?></span> <small>LKR</small></span>
                    </p>
                </div>

                <div class="field full">
                    <span id="availabilityMsg" class="text-muted"></span>
                </div>

                <div class="form-actions">
                    <button type="submit" id="bookBtn" class="submit-btn">Request booking</button>
                </div>
            </form>
        </div>
    </section>

</main>

<script>
    const serviceType = "<?= htmlspecialchars($serviceType, ENT_QUOTES) ?>";
    const basisValue = "<?= htmlspecialchars($prefill['basis'] ?? '', ENT_QUOTES) ?>";
    const preferredTimeValue = "<?= htmlspecialchars($prefill['time'] ?? '', ENT_QUOTES) ?>";
    const lockPrefilledFields = <?= $lockSchedulePrefill ? 'true' : 'false' ?>;
    document.getElementById('basis_hidden').value = basisValue;
    document.getElementById('preferred_time_hidden').value = preferredTimeValue;
</script>
<script src="<?= URLROOT ?>/public/js/client/c_book.js"></script>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
