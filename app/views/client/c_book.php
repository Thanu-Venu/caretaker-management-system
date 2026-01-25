<?php
// Header & Sidebar
include_once APPROOT . "/views/templates/client/c_header.php";
include_once APPROOT . "/views/templates/client/c_sidebar.php";

// Safely extract data
$ct = $data['caretaker'] ?? [];

$serviceOptions     = $data['serviceOptions'] ?? [];
$servicePriceRates  = $data['servicePriceRates'] ?? [];
$timePriceModifier  = $data['timePriceModifier'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Caretaker</title>
    <link rel="stylesheet" href="<?= URLROOT; ?>/public/css/client/c_book.css">
</head>

<body>
<main class="content">

<?php
// 🚨 HARD STOP if caretaker data missing (prevents blank page)
if (empty($ct) || empty($ct['id'])) {
    echo '<div class="alert error">Caretaker data not loaded. Please go back and select a caretaker again.</div>';
    echo '</main></body></html>';
    exit;
}
?>

<h1>Book Your Caretaker</h1>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert error">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- ================= CARETAKER SUMMARY ================= -->
<section class="caretaker-summary">
    <h2><?= htmlspecialchars($ct['name'] ?? 'N/A') ?></h2>
    <p><strong>Service:</strong> <?= htmlspecialchars($ct['service_type'] ?? 'N/A') ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($ct['location'] ?? 'N/A') ?></p>
    <p><strong>Rating:</strong> ⭐ <?= htmlspecialchars($ct['rating'] ?? 'N/A') ?></p>
</section>

<!-- ================= BASE PRICE ================= -->
<div class="form-group">
    <label>Base Price:</label>
    <span id="basePrice">Select a basis to see price</span>
    <p class="hint">Final price depends on duration and preferred time.</p>
</div>

<!-- ================= BOOKING FORM ================= -->
<section class="booking-form">
<form id="bookingForm" method="POST" action="<?= URLROOT ?>/public/?url=client/bookCaretaker">

    <input type="hidden" name="caretaker_id" value="<?= (int)$ct['id'] ?>">
    <input type="hidden" name="total_payment" id="total_payment" value="0">
    <input type="hidden" name="end_date" id="end_date" value="">

    <!-- ===== BASIS ===== -->
    <div class="form-group">
        <label for="basis">Select Basis</label>
        <select id="basis" name="basis" required>
            <option value="">-- Select --</option>

            <?php
            $serviceType = $ct['service_type'] ?? '';
            $bases = $serviceOptions[$serviceType] ?? [];
            ?>

            <?php if (!empty($bases)): ?>
                <?php foreach ($bases as $b): ?>
                    <option value="<?= htmlspecialchars($b, ENT_QUOTES) ?>">
                        <?= htmlspecialchars($b) ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">No basis options available</option>
            <?php endif; ?>
        </select>
    </div>

    <!-- ===== DURATION ===== -->
    <div class="form-group">
        <label for="duration">Duration</label>
        <input type="number" id="duration" name="duration" min="1" required>
    </div>

    <!-- ===== DATE ===== -->
    <div class="form-group">
        <label for="date">Preferred Start Date</label>
        <?php $minDate = date('Y-m-d', strtotime('+3 days')); ?>
        <input type="date" id="date" name="booking_date" min="<?= $minDate ?>" required>
        <small class="hint">Bookings must be made at least 3 days in advance.</small>
    </div>

    <!-- ===== TIME ===== -->
    <div class="form-group">
        <label for="preferredTime">Preferred Time</label>
        <select id="preferredTime" name="preferred_time" required>
            <option value="">Select Time</option>

            <?php
            $timeOptions = [
                "Elder Care" => [
                    "Full Time (8am - 5pm)",
                    "Morning (8am - 12pm)",
                    "Evening (1pm - 5pm)",
                    "Night (6pm - 10pm)"
                ],
                "Babysitter" => [
                    "Full Time (8am - 5pm)",
                    "Morning (8am - 12pm)",
                    "Evening (1pm - 5pm)"
                ],
                "Maid" => [
                    "Full Time (8am - 5pm)",
                    "Morning (8am - 12pm)",
                    "Evening (1pm - 5pm)"
                ]
                
            ];

            $times = $timeOptions[$serviceType] ?? [];
            ?>

            <?php if (!empty($times)): ?>
                <?php foreach ($times as $t): ?>
                    <option value="<?= htmlspecialchars($t, ENT_QUOTES) ?>">
                        <?= htmlspecialchars($t) ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">No time options available</option>
            <?php endif; ?>
        </select>
    </div>

    <!-- ===== LOCATION ===== -->
    <div class="form-group">
        <label>District</label>
    <input type="text"
           name="district"
           value="<?= htmlspecialchars($ct['location'] ?? '') ?>"
           readonly>


<label>Street</label>
<input type="text" name="street" placeholder="e.g., Galle Road">

<label>Address Line 1</label>
<input type="text" name="address_line1" required placeholder="No, Street, Area">

<label>Address Line 2 (optional)</label>
<input type="text" name="address_line2" placeholder="Landmark / Apartment">

<label>Postal Code (optional)</label>
<input type="text" name="postal_code" placeholder="e.g., 10300">

    </div>

    <!-- ===== CUSTOMIZATION ===== -->
    <div class="form-group">
        <label for="customization">Any Customization</label>
        <textarea id="customization" name="customization" placeholder="Special requests (optional)"></textarea>
    </div>

    <!-- ===== PRICE ===== -->
    <div class="price-box">
        <p><strong>Estimated Price:</strong> <span id="price">0</span> LKR</p>
    </div>

    <!-- ===== AVAILABILITY ===== -->
    <div id="availabilityBox" class="availability-box" style="display:none;">
        <span id="availabilityMsg"></span>
    </div>

    <!-- ===== BUTTONS ===== -->
    <div class="btn-row">
        <button type="button" id="checkBtn" class="secondary-btn">Check Availability</button>
        <button type="submit" id="bookBtn" class="primary-btn" disabled>Request Booking</button>
    </div>

</form>
</section>

<!-- ===== ALTERNATIVES ===== -->
<section id="otherCaretakers" style="display:none;">
    <h3>Other Available Caretakers</h3>
    <div class="caretaker-grid" id="altGrid"></div>
</section>

</main>

<script>
    const URLROOT = "<?= URLROOT ?>";
    const SERVICE_TYPE = "<?= htmlspecialchars($serviceType, ENT_QUOTES) ?>";
    const SERVICE_PRICE_RATES = <?= json_encode($servicePriceRates) ?>;
    const TIME_MODIFIERS = <?= json_encode($timePriceModifier) ?>;
</script>

<script src="<?= URLROOT ?>/public/js/client/c_book.js"></script>

</body>
</html>
