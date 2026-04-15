<?php
$user = $data['user'] ?? null;
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$hrPageTitle = 'Profile & settings — HR';
$hrExtraCss  = ['hr/hr_settings.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

$settingsListUrl = URLROOT . '/public?url=hr/hr_settings';
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
                    src="<?= URLROOT ?>/public/images/profiles/<?= htmlspecialchars($user['profile_pic'] ?? 'default.jpg', ENT_QUOTES, 'UTF-8') ?>"
                    alt="Profile">
                <form id="hrProfileForm" method="POST" action="<?= URLROOT ?>/hrsettings/update_profile" enctype="multipart/form-data">
                    <div class="pro-section">
                        <div class="field">
                            <label for="settings-username">Full name<span class="required-mark" aria-hidden="true">*</span></label>
                            <input id="settings-username" type="text" name="username" maxlength="120"
                                value="<?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required autocomplete="name">
                        </div>

                        <div class="field">
                            <label for="settings-email">Email</label>
                            <input id="settings-email" type="email" name="email"
                                value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                readonly autocomplete="email">
                        </div>

                        <div class="field">
                            <label for="settings-phone">Phone number<span class="required-mark" aria-hidden="true">*</span></label>
                            <input id="settings-phone" type="text" name="phone" required maxlength="10" inputmode="numeric" pattern="[0-9]*"
                                placeholder="10-digit number" autocomplete="tel"
                                value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label for="profileFileInput">Profile picture</label>
                            <input type="file" name="profileFile" id="profileFileInput" accept="image/jpeg,image/png,image/gif,image/webp">
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
            <form id="hrPasswordForm" method="POST" action="<?= URLROOT ?>/hrsettings/change_password">
                <div class="field">
                    <label for="settings-current-password">Current password<span class="required-mark" aria-hidden="true">*</span></label>
                    <input id="settings-current-password" type="password" name="current_password" placeholder="Current password" required autocomplete="current-password">
                </div>

                <div class="field">
                    <label for="settings-new-password">New password<span class="required-mark" aria-hidden="true">*</span></label>
                    <input id="settings-new-password" type="password" name="new_password" placeholder="Min. 8 chars, upper, lower, number" required autocomplete="new-password">
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
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
