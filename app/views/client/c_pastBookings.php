<?php
$clientPageTitle = 'Past bookings — SmartCare';
$clientExtraCss  = ['client/c_pastBookings.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';
?>

<main class="main-content">
    <header class="page-header">
        <div>
            <h1 class="page-title">Past bookings</h1>
            <p class="text-muted">Completed services and feedback.</p>
        </div>
    </header>

    <?php if (empty($data['bookings'])): ?>
        <p class="empty">You do not have any past bookings yet.</p>
    <?php else: ?>
        <div class="table-container">
            <table class="client-table">
                <thead>
                    <tr>
                        <th>Caregiver</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Feedback</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['bookings'] as $b): ?>
                        <tr data-booking-id="<?= (int) $b['booking_id'] ?>" data-caretaker-id="<?= (int) ($b['caretaker_id'] ?? 0) ?>">
                            <td><?= htmlspecialchars((string) $b['caretaker_name']) ?></td>
                            <td><?= htmlspecialchars((string) $b['service_type']) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime((string) $b['booking_date']))) ?></td>
                            <td><?= htmlspecialchars((string) $b['preferred_time']) ?></td>
                            <td><?= (int) $b['duration'] . ' ' . htmlspecialchars((string) $b['basis']) ?></td>
                            <td><span class="status completed">Completed</span></td>
                            <td>
                                <?php if ($b['rating'] !== null): ?>
                                    <div class="rating-stars" aria-label="Rating <?= (int) $b['rating'] ?> out of 5">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?= $i <= (int) $b['rating'] ? 'star-filled' : 'star-empty' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="feedback-mini text-muted"><?= htmlspecialchars((string) ($b['feedback'] ?? '')) ?></div>
                                <?php else: ?>
                                    <span class="text-muted">No feedback yet</span>
                                <?php endif; ?>
                            </td>
                            <td class="action-cell">
                                <a class="btn tiny approve" href="<?= URLROOT ?>/client/c_contactCT?booking_id=<?= (int) $b['booking_id'] ?>">Contact</a>
                                <?php if ($b['rating'] === null): ?>
                                    <button type="button" class="btn tiny secondary feedback-btn"
                                        data-booking-id="<?= (int) $b['booking_id'] ?>"
                                        data-caretaker-id="<?= (int) ($b['caretaker_id'] ?? 0) ?>">Give feedback</button>
                                <?php else: ?>
                                    <span class="text-muted feedback-done-label">Submitted</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div id="feedbackModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="feedbackModalTitle">
        <div class="modal-content">
            <button type="button" class="close feedback-close" aria-label="Close">&times;</button>
            <h3 id="feedbackModalTitle">Give feedback</h3>
            <form method="POST" action="<?= URLROOT ?>/client/submitFeedback" id="feedbackForm">
                <input type="hidden" name="booking_Id" id="bookingId">
                <input type="hidden" name="caretaker_id" id="caretakerId">
                <input type="hidden" name="rating" id="ratingInput">
                <div class="field">
                    <label>Rating</label>
                    <div class="stars" role="group" aria-label="Star rating">
                        <span data-value="1" tabindex="0">★</span>
                        <span data-value="2" tabindex="0">★</span>
                        <span data-value="3" tabindex="0">★</span>
                        <span data-value="4" tabindex="0">★</span>
                        <span data-value="5" tabindex="0">★</span>
                    </div>
                    <small id="ratingText" class="text-muted">0 / 5</small>
                </div>
                <div class="field">
                    <label for="feedback">Your feedback</label>
                    <textarea name="feedback" id="feedback" rows="4" required placeholder="Write your feedback here..."></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" id="cancelFeedback" class="btn secondary">Cancel</button>
                    <button type="submit" class="btn">Submit feedback</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="<?= URLROOT ?>/public/js/client/c_pastBookings.js"></script>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
