<?php
require_once APPROOT . '/core/Database.php';

class CaretakerModel {
    private $conn;  

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /** @return array */
    public function getCaretakers() {
        $result = $this->conn->query("SELECT * FROM caretakers");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCaretakerById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM caretakers WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }



   public function addCaretaker($data) {
    // Ensure all expected keys exist to avoid binding nulls for NOT NULL columns
    $expected = ['name','email','phone','service_type','status','experience','location','qualifications','profile_image','password'];
    foreach ($expected as $key) {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            $data[$key] = '';
        }
    }

    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $this->conn->prepare(
        "INSERT INTO caretakers (name, email, phone, service_type, status, experience, location, qualifications, profile_image, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssssssss",
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['service_type'],
        $data['status'],
        $data['experience'],
        $data['location'],
        $data['qualifications'],
        $data['profile_image'],
        $hashedPassword
    );

    return $stmt->execute();
 }

  private $timeMap = [
    "Morning (8am - 12pm)" => ["08:00:00", "12:00:00"],
    "Evening (1pm - 5pm)"  => ["13:00:00", "17:00:00"],
    "Night (6pm - 10pm)"   => ["18:00:00", "22:00:00"],
    "Full Time (8am - 5pm)"=> ["08:00:00", "17:00:00"]
];





    public function updateCaretaker($id, $data, $profileImage = null)
{
    if ($profileImage) {
        // profile image update included
        $stmt = $this->conn->prepare(
            "UPDATE caretakers 
             SET name=?, email=?, phone=?, experience=?, location=?, qualifications=?, service_type=?, status=?, profile_image=?
             WHERE id=?"
        );

        $stmt->bind_param(
            "sssssssssi",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['experience'],
            $data['location'],
            $data['qualifications'],
            $data['service_type'],
            $data['status'],
            $profileImage,
            $id
        );
    } else {
        // without changing profile image
        $stmt = $this->conn->prepare(
            "UPDATE caretakers 
             SET name=?, email=?, phone=?, experience=?, location=?, qualifications=?, service_type=?, status=?
             WHERE id=?"
        );

        $stmt->bind_param(
            "ssssssssi",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['experience'],
            $data['location'],
            $data['qualifications'],
            $data['service_type'],
            $data['status'],
            $id
        );

    }

    return $stmt->execute();
}


     public function updateCaretakerDetails($id, $data) {
        $stmt = $this->conn->prepare("UPDATE caretakers SET name=?,email=?,phone=? WHERE id=?");
        $stmt->bind_param("sssi", $data['name'],$data['email'],$data['phone'],$id);
        return $stmt->execute();
    }

    public function deleteCaretaker($id) {
        $stmt = $this->conn->prepare("DELETE FROM caretakers WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }




     public function updateProfileCaretaker($id, $data) {

        $stmt = $this->conn->prepare(
            "UPDATE caretakers SET 
                name = ?, 
                email = ?, 
                phone = ?,  
                experience = ?, 
                qualifications = ?,
                profile_image = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssssssi",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['experience'],
            $data['qualifications'],
            $data['profile_image'],
            $id
        );

        return $stmt->execute();
    }

     public function updateCaretakerPassword($id, $hashedPassword) {

        $stmt = $this->conn->prepare(
            "UPDATE caretakers 
             SET password = ?
             WHERE id = ?"
        );

        $stmt->bind_param("si", $hashedPassword, $id);

        return $stmt->execute();
    }

private function getTimeRangeFromString($timeString)
{
    $map = [
        "Morning (8am - 12pm)" => ["08:00:00", "12:00:00"],
        "Evening (1pm - 5pm)"  => ["13:00:00", "17:00:00"],
        "Night (6pm - 10pm)"   => ["18:00:00", "22:00:00"],
        "Full Time (8am - 5pm)"=> ["08:00:00", "17:00:00"]
    ];

    return $map[$timeString] ?? ["00:00:00", "23:59:59"];
}

public function getAvailableCaretakers($service, $date, $preferredTime, $basis, $duration)
{
    $startDate = $date;
    if (strtolower($basis) === 'hourly') {
        $endDate = $date; // hourly bookings only block the same day
    } else {
        $endDate = date('Y-m-d', strtotime("+".($duration-1)." days", strtotime($date)));
    }
    list($searchStart, $searchEnd) = $this->getTimeRangeFromString($preferredTime);

    $sql = "
SELECT c.*
FROM caretakers c
WHERE c.service_type = ?
AND NOT EXISTS (
    SELECT 1 FROM bookings b
    WHERE b.caretaker_id = c.id
      AND b.status IN ('Requested','Payment_Requested','Advance_Paid','Accepted','Approved')
      AND b.booking_date <= ?
      AND DATE_ADD(b.booking_date, INTERVAL b.duration-1 DAY) >= ?
      AND (
          b.basis <> 'hourly'
          OR (
              ? <
              CASE b.preferred_time
                  WHEN 'Morning (8am - 12pm)' THEN '12:00:00'
                  WHEN 'Evening (1pm - 5pm)'  THEN '17:00:00'
                  WHEN 'Night (6pm - 10pm)'   THEN '22:00:00'
                  WHEN 'Full Time (8am - 5pm)' THEN '17:00:00'
              END
              AND
              ? >
              CASE b.preferred_time
                  WHEN 'Morning (8am - 12pm)' THEN '08:00:00'
                  WHEN 'Evening (1pm - 5pm)'  THEN '13:00:00'
                  WHEN 'Night (6pm - 10pm)'   THEN '18:00:00'
                  WHEN 'Full Time (8am - 5pm)' THEN '08:00:00'
              END
          )
      )
)
";


    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("sssss", $service, $startDate, $endDate, $searchStart, $searchEnd);

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}




    // Upcoming bookings
   // Get Upcoming Bookings for Caretaker
public function getUpcomingBookings($caretakerId) {
    $updateSql = "
        UPDATE bookings
        SET status = 'Completed'
        WHERE caretaker_id = ?
          AND booking_date < CURDATE()
          AND status IN ('Requested','Payment_Requested','Advance_Paid','Accepted')
    ";
    $updateStmt = $this->conn->prepare($updateSql);
    $updateStmt->bind_param("i", $caretakerId);
    $updateStmt->execute();
    $updateStmt->close();

    $sql = "SELECT 
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                CONCAT(
                    b.district, ', ',
                    b.street, ', ',
                    b.address_line1, ', ',
                    b.address_line2, ', ',
                    b.postal_code
                ) AS service_location,
                b.total_payment,
                c.name AS client_name
            FROM bookings b
            JOIN clients c ON c.id = b.client_id
            WHERE b.caretaker_id = ? AND b.status IN ('Accepted','Advance_Paid','Payment_Requested','Requested') AND b.booking_date >= CURDATE()
            ORDER BY b.booking_date ASC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $caretakerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get Past Bookings for Caretaker
public function getPastBookings($caretakerId) {
    $sql = "SELECT 
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                CONCAT(
                    b.district, ', ',
                    b.street, ', ',
                    b.address_line1, ', ',
                    b.address_line2, ', ',
                    b.postal_code
                ) AS service_location,
                b.total_payment,
                c.name AS client_name
            FROM bookings b
            JOIN clients c ON c.id = b.client_id
            WHERE b.caretaker_id = ? AND b.status = 'Completed' AND b.booking_date < CURDATE()
            ORDER BY b.booking_date DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $caretakerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}



// Get approved bookings with client details
public function getApprovedBookingsWithClientDetails($caretakerId) {
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
                c.id AS client_id,
                c.name AS client_name,
                c.phone AS client_phone,
                c.email AS client_email,
                p.amount AS advance_paid,
                p.status AS payment_status
            FROM bookings b
            JOIN clients c ON b.client_id = c.id
            LEFT JOIN payments p ON b.id = p.booking_id AND p.payment_type = 'advance'
            WHERE b.caretaker_id = ? AND b.status = 'Approved'
            ORDER BY b.booking_date ASC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $caretakerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}






    public function login($email, $password) {
    $stmt = $this->conn->prepare("SELECT * FROM caretakers WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $caretaker = $stmt->get_result()->fetch_assoc();
    if ($caretaker && password_verify($password, $caretaker['password'])) {
        return $caretaker;
    }
    return false;
}

public function getClientsByCaretaker($caretakerId)
{
    $sql = "SELECT 
                b.id AS booking_id,
                b.client_id,
                c.name AS client_name,
                b.service_type,
                b.booking_date,
                b.preferred_time
            FROM bookings b
            JOIN clients c ON b.client_id = c.id
            WHERE b.caretaker_id = ?
            ORDER BY b.booking_date DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param('i', $caretakerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

public function countCaretakers()
{
    $result = $this->conn->query("SELECT COUNT(*) AS total FROM caretakers");
    return $result->fetch_assoc()['total'] ?? 0;
}

public function addComplaint($data) {
    $stmt = $this->conn->prepare(
        "INSERT INTO ct_complaints (client_id, caretaker_id, service_type, service_date, description, status) 
         VALUES (?, ?, ?, ?, ?, 'Pending')"
    );

    $stmt->bind_param(
        "iisss",
        $data['client_id'],
        $data['caretaker_id'],
        $data['service_type'],
        $data['service_date'],
        $data['description']
    );

    return $stmt->execute();
}
  
public function getResolvedComplaintsByCaretaker($caretaker_id)
{
    $stmt = $this->conn->prepare(
        "SELECT * FROM ct_complaints 
         WHERE caretaker_id = ? AND status = 'Resolved'
         ORDER BY service_date DESC"
    );

    $stmt->bind_param("i", $caretaker_id);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}



public function getCaretakerFeedbacks($caretakerId)
{
    $sql = "SELECT 
                cl.name AS client_name,
                b.service_type AS service,
                f.rating,
                f.feedback AS comment,
                f.created_at
            FROM feedbacks f
            JOIN clients cl ON f.client_id = cl.id
            JOIN bookings b ON f.booking_id = b.id
            WHERE f.caretaker_id = ?
            ORDER BY f.created_at DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $caretakerId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}



}

?>