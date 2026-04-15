<?php

require_once APPROOT . "/models/NotificationModel.php";

AuthSession::requireLogin();

$notifModel = new NotificationModel();
$user_id   = AuthSession::profileId();
$user_role = $_SESSION['legacy_role'] ?? ($_SESSION['user']['role'] ?? ($_SESSION['role'] ?? 'client'));

$notifications = $notifModel->getNotifications($user_id, $user_role);
$unreadCount   = $notifModel->countUnread($user_id, $user_role);

$user_display = trim((string) ($_SESSION['user']['name'] ?? ''));
if ($user_display === '') {
    $user_display = (string) ($_SESSION['user']['username'] ?? 'Client');
}

if (!empty($_SESSION['user']['profile_image'])) {
    $profilePicUrl = URLROOT . '/public/uploads/' . rawurlencode((string) $_SESSION['user']['profile_image']);
} else {
    $profilePicFile = $_SESSION['user']['profile_pic'] ?? 'default.jpg';
    $profilePicUrl  = URLROOT . '/public/images/profiles/' . rawurlencode((string) $profilePicFile);
}
?>
<script>
(function () {
    try {
        if (typeof localStorage !== 'undefined' && localStorage.getItem('adminSidebarCollapsed') === '1') {
            document.body.classList.add('admin-sidebar-collapsed');
        }
    } catch (e) { /* private mode / blocked storage */ }
})();
</script>
<header class="main-header">
    <div class="left-section">
        <div class="logo-section">
            <img src="<?= URLROOT ?>/public/images/logo.jpg" class="logo" alt="SmartCare">
            <span class="company-name">SmartCare</span>
        </div>
    </div>

    <div class="header-icons">
        <div class="notification-wrapper">
            <button id="notifBtn" class="notif-btn" type="button">
                <i class="fa-solid fa-bell"></i>
                <span class="notif-count"><?= (int) $unreadCount ?></span>
            </button>

            <div id="notifDropdown" class="notif-dropdown">
                <ul>
                    <?php if (empty($notifications)): ?>
                        <li>No notifications</li>
                    <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                            <li style="<?= $n['is_read'] == 0 ? 'font-weight:bold;' : '' ?>">
                                <?= htmlspecialchars((string) ($n['title'] ?? '')) ?><br>
                                <small><?= htmlspecialchars((string) ($n['message'] ?? '')) ?></small>
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
                aria-haspopup="true" aria-controls="clientProfileDropdown" title="Account menu">
                <img src="<?= htmlspecialchars($profilePicUrl, ENT_QUOTES, 'UTF-8') ?>" class="profile-img" alt="">
                <span class="profile-menu-name"><?= htmlspecialchars($user_display, ENT_QUOTES, 'UTF-8') ?></span>
                <i class="fa-solid fa-chevron-down profile-menu-chevron" aria-hidden="true"></i>
            </button>
            <div id="clientProfileDropdown" class="profile-dropdown" role="menu">
                <a href="<?= URLROOT ?>/public?url=client/c_settings" class="profile-menu-item" role="menuitem">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                    <span>Profile &amp; settings</span>
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
<script src="<?= URLROOT ?>/public/js/common/custom-select.js"></script>