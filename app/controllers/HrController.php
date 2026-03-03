<?php
require_once APPROOT . "/models/HRDashboardModel.php";
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
        $this->complaintModel = $this->model('ComplaintModel');
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
    $dash = new HRDashboardModel();

    $data = [
        'totalCaretakers' => $dash->totalCaretakers(),
        'activeServices'  => $dash->activeServicesToday(),
        'pendingLeave'    => $dash->pendingLeaveRequests(),
        'pendingRequests' => $dash->pendingClientRequests(),
        'recentLeaves'    =>$dash->recentLeaveRequests(5),
        'recentComplaints'=>$dash->recentComplaints(5),
        'recentBookings'  =>$dash->recentClientRequests(5)
    ];

    $this->view('hr/hr_dashboard', $data);
}


public function hr_complaint() {

    // caretaker complaints (from ct_complaints table)
    $ctComplaints = $this->complaintModel->getCaretakerComplaints();

    // client complaints (from complaints table)
    $clientComplaints = $this->complaintModel->getAllComplaints();

    $this->view("hr/hr_complaint", [
        'ct_complaints' => $ctComplaints,
        'complaints'    => $clientComplaints
    ]);
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
   $booking = $this->hrModel->getBookingSummary($booking_id);

$details = "";
if ($booking && $updated) {
    $details =
        "Booking #{$booking['booking_id']} | " .
        "Service: {$booking['service_type']} | " .
        "Date: {$booking['booking_date']} | " .
        "Time: {$booking['preferred_time']} | " .
        "Duration: {$booking['duration']} {$booking['basis']}" .
        (!empty($booking['caretaker_name']) ? " | Caregiver: {$booking['caretaker_name']}" : "");
}

$message = "Advance payment is required to proceed.\n" . $details . "\n\nClick to pay now.";

$this->notificationModel->addNotification(
    $client_id,
    'client',
    "Advance Payment Required",
    $message,
    URLROOT . "/client/c_makePayment?booking_id=" . $booking_id
);

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

// ================= CHANGE REQUESTS =================
public function changeRequests()
{
    $crModel = $this->model('ChangeRequestModel');
    $requests = $crModel->getPendingRequests();
    $this->view('hr/changeRequests', ['requests' => $requests]);
}

// ================= RESCHEDULE REQUESTS =================
public function rescheduleRequests()
{
    $rrModel = $this->model('RescheduleRequestModel');
    $requests = $rrModel->getPendingRequests();
    $this->view('hr/rescheduleRequests', ['requests' => $requests]);
}

public function approveReschedule()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/hr/rescheduleRequests");
        exit;
    }
    $requestId = $_POST['request_id'] ?? null;
    if (!$requestId) {
        header("Location: " . URLROOT . "/hr/rescheduleRequests");
        exit;
    }

    $rrModel = $this->model('RescheduleRequestModel');
    $bookingId = $rrModel->approveRequest($requestId);

    if ($bookingId) {
        // after updating booking we may want to keep status accepted
        $this->model('ClientModel')->updateBookingStatus($bookingId, 'Accepted');

        // notify client and caretaker about the change
        require_once APPROOT . '/models/NotificationModel.php';
        $notif = new NotificationModel();
        // fetch booking info for names
        $booking = $this->model('ClientModel')->getBookingById($bookingId);
        if ($booking) {
            $clientId = $booking['client_id'];
            $caretakerId = $booking['caretaker_id'];
            $msgClient = "Your booking #{$bookingId} has been rescheduled to {$booking['booking_date']} ({$booking['preferred_time']}).";
            $msgCt = "Booking #{$bookingId} assigned to you has been rescheduled to {$booking['booking_date']} ({$booking['preferred_time']}).";
            $notif->addNotification($clientId, 'client', 'Reschedule Approved', $msgClient, URLROOT . "/client/c_upcomingBookings");
            $notif->addNotification($caretakerId, 'caretaker', 'Reschedule Approved', $msgCt, URLROOT . "/caretaker/ct_ongoingBookings");
        }
    }

    $_SESSION['success'] = "Reschedule request approved.";
    header("Location: " . URLROOT . "/hr/rescheduleRequests");
    exit;
}

public function rejectReschedule()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/hr/rescheduleRequests");
        exit;
    }
    $requestId = $_POST['request_id'] ?? null;
    if (!$requestId) {
        header("Location: " . URLROOT . "/hr/rescheduleRequests");
        exit;
    }

    $rrModel = $this->model('RescheduleRequestModel');
    // determine associated booking id before changing status
    $reqDetails = $rrModel->getRequestById($requestId);
    $rrModel->rejectRequest($requestId);

    // revert booking status so client can continue using it
    if ($reqDetails && isset($reqDetails['booking_id'])) {
        $this->model('ClientModel')->updateBookingStatus($reqDetails['booking_id'], 'Accepted');
        // notify client of rejection
        require_once APPROOT . '/models/NotificationModel.php';
        $notif = new NotificationModel();
        $cid = $reqDetails['client_id'];
        $msg = "Your reschedule request for booking #{$reqDetails['booking_id']} has been rejected.";
        $notif->addNotification($cid, 'client', 'Reschedule Rejected', $msg, URLROOT . "/client/c_upcomingBookings");
    }

    $_SESSION['success'] = "Reschedule request rejected.";
    header("Location: " . URLROOT . "/hr/rescheduleRequests");
    exit;
}

    public function approveChange()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/hr/changeRequests");
            exit;
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        $hrNote = trim($_POST['hr_note'] ?? '');

        if ($requestId <= 0) {
            header("Location: " . URLROOT . "/hr/changeRequests");
            exit;
        }

        $crModel = $this->model('ChangeRequestModel');
        $result = $crModel->approveRequest($requestId, $hrNote); // (we will update model to accept note)

        if ($result) {
            // ✅ notify client + (optional) caretaker
            $bookingId = (int)$result['booking_id'];

            $clientModel = $this->model('ClientModel');
            $booking = $clientModel->getBookingById($result['booking_id']);

            if ($booking) {
                $msg = "Your caregiver change request for Booking #{$result['booking_id']} was approved.";
                if ($hrNote !== '') $msg .= "\nHR Note: {$hrNote}";
                $ok1 = $this->notificationModel->addNotification(
                    (int)$booking['client_id'],
                    'client',
                    'Change Request Approved',
                    $msg,
                    URLROOT . "/client/c_ongoingBookings"
                );

                // optional: notify new caretaker
                // ✅ new caretaker notification
                $ok2 = $this->notificationModel->addNotification(
                    (int)$booking['caretaker_id'],
                    'caretaker',
                    'New Booking Assigned',
                    "Booking #{$bookingId} has been assigned to you (caregiver change approved).",
                    URLROOT . "/caretaker/ct_booking?booking_id={$bookingId}&tab=upcoming"
                );

                // ✅ old caretaker notification (optional but useful)
                $oldCt = (int)$result['old_caretaker_id'];
                $newCt = (int)$result['new_caretaker_id'];
                if ($oldCt > 0 && $oldCt !== $newCt) {
                    $this->notificationModel->addNotification(
                        $oldCt,
                        'caretaker',
                        'Booking Reassigned',
                        "Booking #{$bookingId} is no longer assigned to you (caregiver change approved).",
                        URLROOT . "/caretaker/ct_booking?tab=upcoming"
                    );
                }

                // if insert fails, log it
                if (!$ok1 || !$ok2) {
                    error_log("Change approve: notification insert failed for booking {$bookingId}");
                }
            }

            $_SESSION['success'] = "Change request approved.";
        } else {
            $_SESSION['error'] = "Failed to approve change request.";
        }

        header("Location: " . URLROOT . "/hr/changeRequests");
        exit;
    }

public function rejectChange()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/hr/changeRequests");
        exit;
    }
    $requestId = $_POST['request_id'] ?? null;
    $hrNote= trim($_POST['hr_note'] ?? '');
    if ($requestId<=0) {
        header("Location: " . URLROOT . "/hr/changeRequests");
        exit;
    }

    $crModel = $this->model('ChangeRequestModel');
    $result=$crModel->rejectRequest($requestId, $hrNote);

        if ($result) {
            $bookingId = (int)$result['booking_id'];
            $clientId  = (int)$result['client_id'];

            $msg = "Your caregiver change request for Booking #{$bookingId} was REJECTED.";
            if ($hrNote !== '') $msg .= "\nHR Note: {$hrNote}";

            $this->notificationModel->addNotification(
                $clientId,
                'client',
                'Change Request Rejected',
                $msg,
                URLROOT . "/client/c_ongoingBookings"
            );

            $_SESSION['success'] = "Change request rejected.";
        } else {
            $_SESSION['error'] = "Failed to reject change request.";
        }

        header("Location: " . URLROOT . "/hr/changeRequests");
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