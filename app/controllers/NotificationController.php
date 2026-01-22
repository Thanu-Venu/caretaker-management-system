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
        $this->notifModel->markAsRead($this->user_id, $this->user_role);

        // Return success JSON
        echo json_encode(['status' => 'success']);
        exit;
    }

    // Optional: get unread count (for AJAX badge update)
    public function getUnreadCount() {
        $count = $this->notifModel->countUnread($this->user_id, $this->user_role);
        echo json_encode(['count' => $count]);
        exit;
    }
}
?>
