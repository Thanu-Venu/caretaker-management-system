<?php
class Staff {
    private $conn;
    private $table = "staff";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login staff
    public function login($email, $password) {
        // Use ? placeholder for mysqli
        $stmt = $this->conn->prepare("SELECT * FROM staff WHERE email = ? LIMIT 1");
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        // Bind parameter (s = string)
        $stmt->bind_param("s", $email);

        // Execute statement
        $stmt->execute();

        // Get result
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();

        $stmt->close();

        // Verify password
        if ($staff && password_verify($password, $staff['password'])) {
            return $staff;
        }

        return false;
    }
}
?>
