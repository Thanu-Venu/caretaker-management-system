<?php
require_once APPROOT . "/models/HRDashboardModel.php";
class HrController extends Controller
{

    private $userModel;
    private $hrModel;

    private $caretakerModel;
    private $complaintModel;
    private $clientModel;
    private $hrLeaveModel;
    private $notificationModel;
    private $hrLogsModel;

    public function __construct()
    {
        // Load caretaker model once
        $this->caretakerModel = $this->model('CaretakerModel');
        $this->complaintModel = $this->model('ComplaintModel');
        $this->userModel = $this->model('UserModel');
        $this->clientModel = $this->model('ClientModel');
        $this->hrLeaveModel = $this->model('HRLeaveModel');
        $this->notificationModel = $this->model('NotificationModel');
        $this->hrLogsModel = $this->model('HRLogsModel');


        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Manager') {
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
    public function hr_dashboard()
    {
        $dash = new HRDashboardModel();

        $data = [
            'totalCaretakers' => $dash->totalCaretakers(),
            'activeServices'  => $dash->activeServicesToday(),
            'pendingLeave'    => $dash->pendingLeaveRequests(),
            'pendingRequests' => $dash->pendingClientRequests(),
            'recentLeaves'    => $dash->recentLeaveRequests(5),
            'recentComplaints' => $dash->recentComplaints(5),
            'recentBookings'  => $dash->recentClientRequests(5)
        ];

        $this->view('hr/hr_dashboard', $data);
    }


    public function hr_complaint()
    {

        // caretaker complaints (from ct_complaints table)
        $ctComplaints = $this->complaintModel->getCaretakerComplaints();

        // client complaints (from complaints table)
        $clientComplaints = $this->complaintModel->getAllComplaints();

        $this->view("hr/hr_complaint", [
            'ct_complaints' => $ctComplaints,
            'complaints'    => $clientComplaints
        ]);
    }



    public function hr_addct()
    {
        $caretakers = $this->caretakerModel->getCaretakers(); // ✅ use the initialized property
        $this->view("hr/hr_addct", ['caretakers' => $caretakers]);
    }

    public function hr_managect()
    {
        $this->view("hr/hr_managect");
    }

    public function hr_logs()
    {
        $perPage = 10;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $currentUserId = (int)($_SESSION['user']['id'] ?? 0);

        $totalLogs = $this->hrLogsModel->getTotalLogsByUser($currentUserId);
        $totalPages = max(1, (int)ceil($totalLogs / $perPage));

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $perPage;
        $logs = $this->hrLogsModel->getLogsPaginatedByUser($currentUserId, $perPage, $offset);

        $this->view("hr/hr_logs", [
            'logs' => $logs,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ]);
    }

    private function logHrAction($action, $section)
    {
        $userId = (int)($_SESSION['user']['id'] ?? 0);
        if ($userId <= 0) return;

        $this->hrLogsModel->log([
            'user_id' => $userId,
            'username' => $_SESSION['user']['username'] ?? 'unknown',
            'role' => 'Manager',
            'action' => $action,
            'section' => $section
        ]);
    }

    public function hr_leave()
    {
        $leaves = $this->hrLeaveModel->getAllLeaves();
        $this->view("hr/hr_leave", ['leaves' => $leaves]);
    }

    public function update_leave_status($id, $status)
    {
        $this->hrLeaveModel->updateLeaveStatus($id, $status); // update in DB
        $this->logHrAction("Updated leave status to {$status} (Leave ID: {$id})", 'Leaves');
        header('Location: ' . URLROOT . '/hr/hr_leave'); // redirect back to admin leave page
        exit();
    }

    public function hr_schedule()
    {
        $this->view("hr/hr_schedule");
    }

    private function respondJson($payload, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    public function scheduleMonthAggregates()
    {
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        if (!$start || !$end) {
            $this->respondJson(['success' => false, 'message' => 'Missing start/end parameters'], 400);
        }

        $scheduleModel = $this->model('HRScheduleModel');
        $data = $scheduleModel->getCalendarMonthAggregates($start, $end);

        if (empty($data['start']) || empty($data['end'])) {
            $this->respondJson(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
        }

        $this->respondJson([
            'success' => true,
            'data' => $data,
            'schema_assumptions' => $scheduleModel->getSchemaAssumptions()
        ]);
    }

    public function scheduleDayDetails()
    {
        $date = $_GET['date'] ?? null;
        if (!$date) {
            $this->respondJson(['success' => false, 'message' => 'Missing date parameter'], 400);
        }

        $scheduleModel = $this->model('HRScheduleModel');
        $details = $scheduleModel->getDayDetails($date);

        if (!$details) {
            $this->respondJson(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
        }

        $this->respondJson([
            'success' => true,
            'data' => $details
        ]);
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

    public function requestAdvancePayment()
    {

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

        $this->logHrAction("Requested advance payment for Booking #{$booking_id}", 'Pending Requests');

        // 3️⃣ Redirect HR
        header("Location: " . URLROOT . "/hr/hr_pending_request");
        exit;
    }
    public function updateComplaintStatus()
    {
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
        $this->logHrAction("Updated complaint status to {$status} (Complaint ID: {$complaintId})", 'Complaints');

        // Redirect back to the pending requests page
        header("Location: " . URLROOT . "/hr/hr_complaint");
        exit;
    }


    public function hr_feedback()
    {
        $this->view("hr/hr_feedback");
    }

    public function hr_reports()
    {
        $this->view("hr/hr_reports");
    }

    public function hr_settings()
    {
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



    public function hr_announcement()
    {
        $announcementModel = $this->model('AnnouncementModel');
        $announcements = $announcementModel->getUserAnnouncements();

        $this->view("hr/hr_announcement", $announcements);
    }






    /* ================= VIEW PENDING PAYMENTS ================= */
    public function pendingPayments()
    {
        $clientModel = $this->model('ClientModel');
        $pendingPayments = $clientModel->getPendingPayments();

        $this->view('hr/hr_pendingPayments', ['payments' => $pendingPayments]);
    }

    public function paymentMonitor()
    {
        $allowedRecurringStatuses = ['all', 'pending', 'paid', 'overdue', 'cancelled'];
        $allowedPaymentStatuses = ['all', 'pending', 'approved', 'rejected'];

        $filters = [
            'client' => trim((string)($_GET['client'] ?? '')),
            'recurring_status' => (string)($_GET['recurring_status'] ?? 'all'),
            'payment_status' => (string)($_GET['payment_status'] ?? 'all'),
            'from_date' => trim((string)($_GET['from_date'] ?? '')),
            'to_date' => trim((string)($_GET['to_date'] ?? '')),
        ];

        if (!in_array($filters['recurring_status'], $allowedRecurringStatuses, true)) {
            $filters['recurring_status'] = 'all';
        }

        if (!in_array($filters['payment_status'], $allowedPaymentStatuses, true)) {
            $filters['payment_status'] = 'all';
        }

        $data = [
            'summary' => $this->hrModel->getPaymentSummary(),
            'recurring' => $this->hrModel->getRecurringPaymentOverview(100, $filters),
            'recent' => $this->hrModel->getRecentPaymentTimeline(100, $filters),
            'filters' => $filters
        ];

        $this->view('hr/hr_paymentMonitor', $data);
    }

    /* ================= APPROVE PAYMENT ================= */
    public function approvePayment()
    {
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

        if ($payment['payment_type'] === 'advance') {
            // Update booking status to Accepted
            $clientModel->updateBookingStatus($payment['booking_id'], 'Accepted');

            // Set advance_paid_date and create recurring payments if needed
            $bookingDetails = $clientModel->getBookingById($payment['booking_id']);
            if ($bookingDetails) {
                $clientModel->updateBookingAdvancePaidDate($payment['booking_id']);

                require_once APPROOT . '/controllers/PaymentController.php';
                PaymentController::createRecurringPayments($payment['booking_id'], $bookingDetails);
            }

            // Send notification to caretaker for first approval.
            $notifModel = $this->model('NotificationModel');
            $notifModel->addNotification(
                $payment['caretaker_id'],
                'caretaker',
                'Booking Accepted',
                "Booking #" . $payment['booking_id'] . " has been accepted after payment approval. Client: " . $payment['client_name'] . ". You can now view the booking details in your Bookings page.",
                URLROOT . "/caretaker/ct_booking?booking_id=" . $payment['booking_id'] . "&tab=upcoming"
            );
        } else {
            // For recurring/follow-up payments, mark matching recurring payment as paid.
            require_once APPROOT . '/core/RecurringPaymentService.php';
            $recurringService = new RecurringPaymentService();
            $recurringService->markRecurringPaymentAsPaidByDetails(
                (int)$payment['booking_id'],
                (string)$payment['due_date'],
                (float)$payment['amount'],
                (int)$paymentId
            );
        }

        $this->logHrAction("Approved payment #{$paymentId} for Booking #{$payment['booking_id']}", 'Payments');

        $_SESSION['success'] = "Payment approved successfully! Caretaker notified.";
        header("Location: " . URLROOT . "/hr/pendingPayments");
        exit;
    }

    // ================= CHANGE REQUESTS =================
    public function changeRequests()
    {
        $crModel = $this->model('ChangeRequestModel');
        $pendingRequests = $crModel->getPendingRequests();
        $completedRequests = $crModel->getCompletedRequests();
        $this->view('hr/changeRequests', [
            'pending_requests' => $pendingRequests,
            'completed_requests' => $completedRequests
        ]);
    }

    // ================= RESCHEDULE REQUESTS =================
    public function rescheduleRequests()
    {
        $rrModel = $this->model('RescheduleRequestModel');
        $pendingRequests = $rrModel->getPendingRequests();
        $completedRequests = $rrModel->getCompletedRequests();
        $this->view('hr/rescheduleRequests', [
            'pending_requests' => $pendingRequests,
            'completed_requests' => $completedRequests
        ]);
    }

    public function approveReschedule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        $hrNote = trim($_POST['hr_note'] ?? '');

        if (!$requestId) {
            $_SESSION['error'] = "Invalid request ID.";
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        $rrModel = $this->model('RescheduleRequestModel');

        // Validate request exists and is pending
        $request = $rrModel->getRequestById($requestId);
        if (!$request) {
            $_SESSION['error'] = "Reschedule request not found.";
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        if ($request['status'] !== 'pending') {
            $_SESSION['error'] = "This request has already been processed.";
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        // Use transaction-based approval from model
        $bookingId = $rrModel->approveRequest($requestId, $hrNote);

        if ($bookingId) {
            // Revert booking status to 'Requested' so client can continue normal workflow (payment, etc.)
            $this->model('ClientModel')->updateBookingStatus($bookingId, 'Requested');

            // Send notifications to client and caretaker
            require_once APPROOT . '/models/NotificationModel.php';
            $notif = new NotificationModel();

            // Fetch updated booking info
            $booking = $this->model('ClientModel')->getBookingById($bookingId);

            if ($booking) {
                $clientId = $booking['client_id'];
                $caretakerId = $booking['caretaker_id'];

                $msgClient = "Your reschedule request has been approved! Booking #{$bookingId} is now scheduled for {$booking['booking_date']} at {$booking['preferred_time']}. Please complete any pending payment steps.";
                $msgCaretaker = "Booking #{$bookingId} assigned to you has been rescheduled to {$booking['booking_date']} at {$booking['preferred_time']}.";

                $notif->addNotification(
                    $clientId,
                    'client',
                    'Reschedule Approved',
                    $msgClient,
                    URLROOT . "/client/c_upcomingBookings"
                );

                $notif->addNotification(
                    $caretakerId,
                    'caretaker',
                    'Reschedule Approved',
                    $msgCaretaker,
                    URLROOT . "/caretaker/ct_ongoingBookings"
                );
            }

            $_SESSION['success'] = "Reschedule request approved successfully.";
            $this->logHrAction("Approved reschedule request #{$requestId} (Booking #{$bookingId})", 'Reschedule Requests');
        } else {
            $_SESSION['error'] = "Failed to approve reschedule request. Please try again.";
        }

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
        $hrNote = $_POST['hr_note'] ?? '';
        if (!$requestId) {
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        $rrModel = $this->model('RescheduleRequestModel');
        // determine associated booking id before changing status
        $reqDetails = $rrModel->getRequestById($requestId);
        $rrModel->rejectRequest($requestId, $hrNote);

        // Revert booking status to 'Requested' so client can continue normal workflow
        if ($reqDetails && isset($reqDetails['booking_id'])) {
            $this->model('ClientModel')->updateBookingStatus($reqDetails['booking_id'], 'Requested');
            // notify client of rejection
            require_once APPROOT . '/models/NotificationModel.php';
            $notif = new NotificationModel();
            $cid = $reqDetails['client_id'];
            $msg = "Your reschedule request for booking #{$reqDetails['booking_id']} has been rejected. The booking remains on the original date. Please check for any HR notes.";
            $notif->addNotification($cid, 'client', 'Reschedule Rejected', $msg, URLROOT . "/client/c_upcomingBookings");
        }

        if ($reqDetails && isset($reqDetails['booking_id'])) {
            $this->logHrAction("Rejected reschedule request #{$requestId} (Booking #{$reqDetails['booking_id']})", 'Reschedule Requests');
        } else {
            $this->logHrAction("Rejected reschedule request #{$requestId}", 'Reschedule Requests');
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
            $this->logHrAction("Approved caregiver change request #{$requestId} (Booking #{$bookingId})", 'Change Requests');
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
        $hrNote = trim($_POST['hr_note'] ?? '');
        if ($requestId <= 0) {
            header("Location: " . URLROOT . "/hr/changeRequests");
            exit;
        }

        $crModel = $this->model('ChangeRequestModel');
        $result = $crModel->rejectRequest($requestId, $hrNote);

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
            $this->logHrAction("Rejected caregiver change request #{$requestId} (Booking #{$bookingId})", 'Change Requests');
        } else {
            $_SESSION['error'] = "Failed to reject change request.";
        }

        header("Location: " . URLROOT . "/hr/changeRequests");
        exit;
    }

    /* ================= REJECT PAYMENT ================= */
    public function rejectPayment()
    {
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

        // Revert only advance-payment bookings back to Requested.
        if ($payment['payment_type'] === 'advance') {
            $clientModel->updateBookingStatus($payment['booking_id'], 'Requested');
        }

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

        $this->logHrAction("Rejected payment #{$paymentId} for Booking #{$payment['booking_id']}", 'Payments');

        $_SESSION['success'] = "Payment rejected. Client notified.";
        header("Location: " . URLROOT . "/hr/pendingPayments");
        exit;
    }
}
