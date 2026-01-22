<?php
require_once APPROOT . '/core/Database.php';

class AnnouncementModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Add announcement
    public function addAnnouncement($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO announcements (title, message, target_role, created_by, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param(
            "sssi",
            $data['title'],
            $data['message'],
            $data['target_role'],
            $data['created_by']
        );
        return $stmt->execute();
    }

    // Get all announcements
    public function getAllAnnouncements() {
        $result = $this->conn->query("SELECT a.*, u.username AS created_by_name FROM announcements a 
                                      LEFT JOIN users u ON a.created_by = u.id
                                      ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Delete announcement
    public function deleteAnnouncement($id) {
        $stmt = $this->conn->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Get single announcement
    public function getAnnouncementById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update announcement
    public function updateAnnouncement($data) {
        $stmt = $this->conn->prepare(
            "UPDATE announcements SET title=?, message=?, target_role=? WHERE id=?"
        );
        $stmt->bind_param(
            "sssi",
            $data['title'],
            $data['message'],
            $data['target_role'],
            $data['id']
        );
        return $stmt->execute();
    }
public function getCaretakerAnnouncements()
{
    $stmt = $this->conn->prepare(
        "SELECT * FROM announcements
         WHERE target_role IN ('caretaker', 'All')
         ORDER BY created_at DESC"
    );
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
public function getClientAnnouncements()
{
    $stmt = $this->conn->prepare(
        "SELECT * FROM announcements
         WHERE target_role IN ('client', 'All')
         ORDER BY created_at DESC"
    );
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    public function getUserAnnouncements()
{
    $stmt = $this->conn->prepare(
        "SELECT * FROM announcements
         WHERE target_role IN ('users', 'All')
         ORDER BY created_at DESC"
    );
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

}
?>
