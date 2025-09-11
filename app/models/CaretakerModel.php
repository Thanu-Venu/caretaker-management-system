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
    $stmt = $this->conn->prepare("INSERT INTO caretakers (name,email,phone,service_type,status,password) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $data['name'],$data['email'],$data['phone'],$data['service_type'],$data['status'],$hashedPassword);
    return $stmt->execute();
}


    public function updateCaretaker($id, $data) {
        $stmt = $this->conn->prepare("UPDATE caretakers SET name=?,email=?,phone=?,service_type=?,status=? WHERE id=?");
        $stmt->bind_param("sssssi", $data['name'],$data['email'],$data['phone'],$data['service_type'],$data['status'],$id);
        return $stmt->execute();
    }

    public function deleteCaretaker($id) {
        $stmt = $this->conn->prepare("DELETE FROM caretakers WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
