<?php
require_once APPROOT . '/core/Database.php';

class UserModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn; // mysqli object
    }

    // 🔹 Get all users
    public function getAllUsers() {
        $result = $this->conn->query("SELECT * FROM users ORDER BY id DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // 🔹 Add user
    public function addUser($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data['username'], $data['email'], $hashedPassword, $data['role'], $data['status']);
        return $stmt->execute();
    }

    // 🔹 Get user by ID
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // 🔹 Update user
    public function updateUser($id, $data) {
        $stmt = $this->conn->prepare("UPDATE users SET username=?, email=?, role=?, status=? WHERE id=?");
        $stmt->bind_param("ssssi", $data['username'], $data['email'], $data['role'], $data['status'], $id);
        return $stmt->execute();
    }

    // 🔹 Delete user
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function login($email, $password) {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
    }
    // In UserModel
public function updateUserProfile($id, $data) {
    $stmt = $this->conn->prepare("UPDATE users SET username=?, phone=?, profile_pic=COALESCE(?, profile_pic) WHERE id=?");
    $stmt->bind_param("sssi", $data['username'], $data['phone'], $data['profile_pic'], $id);
    return $stmt->execute();
}

public function updatePassword($id, $hashedPassword) {
    $stmt = $this->conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $hashedPassword, $id);
    return $stmt->execute();
}



}
?>
