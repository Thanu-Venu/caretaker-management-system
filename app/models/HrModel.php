<?php
require_once APPROOT . '/core/Database.php';

class HrModel
{
    private $conn;

    private const TIME_RANGE_MAP = [
        "Morning (8am - 12pm)" => ["08:00:00", "12:00:00"],
        "Evening (1pm - 5pm)" => ["13:00:00", "17:00:00"],
        "Night (6pm - 10pm)" => ["18:00:00", "22:00:00"],
        "Full Time (8am - 5pm)" => ["08:00:00", "17:00:00"]
    ];

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function getAllBookings()
    {
        $sql = "SELECT
                b.id AS booking_id,
                b.client_id,
                b.caretaker_id,
                b.booking_date,
                b.preferred_time,
                b.customization,
                b.customization_hours,
                b.customization_price,
                b.status,
                b.service_type,
                c.name AS client_name,
                ct.name AS caretaker_name
            FROM bookings b
            JOIN clients c ON b.client_id = c.id
            JOIN caretakers ct ON b.caretaker_id = ct.id
            ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function requestAdvancePayment($booking_id)
    {
        $stmt = $this->conn->prepare("
        UPDATE bookings
        SET status = 'Payment_Requested'
        WHERE id = ? AND status = 'Requested'
    ");

        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("i", $booking_id);
        $result = $stmt->execute();

        if (!$result) {
            error_log("Execute failed: " . $stmt->error);
            $stmt->close();
            return false;
        }

        $affected_rows = $stmt->affected_rows;
        $stmt->close();

        return $affected_rows > 0;
    }

    public function getRequestedBookings()
    {

        $sql = "
        SELECT
            b.id AS booking_id,
            b.client_id,
            b.caretaker_id,
            b.service_type,
            b.booking_date,
            b.preferred_time,
            b.customization,
            b.customization_hours,
            b.customization_price,
            b.status,
            c.name AS client_name,
            t.name AS caretaker_name
        FROM bookings b
        JOIN users c ON b.client_id = c.id
        JOIN users t ON b.caretaker_id = t.id
        WHERE b.status IN ('Requested', 'Payment_Requested')
        ORDER BY b.created_at DESC
    ";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function approveBookingAddCustomizationFee(int $bookingId, float $customFee, int $hrId): bool
    {
        // only approve if still pending
        $stmtCheck = $this->conn->prepare("SELECT status, total_payment, client_id, customization FROM bookings WHERE id=?");
        $stmtCheck->bind_param("i", $bookingId);
        $stmtCheck->execute();
        $b = $stmtCheck->get_result()->fetch_assoc();

        if (!$b) return false;
        if ($b['status'] !== 'Pending') return false;

        $fee = max(0, (float)$customFee);
        $newTotal = (float)$b['total_payment'] + $fee;

        $status = "AwaitingPayment";

        $stmt = $this->conn->prepare("
        UPDATE bookings
        SET status = ?,
            customization_fee = ?,
            final_total = ?
        WHERE id = ? AND status='Pending'
    ");

        // If you don't have approved_by/approved_at columns, remove them from query + bind.
        $stmt->bind_param("sddi", $status, $fee, $newTotal, $bookingId);
        return $stmt->execute();
    }


    public function getBookingTotal(int $bookingId): ?float
    {
        $stmt = $this->conn->prepare(
            "SELECT final_total FROM bookings WHERE id=?"
        );
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        return $row ? (float)$row['final_total'] : null;
    }



    public function updateBookingStatus($booking_id, $status)
    {
        $stmt = $this->conn->prepare("
            UPDATE bookings
            SET status = ?
            WHERE id = ?
        ");
        $stmt->bind_param("si", $status, $booking_id);
        $stmt->execute();
        $stmt->close();
    }

    public function sendNotification($data)
    {
        $sql = "INSERT INTO notifications
            (user_id, user_role, title, message, link, is_read, created_at)
            VALUES (?, ?, ?, ?, ?, 0, NOW())";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "issss",
            $data['user_id'],
            $data['user_role'],
            $data['title'],
            $data['message'],
            $data['link']
        );

        return $stmt->execute();
    }

    public function getBookingClientId(int $bookingId): ?int
    {
        $stmt = $this->conn->prepare("SELECT client_id FROM bookings WHERE id=?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? (int)$row['client_id'] : null;
    }

    public function getBookingTotalPayment(int $bookingId): ?array
    {
        $stmt = $this->conn->prepare("SELECT total_payment FROM bookings WHERE id=?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }
    public function notifyUser(int $userId, string $role, string $title, string $message, string $link = ''): bool
    {
        $isRead = 0;

        $sql = "INSERT INTO notifications (user_id, user_role, title, message, link, is_read)
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssi", $userId, $role, $title, $message, $link, $isRead);
        return $stmt->execute();
    }

    public function getBookingDetailsForApproval(int $bookingId): ?array
    {
        $sql = "SELECT id, client_id, total_payment, customization
            FROM bookings
            WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    public function countBookingsByStatus(?string $status = null): int
    {
        if ($status && $status !== 'All') {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) AS total FROM bookings WHERE status = ?"
            );
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
        } else {
            $result = $this->conn->query(
                "SELECT COUNT(*) AS total FROM bookings"
            );
            $row = $result->fetch_assoc();
        }

        return (int)($row['total'] ?? 0);
    }

    public function getBookingsPaginatedByStatus(
        int $limit,
        int $offset,
        ?string $status = null
    ): array {
        $sql = "
        SELECT
            b.*,
            c.name AS client_name,
            ct.name AS caretaker_name
        FROM bookings b
        JOIN clients c ON b.client_id = c.id
        JOIN caretakers ct ON b.caretaker_id = ct.id
    ";

        if ($status && $status !== 'All') {
            $sql .= " WHERE b.status = ? ";
        }

        $sql .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("HrModel::getBookingsPaginatedByStatus prepare failed: " . $this->conn->error);
            return [];
        }

        if ($status && $status !== 'All') {
            $stmt->bind_param("sii", $status, $limit, $offset);
        } else {
            $stmt->bind_param("ii", $limit, $offset);
        }

        $exec = $stmt->execute();
        if (!$exec) {
            error_log("HrModel::getBookingsPaginatedByStatus execute failed: " . $stmt->error);
            return [];
        }

        $res = $stmt->get_result();
        if ($res === false) {
            error_log("HrModel::getBookingsPaginatedByStatus get_result failed: " . $this->conn->error);
            return [];
        }

        $rows = $res->fetch_all(MYSQLI_ASSOC);
        foreach ($rows as &$row) {
            if (!isset($row['booking_id']) && isset($row['id'])) {
                $row['booking_id'] = (int) $row['id'];
            }
        }
        unset($row);

        return $rows;
    }

    /**
     * HR rejects a booking that is still in Requested status.
     */
    public function rejectBookingIfRequested(int $bookingId): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE bookings SET status = 'Rejected' WHERE id = ? AND status = 'Requested'"
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param('i', $bookingId);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();

        return $ok;
    }

    public function getBookingSummary($bookingId)
    {
        $sql = "SELECT b.id AS booking_id, b.booking_date, b.preferred_time, b.duration, b.basis, b.service_type,
                   ct.name AS caretaker_name
            FROM bookings b
            LEFT JOIN caretakers ct ON ct.id = b.caretaker_id
            WHERE b.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function getTimeRangeFromBookingTime($timeString): array
    {
        return self::TIME_RANGE_MAP[$timeString] ?? ["00:00:00", "23:59:59"];
    }

    private function rangesOverlap(string $startA, string $endA, string $startB, string $endB): bool
    {
        return ($startA < $endB) && ($endA > $startB);
    }

    /**
     * Returns first conflicting booking for assigned caretaker, or null if available.
     */
    public function findCaretakerConflictForBooking(int $bookingId): ?array
    {
        $targetSql = "SELECT
                b.id,
                b.caretaker_id,
                b.basis,
                b.duration,
                b.preferred_time,
                COALESCE(b.service_start_date, b.booking_date) AS start_date,
                CASE
                    WHEN LOWER(TRIM(b.basis)) = 'hourly' THEN COALESCE(b.service_start_date, b.booking_date)
                    WHEN LOWER(TRIM(b.basis)) = 'monthly' THEN DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) MONTH), INTERVAL 1 DAY)
                    WHEN LOWER(TRIM(b.basis)) = 'yearly' THEN DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) YEAR), INTERVAL 1 DAY)
                    ELSE DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) DAY), INTERVAL 1 DAY)
                END AS end_date
            FROM bookings b
            WHERE b.id = ?
            LIMIT 1";

        $targetStmt = $this->conn->prepare($targetSql);
        $targetStmt->bind_param("i", $bookingId);
        $targetStmt->execute();
        $target = $targetStmt->get_result()->fetch_assoc();
        $targetStmt->close();

        if (!$target || empty($target['caretaker_id'])) {
            return null;
        }

        $conflictSql = "SELECT
                b.id AS conflict_booking_id,
                b.client_id,
                b.status,
                b.basis,
                b.duration,
                b.preferred_time,
                COALESCE(b.service_start_date, b.booking_date) AS start_date,
                CASE
                    WHEN LOWER(TRIM(b.basis)) = 'hourly' THEN COALESCE(b.service_start_date, b.booking_date)
                    WHEN LOWER(TRIM(b.basis)) = 'monthly' THEN DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) MONTH), INTERVAL 1 DAY)
                    WHEN LOWER(TRIM(b.basis)) = 'yearly' THEN DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) YEAR), INTERVAL 1 DAY)
                    ELSE DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) DAY), INTERVAL 1 DAY)
                END AS end_date
            FROM bookings b
            WHERE b.caretaker_id = ?
              AND b.id <> ?
              AND LOWER(TRIM(b.status)) IN ('requested','payment_requested','advance_paid','accepted','change_requested','reschedule_requested')
              AND COALESCE(b.service_start_date, b.booking_date) <= ?
              AND (
                CASE
                    WHEN LOWER(TRIM(b.basis)) = 'hourly' THEN COALESCE(b.service_start_date, b.booking_date)
                    WHEN LOWER(TRIM(b.basis)) = 'monthly' THEN DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) MONTH), INTERVAL 1 DAY)
                    WHEN LOWER(TRIM(b.basis)) = 'yearly' THEN DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) YEAR), INTERVAL 1 DAY)
                    ELSE DATE_SUB(DATE_ADD(COALESCE(b.service_start_date, b.booking_date), INTERVAL GREATEST(b.duration, 1) DAY), INTERVAL 1 DAY)
                END
              ) >= ?
            ORDER BY start_date ASC";

        $conflictStmt = $this->conn->prepare($conflictSql);
        $caretakerId = (int)$target['caretaker_id'];
        $startDate = (string)$target['start_date'];
        $endDate = (string)$target['end_date'];
        $conflictStmt->bind_param("iiss", $caretakerId, $bookingId, $endDate, $startDate);
        $conflictStmt->execute();
        $candidates = $conflictStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $conflictStmt->close();

        if (empty($candidates)) {
            return null;
        }

        $targetBasis = strtolower(trim((string)$target['basis']));
        [$targetStartTime, $targetEndTime] = $this->getTimeRangeFromBookingTime((string)($target['preferred_time'] ?? ''));

        foreach ($candidates as $candidate) {
            $candidateBasis = strtolower(trim((string)$candidate['basis']));

            // Any non-hourly conflicting booking blocks availability.
            if ($candidateBasis !== 'hourly') {
                return $candidate;
            }

            // Hourly vs non-hourly: check target time window overlap with hourly booking.
            if ($targetBasis !== 'hourly') {
                [$candidateStartTime, $candidateEndTime] = $this->getTimeRangeFromBookingTime((string)($candidate['preferred_time'] ?? ''));
                if ($this->rangesOverlap($targetStartTime, $targetEndTime, $candidateStartTime, $candidateEndTime)) {
                    return $candidate;
                }
                continue;
            }

            // Hourly vs hourly: check time overlap.
            [$candidateStartTime, $candidateEndTime] = $this->getTimeRangeFromBookingTime((string)($candidate['preferred_time'] ?? ''));
            if ($this->rangesOverlap($targetStartTime, $targetEndTime, $candidateStartTime, $candidateEndTime)) {
                return $candidate;
            }
        }

        return null;
    }

    public function getPaymentSummary(): array
    {
        $summary = [
            'pending_approvals' => 0,
            'approved_payments' => 0,
            'rejected_payments' => 0,
            'pending_recurring' => 0,
            'overdue_recurring' => 0,
            'paid_recurring' => 0,
            'amount_pending_approval' => 0,
            'amount_approved' => 0,
            'amount_overdue' => 0
        ];

        $paymentsSql = "SELECT
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_approvals,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_payments,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_payments,
                SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) AS amount_pending_approval,
                SUM(CASE WHEN status = 'approved' THEN amount ELSE 0 END) AS amount_approved
            FROM payments";

        $res1 = $this->conn->query($paymentsSql);
        if ($res1) {
            $row = $res1->fetch_assoc();
            $summary['pending_approvals'] = (int)($row['pending_approvals'] ?? 0);
            $summary['approved_payments'] = (int)($row['approved_payments'] ?? 0);
            $summary['rejected_payments'] = (int)($row['rejected_payments'] ?? 0);
            $summary['amount_pending_approval'] = (float)($row['amount_pending_approval'] ?? 0);
            $summary['amount_approved'] = (float)($row['amount_approved'] ?? 0);
        }

        $recurringSql = "SELECT
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_recurring,
                SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) AS overdue_recurring,
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) AS paid_recurring,
                SUM(CASE WHEN status = 'overdue' THEN amount ELSE 0 END) AS amount_overdue
            FROM recurring_payments";

        $res2 = $this->conn->query($recurringSql);
        if ($res2) {
            $row = $res2->fetch_assoc();
            $summary['pending_recurring'] = (int)($row['pending_recurring'] ?? 0);
            $summary['overdue_recurring'] = (int)($row['overdue_recurring'] ?? 0);
            $summary['paid_recurring'] = (int)($row['paid_recurring'] ?? 0);
            $summary['amount_overdue'] = (float)($row['amount_overdue'] ?? 0);
        }

        return $summary;
    }

    public function getRecurringPaymentOverview(int $limit = 100, array $filters = []): array
    {
        $limit = max(1, $limit);
        $allowedStatuses = ['pending', 'paid', 'overdue', 'cancelled'];
        $conditions = [];

        $recurringStatus = $filters['recurring_status'] ?? 'all';
        if (in_array($recurringStatus, $allowedStatuses, true)) {
            $conditions[] = "rp.status = '" . $recurringStatus . "'";
        }

        $client = trim((string)($filters['client'] ?? ''));
        if ($client !== '') {
            $escapedClient = $this->conn->real_escape_string($client);
            $conditions[] = "c.name LIKE '%{$escapedClient}%'";
        }

        $fromDate = $this->sanitizeDate($filters['from_date'] ?? '');
        if ($fromDate !== null) {
            $conditions[] = "rp.due_date >= '" . $fromDate . "'";
        }

        $toDate = $this->sanitizeDate($filters['to_date'] ?? '');
        if ($toDate !== null) {
            $conditions[] = "rp.due_date <= '" . $toDate . "'";
        }

        $whereSql = '';
        if (!empty($conditions)) {
            $whereSql = ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql = "SELECT
                rp.id,
                rp.booking_id,
                rp.client_id,
                rp.caretaker_id,
                rp.cycle_number,
                rp.cycle_type,
                rp.due_date,
                rp.amount,
                rp.status,
                rp.paid_at,
                rp.payment_id,
                rp.grace_period_end,
                rp.reminder_7_days_sent,
                rp.reminder_3_days_sent,
                rp.reminder_due_date_sent,
                rp.created_at AS recurring_created_at,
                rp.updated_at AS recurring_updated_at,
                c.name AS client_name,
                ct.name AS caretaker_name,
                b.service_type,
                b.basis,
                b.duration AS booking_duration,
                b.booking_date,
                b.preferred_time,
                b.total_payment AS booking_total_payment,
                b.status AS booking_status,
                b.district AS booking_district,
                b.street AS booking_street,
                b.address_line1 AS booking_address_line1,
                b.address_line2 AS booking_address_line2,
                b.postal_code AS booking_postal_code,
                b.service_location AS booking_service_location,
                b.customization AS booking_customization,
                b.customization_hours AS booking_customization_hours,
                b.customization_price AS booking_customization_price
            FROM recurring_payments rp
            JOIN clients c ON c.id = rp.client_id
            JOIN caretakers ct ON ct.id = rp.caretaker_id
            JOIN bookings b ON b.id = rp.booking_id
            {$whereSql}
            ORDER BY
                CASE rp.status
                    WHEN 'overdue' THEN 1
                    WHEN 'pending' THEN 2
                    WHEN 'paid' THEN 3
                    ELSE 4
                END,
                rp.due_date ASC,
                rp.id DESC
            LIMIT {$limit}";

        $result = $this->conn->query($sql);
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        return $rows;
    }

    public function getRecentPaymentTimeline(int $limit = 100, array $filters = []): array
    {
        $limit = max(1, $limit);
        $allowedStatuses = ['pending', 'approved', 'rejected'];
        $conditions = [];

        $paymentStatus = $filters['payment_status'] ?? 'all';
        if (in_array($paymentStatus, $allowedStatuses, true)) {
            $conditions[] = "p.status = '" . $paymentStatus . "'";
        }

        $client = trim((string)($filters['client'] ?? ''));
        if ($client !== '') {
            $escapedClient = $this->conn->real_escape_string($client);
            $conditions[] = "c.name LIKE '%{$escapedClient}%'";
        }

        $fromDate = $this->sanitizeDate($filters['from_date'] ?? '');
        if ($fromDate !== null) {
            $conditions[] = "DATE(p.created_at) >= '" . $fromDate . "'";
        }

        $toDate = $this->sanitizeDate($filters['to_date'] ?? '');
        if ($toDate !== null) {
            $conditions[] = "DATE(p.created_at) <= '" . $toDate . "'";
        }

        $whereSql = '';
        if (!empty($conditions)) {
            $whereSql = ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql = "SELECT
                p.id,
                p.booking_id,
                p.client_id,
                p.caretaker_id,
                p.amount,
                p.total_booking_amount,
                p.remaining_balance,
                p.payment_type,
                p.payment_method,
                p.status,
                p.due_date,
                p.paid_date,
                p.created_at,
                p.approved_at,
                p.customization_price AS payment_customization_price,
                c.name AS client_name,
                ct.name AS caretaker_name,
                b.service_type,
                b.basis,
                b.duration AS booking_duration,
                b.booking_date,
                b.preferred_time,
                b.total_payment AS booking_total_payment,
                b.status AS booking_status,
                b.district AS booking_district,
                b.street AS booking_street,
                b.address_line1 AS booking_address_line1,
                b.address_line2 AS booking_address_line2,
                b.postal_code AS booking_postal_code,
                b.service_location AS booking_service_location,
                b.customization AS booking_customization,
                b.customization_hours AS booking_customization_hours,
                b.customization_price AS booking_customization_price
            FROM payments p
            JOIN clients c ON c.id = p.client_id
            JOIN caretakers ct ON ct.id = p.caretaker_id
            JOIN bookings b ON b.id = p.booking_id
            {$whereSql}
            ORDER BY p.created_at DESC
            LIMIT {$limit}";

        $result = $this->conn->query($sql);
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        return $rows;
    }

    private function sanitizeDate($value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        $dt = DateTime::createFromFormat('Y-m-d', $value);
        if (!$dt || $dt->format('Y-m-d') !== $value) {
            return null;
        }

        return $value;
    }
}
