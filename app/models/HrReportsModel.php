<?php

/**
 * HrReportsModel
 * Handles all reporting queries for HR/Manager role
 * Includes operational, caretaker management, scheduling reports
 * NO full financial/revenue reports (only payment status visibility)
 */
class HrReportsModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /* ==================== SUMMARY CARDS ==================== */

    /**
     * Get summary statistics for HR dashboard
     */
    public function getSummaryStats($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('booking_date', $fromDate, $toDate);

        // Total Caretakers
        $totalCaretakers = $this->executeScalar("SELECT COUNT(*) FROM caretakers") ?? 0;

        // Active Bookings Today
        $activeToday = $this->executeScalar("
            SELECT COUNT(*) FROM bookings
            WHERE DATE(booking_date) = CURDATE()
            AND status IN ('Accepted', 'Advance_Paid')
        ") ?? 0;

        // Pending Leave Requests
        $pendingLeaves = $this->executeScalar("SELECT COUNT(*) FROM leaves WHERE status = 'Pending'") ?? 0;

        // Pending Reschedule Requests
        $query = "SELECT COUNT(*) FROM reschedule_requests WHERE status = 'pending'";
        $pendingReschedules = $this->executeScalar($query) ?? 0;

        // Pending Booking Approvals
        $pendingBookingsQuery = "SELECT COUNT(*) FROM bookings WHERE status = 'Requested'";
        if ($dateCondition) $pendingBookingsQuery .= " AND " . $dateCondition;
        $pendingBookings = $this->executeScalar($pendingBookingsQuery) ?? 0;

        // Recent Complaints (last 7 days)
        $recentComplaints = $this->executeScalar("
            SELECT COUNT(*) FROM complaints
            WHERE DATE(complaint_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ") ?? 0;

        return [
            'totalCaretakers' => $totalCaretakers,
            'activeToday' => $activeToday,
            'pendingLeaves' => $pendingLeaves,
            'pendingReschedules' => $pendingReschedules,
            'pendingBookings' => $pendingBookings,
            'recentComplaints' => $recentComplaints
        ];
    }

    /* ==================== CARETAKER MANAGEMENT ==================== */

    /**
     * Get caretaker status breakdown
     */
    public function getCaretakerStatusBreakdown()
    {
        $query = "SELECT status, COUNT(*) as count FROM caretakers GROUP BY status";
        return $this->executeQuery($query);
    }

    /**
     * Get newly added caretakers
     */
    public function getNewlyAddedCaretakers($days = 30)
    {
        $query = "
            SELECT
                id as caretaker_id,
                name,
                service_type,
                status,
                DATE(created_at) as joined_date
            FROM caretakers
            WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            ORDER BY created_at DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get caretakers by service type
     */
    public function getCaretakersByServiceType()
    {
        $query = "
            SELECT
                service_type,
                COUNT(*) as count,
                SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active_count
            FROM caretakers
            GROUP BY service_type
            ORDER BY count DESC
        ";
        return $this->executeQuery($query);
    }

    /**
     * Get caretaker availability status
     */
    public function getCaretakerAvailabilityStatus()
    {
        $query = "
            SELECT
                status,
                COUNT(*) as count
            FROM caretakers
            GROUP BY status
        ";
        return $this->executeQuery($query);
    }

    /**
     * Get caretaker workload distribution
     */
    public function getCaretakerWorkloadDistribution($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('b.booking_date', $fromDate, $toDate);

        $query = "
            SELECT
                c.name,
                c.service_type,
                COUNT(b.id) as active_bookings,
                SUM(CASE WHEN b.status = 'Accepted' THEN 1 ELSE 0 END) as ongoing,
                SUM(CASE WHEN b.status = 'Completed' THEN 1 ELSE 0 END) as completed
            FROM caretakers c
            LEFT JOIN bookings b ON c.id = b.caretaker_id
        ";

        if ($dateCondition) {
            $query .= " WHERE " . $dateCondition;
        }

        $query .= "
            GROUP BY c.id, c.name, c.service_type
            HAVING active_bookings > 0
            ORDER BY active_bookings DESC
        ";

        return $this->executeQuery($query);
    }

    /* ==================== LEAVE MANAGEMENT ==================== */

    /**
     * Get leave requests by status
     */
    public function getLeaveRequestsByStatus($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('start_date', $fromDate, $toDate, 'DATE');

        $query = "SELECT status, COUNT(*) as count FROM leaves WHERE 1=1";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY status";

        return $this->executeQuery($query);
    }

    /**
     * Get pending leave requests
     */
    public function getPendingLeaveRequests($limit = 20)
    {
        $query = "
            SELECT
                l.id as leave_id,
                c.name as caretaker_name,
                c.service_type,
                l.leave_type,
                l.start_date as from_date,
                l.end_date as to_date,
                l.reason,
                NULL as applied_date
            FROM leaves l
            INNER JOIN caretakers c ON l.user_id = c.id
            WHERE l.status = 'Pending'
            ORDER BY l.id DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get approved leaves for current month
     */
    public function getApprovedLeavesThisMonth()
    {
        $query = "
            SELECT
                l.id as leave_id,
                c.name as caretaker_name,
                c.service_type,
                l.leave_type,
                l.start_date as from_date,
                l.end_date as to_date,
                DATEDIFF(l.end_date, l.start_date) + 1 as days_requested
            FROM leaves l
            INNER JOIN caretakers c ON l.user_id = c.id
            WHERE l.status = 'Approved'
                AND (
                    (MONTH(l.start_date) = MONTH(CURDATE()) AND YEAR(l.start_date) = YEAR(CURDATE()))
                    OR (MONTH(l.end_date) = MONTH(CURDATE()) AND YEAR(l.end_date) = YEAR(CURDATE()))
                )
            ORDER BY l.start_date ASC
        ";

        return $this->executeQuery($query);
    }

    /**
     * Get caretakers currently on leave
     */
    public function getCaretakersOnLeave()
    {
        $query = "
            SELECT
                c.name,
                c.service_type,
                l.leave_type,
                l.start_date as from_date,
                l.end_date as to_date,
                DATEDIFF(l.end_date, CURDATE()) as days_remaining
            FROM caretakers c
            INNER JOIN leaves l ON c.id = l.user_id
            WHERE l.status = 'Approved'
                AND CURDATE() BETWEEN l.start_date AND l.end_date
            ORDER BY l.end_date ASC
        ";

        return $this->executeQuery($query);
    }

    /**
     * Get leave history by caretaker
     */
    public function getLeaveHistoryByCaretaker($caretakerId, $limit = 10)
    {
        $query = "
            SELECT
                id as leave_id,
                leave_type,
                start_date as from_date,
                end_date as to_date,
                DATEDIFF(end_date, start_date) + 1 as days_requested,
                reason,
                status,
                NULL as applied_date
            FROM leaves
            WHERE user_id = ?
            ORDER BY id DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $caretakerId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* ==================== SCHEDULE & ASSIGNMENT ==================== */

    /**
     * Get booking assignment statistics
     */
    public function getBookingAssignmentStats($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('booking_date', $fromDate, $toDate);

        $assignedQuery = "SELECT COUNT(*) FROM bookings WHERE caretaker_id IS NOT NULL";
        $unassignedQuery = "SELECT COUNT(*) FROM bookings WHERE status = 'Requested'";

        if ($dateCondition) {
            $assignedQuery .= " AND " . $dateCondition;
            $unassignedQuery .= " AND " . $dateCondition;
        }

        return [
            'assigned' => $this->executeScalar($assignedQuery) ?? 0,
            'unassigned' => $this->executeScalar($unassignedQuery) ?? 0
        ];
    }

    /**
     * Get unassigned/pending bookings
     */
    public function getUnassignedBookings($limit = 20)
    {
        $query = "
            SELECT
                b.id as booking_id,
                cl.name as client_name,
                b.service_type,
                b.booking_date,
                b.preferred_time,
                b.duration,
                b.basis,
                b.district,
                b.status
            FROM bookings b
            INNER JOIN clients cl ON b.client_id = cl.id
            WHERE b.status = 'Requested'
            ORDER BY b.booking_date ASC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get upcoming schedules (next 7 days)
     */
    public function getUpcomingSchedules($days = 7)
    {
        $query = "
            SELECT
                b.id as booking_id,
                c.name as caretaker_name,
                cl.name as client_name,
                b.service_type,
                b.booking_date,
                b.preferred_time,
                b.status,
                b.district
            FROM bookings b
            INNER JOIN caretakers c ON b.caretaker_id = c.id
            INNER JOIN clients cl ON b.client_id = cl.id
            WHERE b.booking_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND b.status IN ('Accepted', 'Advance_Paid')
            ORDER BY b.booking_date ASC, b.preferred_time ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get booking assignment distribution
     */
    public function getBookingAssignmentDistribution($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('b.booking_date', $fromDate, $toDate);

        $query = "
            SELECT
                c.name as caretaker_name,
                c.service_type,
                COUNT(b.id) as assigned_bookings,
                SUM(CASE WHEN b.status = 'Accepted' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN b.status = 'Completed' THEN 1 ELSE 0 END) as completed
            FROM caretakers c
            INNER JOIN bookings b ON c.id = b.caretaker_id
            WHERE 1=1
        ";

        if ($dateCondition) $query .= " AND " . $dateCondition;

        $query .= "
            GROUP BY c.id, c.name, c.service_type
            ORDER BY assigned_bookings DESC
        ";

        return $this->executeQuery($query);
    }

    /* ==================== RESCHEDULE MANAGEMENT ==================== */

    /**
     * Get reschedule requests by status
     */
    public function getRescheduleRequestsByStatus($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('created_at', $fromDate, $toDate, 'DATE');

        $query = "SELECT status, COUNT(*) as count FROM reschedule_requests WHERE 1=1";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY status";

        return $this->executeQuery($query);
    }

    /**
     * Get pending reschedule requests
     */
    public function getPendingRescheduleRequests($limit = 20)
    {
        $query = "
            SELECT
                rr.id as request_id,
                b.id as booking_id,
                cl.name as client_name,
                c.name as caretaker_name,
                b.service_type,
                rr.old_date as old_date,
                rr.new_date as new_date,
                rr.reason,
                rr.created_at as requested_at
            FROM reschedule_requests rr
            INNER JOIN bookings b ON rr.booking_id = b.id
            INNER JOIN clients cl ON b.client_id = cl.id
            LEFT JOIN caretakers c ON b.caretaker_id = c.id
            WHERE rr.status = 'pending'
            ORDER BY rr.created_at DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get recent approved reschedules
     */
    public function getRecentApprovedReschedules($days = 30)
    {
        $query = "
            SELECT
                rr.id as request_id,
                b.id as booking_id,
                cl.name as client_name,
                c.name as caretaker_name,
                b.service_type,
                rr.new_date,
                rr.reviewed_at as approved_at
            FROM reschedule_requests rr
            INNER JOIN bookings b ON rr.booking_id = b.id
            INNER JOIN clients cl ON b.client_id = cl.id
            LEFT JOIN caretakers c ON b.caretaker_id = c.id
            WHERE rr.status = 'approved'
                AND DATE(COALESCE(rr.reviewed_at, rr.created_at)) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            ORDER BY COALESCE(rr.reviewed_at, rr.created_at) DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* ==================== OPERATIONAL PAYMENT STATUS (LIMITED) ==================== */

    /**
     * Get bookings awaiting advance payment
     */
    public function getBookingsAwaitingAdvancePayment($limit = 20)
    {
        $query = "
            SELECT
                b.id as booking_id,
                cl.name as client_name,
                c.name as caretaker_name,
                b.service_type,
                b.booking_date,
                b.status,
                DATEDIFF(b.booking_date, CURDATE()) as days_until_service
            FROM bookings b
            INNER JOIN clients cl ON b.client_id = cl.id
            LEFT JOIN caretakers c ON b.caretaker_id = c.id
            WHERE b.status IN ('Requested', 'Payment_Requested')
            ORDER BY b.booking_date ASC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get bookings awaiting final payment
     */
    public function getBookingsAwaitingFinalPayment($limit = 20)
    {
        $query = "
            SELECT
                b.id as booking_id,
                cl.name as client_name,
                c.name as caretaker_name,
                b.service_type,
                b.booking_date,
                b.status
            FROM bookings b
            INNER JOIN clients cl ON b.client_id = cl.id
            INNER JOIN caretakers c ON b.caretaker_id = c.id
            WHERE b.status IN ('Advance_Paid', 'Accepted', 'Completed')
            ORDER BY b.booking_date DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get payment approval pending count (operational visibility only)
     */
    public function getPaymentApprovalPending()
    {
        $query = "SELECT COUNT(*) FROM payments WHERE status = 'pending'";
        return $this->executeScalar($query) ?? 0;
    }

    /* ==================== PERFORMANCE MONITORING ==================== */

    /**
     * Get caretaker feedback summary (average ratings only)
     */
    public function getCaretakerFeedbackSummary()
    {
        $query = "
            SELECT
                c.name,
                c.service_type,
                AVG(f.rating) as avg_rating,
                COUNT(f.id) as total_feedbacks
            FROM caretakers c
            INNER JOIN bookings b ON c.id = b.caretaker_id
            INNER JOIN feedbacks f ON b.id = f.booking_id
            WHERE f.rating > 0
            GROUP BY c.id, c.name, c.service_type
            HAVING total_feedbacks >= 1
            ORDER BY avg_rating DESC, total_feedbacks DESC
        ";

        return $this->executeQuery($query);
    }

    /**
     * Get complaint logs (caretaker-related)
     */
    public function getCaretakerComplaints($limit = 20, $fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('co.complaint_date', $fromDate, $toDate, 'DATE');

        $query = "
            SELECT
                co.Id as complaint_id,
                co.client_name,
                co.caretaker_name,
                co.category as service_type,
                co.details as complaint_text,
                co.status,
                co.complaint_date as created_at
            FROM complaints co
            WHERE 1=1
        ";

        if ($dateCondition) $query .= " AND " . $dateCondition;

        $query .= "
            ORDER BY co.complaint_date DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get booking completion rate
     */
    public function getBookingCompletionRate($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('booking_date', $fromDate, $toDate);

        $totalQuery = "SELECT COUNT(*) FROM bookings WHERE 1=1";
        $completedQuery = "SELECT COUNT(*) FROM bookings WHERE status IN ('Completed', 'Paid')";

        if ($dateCondition) {
            $totalQuery .= " AND " . $dateCondition;
            $completedQuery .= " AND " . $dateCondition;
        }

        $total = $this->executeScalar($totalQuery) ?? 0;
        $completed = $this->executeScalar($completedQuery) ?? 0;

        $rate = $total > 0 ? ($completed / $total) * 100 : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'rate' => number_format($rate, 2)
        ];
    }

    /* ==================== HELPER METHODS ==================== */

    /**
     * Execute a query and return all results
     */
    private function executeQuery($query)
    {
        $result = $this->conn->query($query);
        if (!$result) {
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Execute a query and return a single scalar value
     */
    private function executeScalar($query)
    {
        $result = $this->conn->query($query);
        if (!$result) {
            return null;
        }
        $row = $result->fetch_row();
        return $row ? $row[0] : null;
    }

    /**
     * Build date condition for SQL queries
     */
    private function buildDateCondition($column, $fromDate, $toDate, $dateFunction = null)
    {
        if (!$fromDate || !$toDate) {
            return null;
        }

        if ($dateFunction) {
            return "$dateFunction($column) BETWEEN '$fromDate' AND '$toDate'";
        }

        return "$column BETWEEN '$fromDate' AND '$toDate'";
    }

    /**
     * Get complete report data for HR
     */
    public function getCompleteReportData($fromDate = null, $toDate = null)
    {
        return [
            'summary' => $this->getSummaryStats($fromDate, $toDate),
            'caretakerStatus' => $this->getCaretakerStatusBreakdown(),
            'newCaretakers' => $this->getNewlyAddedCaretakers(30),
            'caretakersByService' => $this->getCaretakersByServiceType(),
            'caretakerAvailability' => $this->getCaretakerAvailabilityStatus(),
            'caretakerWorkload' => $this->getCaretakerWorkloadDistribution($fromDate, $toDate),
            'leaveRequests' => $this->getLeaveRequestsByStatus($fromDate, $toDate),
            'pendingLeaves' => $this->getPendingLeaveRequests(20),
            'approvedLeavesThisMonth' => $this->getApprovedLeavesThisMonth(),
            'caretakersOnLeave' => $this->getCaretakersOnLeave(),
            'assignmentStats' => $this->getBookingAssignmentStats($fromDate, $toDate),
            'unassignedBookings' => $this->getUnassignedBookings(20),
            'upcomingSchedules' => $this->getUpcomingSchedules(7),
            'assignmentDistribution' => $this->getBookingAssignmentDistribution($fromDate, $toDate),
            'rescheduleRequests' => $this->getRescheduleRequestsByStatus($fromDate, $toDate),
            'pendingReschedules' => $this->getPendingRescheduleRequests(20),
            'recentApprovedReschedules' => $this->getRecentApprovedReschedules(30),
            'awaitingAdvancePayment' => $this->getBookingsAwaitingAdvancePayment(20),
            'awaitingFinalPayment' => $this->getBookingsAwaitingFinalPayment(20),
            'paymentApprovalPending' => $this->getPaymentApprovalPending(),
            'caretakerFeedback' => $this->getCaretakerFeedbackSummary(),
            'caretakerComplaints' => $this->getCaretakerComplaints(20, $fromDate, $toDate),
            'completionRate' => $this->getBookingCompletionRate($fromDate, $toDate)
        ];
    }
}
