<?php
require_once APPROOT . "/models/NotificationModel.php";

if (!isset($_SESSION['user'])) {
    header("Location: " . URLROOT . "/auth/login");
    exit;
}

$notifModel = new NotificationModel();

$user_id = AuthSession::profileId();
$user_role = $_SESSION['user']['role'];   // ✅ FIXED

$notifications = $notifModel->getNotifications($user_id, $user_role);
$unreadCount = $notifModel->countUnread($user_id, $user_role);

$user_display = $_SESSION['user']['name']
    ?? $_SESSION['user']['username']
    ?? 'User';

$profilePic = $_SESSION['user']['profile_pic'] ?? 'default.png';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare</title>

    <!-- FONT AWESOME (REQUIRED) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- DESIGN SYSTEM - System Foundation -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/variables.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/reset.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/global.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/typography.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/utilities.css">

    <!-- DESIGN SYSTEM - Layout -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/layout/container.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/layout/grid.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/layout/sidebar.css">

    <!-- DESIGN SYSTEM - Components -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/buttons.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/forms.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/tables.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/cards.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/components/badges.css">

    <!-- DESIGN SYSTEM - Responsive -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/responsive/breakpoints.css">

    <!-- DESIGN SYSTEM - Page Standardization -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/page-fixes.css">

    <!-- DESIGN SYSTEM - Legacy Overrides (MUST BE LAST) -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/system/legacy-overrides.css">

    <!-- Legacy/Page-specific CSS (loaded but overridden by legacy-overrides.css) -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/ad_header.css">
</head>

<body>

    <header class="main-header">
        <div class="left-section">
            <div class="logo-section">
                <img src="<?= URLROOT ?>/public/images/logo.jpg" class="logo">
                <span class="company-name">SmartCare</span>
            </div>
        </div>

        <div class="header-icons">

            <!-- Notifications -->
            <div class="notification-wrapper">
                <button id="notifBtn" class="notif-btn">
                    <i class="fa-solid fa-bell"></i>
                    <span class="notif-count"><?= $unreadCount ?></span>
                </button>

                <div id="notifDropdown" class="notif-dropdown">
                    <ul>
                        <?php if (empty($notifications)): ?>
                            <li>No notifications</li>
                        <?php else: ?>
                            <?php foreach ($notifications as $n): ?>
                                <li style="<?= $n['is_read'] == 0 ? 'font-weight:bold;' : '' ?>">
                                    <?= htmlspecialchars($n['title']) ?><br>
                                    <small><?= htmlspecialchars($n['message']) ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <div class="see-all">
                        <a href="<?= URLROOT ?>/notification/index">See all notifications</a>
                    </div>
                </div>
            </div>

            <div class="header-logout">
                <a href="<?= URLROOT ?>/index.php?url=auth/logout" class="logout-btn" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>

            <!-- Profile -->
            <div class="profile-wrapper">
                <a href="http://localhost/CMA/public?url=admin/ad_settings" class="profile-link">
                    <img src="<?= URLROOT ?>/images/profiles/<?= htmlspecialchars($profilePic) ?>" class="profile-img"
                        alt="Profile">
                    <span><?= htmlspecialchars($user_display) ?></span>
                </a>
            </div>


        </div>
    </header>

    <script src="<?= URLROOT ?>/public/js/notification.js"></script>
</body>

</html>