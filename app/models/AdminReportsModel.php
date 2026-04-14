<?php

/**
 * AdminReportsModel
 * Handles all reporting queries for Admin role
 * Includes financial, booking, client, caretaker, and feedback analytics
 */
class AdminReportsModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /* ==================== SUMMARY CARDS ==================== */

    /**
     * Get summary statistics for admin dashboard
     */
    public function getSummaryStats($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('booking_date', $fromDate, $toDate);

        // Total Revenue (from completed/paid bookings)
        $revenueQuery = "SELECT SUM(total_payment) as total FROM bookings WHERE status IN ('Completed', 'Paid')";
        if ($dateCondition) $revenueQuery .= " AND " . $dateCondition;
        $revenue = $this->executeScalar($revenueQuery) ?? 0;

        // Total Bookings
        $bookingsQuery = "SELECT COUNT(*) FROM bookings WHERE 1=1";
        if ($dateCondition) $bookingsQuery .= " AND " . $dateCondition;
        $totalBookings = $this->executeScalar($bookingsQuery) ?? 0;

        // Active Caretakers
        $activeCaretakers = $this->executeScalar("SELECT COUNT(*) FROM caretakers WHERE status = 'Active'") ?? 0;

        // Total Clients
        $totalClients = $this->executeScalar("SELECT COUNT(*) FROM clients") ?? 0;

        // Pending Payments (Advance_Paid but not final payment)
        $pendingPaymentsQuery = "SELECT COUNT(*) FROM bookings WHERE status IN ('Advance_Paid', 'Accepted')";
        if ($dateCondition) $pendingPaymentsQuery .= " AND " . $dateCondition;
        $pendingPayments = $this->executeScalar($pendingPaymentsQuery) ?? 0;

        // Average Rating
        $avgRating = $this->executeScalar("SELECT AVG(rating) FROM feedbacks WHERE rating > 0") ?? 0;

        return [
            'totalRevenue' => number_format($revenue, 2),
            'totalBookings' => $totalBookings,
            'activeCaretakers' => $activeCaretakers,
            'totalClients' => $totalClients,
            'pendingPayments' => $pendingPayments,
            'avgRating' => number_format($avgRating, 2)
        ];
    }

    /* ==================== BOOKING ANALYTICS ==================== */

    /**
     * Get booking status breakdown
     */
    public function getBookingStatusBreakdown($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('booking_date', $fromDate, $toDate);

        $query = "SELECT status, COUNT(*) as count FROM bookings WHERE 1=1";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY status";

        return $this->executeQuery($query);
    }

    /**
     * Get monthly booking trend (last 6 months)
     */
    public function getMonthlyBookingTrend()
    {
        $query = "
            SELECT
                DATE_FORMAT(booking_date, '%b %Y') as month_label,
                DATE_FORMAT(booking_date, '%Y-%m') as month_key,
                COUNT(*) as count
            FROM bookings
            WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(booking_date, '%Y-%m'), DATE_FORMAT(booking_date, '%b %Y')
            ORDER BY DATE_FORMAT(booking_date, '%Y-%m') ASC
        ";

        return $this->executeQuery($query);
    }

    /**
     * Bookings count + completed/paid revenue per month (respects date filter; otherwise last 6 months).
     *
     * @return list<array{month_key: string, month_label: string, bookings: int|string, revenue: float|string}>
     */
    public function getMonthlyBookingRevenueTrend($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('booking_date', $fromDate, $toDate);
        $where = $dateCondition
            ? 'WHERE ' . $dateCondition
            : 'WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)';
        $query = "
            SELECT
                DATE_FORMAT(booking_date, '%Y-%m') as month_key,
                DATE_FORMAT(booking_date, '%b %Y') as month_label,
                COUNT(*) as bookings,
                SUM(CASE WHEN status IN ('Completed', 'Paid') THEN COALESCE(total_payment, 0) ELSE 0 END) as revenue
            FROM bookings
            $where
            GROUP BY DATE_FORMAT(booking_date, '%Y-%m'), DATE_FORMAT(booking_date, '%b %Y')
            ORDER BY month_key ASC
        ";

        return $this->executeQuery($query);
    }

    /**
     * Get service type distribution
     */
    public function getServiceTypeDistribution($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('booking_date', $fromDate, $toDate);

        $query = "SELECT service_type, COUNT(*) as count FROM bookings WHERE 1=1";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY service_type ORDER BY count DESC";

        return $this->executeQuery($query);
    }

    /**
     * Get booking basis breakdown
     */
    public function getBookingBasisBreakdown($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('booking_date', $fromDate, $toDate);

        $query = "SELECT basis, COUNT(*) as count FROM bookings WHERE 1=1";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY basis ORDER BY count DESC";

        return $this->executeQuery($query);
    }

    /* ==================== FINANCIAL REPORTS ==================== */

    /**
     * Get monthly revenue trend (last 6 months)
     */
    public function getMonthlyRevenueTrend()
    {
        $query = "
            SELECT
                DATE_FORMAT(booking_date, '%b %Y') as month_label,
                DATE_FORMAT(booking_date, '%Y-%m') as month_key,
                SUM(total_payment) as revenue
            FROM bookings
            WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                AND status IN ('Completed', 'Paid')
            GROUP BY DATE_FORMAT(booking_date, '%Y-%m'), DATE_FORMAT(booking_date, '%b %Y')
            ORDER BY DATE_FORMAT(booking_date, '%Y-%m') ASC
        ";

        return $this->executeQuery($query);
    }

    /**
     * Get revenue by service type
     */
    public function getRevenueByServiceType($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('booking_date', $fromDate, $toDate);

        $query = "
            SELECT
                service_type,
                SUM(total_payment) as revenue,
                COUNT(*) as bookings
            FROM bookings
            WHERE status IN ('Completed', 'Paid')
        ";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY service_type ORDER BY revenue DESC";

        return $this->executeQuery($query);
    }

    /**
     * Get payment status breakdown
     */
    public function getPaymentStatusBreakdown($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('paid_date', $fromDate, $toDate, 'DATE');

        $query = "SELECT status, COUNT(*) as count, SUM(amount) as total FROM payments WHERE 1=1";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY status";

        return $this->executeQuery($query);
    }

    /**
     * Get advance vs final payment summary
     */
    public function getAdvanceVsFinalPayments($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('paid_date', $fromDate, $toDate, 'DATE');

        $query = "SELECT payment_type, COUNT(*) as count, SUM(amount) as total FROM payments WHERE 1=1";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY payment_type";

        return $this->executeQuery($query);
    }

    /**
     * Get refund statistics
     */
    public function getRefundStatistics($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('created_at', $fromDate, $toDate, 'DATE');

        $query = "
            SELECT
                status,
                COUNT(*) as count,
                SUM(refund_amount) as total_amount
            FROM refunds
            WHERE 1=1
        ";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY status";

        return $this->executeQuery($query);
    }

    /* ==================== CARETAKER PERFORMANCE ==================== */

    /**
     * Get top caretakers by bookings
     */
    public function getTopCaretakersByBookings($limit = 10, $fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('b.booking_date', $fromDate, $toDate);

        $query = "
            SELECT
                c.name,
                c.service_type,
                COUNT(b.id) as total_bookings,
                AVG(COALESCE(f.rating, 0)) as avg_rating
            FROM caretakers c
            LEFT JOIN bookings b ON c.id = b.caretaker_id
            LEFT JOIN feedbacks f ON b.id = f.booking_id
            WHERE 1=1
        ";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= "
            GROUP BY c.id, c.name, c.service_type
            HAVING total_bookings > 0
            ORDER BY total_bookings DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get top caretakers by revenue
     */
    public function getTopCaretakersByRevenue($limit = 10, $fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('b.booking_date', $fromDate, $toDate);

        $query = "
            SELECT
                c.name,
                c.service_type,
                SUM(b.total_payment) as total_revenue,
                COUNT(b.id) as total_bookings
            FROM caretakers c
            INNER JOIN bookings b ON c.id = b.caretaker_id
            WHERE b.status IN ('Completed', 'Paid')
        ";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= "
            GROUP BY c.id, c.name, c.service_type
            ORDER BY total_revenue DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get highest rated caretakers
     */
    public function getHighestRatedCaretakers($limit = 10, $minBookings = 3)
    {
        $query = "
            SELECT
                c.name,
                c.service_type,
                AVG(f.rating) as avg_rating,
                COUNT(DISTINCT b.id) as total_bookings
            FROM caretakers c
            INNER JOIN bookings b ON c.id = b.caretaker_id
            INNER JOIN feedbacks f ON b.id = f.booking_id
            WHERE f.rating > 0
            GROUP BY c.id, c.name, c.service_type
            HAVING total_bookings >= ?
            ORDER BY avg_rating DESC, total_bookings DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $minBookings, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get caretaker status distribution
     */
    public function getCaretakerStatusDistribution()
    {
        $query = "SELECT status, COUNT(*) as count FROM caretakers GROUP BY status";
        return $this->executeQuery($query);
    }

    /**
     * Get caretaker service type distribution
     */
    public function getCaretakerServiceDistribution()
    {
        $query = "SELECT service_type, COUNT(*) as count FROM caretakers WHERE status = 'Active' GROUP BY service_type";
        return $this->executeQuery($query);
    }

    /* ==================== CLIENT ANALYTICS ==================== */

    /**
     * Get new clients (last 30 days)
     */
    public function getNewClientsCount($days = 30)
    {
        $query = "SELECT COUNT(*) FROM clients WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        return $stmt->get_result()->fetch_row()[0] ?? 0;
    }

    /**
     * Get top clients by bookings
     */
    public function getTopClientsByBookings($limit = 10, $fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('b.booking_date', $fromDate, $toDate);

        $query = "
            SELECT
                cl.name,
                cl.email,
                COUNT(b.id) as total_bookings,
                SUM(b.total_payment) as total_spent
            FROM clients cl
            INNER JOIN bookings b ON cl.id = b.client_id
            WHERE 1=1
        ";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= "
            GROUP BY cl.id, cl.name, cl.email
            ORDER BY total_bookings DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get top clients by spending
     */
    public function getTopClientsBySpending($limit = 10, $fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('b.booking_date', $fromDate, $toDate);

        $query = "
            SELECT
                cl.name,
                cl.email,
                COUNT(b.id) as total_bookings,
                SUM(b.total_payment) as total_spent
            FROM clients cl
            INNER JOIN bookings b ON cl.id = b.client_id
            WHERE b.status IN ('Completed', 'Paid')
        ";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= "
            GROUP BY cl.id, cl.name, cl.email
            ORDER BY total_spent DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get client location distribution (optional booking date range).
     */
    public function getClientLocationDistribution($limit = 10, $fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('b.booking_date', $fromDate, $toDate);
        $query = "
            SELECT b.district, COUNT(DISTINCT b.client_id) as count
            FROM bookings b
            WHERE b.district IS NOT NULL AND b.district != ''
        ";
        if ($dateCondition) {
            $query .= ' AND ' . $dateCondition;
        }
        $query .= '
            GROUP BY b.district
            ORDER BY count DESC
            LIMIT ?
        ';

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* ==================== FEEDBACK & QUALITY ==================== */

    /**
     * Get service-wise average ratings
     */
    public function getServiceWiseRatings($fromDate = null, $toDate = null)
    {
        $dateCondition = $this->buildDateCondition('b.booking_date', $fromDate, $toDate);

        $query = "
            SELECT
                b.service_type,
                AVG(f.rating) as avg_rating,
                COUNT(f.id) as feedback_count
            FROM bookings b
            INNER JOIN feedbacks f ON b.id = f.booking_id
            WHERE f.rating > 0
        ";
        if ($dateCondition) $query .= " AND " . $dateCondition;
        $query .= " GROUP BY b.service_type ORDER BY avg_rating DESC";

        return $this->executeQuery($query);
    }

    /**
     * Get low-rated bookings (< 3.0)
     */
    public function getLowRatedBookings($threshold = 3.0, $limit = 20)
    {
        $query = "
            SELECT
                b.id as booking_id,
                c.name as caretaker_name,
                cl.name as client_name,
                b.service_type,
                f.rating,
                f.feedback as comment,
                b.booking_date
            FROM bookings b
            INNER JOIN feedbacks f ON b.id = f.booking_id
            INNER JOIN caretakers c ON b.caretaker_id = c.id
            INNER JOIN clients cl ON b.client_id = cl.id
            WHERE f.rating < ? AND f.rating > 0
            ORDER BY f.rating ASC, b.booking_date DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $threshold, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get complaint statistics
     */
    public function getComplaintStatistics($fromDate = null, $toDate = null)
    {
        // Client complaints about caretakers
        $clientComplaintQuery = "SELECT COUNT(*) FROM complaints WHERE 1=1";
        if ($fromDate && $toDate) {
            $clientComplaintQuery .= " AND DATE(complaint_date) BETWEEN ? AND ?";
            $stmt = $this->conn->prepare($clientComplaintQuery);
            $stmt->bind_param("ss", $fromDate, $toDate);
            $stmt->execute();
            $clientComplaints = $stmt->get_result()->fetch_row()[0] ?? 0;
        } else {
            $clientComplaints = $this->executeScalar($clientComplaintQuery) ?? 0;
        }

        // Caretaker complaints about clients
        $caretakerComplaintQuery = "SELECT COUNT(*) FROM ct_complaints WHERE 1=1";
        if ($fromDate && $toDate) {
            $caretakerComplaintQuery .= " AND DATE(created_at) BETWEEN ? AND ?";
            $stmt = $this->conn->prepare($caretakerComplaintQuery);
            $stmt->bind_param("ss", $fromDate, $toDate);
            $stmt->execute();
            $caretakerComplaints = $stmt->get_result()->fetch_row()[0] ?? 0;
        } else {
            $caretakerComplaints = $this->executeScalar($caretakerComplaintQuery) ?? 0;
        }

        return [
            'client_complaints' => $clientComplaints,
            'caretaker_complaints' => $caretakerComplaints,
            'total_complaints' => $clientComplaints + $caretakerComplaints
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
     * Get complete report data for admin
     */
    public function getCompleteReportData($fromDate = null, $toDate = null)
    {
        return [
            'summary' => $this->getSummaryStats($fromDate, $toDate),
            'bookingStatus' => $this->getBookingStatusBreakdown($fromDate, $toDate),
            'monthlyTrends' => $this->getMonthlyBookingRevenueTrend($fromDate, $toDate),
            'serviceDistribution' => $this->getServiceTypeDistribution($fromDate, $toDate),
            'basisBreakdown' => $this->getBookingBasisBreakdown($fromDate, $toDate),
            'revenueByService' => $this->getRevenueByServiceType($fromDate, $toDate),
            'paymentStatus' => $this->getPaymentStatusBreakdown($fromDate, $toDate),
            'advanceVsFinal' => $this->getAdvanceVsFinalPayments($fromDate, $toDate),
            'refunds' => $this->getRefundStatistics($fromDate, $toDate),
            'topCaretakersByBookings' => $this->getTopCaretakersByBookings(10, $fromDate, $toDate),
            'topCaretakersByRevenue' => $this->getTopCaretakersByRevenue(10, $fromDate, $toDate),
            'highestRated' => $this->getHighestRatedCaretakers(10),
            'caretakerStatus' => $this->getCaretakerStatusDistribution(),
            'caretakerServices' => $this->getCaretakerServiceDistribution(),
            'newClients' => $this->getNewClientsCount(30),
            'topClientsByBookings' => $this->getTopClientsByBookings(10, $fromDate, $toDate),
            'topClientsBySpending' => $this->getTopClientsBySpending(10, $fromDate, $toDate),
            'clientLocations' => $this->getClientLocationDistribution(10, $fromDate, $toDate),
            'serviceRatings' => $this->getServiceWiseRatings($fromDate, $toDate),
            'lowRatedBookings' => $this->getLowRatedBookings(3.0, 20),
            'complaints' => $this->getComplaintStatistics($fromDate, $toDate)
        ];
    }
}
