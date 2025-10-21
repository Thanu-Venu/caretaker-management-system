<?php
require_once APPROOT . '/core/Database.php';

class LeaveModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    // 🔹 Get all leaves for a user
    public function getLeavesByUser($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM leaves WHERE user_id=? ORDER BY start_date DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 🔹 Get single leave
    public function getLeaveById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM leaves WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    // 🔹 Add new leave
    public function addLeave($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO leaves (user_id, leave_type, start_date, end_date, start_time, end_time, reason, can_edit_until, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
        );
        $stmt->bind_param(
            "isssssss",
            $data['user_id'],
            $data['leave_type'],
            $data['start_date'],
            $data['end_date'],
            $data['start_time'],
            $data['end_time'],
            $data['reason'],
            $data['can_edit_until']
        );
        return $stmt->execute();
    }

    // 🔹 Update existing leave
    public function updateLeave($data) {
        $stmt = $this->conn->prepare(
            "UPDATE leaves SET leave_type=?, start_date=?, end_date=?, start_time=?, end_time=?, reason=? WHERE id=?"
        );
        $stmt->bind_param(
            "ssssssi",
            $data['leave_type'],
            $data['start_date'],
            $data['end_date'],
            $data['start_time'],
            $data['end_time'],
            $data['reason'],
            $data['id']
        );
        return $stmt->execute();
    }

    // 🔹 Delete leave
    public function deleteLeave($id) {
        $stmt = $this->conn->prepare("DELETE FROM leaves WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
