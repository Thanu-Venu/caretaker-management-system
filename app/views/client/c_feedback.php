<?php
$clientPageTitle = 'Feedback — SmartCare';
$clientExtraCss  = ['client/c_feedback_table.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$feedbacks = $data['feedbacks'] ?? [];
?>

<main class="main-content">
    <header class="page-header">
        <div>
            <h1 class="page-title">My feedback</h1>
            <p class="text-muted">Ratings and comments you submitted after completed services.</p>
        </div>
    </header>

    <div class="client-feedback-hint-box" role="note">
        To give feedback, open <a href="<?= URLROOT ?>/public?url=client/c_pastBookings">Past bookings</a> and use <strong>Give feedback</strong> on a completed visit.
    </div>

    <div class="table-container">
        <table class="client-table">
            <thead>
                <tr>
                    <th>Caregiver</th>
                    <th>Rating</th>
                    <th>Feedback</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($feedbacks)): ?>
                    <tr>
                        <td colspan="4" class="empty">No feedback submitted yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($feedbacks as $fb): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) ($fb['caretaker_name'] ?? '')) ?></td>
                            <td>
                                <div class="rating-display">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="<?= $i <= (int) ($fb['rating'] ?? 0) ? 'star-filled' : 'star-empty' ?>">★</span>
                                    <?php endfor; ?>
                                    <span class="rating-text">(<?= (int) ($fb['rating'] ?? 0) ?>/5)</span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars((string) ($fb['feedback'] ?? '')) ?></td>
                            <td><?= htmlspecialchars(date('M j, Y', strtotime((string) ($fb['created_at'] ?? 'now')))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
