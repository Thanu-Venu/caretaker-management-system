<?php
require_once APPROOT . '/models/NotificationModel.php';

AuthSession::requireLogin();

$canonicalRole = AuthSession::role();
$allowedRoles = ['admin', 'manager', 'client', 'caretaker'];
if (!in_array($canonicalRole, $allowedRoles, true)) {
    header('Location: ' . URLROOT . '/auth/login');
    exit;
}

$user_id = AuthSession::profileId();
$user_role = $canonicalRole === 'manager' ? 'Manager' : $canonicalRole;
$template_role = $canonicalRole === 'manager' ? 'hr' : $canonicalRole;

$header_file = match ($template_role) {
    'admin' => 'admin/ad_header.php',
    'client' => 'client/c_header.php',
    'caretaker' => 'caretaker/ct_header.php',
    'hr' => 'hr/hr_header.php',
    default => 'admin/ad_header.php',
};

$sidebar_file = match ($template_role) {
    'admin' => 'admin/ad_sidebar.php',
    'client' => 'client/c_sidebar.php',
    'caretaker' => 'caretaker/ct_sidebar.php',
    'hr' => 'hr/hr_sidebar.php',
    default => 'admin/ad_sidebar.php',
};

if (isset($data) && is_array($data) && array_key_exists('notifications', $data)) {
    $notifications = is_array($data['notifications']) ? $data['notifications'] : [];
} else {
    $notifModel = new NotificationModel();
    $notifications = $notifModel->getNotifications($user_id, $user_role, 50);
}

$user_display = '';
if (isset($data) && is_array($data) && isset($data['user_display'])) {
    $user_display = trim((string) $data['user_display']);
}
if ($user_display === '') {
    $user_display = trim((string) (AuthSession::name() ?: ($_SESSION['user']['username'] ?? 'User')));
}

ob_start();
?>
        <h2>Notifications for <?= htmlspecialchars($user_display, ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="notif-container">
            <?php if (empty($notifications)): ?>
                <p class="no-notifs">No notifications found.</p>
            <?php else: ?>
                <ul class="notif-list">
                    <?php foreach ($notifications as $n): ?>
                        <li class="notif-item <?= (int) ($n['is_read'] ?? 0) === 0 ? 'unread' : '' ?>">
                            <a href="<?= URLROOT ?>/notification/open/<?= (int) $n['id'] ?>">
                                <strong><?= htmlspecialchars((string) ($n['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                                <span><?= htmlspecialchars((string) ($n['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                <small><?= date('d M Y, H:i', strtotime((string) ($n['created_at'] ?? 'now'))) ?></small>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
<?php
$notifInnerHtml = trim(ob_get_clean());

if ($template_role === 'client') {
    $clientPageTitle = 'Notifications — SmartCare';
    $clientExtraCss = ['admin/ad_notification.css'];
    require_once APPROOT . '/views/templates/client/client_layout_head.php';
    require_once APPROOT . '/views/templates/' . $header_file;
    require_once APPROOT . '/views/templates/' . $sidebar_file;
    echo '<main class="main-content notif-page">' . "\n" . $notifInnerHtml . "\n</main>\n";
    require_once APPROOT . '/views/templates/client/client_layout_close.php';
    return;
}

if ($template_role === 'hr') {
    $hrPageTitle = 'Notifications — SmartCare';
    $hrExtraCss = ['admin/ad_notification.css'];
    require_once APPROOT . '/views/templates/hr/hr_layout_head.php';
    require_once APPROOT . '/views/templates/' . $header_file;
    require_once APPROOT . '/views/templates/' . $sidebar_file;
    echo '<main class="main-content notif-page">' . "\n" . $notifInnerHtml . "\n</main>\n";
    require_once APPROOT . '/views/templates/hr/hr_layout_close.php';
    return;
}

if ($template_role === 'admin') {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications — SmartCare</title>
    <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/ad_notification.css">
</head>
<body>
    <?php include_once APPROOT . '/views/templates/' . $header_file; ?>
    <?php include_once APPROOT . '/views/templates/' . $sidebar_file; ?>
    <main class="main-content notif-page">
<?= $notifInnerHtml ?>

    </main>
</body>
</html>
    <?php
    return;
}

// Caretaker: ct_header / ct_sidebar are full mini-documents; keep prior include pattern only for this role.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications — SmartCare</title>
    <link href="<?= URLROOT ?>/public/vendor/font-awesome/css/all.min.css" rel="stylesheet">
    <link href="<?= URLROOT ?>/public/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/admin-ui.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_sidebar.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/ad_notification.css">
</head>
<body>
    <?php include APPROOT . '/views/templates/' . $header_file; ?>
    <?php include APPROOT . '/views/templates/' . $sidebar_file; ?>
    <main class="main-content notif-page">
<?= $notifInnerHtml ?>

    </main>
</body>
</html>
