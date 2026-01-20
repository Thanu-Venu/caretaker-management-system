<?php
class HrController extends Controller {

    private $userModel;
    private $caretakerModel;


        $this->userModel = $this->model('UserModel');
        $this->clientModel = $this->model('ClientModel');
        $this->hrLeaveModel = $this->model('HRLeaveModel');
    

    
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }
    $this->userModel = $this->model('UserModel');
    $this->caretakerModel = $this->model('CaretakerModel');

        

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
        $caretakers = $this->caretakerModel->getCaretakers(); // ✅ use the initialized property
        $this->view("hr/hr_addct", ['caretakers' => $caretakers]);
    }    

    public function hr_managect() {
        $caretakers = $this->caretakerModel->getCaretakersForHR();

        $data = [
            'caretakers' => $caretakers
        ];

        $this->view("hr/hr_managect", $data);
    }

    public function updateAvailability() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $_POST['id'],
                'availability' => $_POST['availability'],
                'location' => $_POST['location'],
                'check_in' => $_POST['check_in'],
                'check_out' => $_POST['check_out']
            ];

            $this->caretakerModel->updateAvailability($data);
            header("Location: index.php?url=hr/hr_managect");
            exit;
        }
    }


    public function hr_history() {
        $this->view("hr/hr_history");
    }

    public function hr_leave() {
        $leaves = $this->hrLeaveModel->getAllLeaves();
        $this->view("hr/hr_leave", ['leaves' => $leaves]);
    }

    public function update_leave_status($id, $status) {
        $this->hrLeaveModel->updateLeaveStatus($id, $status); // update in DB
        header('Location: ' . URLROOT . '/hr/hr_leave'); // redirect back to admin leave page
        exit();
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
    // Session already started in constructor
    if (!isset($_SESSION['user'])) {
        header("Location: " . URLROOT . "/auth/login");
        exit;
    }

    // Optional: allow only hr role
    if ($_SESSION['user']['role'] !== 'Manager') {
        die("Access denied. Only HR can access this page.");
    }

    // Use session user directly
    $user = $_SESSION['user'];

    $this->view('hr/hr_settings', ['user' => $user]);
}



    public function hr_announcement() {
    $announcementModel = $this->model('AnnouncementModel');
    $announcements = $announcementModel->getUserAnnouncements();

    $this->view("hr/hr_announcement", $announcements);
    }
    
}