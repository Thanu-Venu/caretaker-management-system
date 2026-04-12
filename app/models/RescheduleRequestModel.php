<?php
require_once APPROOT . '/core/Database.php';

class RescheduleRequestModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /**
     * Create a new reschedule request. Expects an array with keys:
     * booking_id, client_id, old_date, old_time, new_date, new_time, reason
     */
    public function createRequest(array $data)
    {
        $sql = "INSERT INTO reschedule_requests
                (booking_id, client_id, old_date, new_date, reason, status)
                VALUES (?, ?, ?, ?, ?,'pending')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iisss",
            $data['booking_id'],
            $data['client_id'],
            $data['old_date'],
            $data['new_date'],
            $data['reason']
        );

        return $stmt->execute();
    }

    /**
     * Fetch pending reschedule requests along with relevant booking/client info.
     */
    public function getPendingRequests()
    {
        $sql = "SELECT rr.id AS request_id,
                       rr.booking_id,
                       rr.client_id,
                       rr.reason,
                       rr.created_at,
                       rr.old_date,
                       rr.new_date,
                       rr.status,
                       rr.hr_note,
                       rr.reviewed_at,
                       b.service_type,
                       b.basis,
                       b.duration,
                       b.total_payment,
                       b.booking_date,
                       b.preferred_time,
                       b.caretaker_id AS booking_caretaker_id,
                       b.status AS booking_status,
                       b.service_start_date,
                       b.district,
                       b.street,
                       b.address_line1,
                       b.address_line2,
                       b.postal_code,
                       b.customization,
                       b.customization_hours,
                       b.customization_price,
                       b.created_at AS booking_created_at,
                       b.advance_months,
                       b.total_months,
                       b.advance_balance,
                       b.cancellation_reason,
                       b.cancelled_at,
                       b.caretaker_changed_once,
                       c.name AS client_name,
                       ct.name AS caretaker_name
                FROM reschedule_requests rr
                JOIN bookings b ON rr.booking_id = b.id
                JOIN clients c ON rr.client_id = c.id
                LEFT JOIN caretakers ct ON b.caretaker_id = ct.id
                WHERE rr.status = 'pending'
                ORDER BY rr.created_at DESC";

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Fetch completed reschedule requests (approved or rejected).
     */
    public function getCompletedRequests()
    {
        $sql = "SELECT rr.id AS request_id,
                       rr.booking_id,
                       rr.client_id,
                       rr.reason,
                       rr.created_at,
                       rr.old_date,
                       rr.new_date,
                       rr.status,
                       rr.hr_note,
                       rr.reviewed_at,
                       b.service_type,
                       b.basis,
                       b.duration,
                       b.total_payment,
                       b.booking_date,
                       b.preferred_time,
                       b.caretaker_id AS booking_caretaker_id,
                       b.status AS booking_status,
                       b.service_start_date,
                       b.district,
                       b.street,
                       b.address_line1,
                       b.address_line2,
                       b.postal_code,
                       b.customization,
                       b.customization_hours,
                       b.customization_price,
                       b.created_at AS booking_created_at,
                       b.advance_months,
                       b.total_months,
                       b.advance_balance,
                       b.cancellation_reason,
                       b.cancelled_at,
                       b.caretaker_changed_once,
                       c.name AS client_name,
                       ct.name AS caretaker_name
                FROM reschedule_requests rr
                JOIN bookings b ON rr.booking_id = b.id
                JOIN clients c ON rr.client_id = c.id
                LEFT JOIN caretakers ct ON b.caretaker_id = ct.id
                WHERE rr.status IN ('approved', 'rejected')
                ORDER BY rr.reviewed_at DESC";

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Mark a request approved and update the underlying booking.
     * Uses transactions to ensure atomic operation.
     * Returns the booking id when successful, false otherwise.
     */
    public function approveRequest($requestId, $hrNote = '')
    {
        $this->conn->begin_transaction();

        try {
            $stmt = $this->conn->prepare(
                "SELECT booking_id, new_date, status FROM reschedule_requests WHERE id = ? FOR UPDATE"
            );
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $req = $stmt->get_result()->fetch_assoc();

            if (!$req || strtolower(trim((string) ($req['status'] ?? ''))) !== 'pending') {
                $this->conn->rollback();
                return false;
            }

            $bookingId = (int) $req['booking_id'];

            $upd = $this->conn->prepare("UPDATE bookings SET booking_date = ? WHERE id = ?");
            $upd->bind_param("si", $req['new_date'], $bookingId);

            if (!$upd->execute()) {
                throw new Exception("Failed to update booking date.");
            }

            $stmt2 = $this->conn->prepare(
                "UPDATE reschedule_requests
                 SET status = 'approved', hr_note = ?, reviewed_at = NOW()
                 WHERE id = ? AND status = 'pending'"
            );
            $stmt2->bind_param("si", $hrNote, $requestId);

            if (!$stmt2->execute() || $stmt2->affected_rows === 0) {
                throw new Exception("Failed to update reschedule request status.");
            }

            $this->conn->commit();
            return $bookingId;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Reschedule approval failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject a reschedule request.
     */
    public function rejectRequest($requestId, $hrNote = '')
    {
        $stmt = $this->conn->prepare(
            "UPDATE reschedule_requests
             SET status = 'rejected', hr_note = ?, reviewed_at = NOW()
             WHERE id = ? AND status = 'pending'"
        );
        $stmt->bind_param("si", $hrNote, $requestId);
        if (!$stmt->execute()) {
            return false;
        }

        return $stmt->affected_rows > 0;
    }

    /**
     * Get the most recent request for a booking (if any).
     */
    public function getRequestByBooking($bookingId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM reschedule_requests WHERE booking_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Retrieve a request record by its request id.
     */
    public function getRequestById($requestId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM reschedule_requests WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * ======================== VALIDATION HELPERS ========================
     */

    /**
     * Count how many reschedule requests exist for a booking with status 'pending' or 'approved'.
     * Business rule: Only allow ONE reschedule per booking.
     */
    public function getRescheduleCountForBooking($bookingId)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) as count
             FROM reschedule_requests
             WHERE booking_id = ? AND status IN ('pending', 'approved')"
        );
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int)($result['count'] ?? 0);
    }

    /**
     * Check if a booking already has a reschedule request (pending or approved).
     * Returns true if exists, false otherwise.
     */
    public function hasRescheduleRequest($bookingId)
    {
        return $this->getRescheduleCountForBooking($bookingId) > 0;
    }

    /**
     * Comprehensive validation: Check if a booking can be rescheduled.
     * Returns array with keys: 'valid' (bool), 'error' (string if invalid).
     *
     * Validation rules (in order):
     * 1. Booking must exist
     * 2. Booking must belong to the requesting client (ownership)
     * 3. Booking status must be 'Requested'
     * 4. No prior reschedule request (pending/approved)
     * 5. New date must not be in the past
     * 6. New date must be at least 24 hours from now
     */
    public function canReschedule($bookingId, $clientId, $newDate)
    {
        // Fetch booking
        $stmt = $this->conn->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();

        // 1. Booking exists?
        if (!$booking) {
            return ['valid' => false, 'error' => 'Booking not found.'];
        }

        // 2. Ownership check
        if ((int)$booking['client_id'] !== (int)$clientId) {
            return ['valid' => false, 'error' => 'You do not have permission to reschedule this booking.'];
        }

        // 3. Status must be 'Requested'
        if ($booking['status'] !== 'Requested') {
            return [
                'valid' => false,
                'error' => "This booking cannot be rescheduled (current status: {$booking['status']}). Only bookings with 'Requested' status can be rescheduled."
            ];
        }

        // 4. Check for existing reschedule requests
        if ($this->hasRescheduleRequest($bookingId)) {
            return [
                'valid' => false,
                'error' => 'A reschedule request has already been submitted for this booking. Only one reschedule is allowed per booking.'
            ];
        }

        // 5. Date validation: not in the past
        $today = date('Y-m-d');
        if ($newDate < $today) {
            return ['valid' => false, 'error' => 'The new date cannot be in the past.'];
        }

        // 6. Date validation: minimum 5 days advance notice
        $minDate = date('Y-m-d', strtotime('+5 days'));
        if ($newDate < $minDate) {
            return [
                'valid' => false,
                'error' => 'Reschedule requests must be made at least 5 days in advance.'
            ];
        }

        // All validations passed
        return ['valid' => true, 'booking' => $booking];
    }
}
