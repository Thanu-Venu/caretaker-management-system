<?php
class HrController extends Controller {

    private $userModel;
    private $hrModel;

    private $caretakerModel;
    private $complaintModel; 
    

    private $clientModel;
    private $hrLeaveModel;

    public function __construct()
    {
        // Load caretaker model once
        $this->caretakerModel = $this->model('CaretakerModel');

        $this->userModel = $this->model('UserModel');
        $this->clientModel = $this->model('ClientModel');
        $this->hrLeaveModel = $this->model('HRLeaveModel');
    

    
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
        $caretakers = $this->caretakerModel->getCaretakers(); // ✅ use the initialized property
        $this->view("hr/hr_addct", ['caretakers' => $caretakers]);
    }    

    public function hr_managect() {
        $this->view("hr/hr_managect");
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
    $perPage = 10;

    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $status = $_GET['status'] ?? 'All';

    $total = $this->hrModel->countBookingsByStatus($status);
    $totalPages = max(1, (int)ceil($total / $perPage));

    if ($page > $totalPages) $page = $totalPages;

    $offset = ($page - 1) * $perPage;

    $bookings = $this->hrModel->getBookingsPaginatedByStatus(
        $perPage,
        $offset,
        $status
    );

    $this->view('hr/hr_pending_request', [
        'bookings' => $bookings,
        'page' => $page,
        'totalPages' => $totalPages,
        'status' => $status
    ]);
}



public function updateBookingStatus()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/hr/hr_pending_request");
        exit;
    }

    $bookingId = (int)($_POST['booking_id'] ?? 0);
    $action    = $_POST['action'] ?? '';

    if (!$bookingId || !$action) {
        header("Location: " . URLROOT . "/hr/hr_pending_request");
        exit;
    }

    // HR user id
    $hrId = (int)($_SESSION['user']['id'] ?? 0);

    if ($action === 'accept') {

        // fee from form (default 0)
        $customFee = (float)($_POST['customization_fee'] ?? 0);

        // OPTIONAL: if customization text exists => require fee
        $booking = $this->hrModel->getBookingDetailsForApproval($bookingId);
        $customText = trim($booking['customization'] ?? '');
        if ($customText !== '' && $customFee <= 0) {
            die("Customization fee is required for this booking.");
        }

        $ok = $this->hrModel->approveBookingAddCustomizationFee($bookingId, $customFee, $hrId);

        if (!$ok) die("Approve failed.");

        // ✅ notify client
        $clientId = (int)$booking['client_id'];
        $newTotal = $this->hrModel->getBookingTotal($bookingId);

        $msg = "Your booking #$bookingId was approved.";
        if ($customFee > 0) {
            $msg .= " Customization fee: Rs." . number_format($customFee, 2) .
                    ". New total: Rs." . number_format($newTotal, 2) . ". Make your payment to confirm the booking.";
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
        } else {
            $msg .= " Total: Rs." . number_format($newTotal, 2) . ".";
        }

        $this->hrModel->notifyUser(
            $clientId,
            "client",
            "Booking Approved",
            $msg,
            URLROOT . "/client/c_upcomingBookings"
        );

    } else {
        $this->hrModel->updateBookingStatus($bookingId, "Rejected");
    }

    header("Location: " . URLROOT . "/hr/hr_pending_request");
    exit;
}
public function updateComplaintStatus() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/hr/hr_complaint");
        exit;
    }

    $complaintId = $_POST['complaint_id'] ?? null;
    $action    = $_POST['action'] ?? null;

    if (!$complaintId || !$action) {
        header("Location: " . URLROOT . "/hr/hr_complaint");
        exit;
    }

    $status = ($action === 'accept') ? 'Approved' : 'Rejected';

    // Call the model to update the complaint status
    $this->hrModel->updateComplaintStatus($complaintId, $status);

    // Redirect back to the pending requests page
    header("Location: " . URLROOT . "/hr/hr_complaint");
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