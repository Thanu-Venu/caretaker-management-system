<?php
require_once APPROOT . '/core/Database.php';

class LeaveModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /* ================= CARETAKER ================= */

    public function getLeavesByUser($userId) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM leaves WHERE user_id=? ORDER BY start_date DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getLeaveById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM leaves WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    public function addLeave($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO leaves 
            (user_id, leave_type, start_date, end_date, start_time, end_time, reason, can_edit_until, status)
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

    public function updateLeave($data) {
        $stmt = $this->conn->prepare(
            "UPDATE leaves 
             SET leave_type=?, start_date=?, end_date=?, start_time=?, end_time=?, reason=? 
             WHERE id=? AND status='Pending'"
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

    public function deleteLeave($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM leaves WHERE id=? AND status='Pending'"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* ================= HR / ADMIN ================= */

    public function getAllLeaves() {
        $sql = "
            SELECT l.*, u.id AS caretaker_id, u.name AS caretaker_name
            FROM leaves l
            JOIN users u ON l.user_id = u.id
            ORDER BY l.start_date DESC
        ";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getLeavesByStatus($status) {
        $stmt = $this->conn->prepare(
            "SELECT l.*, u.id AS caretaker_id, u.name AS caretaker_name
             FROM leaves l
             JOIN users u ON l.user_id = u.id
             WHERE l.status=?"
        );
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateLeaveStatus($id, $status) {
        $stmt = $this->conn->prepare(
            "UPDATE leaves SET status=? WHERE id=?"
        );
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    

}
