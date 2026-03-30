<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<?php
$ct = $data['caretaker'] ?? [];
$prefill = $data['prefill'] ?? [];
$serviceOptions = $data['serviceOptions'] ?? [];
$total_payment = $data['total_payment'] ?? 0;

$serviceType = $ct['service_type'] ?? '';
$bases = $serviceOptions[$serviceType] ?? [];

$timeOptions = [
    "Elder Care" => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
    "Babysitter" => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
    "Maid" => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"]
];
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
        <h1>Book Your Caretaker</h1>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 12px; margin: 15px 0; border: 1px solid #f5c6cb; border-radius: 4px;">
                <strong>Error:</strong> <?php echo htmlspecialchars($_SESSION['error']);
                                        unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 12px; margin: 15px 0; border: 1px solid #c3e6cb; border-radius: 4px;">
                <strong>Success:</strong> <?php echo htmlspecialchars($_SESSION['success']);
                                            unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- ✅ Caretaker Profile Summary (IDs for JS updates) -->
        <section class="caretaker-summary">
            <h2 id="ctName"><?= htmlspecialchars($ct['name'] ?? 'N/A') ?></h2>
            <p><strong>Service:</strong> <span id="ctService"><?= htmlspecialchars($serviceType ?: 'N/A') ?></span></p>
            <p><strong>Location:</strong> <span id="ctLocation"><?= htmlspecialchars($ct['location'] ?? 'N/A') ?></span></p>
            <p><strong>Rating:</strong> ⭐ <span id="ctRating"><?= htmlspecialchars($ct['rating'] ?? 'N/A') ?></span></p>
        </section>

        <div class="form-group">
            <label>Base Price:</label>
            <span id="basePrice">Select a basis to see price</span>
            <p class="hint">Final price depends on duration and preferred time.</p>
        </div>

        <section class="booking-form">
            <form id="bookingForm" method="POST" action="<?= URLROOT; ?>/client/bookCaretaker">

                <!-- Hidden caretaker ID (JS updates this if alternative selected) -->
                <input type="hidden" name="caretaker_id" id="caretaker_id" value="<?= (int)($ct['id'] ?? 0) ?>">

                <!-- Hidden service type -->
                <input type="hidden" name="service_type" id="service_type" value="<?= htmlspecialchars($serviceType, ENT_QUOTES) ?>">

                <!-- Price fields -->
                <input type="hidden" name="total_payment" id="total_payment" value="<?= htmlspecialchars((string)$total_payment, ENT_QUOTES) ?>">

                <!-- ===== BASIS ===== -->
                <div class="form-group">
                    <label for="basis">Select Basis</label>

                    <!-- Editable select for UI -->
                    <select id="basis" required disabled>
                        <option value="">-- Select --</option>
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

                    <!-- Hidden real input to submit -->
                    <input type="hidden" name="basis" id="basis_hidden" value="<?= htmlspecialchars($prefill['basis'] ?? '', ENT_QUOTES) ?>">
                </div>

                <!-- ===== DURATION ===== -->
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="number" id="duration" name="duration" min="1" required
                        readonly
                        value="<?= htmlspecialchars((string)($prefill['duration'] ?? 1), ENT_QUOTES) ?>">
                    <small id="durationHint">Number of booking units</small>
                </div>

                <!-- ===== DATE ===== -->
                <div class="form-group">
                    <label for="date">Preferred Date</label>
                    <input type="date" id="date" name="booking_date" required
                        readonly
                        value="<?= htmlspecialchars($prefill['date'] ?? '', ENT_QUOTES) ?>">
                </div>

                <!-- ===== TIME ===== -->
                <div class="form-group">
                    <label for="preferredTime" id="preferredTimeLabel">Preferred Time</label>

                    <div id="timeContainer">
                        <select id="preferredTime" required disabled>
                            <option value="">Select Time</option>
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

                    <!-- Hidden real input to submit -->
                    <input type="hidden" name="preferred_time" id="preferred_time_hidden"
                        value="<?= htmlspecialchars($prefill['time'] ?? '', ENT_QUOTES) ?>">
                </div>

                <!-- ===== ADDRESS ===== -->
                <div class="form-group">
                    <label>District</label>
                    <input type="text" name="district" value="<?= htmlspecialchars($ct['location'] ?? '', ENT_QUOTES) ?>" readonly>

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
                    <label for="customization_hours">Customization (Extra Hours)</label>
                    <label for="customization_apply">Extra hours apply</label>
                    <select id="customization_apply" name="customization_apply">
                        <option value="once" <?= (($prefill['customization_apply'] ?? 'once') === 'once') ? 'selected' : '' ?>>
                            One-time (only one day)
                        </option>
                        <option value="per_unit" <?= (($prefill['customization_apply'] ?? 'once') === 'per_unit') ? 'selected' : '' ?>>
                            For every booking unit (duration)
                        </option>
                    </select>
                    <small>If duration is 3 (Daily), extra hours will be charged 3 times when you choose “For every booking unit”.</small>
                    <input type="number" id="customization_hours" name="customization_hours" min="0" max="8"
                        value="<?= htmlspecialchars((string)($prefill['customization_hours'] ?? 0), ENT_QUOTES) ?>">
                    <small>Extra hours are charged at LKR 300 per hour</small>

                    <label for="customization">Customization Notes</label>
                    <textarea id="customization" name="customization"
                        placeholder="Only mention preferred time changes or extra hours"></textarea>
                </div>

                <!-- ===== PRICE BOX ===== -->
                <div class="price-box">
                    <p><strong>Base Price:</strong> <span id="basePriceAmount">0</span> LKR</p>
                    <p><strong>Customization Price:</strong> <span id="customizationPrice">0</span> LKR</p>
                    <p><strong>Estimated Price:</strong>
                        <span id="price"><?= number_format((float)$total_payment, 2) ?></span> LKR
                    </p>
                </div>

                <!-- Availability Message -->
                <div class="form-group">
                    <span id="availabilityMsg"></span>
                </div>

                <button type="submit" id="bookBtn">Request Booking</button>
            </form>
        </section>

        <!-- Alternative Caretakers (your JS can fill this) -->
        <section id="otherCaretakers">
            <h3>Other Available Caretakers</h3>
            <div class="caretaker-grid" id="caretakerGrid">
                <!-- JS will populate cards here -->
            </div>
        </section>
    </main>

    <script>
        const serviceType = "<?= htmlspecialchars($serviceType, ENT_QUOTES) ?>";
        const basisValue = "<?= htmlspecialchars($prefill['basis'] ?? '', ENT_QUOTES) ?>";
        const preferredTimeValue = "<?= htmlspecialchars($prefill['time'] ?? '', ENT_QUOTES) ?>";

        // Ensure hidden fields match selected values (even though selects are disabled)
        document.getElementById('basis_hidden').value = basisValue;
        document.getElementById('preferred_time_hidden').value = preferredTimeValue;
    </script>

    <script src="<?= URLROOT; ?>/public/js/client/c_book.js"></script>
</body>

</html>