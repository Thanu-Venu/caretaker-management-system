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
                       rr.reason,
                       rr.created_at,
                       rr.old_date,
                       rr.new_date,
                       rr.status,
                       rr.hr_note,
                       rr.reviewed_at,
                       b.service_type,
                       c.name AS client_name,
                       ct.name AS caretaker_name
                FROM reschedule_requests rr
                JOIN bookings b ON rr.booking_id = b.id
                JOIN clients c ON rr.client_id = c.id
                JOIN caretakers ct ON b.caretaker_id = ct.id
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
                       rr.reason,
                       rr.created_at,
                       rr.old_date,
                       rr.new_date,
                       rr.status,
                       rr.hr_note,
                       rr.reviewed_at,
                       b.service_type,
                       c.name AS client_name,
                       ct.name AS caretaker_name
                FROM reschedule_requests rr
                JOIN bookings b ON rr.booking_id = b.id
                JOIN clients c ON rr.client_id = c.id
                JOIN caretakers ct ON b.caretaker_id = ct.id
                WHERE rr.status IN ('approved', 'rejected')
                ORDER BY rr.reviewed_at DESC";

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Mark a request approved and update the underlying booking.
     * Returns the booking id when successful, false otherwise.
     */
    public function approveRequest($requestId, $hrNote = '')
    {
        // fetch the request details so we know what values to apply
        $stmt = $this->conn->prepare("SELECT booking_id, new_date FROM reschedule_requests WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $req = $stmt->get_result()->fetch_assoc();
        if (!$req) return false;

        // update booking with new date/time/duration
        $upd = $this->conn->prepare("UPDATE bookings SET booking_date = ? WHERE id = ?");
        $upd->bind_param("si", $req['new_date'], $req['booking_id']);
        $upd->execute();

        // update request record with hr_note and reviewed_at
        $stmt2 = $this->conn->prepare("UPDATE reschedule_requests SET status = 'approved', hr_note = ?, reviewed_at = NOW() WHERE id = ?");
        $stmt2->bind_param("si", $hrNote, $requestId);
        $stmt2->execute();

        return $req['booking_id'];
    }

    /**
     * Reject a reschedule request.
     */
    public function rejectRequest($requestId, $hrNote = '')
    {
        $stmt = $this->conn->prepare("UPDATE reschedule_requests SET status = 'rejected', hr_note = ?, reviewed_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $hrNote, $requestId);
        return $stmt->execute();
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
}
