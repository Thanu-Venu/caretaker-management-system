<?php

class FeedbackModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all feedback (Admin + HR)
    public function getAll() {
        $sql = "SELECT feedback.*, 
                       clients.name AS client_name, 
                       caretakers.name AS caretaker_name
                FROM feedback
                JOIN clients ON feedback.client_id = clients.id
                JOIN caretakers ON feedback.caretaker_id = caretakers.id
                ORDER BY feedback.created_at DESC";

        $result = $this->db->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Caretaker-specific feedback list
    public function getByCaretaker($caretaker_id) {
        $sql = "SELECT feedback.*, clients.name AS client_name
                FROM feedback
                JOIN clients ON feedback.client_id = clients.id
                WHERE caretaker_id = $caretaker_id
                ORDER BY created_at DESC";

        return $this->db->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Get client feedback list
    public function getByClient($client_id) {
        $sql = "SELECT feedback.*, 
                       caretakers.name AS caretaker_name
                FROM feedback
                JOIN caretakers ON feedback.caretaker_id = caretakers.id
                WHERE client_id = $client_id
                ORDER BY created_at DESC";

        return $this->db->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Get one feedback
    public function getById($id) {
        $sql = "SELECT * FROM feedback WHERE id = $id";
        return $this->db->conn->query($sql)->fetch_assoc();
    }

    // Insert new feedback
    public function create($data) {
        $client_id = $data['client_id'];
        $caretaker_id = $data['caretaker_id'];
        $service = $data['service'];
        $rating = $data['rating'];
        $comment = $data['comment'];

        $sql = "INSERT INTO feedback (client_id, caretaker_id, service, rating, comment)
                VALUES ('$client_id', '$caretaker_id', '$service', '$rating', '$comment')";

        return $this->db->conn->query($sql);
    }

    // Update
    public function update($id, $data) {
        $rating = $data['rating'];
        $comment = $data['comment'];

        $sql = "UPDATE feedback 
                SET rating='$rating', comment='$comment'
                WHERE id = '$id'";

        return $this->db->conn->query($sql);
    }

    // Delete
    public function delete($id) {
        $sql = "DELETE FROM feedback WHERE id = $id";
        return $this->db->conn->query($sql);
    }
}
