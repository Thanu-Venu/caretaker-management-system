
<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Caretaker</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_book.css">
</head>

<body>
    <main class="content">
        <h1>Book Your Caretaker</h1>

        <?php
          $ct = $data['caretaker'];
        ?>



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
            <p>Warning-The base price will differ according to preffered time</p>
        </div>

          <!-- ================= Booking Form ================= -->
    <section class="booking-form">
  <form id="bookingForm" method="POST" action="<?= URLROOT ?>/client/bookCaretaker">

    <!-- Hidden caretaker ID -->
    <input type="hidden" name="caretaker_id" value="<?= $data['caretaker']['id'] ?>">

    <!-- Hidden service type -->
    <input type="hidden" name="service_type" value="<?= $data['caretaker']['service_type'] ?>">
    <input type="hidden" name="total_payment" id="total_payment" value="0">


    <!-- Basis -->
    <div class="form-group">
        <label for="basis">Select Basis</label>
        <select id="basis" name="basis" required>
            <option value="">-- Select --</option>
            <?php foreach($data['serviceOptions'][$data['caretaker']['service_type']] as $basis): ?>
                <option value="<?= $basis ?>"><?= $basis ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Duration -->
    <div class="form-group">
        <label for="duration">Duration</label>
        <input type="number" id="duration" name="duration" min="1" required>
    </div>

    <!-- Booking Date -->
    <div class="form-group">
    <label for="date">Preferred Date / Start Date</label>
    <input type="date" id="date" name="booking_date" required>

    <small class="date-note">
        Bookings can be scheduled only from <strong>5 days after today</strong>.
    </small>
</div>


    <!-- Preferred Time -->
    <div class="form-group">
        <label for="preferredTime">Preferred Time</label>
        <select id="preferredTime" name="preferred_time" required>
            <option value="">Select Time</option>
            <?php
            $timeOptions = [
                "Elder Care" => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)", "Night (6pm - 10pm)"],
                "Babysitter"   => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
                "Maid"         => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"],
                "Disability Support" => ["Full Time (8am - 5pm)", "Morning (8am - 12pm)", "Evening (1pm - 5pm)"]
            ];

            foreach($timeOptions[$data['caretaker']['service_type']] as $time): ?>
                <option value="<?= $time ?>"><?= $time ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Service Location -->
    <div class="form-group">
        <label for="serviceLocation">Service Location</label>
        <textarea id="serviceLocation" name="service_location" placeholder="Enter service location"></textarea>
    </div>

    <!-- Customization -->
    <div class="form-group">
        <label for="customization">Any Customization</label>
        <textarea id="customization" name="customization" placeholder="Enter any specific requests"></textarea>
    </div>

    <!-- Price -->
    <div class="price-box">
        <p><strong>Estimated Price:</strong>
        <span id="price">0</span> LKR
        </p>
    </div>

    <!-- Availability -->
    <div class="form-group">
        <span id="availabilityMsg"></span>
    </div>

    <!-- Submit Button -->
    <button type="submit" id="bookBtn">
        Request Booking
    </button>

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
    <script src="<?php echo URLROOT; ?>/public/js/client/c_book.js"></script>
</body>

</html>