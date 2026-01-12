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
        "INSERT INTO caretakers (name, email, phone, service_type, status, password) VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssssss",
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['service_type'],
        $data['status'],
        $hashedPassword
    );

    return $stmt->execute();
 }



    public function updateCaretaker($id, $data) {
        $stmt = $this->conn->prepare("UPDATE caretakers SET name=?,email=?,phone=?,service_type=?,status=? WHERE id=?");
        $stmt->bind_param("sssssi", $data['name'],$data['email'],$data['phone'],$data['service_type'],$data['status'],$id);
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