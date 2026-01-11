<?php
require_once APPROOT . '/core/Database.php';

class ClientModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /**
     * Get all registered clients
     * @return array
     */
    public function getAllClients() {
        $result = $this->conn->query(
            "SELECT id, name, email, phone, created_at 
             FROM clients 
             ORDER BY created_at DESC"
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get client by ID
     */
    public function getClientById($id) {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, phone, created_at 
             FROM clients 
             WHERE id=?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

 public function updateClient($id, $data) {
        $stmt = $this->conn->prepare("UPDATE clients SET name=?,email=?,phone=? WHERE id=?");
        $stmt->bind_param("sssi", $data['name'],$data['email'],$data['phone'],$id);
        return $stmt->execute();
    }

    public function updateClientPassword($id, $hashedPassword) {
        $stmt = $this->conn->prepare("UPDATE clients SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }

    /**
     * Delete client
     */
    public function deleteClient($id) {
        $stmt = $this->conn->prepare("DELETE FROM clients WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Search clients
     */
    public function searchClients($keyword) {
        $search = "%" . $keyword . "%";

        $stmt = $this->conn->prepare(
            "SELECT id, name, email, phone, created_at 
             FROM clients 
             WHERE name LIKE ? OR email LIKE ?
             ORDER BY created_at DESC"
        );

        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function login($email, $password) {
    $stmt = $this->conn->prepare("SELECT * FROM clients WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $client = $stmt->get_result()->fetch_assoc();
    if ($client && password_verify($password, $client['password'])) {
        return $client;
    }
    return false;
}

// Get caretaker by ID
 // 1️⃣ Get caretaker details (with selected fields and default rating)
    public function getCaretakerById($id)
    {
        $sql = "SELECT 
                    id,
                    name,
                    service_type,
                    location,
                    IFNULL(rating, 'N/A') AS rating
                FROM caretakers
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // 2️⃣ Create booking
    public function createBooking($data)
{
    $sql = "INSERT INTO bookings 
    (client_id, caretaker_id, service_type, basis, duration, preferred_time, booking_date, service_location, customization, total_payment, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->conn->prepare($sql);

    $stmt->bind_param(
        "iississssds",
        $data['client_id'],
        $data['caretaker_id'],
        $data['service_type'],
        $data['basis'],
        $data['duration'],
        $data['preferred_time'],
        $data['booking_date'],
        $data['service_location'],
        $data['customization'],
        $data['total_payment'],
        $data['status']
    );

    if ($stmt->execute()) {
        return $this->conn->insert_id; // ✅ RETURN BOOKING ID
    }

    return false;
}



public function getBookingById($bookingId) {
    $sql = "SELECT 
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                b.total_payment,
                b.status,
                c.name AS caretaker_name
            FROM bookings b
            JOIN caretakers c ON b.caretaker_id = c.id
            WHERE b.id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


// Fetch Upcoming Bookings
public function getUpcomingBookings($clientId) {
    $sql = "SELECT 
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.duration,
                b.basis,
                b.service_type,
                b.status,
                c.name AS caretaker_name
            FROM bookings b
            JOIN caretakers c ON b.caretaker_id = c.id
            WHERE b.client_id = ? AND b.status != 'Cancelled'
            ORDER BY b.booking_date ASC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


public function getPastBookings($clientId) {
    $sql = "SELECT 
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.duration,
                b.basis,
                b.service_type,
                b.status,
                c.name AS caretaker_name
            FROM bookings b
            JOIN caretakers c ON b.caretaker_id = c.id
            WHERE b.client_id = ? AND (b.status = 'Completed' OR b.status = 'Cancelled')
            ORDER BY b.booking_date DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

public function getCancelledBookingsByClient($clientId) {
    $sql = "SELECT b.id, b.service_type, b.basis, b.duration, b.preferred_time, 
                   b.booking_date, b.service_location, b.customization, b.status,
                   b.cancellation_reason,
                   c.name AS caretaker_name
            FROM bookings b
            JOIN caretakers c ON b.caretaker_id = c.id
            WHERE b.client_id = ? AND b.status = 'Cancelled'
            ORDER BY b.booking_date DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}




    // 3️⃣ Send notification to HR
    public function sendNotificationToHR($data) {
        $sql = "INSERT INTO c_notifications (message, role) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $data['message'], $data['role']);
        return $stmt->execute();
    }
    







    
}
?>