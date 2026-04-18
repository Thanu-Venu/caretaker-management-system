<?php
require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/models/AccountModel.php';

class ClientModel
{
    private $conn;
    private $accountLinkChecked = false;
    private $accountLinkEnabled = false;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    private function hasAccountLinking(): bool
    {
        if ($this->accountLinkChecked) {
            return $this->accountLinkEnabled;
        }

        $tables = $this->conn->query("SHOW TABLES LIKE 'accounts'");
        if (!$tables || $tables->num_rows === 0) {
            $this->accountLinkChecked = true;
            $this->accountLinkEnabled = false;
            return false;
        }

        $cols = $this->conn->query("SHOW COLUMNS FROM clients LIKE 'account_id'");
        $this->accountLinkChecked = true;
        $this->accountLinkEnabled = (bool)($cols && $cols->num_rows > 0);
        return $this->accountLinkEnabled;
    }

    /* ===================== CLIENT CRUD ===================== */

    public function findUserByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT id FROM clients WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function register($data)
    {
        $sql = "INSERT INTO clients (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'client')";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param("ssss", $data['name'], $data['email'], $data['phone'], $hashedPassword);
        return $stmt->execute();
    }

    public function getAllClients()
    {
        $result = $this->conn->query(
            "SELECT id, name, email, phone, created_at
             FROM clients
             ORDER BY created_at DESC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Admin client list with optional search and registration date range.
     *
     * @param array{q?:string,date_from?:string,date_to?:string} $filters
     * @return array<int, array<string, mixed>>
     */
    public function getAllClientsFiltered(array $filters = []): array
    {
        $q = trim((string)($filters['q'] ?? ''));
        $from = trim((string)($filters['date_from'] ?? ''));
        $to = trim((string)($filters['date_to'] ?? ''));

        if ($from !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
            $from = '';
        }
        if ($to !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            $to = '';
        }

        $sql = 'SELECT id, name, email, phone, created_at FROM clients WHERE 1=1';
        $types = '';
        $params = [];

        if ($q !== '') {
            $sql .= ' AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $like = '%' . $q . '%';
            $types .= 'sss';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($from !== '') {
            $sql .= ' AND DATE(created_at) >= ?';
            $types .= 's';
            $params[] = $from;
        }
        if ($to !== '') {
            $sql .= ' AND DATE(created_at) <= ?';
            $types .= 's';
            $params[] = $to;
        }

        $sql .= ' ORDER BY created_at DESC';

        if ($types === '') {
            $result = $this->conn->query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getClientById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT id, account_id, name, email, phone, profile_image, created_at
             FROM clients
             WHERE id=?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /** Password hash only — for verifying current password on settings change. */
    public function getClientPasswordHashById(int $id): ?string
    {
        $stmt = $this->conn->prepare('SELECT password FROM clients WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return isset($row['password']) ? (string) $row['password'] : null;
    }

    // Used by CaretakerController AJAX sometimes
    public function getClientDetails($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT id, account_id, name, email, phone, profile_image, created_at
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
        $img   = $data['profile_image'] ?? 'default.jpg';

        $stmt->bind_param("ssssi", $name, $email, $phone, $img, $id);
        $ok = $stmt->execute();

        if ($ok && $this->hasAccountLinking()) {
            $client = $this->getClientById($id);
            $accountId = (int)($client['account_id'] ?? 0);
            if ($accountId > 0) {
                $role = 'client';
                $status = 'Active';
                $sync = $this->conn->prepare("UPDATE accounts SET name=?, email=?, role=?, status=? WHERE id=?");
                $sync->bind_param("ssssi", $name, $email, $role, $status, $accountId);
                $sync->execute();
                $sync->close();
            }
        }

        return $ok;
    }

    public function updateClientPassword($id, $hashedPassword)
    {
        $stmt = $this->conn->prepare("UPDATE clients SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashedPassword, $id);
        $ok = $stmt->execute();

        if ($ok && $this->hasAccountLinking()) {
            $client = $this->getClientById($id);
            $accountId = (int)($client['account_id'] ?? 0);
            if ($accountId > 0) {
                $sync = $this->conn->prepare("UPDATE accounts SET password=? WHERE id=?");
                $sync->bind_param("si", $hashedPassword, $accountId);
                $sync->execute();
                $sync->close();
            }
        }

        return $ok;
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

    private function getTimeRangeFromString($timeString)
    {
        $map = [
            "Morning (8am - 12pm)" => ["08:00:00", "12:00:00"],
            "Evening (1pm - 5pm)"  => ["13:00:00", "17:00:00"],
            "Night (6pm - 10pm)"   => ["18:00:00", "22:00:00"],
            "Full Time (8am - 5pm)" => ["08:00:00", "17:00:00"]
        ];

        return $map[$timeString] ?? ["00:00:00", "23:59:59"];
    }

    private function calculateBookingEndDate($bookingDate, $basis, $duration)
    {
        try {
            $start = new DateTime($bookingDate);
            $duration = max(1, (int)$duration);
            $basis = strtolower(trim((string)$basis));

            if ($basis === 'hourly') {
                return $start->format('Y-m-d');
            }

            if ($basis === 'monthly') {
                $start->modify('+' . $duration . ' month');
                $start->modify('-1 day');
                return $start->format('Y-m-d');
            }

            if ($basis === 'yearly') {
                $start->modify('+' . $duration . ' year');
                $start->modify('-1 day');
                return $start->format('Y-m-d');
            }

            // Default to day-based booking window.
            $start->modify('+' . ($duration - 1) . ' day');
            return $start->format('Y-m-d');
        } catch (Exception $e) {
            return $bookingDate;
        }
    }

    private function hasBookingConflictForField($field, $fieldId, $bookingDate, $preferredTime, $basis, $duration)
    {
        $allowedFields = ['caretaker_id', 'client_id'];
        if (!in_array($field, $allowedFields, true)) {
            return false;
        }

        $startDate = (string)$bookingDate;
        $endDate = $this->calculateBookingEndDate($startDate, $basis, $duration);
        [$searchStart, $searchEnd] = $this->getTimeRangeFromString((string)$preferredTime);

        $sql = "SELECT b.id
                FROM bookings b
                WHERE b.{$field} = ?
                  AND LOWER(b.status) IN (
                    'requested','payment_requested','advance_paid',
                    'accepted','approved','change_requested','reschedule_requested'
                  )
                  AND b.booking_date <= ?
                  AND (
                        CASE
                            WHEN LOWER(b.basis) = 'hourly' THEN b.booking_date
                            WHEN LOWER(b.basis) = 'monthly' THEN DATE_SUB(DATE_ADD(b.booking_date, INTERVAL GREATEST(b.duration, 1) MONTH), INTERVAL 1 DAY)
                            WHEN LOWER(b.basis) = 'yearly' THEN DATE_SUB(DATE_ADD(b.booking_date, INTERVAL GREATEST(b.duration, 1) YEAR), INTERVAL 1 DAY)
                            ELSE DATE_SUB(DATE_ADD(b.booking_date, INTERVAL GREATEST(b.duration, 1) DAY), INTERVAL 1 DAY)
                        END
                  ) >= ?
                  AND (
                        LOWER(?) <> 'hourly'
                        OR LOWER(b.basis) <> 'hourly'
                        OR (
                            ? < CASE b.preferred_time
                                WHEN 'Morning (8am - 12pm)' THEN '12:00:00'
                                WHEN 'Evening (1pm - 5pm)' THEN '17:00:00'
                                WHEN 'Night (6pm - 10pm)' THEN '22:00:00'
                                WHEN 'Full Time (8am - 5pm)' THEN '17:00:00'
                                ELSE '23:59:59'
                            END
                            AND
                            ? > CASE b.preferred_time
                                WHEN 'Morning (8am - 12pm)' THEN '08:00:00'
                                WHEN 'Evening (1pm - 5pm)' THEN '13:00:00'
                                WHEN 'Night (6pm - 10pm)' THEN '18:00:00'
                                WHEN 'Full Time (8am - 5pm)' THEN '08:00:00'
                                ELSE '00:00:00'
                            END
                        )
                  )
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $normalizedBasis = strtolower(trim((string)$basis));
        // True overlap: existing_start <= requested_end AND existing_end >= requested_start
        $stmt->bind_param("isssss", $fieldId, $endDate, $startDate, $normalizedBasis, $searchStart, $searchEnd);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return !empty($row);
    }

    public function hasCaretakerBookingConflict($caretakerId, $bookingDate, $preferredTime, $basis, $duration)
    {
        return $this->hasBookingConflictForField('caretaker_id', (int)$caretakerId, $bookingDate, $preferredTime, $basis, $duration);
    }

    public function hasClientBookingConflict($clientId, $bookingDate, $preferredTime, $basis, $duration)
    {
        return $this->hasBookingConflictForField('client_id', (int)$clientId, $bookingDate, $preferredTime, $basis, $duration);
    }

    public function createBooking($data)
    {
        // Set service_start_date to booking_date if not provided
        if (!isset($data['service_start_date'])) {
            $data['service_start_date'] = $data['booking_date'];
        }

        $sql = "INSERT INTO bookings
                (client_id, caretaker_id, service_type, basis, duration, preferred_time, booking_date,
                 service_start_date, district, street, address_line1, address_line2, postal_code,
                 customization, customization_hours, customization_price, total_payment, status,
                 advance_months, total_months, advance_balance)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        // Set default values for new fields
        $advanceMonths = $data['advance_months'] ?? 0;
        $totalMonths = $data['total_months'] ?? 0;
        $advanceBalance = $data['advance_balance'] ?? 0.00;

        $stmt->bind_param(
            "iississsssssssiddsiid",
            $data['client_id'],
            $data['caretaker_id'],
            $data['service_type'],
            $data['basis'],
            $data['duration'],
            $data['preferred_time'],
            $data['booking_date'],
            $data['service_start_date'],
            $data['district'],
            $data['street'],
            $data['address_line1'],
            $data['address_line2'],
            $data['postal_code'],
            $data['customization'],
            $data['customization_hours'],
            $data['customization_price'],
            $data['total_payment'],
            $data['status'],
            $advanceMonths,
            $totalMonths,
            $advanceBalance
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
                    b.service_start_date,
                    b.preferred_time,
                    b.basis,
                    b.duration,
                    b.service_type,
                    b.total_payment,
                    b.advance_amount,
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
                LEFT JOIN caretakers c ON b.caretaker_id = c.id
                WHERE b.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUpcomingBookings($clientId)
    {
        // Keep booking status aligned with approved advance payments.
        $this->syncAdvancePaidBookingStatuses((int)$clientId);

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
                            AND COALESCE(b.service_start_date, b.booking_date) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
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
            $link = URLROOT . "/client/paymentDetails/" . $bookingId;

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
            WHERE COALESCE(service_start_date, booking_date) < CURDATE()
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
                    b.total_payment,
                    b.advance_amount,
                    b.service_start_date,
                    b.district,
                    c.name AS caretaker_name
                FROM bookings b
                JOIN caretakers c ON b.caretaker_id = c.id
                WHERE b.client_id = ?
                                    AND b.status IN ('Requested','Payment_Requested','Advance_Paid','Accepted','Reschedule_Requested','Change_Requested')
                                    AND COALESCE(b.service_start_date, b.booking_date) > CURDATE()
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
                            AND status IN ('Requested', 'Payment_Requested', 'Accepted', 'Advance_Paid', 'Change_Requested', 'Reschedule_Requested')
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
                b.total_payment,
                b.advance_amount,
                b.service_start_date,
                b.district,
                ct.name AS caretaker_name
            FROM bookings b
            JOIN caretakers ct ON b.caretaker_id = ct.id
            WHERE b.client_id = ?
                            AND b.status IN ('Requested','Payment_Requested','Accepted','Advance_Paid','Reschedule_Requested','Change_Requested')
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
                            AND status IN ('Requested', 'Payment_Requested', 'Accepted', 'Advance_Paid', 'Change_Requested', 'Reschedule_Requested')
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
                b.service_start_date,
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

    /**
     * All bookings for the client (newest first) — unified “My bookings” table.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllBookingsForClientOverview($clientId, $limit = 200)
    {
        $limit = max(1, min(500, (int) $limit));
        $sql = "SELECT
                    b.id AS booking_id,
                    b.booking_date,
                    b.preferred_time,
                    b.duration,
                    b.basis,
                    b.service_type,
                    b.status,
                    b.customization,
                    b.total_payment,
                    b.advance_amount,
                    b.service_start_date,
                    b.service_location,
                    b.district,
                    b.street,
                    b.address_line1,
                    b.address_line2,
                    b.postal_code,
                    b.created_at,
                    b.advance_paid_date,
                    b.advance_months,
                    b.total_months,
                    b.advance_balance,
                    b.customization_hours,
                    b.customization_price,
                    b.caretaker_changed_once,
                    b.refund_status,
                    b.service_days_used,
                    b.cancellation_reason,
                    b.cancelled_at,
                    ct.name AS caretaker_name,
                    ct.email AS caretaker_email,
                    ct.phone AS caretaker_phone
                FROM bookings b
                JOIN caretakers ct ON b.caretaker_id = ct.id
                WHERE b.client_id = ?
                ORDER BY b.booking_date DESC, b.id DESC
                LIMIT " . $limit;

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

        $hasPayHereCols = $this->hasPayHereColumns();

        if ($hasPayHereCols) {
            $stmt = $this->conn->prepare(
                "INSERT INTO payments
                (booking_id, client_id, caretaker_id, total_booking_amount, customization_price, amount, remaining_balance,
                 payment_method, payment_type, status, due_date, payhere_order_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
        } else {
            $stmt = $this->conn->prepare(
                "INSERT INTO payments
                (booking_id, client_id, caretaker_id, total_booking_amount, customization_price, amount, remaining_balance,
                 payment_method, payment_type, status, due_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
        }

        $status = 'pending';
        $dueDate = $paymentData['due_date'] ?? null;

        $totalBookingAmount = (float)($paymentData['total_booking_amount'] ?? 0);
        $amount = (float)($paymentData['amount'] ?? 0);
        $customizationPrice = (float)($paymentData['customization_price'] ?? 0);

        $remainingBalance = $totalBookingAmount - $amount;

        if ($hasPayHereCols) {
            $orderId = $paymentData['payhere_order_id'] ?? null;
            $stmt->bind_param(
                "iiidddssssss",
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
                $dueDate,
                $orderId
            );
        } else {
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
        }

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

    public function getClientPaymentSummary($clientId)
    {
        $summary = [
            'pending_amount' => 0.0,
            'due_this_week_count' => 0,
            'overdue_count' => 0,
            'paid_this_month' => 0.0,
            'active_bookings_with_payments' => 0
        ];

        $stmt = $this->conn->prepare(
            "SELECT COALESCE(SUM(amount), 0) AS pending_amount
             FROM recurring_payments
             WHERE client_id = ?
               AND status IN ('pending', 'overdue')"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $summary['pending_amount'] = (float)($stmt->get_result()->fetch_assoc()['pending_amount'] ?? 0);
        $stmt->close();

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS due_this_week_count
             FROM recurring_payments
             WHERE client_id = ?
               AND status = 'pending'
               AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $summary['due_this_week_count'] = (int)($stmt->get_result()->fetch_assoc()['due_this_week_count'] ?? 0);
        $stmt->close();

        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS overdue_count
             FROM recurring_payments
             WHERE client_id = ?
               AND status = 'overdue'"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $summary['overdue_count'] = (int)($stmt->get_result()->fetch_assoc()['overdue_count'] ?? 0);
        $stmt->close();

        $stmt = $this->conn->prepare(
            "SELECT COALESCE(SUM(amount), 0) AS paid_this_month
             FROM payments
             WHERE client_id = ?
               AND status = 'approved'
               AND DATE_FORMAT(COALESCE(approved_at, created_at), '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $summary['paid_this_month'] = (float)($stmt->get_result()->fetch_assoc()['paid_this_month'] ?? 0);
        $stmt->close();

        $stmt = $this->conn->prepare(
            "SELECT COUNT(DISTINCT b.id) AS booking_count
             FROM bookings b
             WHERE b.client_id = ?
               AND b.status IN ('Payment_Requested', 'Advance_Paid', 'Accepted', 'Reschedule_Requested', 'Change_Requested')"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $summary['active_bookings_with_payments'] = (int)($stmt->get_result()->fetch_assoc()['booking_count'] ?? 0);
        $stmt->close();

        return $summary;
    }

    public function getClientActionRequiredPayments($clientId)
    {
        $items = [];

        // Advance payments requested by HR.
        $stmt = $this->conn->prepare(
            "SELECT
                b.id AS booking_id,
                b.service_type,
                b.basis,
                b.duration,
                b.total_payment,
                b.advance_balance,
                b.status AS booking_status,
                COALESCE(b.service_start_date, b.booking_date) AS due_date,
                c.name AS caretaker_name
            FROM bookings b
            JOIN caretakers c ON c.id = b.caretaker_id
            WHERE b.client_id = ?
                            AND LOWER(TRIM(b.status)) = 'payment_requested'
                            AND LOWER(TRIM(b.status)) NOT IN ('cancelled', 'rejected', 'completed')
            ORDER BY due_date ASC"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($rows as $row) {
            $items[] = [
                'source_type' => 'advance',
                'recurring_payment_id' => null,
                'booking_id' => (int)$row['booking_id'],
                'service_type' => $row['service_type'],
                'basis' => $row['basis'],
                'caretaker_name' => $row['caretaker_name'],
                'amount_due' => (float)($row['advance_balance'] > 0 ? $row['advance_balance'] : $row['total_payment']),
                'due_date' => $row['due_date'],
                'payment_status' => 'advance_required',
                'days_delta' => (int)((strtotime($row['due_date']) - strtotime(date('Y-m-d'))) / 86400),
                'can_pay_now' => true,
                'booking_status' => $row['booking_status']
            ];
        }

        // Recurring payment cycles.
        $stmt = $this->conn->prepare(
            "SELECT
                rp.id AS recurring_payment_id,
                rp.booking_id,
                rp.cycle_number,
                rp.due_date,
                rp.amount,
                rp.status,
                rp.grace_period_end,
                b.service_type,
                b.basis,
                b.status AS booking_status,
                c.name AS caretaker_name
            FROM recurring_payments rp
            JOIN bookings b ON b.id = rp.booking_id
            JOIN caretakers c ON c.id = b.caretaker_id
            WHERE rp.client_id = ?
              AND rp.status IN ('pending', 'overdue')
                            AND LOWER(TRIM(b.status)) NOT IN ('cancelled', 'rejected', 'completed')
            ORDER BY
              CASE WHEN rp.status = 'overdue' THEN 1 ELSE 2 END,
              rp.due_date ASC"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $todayTs = strtotime(date('Y-m-d'));
        $next7Ts = strtotime('+7 days', $todayTs);

        foreach ($rows as $row) {
            $dueTs = strtotime($row['due_date']);
            $daysDelta = (int)(($dueTs - $todayTs) / 86400);
            $withinGrace = true;

            if (!empty($row['grace_period_end'])) {
                $withinGrace = strtotime($row['grace_period_end']) >= $todayTs;
            }

            $canPayNow = false;
            if (strtolower(trim((string)$row['booking_status'])) !== 'cancelled') {
                if ($row['status'] === 'overdue') {
                    $canPayNow = $withinGrace;
                } else {
                    $canPayNow = ($dueTs <= $next7Ts);
                }
            }

            $items[] = [
                'source_type' => 'recurring',
                'recurring_payment_id' => (int)$row['recurring_payment_id'],
                'booking_id' => (int)$row['booking_id'],
                'service_type' => $row['service_type'],
                'basis' => $row['basis'],
                'caretaker_name' => $row['caretaker_name'],
                'amount_due' => (float)$row['amount'],
                'due_date' => $row['due_date'],
                'payment_status' => $row['status'],
                'days_delta' => $daysDelta,
                'can_pay_now' => $canPayNow,
                'booking_status' => $row['booking_status']
            ];
        }

        return $items;
    }

    public function getClientBookingPaymentOverview($clientId)
    {
        $sql = "SELECT
                    b.id AS booking_id,
                    b.service_type,
                    b.basis,
                    b.duration,
                    b.status,
                    COALESCE(b.service_start_date, b.booking_date) AS service_start_date,
                    ct.name AS caretaker_name,
                    (
                        SELECT rp1.due_date
                        FROM recurring_payments rp1
                        WHERE rp1.booking_id = b.id
                          AND rp1.status IN ('pending', 'overdue')
                        ORDER BY rp1.due_date ASC, rp1.cycle_number ASC
                        LIMIT 1
                    ) AS next_payment_due_date,
                    (
                        SELECT rp2.amount
                        FROM recurring_payments rp2
                        WHERE rp2.booking_id = b.id
                          AND rp2.status IN ('pending', 'overdue')
                        ORDER BY rp2.due_date ASC, rp2.cycle_number ASC
                        LIMIT 1
                    ) AS next_payment_amount,
                    (
                        SELECT COUNT(*)
                        FROM recurring_payments rp3
                        WHERE rp3.booking_id = b.id
                    ) AS total_cycles,
                    (
                        SELECT COUNT(*)
                        FROM recurring_payments rp4
                        WHERE rp4.booking_id = b.id
                          AND rp4.status = 'paid'
                    ) AS paid_cycles
                FROM bookings b
                JOIN caretakers ct ON ct.id = b.caretaker_id
                WHERE b.client_id = ?
                  AND b.status IN ('Payment_Requested', 'Advance_Paid', 'Accepted', 'Reschedule_Requested', 'Change_Requested', 'Completed')
                ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getClientPaymentHistoryDetailed($clientId)
    {
        $sql = "SELECT
                    p.id,
                    p.booking_id,
                    p.amount,
                    p.payment_method,
                    p.payment_type,
                    p.status,
                    COALESCE(p.approved_at, p.paid_date, p.created_at) AS paid_at,
                    b.service_type,
                    b.basis,
                    b.total_payment,
                    ct.name AS caretaker_name
                FROM payments p
                JOIN bookings b ON b.id = p.booking_id
                JOIN caretakers ct ON ct.id = p.caretaker_id
                WHERE p.client_id = ?
                ORDER BY COALESCE(p.approved_at, p.paid_date, p.created_at) DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getBookingPaymentTimelineData($clientId, $bookingId)
    {
        $result = [
            'booking' => null,
            'payments' => [],
            'recurring' => []
        ];

        $stmt = $this->conn->prepare(
            "SELECT
                b.id AS booking_id,
                b.client_id,
                b.service_type,
                b.basis,
                b.duration,
                b.status,
                b.total_payment,
                b.advance_months,
                b.total_months,
                b.advance_paid_date,
                COALESCE(b.service_start_date, b.booking_date) AS service_start_date,
                c.name AS caretaker_name
            FROM bookings b
            JOIN caretakers c ON c.id = b.caretaker_id
            WHERE b.client_id = ? AND b.id = ?
            LIMIT 1"
        );
        $stmt->bind_param("ii", $clientId, $bookingId);
        $stmt->execute();
        $result['booking'] = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$result['booking']) {
            return $result;
        }

        $stmt = $this->conn->prepare(
            "SELECT
                id,
                payment_type,
                amount,
                payment_method,
                status,
                due_date,
                approved_at,
                created_at
            FROM payments
            WHERE client_id = ? AND booking_id = ?
            ORDER BY created_at ASC"
        );
        $stmt->bind_param("ii", $clientId, $bookingId);
        $stmt->execute();
        $result['payments'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $stmt = $this->conn->prepare(
            "SELECT
                id,
                cycle_number,
                cycle_type,
                due_date,
                amount,
                status,
                paid_at,
                grace_period_end,
                payment_id
            FROM recurring_payments
            WHERE client_id = ? AND booking_id = ?
            ORDER BY cycle_number ASC, due_date ASC"
        );
        $stmt->bind_param("ii", $clientId, $bookingId);
        $stmt->execute();
        $result['recurring'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $result;
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
                    p.due_date,
                    p.paid_date,
                    p.created_at,
                    p.approved_at,
                    p.customization_price AS payment_customization_price,
                    c.name AS client_name,
                    c.phone AS client_phone,
                    ct.name AS caretaker_name,
                    b.service_type,
                    b.booking_date,
                    b.preferred_time,
                    b.basis,
                    b.duration,
                    b.total_payment,
                    b.status AS booking_status,
                    b.district AS booking_district,
                    b.street AS booking_street,
                    b.address_line1 AS booking_address_line1,
                    b.address_line2 AS booking_address_line2,
                    b.postal_code AS booking_postal_code,
                    b.service_location AS booking_service_location,
                    b.customization AS booking_customization,
                    b.customization_hours AS booking_customization_hours,
                    b.customization_price AS booking_customization_price,
                    b.created_at AS booking_created_at,
                    b.advance_paid_date AS booking_advance_paid_date,
                    b.service_start_date AS booking_service_start_date,
                    b.advance_amount AS booking_advance_amount,
                    b.refund_status AS booking_refund_status,
                    b.advance_balance AS booking_advance_balance,
                    b.advance_months AS booking_advance_months,
                    b.total_months AS booking_total_months
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
                    b.duration,
                    b.status AS booking_status
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

    public function getRecurringPaymentByIdForClient($recurringPaymentId, $clientId, $bookingId)
    {
        $sql = "SELECT * FROM recurring_payments
                WHERE id = ? AND client_id = ? AND booking_id = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $recurringPaymentId, $clientId, $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $stmt->close();

        return $payment ?: null;
    }

    public function updatePaymentStatus($paymentId, $status)
    {
        $stmt = $this->conn->prepare("UPDATE payments SET status = ?, approved_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $paymentId);
        return $stmt->execute();
    }

    public function getPaymentByOrderId($orderId)
    {
        if (!$this->hasPayHereColumns()) {
            return null;
        }

        $sql = "SELECT
                    p.*,
                    b.status AS booking_status
                FROM payments p
                JOIN bookings b ON p.booking_id = b.id
                WHERE p.payhere_order_id = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $result ?: null;
    }

    public function setPayHereOrderId($paymentId, $orderId)
    {
        if (!$this->hasPayHereColumns()) {
            return true;
        }

        $stmt = $this->conn->prepare("UPDATE payments SET payhere_order_id = ? WHERE id = ?");
        $stmt->bind_param("si", $orderId, $paymentId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updatePaymentGatewayStatus($paymentId, $status, $statusCode, $statusMessage, $payherePaymentId, $md5sig)
    {
        if ($this->hasPayHereColumns()) {
            $stmt = $this->conn->prepare(
                "UPDATE payments
                 SET status = ?,
                     approved_at = CASE WHEN ? = 'approved' THEN NOW() ELSE approved_at END,
                     paid_date = CASE WHEN ? = 'approved' THEN NOW() ELSE paid_date END,
                     payhere_status_code = ?,
                     payhere_status_message = ?,
                     payhere_payment_id = ?,
                     payhere_md5sig = ?
                 WHERE id = ?"
            );

            $stmt->bind_param("sssisssi", $status, $status, $status, $statusCode, $statusMessage, $payherePaymentId, $md5sig, $paymentId);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        }

        $stmt = $this->conn->prepare(
            "UPDATE payments
             SET status = ?,
                 approved_at = CASE WHEN ? = 'approved' THEN NOW() ELSE approved_at END,
                 paid_date = CASE WHEN ? = 'approved' THEN NOW() ELSE paid_date END
             WHERE id = ?"
        );
        $stmt->bind_param("sssi", $status, $status, $status, $paymentId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    private function hasPayHereColumns(): bool
    {
        static $checked = false;
        static $hasColumns = false;

        if ($checked) {
            return $hasColumns;
        }

        $checked = true;
        $result = $this->conn->query("SHOW COLUMNS FROM payments LIKE 'payhere_order_id'");
        $hasColumns = (bool)($result && $result->num_rows > 0);

        return $hasColumns;
    }

    public function updateBookingStatus($bookingId, $status)
    {
        $stmt = $this->conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $bookingId);
        return $stmt->execute();
    }

    /**
     * Update advance_paid_date when advance payment is approved
     *
     * @param int $bookingId
     * @return bool
     */
    public function updateBookingAdvancePaidDate($bookingId)
    {
        $stmt = $this->conn->prepare("UPDATE bookings SET advance_paid_date = NOW() WHERE id = ?");
        $stmt->bind_param("i", $bookingId);
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

    /**
     * Booking counts by status (admin dashboard).
     *
     * @return array{labels: string[], values: int[]}
     */
    public function getBookingStatusDistribution(): array
    {
        $res = $this->conn->query(
            "SELECT COALESCE(NULLIF(TRIM(status), ''), 'Unknown') AS st, COUNT(*) AS cnt FROM bookings GROUP BY st ORDER BY cnt DESC"
        );
        if (!$res) {
            return ['labels' => [], 'values' => []];
        }
        $labels = [];
        $values = [];
        while ($row = $res->fetch_assoc()) {
            $labels[] = (string) $row['st'];
            $values[] = (int) $row['cnt'];
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
                  AND LOWER(p.status) IN ('approved', 'paid', 'success', 'completed')";

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
        $this->conn->begin_transaction();

        try {
            $sql = "UPDATE bookings
                SET status = 'cancelled',
                    cancellation_reason = ?,
                    cancelled_at = NOW()
                WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $reason, $bookingId);
            $stmt->execute();
            $bookingUpdated = $stmt->affected_rows > 0;
            $stmt->close();

            // Keep payment state in sync with booking cancellation for HR payment management.
            $payStmt = $this->conn->prepare(
                "UPDATE payments
                 SET status = 'rejected'
                 WHERE booking_id = ?
                   AND status = 'pending'"
            );
            $payStmt->bind_param("i", $bookingId);
            $payStmt->execute();
            $payStmt->close();

            $this->conn->commit();
            return $bookingUpdated;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log("Cancel booking failed for booking #{$bookingId}: " . $e->getMessage());
            return false;
        }
    }

    public function getAdvancePaymentPendingBookings($clientId)
    {
        // Ensure stale statuses are reconciled before showing pending alerts.
        $this->syncAdvancePaidBookingStatuses((int)$clientId);

        $sql = "SELECT
                    b.id AS booking_id,
                    b.booking_date,
                    b.preferred_time,
                    b.duration,
                    b.basis,
                    b.service_type,
                    b.district,
                    b.total_payment,
                    b.advance_amount,
                    b.service_start_date,
                    ct.name AS caretaker_name
                FROM bookings b
                JOIN caretakers ct ON b.caretaker_id = ct.id
                WHERE b.client_id = ?
                  AND b.status = 'Payment_Requested'
                ORDER BY b.booking_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function syncAdvancePaidBookingStatuses(int $clientId): void
    {
        $sql = "UPDATE bookings b
                JOIN (
                    SELECT
                        booking_id,
                        MAX(COALESCE(approved_at, paid_date, created_at)) AS latest_paid_at
                    FROM payments
                    WHERE status IN ('pending', 'approved')
                      AND LOWER(TRIM(COALESCE(payment_type, 'advance'))) = 'advance'
                    GROUP BY booking_id
                ) ap ON ap.booking_id = b.id
                SET b.status = 'Advance_Paid',
                    b.advance_paid_date = COALESCE(b.advance_paid_date, ap.latest_paid_at)
                WHERE b.client_id = ?
                  AND b.status = 'Payment_Requested'";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return;
        }

        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $stmt->close();
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

    /**
     * Get client bookings that are assigned to a specific caretaker
     * Used for caretaker view of client details
     */
    public function getClientBookingsForCaretaker($clientId, $caretakerId)
    {
        $sql = "SELECT b.*, c.name as client_name, ct.name as caretaker_name
                FROM bookings b
                JOIN clients c ON b.client_id = c.id
                JOIN caretakers ct ON b.caretaker_id = ct.id
                WHERE b.client_id = ? AND b.caretaker_id = ?
                ORDER BY b.booking_date DESC, b.preferred_time DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $clientId, $caretakerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
