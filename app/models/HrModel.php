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
            b.service_type
        FROM bookings b
        JOIN clients c ON b.client_id = c.id
        JOIN caretakers ct ON b.caretaker_id = ct.id
        ORDER BY b.created_at DESC
    ";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}


   public function updateBookingStatus($bookingId, $status) {
    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("si", $status, $bookingId);
    return $stmt->execute();
}





}
