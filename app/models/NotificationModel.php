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

    private function normalizeNotifRole(string $role): string
    {
        $r = strtolower(trim($role));
        if ($r === 'manager' || $r === 'hr') {
            return 'Manager';
        }
        if ($r === 'admin') {
            return 'admin';
        }
        if ($r === 'client') {
            return 'client';
        }
        if ($r === 'caretaker' || $r === 'caregiver') {
            return 'caretaker';
        }
        return $role;
    }

    // Add notification with duplicate prevention
    public function addNotification($user_id, $user_role, $title, $message, $link = "#")
    {
        $user_role = $this->normalizeNotifRole((string)$user_role);

        $stmt = $this->conn->prepare(
            "INSERT INTO notifications (user_id, user_role, title, message, link, is_read)
             VALUES (?, ?, ?, ?, ?, 0)"
        );

        if (!$stmt) {
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
        $user_role = $this->normalizeNotifRole((string)$user_role);

                if ($user_role === 'caretaker') {
                        $stmt = $this->conn->prepare(
                                "SELECT *
                                 FROM notifications
                                 WHERE user_id = ?
                                     AND user_role IN ('caretaker', 'caregiver')
                                 ORDER BY created_at DESC
                                 LIMIT ?"
                        );
                        $stmt->bind_param("ii", $user_id, $limit);
                } else {
                        $stmt = $this->conn->prepare(
                                "SELECT *
                                 FROM notifications
                                 WHERE user_id = ?
                                     AND user_role = ?
                                 ORDER BY created_at DESC
                                 LIMIT ?"
                        );
                        $stmt->bind_param("isi", $user_id, $user_role, $limit);
                }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    // Count unread
    public function countUnread($user_id, $user_role)
    {
        $user_role = $this->normalizeNotifRole((string)$user_role);

        if ($user_role === 'caretaker') {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) AS count
                 FROM notifications
                 WHERE user_id = ?
                   AND user_role IN ('caretaker', 'caregiver')
                   AND is_read = 0"
            );
            $stmt->bind_param("i", $user_id);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) AS count
                 FROM notifications
                 WHERE user_id = ?
                   AND user_role = ?
                   AND is_read = 0"
            );
            $stmt->bind_param("is", $user_id, $user_role);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row['count'] ?? 0;
    }

    public function markAllRead($user_id, $user_role)
    {
        $user_role = $this->normalizeNotifRole((string)$user_role);

        if ($user_role === 'caretaker') {
            $stmt = $this->conn->prepare(
                "UPDATE notifications
                 SET is_read = 1
                 WHERE user_id = ? AND user_role IN ('caretaker', 'caregiver') AND is_read = 0"
            );
            $stmt->bind_param("i", $user_id);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE notifications
                 SET is_read = 1
                 WHERE user_id = ? AND user_role = ? AND is_read = 0"
            );
            $stmt->bind_param("is", $user_id, $user_role);
        }
        $stmt->execute();
        $affected = (int)$stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    // Get all admin user IDs (from users table)
    public function getAdminIds()
    {
        $result = $this->conn->query("SELECT id FROM users WHERE role = 'admin'");
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $ids = [];
        foreach ($rows as $r) {
            $ids[] = (int)$r['id'];
        }
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
        $result = $this->conn->query(
            "SELECT id
             FROM users
             WHERE LOWER(TRIM(role)) IN ('manager', 'hr')"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getNotificationById($notifId, $user_id, $user_role)
    {
        $user_role = $this->normalizeNotifRole((string)$user_role);

        if ($user_role === 'caretaker') {
            $stmt = $this->conn->prepare(
                "SELECT id, link, is_read
                 FROM notifications
                 WHERE id = ? AND user_id = ? AND user_role IN ('caretaker', 'caregiver')
                 LIMIT 1"
            );
            $stmt->bind_param("ii", $notifId, $user_id);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT id, link, is_read
                 FROM notifications
                 WHERE id = ? AND user_id = ? AND user_role = ?
                 LIMIT 1"
            );
            $stmt->bind_param("iis", $notifId, $user_id, $user_role);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }

    public function markOneRead($notifId, $user_id, $user_role)
    {
        $user_role = $this->normalizeNotifRole((string)$user_role);

        if ($user_role === 'caretaker') {
            $stmt = $this->conn->prepare(
                "UPDATE notifications
                 SET is_read = 1
                 WHERE id = ? AND user_id = ? AND user_role IN ('caretaker', 'caregiver')"
            );
            $stmt->bind_param("ii", $notifId, $user_id);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE notifications
                 SET is_read = 1
                 WHERE id = ? AND user_id = ? AND user_role = ?"
            );
            $stmt->bind_param("iis", $notifId, $user_id, $user_role);
        }
        $stmt->execute();
        $ok = ($stmt->affected_rows > 0);
        $stmt->close();
        return $ok;
    }
}
