<?php
require_once APPROOT . "/models/NotificationModel.php";

if (!isset($_SESSION['user'])) {
    header("Location: " . URLROOT . "/auth/login");
    exit;
}

$notifModel = new NotificationModel();

$user_id = $_SESSION['user']['id'];
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

    <!-- HEADER CSS -->
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
                    <?php if ($unreadCount > 0): ?>
                        <span class="notif-count"><?= $unreadCount ?></span>
                    <?php endif; ?>
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