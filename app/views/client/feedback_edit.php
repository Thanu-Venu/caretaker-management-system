<?php
$clientPageTitle = 'Edit feedback — SmartCare';
$clientExtraCss  = ['client/c_feedback_form.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

/** @var array $feedback Provided by FeedbackController */
?>

<main class="main-content">
    <section class="form-section form-section--wide">
        <header class="page-header">
            <h1 class="page-title">Edit feedback</h1>
        </header>
        <form action="<?= URLROOT ?>/index.php?url=feedback/update/<?= (int) ($feedback['id'] ?? 0) ?>" method="POST" class="feedback-form">
            <div class="field">
                <label for="rating">Rating</label>
                <select id="rating" name="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= (($feedback['rating'] ?? 0) == $i) ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="field">
                <label for="comment">Comment</label>
                <textarea id="comment" name="comment"><?= htmlspecialchars((string) ($feedback['comment'] ?? '')) ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">Update</button>
            </div>
        </form>
    </section>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
