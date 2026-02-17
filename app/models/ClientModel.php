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
        $q = $this->conn->query("SELECT id,name,email,phone,created_at FROM clients ORDER BY created_at DESC");
        return $q ? $q->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getClientById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM clients WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function login($email,$password) {
        $stmt = $this->conn->prepare("SELECT * FROM clients WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $u = $stmt->get_result()->fetch_assoc();

        return ($u && password_verify($password,$u['password'])) ? $u : false;
    }

    /* ================= BOOKINGS ================= */

    public function createBooking($data) {
        $sql = "INSERT INTO bookings 
        (client_id, caretaker_id, service_type, basis, duration, preferred_time, booking_date, district, street,
         address_line1, address_line2, postal_code, customization, customization_hours, customization_price,
         total_payment, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "iississssssssidds",
            $data['client_id'],
            $data['caretaker_id'],
            $data['service_type'],
            $data['basis'],
            $data['duration'],
            $data['preferred_time'],
            $data['booking_date'],
            $data['district'],
            $data['street'],
            $data['address_line1'],
            $data['address_line2'],
            $data['postal_code'],
            $data['customization'],
            $data['customization_hours'],
            $data['customization_price'],
            $data['total_payment'],
            $data['status']
        );

        return $stmt->execute() ? $this->conn->insert_id : false;
    }

    public function getBookingById($id) {
        $stmt = $this->conn->prepare("
            SELECT b.*, c.name AS caretaker_name
            FROM bookings b
            JOIN caretakers c ON b.caretaker_id = c.id
            WHERE b.id=?
        ");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function cancelBooking($id,$reason) {
        $status="Cancelled";
        $time=date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("
            UPDATE bookings 
            SET status=?, cancellation_reason=?, cancelled_at=? 
            WHERE id=?
        ");
        $stmt->bind_param("sssi",$status,$reason,$time,$id);
        return $stmt->execute();
    }

    public function rescheduleBooking($id,$date,$time,$duration) {
        $stmt = $this->conn->prepare("
            UPDATE bookings SET booking_date=?, preferred_time=?, duration=? WHERE id=?
        ");
        $stmt->bind_param("ssii",$date,$time,$duration,$id);
        return $stmt->execute();
    }

    /* ================= PAYMENTS ================= */

    public function savePayment($p) {

        $remaining = $p['total_booking_amount'] - $p['amount'];
        $status = "pending";

        $stmt = $this->conn->prepare("
            INSERT INTO payments
            (booking_id,client_id,caretaker_id,total_booking_amount,customization_price,
             amount,remaining_balance,payment_method,payment_type,status,due_date)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "iiidddsssss",
            $p['booking_id'],
            $p['client_id'],
            $p['caretaker_id'],
            $p['total_booking_amount'],
            $p['customization_price'],
            $p['amount'],
            $remaining,
            $p['payment_method'],
            $p['payment_type'],
            $status,
            $p['due_date']
        );

        return $stmt->execute() ? $this->conn->insert_id : false;
    }

    public function updatePaymentStatus($id,$status) {
        $stmt = $this->conn->prepare("UPDATE payments SET status=?, approved_at=NOW() WHERE id=?");
        $stmt->bind_param("si",$status,$id);
        return $stmt->execute();
    }

    /* ================= FEEDBACK ================= */

    public function addFeedback($d) {
        $stmt = $this->conn->prepare("
            INSERT INTO feedbacks (booking_id,client_id,caretaker_id,rating,feedback)
            VALUES (?,?,?,?,?)
        ");
        $stmt->bind_param(
            "iiiis",
            $d['booking_id'],
            $d['client_id'],
            $d['caretaker_id'],
            $d['rating'],
            $d['feedback']
        );
        return $stmt->execute();
    }

    public function feedbackExists($bookingId) {
        $stmt = $this->conn->prepare("SELECT id FROM feedbacks WHERE booking_id=?");
        $stmt->bind_param("i",$bookingId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
