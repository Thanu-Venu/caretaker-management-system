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
                AND status IN ('Reschedule_Requested', 'Accepted', 'Advance_Paid', 'Cancelled')";
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

    /* ===============================
       ATTENDANCE DATA FOR CHARTS
    =============================== */
    public function getAttendanceData($limit = 10) {
        $sql = "SELECT 
                    ct.id,
                    ct.name,
                    COUNT(CASE WHEN a.status = 'Present' THEN 1 END) AS days_present,
                    COUNT(CASE WHEN a.status IN ('Present','Late') THEN 1 END) AS days_worked,
                    COUNT(a.id) AS total_days_tracked
                FROM caretakers ct
                LEFT JOIN attendance a ON ct.id = a.caretaker_id 
                    AND a.attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                WHERE ct.status = 'Active'
                GROUP BY ct.id, ct.name
                ORDER BY days_present DESC
                LIMIT $limit";

        $result = $this->db->query($sql);
        if (!$result) {
            error_log("Attendance query error: " . $this->db->error);
            return [];
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /* ===============================
       PERFORMANCE RATINGS DATA FOR CHARTS
    =============================== */
    public function getPerformanceRatings() {
        $sql = "SELECT 
                    CASE 
                        WHEN AVG(f.rating) >= 4.5 THEN 'Excellent'
                        WHEN AVG(f.rating) >= 4.0 THEN 'Good'
                        WHEN AVG(f.rating) >= 3.0 THEN 'Average'
                        ELSE 'Poor'
                    END AS rating_category,
                    COUNT(DISTINCT f.caretaker_id) AS count
                FROM caretakers ct
                LEFT JOIN feedbacks f ON ct.id = f.caretaker_id
                WHERE ct.status = 'Active'
                GROUP BY rating_category
                UNION ALL
                SELECT 
                    'Excellent' as rating_category,
                    COUNT(DISTINCT id) as count
                FROM caretakers 
                WHERE status = 'Active' AND id NOT IN (
                    SELECT DISTINCT caretaker_id FROM feedbacks
                )
                AND 0 = 1";

        // Simplified version - get actual ratings
        $sql = "SELECT 
                    CASE 
                        WHEN rating >= 4.5 THEN 'Excellent'
                        WHEN rating >= 4.0 THEN 'Good'
                        WHEN rating >= 3.0 THEN 'Average'
                        WHEN rating IS NOT NULL THEN 'Poor'
                        ELSE 'Not Rated'
                    END AS rating_category,
                    COUNT(*) AS count
                FROM caretakers
                WHERE status = 'Active'
                GROUP BY rating_category
                ORDER BY FIELD(rating_category, 'Excellent', 'Good', 'Average', 'Poor', 'Not Rated')";

        $result = $this->db->query($sql);
        if (!$result) {
            error_log("Performance ratings query error: " . $this->db->error);
            return [];
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /* ===============================
       BOOKING SUMMARY DATA FOR CHARTS
    =============================== */
    public function getBookingSummary() {
        $sql = "SELECT 
                    COALESCE(status, 'Pending') as status,
                    COUNT(*) as count
                FROM bookings
                GROUP BY status
                ORDER BY FIELD(status, 'Reschedule_Requested', 'Requested', 'Accepted', 'Advance_Paid', 'Completed', 'Cancelled', 'Declined')";

        $result = $this->db->query($sql);
        if (!$result) {
            error_log("Booking summary query error: " . $this->db->error);
            return [];
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /* ===============================
       GET RATING STATISTICS
    =============================== */
    public function getRatingStats() {
        $sql = "SELECT 
                    COUNT(*) as total_caretakers,
                    COUNT(CASE WHEN rating IS NULL THEN 1 END) as not_rated,
                    COUNT(CASE WHEN rating >= 4.5 THEN 1 END) as excellent,
                    COUNT(CASE WHEN rating >= 4.0 AND rating < 4.5 THEN 1 END) as good,
                    COUNT(CASE WHEN rating >= 3.0 AND rating < 4.0 THEN 1 END) as average,
                    COUNT(CASE WHEN rating < 3.0 THEN 1 END) as poor,
                    AVG(rating) as avg_rating
                FROM caretakers
                WHERE status = 'Active'";

        $result = $this->db->query($sql);
        if (!$result) {
            error_log("Rating stats query error: " . $this->db->error);
            return null;
        }

        return $result->fetch_assoc();
    }
}