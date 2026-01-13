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

    public function getLeavesByUser($userId) {
    $stmt = $this->conn->prepare("SELECT * FROM leaves WHERE user_id = ? ORDER BY start_date DESC");
    $stmt->bind_param("i", $userId);
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





  public function addComplaint($data) {
    $query = "INSERT INTO ct_complaints 
             (caretaker_id, client_name, service_type, date_of_service, description, status)
              VALUES (?, ?, ?, ?, ?, 'Pending')";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("issss",
        $data['caretaker_id'],
        $data['client_name'],
        $data['service_type'],
        $data['date_of_service'],
        $data['description']
    );

    return $stmt->execute();
}

   public function getAllComplaints() {
    $query = "SELECT client_name, service_type, date_of_service, description, status
              FROM ct_complaints
              ORDER BY id DESC";

    $result = $this->conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

}

?>