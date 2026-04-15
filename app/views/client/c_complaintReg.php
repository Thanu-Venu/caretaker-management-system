<?php
$clientPageTitle = 'Register Complaint — SmartCare';
$clientExtraCss  = ['client/c_complaintReg.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$caretakersList = $data['caretakers'] ?? ($caretakers ?? []);
?>

<main class="main-content complaint-page">
    <header class="page-header">
        <div>
            <h1 class="page-title">Register a complaint</h1>
            <p class="text-muted">Tell us what happened and we will review it.</p>
        </div>
        <a class="link-btn" href="<?= URLROOT ?>/public/index.php?url=Complaint/complaintlist">View complaint list</a>
    </header>

    <section class="complaint-section" aria-label="Complaint form">
        <form action="<?= URLROOT ?>/public/index.php?url=Complaint/store" method="POST">
            <label for="client_name">Client name</label>
            <input id="client_name" type="text" name="client_name" value="<?= htmlspecialchars((string)($_SESSION['user']['name'] ?? '')) ?>" readonly>

            <label for="caretaker_name">Caretaker</label>
            <select id="caretaker_name" name="caretaker_name" required>
                <option value="">Select caregiver</option>
                <?php foreach ($caretakersList as $caretaker): ?>
                    <option value="<?= htmlspecialchars((string)($caretaker['name'] ?? '')) ?>">
                        <?= htmlspecialchars((string)($caretaker['name'] ?? '')) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="category">Complaint category</label>
            <select id="category" name="category" required>
                <option value="">Choose a category</option>
                <option value="Caretaker Behavior">Caretaker behavior</option>
                <option value="Service Quality">Service quality</option>
                <option value="Late Arrival">Late arrival</option>
                <option value="Unprofessional">Unprofessional</option>
                <option value="Other">Other</option>
            </select>

            <label for="details">Complaint description</label>
            <textarea id="details" name="details" rows="5" required></textarea>

            <div class="form-actions">
                <button type="submit">Submit complaint</button>
                <a class="btn-cancel" href="<?= URLROOT ?>/public/index.php?url=Complaint/complaintlist">Cancel</a>
            </div>
        </form>
    </section>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
