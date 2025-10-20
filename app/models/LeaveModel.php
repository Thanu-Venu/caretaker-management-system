<?php
require_once APPROOT . '/core/Database.php';

class LeaveModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // Create Leave
    public function addLeave($data) {
        $stmt = $this->conn->prepare("INSERT INTO leaves (caretaker_id, leave_type, start_date, end_date, start_time, end_time, reason) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("issssss", $data['caretaker_id'], $data['leave_type'], $data['start_date'], $data['end_date'], $data['start_time'], $data['end_time'], $data['reason']);
        return $stmt->execute();
    }

    // Get all leaves for a caretaker
    public function getLeavesByCaretaker($caretaker_id) {
        $stmt = $this->conn->prepare("SELECT * FROM leaves WHERE caretaker_id=? ORDER BY start_date DESC");
        $stmt->bind_param("i", $caretaker_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get all leaves for admin
    public function getAllLeaves() {
        $result = $this->conn->query("SELECT l.*, c.name FROM leaves l JOIN caretakers c ON l.caretaker_id=c.id ORDER BY l.start_date DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update leave status (Admin)
    public function updateLeaveStatus($leave_id, $status) {
        $stmt = $this->conn->prepare("UPDATE leaves SET status=? WHERE leave_id=?");
        $stmt->bind_param("si", $status, $leave_id);
        return $stmt->execute();
    }

    // Delete leave
    public function deleteLeave($leave_id) {
        $stmt = $this->conn->prepare("DELETE FROM leaves WHERE leave_id=?");
        $stmt->bind_param("i", $leave_id);
        return $stmt->execute();
    }
}
?>
