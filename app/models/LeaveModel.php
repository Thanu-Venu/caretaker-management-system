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
            ORDER BY l.start_date DESC
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
        $stmt = $this->conn->prepare(
            "UPDATE leaves SET status=? WHERE id=?"
        );
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
}

    /* ================= HR - REASSIGN + APPROVE ================= */

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


public function approveLeave($leaveId, $replacementId, $hrId, $hrNote = '') {

    // no replacement -> store NULL
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
 * Full transaction: validate + reassign + approve
 * Returns: ['ok'=>bool,'message'=>string]
 */
public function approveLeaveWithReassign($leaveId, $replacementId, $hrId, $hrNote = '') {

    $leave = $this->getLeaveById($leaveId);
    if (!$leave) return ['ok'=>false, 'message'=>'Leave not found'];
    if (strtolower($leave->status) !== 'pending') return ['ok'=>false, 'message'=>'Leave is not pending'];

    $leaveStart = $leave->start_date;
    $leaveEnd   = $leave->end_date;
    $oldCaretakerId = (int)$leave->user_id;

    $affected = $this->getAffectedBookingsRange($oldCaretakerId, $leaveStart, $leaveEnd);

    // If affected bookings exist, replacement must be selected
    if (!empty($affected) && empty($replacementId)) {
        return ['ok'=>false, 'message'=>'Replacement caretaker is required because bookings are affected'];
    }

    // If replacement is selected, check conflicts
    if (!empty($replacementId)) {
        if ($this->replacementHasApprovedLeaveConflict($replacementId, $leaveStart, $leaveEnd)) {
            return ['ok'=>false, 'message'=>'Replacement has an approved leave in this date range'];
        }
        if ($this->replacementHasBookingConflict($replacementId, $leaveStart, $leaveEnd)) {
            return ['ok'=>false, 'message'=>'Replacement already has bookings in this date range'];
        }
    }

    $this->conn->begin_transaction();
    try {

        // ✅ Split+reassign only if affected exists
        if (!empty($affected)) {
            $ok = $this->splitAndReassignAffectedBookings($oldCaretakerId, $replacementId, $hrId, $leaveStart, $leaveEnd);
            if (!$ok) throw new Exception("Failed to split and reassign bookings");
        }

        // Approve leave (your approveLeave must allow NULL replacement)
        if (!$this->approveLeave($leaveId, $replacementId, $hrId, $hrNote)) {
            throw new Exception("Failed to approve leave");
        }

        $this->conn->commit();

        return [
            'ok' => true,
            'message' => empty($affected)
                ? "Leave approved (no affected bookings)"
                : "Leave approved and booking(s) split successfully"
        ];

    } catch (Exception $e) {
        $this->conn->rollback();
        return ['ok'=>false, 'message'=>$e->getMessage()];
    }
}



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

    // If no affected bookings, you can either show all active caretakers OR still filter by caregiver profile
    // Here: if no affected bookings => show active caretakers (excluding same caregiver)
    if (empty($affected)) {
        $sql = "SELECT id, name, service_type, location, rating
                FROM caretakers
                WHERE status='Active' AND id <> ?
                ORDER BY rating DESC, name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $oldCaretakerId);
        $stmt->execute();
        return ['ok' => true, 'message' => '', 'caretakers' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'affected' => $affected];
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
        ORDER BY c.rating DESC, c.name ASC
    ";

    $stmt = $this->conn->prepare($sql);
    // types: i s s s s s s
    $stmt->bind_param(
        "issssss",
        $oldCaretakerId,
        $serviceType,
        $district,
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
        'criteria' => ['service_type'=>$serviceType,'district'=>$district]
    ];
}

private function insertBookingCloneWithDates(array $b, int $newCaretakerId, ?int $originalCaretakerId, string $newStart, string $newEnd, ?int $reassignedBy, bool $markReassigned) {

    $sql = "INSERT INTO bookings (
                client_id, caretaker_id, original_caretaker_id, reassigned_by, reassigned_at,
                service_type, basis, duration, preferred_time,
                booking_date, end_date,
                service_location, customization, total_payment, status, cancellation_reason, cancelled_at,
                district, street, address_line1, address_line2, postal_code
            ) VALUES (?,?,?,?,?,
                      ?,?,?,?,
                      ?,?,
                      ?,?,?,?, ?,?,
                      ?,?,?,?,?)";

    $stmt = $this->conn->prepare($sql);

    $client_id = (int)$b['client_id'];
    $caretaker_id = $newCaretakerId;

    // If this segment is the replacement period, store original caretaker id for history
    $orig_id = $originalCaretakerId; // can be NULL
    $rb = $markReassigned ? $reassignedBy : null;
    $ra = $markReassigned ? date('Y-m-d H:i:s') : null;

    $service_type = $b['service_type'];
    $basis = $b['basis'];
    $duration = (int)$b['duration'];
    $preferred_time = $b['preferred_time'];

    $booking_date = $newStart;
    $end_date = $newEnd;

    $service_location = $b['service_location'] ?? null;
    $customization = $b['customization'] ?? null;
    $total_payment = $b['total_payment']; // keep same for now (see note below)
    $status = $b['status'];
    $cancellation_reason = $b['cancellation_reason'] ?? null;
    $cancelled_at = $b['cancelled_at'] ?? null;

    $district = $b['district'] ?? null;
    $street = $b['street'] ?? null;
    $address1 = $b['address_line1'] ?? null;
    $address2 = $b['address_line2'] ?? null;
    $postal = $b['postal_code'] ?? null;

    // Bind mostly as strings to avoid type headaches in WAMP
    $stmt->bind_param(
  "iiisssssssssssssssssss",
  $client_id, $caretaker_id, $orig_id, $rb, $ra,
  $service_type, $basis, $duration, $preferred_time,
  $booking_date, $end_date,
  $service_location, $customization, $total_payment, $status,
  $cancellation_reason, $cancelled_at,
  $district, $street, $address1, $address2, $postal
);



    return $stmt->execute();
}
private function updateBookingDatesAndCaretaker(int $bookingId, int $caretakerId, ?int $originalCaretakerId, ?int $reassignedBy, bool $markReassigned, string $newStart, string $newEnd) {

    if ($markReassigned) {
        $sql = "UPDATE bookings
                SET caretaker_id=?,
                    original_caretaker_id=?,
                    reassigned_by=?,
                    reassigned_at=NOW(),
                    booking_date=?,
                    end_date=?
                WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiissi", $caretakerId, $originalCaretakerId, $reassignedBy, $newStart, $newEnd, $bookingId);
        return $stmt->execute();
    }

    $sql = "UPDATE bookings
            SET caretaker_id=?,
                booking_date=?,
                end_date=?
            WHERE id=?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("issi", $caretakerId, $newStart, $newEnd, $bookingId);
    return $stmt->execute();
}

private function splitAndReassignAffectedBookings(int $oldCaretakerId, int $replacementId, int $hrId, string $leaveStart, string $leaveEnd): bool {

    $affected = $this->getAffectedBookingsRange($oldCaretakerId, $leaveStart, $leaveEnd);

    foreach ($affected as $b) {
        $bookingId = (int)$b['id'];
        $bStart = $b['booking_date'];
        $bEnd   = $b['end_date'] ?: $bStart;

        // overlap segment
        $oStart = (strtotime($bStart) > strtotime($leaveStart)) ? $bStart : $leaveStart;
        $oEnd   = (strtotime($bEnd)   < strtotime($leaveEnd))   ? $bEnd   : $leaveEnd;

        // before segment
        $beforeStart = $bStart;
        $beforeEnd = date('Y-m-d', strtotime($oStart . ' -1 day'));
        $hasBefore = strtotime($beforeEnd) >= strtotime($beforeStart);

        // after segment
        $afterStart = date('Y-m-d', strtotime($oEnd . ' +1 day'));
        $afterEnd = $bEnd;
        $hasAfter = strtotime($afterEnd) >= strtotime($afterStart);

        // CASE 1: Entire booking is within leave => just reassign this one row
        if (!$hasBefore && !$hasAfter) {
            $ok = $this->updateBookingDatesAndCaretaker(
                $bookingId,
                $replacementId,
                $oldCaretakerId,
                $hrId,
                true,
                $bStart,
                $bEnd
            );
            if (!$ok) return false;
            continue;
        }

        // If there is a BEFORE part, keep existing row as BEFORE (original)
        if ($hasBefore) {
            $ok = $this->updateBookingDatesAndCaretaker(
                $bookingId,
                $oldCaretakerId,
                null,
                null,
                false,
                $beforeStart,
                $beforeEnd
            );
            if (!$ok) return false;

            // Insert overlap as replacement
            $ok = $this->insertBookingCloneWithDates($b, $replacementId, $oldCaretakerId, $oStart, $oEnd, $hrId, true);
            if (!$ok) return false;

        } else {
            // No BEFORE => make existing row the overlap (replacement)
            $ok = $this->updateBookingDatesAndCaretaker(
                $bookingId,
                $replacementId,
                $oldCaretakerId,
                $hrId,
                true,
                $oStart,
                $oEnd
            );
            if (!$ok) return false;
        }

        // Insert AFTER as original (only if needed)
        if ($hasAfter) {
            $ok = $this->insertBookingCloneWithDates($b, $oldCaretakerId, null, $afterStart, $afterEnd, null, false);
            if (!$ok) return false;
        }
    }

    return true;
    }

    

}
