<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<?php
$ct = $data['caretaker'];
$prefill = $data['prefill'];
$serviceOptions = $data['serviceOptions'];
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

        <!-- Caretaker Profile Summary -->
        <section class="caretaker-summary">
            <h2><?= htmlspecialchars($ct['name']) ?></h2>
            <p><strong>Service:</strong> <?= htmlspecialchars($ct['service_type']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($ct['location']) ?></p>
            <p><strong>Rating:</strong> ⭐ <?= htmlspecialchars($ct['rating'] ?? 'N/A') ?></p>
        </section>

        <!-- Base Price Display -->
        <div class="form-group">
            <label>Base Price:</label>
            <span id="basePrice">Select a service to see price</span>
            <p>Note: The base price may differ according to preferred time</p>
        </div>
    

    <!-- ✅ IMPORTANT: IDs added so JS can update the caretaker summary when selecting an alternative -->
    <section class="caretaker-summary">
        <h2 id="ctName"><?= htmlspecialchars($ct['name'] ?? 'N/A') ?></h2>
        <p><strong>Service:</strong> <span id="ctService"><?= htmlspecialchars($ct['service_type'] ?? 'N/A') ?></span></p>
        <p><strong>Location:</strong> <span id="ctLocation"><?= htmlspecialchars($ct['location'] ?? 'N/A') ?></span></p>
        <p><strong>Rating:</strong> ⭐ <span id="ctRating"><?= htmlspecialchars($ct['rating'] ?? 'N/A') ?></span></p>
    </section>

    <div class="form-group">
        <label>Base Price:</label>
        <span id="basePrice">Select a basis to see price</span>
        <p class="hint">Final price depends on duration and preferred time.</p>
    </div>

    <section class="booking-form">
        <form id="bookingForm" method="POST" action="<?= URLROOT ?>/public/?url=client/bookCaretaker">

            <!-- ✅ JS will update caretaker_id if alternative selected -->
            <input type="hidden" name="caretaker_id" id="caretaker_id" value="<?= (int)$ct['id'] ?>">

            <!-- ✅ JS uses this for pricing/service validations -->
            <input type="hidden" name="service_type" id="service_type" value="<?= htmlspecialchars($serviceType, ENT_QUOTES) ?>">

            <input type="hidden" name="total_payment" id="total_payment" value="0">
            <input type="hidden" name="end_date" id="end_date" value="">

            <!-- ===== BASIS ===== -->
            <div class="form-group">
                <label for="basis">Select Basis</label>
                <select id="basis" name="basis" required>
                    <option value="">-- Select --</option>

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

        <!-- ================= Booking Form ================= -->
        <section class="booking-form">
            <form id="bookingForm" method="POST" action="<?= URLROOT ?>/client/bookCaretaker">

                <!-- Hidden caretaker ID -->
                <input type="hidden" name="caretaker_id" value="<?= $ct['id'] ?>">
                <input type="hidden" name="service_type" value="<?= $ct['service_type'] ?>">
                <input type="hidden" name="total_payment" id="total_payment" value="0">

                <!-- Basis -->
                <div class="form-group">
                    <label for="basis">Select Basis</label>
                    <select id="basis" name="basis" required disabled>
                        <option value="">-- Select --</option>
                        <?php foreach ($serviceOptions[$ct['service_type']] as $basis): ?>
                            <option value="<?= $basis ?>" <?= ($prefill['basis'] === $basis) ? 'selected' : '' ?>>
                                <?= $basis ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="basis" value="<?= htmlspecialchars($prefill['basis']) ?>">
                </div>

                <!-- Duration -->
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="number" id="duration" name="duration" min="1" required readonly
                        value="<?= htmlspecialchars($prefill['duration']) ?>">
                </div>

                <!-- Booking Date -->
                <div class="form-group">
                    <label for="date">Preferred Date</label>
                    <input type="date" id="date" name="booking_date" required readonly
                        value="<?= htmlspecialchars($prefill['date']) ?>">
                </div>

                <!-- Preferred Time -->
                <div class="form-group">
                    <label for="preferredTime">Preferred Time</label>
                    <select id="preferredTime" name="preferred_time" required disabled>
                        <option value="">Select Time</option>
                        <?php
                        $timeOptions = [
                            "Elder Care" => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
                            "Babysitter" => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
                            "Maid" => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
                            "Disability Support" => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"]
                        ];

                        foreach ($timeOptions[$ct['service_type']] as $time): ?>
                            <option value="<?= $time ?>" <?= ($prefill['time'] === $time) ? 'selected' : '' ?>>
                                <?= $time ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="preferred_time" value="<?= htmlspecialchars($prefill['time']) ?>">
                </div>



                  <div class="form-group">
                    <label>District</label>
                                        <input type="text" name="district" value="<?= htmlspecialchars($ct['location'] ?? '') ?>" readonly>


                    <label>Street</label>
                    <input type="text" name="street" placeholder="e.g., Galle Road">

                    <label>Address Line 1</label>
                    <input type="text" name="address_line1" required placeholder="No, Street, Area">

                    <label>Address Line 2 (optional)</label>
                    <input type="text" name="address_line2" placeholder="Landmark / Apartment">

                    <label>Postal Code (optional)</label>
                    <input type="text" name="postal_code" placeholder="e.g., 10300">

                </div>

                <!-- Customization -->
                <div class="form-group">
                    <label for="customization_hours">Customization (Extra Hours)</label>
                    <small>Extra hours are charged at LKR 300 per hour </small>
                    <input type="number" id="customization_hours" name="customization_hours" min="0" max="8" value="0">
                    

                    <label for="customization">Customization Notes</label>
                    <textarea id="customization" name="customization"
                        placeholder="Only mention preferred time changes or extra hours"></textarea>
                </div>

                                <!-- Estimated Price -->
                             <div class="price-box">
                                 <p><strong>Base Price:</strong>
                                     <span id="basePriceAmount">0</span> LKR
                                 </p>
                                 <p><strong>Customization Price:</strong>
                                     <span id="customizationPrice">0</span> LKR
                                 </p>
                                 <p><strong>Estimated Price:</strong>
                                     <span id="price"><?= number_format($data['total_payment']) ?></span> LKR
                                 </p>
                             </div>


                <!-- Availability Message -->
                <div class="form-group">
                    <span id="availabilityMsg"></span>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" id="bookBtn">Request Booking</button>
                    <button type="button" class="cancel-btn" onclick="window.history.back()">Cancel</button>
                </div>

            </form>
        </section>

        <!-- Alternative Caretakers -->
        <section id="otherCaretakers">
            <h3>Other Available Caretakers</h3>
            <div class="caretaker-grid">
                <div class="caretaker-card"></div>
            </div>
        </section>
    </main>

    <script>
        const serviceType = "<?= htmlspecialchars($ct['service_type'], ENT_QUOTES) ?>";
    </script>
    <script src="<?= URLROOT; ?>/public/js/client/c_book.js"></script>
</body>

</html>