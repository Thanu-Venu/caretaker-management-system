<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // 🔹 Get all users
    public function getAllUsers() {
        $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $this->db->resultSet();
    }

    // 🔹 Add user
    public function addUser($data) {
        $this->db->query("INSERT INTO users (username, email, password, role, status) 
                          VALUES (:username, :email, :password, :role, :status)");
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    // 🔹 Get user by ID
    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // 🔹 Update user
    public function updateUser($id, $data) {
        $this->db->query("UPDATE users 
                          SET username = :username, email = :email, role = :role, status = :status 
                          WHERE id = :id");
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // 🔹 Delete user
    public function deleteUser($id) {
        $this->db->query("DELETE FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
?>
