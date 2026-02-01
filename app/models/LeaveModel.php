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
            SELECT l.*, c.id AS caretaker_id, c.name AS caretaker_name
            FROM leaves l
            JOIN caretakers c ON l.user_id = c.id
            ORDER BY l.id DESC
        ";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getLeavesByStatus($status) {
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

    public function updateLeaveStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE leaves SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    /* ================= HR - REASSIGN + APPROVE (USING booking_reassignments) ================= */

    // Bookings affected by leave overlap (booking_date..end_date overlaps leaveStart..leaveEnd)
    public function getAffectedBookingsRange($caretakerId, $leaveStart, $leaveEnd) {
        $sql = "SELECT *
                FROM bookings
                WHERE caretaker_id = ?
                  AND status IN ('Pending','Accepted')
                  AND booking_date <= ?
                  AND end_date >= ?
                ORDER BY booking_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $caretakerId, $leaveEnd, $leaveStart);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Replacement cannot have an approved leave that overlaps
    public function replacementHasApprovedLeaveConflict($replacementId, $startDate, $endDate) {
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
    public function replacementHasBookingConflict($replacementId, $startDate, $endDate) {
        if (empty($replacementId)) return false;

        $sql = "SELECT COUNT(*) AS cnt
                FROM bookings
                WHERE caretaker_id = ?
                  AND status IN ('Pending','Accepted')
                  AND booking_date <= ?
                  AND end_date >= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $replacementId, $endDate, $startDate);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return ((int)($row['cnt'] ?? 0)) > 0;
    }

    // Replacement cannot already be assigned as a replacement in another reassignment range that overlaps
    public function replacementHasReassignmentConflict($replacementId, $startDate, $endDate) {
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
    public function approveLeave($leaveId, $replacementId, $hrId, $hrNote = '') {
        if (empty($replacementId)) {
            $sql = "UPDATE leaves
                    SET status='Approved',
                        approved_by=?,
                        approved_at=NOW(),
                        replacement_caretaker_id=NULL,
                        hr_note=?
                    WHERE id=? AND status='Pending'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("isi", $hrId, $hrNote, $leaveId);
            return $stmt->execute();
        }

        $sql = "UPDATE leaves
                SET status='Approved',
                    approved_by=?,
                    approved_at=NOW(),
                    replacement_caretaker_id=?,
                    hr_note=?
                WHERE id=? AND status='Pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisi", $hrId, $replacementId, $hrNote, $leaveId);
        return $stmt->execute();
    }

    /**
     * Insert reassignment rows for all affected bookings.
     * NOTE: We store only the overlap portion per booking (better than storing whole leave blindly).
     */
    private function createReassignmentsForLeave($oldCaretakerId, $replacementId, $hrId, $leaveStart, $leaveEnd, $note='') {
        $affected = $this->getAffectedBookingsRange($oldCaretakerId, $leaveStart, $leaveEnd);
        if (empty($affected)) return true;

        $sql = "INSERT INTO booking_reassignments
                (booking_id, old_caretaker_id, new_caretaker_id, start_date, end_date, reassigned_by, reassigned_at, note)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $this->conn->prepare($sql);

        foreach ($affected as $b) {
            $bookingId = (int)$b['id'];

            $bStart = $b['booking_date'];
            $bEnd   = $b['end_date'] ?: $bStart;

            $oStart = (strtotime($bStart) > strtotime($leaveStart)) ? $bStart : $leaveStart;
            $oEnd   = (strtotime($bEnd)   < strtotime($leaveEnd))   ? $bEnd   : $leaveEnd;

            $stmt->bind_param("iiissis", $bookingId, $oldCaretakerId, $replacementId, $oStart, $oEnd, $hrId, $note);
            if (!$stmt->execute()) return false;
        }

        return true;
    }

    /**
     * Full transaction: validate conflicts + create reassignment records (if needed) + approve leave.
     * Does NOT modify bookings table.
     */
    public function approveLeaveWithReassign($leaveId, $replacementId, $hrId, $hrNote = '') {
        $leave = $this->getLeaveById($leaveId);
        if (!$leave) return ['ok'=>false, 'message'=>'Leave not found'];
        if (strtolower($leave->status) !== 'pending') return ['ok'=>false, 'message'=>'Leave is not pending'];

        $leaveStart = $leave->start_date;
        $leaveEnd   = $leave->end_date;
        $oldCaretakerId = (int)$leave->user_id;

        $affected = $this->getAffectedBookingsRange($oldCaretakerId, $leaveStart, $leaveEnd);

        // If bookings are affected, replacement is required
        if (!empty($affected) && empty($replacementId)) {
            return ['ok'=>false, 'message'=>'Replacement caretaker is required because bookings are affected'];
        }

        // Validate conflicts if replacement is selected
        if (!empty($replacementId)) {
            if ($this->replacementHasApprovedLeaveConflict($replacementId, $leaveStart, $leaveEnd)) {
                return ['ok'=>false, 'message'=>'Replacement has an approved leave in this date range'];
            }
            if ($this->replacementHasBookingConflict($replacementId, $leaveStart, $leaveEnd)) {
                return ['ok'=>false, 'message'=>'Replacement already has bookings in this date range'];
            }
            if ($this->replacementHasReassignmentConflict($replacementId, $leaveStart, $leaveEnd)) {
                return ['ok'=>false, 'message'=>'Replacement is already assigned as a replacement in this date range'];
            }
        }

        $this->conn->begin_transaction();
        try {
            // Create reassignment records only if affected bookings exist
            if (!empty($affected)) {
                $ok = $this->createReassignmentsForLeave($oldCaretakerId, $replacementId, $hrId, $leaveStart, $leaveEnd, $hrNote);
                if (!$ok) throw new Exception("Failed to create reassignment records");
            }

            // Approve leave (replacement can be NULL)
            if (!$this->approveLeave($leaveId, $replacementId, $hrId, $hrNote)) {
                throw new Exception("Failed to approve leave");
            }

            $this->conn->commit();

            return [
                'ok' => true,
                'message' => empty($affected)
                    ? "Leave approved (no affected bookings)"
                    : "Leave approved and reassignment records saved"
            ];

        } catch (Exception $e) {
            $this->conn->rollback();
            return ['ok'=>false, 'message'=>$e->getMessage()];
        }
    }

    /* ================= Replacement caretakers list ================= */

    private function getSingleReplacementCriteria($affectedBookings) {
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

    public function getEligibleReplacementCaretakers($leaveId) {
        $leave = $this->getLeaveById($leaveId);
        if (!$leave) return ['ok' => false, 'message' => 'Leave not found', 'caretakers' => []];

        $oldCaretakerId = (int)$leave->user_id;
        $leaveStart = $leave->start_date;
        $leaveEnd   = $leave->end_date;

        $affected = $this->getAffectedBookingsRange($oldCaretakerId, $leaveStart, $leaveEnd);

        $criteria = $this->getSingleReplacementCriteria($affected);
        if (!$criteria['ok']) {
            return ['ok' => false, 'message' => $criteria['message'], 'caretakers' => [], 'affected' => $affected];
        }

        // If no affected bookings, show active caretakers (excluding same caregiver)
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

        $serviceType = $criteria['service_type'];
        $district    = $criteria['district'];

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

              -- no booking conflict (range overlap)
              AND NOT EXISTS (
                  SELECT 1
                  FROM bookings b2
                  WHERE b2.caretaker_id = c.id
                    AND b2.status IN ('Pending','Accepted')
                    AND b2.booking_date <= ?
                    AND b2.end_date >= ?
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
        $stmt->bind_param(
            "issssssss",
            $oldCaretakerId,
            $serviceType,
            $district,
            $leaveEnd, $leaveStart,
            $leaveEnd, $leaveStart,
            $leaveEnd, $leaveStart
        );
        $stmt->execute();

        return [
            'ok' => true,
            'message' => '',
            'caretakers' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC),
            'affected' => $affected,
            'criteria' => ['service_type'=>$serviceType,'district'=>$district]
        ];
    }

    /* ================= Helper: assigned caretaker for a date ================= */

    public function getAssignedCaretakerForBookingOnDate($bookingId, $date) {
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

    public function countAllLeaves(): int {
    $sql = "SELECT COUNT(*) AS total FROM leaves";
    $row = $this->conn->query($sql)->fetch_assoc();
    return (int)($row['total'] ?? 0);
}

public function getLeavesPage(int $limit, int $offset): array {
    $limit  = max(1, (int)$limit);
    $offset = max(0, (int)$offset);

    $sql = "SELECT l.*, c.id AS caretaker_id, c.name AS caretaker_name
            FROM leaves l
            JOIN caretakers c ON l.user_id = c.id
            ORDER BY l.start_date DESC
            LIMIT $limit OFFSET $offset";

    $result = $this->conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

}
