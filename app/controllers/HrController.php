<?php
class HrController extends Controller {
    private $userModel;
    private $hrModel;

    public function __construct() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }
    $this->userModel = $this->model('UserModel');
    $this->hrModel   = $this->model('HrModel'); 
        

    // Revalidate caretaker from DB
    $user = $this->userModel->getUserById($_SESSION['user']['id']); // lowercase usage
    if (!$user) {
        session_destroy();
        header("Location: index.php?url=auth/login");
        exit;
    }

    $_SESSION['user'] = $user;
}
    public function hr_dashboard() {
        $this->view("hr/hr_dashboard");
    }
        public function hr_complaint() {
        $this->view("hr/hr_complaint");
    }
    

    public function hr_addct() {
        $this->view("hr/hr_addct");
    }    

    public function hr_managect() {
        $this->view("hr/hr_managect");
    }

    public function hr_history() {
        $this->view("hr/hr_history");
    }

    public function hr_leave() {
        $this->view("hr/hr_leave");
    }
    
    public function hr_schedule() {
        $this->view("hr/hr_schedule");
    }

   public function hr_pending_request()
{
    $hrModel = $this->model('HrModel');
    $bookings = $hrModel->getAllBookings();

    $this->view('hr/hr_pending_request', [
        'bookings' => $bookings
    ]);
}

public function updateBookingStatus() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/hr/hr_pending_request");
        exit;
    }

    $bookingId = $_POST['booking_id'] ?? null;
    $action    = $_POST['action'] ?? null;

    if (!$bookingId || !$action) {
        header("Location: " . URLROOT . "/hr/hr_pending_request");
        exit;
    }

    $status = ($action === 'accept') ? 'Accepted' : 'Rejected';

    // Call the model to update the booking status
    $this->hrModel->updateBookingStatus($bookingId, $status);

    // Redirect back to the pending requests page
    header("Location: " . URLROOT . "/hr/hr_pending_request");
    exit;
}



     public function hr_feedback() {
        $this->view("hr/hr_feedback");
    }

    public function hr_reports() {
        $this->view("hr/hr_reports");
    }

     public function hr_settings() {
        $this->view("hr/hr_settings");
    }
    
}