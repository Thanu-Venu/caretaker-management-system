<?php
$clientPageTitle = 'Add feedback — SmartCare';
$clientExtraCss  = ['client/c_feedback_form.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';
?>

<main class="main-content">
    <section class="form-section form-section--wide">
        <header class="page-header">
            <h1 class="page-title">Give feedback</h1>
        </header>
        <form action="<?= URLROOT ?>/index.php?url=feedback/store" method="POST" class="feedback-form">
            <div class="field">
                <label for="caretaker_id">Caregiver ID</label>
                <input id="caretaker_id" type="number" name="caretaker_id" required>
            </div>
            <div class="field">
                <label for="rating">Rating</label>
                <select id="rating" name="rating" required>
                    <option value="">Select</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="field">
                <label for="comment">Comment</label>
                <textarea id="comment" name="comment" required></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">Submit</button>
            </div>
        </form>
    </section>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
