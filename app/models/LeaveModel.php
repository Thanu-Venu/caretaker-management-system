<?php
require_once APPROOT . '/core/Database.php';

class LeaveModel
{
    private $conn;
    public const MAX_DAYS_PER_REQUEST = 5;
    public const MONTHLY_LEAVE_LIMIT = 5;
    public const ADVANCE_NOTICE_DAYS = 3;
    private $notificationModel;

    private function bookingEndDateExpr(): string
    {
        return "
        CASE
            WHEN basis = 'Daily'   THEN DATE_ADD(booking_date, INTERVAL (duration - 1) DAY)
            WHEN basis = 'Weekly'  THEN DATE_ADD(booking_date, INTERVAL (duration*7 - 1) DAY)
            WHEN basis = 'Monthly' THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL duration MONTH), INTERVAL 1 DAY)
            WHEN basis = 'Yearly'  THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL duration YEAR), INTERVAL 1 DAY)
            ELSE booking_date
        END
    ";
    }
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
        // Use NotificationModel for notifications
        require_once APPROOT . '/models/NotificationModel.php';
        $this->notificationModel = new NotificationModel();
    }

    private function bindDynamicParams(mysqli_stmt $stmt, string $types, array &$params): void
    {
        $bindValues = [$types];
        foreach ($params as $key => &$value) {
            $bindValues[] = &$params[$key];
        }
        call_user_func_array([$stmt, 'bind_param'], $bindValues);
    }

    /* ================= CARETAKER ================= */

    public function getLeavesByUser($userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM leaves WHERE user_id=? ORDER BY start_date DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getLeaveByIdWithCaretaker($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT l.*, c.name AS caretaker_name
             FROM leaves l
             LEFT JOIN caretakers c ON c.id = l.user_id
             WHERE l.id = ?
             LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getLeaveById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM leaves WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    public function calculateInclusiveDays(string $startDate, string $endDate): int
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        if ($end < $start) {
            return 0;
        }
        return (int)$start->diff($end)->format('%a') + 1;
    }

    public function hasOverlappingLeave(
        int $userId,
        string $startDate,
        string $endDate,
        array $statuses = ['Approved', 'Pending'],
        ?int $excludeLeaveId = null
    ): bool {
        if (empty($statuses)) {
            return false;
        }

        $statusPlaceholders = implode(',', array_fill(0, count($statuses), '?'));
        $types = 'iss';
        $params = [$userId, $endDate, $startDate];

        $sql = "SELECT COUNT(*) AS cnt
                FROM leaves
                WHERE user_id = ?
                  AND start_date <= ?
                  AND end_date >= ?
                  AND status IN ($statusPlaceholders)";

        foreach ($statuses as $status) {
            $types .= 's';
            $params[] = $status;
        }

        if ($excludeLeaveId !== null) {
            $sql .= ' AND id <> ?';
            $types .= 'i';
            $params[] = $excludeLeaveId;
        }

        $stmt = $this->conn->prepare($sql);
        $this->bindDynamicParams($stmt, $types, $params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return ((int)($row['cnt'] ?? 0)) > 0;
    }

    public function getMonthlyLeaveUsage(
        int $userId,
        int $year,
        int $month,
        bool $includePending = true,
        ?int $excludeLeaveId = null
    ): int {
        $monthStart = sprintf('%04d-%02d-01', $year, $month);
        $monthEnd = date('Y-m-t', strtotime($monthStart));

        $statuses = $includePending ? ['Approved', 'Pending'] : ['Approved'];
        $statusPlaceholders = implode(',', array_fill(0, count($statuses), '?'));

        $sql = "SELECT COALESCE(SUM(
                    DATEDIFF(
                        LEAST(end_date, ?),
                        GREATEST(start_date, ?)
                    ) + 1
                ), 0) AS used_days
                FROM leaves
                WHERE user_id = ?
                  AND status IN ($statusPlaceholders)
                  AND start_date <= ?
                  AND end_date >= ?";

        $types = 'ssi';
        $params = [$monthEnd, $monthStart, $userId];

        foreach ($statuses as $status) {
            $types .= 's';
            $params[] = $status;
        }

        $types .= 'ss';
        $params[] = $monthEnd;
        $params[] = $monthStart;

        if ($excludeLeaveId !== null) {
            $sql .= ' AND id <> ?';
            $types .= 'i';
            $params[] = $excludeLeaveId;
        }

        $stmt = $this->conn->prepare($sql);
        $this->bindDynamicParams($stmt, $types, $params);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['used_days'] ?? 0);
    }

    public function getCurrentMonthLeaveSummary(int $userId, bool $includePending = true): array
    {
        $year = (int)date('Y');
        $month = (int)date('m');
        $used = $this->getMonthlyLeaveUsage($userId, $year, $month, $includePending);
        $remaining = max(0, self::MONTHLY_LEAVE_LIMIT - $used);
        $percentage = (int)min(100, round(($used / self::MONTHLY_LEAVE_LIMIT) * 100));

        return [
            'limit' => self::MONTHLY_LEAVE_LIMIT,
            'used' => $used,
            'remaining' => $remaining,
            'includePending' => $includePending,
            'percentage' => $percentage,
            'label' => $used . ' / ' . self::MONTHLY_LEAVE_LIMIT . ' days used',
            'monthLabel' => date('F Y')
        ];
    }

    public function getLeaveDaysWithinMonth(string $startDate, string $endDate, int $year, int $month): int
    {
        $monthStart = sprintf('%04d-%02d-01', $year, $month);
        $monthEnd = date('Y-m-t', strtotime($monthStart));

        if ($startDate > $monthEnd || $endDate < $monthStart) {
            return 0;
        }

        $overlapStart = max($startDate, $monthStart);
        $overlapEnd = min($endDate, $monthEnd);
        return $this->calculateInclusiveDays($overlapStart, $overlapEnd);
    }

    public function getActiveBookingImpactSummary(int $caretakerId, string $leaveStart, string $leaveEnd): array
    {
        $affected = $this->getAffectedBookingsRange($caretakerId, $leaveStart, $leaveEnd);
        $bookingIds = [];
        $serviceDates = [];

        foreach ($affected as $booking) {
            $bookingIds[] = (int)$booking['id'];
            $serviceDates[] = [
                'booking_id' => (int)$booking['id'],
                'start_date' => $booking['booking_date'],
                'end_date' => $booking['booking_end_date'] ?? $booking['booking_date']
            ];
        }

        return [
            'count' => count($affected),
            'booking_ids' => $bookingIds,
            'service_dates' => $serviceDates,
            'affected' => $affected,
            'replacement_required' => !empty($affected)
        ];
    }

    public function addLeave($data)
    {
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
        $ok = $stmt->execute();
        if (!$ok) {
            return false;
        }

        $leaveId = $this->conn->insert_id;

        $title = "New Leave Request";
        $message = "Caretaker ID: {$data['user_id']}\n"
            . "Type: {$data['leave_type']}\n"
            . "Dates: {$data['start_date']} to {$data['end_date']}\n"
            . "Time: {$data['start_time']} - {$data['end_time']}\n"
            . "Reason: {$data['reason']}";
        // Notify Manager
        $managerLink = URLROOT . "/public?url=hr/hr_leave";
        $this->notifyRoleUsers("Manager", $title, $message, $managerLink);

        // Notify Admin
        $adminLink = URLROOT . "/public?url=admin/ad_leave";
        $this->notifyRoleUsers("admin", $title, $message, $adminLink);
        return true;
    }

    public function updateLeave($data)
    {
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

    public function deleteLeave($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM leaves WHERE id=? AND status='Pending'"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* ================= HR / ADMIN ================= */

    public function getAllLeaves()
    {
        $sql = "
            SELECT l.*, c.id AS caretaker_id, c.name AS caretaker_name
            FROM leaves l
            JOIN caretakers c ON l.user_id = c.id
            ORDER BY l.id DESC
        ";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getLeavesByStatus($status)
    {
        $stmt = $this->conn->prepare(
            "SELECT l.*, c.id AS caretaker_id, c.name AS caretaker_name
             FROM leaves l
             JOIN caretakers c ON l.user_id = c.id
             WHERE l.status=?
             ORDER BY l.start_date DESC"
        );
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateLeaveStatus($id, $status)
    {
        $allowed = ['Pending', 'Approved', 'Rejected', 'Cancelled'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE leaves SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    /**
     * HR rejection: set status + note only when still pending.
     */
    public function rejectLeave(int $leaveId, string $hrNote = ''): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE leaves SET status = 'Rejected', hr_note = ? WHERE id = ? AND status = 'Pending'"
        );
        $stmt->bind_param("si", $hrNote, $leaveId);
        if (!$stmt->execute()) {
            return false;
        }

        return $stmt->affected_rows > 0;
    }

    /* ================= HR - REASSIGN + APPROVE (PRIMARY: booking_reassignments) ================= */

    // Bookings affected by leave overlap (booking_date..end_date overlaps leaveStart..leaveEnd)
    public function getAffectedBookingsRange($caretakerId, $leaveStart, $leaveEnd)
    {
        $endExpr = $this->bookingEndDateExpr();

        $sql = "SELECT b.*,
                   ($endExpr) AS booking_end_date
            FROM bookings b
            WHERE b.caretaker_id = ?
              AND b.status IN ('Requested','Payment_Requested','Advance_Paid','Accepted','Change_Requested')
              AND b.booking_date <= ?
              AND ($endExpr) >= ?
            ORDER BY b.booking_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iss', $caretakerId, $leaveEnd, $leaveStart);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Replacement cannot have an approved leave that overlaps
    public function replacementHasApprovedLeaveConflict($replacementId, $startDate, $endDate)
    {
        if (empty($replacementId)) return false;

        $sql = "SELECT COUNT(*) AS cnt
                FROM leaves
                WHERE user_id = ?
                  AND status = 'Approved'
                  AND start_date <= ?
                  AND end_date >= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $replacementId, $endDate, $startDate);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return ((int)($row['cnt'] ?? 0)) > 0;
    }

    // Replacement cannot have another booking that overlaps
    public function replacementHasBookingConflict($replacementId, $startDate, $endDate)
    {
        if (empty($replacementId)) return false;

        $endExpr = $this->bookingEndDateExpr();

        $sql = "SELECT COUNT(*) AS cnt
            FROM bookings b
            WHERE b.caretaker_id = ?
              AND b.status IN ('Requested','Payment_Requested','Advance_Paid','Accepted','Change_Requested')
              AND b.booking_date <= ?
              AND ($endExpr) >= ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $replacementId, $endDate, $startDate);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return ((int)($row['cnt'] ?? 0)) > 0;
    }

    // Replacement cannot already be assigned as a replacement in another reassignment range that overlaps
    public function replacementHasReassignmentConflict($replacementId, $startDate, $endDate)
    {
        if (empty($replacementId)) return false;

        $sql = "SELECT COUNT(*) AS cnt
                FROM booking_reassignments
                WHERE new_caretaker_id = ?
                  AND start_date <= ?
                  AND end_date >= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $replacementId, $endDate, $startDate);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return ((int)($row['cnt'] ?? 0)) > 0;
    }

    // Approve leave (replacement optional)
    public function approveLeave($leaveId, $replacementId, $hrId, $hrNote = '')
    {
        if (empty($replacementId)) {
            $sql = "UPDATE leaves
                    SET status='Approved',
                        approved_at=NOW(),
                        replacement_caretaker_id=NULL,
                        hr_note=?
                    WHERE id=? AND status='Pending'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $hrNote, $leaveId);
            return $stmt->execute();
        }

        $sql = "UPDATE leaves
                SET status='Approved',
                    approved_at=NOW(),
                    replacement_caretaker_id=?,
                    hr_note=?
                WHERE id=? AND status='Pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isi", $replacementId, $hrNote, $leaveId);
        return $stmt->execute();
    }

    /**
     * Insert reassignment rows for all affected bookings.
     * NOTE: We store only the overlap portion per booking (better than storing whole leave blindly).
     */
    private function createReassignmentsForLeave($leaveId, $oldCaretakerId, $replacementId, $hrId, $leaveStart, $leaveEnd, $note = '')
    {
        $affected = $this->getAffectedBookingsRange($oldCaretakerId, $leaveStart, $leaveEnd);
        if (empty($affected)) return true;

        $sql = "INSERT INTO booking_reassignments
            (booking_id, old_caretaker_id, new_caretaker_id, start_date, end_date, reassigned_by, reassigned_at, note)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $this->conn->prepare($sql);

        // Backward-compat: some environments still inspect legacy leave_booking_reassignment table.
        $legacySql = "INSERT INTO leave_booking_reassignment
            (leave_id, booking_id, old_caretaker_id, new_caretaker_id, reassign_start, reassign_end, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $legacyStmt = $this->conn->prepare($legacySql);

        foreach ($affected as $b) {
            $bookingId = (int)$b['id'];
            $bStart = $b['booking_date'];
            $bEnd   = $b['booking_end_date'] ?? $bStart;

            $oStart = (strtotime($bStart) > strtotime($leaveStart)) ? $bStart : $leaveStart;
            $oEnd   = (strtotime($bEnd)   < strtotime($leaveEnd))   ? $bEnd   : $leaveEnd;

            $stmt->bind_param("iiissis", $bookingId, $oldCaretakerId, $replacementId, $oStart, $oEnd, $hrId, $note);
            if (!$stmt->execute()) return false;

            if ($legacyStmt) {
                $legacyStmt->bind_param("iiiiss", $leaveId, $bookingId, $oldCaretakerId, $replacementId, $oStart, $oEnd);
                if (!$legacyStmt->execute()) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Full transaction: validate conflicts + create reassignment records (if needed) + approve leave.
     * Does NOT modify bookings table.
     */
    public function approveLeaveWithReassign($leaveId, $replacementId, $hrId, $hrNote = '')
    {
        $leave = $this->getLeaveById($leaveId);
        if (!$leave) return ['ok' => false, 'message' => 'Leave not found'];
        if (strtolower($leave->status) !== 'pending') return ['ok' => false, 'message' => 'Leave is not pending'];

        $leaveStart = $leave->start_date;
        $leaveEnd   = $leave->end_date;
        $oldCaretakerId = (int)$leave->user_id;

        // Uses derived booking_end_date (make sure your getAffectedBookingsRange() is the fixed version)
        $affected = $this->getAffectedBookingsRange($oldCaretakerId, $leaveStart, $leaveEnd);

        // If bookings are affected, replacement is required
        if (!empty($affected) && empty($replacementId)) {
            return ['ok' => false, 'message' => 'Replacement caretaker is required because bookings are affected'];
        }

        // Validate conflicts if replacement is selected
        if (!empty($replacementId)) {
            // 1) replacement has approved leave overlap
            if ($this->replacementHasApprovedLeaveConflict($replacementId, $leaveStart, $leaveEnd)) {
                return ['ok' => false, 'message' => 'Replacement has an approved leave in this date range'];
            }

            // 2) replacement has booking overlap (make sure replacementHasBookingConflict() is also fixed to derive end date)
            if ($this->replacementHasBookingConflict($replacementId, $leaveStart, $leaveEnd)) {
                return ['ok' => false, 'message' => 'Replacement already has bookings in this date range'];
            }

            // 3) replacement already assigned as a replacement elsewhere overlap
            if ($this->replacementHasReassignmentConflict($replacementId, $leaveStart, $leaveEnd)) {
                return ['ok' => false, 'message' => 'Replacement is already assigned as a replacement in this date range'];
            }
        }

        $this->conn->begin_transaction();
        try {
            // Create reassignment records only if affected bookings exist
            if (!empty($affected)) {
                $ok = $this->createReassignmentsForLeave(
                    $leaveId,
                    $oldCaretakerId,
                    $replacementId,
                    $hrId,
                    $leaveStart,
                    $leaveEnd,
                    $hrNote
                );
                if (!$ok) throw new Exception("Failed to create reassignment records");
            }

            // Approve leave (replacement can be NULL if no affected bookings)
            if (!$this->approveLeave($leaveId, $replacementId, $hrId, $hrNote)) {
                throw new Exception("Failed to approve leave");
            }

            $this->conn->commit();

            /* ===================== NOTIFICATION TO CARETAKER ===================== */
            // Uses your common notifications table.
            // Receiver is caretaker => user_role='caretaker'
            $title = empty($affected) ? "Leave Approved" : "Leave Approved (Reassigned)";
            $msg   = "Your leave request has been approved.\n"
                . "Period: {$leaveStart} to {$leaveEnd}\n"
                . "Note: " . (trim($hrNote) !== '' ? $hrNote : '—');

            // Change this link to your caretaker leave page route
            $link  = URLROOT . "/public?url=caretaker/ct_leave";

            // Notify original caretaker via shared helper (includes role fallback handling).
            $this->notifyUser($oldCaretakerId, 'caretaker', $title, $msg, $link);

            if (!empty($affected) && !empty($replacementId)) {
                // Notify replacement caretaker
                $replacementTitle = 'New Service Assignment';
                $replacementMessage = "You have been assigned as a replacement caregiver.\n"
                    . "Leave period: {$leaveStart} to {$leaveEnd}.\n"
                    . "Affected bookings: " . count($affected) . "\n"
                    . "Please review your updated schedule.";
                $replacementLink = URLROOT . '/public?url=caretaker/ct_booking';
                $this->notifyUser((int)$replacementId, 'caretaker', $replacementTitle, $replacementMessage, $replacementLink);

                // Get replacement caregiver details for client notifications
                $sqlRepl = "SELECT id, name FROM caretakers WHERE id = ?";
                $stmtRepl = $this->conn->prepare($sqlRepl);
                $stmtRepl->bind_param("i", $replacementId);
                $stmtRepl->execute();
                $replResult = $stmtRepl->get_result()->fetch_assoc();
                $replacementName = $replResult ? $replResult['name'] : 'New Caregiver';

                // Notify all affected clients about the new caregiver
                $uniqueClients = [];
                foreach ($affected as $booking) {
                    $clientId = (int)($booking['client_id'] ?? 0);
                    if ($clientId > 0 && !isset($uniqueClients[$clientId])) {
                        $uniqueClients[$clientId] = true;

                        $clientTitle = 'Your Caregiver Has Been Changed';
                        $clientMessage = "Your caregiver has been reassigned for your service.\n"
                            . "New caregiver: {$replacementName}\n"
                            . "Service period: {$leaveStart} to {$leaveEnd}\n"
                            . "Your service will continue uninterrupted.";
                        $clientLink = URLROOT . '/client/c_booking';
                        $this->notifyUser($clientId, 'client', $clientTitle, $clientMessage, $clientLink);
                    }
                }

                // Notify HR/Manager about completion
                $hrTitle = 'Leave Approved with Reassignment';
                $hrMessage = "Leave ID {$leaveId} approved and reassigned to caregiver {$replacementName} (ID: {$replacementId}).\n"
                    . 'Affected bookings: ' . count($affected) . '\n'
                    . "Clients notified about service continuity.";
                $this->notifyRoleUsers('Manager', $hrTitle, $hrMessage, URLROOT . '/HrLeave/index');
            }

            return [
                'ok' => true,
                'message' => empty($affected)
                    ? "Leave approved (no affected bookings)"
                    : "Leave approved and reassignment records saved"
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    /* ================= Replacement caretakers list ================= */

    private function getSingleReplacementCriteria($affectedBookings)
    {
        if (empty($affectedBookings)) return ['ok' => true, 'service_type' => null, 'district' => null];

        $serviceType = $affectedBookings[0]['service_type'] ?? null;
        $district    = $affectedBookings[0]['district'] ?? null;

        foreach ($affectedBookings as $b) {
            if (($b['service_type'] ?? null) !== $serviceType) {
                return ['ok' => false, 'message' => 'Affected bookings have different service types. Use replacement-per-booking.'];
            }
            if (($b['district'] ?? null) !== $district) {
                return ['ok' => false, 'message' => 'Affected bookings are in different districts. Use replacement-per-booking.'];
            }
        }

        return ['ok' => true, 'service_type' => $serviceType, 'district' => $district];
    }

    public function getEligibleReplacementCaretakers($leaveId)
    {
        $leave = $this->getLeaveById($leaveId);
        if (!$leave) {
            return ['ok' => false, 'message' => 'Leave not found', 'caretakers' => []];
        }

        $oldCaretakerId = (int)$leave->user_id;
        $leaveStart = $leave->start_date;
        $leaveEnd   = $leave->end_date;

        // affected bookings for this caretaker during leave window (range overlap)
        $affected = $this->getAffectedBookingsRange($oldCaretakerId, $leaveStart, $leaveEnd);

        $criteria = $this->getSingleReplacementCriteria($affected);
        if (!$criteria['ok']) {
            return [
                'ok' => false,
                'message' => $criteria['message'],
                'caretakers' => [],
                'affected' => $affected
            ];
        }

        // If no affected bookings, show any active caretakers (excluding same caregiver)
        if (empty($affected)) {
            $sql = "SELECT id, name, service_type, location, rating
                FROM caretakers
                WHERE status='Active' AND id <> ?
                ORDER BY rating DESC, name ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $oldCaretakerId);
            $stmt->execute();

            return [
                'ok' => true,
                'message' => '',
                'caretakers' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC),
                'affected' => $affected
            ];
        }

        $serviceType = $criteria['service_type']; // from affected bookings
        $district    = $criteria['district'];     // from affected bookings

        // Derived booking end date (because bookings table has no end_date)
        // NOTE: These are DATE ranges (not time-of-day). Hourly is treated as same-day.
        $bookingEndExpr = "
        CASE
            WHEN b2.basis = 'Daily'   THEN DATE_ADD(b2.booking_date, INTERVAL (b2.duration - 1) DAY)
            WHEN b2.basis = 'Weekly'  THEN DATE_ADD(b2.booking_date, INTERVAL (b2.duration*7 - 1) DAY)
            WHEN b2.basis = 'Monthly' THEN DATE_SUB(DATE_ADD(b2.booking_date, INTERVAL b2.duration MONTH), INTERVAL 1 DAY)
            WHEN b2.basis = 'Yearly'  THEN DATE_SUB(DATE_ADD(b2.booking_date, INTERVAL b2.duration YEAR), INTERVAL 1 DAY)
            ELSE b2.booking_date
        END
    ";

        // Booking statuses that should block availability
        $blockingStatuses = "('Requested','Payment_Requested','Advance_Paid','Accepted','Change_Requested')";

        $sql = "
        SELECT c.id, c.name, c.service_type, c.location, c.rating
        FROM caretakers c
        WHERE c.status = 'Active'
          AND c.id <> ?
          AND c.service_type = ?
          AND c.location = ?

          -- no approved leave conflict
          AND NOT EXISTS (
              SELECT 1
              FROM leaves l2
              WHERE l2.user_id = c.id
                AND l2.status = 'Approved'
                AND l2.start_date <= ?
                AND l2.end_date >= ?
          )

          -- no booking conflict (range overlap using derived end date)
          AND NOT EXISTS (
              SELECT 1
              FROM bookings b2
              WHERE b2.caretaker_id = c.id
                AND b2.status IN $blockingStatuses
                AND b2.booking_date <= ?
                AND ($bookingEndExpr) >= ?
          )

          -- no reassignment conflict (already replacement elsewhere)
          AND NOT EXISTS (
              SELECT 1
              FROM booking_reassignments br
              WHERE br.new_caretaker_id = c.id
                AND br.start_date <= ?
                AND br.end_date >= ?
          )

        ORDER BY c.rating DESC, c.name ASC
    ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'message' => 'SQL prepare failed: ' . $this->conn->error, 'caretakers' => [], 'affected' => $affected];
        }

        // Bind order must match the placeholders:
        // 1 oldCaretakerId
        // 2 serviceType
        // 3 district(location)
        // 4 leaveEnd, 5 leaveStart (leave overlap)
        // 6 leaveEnd, 7 leaveStart (booking overlap)
        // 8 leaveEnd, 9 leaveStart (reassignment overlap)
        $stmt->bind_param(
            "issssssss",
            $oldCaretakerId,
            $serviceType,
            $district,
            $leaveEnd,
            $leaveStart,
            $leaveEnd,
            $leaveStart,
            $leaveEnd,
            $leaveStart
        );

        $stmt->execute();

        return [
            'ok' => true,
            'message' => '',
            'caretakers' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC),
            'affected' => $affected,
            'criteria' => ['service_type' => $serviceType, 'district' => $district]
        ];
    }
    /* ================= Helper: assigned caretaker for a date ================= */

    public function getAssignedCaretakerForBookingOnDate($bookingId, $date)
    {
        $sql = "SELECT
                  b.id,
                  COALESCE(r.new_caretaker_id, b.caretaker_id) AS assigned_caretaker_id
                FROM bookings b
                LEFT JOIN booking_reassignments r
                  ON r.booking_id = b.id
                 AND ? BETWEEN r.start_date AND r.end_date
                WHERE b.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $date, $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function countAllLeaves(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM leaves";
        $row = $this->conn->query($sql)->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    public function getLeavesPage(int $limit, int $offset): array
    {
        $limit  = max(1, (int)$limit);
        $offset = max(0, (int)$offset);

        $sql = "SELECT l.*,
                       c.id AS caretaker_id,
                       c.name AS caretaker_name,
                       rpl.name AS replacement_caretaker_name
                FROM leaves l
                JOIN caretakers c ON l.user_id = c.id
                LEFT JOIN caretakers rpl ON l.replacement_caretaker_id = rpl.id
                ORDER BY l.start_date DESC
                LIMIT $limit OFFSET $offset";

        $result = $this->conn->query($sql);
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        foreach ($rows as &$row) {
            $impact = $this->getActiveBookingImpactSummary((int)$row['user_id'], $row['start_date'], $row['end_date']);
            $row['affected_booking_count'] = $impact['count'];
            $row['replacement_required'] = $impact['replacement_required'];

            $year = (int)date('Y', strtotime($row['start_date']));
            $month = (int)date('m', strtotime($row['start_date']));
            $used = $this->getMonthlyLeaveUsage((int)$row['user_id'], $year, $month, true, (int)$row['id']);
            $requestDays = $this->getLeaveDaysWithinMonth($row['start_date'], $row['end_date'], $year, $month);
            $row['request_days'] = $requestDays;
            $row['monthly_used_before_request'] = $used;
            $row['monthly_used_after_request'] = $used + $requestDays;
            $row['monthly_limit'] = self::MONTHLY_LEAVE_LIMIT;
        }
        unset($row);

        return $rows;
    }

    private function notifyUser($userId, $role, $title, $message, $link = null)
    {
        // Use NotificationModel for role normalization and notification creation
        $result = $this->notificationModel->addNotification($userId, $role, $title, $message, $link ?? '#');
        if (!$result) {
            error_log("[LeaveModel] Failed to create notification for user_id={$userId}, role={$role}, title={$title}");
        }
        return $result;
    }

    /**
     * Notify all users of a given role (Manager/admin).
     * This assumes you have a users table. If you don't, tell me where Managers are stored.
     */
    private function notifyRoleUsers($role, $title, $message, $link = null)
    {
        // Use NotificationModel to get all users of the role and notify them
        $sql = "SELECT id FROM users WHERE role = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $ok = true;
        foreach ($rows as $r) {
            $uid = (int)$r['id'];
            $ok = $ok && $this->notificationModel->addNotification($uid, $role, $title, $message, $link ?? '#');
            if (!$ok) {
                error_log("[LeaveModel] Failed to notify role user_id={$uid}, role={$role}, title={$title}");
            }
        }
        return $ok;
    }
}
