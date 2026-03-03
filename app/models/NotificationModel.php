<?php
require_once APPROOT . '/core/Database.php';

class NotificationModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Add notification with duplicate prevention
    public function addNotification($user_id, $user_role, $title, $message, $link = "#")
    {
        // Verify the table has the correct columns
        $stmt = $this->conn->prepare("
            INSERT INTO notifications 
            (user_id, user_role, title, message, link, is_read)
            VALUES (?, ?, ?, ?, ?, 0)
        ");
        
        if (!$stmt) {
            // If table doesn't have these columns, log error and return false
            error_log("Notification insert error: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("issss", $user_id, $user_role, $title, $message, $link);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Get notifications
    public function getNotifications($user_id, $user_role, $limit = 5)
    {
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
    public function countUnread($user_id, $user_role)
    {
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

    // Get all admin user IDs (from users table)
    public function getAdminIds()
    {
        $result = $this->conn->query("SELECT id FROM users WHERE role = 'admin'");
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $ids = [];
        foreach ($rows as $r)
            $ids[] = (int) $r['id'];
        return $ids;
    }

    // Send a notification to ALL admins (each admin gets their own row)
    public function notifyAdmins($title, $message, $link = "#")
    {
        $adminIds = $this->getAdminIds();

        foreach ($adminIds as $adminId) {
            $this->addNotification($adminId, 'admin', $title, $message, $link);
        }
    }

    public function getHRUsers()
{
    $result = $this->conn->query("SELECT id FROM users WHERE role = 'Manager'");
    return $result->fetch_all(MYSQLI_ASSOC);
}
public function getNotificationById($notifId, $user_id, $user_role)
{
    $stmt = $this->conn->prepare("
        SELECT id, link, is_read
        FROM notifications
        WHERE id = ? AND user_id = ? AND user_role = ?
        LIMIT 1
    ");
    $stmt->bind_param("iis", $notifId, $user_id, $user_role);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row;
}

public function markOneRead($notifId, $user_id, $user_role)
{
    $stmt = $this->conn->prepare("
        UPDATE notifications
        SET is_read = 1
        WHERE id = ? AND user_id = ? AND user_role = ?
    ");
    $stmt->bind_param("iis", $notifId, $user_id, $user_role);
    $stmt->execute();
    $ok = ($stmt->affected_rows > 0);
    $stmt->close();
    return $ok;
}
}