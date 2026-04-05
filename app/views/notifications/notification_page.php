<?php
require_once APPROOT . "/models/NotificationModel.php";

AuthSession::requireLogin();

$notifModel = new NotificationModel();
$user_id = AuthSession::profileId();
$canonicalRole = strtolower(trim((string)($_SESSION['role'] ?? '')));
$allowedRoles = ['admin', 'manager', 'client', 'caretaker'];
if (!in_array($canonicalRole, $allowedRoles, true)) {
    header("Location: " . URLROOT . "/auth/login");
    exit;
}
$user_role = $canonicalRole === 'manager' ? 'Manager' : $canonicalRole;
$template_role = $canonicalRole === 'manager' ? 'hr' : $canonicalRole;

$header_file = match ($template_role) {
    'admin' => 'admin/ad_header.php',
    'client' => 'client/c_header.php',
    'caretaker' => 'caretaker/ct_header.php',
    'hr' => 'hr/hr_header.php',
    default => 'admin/ad_header.php',
};

include APPROOT . "/views/templates/" . $header_file;


$sidebar_file = match ($template_role) {
    'admin' => 'admin/ad_sidebar.php',
    'client' => 'client/c_sidebar.php',
    'caretaker' => 'caretaker/ct_sidebar.php',
    'hr' => 'hr/hr_sidebar.php',
    default => 'admin/ad_sidebar.php',
};

include APPROOT . "/views/templates/" . $sidebar_file;


// Get all notifications (or limit as needed)
$notifications = $notifModel->getNotifications($user_id, $user_role, 50);

// Display name
$user_display = $_SESSION['user']['name'] ?? $_SESSION['user']['username'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - SmartCare</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_notification.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body>

    <main class="notif-page">
        <h2>Notifications for <?= htmlspecialchars($user_display) ?></h2>
        <div class="notif-container">
            <?php if (empty($notifications)): ?>
                <p class="no-notifs">No notifications found.</p>
            <?php else: ?>
                <ul class="notif-list">
                    <?php foreach ($notifications as $n): ?>
                        <li class="notif-item <?= $n['is_read'] == 0 ? 'unread' : '' ?>">
                            <a href="<?= URLROOT ?>/notification/open/<?= (int)$n['id'] ?>">
                                <strong><?= htmlspecialchars($n['title']) ?></strong>
                                <span><?= htmlspecialchars($n['message']) ?></span>
                                <small><?= date("d M Y, H:i", strtotime($n['created_at'])) ?></small>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>