<?php
class HRDashboardModel {
     private $db;

    public function __construct() {
        $this->db = new mysqli("localhost", "root", "", "smartcare");
        if($this->db->connect_errno){
            die("Failed to connect to MySQL: " . $this->db->connect_error);
        }
    }

    public function totalCaretakers() {
        $sql = "SELECT COUNT(*) AS total FROM caretakers";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function pendingLeaveRequests() {
        $sql = "SELECT COUNT(*) AS total FROM leaves WHERE status='Pending'";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function pendingClientRequests() {
        $sql = "SELECT COUNT(*) AS total FROM bookings WHERE status='Requested'";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function activeServicesToday() {
        $sql = "SELECT COUNT(*) AS total 
                FROM bookings 
                WHERE booking_date >= CURDATE()
                AND status IN ('Advance_Paid','Accepted','Change_Requested','Reschedule_Requested')";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function recentLeaveRequests($limit = 5) {
    $sql = "SELECT user_id, start_date, end_date
            FROM leaves
            WHERE status = 'Pending'
            ORDER BY id DESC
            LIMIT $limit";

    $result = $this->db->query($sql);

    $leaves = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $leaves[] = $row;
        }
    }

    return $leaves;
    }

    public function recentComplaints($limit = 5) {
    $sql = "SELECT id, client_name,caretaker_name,category
            FROM complaints
            WHERE status = 'open'
            ORDER BY id DESC
            LIMIT $limit";

    $result = $this->db->query($sql);

    $complaints = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $complaints[] = $row;
        }
    }

    return $complaints;
    }

    public function recentClientRequests($limit = 5) {
    $sql = "SELECT b.id, c.name AS client_name, b.booking_date, b.preferred_time, b.service_type
            FROM bookings b
            JOIN clients c ON b.client_id = c.id
            WHERE b.status = 'Requested'
            ORDER BY b.id DESC
            LIMIT $limit";
    $result = $this->db->query($sql);

    $bookings = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }

    return $bookings;
    }
}