<?php
require_once APPROOT . '/core/Database.php';

class ClientModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /* ================= CLIENT ================= */

    public function getAllClients() {
        $result = $this->conn->query(
            "SELECT id, name, email, phone, created_at 
             FROM clients 
             ORDER BY created_at DESC"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getClientById($id) {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, phone, profile_image, created_at 
             FROM clients WHERE id=?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getClientByName($name) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM clients WHERE name=? LIMIT 1"
        );
        $stmt->bind_param("s", $name);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateClient($id, $data) {
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
        $stmt = $this->conn->prepare(
            "UPDATE clients SET password=? WHERE id=?"
        );
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }

    public function deleteClient($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM clients WHERE id=?"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function searchClients($keyword) {
        $search = "%$keyword%";
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
        $stmt = $this->conn->prepare(
            "SELECT * FROM clients WHERE email=?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $client = $stmt->get_result()->fetch_assoc();

        return ($client && password_verify($password, $client['password']))
            ? $client
            : false;
    }

    /* ================= CARETAKER (for booking) ================= */

    public function getCaretakerById($id) {
        $stmt = $this->conn->prepare(
            "SELECT id, name, service_type, location, IFNULL(rating,'N/A') AS rating
             FROM caretakers WHERE id=?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* ================= BOOKINGS ================= */

    public function createBooking($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO bookings 
            (client_id, caretaker_id, service_type, basis, duration, preferred_time,
             booking_date, service_location, customization, total_payment, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

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

        return $stmt->execute() ? $this->conn->insert_id : false;
    }

    public function getBookingById($bookingId) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, c.name AS caretaker_name
             FROM bookings b
             JOIN caretakers c ON b.caretaker_id = c.id
             WHERE b.id=?"
        );
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUpcomingBookings($clientId) {
        $this->conn->query(
            "UPDATE bookings 
             SET status='Completed'
             WHERE booking_date < CURDATE()
             AND status IN ('Pending','Accepted')"
        );

        $stmt = $this->conn->prepare(
            "SELECT b.*, c.name AS caretaker_name
             FROM bookings b
             JOIN caretakers c ON b.caretaker_id = c.id
             WHERE b.client_id=?
             AND b.booking_date >= CURDATE()
             AND b.status IN ('Pending','Accepted')
             ORDER BY b.booking_date ASC"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPastBookings($clientId) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, c.name AS caretaker_name
             FROM bookings b
             JOIN caretakers c ON b.caretaker_id = c.id
             WHERE b.client_id=? AND b.status='Completed'
             ORDER BY b.booking_date DESC"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function cancelBooking($bookingId, $reason) {
        $stmt = $this->conn->prepare(
            "UPDATE bookings 
             SET status='Cancelled',
                 cancellation_reason=?,
                 cancelled_at=NOW()
             WHERE id=?"
        );
        $stmt->bind_param("si", $reason, $bookingId);
        return $stmt->execute();
    }

    public function rescheduleBooking($bookingId, $date, $time, $duration) {
        $stmt = $this->conn->prepare(
            "UPDATE bookings
             SET booking_date=?, preferred_time=?, duration=?
             WHERE id=?"
        );
        $stmt->bind_param("ssii", $date, $time, $duration, $bookingId);
        return $stmt->execute();
    }

    /* ================= FEEDBACK ================= */

    public function addFeedback($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO feedbacks
             (booking_id, client_id, caretaker_id, rating, feedback)
             VALUES (?, ?, ?, ?, ?)"
        );
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
            "SELECT id FROM feedbacks WHERE booking_id=?"
        );
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /* ================= NOTIFICATIONS ================= */

    public function sendNotificationToHR($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO c_notifications (message, role) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $data['message'], $data['role']);
        return $stmt->execute();
    }
}
