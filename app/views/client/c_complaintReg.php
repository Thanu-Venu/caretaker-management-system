<?php
$clientPageTitle = 'Register complaint — SmartCare';
$clientExtraCss  = ['client/c_complaintReg.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$caretakers = $caretakers ?? [];
?>

<main class="main-content">
    <header class="page-header">
        <div>
            <h1 class="page-title">Register a complaint</h1>
            <p class="text-muted">Tell us what went wrong so we can help.</p>
        </div>
        <div class="header-actions">
            <a class="btn secondary" href="<?= URLROOT ?>/public/index.php?url=Complaint/complaintlist">Complaint list</a>
        </div>
    </header>

    <section class="form-section form-section--wide">
        <form action="<?= URLROOT ?>/public/index.php?url=Complaint/store" method="POST">
            <div class="field">
                <label for="client_name">Client name</label>
                <input id="client_name" type="text" name="client_name" value="<?= htmlspecialchars((string) ($_SESSION['user']['name'] ?? '')) ?>" readonly>
            </div>
            <div class="field">
                <label for="caretaker_name">Caretaker</label>
                <select id="caretaker_name" name="caretaker_name" required>
                    <option value="">Select caregiver</option>
                    <?php foreach ($caretakers as $caretaker): ?>
                        <option value="<?= htmlspecialchars((string) ($caretaker['name'] ?? '')) ?>">
                            <?= htmlspecialchars((string) ($caretaker['name'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="category">Complaint category</label>
                <select id="category" name="category" required>
                    <option value="">Choose a category</option>
                    <option value="Caretaker Behavior">Caretaker behavior</option>
                    <option value="Service Quality">Service quality</option>
                    <option value="Late Arrival">Late arrival</option>
                    <option value="Unprofessional">Unprofessional</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="field">
                <label for="details">Complaint description</label>
                <textarea id="details" name="details" rows="5" required></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">Submit complaint</button>
            </div>
        </form>
    </section>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
