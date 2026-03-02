<?php
class HrController extends Controller {

    private $userModel;
    private $hrModel;

    private $caretakerModel;
    private $complaintModel; 
    

    private $clientModel;
    private $hrLeaveModel;
    private $notificationModel;

    public function __construct()
    {
        // Load caretaker model once
        $this->caretakerModel = $this->model('CaretakerModel');

        $this->userModel = $this->model('UserModel');
        $this->clientModel = $this->model('ClientModel');
        $this->hrLeaveModel = $this->model('HRLeaveModel');
        $this->notificationModel = $this->model('NotificationModel');
    

    
    if (session_status() === PHP_SESSION_NONE) session_start();

     if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role']!=='Manager'){
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
        header("Location: " . URLROOT . "/HRCaretakerCRUD/list?page=1");
        exit;
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

public function requestAdvancePayment() {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/hr/hr_pending_request");
        exit;
    }

    $booking_id = $_POST['booking_id'] ?? null;
    $client_id  = $_POST['client_id'] ?? null;

    if (!$booking_id || !$client_id) {
        $_SESSION['error'] = "Invalid booking or client information.";
        header("Location: " . URLROOT . "/hr/hr_pending_request");
        exit;
    }

    // 1️⃣ Update booking status
    $updated = $this->hrModel->requestAdvancePayment($booking_id);

    if ($updated) {
        // 2️⃣ Notify client with link to payment page
        $this->notificationModel->addNotification(
            $client_id,
            'client',
            "Advance Payment Required",
            "Please pay the advance payment to proceed with your booking.",
            URLROOT . "/client/c_makePayment?booking_id=" . $booking_id
        );
        $_SESSION['success'] = "Advance payment requested successfully!";
    } else {
        $_SESSION['error'] = "Failed to request advance payment. Please try again.";
    }

    // 3️⃣ Redirect HR
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

    




/* ================= VIEW PENDING PAYMENTS ================= */
public function pendingPayments() {
    $clientModel = $this->model('ClientModel');
    $pendingPayments = $clientModel->getPendingPayments();
    
    $this->view('hr/hr_pendingPayments', ['payments' => $pendingPayments]);
}

/* ================= APPROVE PAYMENT ================= */
public function approvePayment() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/hr/pendingPayments");
        exit;
    }

    $paymentId = $_POST['payment_id'] ?? null;
    if (!$paymentId) {
        $_SESSION['error'] = "Invalid payment ID";
        header("Location: " . URLROOT . "/hr/pendingPayments");
        exit;
    }

    $clientModel = $this->model('ClientModel');
    
    // Get payment details
    $payment = $clientModel->getPaymentById($paymentId);
    
    if (!$payment) {
        $_SESSION['error'] = "Payment not found";
        header("Location: " . URLROOT . "/hr/pendingPayments");
        exit;
    }

    // Update payment status to approved
    $clientModel->updatePaymentStatus($paymentId, 'approved');

    // Update booking status to Accepted
    $clientModel->updateBookingStatus($payment['booking_id'], 'Accepted');

    // Send notification to caretaker
    $notifModel = $this->model('NotificationModel');
    $notifModel->addNotification(
        $payment['caretaker_id'],
        'caretaker',
        'Booking Accepted',
        "Booking #" . $payment['booking_id'] . " has been accepted after payment approval. Client: " . $payment['client_name'] . ". You can now view the booking details in your Bookings page.",
        URLROOT . "/caretaker/ct_booking?booking_id=" . $payment['booking_id'] . "&tab=upcoming"
    );

    $_SESSION['success'] = "Payment approved successfully! Caretaker notified.";
    header("Location: " . URLROOT . "/hr/pendingPayments");
    exit;
}

/* ================= REJECT PAYMENT ================= */
public function rejectPayment() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/hr/pendingPayments");
        exit;
    }

    $paymentId = $_POST['payment_id'] ?? null;
    $reason = $_POST['reason'] ?? '';
    
    if (!$paymentId) {
        $_SESSION['error'] = "Invalid payment ID";
        header("Location: " . URLROOT . "/hr/pendingPayments");
        exit;
    }

    $clientModel = $this->model('ClientModel');
    
    // Get payment details
    $payment = $clientModel->getPaymentById($paymentId);
    
    if (!$payment) {
        $_SESSION['error'] = "Payment not found";
        header("Location: " . URLROOT . "/hr/pendingPayments");
        exit;
    }

    // Update payment status to rejected
    $clientModel->updatePaymentStatus($paymentId, 'rejected');

    // Update booking status back to Requested
    $clientModel->updateBookingStatus($payment['booking_id'], 'Requested');

    // Send notification to client
    $notifModel = $this->model('NotificationModel');
    $clientMessage = "Payment for booking #" . $payment['booking_id'] . " has been rejected. Reason: " . $reason . ". Please contact HR for details.";
    $notifModel->addNotification(
        $payment['client_id'],
        'client',
        'Payment Rejected',
        $clientMessage,
        URLROOT . "/client/c_paymentHistory"
    );

    $_SESSION['success'] = "Payment rejected. Client notified.";
    header("Location: " . URLROOT . "/hr/pendingPayments");
    exit;
}






    
}