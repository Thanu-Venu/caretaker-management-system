<?php
$user = $data['user'] ?? null;
$flash_success = $_SESSION['flash_success'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';

// Clear flash messages after showing
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/ad_settings.css">
    <!-- Design System Override (ensures consistency) -->
</head>

<body>
    <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
    <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
    <div class="main-content">
        <section class="page-header settings-page-header">
            <h1 class="page-title">Profile &amp; Settings</h1>
        </section>

        <?php if ($flash_success): ?>
            <div class="flash-message success"><?= htmlspecialchars($flash_success) ?></div>
        <?php endif; ?>
        <?php if ($flash_error): ?>
            <div class="flash-message error"><?= htmlspecialchars($flash_error) ?></div>
        <?php endif; ?>

        <div class="settings-container">

            <!-- Profile Details -->
            <section class="card profile">
                <h3>Profile Details</h3>
                <div class="profile-body">
                    <img id="profileImg"
                        src="<?= URLROOT ?>/public/images/profiles/<?= htmlspecialchars($user['profile_pic'] ?? 'admin.png') ?>"
                        alt="Profile">
                    <form id="adminProfileForm" method="POST" action="<?= URLROOT ?>/adminsettings/update_profile"
                        enctype="multipart/form-data" data-admin-validate>
                        <div class="pro-section">
                            <div class="field">
                                <label for="settings-username">Full name<span class="required-mark" aria-hidden="true">*</span></label>
                                <input id="settings-username" type="text" name="username" maxlength="120"
                                    value="<?= htmlspecialchars($user['username'] ?? '') ?>" required autocomplete="name">
                            </div>

                            <div class="field">
                                <label for="settings-email">Email</label>
                                <input id="settings-email" type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                    readonly autocomplete="email">
                            </div>

                            <div class="field">
                                <label for="settings-phone">Phone number<span class="required-mark" aria-hidden="true">*</span></label>
                                <input id="settings-phone" type="text" name="phone" required maxlength="10" inputmode="numeric" pattern="[0-9]*"
                                    placeholder="10-digit number" autocomplete="tel"
                                    value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>

                            <div class="field">
                                <label for="profileFileInput">Profile picture</label>
                                <input type="file" name="profileFile" id="profileFileInput" accept="image/jpeg,image/png,image/gif,image/webp">
                                <p class="field-hint">Optional. Max 5 MB.</p>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save">Save Profile</button>
                                <a href="<?= URLROOT ?>/adminsettings" class="btn-cancel">Cancel</a>
                            </div>

                        </div>
                    </form>
                </div>
            </section>

            <!-- Change Password -->
            <section class="card">
                <h3>Change Password</h3>
                <form id="adminPasswordForm" method="POST" action="<?= URLROOT ?>/adminsettings/change_password" data-admin-validate>
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
                        <button type="submit" class="btn-save">Update Password</button>
                        <a href="<?= URLROOT ?>/adminsettings" class="btn-cancel">Cancel</a>
                    </div>

                </form>
            </section>

        </div>
    </div>

    <script>
        // Real-time profile picture preview
        const profileFileInput = document.getElementById('profileFileInput');
        const profileImg = document.getElementById('profileImg');
        const navbarProfileImg = document.querySelector('.profile-img');

        if (profileFileInput) {
            profileFileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                
                if (file) {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Please select a valid image file');
                        this.value = '';
                        return;
                    }

                    // Validate file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size must be less than 5MB');
                        this.value = '';
                        return;
                    }

                    // Create preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imageUrl = e.target.result;
                        
                        // Update settings page profile picture
                        if (profileImg) {
                            profileImg.src = imageUrl;
                        }
                        
                        // Note: We intentionally do NOT update the navbar image here.
                        // The navbar will cleanly update itself when the admin clicks
                        // "Save Profile" and the page successfully reloads.
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Update navbar after form submission
        const profileForm = document.querySelector('form[action*="update_profile"]');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                // Form will submit and page will reload
                // Session will be updated by the server
                // Navbar profile pic will automatically update on page reload
            });
        }
    </script>
</body>

</html>
