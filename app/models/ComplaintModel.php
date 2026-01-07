<?php
class ComplaintModel {
    private $db;

    public function __construct() {
        $this->db = new mysqli("localhost", "root", "", "smartcare");
        if($this->db->connect_errno){
            die("Failed to connect to MySQL: " . $this->db->connect_error);
        }
    }

    // Insert new complaint
    public function createComplaint($client_name, $caretaker_name, $category, $details) {
        $status = "Open"; // default
        $stmt = $this->db->prepare("
            INSERT INTO complaints (client_name, caretaker_name, category, details, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        if(!$stmt){
            die("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("sssss", $client_name, $caretaker_name, $category, $details, $status);
        $result = $stmt->execute();
        if(!$result){
            die("Execute failed: " . $stmt->error);
        }
        return $result;
    }

    public function getAllComplaints() {
        $result = $this->db->query("SELECT * FROM complaints ORDER BY Id DESC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    public function getComplaintById($id) {
    // Ensure $id is an integer for security
    $id = (int)$id;

    // Prepare SQL to select one complaint by its Id
  // Use SELECT * to avoid name-mismatch problems
    $stmt = $this->db->prepare("SELECT * FROM `complaints` WHERE `Id` = ?");
    if (!$stmt) {
        die("Prepare failed: " . $this->db->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}


    // Update complaint by Id
public function updateComplaint($id, $client_name, $caretaker_name, $category, $details, $status) {
    $stmt = $this->db->prepare("
        UPDATE `complaints`
        SET `client_name` = ?, `caretaker_name` = ?, `category` = ?, `details` = ?, `status` = ?
        WHERE `Id` = ?
    ");
    if(!$stmt) {
        die("Prepare failed: " . $this->db->error);
    }
    $stmt->bind_param("sssssi", $client_name, $caretaker_name, $category, $details, $status, $id);
    $ok = $stmt->execute();
    if(!$ok) {
        die("Execute failed: " . $stmt->error);
    }
    return $ok;
}

public function deleteComplaintById($id)
{
    $stmt = $this->db->prepare("DELETE FROM complaints WHERE Id = ?");
    $stmt->bind_param("i", var: $id);
    return $stmt->execute();
}


public function getComplaintsByClient($client_name)
{
    $query = "SELECT * FROM complaints WHERE client_name = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param(types: "s", $client_name);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

public function updateComplaintByClient($id, $details)
{
    $query = "UPDATE complaints SET details = ? WHERE Id = ? AND status != 'Approved'";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("si", $details, $id);
    return $stmt->execute();
}

public function deleteComplaintByClient($id)
{
    $query = "DELETE FROM complaints WHERE Id = ? AND status != 'Approved'";
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

}
?>