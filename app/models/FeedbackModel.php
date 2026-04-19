<?php
require_once APPROOT . '/core/Database.php';

class FeedbackModel {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /* ADMIN / HR – GET ALL FEEDBACKS */
    public function getAll() {
        $sql = "SELECT 
                    f.id,
                    f.booking_id,
                    f.rating,
                    f.feedback,
                    f.created_at,
                    c.name AS client_name,
                    ct.name AS caretaker_name,
                    b.service_type AS service
                FROM feedbacks f
                JOIN clients c ON f.client_id = c.id
                JOIN caretakers ct ON f.caretaker_id = ct.id
                LEFT JOIN bookings b ON f.booking_id = b.id
                ORDER BY f.created_at DESC";

        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* CLIENT – GET OWN FEEDBACKS */
    public function getByClient($clientId) {
        $stmt = $this->conn->prepare(
            "SELECT 
                f.id,
                f.booking_id,
                f.rating,
                f.feedback,
                f.created_at,
                ct.name AS caretaker_name
             FROM feedbacks f
             JOIN caretakers ct ON f.caretaker_id = ct.id
             WHERE f.client_id = ?
             ORDER BY f.created_at DESC"
        );
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* CARETAKER – GET OWN FEEDBACKS */
    public function getByCaretaker($caretakerId) {
        $stmt = $this->conn->prepare(
            "SELECT 
                f.id,
                f.booking_id,
                f.rating,
                f.feedback,
                f.created_at,
                c.name AS client_name
             FROM feedbacks f
             JOIN clients c ON f.client_id = c.id
             WHERE f.caretaker_id = ?
             ORDER BY f.created_at DESC"
        );
        $stmt->bind_param("i", $caretakerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /* GET SINGLE FEEDBACK */
    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM feedbacks WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* CREATE FEEDBACK */
    public function create($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO feedbacks 
            (booking_id, client_id, caretaker_id, rating, feedback)
            VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iiiis",
            $data['booking_id'],
            $data['client_id'],
            $data['caretaker_id'],
            $data['rating'],
            $data['feedback']
        );

        if ($stmt->execute()) {
            return $this->conn->insert_id; // ✅ return feedback ID
        }
        return false;
    }

    /* UPDATE FEEDBACK */
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE feedbacks
             SET rating = ?, feedback = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "isi",
            $data['rating'],
            $data['feedback'],
            $id
        );

        return $stmt->execute();
    }

    /* DELETE FEEDBACK */
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM feedbacks WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* CHECK IF FEEDBACK EXISTS (PER BOOKING) */
    public function feedbackExists($bookingId) {
        $stmt = $this->conn->prepare(
            "SELECT id FROM feedbacks WHERE booking_id = ?"
        );
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
