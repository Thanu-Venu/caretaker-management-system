<?php
class Client {
    private $conn;
    private $table = "users"; // your clients table

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($data) {
        $sql = "INSERT INTO {$this->table} (name, email, phone, password, role) 
                VALUES (?, ?, ?, ?, 'client')";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param("ssss", $data['name'], $data['email'], $data['phone'], $hashedPassword);

        $stmt->execute();
        $stmt->close();
        return true;
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password']) ) {
            return $user;
        }
        return false;

        if (!$staffUser && !$clientUser) {
    // invalid credentials
        $this->view('auth/login', ['error' => 'Invalid email or password']);
        return;
        }

    }
}
?>
