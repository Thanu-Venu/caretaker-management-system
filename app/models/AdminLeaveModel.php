<?php
require_once APPROOT . '/core/Database.php';

class AdminLeaveModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Get all leaves submitted by caretakers
    public function getAllLeaves() {
        $sql = "
    SELECT l.*, c.name AS caretaker_name
    FROM leaves l
    JOIN caretakers c ON l.user_id = c.id
    ORDER BY l.start_date DESC
";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update leave status: Approve / Reject
    public function updateLeaveStatus($leave_id, $status) {
        $stmt = $this->conn->prepare("UPDATE leaves SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $leave_id);
        return $stmt->execute();
    }
}
