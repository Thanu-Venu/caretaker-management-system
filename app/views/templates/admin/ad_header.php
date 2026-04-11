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
<!-- Admin top bar (fragment — include once inside the real page <body>, after <body> opens) -->
<header class="main-header">
    <div class="left-section">
        <div class="logo-section">
            <img src="<?= URLROOT ?>/public/images/logo.jpg" class="logo" alt="">
            <span class="company-name">SmartCare</span>
        </div>
    </div>

    <div class="header-icons">

        <!-- Notifications -->
        <div class="notification-wrapper">
            <button id="notifBtn" class="notif-btn" type="button">
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
            <a href="<?= URLROOT ?>/public?url=admin/ad_settings" class="profile-link">
                <img src="<?= URLROOT ?>/public/images/profiles/<?= htmlspecialchars($profilePic) ?>" class="profile-img"
                    alt="">
                <span><?= htmlspecialchars($user_display) ?></span>
            </a>
        </div>

    </div>
</header>

<script src="<?= URLROOT ?>/public/js/notification.js"></script>
