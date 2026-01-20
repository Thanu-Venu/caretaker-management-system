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


public function getCaretakersFiltered($service, $location)
{
    $sql = "SELECT * FROM caretakers WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($service)) {
        $sql .= " AND service_type=?";
        $params[] = $service;
        $types .= "s";
    }

    if (!empty($location)) {
        $sql .= " AND location=?";
        $params[] = $location;
        $types .= "s";
    }

    $stmt = $this->conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}



    // Upcoming bookings
   // Get Upcoming Bookings for Caretaker
public function getUpcomingBookings($caretakerId) {
    $sql = "SELECT 
                b.id AS booking_id,
                b.booking_date,
                b.preferred_time,
                b.basis,
                b.duration,
                b.service_type,
                b.service_location,
                b.total_payment,
                c.name AS client_name
            FROM bookings b
            JOIN clients c ON c.id = b.client_id
            WHERE b.caretaker_id = ? AND b.status = 'Accepted' AND b.booking_date >= CURDATE()
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
                b.service_location,
                b.total_payment,
                c.name AS client_name
            FROM bookings b
            JOIN clients c ON c.id = b.client_id
            WHERE b.caretaker_id = ? AND b.status = 'Accepted' AND b.booking_date < CURDATE()
            ORDER BY b.booking_date DESC";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $caretakerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}









    public function getActiveCaretakers() {
        $result = $this->conn->query("SELECT * FROM caretakers WHERE status='Active'");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

// HR: get caretakers with availability
public function getCaretakersForHR() {
    $query = "
        SELECT id, name, availability, location, check_in, check_out
        FROM caretakers
        WHERE status = 'Active'
        ORDER BY id DESC
    ";
    $result = $this->conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// HR: update availability
public function updateAvailability($data) {
    $stmt = $this->conn->prepare(
        "UPDATE caretakers 
         SET availability=?, location=?, check_in=?, check_out=? 
         WHERE id=?"
    );

    $stmt->bind_param(
        "ssssi",
        $data['availability'],
        $data['location'],
        $data['check_in'],
        $data['check_out'],
        $data['id']
    );

    return $stmt->execute();
}


}
?>