<?php
require_once APPROOT . "/models/NotificationModel.php";

// Redirect if user not logged in
if (!isset($_SESSION['user'])) {
    header("Location: " . URLROOT . "/auth/login");
    exit;
}

class NotificationController {

    private $notifModel;
    private $user_id;
    private $user_role;
    private $user_display;

    public function __construct() {
        $this->notifModel = new NotificationModel();

        // Get logged-in user info
        $this->user_id = $_SESSION['user']['id'];
        $this->user_role = $_SESSION['user']['role'] ?? 'client'; // default to client if missing
        $this->user_display = $_SESSION['user']['name'] ?? $_SESSION['user']['username'];
    }

    // Show notifications page
    public function index() {
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
   public function markAllRead() {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'success', 'count' => 0]);
    exit;
}

    // Optional: get unread count (for AJAX badge update)
    public function getUnreadCount() {
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

    $link = $note['link'] ?? '#';
    if ($link === '#' || trim($link) === '') {
        header("Location: " . URLROOT . "/notification/index");
        exit;
    }

    header("Location: " . $link);
    exit;
}
}
?>
