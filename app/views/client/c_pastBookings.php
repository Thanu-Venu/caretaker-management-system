<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Past Bookings</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/client/c_pastBookings.css">
</head>

<body>
<main class="content">
    <h1>My Past Bookings</h1>

    <?php if (empty($data['bookings'])): ?>
        <p class="no-bookings">You don’t have any past bookings yet.</p>
    <?php else: ?>
        <div class="bookings-list">
            <?php foreach ($data['bookings'] as $b): ?>
                <div class="booking-card completed"
                     data-booking-id="<?= $b['booking_id'] ?>"
                     data-caretaker-id="<?= $b['caretaker_id'] ?? '' ?>">

                    <h2><?= htmlspecialchars($b['caretaker_name']) ?></h2>

                    <p><strong>Service:</strong> <?= htmlspecialchars($b['service_type']) ?></p>
                    <p><strong>Date:</strong> <?= date('Y-m-d', strtotime($b['booking_date'])) ?></p>
                    <p><strong>Time:</strong> <?= htmlspecialchars($b['preferred_time']) ?></p>
                    <p><strong>Duration:</strong> <?= $b['duration'].' '.$b['basis'] ?></p>
                    <p><strong>Status:</strong>
                        <span class="status completed">Completed</span>
                    </p>

                    <?php if ($b['rating'] !== null): ?>

    <!-- SHOW FEEDBACK -->
    <div class="feedback-display">
        <p><strong>Your Rating:</strong>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <span class="<?= $i <= $b['rating'] ? 'star-filled' : 'star-empty' ?>">★</span>
            <?php endfor; ?>
        </p>

        <p><strong>Your Feedback:</strong></p>
        <p class="feedback-text">
            <?= htmlspecialchars($b['feedback']) ?>
        </p>
    </div>

<?php else: ?>

    <!-- SHOW BUTTON -->
    <button class="feedback-btn">Give Feedback</button>

<?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<!-- ================= FEEDBACK MODAL ================= -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close feedback-close">&times;</span>

        <h2>Give Feedback</h2>

        <form method="POST" action="<?= URLROOT ?>/client/submitFeedback" id="feedbackForm">

            <input type="hidden" name="booking_Id" id="bookingId">
            <input type="hidden" name="caretaker_id" id="caretakerId">
            <input type="hidden" name="rating" id="ratingInput">

            <div class="form-group">
                <label>Rating</label>
                <div class="stars">
                    <span data-value="1">★</span>
                    <span data-value="2">★</span>
                    <span data-value="3">★</span>
                    <span data-value="4">★</span>
                    <span data-value="5">★</span>
                </div>
                <small id="ratingText">0 / 5</small>
            </div>

            <div class="form-group">
                <label>Your Feedback</label>
                <textarea name="feedback" id="feedback" rows="4" required
                          placeholder="Write your feedback here..."></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-btn">Submit Feedback</button>
                <button type="button" id="cancelFeedback" class="cancel-btn-secondary">
                    Cancel
                </button>
            </div>

        </form>
    </div>
</div>

<script src="<?= URLROOT ?>/public/js/client/c_pastBookings.js"></script>
</body>
</html>
