<?php
require_once APPROOT . '/core/Database.php';

class NotificationModel {
       private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Add notification
    public function addNotification($user_id, $user_role, $title, $message, $link = "#") {
        $stmt = $this->conn->prepare("
            INSERT INTO notifications 
            (user_id, user_role, title, message, link, is_read)
            VALUES (?, ?, ?, ?, ?, 0)
        ");
        $stmt->bind_param("issss", $user_id, $user_role, $title, $message, $link);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Get notifications
    public function getNotifications($user_id, $user_role, $limit = 5) {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM notifications
            WHERE user_id = ?
              AND user_role = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("isi", $user_id, $user_role, $limit);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // Count unread
    public function countUnread($user_id, $user_role) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS count
            FROM notifications
            WHERE user_id = ?
              AND user_role = ?
              AND is_read = 0
        ");
        $stmt->bind_param("is", $user_id, $user_role);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row['count'] ?? 0;
    }

    // Mark all as read
    public function markAsRead($user_id, $user_role) {
        $stmt = $this->conn->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE user_id = ?
              AND user_role = ?
        ");
        $stmt->bind_param("is", $user_id, $user_role);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
