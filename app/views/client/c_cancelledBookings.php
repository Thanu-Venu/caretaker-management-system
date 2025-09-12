<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelled Bookings</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_cancelledBookings.css">
</head>

<body>
<main class="content">
    <h1>My Cancelled Bookings</h1>

    <p id="noCancelled" class="no-cancelled" style="display: none;">
        You don’t have any cancelled bookings yet.
    </p>

    <!-- Cancelled Bookings List -->
    <div class="cancelled-list">

        <!-- Example Booking Card -->
        <div class="cancelled-card">
            <h2>Jane Doe</h2>
            <p><strong>Service:</strong> Babysitting</p>
            <p><strong>Date:</strong> 2025-08-20</p>
            <p><strong>Time:</strong> Morning</p>
            <p><strong>Duration:</strong> 3 Hours</p>
            <p><strong>Status:</strong> <span class="status cancelled">Cancelled</span></p>
            <p class="cancel-reason"><strong>Reason:</strong> Client was not available</p>
        </div>

        <div class="cancelled-card">
            <h2>Sam Silva</h2>
            <p><strong>Service:</strong> Elder Care</p>
            <p><strong>Date:</strong> 2025-07-15</p>
            <p><strong>Time:</strong> Evening</p>
            <p><strong>Duration:</strong> 2 Days</p>
            <p><strong>Status:</strong> <span class="status cancelled">Cancelled</span></p>
            <p class="cancel-reason"><strong>Reason:</strong> Caretaker emergency</p>
        </div>

    </div> 
</main>

<script src="<?php echo URLROOT; ?>/public/js/client/c_cancelledBookings.js"></script>
</body>
</html>
