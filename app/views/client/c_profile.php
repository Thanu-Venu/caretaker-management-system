<?php
$clientPageTitle = 'Edit profile — SmartCare';
$clientExtraCss  = ['client/c_profile.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$user = $data['user'] ?? ($_SESSION['user'] ?? []);
?>

<main class="main-content">
    <header class="page-header">
        <div>
            <h1 class="page-title">Edit profile</h1>
            <p class="text-muted">View your account details (read-only demo fields).</p>
        </div>
    </header>

    <section class="form-section form-section--wide">
        <form id="profileForm">
            <div class="form-grid">
                <div class="field">
                    <label for="profileName">Full name</label>
                    <input id="profileName" type="text" value="<?= htmlspecialchars((string) ($user['name'] ?? '')) ?>" readonly>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="<?= htmlspecialchars((string) ($user['email'] ?? '')) ?>" readonly>
                </div>
                <div class="field full">
                    <label for="address">Address</label>
                    <input type="text" id="address">
                </div>
                <div class="field full">
                    <label for="contact">Contact number</label>
                    <input type="text" id="contact">
                </div>
                <div class="field full">
                    <label for="city">City</label>
                    <select id="city">
                        <option selected>Colombo</option>
                        <option>Jaffna</option>
                        <option>Matara</option>
                        <option>Vavuniya</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn secondary">Cancel</button>
                <button type="submit" class="submit-btn">Save</button>
            </div>
        </form>
    </section>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
