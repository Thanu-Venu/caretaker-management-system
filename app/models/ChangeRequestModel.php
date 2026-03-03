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
        $this->conn->begin_transaction();

        try {
            // ✅ block if already pending for this booking
            $chk = $this->conn->prepare("
            SELECT id FROM change_requests
            WHERE booking_id = ? AND status = 'pending'
            LIMIT 1
            FOR UPDATE
        ");
            $chk->bind_param("i", $data['booking_id']);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $this->conn->rollback();
                return false;
            }

            // ✅ booking must still be Accepted at DB level (extra safety)
            $chkB = $this->conn->prepare("
            SELECT id FROM bookings
            WHERE id = ? AND LOWER(TRIM(status)) = 'accepted' AND caretaker_changed_once = 0
            FOR UPDATE
        ");
            $chkB->bind_param("i", $data['booking_id']);
            $chkB->execute();
            if ($chkB->get_result()->num_rows === 0) {
                $this->conn->rollback();
                return false;
            }

            // Insert change request
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
            $stmt->execute();

            // ✅ set booking status -> Change_Requested
            $upd = $this->conn->prepare("UPDATE bookings SET status = 'Change_Requested' WHERE id = ?");
            $upd->bind_param("i", $data['booking_id']);
            $upd->execute();

            $this->conn->commit();
            return true;
        } catch (\Throwable $e) {
            $this->conn->rollback();
            return false;
        }
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
        $this->conn->begin_transaction();

        try {
            // Get request details + ensure it's pending
            $stmt = $this->conn->prepare("
            SELECT id, booking_id, old_caretaker_id, new_caretaker_id, status
            FROM change_requests
            WHERE id = ?
            FOR UPDATE
        ");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $req = $stmt->get_result()->fetch_assoc();
            if (!$req) {
                $this->conn->rollback();
                return false;
            }



            if (strtolower(trim($req['status'])) !== 'pending') {
                $this->conn->rollback();
                return false; // already processed
            }

            // Lock booking row and validate caretaker matches old_caretaker_id
            $stmtB = $this->conn->prepare("
            SELECT id, caretaker_id, status
            FROM bookings
            WHERE id = ?
            FOR UPDATE
            ");
            $stmtB->bind_param("i", $req['booking_id']);
            $stmtB->execute();
            $booking = $stmtB->get_result()->fetch_assoc();
            if (strtolower(trim($booking['status'])) !== 'change_requested') {
                $this->conn->rollback();
                return false;
            }
            if (!$booking) {
                $this->conn->rollback();
                return false;
            }


            if ((int)$booking['caretaker_id'] !== (int)$req['old_caretaker_id']) {
                // Someone already changed caretaker OR request is stale/reversed
                $this->conn->rollback();
                return false;
            }

            // Update booking caretaker_id + reset booking status from Change_Requested if needed
            $newCt = (int)$req['new_caretaker_id'];
            $bid   = (int)$req['booking_id'];
            $upd = $this->conn->prepare("
            UPDATE bookings
                SET caretaker_id = ?, status='Accepted', caretaker_changed_once = 1
                WHERE id = ?
           ");
            $upd->bind_param("ii", $newCt, $bid);
            $upd->execute();

            // Approve this request
            $stmt2 = $this->conn->prepare("UPDATE change_requests  SET status='approved', hr_note=?, reviewed_at=NOW()
            WHERE id=?");
            $stmt2->bind_param("si", $hrNote, $requestId);
            $stmt2->execute();

            // Reject all other pending requests for same booking (prevents duplicates)
            $rej = $this->conn->prepare("
            UPDATE change_requests
            SET status = 'rejected'
            WHERE booking_id = ?
              AND id <> ?
              AND status = 'pending'
            ");
            $rej->bind_param("ii", $bid, $requestId);
            $rej->execute();

            $this->conn->commit();
            // ✅ return everything controller needs
            return [
                'booking_id' => $bid,
                'client_id' => (int)$req['client_id'],
                'old_caretaker_id' => (int)$req['old_caretaker_id'],
                'new_caretaker_id' => $newCt,
            ];
            } catch (\Throwable $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function rejectRequest(int $requestId, string $hrNote = '')
    {
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("SELECT * FROM change_requests WHERE id=? FOR UPDATE");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $req = $stmt->get_result()->fetch_assoc();
            if (!$req) {
                $this->conn->rollback();
                return false;
            }

            if (strtolower(trim($req['status'])) !== 'pending') {
                $this->conn->rollback();
                return false;
            }

            // revert booking status back to Accepted (your rule)
            $bid = (int)$req['booking_id'];
            $updB = $this->conn->prepare("UPDATE bookings SET status='Accepted' WHERE id=?");
            $updB->bind_param("i", $bid);
            $updB->execute();

            $upd = $this->conn->prepare("UPDATE change_requests SET status='rejected', hr_note=? WHERE id=?");
            $upd->bind_param("si", $hrNote, $requestId);
            $upd->execute();

            $this->conn->commit();
            return [
                'booking_id' => $bid,
                'client_id' => (int)$req['client_id'],
            ];
        } catch (\Throwable $e) {
            $this->conn->rollback();
            return false;
        }
    }
    public function getRequestByBooking($bookingId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM change_requests WHERE booking_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
