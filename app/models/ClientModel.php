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
    public function getClientById($id) {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, phone, created_at 
             FROM clients 
             WHERE id=?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

 public function updateClient($id, $data) {
        $stmt = $this->conn->prepare("UPDATE clients SET name=?,email=?,phone=? WHERE id=?");
        $stmt->bind_param("sssi", $data['name'],$data['email'],$data['phone'],$id);
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
}
?>