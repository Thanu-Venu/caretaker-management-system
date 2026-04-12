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

            <div class="table-wrapper">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Caretaker</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Feedback</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data['bookings'] as $b): ?>
                            <tr
                                data-booking-id="<?= $b['booking_id'] ?>"
                                data-caretaker-id="<?= $b['caretaker_id'] ?? '' ?>">
                                <td><?= htmlspecialchars($b['caretaker_name']) ?></td>
                                <td><?= htmlspecialchars($b['service_type']) ?></td>
                                <td><?= date('Y-m-d', strtotime($b['booking_date'])) ?></td>
                                <td><?= htmlspecialchars($b['preferred_time']) ?></td>
                                <td><?= (int)$b['duration'] . ' ' . htmlspecialchars($b['basis']) ?></td>

                                <td>
                                    <span class="status completed">Completed</span>
                                </td>

                                <td>
                                    <?php if ($b['rating'] !== null): ?>
                                        <div class="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="<?= $i <= (int)$b['rating'] ? 'star-filled' : 'star-empty' ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="feedback-mini">
                                            <?= htmlspecialchars($b['feedback']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="no-feedback">No feedback yet</span>
                                    <?php endif; ?>
                                </td>

                                <td class="actions">
                                    <!-- View Contact Button for completed bookings -->
                                    <a class="action-btn" id="contact-btn"
                                        href="<?= URLROOT ?>/client/c_contactCT?booking_id=<?= (int)$b['booking_id'] ?>"
                                        style="background-color: #1e88e5; color: #fff; padding: 8px 12px; text-decoration: none; display: inline-block; margin-bottom: 5px; border-radius: 10px; font-weight: 600; border: none;">
                                        View Contact
                                    </a>

                                    <?php if ($b['rating'] === null): ?>
                                        <!-- Keep the same class so your existing JS works -->
                                        <button type="button"
                                            class="feedback-btn"
                                            data-booking-id="<?= $b['booking_id'] ?>"
                                            data-caretaker-id="<?= $b['caretaker_id'] ?? '' ?>">
                                            Give Feedback
                                        </button>
                                    <?php else: ?>
                                        <span class="done-text">Submitted</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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