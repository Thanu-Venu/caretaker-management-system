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
   public function getClientById($id)
{
    $stmt = $this->conn->prepare(
        "SELECT id, name, email, phone, profile_image, created_at 
         FROM clients 
         WHERE id=?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


public function updateClient($id, $data)
{
    $stmt = $this->conn->prepare(
        "UPDATE clients 
         SET name=?, email=?, phone=?, profile_image=? 
         WHERE id=?"
    );

    $stmt->bind_param(
        "ssssi",
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['profile_image'],
        $id
    );

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
public function getUpcomingBookings($clientId)
{
     $updateSql = "
        UPDATE bookings
        SET status = 'Completed'
        WHERE booking_date < CURDATE()
          AND status IN ('Pending','Accepted')
    ";
    $this->conn->query($updateSql);

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
            WHERE b.client_id = ?
              AND b.status IN ('Pending', 'Accepted')
              AND b.booking_date >= CURDATE()
            ORDER BY b.booking_date ASC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


// app/models/ClientModel.php
public function cancelBooking($booking_id, $reason)
{
    $status = "Cancelled";
    $cancelled_at = date('Y-m-d H:i:s');

    $sql = "UPDATE bookings 
            SET status = ?, 
                cancellation_reason = ?, 
                cancelled_at = ? 
            WHERE id = ?";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("sssi", $status, $reason, $cancelled_at, $booking_id);

    return $stmt->execute();
}


public function getBookingsByStatus($status)
{
    $stmt = $this->conn->prepare("SELECT b.id as booking_id, b.booking_date, b.preferred_time, b.duration, b.basis, b.service_type, c.name as caretaker_name
                                  FROM bookings b
                                  JOIN caretakers c ON b.caretaker_id = c.id
                                  WHERE b.status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


// Get all cancelled bookings for a client
public function getCancelledBookings($clientId)
{
    $sql = "SELECT 
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                b.cancellation_reason,
                b.cancelled_at,
                c.name AS caretaker_name
            FROM bookings b
            JOIN caretakers c ON b.caretaker_id = c.id
            WHERE b.client_id = ?
              AND b.cancellation_reason IS NOT NULL
              AND b.cancellation_reason != ''
            ORDER BY b.cancelled_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param('i', $clientId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}




  public function rescheduleBooking($bookingId, $newDate, $newTime, $newDuration)
{
    $sql = "UPDATE bookings
            SET booking_date = ?,
                preferred_time = ?,
                duration = ?
            WHERE id = ?";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ssii", $newDate, $newTime, $newDuration, $bookingId);
    return $stmt->execute();
}



   
    /* ================= MARK AS PAID ================= */
    public function markAsPaid($bookingId)
    {
        $this->conn->query("
            UPDATE bookings
            SET status = 'Paid'
            WHERE booking_id = :booking_id
        ");

        $this->conn->bind(':booking_id', $bookingId);
        return $this->conn->execute();
    }



public function getPastBookings($clientId)
{
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
            WHERE b.client_id = ?
              AND b.status = 'Completed'
            ORDER BY b.booking_date DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


public function addFeedback($data) {
        $sql = "INSERT INTO feedbacks 
                (booking_id, client_id, caretaker_id, rating, feedback)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iiiis",
            $data['booking_id'],
            $data['client_id'],
            $data['caretaker_id'],
            $data['rating'],
            $data['feedback']
        );

        return $stmt->execute();
    }

    public function feedbackExists($bookingId) {
        $stmt = $this->conn->prepare(
            "SELECT id FROM feedbacks WHERE booking_id = ?"
        );
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function getCaretakerIdByBooking($bookingId)
{
    $sql = "SELECT caretaker_id FROM bookings WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return $result ? $result['caretaker_id'] : null;
}

public function getPastBookingsWithFeedback($clientId)
{
    $sql = "SELECT 
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.duration,
                b.basis,
                b.status,
                c.name AS caretaker_name,
                c.service_type,
                f.rating,
                f.feedback
            FROM bookings b
            JOIN caretakers c ON b.caretaker_id = c.id
            LEFT JOIN feedbacks f ON b.id = f.booking_id
            WHERE b.client_id = ?
              AND b.status = 'Completed'
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