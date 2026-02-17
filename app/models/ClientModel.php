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
    (client_id, caretaker_id, service_type, basis, duration, preferred_time, booking_date, district, street, address_line1, address_line2, postal_code, customization, customization_hours, customization_price, total_payment, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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

    if ($stmt->execute()) {
        return $this->conn->insert_id; // ✅ RETURN BOOKING ID
    }

    return false;
}



public function getBookingById($bookingId) {
    $sql = "SELECT 
                b.id AS booking_id,
                b.caretaker_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                b.total_payment,
                b.created_at,
                b.customization,
                b.customization_hours,
                b.customization_price,
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
    $fixStatusSql = "
       UPDATE bookings
       SET status = 'Requested'
       WHERE status IS NULL OR status = ''
    ";
    $this->conn->query($fixStatusSql);

    $updateSql = "
        UPDATE bookings
        SET status = 'Completed'
        WHERE booking_date < CURDATE()
                    AND status IN ('Requested','Payment_Requested','Advance_Paid','Accepted')
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
                            AND b.status IN ('Requested','Payment_Requested','Advance_Paid','Accepted')
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
   



public function savePayment($paymentData) {
    // Prevent duplicate advance payments for the same booking
    $bookingId = $paymentData['booking_id'] ?? null;
    $paymentType = $paymentData['payment_type'] ?? 'advance';
    if ($bookingId && $paymentType === 'advance') {
        $check = $this->conn->prepare(
            "SELECT id FROM payments WHERE booking_id = ? AND payment_type = ? AND status IN ('pending','approved') ORDER BY created_at DESC LIMIT 1"
        );
        $check->bind_param("is", $bookingId, $paymentType);
        $check->execute();
        $existing = $check->get_result()->fetch_assoc();
        $check->close();
        if ($existing && !empty($existing['id'])) {
            return (int) $existing['id'];
        }
    }

    $stmt = $this->conn->prepare(
        "INSERT INTO payments (booking_id, client_id, caretaker_id, total_booking_amount, customization_price, amount, remaining_balance, payment_method, payment_type, status, due_date) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    $status = 'pending';
    $paymentType = $paymentData['payment_type'] ?? 'advance';
    $dueDate = $paymentData['due_date'] ?? null;
    $remainingBalance = ($paymentData['total_booking_amount'] ?? 0) - ($paymentData['amount'] ?? 0);
    $customizationPrice = $paymentData['customization_price'] ?? 0;
    
    $stmt->bind_param(
        "iiidddsssss",
        $paymentData['booking_id'],
        $paymentData['client_id'],
        $paymentData['caretaker_id'],
        $paymentData['total_booking_amount'],
        $customizationPrice,
        $paymentData['amount'],
        $remainingBalance,
        $paymentData['payment_method'],
        $paymentType,
        $status,
        $dueDate
    );
    
    if ($stmt->execute()) {
        return $this->conn->insert_id;
    }
    return false;
}

// Get payments by booking
public function getPaymentsByBooking($bookingId) {
    $sql = "SELECT * FROM payments WHERE booking_id = ? ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get payments by client (for payment history)
public function getPaymentsByClient($clientId) {
    $sql = "SELECT 
                p.id,
                p.booking_id,
                p.client_id,
                p.caretaker_id,
                p.amount,
                p.payment_method,
                p.payment_type,
                p.status,
                p.created_at,
                b.service_type,
                b.duration,
                b.basis,
                b.total_payment,
                b.booking_date,
                ct.name AS caretaker_name
            FROM payments p
            JOIN bookings b ON p.booking_id = b.id
            JOIN caretakers ct ON p.caretaker_id = ct.id
            WHERE p.client_id = ?
            ORDER BY p.created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Update payment status


// Get payments by status
public function getPaymentsByStatus($status) {
    $sql = "SELECT p.*, b.total_payment, b.basis, c.name as client_name, ct.name as caretaker_name 
            FROM payments p
            JOIN bookings b ON p.booking_id = b.id
            JOIN clients c ON p.client_id = c.id
            JOIN caretakers ct ON p.caretaker_id = ct.id
            WHERE p.status = ?
            ORDER BY p.created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Update booking status


// Get all pending payments with details

// Get all payments with details (pending, approved, rejected)
public function getPendingPayments() {
    $sql = "SELECT 
                p.id,
                p.booking_id,
                p.client_id,
                p.caretaker_id,
                p.amount,
                p.total_booking_amount,
                p.remaining_balance,
                p.payment_method,
                p.payment_type,
                p.status,
                p.created_at,
                p.approved_at,
                c.name AS client_name,
                c.phone AS client_phone,
                ct.name AS caretaker_name,
                b.service_type,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.total_payment
            FROM payments p
            JOIN clients c ON p.client_id = c.id
            JOIN caretakers ct ON p.caretaker_id = ct.id
            JOIN bookings b ON p.booking_id = b.id
            ORDER BY CASE 
                WHEN p.status = 'pending' THEN 1
                WHEN p.status = 'approved' THEN 2
                WHEN p.status = 'rejected' THEN 3
            END, p.created_at DESC";
    
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get payment by ID
public function getPaymentById($paymentId) {
    $sql = "SELECT 
                p.*,
                c.name AS client_name,
                c.phone AS client_phone,
                c.email AS client_email,
                ct.name AS caretaker_name,
                b.service_type,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration
            FROM payments p
            JOIN clients c ON p.client_id = c.id
            JOIN caretakers ct ON p.caretaker_id = ct.id
            JOIN bookings b ON p.booking_id = b.id
            WHERE p.id = ?";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $paymentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Update payment status
public function updatePaymentStatus($paymentId, $status) {
    $sql = "UPDATE payments SET status = :status, approved_at = NOW() WHERE id = :id";
    $stmt = $this->conn->prepare("UPDATE payments SET status = ?, approved_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $status, $paymentId);
    return $stmt->execute();
}

// Update booking status
public function updateBookingStatus($bookingId, $status) {
    $stmt = $this->conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $bookingId);
    return $stmt->execute();
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






    
    

    public function getActiveBookingsCount($clientId)
{
        $sql = "SELECT COUNT(*) AS total
                        FROM bookings
                        WHERE client_id = ?
                            AND status IN ('Requested','Payment_Requested','Advance_Paid','Accepted','Pending')";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc()['total'];
}


public function getAssignedCaretakersCount($clientId)
{
        $sql = "SELECT COUNT(DISTINCT caretaker_id) AS total
                        FROM bookings
                        WHERE client_id = ?
                            AND status IN ('Requested','Payment_Requested','Advance_Paid','Accepted','Pending','Completed','Paid')";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc()['total'];
}

public function getTotalSpent($clientId)
{
    $sql = "SELECT COALESCE(SUM(p.amount),0) AS total
            FROM payments p
            WHERE p.client_id = ?
              AND p.status = 'approved'";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc()['total'];
}


public function getRecentBookings($clientId)
{
    $sql = "SELECT 
                b.caretaker_id,
                b.booking_date,
                b.preferred_time,
                b.duration,
                b.status,
                b.service_type,
                c.name AS caretaker_name
            FROM bookings b
            JOIN caretakers c ON b.caretaker_id = c.id
            WHERE b.client_id = ?
            ORDER BY b.created_at DESC
            LIMIT 3";

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

    // Get HR notifications (unread only)
public function getHRNotifications($limit = 10) {
    $sql = "SELECT 
                n.id,
                n.user_id,
                n.user_type,
                n.message,
                n.is_read,
                n.created_at
            FROM notifications n
            WHERE n.user_id = 1
              AND n.user_type = 'hr'
            ORDER BY n.is_read ASC, n.created_at DESC
            LIMIT ?";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get unread HR notification count
public function getUnreadHRNotificationCount() {
    $sql = "SELECT COUNT(*) AS count FROM notifications 
            WHERE user_id = 1 AND user_type = 'hr' AND is_read = 0";
    
    $result = $this->conn->query($sql);
    return $result->fetch_assoc()['count'];
}

// Mark notification as read
public function markNotificationAsRead($notificationId) {
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $notificationId);
    return $stmt->execute();
}

// Mark all HR notifications as read
public function markAllHRNotificationsAsRead() {
    $sql = "UPDATE notifications SET is_read = 1 
            WHERE user_id = 1 AND user_type = 'hr' AND is_read = 0";
    return $this->conn->query($sql);
}

// Update saveNotification to include is_read field
public function saveNotification($notificationData) {
    $stmt = $this->conn->prepare(
        "INSERT INTO notifications (user_id, user_type, message, type, is_read, created_at) 
         VALUES (?, ?, ?, ?, 0, NOW())"
    );
    
    $type = $notificationData['type'] ?? 'general';
    $stmt->bind_param(
        "isss",
        $notificationData['user_id'],
        $notificationData['user_type'],
        $notificationData['message'],
        $type
    );
    
    return $stmt->execute();
}

public function getClientNotifications($clientId)
{
    $sql = "SELECT message, created_at
            FROM c_notifications
            WHERE role = 'Client'
            ORDER BY created_at DESC
            LIMIT 3";

    return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

public function getAverageRatingGiven($clientId)
{
    $sql = "SELECT ROUND(AVG(rating),1) AS avg_rating
            FROM feedbacks
            WHERE client_id = ?";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc()['avg_rating'];
}


// Count total clients for admin dashboard


    
}
?>