<?php
require_once APPROOT . "/models/NotificationModel.php";

// Redirect if user not logged in
if (!AuthSession::isLoggedIn()) {
    header("Location: " . URLROOT . "/auth/login");
    exit;
}

class NotificationController
{

    private $notifModel;
    private $user_id;
    private $user_role;
    private $user_display;

    private function currentCanonicalRole(): string
    {
        return AuthSession::role();
    }

    private function currentNotificationRole(): string
    {
        $role = $this->currentCanonicalRole();
        if ($role === 'manager') {
            return 'Manager';
        }
        return $role;
    }

    private function requireAllowedRole(): void
    {
        $allowed = ['admin', 'manager', 'client', 'caretaker'];
        $role = $this->currentCanonicalRole();
        if (!in_array($role, $allowed, true)) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }
    }

    private function toSafeInternalUrl(string $link): string
    {
        $link = trim($link);
        if ($link === '' || $link === '#') {
            return URLROOT . '/notification/index';
        }

        // Block absolute external URLs.
        if (preg_match('/^https?:\/\//i', $link)) {
            $urlRootHost = parse_url(URLROOT, PHP_URL_HOST);
            $linkHost = parse_url($link, PHP_URL_HOST);
            if (!$urlRootHost || !$linkHost || strcasecmp($urlRootHost, $linkHost) !== 0) {
                return URLROOT . '/notification/index';
            }
            return $link;
        }

        if (str_starts_with($link, '/')) {
            return URLROOT . $link;
        }

        return URLROOT . '/' . ltrim($link, '/');
    }

    public function __construct()
    {
        $this->notifModel = new NotificationModel();
        $this->requireAllowedRole();

        // Get logged-in user info
        $this->user_id = AuthSession::profileId();
        $this->user_role = $this->currentNotificationRole();
        $this->user_display = AuthSession::name();
    }

    // Show notifications page
    public function index()
    {
        $notifications = $this->notifModel->getNotifications($this->user_id, $this->user_role, 50);
        $unreadCount = $this->notifModel->countUnread($this->user_id, $this->user_role);

        // Pass data to the view
        $data = [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'user_display' => $this->user_display
        ];

        // Load the view
        include_once APPROOT . "/views/notifications/notification_page.php";
    }

    // AJAX: mark all notifications as read
    public function markAllRead()
    {
        header('Content-Type: application/json; charset=utf-8');
        $updated = $this->notifModel->markAllRead($this->user_id, $this->user_role);
        $count = $this->notifModel->countUnread($this->user_id, $this->user_role);
        echo json_encode(['status' => 'success', 'updated' => $updated, 'count' => $count]);
        exit;
    }

    // Optional: get unread count (for AJAX badge update)
    public function getUnreadCount()
    {
        $count = $this->notifModel->countUnread($this->user_id, $this->user_role);
        echo json_encode(['count' => $count]);
        exit;
    }

    // AJAX: mark ONE notification as read
    public function markOneRead()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'missing id']);
            exit;
        }

        $ok = $this->notifModel->markOneRead($id, $this->user_id, $this->user_role);
        $count = $this->notifModel->countUnread($this->user_id, $this->user_role);

        echo json_encode(['status' => $ok ? 'success' : 'nochange', 'count' => $count]);
        exit;
    }

    // Click: open a notification, mark read, redirect to link
    public function open($id = null)
    {
        $id = (int)$id;
        if ($id <= 0) {
            header("Location: " . URLROOT . "/notification/index");
            exit;
        }

        $note = $this->notifModel->getNotificationById($id, $this->user_id, $this->user_role);
        if (!$note) {
            header("Location: " . URLROOT . "/notification/index");
            exit;
        }

        if ((int)$note['is_read'] === 0) {
            $this->notifModel->markOneRead($id, $this->user_id, $this->user_role);
        }

        $link = (string)($note['link'] ?? '#');
        header("Location: " . $this->toSafeInternalUrl($link));
        exit;
    }
}
