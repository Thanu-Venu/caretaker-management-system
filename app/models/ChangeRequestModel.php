<?php
require_once APPROOT . '/core/Database.php';

class ChangeRequestModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function createRequest($data)
    {
        $sql = "INSERT INTO change_requests
                (booking_id, client_id, old_caretaker_id, new_caretaker_id, reason, status)
                VALUES (?, ?, ?, ?, ?, 'pending')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iiiis",
            $data['booking_id'],
            $data['client_id'],
            $data['old_caretaker_id'],
            $data['new_caretaker_id'],
            $data['reason']
        );

        return $stmt->execute();
    }

    public function getPendingRequests()
    {
        $sql = "SELECT cr.id AS request_id,
                       cr.booking_id,
                       cr.reason,
                       cr.created_at,
                       b.booking_date,
                       b.preferred_time,
                       b.service_type,
                       c.name AS client_name,
                       oldc.name AS old_caretaker,
                       newc.name AS new_caretaker
                FROM change_requests cr
                JOIN bookings b ON cr.booking_id = b.id
                JOIN clients c ON cr.client_id = c.id
                LEFT JOIN caretakers oldc ON cr.old_caretaker_id = oldc.id
                LEFT JOIN caretakers newc ON cr.new_caretaker_id = newc.id
                WHERE cr.status = 'pending'
                ORDER BY cr.created_at DESC";

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function approveRequest($requestId)
    {
        // first get the request so we know booking and new caretaker
        $stmt = $this->conn->prepare("SELECT booking_id, new_caretaker_id FROM change_requests WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $req = $stmt->get_result()->fetch_assoc();
        if (!$req) return false;

        // update booking with new caretaker
        $upd = $this->conn->prepare("UPDATE bookings SET caretaker_id = ? WHERE id = ?");
        $upd->bind_param("ii", $req['new_caretaker_id'], $req['booking_id']);
        $upd->execute();

        // mark request approved
        $stmt2 = $this->conn->prepare("UPDATE change_requests SET status = 'approved' WHERE id = ?");
        $stmt2->bind_param("i", $requestId);
        $stmt2->execute();

        return $req['booking_id'];
    }

    public function rejectRequest($requestId)
    {
        $stmt = $this->conn->prepare("UPDATE change_requests SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        return $stmt->execute();
    }

    public function getRequestByBooking($bookingId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM change_requests WHERE booking_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
