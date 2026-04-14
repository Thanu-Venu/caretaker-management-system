<?php
class ComplaintModel
{
    private $db;

<<<<<<< HEAD
    public function __construct() {
        $this->db = new mysqli("localhost", "root", "Thanuvenu", "smartcare");
        if($this->db->connect_errno){
=======
    public function __construct()
    {
        $this->db = new mysqli("localhost", "root", "Thanuvenu", "smartcare");
        if ($this->db->connect_errno) {
>>>>>>> 62de69af75adfb5fba34de87af93d9cd9d508008
            die("Failed to connect to MySQL: " . $this->db->connect_error);
        }
    }

    // Insert new complaint
    public function createComplaint($client_name, $caretaker_name, $category, $details)
    {
        $status = "Open"; // default
        $stmt = $this->db->prepare("
            INSERT INTO complaints (client_name, caretaker_name, category, details, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("sssss", $client_name, $caretaker_name, $category, $details, $status);
        $result = $stmt->execute();
        if (!$result) {
            die("Execute failed: " . $stmt->error);
        }
        return $result;
    }

    public function getAllComplaints()
    {
        $sql = "SELECT * FROM complaints ORDER BY Id DESC";
        $result = $this->db->query($sql);

        if (!$result) {
            die("SQL Error: " . $this->db->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }



    public function getComplaintById($id)
    {
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
    public function updateComplaint($id, $client_name, $caretaker_name, $category, $details, $status)
    {
        $stmt = $this->db->prepare("
        UPDATE `complaints`
        SET `client_name` = ?, `caretaker_name` = ?, `category` = ?, `details` = ?, `status` = ?
        WHERE `Id` = ?
    ");
        if (!$stmt) {
            die("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("sssssi", $client_name, $caretaker_name, $category, $details, $status, $id);
        $ok = $stmt->execute();
        if (!$ok) {
            die("Execute failed: " . $stmt->error);
        }
        return $ok;
    }

    public function deleteComplaintById($id)
    {
        $stmt = $this->db->prepare("DELETE FROM complaints WHERE Id = ?");
        $stmt->bind_param("i", $id);   // ✅ FIXED
        return $stmt->execute();
    }



    public function getComplaintsByClient($client_name)
    {
        $query = "SELECT * FROM complaints WHERE client_name = ? ORDER BY complaint_date DESC, Id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $client_name); // ✅ FIXED
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

    public function updateComplaintStatus($id, $status)
    {
        $stmt = $this->db->prepare(
            "UPDATE complaints SET status = ? WHERE Id = ?"
        );
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function addNotification($user_name, $message)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO notifications (user_name, message) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $user_name, $message);
        return $stmt->execute();
    }
    // Fetch caretaker complaints// Fetch caretaker complaints for HR
    public function getCaretakerComplaints()
    {
        $sql = "SELECT cc.complaint_id, cc.caretaker_id, cc.client_id, cc.service_type, cc.service_date, cc.description, cc.status,
                   c.name AS client_name, ct.name AS caretaker_name
            FROM ct_complaints cc
            LEFT JOIN clients c ON cc.client_id = c.id
            LEFT JOIN caretakers ct ON cc.caretaker_id = ct.id
            ORDER BY cc.created_at DESC";

        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update status for caretaker complaint
    public function updateCaretakerComplaintStatus($complaint_id, $status)
    {
        $stmt = $this->db->prepare(
            "UPDATE ct_complaints SET status = ? WHERE complaint_id = ?"
        );
        $stmt->bind_param("si", $status, $complaint_id);
        return $stmt->execute();
    }

    // In ComplaintModel.php
    public function getResolvedCaretakerComplaints($caretaker_id)
    {
        $stmt = $this->db->prepare("
        SELECT cc.complaint_id, cc.caretaker_id, cc.client_id, cc.service_type, cc.service_date, cc.description, cc.status,
               c.name AS client_name, ct.name AS caretaker_name
        FROM ct_complaints cc
        LEFT JOIN clients c ON cc.client_id = c.id
        LEFT JOIN caretakers ct ON cc.caretaker_id = ct.id
        WHERE cc.caretaker_id = ? AND cc.status = 'Resolved'
        ORDER BY cc.created_at DESC
    ");
        $stmt->bind_param("i", $caretaker_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Fetch resolved complaints for a specific caretaker

    // Fetch all complaints for a specific caretaker
    public function getComplaintsByCaretaker($caretaker_id)
    {
        $stmt = $this->db->prepare("
        SELECT cc.complaint_id, cc.caretaker_id, cc.client_id, cc.service_type, cc.service_date, cc.description, cc.status,
               c.name AS client_name, ct.name AS caretaker_name
        FROM ct_complaints cc
        LEFT JOIN clients c ON cc.client_id = c.id
        LEFT JOIN caretakers ct ON cc.caretaker_id = ct.id
        WHERE cc.caretaker_id = ?
        ORDER BY cc.created_at DESC
    ");
        $stmt->bind_param("i", $caretaker_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
