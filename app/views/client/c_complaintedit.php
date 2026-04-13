<?php
$clientPageTitle = 'Edit complaint — SmartCare';
$clientExtraCss  = ['client/c_complaintedit.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

/** @var array $complaint Set by ComplaintController::clientEdit */
?>

<main class="main-content">
    <header class="page-header">
        <div>
            <h1 class="page-title">Edit complaint</h1>
            <p class="text-muted">Update the details of your complaint.</p>
        </div>
    </header>

    <section class="form-section form-section--wide">
        <form method="POST" action="<?= URLROOT ?>/index.php?url=Complaint/clientUpdate/<?= (int) ($complaint['Id'] ?? 0) ?>">
            <input type="hidden" name="Id" value="<?= (int) ($complaint['Id'] ?? 0) ?>">
            <div class="field">
                <label for="details">Details</label>
                <textarea id="details" name="details" rows="6" required><?= htmlspecialchars((string) ($complaint['details'] ?? '')) ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">Update complaint</button>
            </div>
        </form>
    </section>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
