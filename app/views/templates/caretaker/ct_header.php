<?php
require_once APPROOT . "/models/NotificationModel.php";

if (!isset($_SESSION['user'])) {
    header("Location: " . URLROOT . "/auth/login");
    exit;
}

$notifModel = new NotificationModel();
$user_id   = AuthSession::profileId();
$user_role = $_SESSION['legacy_role'] ?? $_SESSION['role'];

$notifications = $notifModel->getNotifications($user_id, $user_role);
$unreadCount   = $notifModel->countUnread($user_id, $user_role);

$user_display = $_SESSION['user']['name'] ?? $_SESSION['user']['username'];
$profilePic = $_SESSION['user']['profile_image'] ?? 'default.jpg';
?>

<!-- Restore collapsed rail before paint (avoids full-width flash; must run before layout CSS paints) -->
<script>
(function () {
    try {
        if (typeof localStorage !== 'undefined' && localStorage.getItem('adminSidebarCollapsed') === '1') {
            document.body.classList.add('admin-sidebar-collapsed');
        }
    } catch (e) { /* private mode / blocked storage */ }
})();
</script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare</title>

    <!-- FONT AWESOME (REQUIRED) -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- HEADER CSS -->
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
    
    <style>
        .main-content, .content {
            margin-top: 60px !important;
            min-height: calc(100vh - 60px) !important;
        }
    </style>
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
                                <?php $notifLink = !empty($n['link']) ? $n['link'] : '#'; ?>
                                <li style="<?= $n['is_read'] == 0 ? 'font-weight:bold;' : '' ?>">
                                    <a href="<?= htmlspecialchars($notifLink) ?>">
                                        <?= htmlspecialchars($n['title']) ?><br>
                                        <small><?= htmlspecialchars($n['message']) ?></small>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <div class="see-all">
                        <a href="<?= URLROOT ?>/notification/index">See all notifications</a>
                    </div>
                </div>
            </div>

            <div class="profile-wrapper">
                <button type="button" id="profileMenuBtn" class="profile-menu-trigger" aria-expanded="false"
                    aria-haspopup="true" aria-controls="caretakerProfileDropdown" title="Account menu">
                    <img src="<?= URLROOT ?>/public/uploads/<?= htmlspecialchars($profilePic) ?>" class="profile-img"
                        alt="">
                    <span class="profile-menu-name"><?= htmlspecialchars($user_display) ?></span>
                    <i class="fa-solid fa-chevron-down profile-menu-chevron" aria-hidden="true"></i>
                </button>
                <div id="caretakerProfileDropdown" class="profile-dropdown" role="menu">
                    <a href="<?= URLROOT ?>/public?url=caretaker/ct_settings" class="profile-menu-item" role="menuitem">
                        <i class="fa-solid fa-user" aria-hidden="true"></i>
                        <span>Profile</span>
                    </a>
                    <a href="<?= URLROOT ?>/index.php?url=auth/logout" class="profile-menu-item profile-menu-item--logout" role="menuitem">
                        <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <script src="<?= URLROOT ?>/public/js/notification.js"></script>
</body>

</html>