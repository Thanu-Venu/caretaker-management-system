<?php
$user = isset($user) && is_array($user) ? $user : ($_SESSION['user'] ?? []);

$clientPageTitle = 'Profile & settings — SmartCare';
$clientExtraCss  = ['admin/ad_settings.css', 'client/c_settings.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$settingsListUrl = URLROOT . '/public?url=client/c_settings';
$profilePic = htmlspecialchars((string) ($user['profile_image'] ?? 'default.jpg'), ENT_QUOTES, 'UTF-8');
$flash_success = $_SESSION['success'] ?? '';
$flash_error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<main class="main-content">
    <header class="page-header settings-page-header">
        <h1 class="page-title">Profile &amp; settings</h1>
    </header>

    <?php if ($flash_success): ?>
        <div class="flash-message success"><?= htmlspecialchars((string) $flash_success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
        <div class="flash-message error"><?= htmlspecialchars((string) $flash_error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="settings-container">

        <section class="card profile">
            <h3>Profile details</h3>
            <div class="profile-body">
                <img id="profileImg"
                    src="<?= URLROOT ?>/public/uploads/<?= $profilePic ?>"
                    alt="Profile">
                <form id="clientProfileForm" method="POST" action="<?= URLROOT ?>/index.php?url=Client/editClientDetails" enctype="multipart/form-data">
                    <div class="pro-section">
                        <div class="field">
                            <label for="settings-name">Full name<span class="required-mark" aria-hidden="true">*</span></label>
                            <input id="settings-name" type="text" name="name" maxlength="120"
                                value="<?= htmlspecialchars((string) ($user['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required autocomplete="name">
                        </div>

                        <div class="field">
                            <label for="settings-email">Email</label>
                            <input id="settings-email" type="email" name="email_display" readonly autocomplete="email"
                                value="<?= htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars((string) ($user['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label for="settings-phone">Phone number<span class="required-mark" aria-hidden="true">*</span></label>
                            <input id="settings-phone" type="text" name="phone" required maxlength="32" inputmode="tel" autocomplete="tel"
                                placeholder="Contact number"
                                value="<?= htmlspecialchars((string) ($user['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label for="profileFileInput">Profile picture</label>
                            <input type="file" name="profile_image" id="profileFileInput" accept="image/jpeg,image/png,image/gif,image/webp">
                            <p class="field-hint">Optional. Max 5 MB.</p>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">Save profile</button>
                            <a href="<?= htmlspecialchars($settingsListUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn-cancel">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="card">
            <h3>Change password</h3>
            <form id="clientPasswordForm" method="POST" action="<?= URLROOT ?>/index.php?url=Client/editPasswordDetails">
                <div class="field">
                    <label for="settings-current-password">Current password<span class="required-mark" aria-hidden="true">*</span></label>
                    <input id="settings-current-password" type="password" name="current_password" placeholder="Current password" required autocomplete="current-password">
                </div>

                <div class="field">
                    <label for="settings-new-password">New password<span class="required-mark" aria-hidden="true">*</span></label>
                    <input id="settings-new-password" type="password" name="new_password" placeholder="Min. 8 characters" required autocomplete="new-password">
                </div>

                <div class="field">
                    <label for="settings-confirm-password">Confirm new password<span class="required-mark" aria-hidden="true">*</span></label>
                    <input id="settings-confirm-password" type="password" name="confirm_password" placeholder="Re-enter new password" required autocomplete="new-password">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">Update password</button>
                    <a href="<?= htmlspecialchars($settingsListUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </section>

    </div>
</main>

<script>
(function () {
    var profileFileInput = document.getElementById('profileFileInput');
    var profileImg = document.getElementById('profileImg');

    if (profileFileInput) {
        profileFileInput.addEventListener('change', function (event) {
            var file = event.target.files[0];
            if (!file) {
                return;
            }
            if (!file.type.startsWith('image/')) {
                window.alert('Please select a valid image file');
                this.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                window.alert('File size must be less than 5MB');
                this.value = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                if (profileImg) {
                    profileImg.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        });
    }
})();
</script>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
