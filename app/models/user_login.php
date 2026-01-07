<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login staff
    public function login($email, $password) {
        // Use ? placeholder for mysqli
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        // Bind parameter (s = string)
        $stmt->bind_param("s", $email);

        // Execute statement
        $stmt->execute();

        // Get result
        $result = $stmt->get_result();
        $users = $result->fetch_assoc();

        $stmt->close();

        // Verify password
        if ($users && password_verify($password, $users['password'])) {
            return $users;
        }

        return false;
    }
}
?>