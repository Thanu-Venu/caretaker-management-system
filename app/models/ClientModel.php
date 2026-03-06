<?php
require_once APPROOT . '/core/Database.php';

class ClientModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /* ===================== CLIENT CRUD ===================== */

    public function getAllClients()
    {
        $result = $this->conn->query(
            "SELECT id, name, email, phone, created_at
             FROM clients
             ORDER BY created_at DESC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

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

    // Used by CaretakerController AJAX sometimes
    public function getClientDetails($id)
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

        $name  = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $img   = $data['profile_image'] ?? 'default.png';

        $stmt->bind_param("ssssi", $name, $email, $phone, $img, $id);
        return $stmt->execute();
    }

    public function updateClientPassword($id, $hashedPassword)
    {
        $stmt = $this->conn->prepare("UPDATE clients SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }

    public function deleteClient($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM clients WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function searchClients($keyword)
    {
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

    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM clients WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $client = $stmt->get_result()->fetch_assoc();

        if ($client && password_verify($password, $client['password'])) {
            return $client;
        }
        return false;
    }

    public function countClients()
    {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM clients");
        return $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function getAllClient()
    {
        // If your controllers call getAllClient(), keep it as alias
        return $this->getAllClients();
    }

    /* ===================== CARETAKER HELPERS ===================== */

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

    /* ===================== BOOKINGS ===================== */

    public function createBooking($data)
    {
        $sql = "INSERT INTO bookings
                (client_id, caretaker_id, service_type, basis, duration, preferred_time, booking_date,
                 district, street, address_line1, address_line2, postal_code,
                 customization, customization_hours, customization_price, total_payment, status)
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
            return $this->conn->insert_id;
        }
        return false;
    }

    public function getBookingById($bookingId)
    {
        $sql = "SELECT
                    b.id AS booking_id,
                    b.client_id,
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
                    b.district,
                    b.street,
                    b.address_line1,
                    b.address_line2,
                    b.postal_code,
                    b.cancellation_reason,
                    b.cancelled_at,
                    c.name AS caretaker_name
                FROM bookings b
                JOIN caretakers c ON b.caretaker_id = c.id
                WHERE b.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUpcomingBookings($clientId)
    {
        // Fix NULL/empty status
        $this->conn->query("
            UPDATE bookings
            SET status = 'Requested'
            WHERE status IS NULL OR status = ''
        ");

        // Send reminder for bookings happening tomorrow that still need payment/acceptance
        $reminderCheckStmt = $this->conn->prepare("
            SELECT b.id, b.booking_date
            FROM bookings b
            WHERE b.client_id = ?
              AND b.booking_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
              AND b.status IN ('Requested','Payment_Requested')
        ");
        $reminderCheckStmt->bind_param("i", $clientId);
        $reminderCheckStmt->execute();
        $reminderRows = $reminderCheckStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $notifExistsStmt = $this->conn->prepare("
            SELECT id
            FROM notifications
            WHERE user_id = ?
              AND user_role = 'client'
              AND title = 'Booking Payment Reminder'
              AND link = ?
              AND DATE(created_at) = CURDATE()
            LIMIT 1
        ");

        $insertNotifStmt = $this->conn->prepare("
            INSERT INTO notifications (user_id, user_role, title, message, link, is_read, created_at)
            VALUES (?, 'client', ?, ?, ?, 0, NOW())
        ");

        foreach ($reminderRows as $row) {
            $bookingId = (int)$row['id'];
            $link = URLROOT . "/client/c_payment?booking_id=" . $bookingId;

            $notifExistsStmt->bind_param("is", $clientId, $link);
            $notifExistsStmt->execute();
            $alreadyExists = $notifExistsStmt->get_result()->fetch_assoc();

            if (!$alreadyExists) {
                $title = "Booking Payment Reminder";
                $message = "Your booking #{$bookingId} is scheduled for tomorrow ({$row['booking_date']}). Please complete advance payment and confirmation before the service date.";
                $insertNotifStmt->bind_param("isss", $clientId, $title, $message, $link);
                $insertNotifStmt->execute();
            }
        }

        // Auto-cancel overdue bookings if payment/acceptance was not completed
        $this->conn->query("
            UPDATE bookings
            SET status = 'Cancelled',
                cancellation_reason = 'Auto-cancelled: advance payment/acceptance was not completed before service date.',
                cancelled_at = NOW()
            WHERE booking_date < CURDATE()
              AND status IN ('Requested','Payment_Requested')
        ");

        $sql = "SELECT
                    b.id AS booking_id,
                    b.booking_date,
                    b.preferred_time,
                    b.duration,
                    b.basis,
                    b.service_type,
                    b.status,
                    b.customization,
                    c.name AS caretaker_name
                FROM bookings b
                JOIN caretakers c ON b.caretaker_id = c.id
                WHERE b.client_id = ?
                  AND b.status IN ('Requested','Payment_Requested','Advance_Paid')
                  AND b.booking_date > CURDATE()
                ORDER BY b.booking_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                    b.customization,
                    c.name AS caretaker_name
                FROM bookings b
                JOIN caretakers c ON b.caretaker_id = c.id
                WHERE b.client_id = ?
                  AND b.status IN ('Completed')
                ORDER BY b.booking_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get bookings that are currently active for today.
     * Ongoing means status is Accepted/Advance_Paid/change requested/reschedule requested and today's date is within
     * the booking period from booking_date up to the calculated end date.
     */
    public function getOngoingBookings($clientId)
    {
        // Auto-complete bookings that have passed their end date
        $this->conn->query("
            UPDATE bookings
            SET status = 'Completed'
            WHERE client_id = " . (int)$clientId . "
              AND status IN ('Accepted', 'Advance_Paid', 'Change_Requested', 'Reschedule_Requested')
              AND CURDATE() > (
                    CASE
                        WHEN basis = 'Hourly' THEN booking_date
                        WHEN basis = 'Daily' THEN DATE_ADD(booking_date, INTERVAL (GREATEST(duration, 1) - 1) DAY)
                        WHEN basis = 'Monthly' THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL GREATEST(duration, 1) MONTH), INTERVAL 1 DAY)
                        WHEN basis = 'Yearly' THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL GREATEST(duration, 1) YEAR), INTERVAL 1 DAY)
                        ELSE booking_date
                    END
              )
        ");

        $sql = "SELECT
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.duration,
                b.basis,
                b.service_type,
                b.status,
                b.customization,
                b.caretaker_changed_once,
                ct.name AS caretaker_name
            FROM bookings b
            JOIN caretakers ct ON b.caretaker_id = ct.id
            WHERE b.client_id = ?
                            AND b.status IN ('Accepted','Advance_Paid','Reschedule_Requested','Change_Requested')
                            AND CURDATE() BETWEEN b.booking_date AND (
                                        CASE
                                                WHEN b.basis = 'Hourly' THEN b.booking_date
                                                WHEN b.basis = 'Daily' THEN DATE_ADD(b.booking_date, INTERVAL (GREATEST(b.duration, 1) - 1) DAY)
                                                WHEN b.basis = 'Monthly' THEN DATE_SUB(DATE_ADD(b.booking_date, INTERVAL GREATEST(b.duration, 1) MONTH), INTERVAL 1 DAY)
                                                WHEN b.basis = 'Yearly' THEN DATE_SUB(DATE_ADD(b.booking_date, INTERVAL GREATEST(b.duration, 1) YEAR), INTERVAL 1 DAY)
                                                ELSE b.booking_date
                                        END
                            )
            ORDER BY b.booking_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function getPastBookingsWithFeedback($clientId)
    {
        // Auto-complete bookings that have passed their end date
        $this->conn->query("
            UPDATE bookings
            SET status = 'Completed'
            WHERE client_id = " . (int)$clientId . "
              AND status IN ('Accepted', 'Advance_Paid', 'Change_Requested', 'Reschedule_Requested')
              AND CURDATE() > (
                    CASE
                        WHEN basis = 'Hourly' THEN booking_date
                        WHEN basis = 'Daily' THEN DATE_ADD(booking_date, INTERVAL (GREATEST(duration, 1) - 1) DAY)
                        WHEN basis = 'Monthly' THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL GREATEST(duration, 1) MONTH), INTERVAL 1 DAY)
                        WHEN basis = 'Yearly' THEN DATE_SUB(DATE_ADD(booking_date, INTERVAL GREATEST(duration, 1) YEAR), INTERVAL 1 DAY)
                        ELSE booking_date
                    END
              )
        ");

        $sql = "SELECT
                    b.id AS booking_id,
                    b.caretaker_id,
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

    public function getCancelledBookings($clientId)
    {
        $sql = "SELECT
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.duration,
                b.basis,
                b.service_type,
                b.status,
                b.cancellation_reason,
                b.cancelled_at,
                ct.name AS caretaker_name
            FROM bookings b
            JOIN caretakers ct ON b.caretaker_id = ct.id
            WHERE b.client_id = ?
              AND LOWER(TRIM(b.status)) = 'cancelled'
            ORDER BY b.cancelled_at DESC, b.booking_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
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

    public function getBookingsByStatus($status)
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
                WHERE b.status = ?
                ORDER BY b.booking_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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

    /* ===================== PAYMENTS ===================== */

    public function savePayment($paymentData)
    {
        $bookingId = $paymentData['booking_id'] ?? null;
        $paymentType = $paymentData['payment_type'] ?? 'advance';

        // Prevent duplicate advance payments
        if ($bookingId && $paymentType === 'advance') {
            $check = $this->conn->prepare(
                "SELECT id
                 FROM payments
                 WHERE booking_id = ? AND payment_type = ?
                   AND status IN ('pending','approved')
                 ORDER BY created_at DESC
                 LIMIT 1"
            );
            $check->bind_param("is", $bookingId, $paymentType);
            $check->execute();
            $existing = $check->get_result()->fetch_assoc();
            $check->close();

            if ($existing && !empty($existing['id'])) {
                return (int)$existing['id'];
            }
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO payments
            (booking_id, client_id, caretaker_id, total_booking_amount, customization_price, amount, remaining_balance,
             payment_method, payment_type, status, due_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $status = 'pending';
        $dueDate = $paymentData['due_date'] ?? null;

        $totalBookingAmount = (float)($paymentData['total_booking_amount'] ?? 0);
        $amount = (float)($paymentData['amount'] ?? 0);
        $customizationPrice = (float)($paymentData['customization_price'] ?? 0);

        $remainingBalance = $totalBookingAmount - $amount;

        $stmt->bind_param(
            "iiidddsssss",
            $paymentData['booking_id'],
            $paymentData['client_id'],
            $paymentData['caretaker_id'],
            $totalBookingAmount,
            $customizationPrice,
            $amount,
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

    public function getPaymentsByBooking($bookingId)
    {
        $sql = "SELECT * FROM payments WHERE booking_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPaymentsByClient($clientId)
    {
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

    public function getPaymentsByStatus($status)
    {
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

    public function getPendingPayments()
    {
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
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getPaymentById($paymentId)
    {
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

    public function updatePaymentStatus($paymentId, $status)
    {
        $stmt = $this->conn->prepare("UPDATE payments SET status = ?, approved_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $paymentId);
        return $stmt->execute();
    }

    public function updateBookingStatus($bookingId, $status)
    {
        $stmt = $this->conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $bookingId);
        return $stmt->execute();
    }

    /* ===================== FEEDBACK ===================== */

    public function addFeedback($data)
    {
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

    public function feedbackExists($bookingId)
    {
        $stmt = $this->conn->prepare("SELECT id FROM feedbacks WHERE booking_id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function getAverageRatingGiven($clientId)
    {
        $sql = "SELECT ROUND(AVG(rating),1) AS avg_rating
                FROM feedbacks
                WHERE client_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['avg_rating'] ?? null;
    }

    /* ===================== NOTIFICATIONS ===================== */

    public function saveNotification($notificationData)
    {
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

    public function getHRNotifications($limit = 10)
    {
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

    public function getUnreadHRNotificationCount()
    {
        $sql = "SELECT COUNT(*) AS count
                FROM notifications
                WHERE user_id = 1 AND user_type = 'hr' AND is_read = 0";

        $result = $this->conn->query($sql);
        $row = $result ? $result->fetch_assoc() : ['count' => 0];
        return $row['count'] ?? 0;
    }

    public function markNotificationAsRead($notificationId)
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $notificationId);
        return $stmt->execute();
    }

    public function markAllHRNotificationsAsRead()
    {
        $sql = "UPDATE notifications
                SET is_read = 1
                WHERE user_id = 1 AND user_type = 'hr' AND is_read = 0";
        return $this->conn->query($sql);
    }

    public function getAllClientNotifications($clientId)
    {
        $sql = "SELECT
                    n.id,
                    n.user_id,
                    n.user_role,
                    n.message,
                    n.is_read,
                    n.created_at
                FROM notifications n
                WHERE n.user_id = ? AND n.user_role = 'client'
                ORDER BY n.is_read ASC, n.created_at DESC
                LIMIT 5";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    public function getBookingSummaryForNotification($bookingId)
    {
        $sql = "SELECT b.id AS booking_id, b.service_type, b.booking_date, b.preferred_time,
                   b.basis, b.duration, b.district, b.street, b.total_payment,
                   cl.name AS client_name, cl.email AS client_email,
                   ct.name AS caretaker_name
            FROM bookings b
            JOIN clients cl ON cl.id = b.client_id
            LEFT JOIN caretakers ct ON ct.id = b.caretaker_id
            WHERE b.id = ?
            LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row;
    }
    /* ===================== ADMIN REPORTS / STATS ===================== */

    public function getAllBookingsAdmin()
    {
        $sql = "SELECT
                    b.id AS booking_id,
                    cl.name AS client_name,
                    ct.name AS caretaker_name,
                    b.service_type,
                    b.booking_date,
                    b.status,
                    b.customization
                FROM bookings b
                JOIN clients cl ON b.client_id = cl.id
                JOIN caretakers ct ON b.caretaker_id = ct.id
                ORDER BY b.id ASC";

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countUpcomingBookings()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM bookings WHERE booking_date >= CURDATE()");
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getBookingsLast4Weeks()
    {
        $stmt = $this->conn->prepare("
            SELECT YEARWEEK(booking_date, 1) as yw, COUNT(*) as total
            FROM bookings
            WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)
            GROUP BY yw
            ORDER BY yw ASC
        ");
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $labels = [];
        $values = [];
        foreach ($rows as $r) {
            $labels[] = "Week " . $r['yw'];
            $values[] = (int)$r['total'];
        }
        return ['labels' => $labels, 'values' => $values];
    }

    public function getClientEngagementLast6Months()
    {
        $stmt = $this->conn->prepare("
            SELECT
                DATE_FORMAT(booking_date, '%Y-%m') AS ym,
                DATE_FORMAT(MIN(booking_date), '%b') AS mon,
                COUNT(*) AS total
            FROM bookings
            WHERE booking_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY ym
            ORDER BY ym ASC
        ");
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $labels = [];
        $values = [];
        foreach ($rows as $r) {
            $labels[] = $r['mon'];
            $values[] = (int)$r['total'];
        }
        return ['labels' => $labels, 'values' => $values];
    }

    /* ===================== CLIENT DASHBOARD STATS ===================== */

    public function getActiveBookingsCount($clientId)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM bookings
                WHERE client_id = ?
                  AND status IN ('Requested','Payment_Requested','Advance_Paid','Accepted','Pending','Change_Requested')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'] ?? 0;
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
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'] ?? 0;
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
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'] ?? 0;
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
    /* ===================== ADMIN: PAGINATED BOOKINGS ===================== */

    public function getBookingsPaginated($limit, $offset, $search = '', $status = '')
    {
        $limit  = (int)$limit;
        $offset = (int)$offset;

        $sql = "SELECT
                b.id AS booking_id,
                cl.name AS client_name,
                cl.phone AS client_phone,
                ct.name AS caretaker_name,
                b.service_type,
                b.basis,
                b.duration,
                b.preferred_time,
                b.booking_date,
                b.total_payment,
                b.status,
                b.created_at
            FROM bookings b
            JOIN clients cl ON b.client_id = cl.id
            JOIN caretakers ct ON b.caretaker_id = ct.id
            WHERE 1=1";

        $types = "";
        $params = [];

        // Search (client name OR caretaker name OR booking id)
        if (!empty($search)) {
            $sql .= " AND (cl.name LIKE ? OR ct.name LIKE ? OR b.id LIKE ?)";
            $types .= "sss";
            $like = "%" . $search . "%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        // Status filter
        if (!empty($status) && $status !== 'All') {
            $sql .= " AND b.status = ?";
            $types .= "s";
            $params[] = $status;
        }

        $sql .= " ORDER BY b.created_at DESC LIMIT ? OFFSET ?";
        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalBookings($search = '', $status = '')
    {
        $sql = "SELECT COUNT(*) AS total
            FROM bookings b
            JOIN clients cl ON b.client_id = cl.id
            JOIN caretakers ct ON b.caretaker_id = ct.id
            WHERE 1=1";

        $types = "";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (cl.name LIKE ? OR ct.name LIKE ? OR b.id LIKE ?)";
            $types .= "sss";
            $like = "%" . $search . "%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if (!empty($status) && $status !== 'All') {
            $sql .= " AND b.status = ?";
            $types .= "s";
            $params[] = $status;
        }

        $stmt = $this->conn->prepare($sql);

        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    public function getAssignedCaretaker($client_id)
    {
        $sql = "SELECT
                b.id AS booking_id,
                b.status,
                b.booking_date,
                b.service_type,
                ct.id AS caretaker_id,
                ct.name AS caretaker_name,
                ct.phone AS caretaker_phone,
                ct.email AS caretaker_email,
                ct.service_type AS caretaker_service,
                ct.location AS caretaker_location
            FROM bookings b
            JOIN caretakers ct ON ct.id = b.caretaker_id
            WHERE b.client_id = ?
              AND b.status IN ('Accepted','Advance_Paid','Payment_Requested')
            ORDER BY b.created_at DESC
            LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null; // or throw exception / log $this->conn->error
        }

        $stmt->bind_param("i", $client_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;

        $stmt->close();
        return $row;
    }


    public function cancelBooking($bookingId, $reason)
    {
        $sql = "UPDATE bookings
            SET status = 'cancelled',
                cancellation_reason = ?,
                cancelled_at = NOW()
            WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $reason, $bookingId);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    public function getAdvancePaymentPendingBookings($clientId)
    {
        $sql = "SELECT id AS booking_id,booking_date, preferred_time, duration, basis, service_type
            FROM bookings
            WHERE client_id = ?
              AND status = 'Payment_Requested'
            ORDER BY booking_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getBookedCaretakersByClient($client_id)
    {
        $sql = "
        SELECT DISTINCT c.id, c.name
        FROM bookings b
        JOIN caretakers c ON b.caretaker_id = c.id
        WHERE b.client_id = ?
        AND b.status IN ('Accepted', 'Advance_Paid', 'Completed','change_requested','reschedule_requested')
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $client_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get the most recent booking ID for a client with a specific caretaker
     * Used for adding feedback when booking_id is not explicitly provided
     */
    public function getRecentBookingWithCaretaker($client_id, $caretaker_id)
    {
        $stmt = $this->conn->prepare(
            "SELECT id
             FROM bookings
             WHERE client_id = ? AND caretaker_id = ?
             AND status IN ('Completed', 'Accepted', 'Advance_Paid')
             ORDER BY created_at DESC
             LIMIT 1"
        );
        $stmt->bind_param("ii", $client_id, $caretaker_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['id'] : null;
    }
}