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
        $id = $_GET['id'] ?? '';
        $name = $_GET['name'] ?? 'Unknown';
        $service = $_GET['service'] ?? 'Not Selected';
        $location = $_GET['location'] ?? 'Not Provided';
        $rating = $_GET['rating'] ?? '';
        ?>

        <!-- Caretaker Profile Summary -->
        <section class="caretaker-summary">
            <h2><?php echo htmlspecialchars($name); ?></h2>
            <p><strong>Service:</strong> <?php echo htmlspecialchars($service); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($location); ?></p>
            <p><strong>Rating:</strong> ⭐ <?php echo htmlspecialchars($rating); ?></p>
        </section>

        <!-- Base Price Display -->
        <div class="form-group">
            <label>Base Price:</label>
            <span id="basePrice">Select a service to see price</span>
            <p>Warning-The base price will differ according to preffered time</p>
        </div>

        <!-- Booking Form -->
        <section class="booking-form">
            <form id="bookingForm">

                <!-- Basis -->
                <div class="form-group">
                    <label for="basis">Select Basis</label>
                    <select id="basis" name="basis" required>
                        <option value="">-- Select --</option>
                    </select>
                </div>

                <!-- Duration -->
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="number" id="duration" name="duration" min="1" placeholder="Enter duration" required>
                </div>

                <!-- Date -->
                <div class="form-group">
                    <label for="date">Preferred Date</label>
                    <input type="date" id="date" name="date" required>
                </div>

                <!-- Time -->
                <div class="form-group">
                    <label for="preferredTime">Preferred Time</label>
                    <select id="preferredTime" required>
                        <option value="">Select Time</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="customization">Any Customization</label>
                    <textarea id="customization" name="customization" placeholder="Enter any specific requests"></textarea>
                </div>

                <!-- Price Calculation -->
                <div class="price-box">
                    <p><strong>Estimated Price:</strong> <span id="price">0</span> LKR</p>
                </div>
                <div class="form-group">
                    <span id="availabilityMsg"></span>
                </div>

                <button id="bookBtn" disabled>Request Booking</button>
        </section>
        <!-- Alternative Caretakers -->
        <section id="otherCaretakers">
            <h3>Other Available Caretakers</h3>
            <div class="caretaker-grid">
        <div class="caretaker-card"></div>
        </div>
        </section>

        </form>
        </section>
    </main>

    <script>
        const serviceType = "<?php echo $service; ?>";
    </script>
    <script src="<?php echo URLROOT; ?>/public/js/client/c_book.js"></script>
</body>

</html>