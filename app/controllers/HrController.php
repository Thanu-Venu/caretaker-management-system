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

        if (!AuthSession::hasRole('manager')) {
            header("Location: index.php?url=auth/login");
            exit;
        }
        $this->userModel = $this->model('UserModel');
        $this->hrModel   = $this->model('HrModel');


        // Revalidate caretaker from DB
        $user = $this->userModel->getUserById(AuthSession::profileId()); // lowercase usage
        if (!$user) {
            session_destroy();
            header("Location: index.php?url=auth/login");
            exit;
        }

        $user = $this->userModel->withDisplayNameForProfile($user);
        $_SESSION['name'] = (string) ($user['name'] ?? ($_SESSION['name'] ?? ''));
        $_SESSION['user'] = $user;
        AuthSession::refreshLegacyUser($user);
    }
    public function hr_dashboard()
    {
        $dash = new HRDashboardModel();

        // Get chart data
        $bookingData = $dash->getBookingSummary();
        $performanceData = $dash->getPerformanceRatings();
        $ratingStats = $dash->getRatingStats();

        // Prepare labels and data for booking chart
        $bookingStatusLabels = [];
        $bookingStatusCounts = [];
        $statusColorMap = [
            'Reschedule_Requested' => '#FFC107',
            'Requested' => '#FF9800',
            'Accepted' => '#00BFA5',
            'Advance_Paid' => '#1E88E5',
            'Completed' => '#4CAF50',
            'Cancelled' => '#F44336',
            'Declined' => '#9E9E9E'
        ];
        $bookingStatusColors = [];
        
        foreach ($bookingData as $row) {
            $bookingStatusLabels[] = $row['status'];
            $bookingStatusCounts[] = (int)$row['count'];
            $bookingStatusColors[] = $statusColorMap[$row['status']] ?? '#9E9E9E';
        }

        // Prepare labels and data for performance chart
        $performanceLabels = [];
        $performanceCounts = [];
        $performanceColors = ['#1E88E5', '#00BFA5', '#FFC107', '#F44336', '#9E9E9E'];
        $colorMap = ['Excellent' => '#1E88E5', 'Good' => '#00BFA5', 'Average' => '#FFC107', 'Poor' => '#F44336', 'Not Rated' => '#9E9E9E'];
        
        foreach ($performanceData as $row) {
            $performanceLabels[] = $row['rating_category'];
            $performanceCounts[] = (int)$row['count'];
        }

        $pendingCountModel = $this->model('PendingCountModel');

        $data = [
            'totalCaretakers' => $dash->totalCaretakers(),
            'activeServices'  => $dash->activeServicesToday(),
            'pendingLeave'    => $dash->pendingLeaveRequests(),
            'pendingRequests' => $dash->pendingClientRequests(),
            'recentLeaves'    => $dash->recentLeaveRequests(5),
            'recentComplaints' => $dash->recentComplaints(5),
            'recentBookings'  => $dash->recentClientRequests(5),
            'badgeCounts'     => $pendingCountModel->getHRPendingCounts(),
            // Chart data
            'bookingStatusLabels' => json_encode($bookingStatusLabels),
            'bookingStatusCounts' => json_encode($bookingStatusCounts),
            'bookingStatusColors' => json_encode($bookingStatusColors),
            'performanceLabels' => json_encode($performanceLabels),
            'performanceCounts' => json_encode($performanceCounts),
            'performanceColors' => json_encode(array_slice($performanceColors, 0, count($performanceLabels))),
            'bookingData' => $bookingData,
            'performanceData' => $performanceData,
            'ratingStats' => $ratingStats
        ];

        $this->view('hr/hr_dashboard', $data);
    }


    public function hr_complaint()
    {

        // caretaker complaints (from ct_complaints table)
        $ctComplaints = $this->complaintModel->getCaretakerComplaints();

        // client complaints (from complaints table)
        $clientComplaints = $this->complaintModel->getAllComplaints();

        // Debug: Check if data is loaded
        error_log("CT Complaints count: " . count($ctComplaints));
        error_log("Client Complaints count: " . count($clientComplaints));
        
        if (!empty($ctComplaints)) {
            error_log("First CT Complaint: " . print_r($ctComplaints[0], true));
        }

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

    public function hr_logs()
    {
        $perPage = 10;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $currentUserId = (int)AuthSession::profileId();

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
        $userId = (int)AuthSession::profileId();
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

        foreach ($bookings as &$booking) {
            $conflict = $this->hrModel->findCaretakerConflictForBooking((int)$booking['booking_id']);
            $booking['availability_ok'] = empty($conflict);
            $booking['availability_conflict'] = $conflict ?: null;
        }
        unset($booking);

        // Detect overlapping bookings for the same caretaker within this result set
        $caretakerBookings = [];
        foreach ($bookings as &$booking) {
            $caretakerId = (int)($booking['caretaker_id'] ?? 0);
            if ($caretakerId > 0) {
                if (!isset($caretakerBookings[$caretakerId])) {
                    $caretakerBookings[$caretakerId] = [];
                }
                $caretakerBookings[$caretakerId][] = &$booking;
            }
        }
        unset($booking);

        // Mark overlapping bookings
        foreach ($caretakerBookings as $caretakerId => $bookingList) {
            if (count($bookingList) >= 2) {
                // Check for overlaps between bookings for this caretaker
                for ($i = 0; $i < count($bookingList); $i++) {
                    for ($j = $i + 1; $j < count($bookingList); $j++) {
                        if ($this->doBookingsOverlap($bookingList[$i], $bookingList[$j])) {
                            $bookingList[$i]['caretaker_overlap'] = true;
                            $bookingList[$j]['caretaker_overlap'] = true;
                        }
                    }
                }
            }
        }

        $this->view('hr/hr_pending_request', [
            'bookings' => $bookings,
            'page' => $page,
            'totalPages' => $totalPages,
            'status' => $status
        ]);
    }

    /**
     * Check if two bookings overlap in time (helper for caretaker overlap detection)
     */
    private function doBookingsOverlap($booking1, $booking2)
    {
        try {
            $date1 = $booking1['booking_date'] ?? null;
            $date2 = $booking2['booking_date'] ?? null;
            $basis1 = strtolower(trim($booking1['basis'] ?? ''));
            $basis2 = strtolower(trim($booking2['basis'] ?? ''));
            $duration1 = max(1, (int)($booking1['duration'] ?? 1));
            $duration2 = max(1, (int)($booking2['duration'] ?? 1));
            $time1 = $booking1['preferred_time'] ?? 'Full Time (8am - 5pm)';
            $time2 = $booking2['preferred_time'] ?? 'Full Time (8am - 5pm)';

            if (!$date1 || !$date2) return false;

            // Calculate end dates
            $end1 = $this->calculateBookingEndDate($date1, $basis1, $duration1);
            $end2 = $this->calculateBookingEndDate($date2, $basis2, $duration2);

            // Check date overlap: date1_start <= date2_end AND date1_end >= date2_start
            if ($date1 > $end2 || $date2 > $end1) {
                return false; // No date overlap
            }

            // If both are hourly, check time overlap
            if ($basis1 === 'hourly' && $basis2 === 'hourly') {
                $timeMap = [
                    "Morning (8am - 12pm)" => ["08:00:00", "12:00:00"],
                    "Evening (1pm - 5pm)"  => ["13:00:00", "17:00:00"],
                    "Night (6pm - 10pm)"   => ["18:00:00", "22:00:00"],
                    "Full Time (8am - 5pm)" => ["08:00:00", "17:00:00"]
                ];
                [$start1, $end1_time] = $timeMap[$time1] ?? ["00:00:00", "23:59:59"];
                [$start2, $end2_time] = $timeMap[$time2] ?? ["00:00:00", "23:59:59"];

                // Time overlap check
                if ($start1 >= $end2_time || $start2 >= $end1_time) {
                    return false;
                }
            }

            return true; // Overlap detected
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Calculate end date for a booking based on basis
     */
    private function calculateBookingEndDate($date, $basis, $duration)
    {
        try {
            $start = new DateTime($date);
            if ($basis === 'monthly') {
                $start->modify('+' . $duration . ' month -1 day');
            } elseif ($basis === 'yearly') {
                $start->modify('+' . $duration . ' year -1 day');
            } else {
                $start->modify('+' . ($duration - 1) . ' day');
            }
            return $start->format('Y-m-d');
        } catch (Exception $e) {
            return $date;
        }
    }

    /**
     * Redirect back to the pending-requests list preserving filter and page from POST.
     */
    private function redirectHrPendingRequestList(): void
    {
        $allowedStatuses = [
            'All', 'Requested', 'Payment_Requested', 'Advance_Paid', 'Accepted', 'Change_Requested',
            'Rejected', 'Cancelled', 'Completed', 'Reschedule_Requested',
        ];
        $status = (string) ($_POST['return_status'] ?? 'All');
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'All';
        }
        $page = max(1, (int) ($_POST['return_page'] ?? 1));
        $q = http_build_query(['status' => $status, 'page' => $page]);
        header('Location: ' . URLROOT . '/hr/hr_pending_request?' . $q);
        exit;
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
            $this->redirectHrPendingRequestList();
        }

        // Ensure assigned caregiver is still available for the booking period before requesting advance payment.
        $conflict = $this->hrModel->findCaretakerConflictForBooking((int)$booking_id);
        if (!empty($conflict)) {
            $conflictId = (int)($conflict['conflict_booking_id'] ?? 0);
            $conflictStatus = (string)($conflict['status'] ?? 'unknown');
            $conflictStart = (string)($conflict['start_date'] ?? 'N/A');
            $conflictEnd = (string)($conflict['end_date'] ?? 'N/A');

            $_SESSION['error'] = "Cannot request advance payment for Booking #{$booking_id}. " .
                "Assigned caregiver has a schedule conflict with Booking #{$conflictId} " .
                "({$conflictStart} to {$conflictEnd}, status: {$conflictStatus}).";
            $this->redirectHrPendingRequestList();
        }

        // 1️⃣ Update booking status
        $updated = $this->hrModel->requestAdvancePayment($booking_id);
        if (!$updated) {
            $_SESSION['error'] = 'Could not request advance payment. The booking may no longer be in Requested status.';
            $this->redirectHrPendingRequestList();
        }

        $booking = $this->hrModel->getBookingSummary($booking_id);

        $details = "";
        if ($booking) {
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

        $_SESSION['success'] = 'Advance payment requested. The client has been notified.';
        $this->redirectHrPendingRequestList();
    }

    public function rejectBookingRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/hr/hr_pending_request");
            exit;
        }

        $booking_id = (int) ($_POST['booking_id'] ?? 0);
        if ($booking_id < 1) {
            $_SESSION['error'] = 'Invalid booking.';
            $this->redirectHrPendingRequestList();
        }

        $ok = $this->hrModel->rejectBookingIfRequested($booking_id);
        if (!$ok) {
            $_SESSION['error'] = 'This booking could not be rejected. It may no longer be in Requested status.';
            $this->redirectHrPendingRequestList();
        }

        $clientId = $this->hrModel->getBookingClientId($booking_id);
        if ($clientId) {
            $this->notificationModel->addNotification(
                $clientId,
                'client',
                'Booking request declined',
                "Your booking request #{$booking_id} has been declined by HR.",
                URLROOT . '/client/c_upcomingBookings'
            );
        }

        $this->logHrAction("Rejected booking request #{$booking_id}", 'Pending Requests');
        $_SESSION['success'] = 'Booking request rejected.';
        $this->redirectHrPendingRequestList();
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
        require_once APPROOT . '/models/FeedbackModel.php';
        $feedbackModel = new FeedbackModel();
        $feedbacks = $feedbackModel->getAll();
        $this->view('hr/hr_feedback', ['feedbacks' => $feedbacks]);
    }

    public function hr_reports()
    {
        // Check for export request
        if (isset($_GET['export'])) {
            $this->exportHrReport();
            return;
        }

        $reportsModel = $this->model('HrReportsModel');

        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;

        // Get comprehensive operational report data using the specialized HR model
        $data = $reportsModel->getCompleteReportData($fromDate, $toDate);
        $data['fromDate'] = $fromDate;
        $data['toDate'] = $toDate;

        $this->view("hr/hr_reports", $data);
    }

    /**
     * AJAX endpoint to fetch filtered report data
     */
    public function getReportData()
    {
        header('Content-Type: application/json');

        $reportsModel = $this->model('HrReportsModel');

        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;

        $data = $reportsModel->getCompleteReportData($fromDate, $toDate);

        echo json_encode($data);
        exit;
    }

    /**
     * Export HR report to CSV or PDF
     */
    private function exportHrReport()
    {
        require_once APPROOT . '/core/ReportExporter.php';

        $reportsModel = $this->model('HrReportsModel');

        $fromDate = $_GET['from'] ?? null;
        $toDate = $_GET['to'] ?? null;
        $format = $_GET['format'] ?? 'csv'; // csv or pdf

        // Get all operational report data
        $data = $reportsModel->getCompleteReportData($fromDate, $toDate);

        // Generate filename
        $dateRange = ($fromDate && $toDate) ? "_" . $fromDate . "_to_" . $toDate : "_all_time";
        $filename = "hr_report" . $dateRange;

        // Export based on format
        if ($format === 'pdf') {
            ReportExporter::exportToPDF($data, $filename, 'hr');
        } else {
            ReportExporter::exportToCSV($data, $filename, 'hr');
        }
    }

    public function hr_settings()
    {
        // Session already started in constructor
        if (!AuthSession::isLoggedIn()) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        // Optional: allow only hr role
        if (!AuthSession::hasRole('manager')) {
            die("Access denied. Only HR can access this page.");
        }

        // Use session user directly
        $user = $_SESSION['user'];

        $this->view('hr/hr_settings', ['user' => $user]);
    }



    public function hr_announcement()
    {
        $announcementModel = $this->model('AnnouncementModel');
        $perPage     = 15;
        $currentPage = max(1, (int) ($_GET['page'] ?? 1));
        $filters     = [
            'for_hr_portal' => true,
            'date_from'     => trim((string) ($_GET['date_from'] ?? '')),
            'date_to'       => trim((string) ($_GET['date_to'] ?? '')),
            'q'             => trim((string) ($_GET['q'] ?? '')),
        ];
        $totalRecords = $announcementModel->countAnnouncementsFiltered($filters);
        $totalPages   = $totalRecords > 0 ? (int) ceil($totalRecords / $perPage) : 1;
        $currentPage  = max(1, min($currentPage, $totalPages));
        $offset         = ($currentPage - 1) * $perPage;
        $announcements = $announcementModel->getAnnouncementsFilteredPaged($filters, $perPage, $offset);

        $this->view('hr/hr_announcement', [
            'announcements' => $announcements,
            'filters'       => $filters,
            'currentPage'   => $currentPage,
            'totalPages'    => $totalPages,
            'totalRecords'  => $totalRecords,
            'perPage'       => $perPage,
        ]);
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

        $paymentType = strtolower(trim((string)($payment['payment_type'] ?? '')));
        $bookingStatus = (string)($payment['booking_status'] ?? '');
        $notifModel = $this->model('NotificationModel');
        // Fallback: treat payment as advance-stage approval if booking is still in pre-accepted states.
        $isAdvanceApproval = ($paymentType === 'advance')
            || in_array($bookingStatus, ['Payment_Requested', 'Advance_Paid'], true);

        if ($isAdvanceApproval) {
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

        $approvedAmount = number_format((float)($payment['amount'] ?? 0), 2);
        $clientMessage = "HR approved your payment for booking #" . $payment['booking_id'] . ". Amount: LKR " . $approvedAmount . ".";
        $notifModel->addNotification(
            $payment['client_id'],
            'client',
            'Payment Approved',
            $clientMessage,
            URLROOT . '/client/payments?tab=paid_history'
        );

        $this->logHrAction("Approved payment #{$paymentId} for Booking #{$payment['booking_id']}", 'Payments');

        $_SESSION['success'] = "Payment approved successfully! Client and caretaker notified.";
        header("Location: " . URLROOT . "/hr/pendingPayments");
        exit;
    }

    // ================= CHANGE REQUESTS =================
    public function changeRequests()
    {
        $crModel = $this->model('ChangeRequestModel');
        $pendingRequests = $crModel->getPendingRequests();
        $completedRequests = $crModel->getCompletedRequests();
        $changeRequests = array_merge($pendingRequests, $completedRequests);

        $allowedStatus = ['all', 'pending', 'approved', 'rejected'];
        $statusFilter = strtolower(trim((string) ($_GET['status'] ?? 'all')));
        if (!in_array($statusFilter, $allowedStatus, true)) {
            $statusFilter = 'all';
        }
        if ($statusFilter !== 'all') {
            $changeRequests = array_values(array_filter(
                $changeRequests,
                static function (array $r) use ($statusFilter): bool {
                    return strtolower((string) ($r['status'] ?? '')) === $statusFilter;
                }
            ));
        }

        $this->view('hr/changeRequests', [
            'change_requests' => $changeRequests,
            'pending_count' => count($pendingRequests),
            'history_count' => count($completedRequests),
            'status_filter' => $statusFilter,
        ]);
    }

    // ================= RESCHEDULE REQUESTS =================
    public function rescheduleRequests()
    {
        $rrModel = $this->model('RescheduleRequestModel');
        $pendingRequests = $rrModel->getPendingRequests();
        $completedRequests = $rrModel->getCompletedRequests();
        $rescheduleRequests = array_merge($pendingRequests, $completedRequests);

        $allowedStatus = ['all', 'pending', 'approved', 'rejected'];
        $statusFilter = strtolower(trim((string) ($_GET['status'] ?? 'all')));
        if (!in_array($statusFilter, $allowedStatus, true)) {
            $statusFilter = 'all';
        }
        if ($statusFilter !== 'all') {
            $rescheduleRequests = array_values(array_filter(
                $rescheduleRequests,
                static function (array $r) use ($statusFilter): bool {
                    return strtolower((string) ($r['status'] ?? '')) === $statusFilter;
                }
            ));
        }

        $this->view('hr/rescheduleRequests', [
            'reschedule_requests' => $rescheduleRequests,
            'pending_count' => count($pendingRequests),
            'history_count' => count($completedRequests),
            'status_filter' => $statusFilter,
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

        $requestId = (int) ($_POST['request_id'] ?? 0);
        $hrNote = trim((string) ($_POST['hr_note'] ?? ''));

        if ($requestId <= 0) {
            $_SESSION['error'] = "Invalid request ID.";
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        $rrModel = $this->model('RescheduleRequestModel');
        $reqDetails = $rrModel->getRequestById($requestId);

        if (!$reqDetails) {
            $_SESSION['error'] = "Reschedule request not found.";
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        if (($reqDetails['status'] ?? '') !== 'pending') {
            $_SESSION['error'] = "This request has already been processed.";
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        if ($hrNote === '') {
            $_SESSION['error'] = "A reason or HR note is required when rejecting.";
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        $ok = $rrModel->rejectRequest($requestId, $hrNote);
        if (!$ok) {
            $_SESSION['error'] = "Failed to reject reschedule request. It may have already been processed.";
            header("Location: " . URLROOT . "/hr/rescheduleRequests");
            exit;
        }

        $bookingId = (int) ($reqDetails['booking_id'] ?? 0);
        if ($bookingId > 0) {
            $this->model('ClientModel')->updateBookingStatus($bookingId, 'Requested');
            require_once APPROOT . '/models/NotificationModel.php';
            $notif = new NotificationModel();
            $cid = (int) ($reqDetails['client_id'] ?? 0);
            $msg = "Your reschedule request for booking #{$bookingId} has been rejected. The booking remains on the original date. Please check for any HR notes.";
            if ($hrNote !== '') {
                $msg .= "\nHR note: {$hrNote}";
            }
            $notif->addNotification($cid, 'client', 'Reschedule Rejected', $msg, URLROOT . "/client/c_upcomingBookings");
        }

        if ($bookingId > 0) {
            $this->logHrAction("Rejected reschedule request #{$requestId} (Booking #{$bookingId})", 'Reschedule Requests');
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
        $requestId = (int) ($_POST['request_id'] ?? 0);
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

        // Revert only advance-payment bookings back to payment requested stage.
        $bookingStatus = strtolower(trim((string)($payment['booking_status'] ?? '')));
        if (
            $payment['payment_type'] === 'advance'
            && !in_array($bookingStatus, ['cancelled', 'rejected', 'completed'], true)
        ) {
            $clientModel->updateBookingStatus($payment['booking_id'], 'Payment_Requested');
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
    /* ================= REFUND MANAGEMENT ================= */

    /**
     * Display refunds management page
     */
    public function refunds()
    {
        require_once APPROOT . '/core/RefundCalculationService.php';
        $refundService = new RefundCalculationService();

        $statusFilter = $_GET['status'] ?? 'all';

        // Get refunds based on filter
        if ($statusFilter === 'all') {
            $refunds = $refundService->getAllRefunds(null, 100);
        } else {
            $refunds = $refundService->getAllRefunds($statusFilter, 100);
        }

        $data = [
            'refunds' => $refunds,
            'status_filter' => $statusFilter,
            'pending_count' => count(array_filter($refunds, function ($r) {
                return $r['status'] === 'pending';
            }))
        ];

        $this->view('hr/hr_refunds', $data);
    }

    /**
     * View refund details
     */
    public function refundDetails()
    {
        $refundId = $_GET['refund_id'] ?? null;

        if (!$refundId) {
            $_SESSION['error'] = 'Invalid refund ID.';
            header("Location: " . URLROOT . "/hr/refunds");
            exit;
        }

        require_once APPROOT . '/core/RefundCalculationService.php';
        $refundService = new RefundCalculationService();

        $refund = $refundService->getRefundById($refundId);

        if (!$refund) {
            $_SESSION['error'] = 'Refund not found.';
            header("Location: " . URLROOT . "/hr/refunds");
            exit;
        }

        // Parse refund calculation JSON
        $refund['calculation_details'] = json_decode($refund['refund_calculation'], true);

        $data = ['refund' => $refund];
        $this->view('hr/hr_refund_details', $data);
    }

    /**
     * Approve or decline a refund
     */
    public function processRefund()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/hr/refunds");
            exit;
        }

        $refundId = $_POST['refund_id'] ?? null;
        $action = $_POST['action'] ?? null;
        $notes = $_POST['notes'] ?? '';

        if (!$refundId || !$action) {
            $_SESSION['error'] = 'Invalid refund or action.';
            header("Location: " . URLROOT . "/hr/refunds");
            exit;
        }

        require_once APPROOT . '/core/RefundCalculationService.php';
        $refundService = new RefundCalculationService();

        $userId = AuthSession::profileId();

        if ($action === 'approve') {
            $status = 'approved';
            $result = $refundService->updateRefundStatus($refundId, $status, $userId, $notes);
            $message = 'Refund approved successfully.';
            $logAction = "Approved Refund #{$refundId}";
        } elseif ($action === 'decline') {
            $status = 'declined';
            $result = $refundService->updateRefundStatus($refundId, $status, $userId, $notes);
            $message = 'Refund declined.';
            $logAction = "Declined Refund #{$refundId}";
        } else {
            $_SESSION['error'] = 'Invalid action.';
            header("Location: " . URLROOT . "/hr/refunds");
            exit;
        }

        if ($result) {
            // Send notification to client
            $refund = $refundService->getRefundById($refundId);
            if ($refund) {
                $this->sendRefundStatusNotification($refund, $status, $notes);
            }

            $_SESSION['success'] = $message;
            $this->logHrAction($logAction, 'Refunds');
        } else {
            $_SESSION['error'] = 'Failed to process refund.';
        }

        header("Location: " . URLROOT . "/hr/refunds");
        exit;
    }

    /**
     * Mark refund as processed/completed
     */
    public function completeRefund()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/hr/refunds");
            exit;
        }

        $refundId = $_POST['refund_id'] ?? null;
        $refundMethod = $_POST['refund_method'] ?? '';
        $refundReference = $_POST['refund_reference'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if (!$refundId) {
            $_SESSION['error'] = 'Invalid refund ID.';
            header("Location: " . URLROOT . "/hr/refunds");
            exit;
        }

        require_once APPROOT . '/core/RefundCalculationService.php';
        $refundService = new RefundCalculationService();

        $userId = AuthSession::profileId();
        $result = $refundService->updateRefundStatus($refundId, 'completed', $userId, $notes, $refundMethod, $refundReference);

        if ($result) {
            // Send notification to client
            $refund = $refundService->getRefundById($refundId);
            if ($refund) {
                $this->sendRefundCompletedNotification($refund, $refundMethod, $refundReference);
            }

            $_SESSION['success'] = 'Refund marked as completed.';
            $this->logHrAction("Completed Refund #{$refundId} via {$refundMethod}", 'Refunds');
        } else {
            $_SESSION['error'] = 'Failed to complete refund.';
        }

        header("Location: " . URLROOT . "/hr/refunds");
        exit;
    }

    /**
     * Send refund status notification to client
     */
    private function sendRefundStatusNotification($refund, $status, $notes)
    {
        $clientId = $refund['client_id'];
        $bookingId = $refund['booking_id'];
        $refundAmount = $refund['refund_amount'];

        if ($status === 'approved') {
            $title = 'Refund Approved';
            $message = "Your refund request for Booking #{$bookingId} has been approved.\n\n" .
                "Refund Amount: LKR " . number_format($refundAmount, 2) . "\n" .
                "The refund will be processed and transferred to your account shortly.\n";

            if (!empty($notes)) {
                $message .= "\nNote: {$notes}";
            }
        } else {
            $title = 'Refund Declined';
            $message = "Your refund request for Booking #{$bookingId} has been declined.\n\n";

            if (!empty($notes)) {
                $message .= "Reason: {$notes}\n";
            }

            $message .= "\nPlease contact our support team if you have any questions.";
        }

        $this->notificationModel->addNotification(
            $clientId,
            'client',
            $title,
            $message,
            URLROOT . "/client/c_cancelledBookings"
        );
    }

    /**
     * Send refund completed notification to client
     */
    private function sendRefundCompletedNotification($refund, $refundMethod, $refundReference)
    {
        $clientId = $refund['client_id'];
        $bookingId = $refund['booking_id'];
        $refundAmount = $refund['refund_amount'];

        $message = "Your refund for Booking #{$bookingId} has been processed.\n\n" .
            "Refund Amount: LKR " . number_format($refundAmount, 2) . "\n" .
            "Method: {$refundMethod}\n";

        if (!empty($refundReference)) {
            $message .= "Reference: {$refundReference}\n";
        }

        $message .= "\nPlease allow 3-5 business days for the funds to reflect in your account.";

        $this->notificationModel->addNotification(
            $clientId,
            'client',
            'Refund Completed',
            $message,
            URLROOT . "/client/c_cancelledBookings"
        );
    }

    /* ================= END REFUND MANAGEMENT ================= */
}
