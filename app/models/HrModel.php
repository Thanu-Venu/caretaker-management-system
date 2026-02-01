<?php
require_once APPROOT . '/core/Database.php';

class HrModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

   public function getAllBookings() {
    $sql = "
        SELECT 
            b.id AS booking_id,
            b.booking_date,
            b.preferred_time,
            b.status,
            c.name AS client_name,
            ct.name AS caretaker_name,
            b.service_type,
            b.total_payment,
            b.customization
        FROM bookings b
        JOIN clients c ON b.client_id = c.id
        JOIN caretakers ct ON b.caretaker_id = ct.id
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



   public function updateBookingStatus($bookingId, $status) {
    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("si", $status, $bookingId);
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
            b.id AS booking_id,
            b.booking_date,
            b.preferred_time,
            b.status,
            b.customization,
            b.total_payment,
            b.service_type,
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

    if ($status && $status !== 'All') {
        $stmt->bind_param("sii", $status, $limit, $offset);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

}
