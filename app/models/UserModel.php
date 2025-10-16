<?php
class User {
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
        $this->db->query("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);

        return $this->db->execute();
    }

    // 🔹 Get user by ID
    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // 🔹 Update user
    public function updateUser($data) {
        $this->db->query("UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id");
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':id', $data['id']);

        return $this->db->execute();
    }

    // 🔹 Delete user
    public function deleteUser($id) {
        $this->db->query("DELETE FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
