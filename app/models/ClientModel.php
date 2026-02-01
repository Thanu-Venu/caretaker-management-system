<?php
require_once APPROOT . '/core/Database.php';

class ClientModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }
    public function getAllClients() {
        $result = $this->conn->query(
            "SELECT id, name, email, phone, created_at
             FROM clients
             ORDER BY created_at DESC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getClientById($id) {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, phone, profile_image, created_at
             FROM clients
             WHERE id=?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateClient($id, $data) {
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

    public function updateClientPassword($id, $hashedPassword) {
        $stmt = $this->conn->prepare("UPDATE clients SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }

    public function deleteClient($id) {
        $stmt = $this->conn->prepare("DELETE FROM clients WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

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
    public function getCaretakerById($id) {
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

    public function createBooking($data) {
        $sql = "INSERT INTO bookings
            (
                client_id, caretaker_id, service_type, basis, duration, preferred_time,
                booking_date, end_date,
                district, street, address_line1, address_line2, postal_code,
                customization, total_payment, status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $district      = $data['district'] ?? '';
        $street        = $data['street'] ?? '';
        $address_line1 = $data['address_line1'] ?? '';
        $address_line2 = $data['address_line2'] ?? '';
        $postal_code   = $data['postal_code'] ?? '';
        $customization = $data['customization'] ?? '';

        $stmt->bind_param(
            "iississsssssssds",
            $data['client_id'],       // i
            $data['caretaker_id'],    // i
            $data['service_type'],    // s
            $data['basis'],           // s
            $data['duration'],        // i
            $data['preferred_time'],  // s
            $data['booking_date'],    // s
            $data['end_date'],        // s
            $district,                // s
            $street,                  // s
            $address_line1,           // s
            $address_line2,           // s
            $postal_code,             // s
            $customization,           // s
            $data['total_payment'],   // d
            $data['status']           // s
        );

        if ($stmt->execute()) {
            return $this->conn->insert_id;
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
                    b.district,
                    b.street,
                    b.address_line1,
                    b.address_line2,
                    b.postal_code,
                    b.customization,
                    c.name AS caretaker_name
                FROM bookings b
                JOIN caretakers c ON b.caretaker_id = c.id
                WHERE b.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUpcomingBookings($clientId) {
        $updateSql = "
            UPDATE bookings
            SET status = 'Completed'
            WHERE client_id = ?
              AND booking_date < CURDATE()
              AND status IN ('Pending','Accepted')
        ";
        $u = $this->conn->prepare($updateSql);
        $u->bind_param("i", $clientId);
        $u->execute();

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
                  AND b.status IN ('Pending', 'Accepted')
                  AND b.booking_date >= CURDATE()
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
                    b.customization,
                    c.name AS caretaker_name
                FROM bookings b
                JOIN caretakers c ON b.caretaker_id = c.id
                WHERE b.client_id = ?
                  AND b.status = 'Completed'
                ORDER BY b.booking_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPastBookingsWithFeedback($clientId) {
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

    public function cancelBooking($booking_id, $reason) {
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

    public function rescheduleBooking($bookingId, $newDate, $newTime, $newDuration) {
        $sql = "UPDATE bookings
                SET booking_date = ?,
                    preferred_time = ?,
                    duration = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssii", $newDate, $newTime, $newDuration, $bookingId);
        return $stmt->execute();
    }

    public function getBookingsByStatus($status) {
        $sql = "SELECT
                    b.id as booking_id,
                    b.booking_date,
                    b.preferred_time,
                    b.duration,
                    b.basis,
                    b.service_type,
                    c.name as caretaker_name
                FROM bookings b
                JOIN caretakers c ON b.caretaker_id = c.id
                WHERE b.status = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getCancelledBookings($clientId) {
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
                  AND b.status = 'Cancelled'
                ORDER BY b.cancelled_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $clientId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

   /* public function markAsPaid($bookingId) {
        $sql = "UPDATE bookings SET status = 'Paid' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        return $stmt->execute();
    }*/
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
        $stmt = $this->conn->prepare("SELECT id FROM feedbacks WHERE booking_id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function getCaretakerIdByBooking($bookingId) {
        $sql = "SELECT caretaker_id FROM bookings WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['caretaker_id'] : null;
    }

    public function sendNotificationToHR($data) {
        $sql = "INSERT INTO c_notifications (message, role) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $data['message'], $data['role']);
        return $stmt->execute();
    }

    public function getAllBookingsAdmin() {
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

    public function countClients() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM clients");
        return $result ? ($result->fetch_assoc()['total'] ?? 0) : 0;
    }

    public function countUpcomingBookings() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM bookings WHERE booking_date >= CURDATE()");
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getBookingsLast4Weeks() {
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

    public function getClientEngagementLast6Months() {
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

    public function calcEndDate(string $startDate, string $basis, int $duration): string
{
    $dt = new DateTime($startDate);
    $duration = max(1, (int)$duration);

    switch ($basis) {
        case 'Hourly':
        case 'Daily':
            // treat duration as number of days
            $dt->modify('+' . ($duration - 1) . ' days');
            break;

        case 'Monthly':
            $dt->modify('+' . $duration . ' months');
            $dt->modify('-1 day');
            break;

        case 'Yearly':
            $dt->modify('+' . $duration . ' years');
            $dt->modify('-1 day');
            break;

        default:
            // fallback: 1 day
            break;
    }

    return $dt->format('Y-m-d');
}

public function isCaretakerAvailable(int $caretakerId, string $reqStart, string $reqEnd): bool
{
    // 1) caretaker must be Active
    $stmt = $this->conn->prepare("SELECT status FROM caretakers WHERE id=?");
    $stmt->bind_param("i", $caretakerId);
    $stmt->execute();
    $ct = $stmt->get_result()->fetch_assoc();

    if (!$ct || $ct['status'] !== 'Active') return false;

    // 2) booking conflicts (Pending, Accepted)
    $sqlBookings = "SELECT COUNT(*) AS total
                    FROM bookings
                    WHERE caretaker_id = ?
                      AND status IN ('Pending','Accepted')
                      AND NOT (end_date < ? OR booking_date > ?)";

    $stmt = $this->conn->prepare($sqlBookings);
    $stmt->bind_param("iss", $caretakerId, $reqStart, $reqEnd);
    $stmt->execute();
    $b = $stmt->get_result()->fetch_assoc();
    if (($b['total'] ?? 0) > 0) return false;

    // 3) leave conflicts (Pending, Approved)
    $sqlLeaves = "SELECT COUNT(*) AS total
                  FROM leaves
                  WHERE user_id = ?
                    AND status IN ('Pending','Approved')
                    AND NOT (end_date < ? OR start_date > ?)";

    $stmt = $this->conn->prepare($sqlLeaves);
    $stmt->bind_param("iss", $caretakerId, $reqStart, $reqEnd);
    $stmt->execute();
    $l = $stmt->get_result()->fetch_assoc();
    if (($l['total'] ?? 0) > 0) return false;

    return true;
}
public function getCaretakersWithAvailability(string $reqStart, string $reqEnd, ?string $serviceType = null, ?string $location = null): array
{
    // fetch all active caretakers (optionally filter by service/location)
    $sql = "SELECT * FROM caretakers WHERE status='Active'";
    $params = [];
    $types = "";

    if ($serviceType) { $sql .= " AND service_type=?"; $types .= "s"; $params[] = $serviceType; }
        if ($location) {
            $sql .= " AND LOWER(location) LIKE LOWER(?)";
            $types .= "s";
            $params[] = "%" . strtolower($location) . "%";
        }

    $sql .= " ORDER BY rating DESC, created_at DESC";

    $stmt = $this->conn->prepare($sql);
    if (!empty($params)) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $all = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $available = [];
    $unavailable = [];

    foreach ($all as $ct) {
        $isFree = $this->isCaretakerAvailable((int)$ct['id'], $reqStart, $reqEnd); // call the function above (same model or share via helper)
        $ct['is_available'] = $isFree;

        if ($isFree) $available[] = $ct;
        else $unavailable[] = $ct;
    }

    return ['available' => $available, 'unavailable' => $unavailable];
}

public function getAlternativeCaretakers(
    int $excludeCaretakerId,
    string $serviceType,
    string $reqStart,
    string $reqEnd,
    ?string $location = null,
    int $limit = 6
): array {
    // base query: active + same service type + not the current caretaker
    $sql = "SELECT id, name, service_type, location, experience, qualifications, profile_image, rating
            FROM caretakers
            WHERE status='Active'
              AND service_type = ?
              AND id <> ?";

    $types = "si";
    $params = [$serviceType, $excludeCaretakerId];

    // optional: prefer same location
    if (!empty($location)) {
        $sql .= " AND location LIKE ?";
        $types .= "s";
        $params[] = "%$location%";
    }

    // order by rating then newest
    $sql .= " ORDER BY rating DESC, created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // filter by availability (bookings + leaves overlap)
    $available = [];
    foreach ($rows as $ct) {
        if ($this->isCaretakerAvailable((int)$ct['id'], $reqStart, $reqEnd)) {
            $available[] = $ct;
            if (count($available) >= $limit) break;
        }
    }

    return $available;
}

}

