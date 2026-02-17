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
                                <input type="file" name="profileFile" accept="image/*">

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
</body>

</html>