<?php

class FeedbackController extends Controller
{

    private $feedbackModel;
    private $notifModel;


    public function __construct()
    {
        $this->feedbackModel = new FeedbackModel();
        $this->notifModel = new NotificationModel();
    }

    // -------------------------------
    // ADMIN + HR + CARETAKER
    // -------------------------------

    // Admin list
    public function adminList()
    {
        $data = $this->feedbackModel->getAll();
        $this->view("admin/feedback_list", ['feedbacks' => $data]);
    }

    // HR list
    public function hrList()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!AuthSession::hasRole('manager')) {
            die('Forbidden');
        }
        $data = $this->feedbackModel->getAll();
        $this->view("hr/hr_feedback", ['feedbacks' => $data]);
    }

    // Caretaker list
    public function caretakerList($caretaker_id)
    {
        $data = $this->feedbackModel->getByCaretaker($caretaker_id);
        $this->view("caretaker/feedback_list", ['feedbacks' => $data]);
    }

    // -------------------------------
    // CLIENT CRUD
    // -------------------------------

    // Client list
    public function index($client_id = null)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Use session client_id if not provided in URL
        if (!$client_id) {
            $client_id = AuthSession::profileId() ?: null;
        }

        if (!$client_id) {
            die('User not logged in');
        }

        $feedbacks = $this->feedbackModel->getByClient($client_id);
        $this->view("client/c_feedback", ["feedbacks" => $feedbacks]);
    }

    // Add
    public function create()
    {
        $this->view("client/feedback_add");
    }

    // Store
    public function store()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get client ID from session
            if (session_status() === PHP_SESSION_NONE) session_start();
            $client_id = AuthSession::profileId() ?: null;

            if (!$client_id) {
                die('User not logged in');
            }

            $caretaker_id = $_POST['caretaker_id'];

            // Get the most recent booking with this caretaker
            $clientModel = $this->model('ClientModel');
            $booking_id = $clientModel->getRecentBookingWithCaretaker($client_id, $caretaker_id);

            if (!$booking_id) {
                // If no booking found, create feedback without specific booking ID
                // or redirect with error message
                die('No booking found with this caretaker. Please ensure you have completed a service.');
            }

            $data = [
                'booking_id' => $booking_id,
                'client_id' => $client_id,
                'caretaker_id' => $caretaker_id,
                'rating' => $_POST['rating'],
                'feedback' => $_POST['feedback'],
            ];

            $this->feedbackModel->create($data);
            // ✅ Notify ALL admins
            $this->notifModel->notifyAdmins(
                "New Feedback",
                "New feedback received (Client ID: {$data['client_id']}, Caretaker ID: {$data['caretaker_id']}, Rating: {$data['rating']}).",
                URLROOT . "/admin/ad_feedback"
            );

            header("Location: " . URLROOT . "/feedback/index/" . $client_id);
            exit;
        }
    }

    // Edit
    public function edit($id)
    {
        $fb = $this->feedbackModel->getById($id);
        $this->view("client/feedback_edit", ["feedback" => $fb]);
    }

    // Update
    public function update($id)
    {

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
    public function delete($id)
    {

        $this->feedbackModel->delete($id);

        header("Location: " . URLROOT . "/feedback/index/" . $_SESSION['client_id']);
        exit;
    }
}
