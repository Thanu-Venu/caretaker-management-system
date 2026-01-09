<?php
class Caretaker {
    private $conn;
    private $table = "caretakers";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login staff
    public function login($email, $password) {
        // Use ? placeholder for mysqli
        $stmt = $this->conn->prepare("SELECT * FROM caretakers WHERE email = ? LIMIT 1");
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        // Bind parameter (s = string)
        $stmt->bind_param("s", $email);

        // Execute statement
        $stmt->execute();

        // Get result
        $result = $stmt->get_result();
        $caretaker = $result->fetch_assoc();

        $stmt->close();

        // Verify password
        if ($caretaker && password_verify($password, $caretaker['password'])) {
            return $caretaker;
        }

        return false;
    }
}
?>
