<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/ad_settings.css">
    <!-- Design System Override (ensures consistency) -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/legacy-overrides.css">
</head>

<body>
    <div class="main-content">
        <h1>Profile & Settings</h1>

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
                    <form method="POST" action="<?= URLROOT ?>/adminsettings/update_profile"
                        enctype="multipart/form-data">
                        <div class="pro-section">
                            <label>Full Name
                                <input type="text" name="username"
                                    value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                            </label><br>

                            <label>Email
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                    readonly>
                            </label><br>

                            <label>Phone Number
                                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </label><br>

                            <label>Profile Picture
                                <input type="file" name="profileFile" id="profileFileInput" accept="image/*">
                            </label><br><br>

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
                <form method="POST" action="<?= URLROOT ?>/adminsettings/change_password">
                    <label>Current Password
                        <input type="password" name="current_password" placeholder="Current password" required>
                    </label><br>

                    <label>New Password
                        <input type="password" name="new_password" placeholder="New password" required>
                    </label><br>

                    <label>Confirm New Password
                        <input type="password" name="confirm_password" placeholder="Confirm password" required>
                    </label><br>

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