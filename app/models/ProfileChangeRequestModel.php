<?php
require_once APPROOT . '/core/Database.php';

class ProfileChangeRequestModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    public function countPending(): int
    {
        $res = $this->conn->query("SELECT COUNT(*) AS c FROM caretaker_profile_change_requests WHERE status = 'Pending'");
        if (!$res) {
            return 0;
        }
        $row = $res->fetch_assoc();

        return (int) ($row['c'] ?? 0);
    }

    public function hasPendingRequest(int $caretakerId): bool
    {
        $stmt = $this->conn->prepare("SELECT id FROM caretaker_profile_change_requests WHERE caretaker_id = ? AND status = 'Pending' LIMIT 1");
        $stmt->bind_param('i', $caretakerId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result && $result->num_rows > 0;
    }

    public function getLatestRequestByCaretaker(int $caretakerId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM caretaker_profile_change_requests WHERE caretaker_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param('i', $caretakerId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    public function createRequest(array $data): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO caretaker_profile_change_requests
            (caretaker_id, requested_name, requested_email, requested_phone, requested_experience, requested_location, requested_qualifications, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')"
        );

        $stmt->bind_param(
            'issssss',
            $data['caretaker_id'],
            $data['requested_name'],
            $data['requested_email'],
            $data['requested_phone'],
            $data['requested_experience'],
            $data['requested_location'],
            $data['requested_qualifications']
        );

        return $stmt->execute();
    }

    public function getRequests(string $status = 'All'): array
    {
        if ($status === 'All') {
            $sql = "SELECT r.*, c.name AS current_name, c.email AS current_email, c.phone AS current_phone
                    FROM caretaker_profile_change_requests r
                    JOIN caretakers c ON c.id = r.caretaker_id
                    ORDER BY r.created_at DESC";
            $result = $this->conn->query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        $stmt = $this->conn->prepare(
            "SELECT r.*, c.name AS current_name, c.email AS current_email, c.phone AS current_phone
             FROM caretaker_profile_change_requests r
             JOIN caretakers c ON c.id = r.caretaker_id
             WHERE r.status = ?
             ORDER BY r.created_at DESC"
        );
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById(int $requestId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM caretaker_profile_change_requests WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $requestId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    public function approveRequest(int $requestId, int $adminId, string $adminNote = ''): bool
    {
        $this->conn->begin_transaction();

        try {
            $request = $this->getById($requestId);
            if (!$request || $request['status'] !== 'Pending') {
                $this->conn->rollback();
                return false;
            }

            $updateCaretaker = $this->conn->prepare(
                "UPDATE caretakers
                 SET name = ?, email = ?, phone = ?, experience = ?, location = ?, qualifications = ?
                 WHERE id = ?"
            );
            $updateCaretaker->bind_param(
                'ssssssi',
                $request['requested_name'],
                $request['requested_email'],
                $request['requested_phone'],
                $request['requested_experience'],
                $request['requested_location'],
                $request['requested_qualifications'],
                $request['caretaker_id']
            );
            $updateCaretaker->execute();

            $updateRequest = $this->conn->prepare(
                "UPDATE caretaker_profile_change_requests
                 SET status = 'Approved', admin_note = ?, reviewed_by = ?, reviewed_at = NOW()
                 WHERE id = ?"
            );
            $updateRequest->bind_param('sii', $adminNote, $adminId, $requestId);
            $updateRequest->execute();

            $this->conn->commit();
            return true;
        } catch (\Throwable $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function rejectRequest(int $requestId, int $adminId, string $adminNote = ''): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE caretaker_profile_change_requests
             SET status = 'Rejected', admin_note = ?, reviewed_by = ?, reviewed_at = NOW()
             WHERE id = ? AND status = 'Pending'"
        );
        $stmt->bind_param('sii', $adminNote, $adminId, $requestId);
        return $stmt->execute();
    }
}
