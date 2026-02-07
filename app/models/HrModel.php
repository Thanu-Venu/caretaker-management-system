<?php
require_once APPROOT . '/core/Database.php';

class HrModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

  public function getAllBookings()
{
    $sql = "SELECT 
                b.id AS booking_id,
                b.client_id,                   
                b.caretaker_id,                
                b.booking_date,
                b.preferred_time,
                b.customization,
                b.customization_hours,
                b.customization_price,
                b.status,
                b.service_type,
                c.name AS client_name,
                ct.name AS caretaker_name
            FROM bookings b
            JOIN clients c ON b.client_id = c.id
            JOIN caretakers ct ON b.caretaker_id = ct.id
            ORDER BY b.created_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

public function requestAdvancePayment($booking_id) {
    $stmt = $this->conn->prepare("
        UPDATE bookings
        SET status = 'Payment_Requested'
        WHERE id = ?
    ");
    
    if (!$stmt) {
        error_log("Prepare failed: " . $this->conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $booking_id);
    $result = $stmt->execute();
    
    if (!$result) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    // Check if any rows were actually updated
    $affected_rows = $this->conn->affected_rows;
    error_log("Booking ID: $booking_id, Affected rows: $affected_rows");
    
    $stmt->close();
    return $result;
}

public function getRequestedBookings() {

    $sql = "
        SELECT 
            b.id AS booking_id,
            b.client_id,
            b.caretaker_id,
            b.service_type,
            b.booking_date,
            b.preferred_time,
            b.customization,
            b.customization_hours,
            b.customization_price,
            b.status,
            c.name AS client_name,
            t.name AS caretaker_name
        FROM bookings b
        JOIN users c ON b.client_id = c.id
        JOIN users t ON b.caretaker_id = t.id
        WHERE b.status IN ('Requested', 'Payment_Requested')
        ORDER BY b.created_at DESC
    ";

    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}


public function updateBookingStatus($booking_id, $status) {
        $stmt = $this->conn->prepare("
            UPDATE bookings
            SET status = ?
            WHERE id = ?
        ");
        $stmt->bind_param("si", $status, $booking_id);
        $stmt->execute();
        $stmt->close();
    }

public function sendNotification($data)
{
    $sql = "INSERT INTO c_notifications (message, role)
            VALUES (?, ?)";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ss", $data['message'], $data['role']);
    return $stmt->execute();
}






}
