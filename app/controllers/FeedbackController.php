<?php

class FeedbackController extends Controller {

    private $feedbackModel;

    public function __construct() {
        $this->feedbackModel = new FeedbackModel();
    }

    // -------------------------------
    // ADMIN + HR + CARETAKER
    // -------------------------------
    
    // Admin list
    public function adminList() {
        $data = $this->feedbackModel->getAll();
        $this->view("admin/feedback_list", ['feedbacks' => $data]);
    }

    // HR list
    public function hrList() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hr') {
            die('Forbidden');
        }
        $data = $this->feedbackModel->getAll();
        $this->view("hr/hr_feedback", ['feedbacks' => $data]);
    }

    // Caretaker list
    public function caretakerList($caretaker_id) {
        $data = $this->feedbackModel->getByCaretaker($caretaker_id);
        $this->view("caretaker/feedback_list", ['feedbacks' => $data]);
    }

    // -------------------------------
    // CLIENT CRUD
    // -------------------------------

    // Client list
    public function index($client_id) {
        $feedbacks = $this->feedbackModel->getByClient($client_id);
        $this->view("client/feedback_list", ["feedbacks" => $feedbacks]);
    }

    // Add
    public function create() {
        $this->view("client/feedback_add");
    }

    // Store
    public function store() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'client_id' => $_POST['client_id'],
                'caretaker_id' => $_POST['caretaker_id'],
                'service' => $_POST['service'] ?? null,
                'rating' => $_POST['rating'],
                'comment' => $_POST['comment'],
            ];

            $this->feedbackModel->create($data);

            header("Location: " . URLROOT . "/feedback/index/" . $data['client_id']);
            exit;
        }
    }

    // Edit
    public function edit($id) {
        $fb = $this->feedbackModel->getById($id);
        $this->view("client/feedback_edit", ["feedback" => $fb]);
    }

    // Update
    public function update($id) {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'rating' => $_POST['rating'],
                'comment' => $_POST['comment']
            ];

            $this->feedbackModel->update($id, $data);

            header("Location: " . URLROOT . "/feedback/index/" . $_SESSION['client_id']);
            exit;
        }
    }

    // Delete
    public function delete($id) {

        $this->feedbackModel->delete($id);

        header("Location: " . URLROOT . "/feedback/index/" . $_SESSION['client_id']);
        exit;
    }
}
