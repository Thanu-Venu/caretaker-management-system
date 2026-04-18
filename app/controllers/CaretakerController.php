<?php

class CaretakerController extends Controller
{

    private $leaveModel;
    private $caretakerModel;
    private $clientModel;
    private $complaintModel;
    private $profileChangeRequestModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        if (!AuthSession::hasRole('caretaker')) {
            header("Location: " . URLROOT . "/public/?url=auth/login");
            exit;
        }

    
        $this->leaveModel = $this->model('LeaveModel');
        $this->caretakerModel = $this->model('CaretakerModel'); // lowercase property
        $this->clientModel = $this->model("ClientModel");
        $this->complaintModel = $this->model("ComplaintModel");
        $this->profileChangeRequestModel = $this->model("ProfileChangeRequestModel");

        // Revalidate caretaker from DB
        $user = $this->caretakerModel->getCaretakerById(AuthSession::profileId()); // lowercase usage
        if (!$user) {
            session_destroy();
            header("Location: " . URLROOT . "/public/?url=auth/login");
            exit;
        }

        $_SESSION['user'] = $user;
    }

    public function ct_dashboard()
    {
        try {
            $userId = AuthSession::profileId();
            
            // Get caretaker details
            $caretaker = $this->caretakerModel->getCaretakerById($userId);
            
            // Simple data for now
            $data = [
                'caretaker' => $caretaker,
                'leaves' => [],
                'upcoming' => [],
                'latestProfileChangeRequest' => null,
                'leaveMonthlySummary' => [],
                'workingDates' => [],
                'monthlyStats' => [
                    'is_available' => true,
                    'active_bookings' => 0,
                    'working_days' => 0,
                    'completed_bookings' => 0,
                    'rating' => 0
                ],
                'calendarYear' => (int)date('Y'),
                'calendarMonth' => (int)date('n')
            ];
            
            $this->view("caretaker/ct_dashboard", $data);
        } catch (Exception $e) {
            echo "Error in dashboard: " . $e->getMessage();
            exit;
        }
    }

    public function updateAvailabilityStatus()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $caretakerId = AuthSession::profileId();
        $isAvailable = isset($_POST['is_available']) && $_POST['is_available'] === '1';
        $status = $isAvailable ? 'Active' : 'Inactive';

        $ok = $this->caretakerModel->updateAvailabilityStatus($caretakerId, $status);
        if ($ok) {
            $_SESSION['user']['status'] = $status;
            echo json_encode(['success' => true, 'status' => $status]);
            exit;
        }

        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        exit;
    }

    private function getBookingInclusiveEndDate(array $booking): DateTime
    {
        $start = new DateTime($booking['booking_date']);
        $end = clone $start;
        $duration = max(1, (int)($booking['duration'] ?? 1));
        $basis = strtolower((string)($booking['basis'] ?? 'daily'));

        if ($basis === 'hourly') {
            return $end;
        }

        if ($basis === 'monthly') {
            $end->modify('+' . $duration . ' month')->modify('-1 day');
            return $end;
        }

        if ($basis === 'yearly') {
            $end->modify('+' . $duration . ' year')->modify('-1 day');
            return $end;
        }

        $end->modify('+' . ($duration - 1) . ' day');
        return $end;
    }

    public function ct_editprofile()
    {
        $this->view("caretaker/ct_editprofile");
    }

    public function ct_leave()
    {
        if (!AuthSession::hasRole('caretaker')) {
            die("Caretaker not logged in");
        }

        $leaveModel = $this->model('LeaveModel');
        $userId = AuthSession::profileId();
        $leaves = $leaveModel->getLeavesByUser($userId);
        $monthlySummary = $leaveModel->getCurrentMonthLeaveSummary($userId, true);

        $filters = [
            'status' => trim((string) ($_GET['leave_status'] ?? '')),
            'leave_type' => trim((string) ($_GET['leave_type'] ?? '')),
        ];

        $leaveTypeOptions = [];
        foreach ($leaves as $leave) {
            $leaveType = trim((string) ($leave['leave_type'] ?? ''));
            if ($leaveType !== '') {
                $leaveTypeOptions[$leaveType] = true;
            }
        }
        $leaveTypeOptions = array_keys($leaveTypeOptions);
        sort($leaveTypeOptions, SORT_NATURAL | SORT_FLAG_CASE);

        $leaves = array_values(array_filter($leaves, static function ($leave) use ($filters) {
            $status = trim((string) ($leave['status'] ?? ''));
            $leaveType = trim((string) ($leave['leave_type'] ?? ''));

            if ($filters['status'] !== '' && strcasecmp($status, $filters['status']) !== 0) {
                return false;
            }

            if ($filters['leave_type'] !== '' && strcasecmp($leaveType, $filters['leave_type']) !== 0) {
                return false;
            }

            return true;
        }));

        $this->view('caretaker/ct_leave', [
            'leaves' => $leaves,
            'monthlySummary' => $monthlySummary,
            'success' => $_SESSION['leave_success'] ?? '',
            'warning' => $_SESSION['leave_warning'] ?? '',
            'filters' => $filters,
            'leaveTypeOptions' => $leaveTypeOptions,
        ]);

        unset($_SESSION['leave_success'], $_SESSION['leave_warning']);
    }

    public function ct_booking()
    {
        $user = $_SESSION['user'];
        $caretakerId = $user['id'];

        $caretakerModel = $this->model('CaretakerModel');

        // Fetch bookings directly from DB
        $ongoing = $caretakerModel->getOngoingBookings($caretakerId);
        $upcoming = $caretakerModel->getUpcomingBookings($caretakerId);
        $past = $caretakerModel->getPastBookings($caretakerId);

        $filters = [
            'service_type' => trim((string) ($_GET['booking_service'] ?? '')),
            'date_from' => trim((string) ($_GET['booking_from'] ?? '')),
            'date_to' => trim((string) ($_GET['booking_to'] ?? '')),
        ];

        $serviceTypeOptions = [];
        foreach (array_merge($ongoing, $upcoming, $past) as $booking) {
            $serviceType = trim((string) ($booking['service_type'] ?? ''));
            if ($serviceType !== '') {
                $serviceTypeOptions[$serviceType] = true;
            }
        }
        $serviceTypeOptions = array_keys($serviceTypeOptions);
        sort($serviceTypeOptions, SORT_NATURAL | SORT_FLAG_CASE);

        $filterBookings = static function (array $rows) use ($filters): array {
            return array_values(array_filter($rows, static function ($booking) use ($filters) {
                $serviceType = trim((string) ($booking['service_type'] ?? ''));
                $bookingDate = (string) ($booking['booking_date'] ?? '');

                if ($filters['service_type'] !== '' && strcasecmp($serviceType, $filters['service_type']) !== 0) {
                    return false;
                }

                if ($filters['date_from'] !== '' && $bookingDate !== '' && $bookingDate < $filters['date_from']) {
                    return false;
                }

                if ($filters['date_to'] !== '' && $bookingDate !== '' && $bookingDate > $filters['date_to']) {
                    return false;
                }

                return true;
            }));
        };

        $ongoing = $filterBookings($ongoing);
        $upcoming = $filterBookings($upcoming);
        $past = $filterBookings($past);

        // Just pass the booking_date and preferred_time as they are
        $this->view('caretaker/ct_booking', [
            'ongoing' => $ongoing,
            'upcoming' => $upcoming,
            'past' => $past,
            'filters' => $filters,
            'serviceTypeOptions' => $serviceTypeOptions,
        ]);
    }

    public function ct_schedule()
    {
        $this->view("caretaker/ct_schedule");
    }

    public function getScheduleEvents()
    {
        header('Content-Type: application/json');

        $caretakerId = AuthSession::profileId();
        $bookings = $this->caretakerModel->getAllActiveBookings($caretakerId);

        // Get approved leaves for the caretaker
        require_once APPROOT . '/models/LeaveModel.php';
        $leaveModel = new LeaveModel();
        $approvedLeaves = $leaveModel->getLeavesByStatusAndUser('Approved', $caretakerId);

        $events = [];
        foreach ($bookings as $booking) {
            // Format duration display
            $durationText = $booking['duration'] . ' ' . ucfirst($booking['basis']);
            if ($booking['duration'] > 1) {
                // Add plural 's' if needed
                if ($booking['basis'] === 'Monthly') {
                    $durationText = $booking['duration'] . ' Months';
                } elseif ($booking['basis'] === 'Yearly') {
                    $durationText = $booking['duration'] . ' Years';
                } elseif ($booking['basis'] === 'Daily') {
                    $durationText = $booking['duration'] . ' Days';
                } elseif ($booking['basis'] === 'Hourly') {
                    $durationText = $booking['duration'] . ' Hours';
                }
            }

            // Build calendar range so all working dates are highlighted.
            $startDate = new DateTime($booking['booking_date']);
            $inclusiveEndDate = clone $startDate;
            $duration = max(1, (int) $booking['duration']);
            $basis = strtolower((string) $booking['basis']);

            if ($basis === 'hourly') {
                // Hourly work is only on the booking date.
                $inclusiveEndDate = clone $startDate;
            } elseif ($basis === 'monthly') {
                $inclusiveEndDate->modify('+' . $duration . ' month')->modify('-1 day');
            } elseif ($basis === 'yearly') {
                $inclusiveEndDate->modify('+' . $duration . ' year')->modify('-1 day');
            } else {
                // Daily and fallback handling.
                $inclusiveEndDate->modify('+' . ($duration - 1) . ' day');
            }

            // Set all bookings to blue color
            $backgroundColor = '#007bff'; // blue for all bookings
            $borderColor = '#0056b3';

            // Create individual events for each day in the booking period
            $currentDate = clone $startDate;
            $eventId = 0;

            while ($currentDate <= $inclusiveEndDate) {
                $dateRange = $startDate->format('Y-m-d');
                if ($startDate->format('Y-m-d') !== $inclusiveEndDate->format('Y-m-d')) {
                    $dateRange .= ' to ' . $inclusiveEndDate->format('Y-m-d');
                }

                // Add a compact duration tag for easier month-view scanning.
                $titleSuffix = '';
                if ($duration > 0) {
                    if ($basis === 'hourly') {
                        $unit = $duration === 1 ? 'hour' : 'hours';
                        $titleSuffix = ' for ' . $duration . ' ' . $unit;
                    } elseif ($basis === 'monthly') {
                        $unit = $duration === 1 ? 'month' : 'months';
                        $titleSuffix = ' for ' . $duration . ' ' . $unit;
                    } elseif ($basis === 'yearly') {
                        $unit = $duration === 1 ? 'year' : 'years';
                        $titleSuffix = ' for ' . $duration . ' ' . $unit;
                    } else {
                        $unit = $duration === 1 ? 'day' : 'days';
                        $titleSuffix = ' for ' . $duration . ' ' . $unit;
                    }
                }

                $events[] = [
                    'id' => $booking['booking_id'] . '_' . $eventId,
                    'title' => $booking['client_name'] . ' - ' . $booking['service_type'],
                    'start' => $currentDate->format('Y-m-d'),
                    'end' => $currentDate->format('Y-m-d'), // Single day event
                    'allDay' => true,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'extendedProps' => [
                        'client' => $booking['client_name'],
                        'service' => $booking['service_type'],
                        'time' => $booking['preferred_time'],
                        'duration' => $durationText,
                        'dateRange' => $dateRange,
                        'location' => $booking['service_location'],
                        'status' => $booking['status'],
                        'bookingId' => $booking['booking_id']
                    ]
                ];

                $currentDate->modify('+1 day');
                $eventId++;
            }
        }

        // Add approved leave events in orange color
        foreach ($approvedLeaves as $leave) {
            $startDate = new DateTime($leave['start_date']);
            $endDate = new DateTime($leave['end_date']);
            
            // Create individual events for each day of the leave
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $events[] = [
                    'id' => 'leave_' . $leave['id'] . '_' . $currentDate->format('Ymd'),
                    'title' => 'Leave - ' . htmlspecialchars($leave['leave_type']),
                    'start' => $currentDate->format('Y-m-d'),
                    'end' => $currentDate->format('Y-m-d'),
                    'allDay' => true,
                    'backgroundColor' => '#FF8C00', // orange color for leaves
                    'borderColor' => '#FF6600',
                    'extendedProps' => [
                        'type' => 'leave',
                        'leave_type' => $leave['leave_type'],
                        'reason' => htmlspecialchars($leave['reason']),
                        'leave_id' => $leave['id'],
                        'start_time' => $leave['start_time'],
                        'end_time' => $leave['end_time']
                    ]
                ];
                
                $currentDate->modify('+1 day');
            }
        }

        // Debug: Log final events
        error_log('Final events: ' . print_r($events, true));
        
        echo json_encode($events);
        exit;
    }

    public function ct_leaveHistory()
    {
        $this->view("caretaker/ct_leaveHistory");
    }
public function ct_complaints()
{
    $caretakerId = AuthSession::profileId();
    $complaints = $this->caretakerModel->getComplaintsByCaretaker($caretakerId);

    $filters = [
        'status' => trim((string) ($_GET['complaint_status'] ?? '')),
        'service_type' => trim((string) ($_GET['complaint_service'] ?? '')),
    ];

    $serviceTypeOptions = [];
    $statusOptions = [];
    foreach ($complaints as $complaint) {
        $serviceType = trim((string) ($complaint['service_type'] ?? ''));
        $status = trim((string) ($complaint['status'] ?? ''));
        if ($serviceType !== '') {
            $serviceTypeOptions[$serviceType] = true;
        }
        if ($status !== '') {
            $statusOptions[$status] = true;
        }
    }
    $serviceTypeOptions = array_keys($serviceTypeOptions);
    $statusOptions = array_keys($statusOptions);
    sort($serviceTypeOptions, SORT_NATURAL | SORT_FLAG_CASE);
    sort($statusOptions, SORT_NATURAL | SORT_FLAG_CASE);

    $complaints = array_values(array_filter($complaints, static function ($complaint) use ($filters) {
        $status = trim((string) ($complaint['status'] ?? ''));
        $serviceType = trim((string) ($complaint['service_type'] ?? ''));

        if ($filters['status'] !== '' && strcasecmp($status, $filters['status']) !== 0) {
            return false;
        }

        if ($filters['service_type'] !== '' && strcasecmp($serviceType, $filters['service_type']) !== 0) {
            return false;
        }

        return true;
    }));

    // Generate form token to prevent duplicate submissions
    $formToken = bin2hex(random_bytes(32));
    $_SESSION['complaint_form_token'] = $formToken;

    $data = [
        'clients' => $this->caretakerModel->getClients($caretakerId),
        'complaints' => $complaints,
        'filters' => $filters,
        'serviceTypeOptions' => $serviceTypeOptions,
        'statusOptions' => $statusOptions,
        'form_token' => $formToken,
    ];

    $this->view('caretaker/ct_complaints', $data);
}

    public function getClientInfo()
    {
        if (isset($_POST['client_id'])) {
            $client = $this->clientModel->getClientDetails($_POST['client_id']);
            echo json_encode($client);
        }
    }
public function addComplaint()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $data = [
            'caretaker_id' => AuthSession::profileId(),
            'client_id' => $_POST['client_id'], // 
            'service_type' => $_POST['service_type'],
            'service_date' => $_POST['service_date'], // 
            'description' => $_POST['description']
        ];

        $this->caretakerModel->addComplaint($data);

     
    }
}
public function saveComplaint()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/caretaker/ct_complaints");
        exit;
    }

    $data = [
        'caretaker_id' => AuthSession::profileId(),
        'client_id' => $_POST['client_id'] ?? '',
        'service_type' => $_POST['service_type'] ?? '',
        'service_date' => $_POST['service_date'] ?? '',
        'description' => $_POST['description'] ?? ''
    ];

    $ok = $this->caretakerModel->addComplaint($data);

    if ($ok) {
        $_SESSION['success'] = 'Complaint submitted successfully.';
    } else {
        $_SESSION['error'] = 'Failed to save complaint. Please try again.';
    }

    header("Location: " . URLROOT . "/caretaker/ct_complaints");
    exit;
}
    public function ct_reports()
    {
        $caretakerId = AuthSession::profileId();
        $services = $this->caretakerModel->getPastBookings($caretakerId);

        $this->view("caretaker/ct_reports", [
            'services' => $services
        ]);
    }

    public function ct_settings()
    {
        if (!AuthSession::isLoggedIn()) {
            header("Location: " . URLROOT . "/public/?url=auth/login");
            exit;
        }

        $user = $_SESSION['user'];
        $latestProfileChangeRequest = $this->profileChangeRequestModel->getLatestRequestByCaretaker((int)$user['id']);

        // pass user info to the view
        $this->view("caretaker/ct_settings", [
            'user' => $user,
            'latestProfileChangeRequest' => $latestProfileChangeRequest
        ]);
    }

    public function editCaretakerDetails()
    {

        if (!AuthSession::hasRole('caretaker')) {
            header("Location: " . URLROOT . "/public/?url=auth/login");
            exit;
        }

        $user = $_SESSION['user'];

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: " . URLROOT . "/caretaker/ct_settings");
            exit();
        }

        if ($this->profileChangeRequestModel->hasPendingRequest((int)$user['id'])) {
            $_SESSION['error'] = "You already have a pending profile change request.";
            header("Location: " . URLROOT . "/caretaker/ct_settings");
            exit();
        }

        $payload = [
            'caretaker_id' => (int)$user['id'],
            'requested_name' => trim($_POST['name'] ?? ($user['name'] ?? '')),
            'requested_email' => trim($_POST['email'] ?? ($user['email'] ?? '')),
            'requested_phone' => trim($_POST['phone'] ?? ($user['phone'] ?? '')),
            'requested_experience' => trim($_POST['experience'] ?? ($user['experience'] ?? '')),
            'requested_location' => trim($_POST['location'] ?? ($user['location'] ?? '')),
            'requested_qualifications' => trim($_POST['qualifications'] ?? ($user['qualifications'] ?? ''))
        ];

        $ok = $this->profileChangeRequestModel->createRequest($payload);

        header("Location: " . URLROOT . "/caretaker/ct_settings");
        exit();
    }

    public function editPasswordDetails()
    {
        if (!AuthSession::hasRole('caretaker')) {
            header("Location: " . URLROOT . "/auth/login");
            exit();
        }

        $user = $_SESSION['user'];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $newPassword = $_POST['new-password'];
            $confirmPassword = $_POST['confirm-password'];

            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = "Passwords do not match!";
                header("Location: " . URLROOT . "/caretaker/ct_settings");
                exit();
            }

            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update DB
            $this->caretakerModel->updateCaretakerPassword($user['id'], $hashedPassword);

            // Success
            $_SESSION['success'] = "Password updated successfully!";
            header("Location: " . URLROOT . "/caretaker/ct_settings");
            exit();
        }
    }

    public function ct_reviews()
    {
        if (!AuthSession::isLoggedIn()) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $caretakerId = AuthSession::profileId();

        $caretakerModel = $this->model('CaretakerModel');
        $feedbacks = $caretakerModel->getCaretakerFeedbacks($caretakerId);
        $avgRating = $caretakerModel->getAverageRating($caretakerId);

        $filters = [
            'service' => trim((string) ($_GET['feedback_service'] ?? '')),
            'rating' => trim((string) ($_GET['feedback_rating'] ?? '')),
        ];

        $serviceOptions = [];
        foreach ($feedbacks as $feedback) {
            $service = trim((string) ($feedback['service'] ?? ''));
            if ($service !== '') {
                $serviceOptions[$service] = true;
            }
        }
        $serviceOptions = array_keys($serviceOptions);
        sort($serviceOptions, SORT_NATURAL | SORT_FLAG_CASE);

        $feedbacks = array_values(array_filter($feedbacks, static function ($feedback) use ($filters) {
            $service = trim((string) ($feedback['service'] ?? ''));
            $rating = (string) ($feedback['rating'] ?? '');

            if ($filters['service'] !== '' && strcasecmp($service, $filters['service']) !== 0) {
                return false;
            }

            if ($filters['rating'] !== '' && $rating !== $filters['rating']) {
                return false;
            }

            return true;
        }));

        $this->view("caretaker/ct_reviews", [
            'feedbacks' => $feedbacks,
            'avgRating' => $avgRating,
            'filters' => $filters,
            'serviceOptions' => $serviceOptions,
        ]);
    }

    public function index()
    {
        // Redirect to dashboard when accessing /caretaker without a specific method
        $this->ct_dashboard();
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = [
                'client_id' => $_POST['client_id'],
                'service_type' => $_POST['service_type'],
                'date_of_service' => $_POST['date_of_service'],
                'description' => $_POST['description'],
                'caretaker_id' => AuthSession::profileId()
            ];

            $this->complaintModel->addComplaint($data);

            header("Location: " . URLROOT . "/complaint/index");
        }
    }

    public function ct_profile()
    {
        if (!AuthSession::hasRole('caretaker')) {
            header("Location: " . URLROOT . "/public/?url=auth/login");
            exit;
        }

        $this->view("caretaker/ct_profile");
    }

    public function ct_announcement()
    {
        $announcementModel = $this->model('AnnouncementModel');
        $perPage     = 15;
        $currentPage = max(1, (int) ($_GET['page'] ?? 1));
        $filters     = [
            'for_caretaker_portal' => true,
            'date_from'            => trim((string) ($_GET['date_from'] ?? '')),
            'date_to'              => trim((string) ($_GET['date_to'] ?? '')),
            'q'                    => trim((string) ($_GET['q'] ?? '')),
        ];
        $totalRecords = $announcementModel->countAnnouncementsFiltered($filters);
        $totalPages   = $totalRecords > 0 ? (int) ceil($totalRecords / $perPage) : 1;
        $currentPage  = max(1, min($currentPage, $totalPages));
        $offset       = ($currentPage - 1) * $perPage;
        $announcements = $announcementModel->getAnnouncementsFilteredPaged($filters, $perPage, $offset);

        $this->view('caretaker/ct_announcement', [
            'announcements' => $announcements,
            'filters'       => $filters,
            'currentPage'   => $currentPage,
            'totalPages'    => $totalPages,
            'totalRecords'  => $totalRecords,
            'perPage'       => $perPage,
        ]);
    }

    public function ct_myBookings()
    {
        $caretakerId = AuthSession::profileId();
        $bookings = $this->caretakerModel->getAllBookingsForCaretakerOverview($caretakerId);

        $this->view('caretaker/ct_my_bookings', [
            'bookings' => $bookings,
        ]);
    }

    public function ct_upcomingBookings()
    {
        $caretakerId = AuthSession::profileId();
        $bookings = $this->caretakerModel->getUpcomingBookings($caretakerId);

        $this->view("caretaker/ct_upcomingBookings", ['bookings' => $bookings]);
    }

    public function ct_pastBookings()
    {
        $caretakerId = AuthSession::profileId();
        $bookings = $this->caretakerModel->getPastBookings($caretakerId);
        $this->view("caretaker/ct_pastBookings", ['bookings' => $bookings]);
    }

    public function ct_ongoingBookings()
    {
        $caretakerId = AuthSession::profileId();
        $bookings = $this->caretakerModel->getOngoingBookings($caretakerId);

        $this->view("caretaker/ct_ongoingBookings", ['bookings' => $bookings]);
    }

    public function ct_feedback()
    {
        $caretakerId = AuthSession::profileId() ?: null;
        if (!$caretakerId) {
            header("Location: " . URLROOT . "/public/?url=auth/login");
            exit;
        }

        $feedbackModel = $this->model('FeedbackModel');
        $feedbacks = $feedbackModel->getByCaretaker($caretakerId);

        $this->view("caretaker/ct_feedback", ['feedbacks' => $feedbacks]);
    }

    public function ct_complaintReg()
    {
        $this->view("caretaker/ct_complaintReg");
    }

    public function submitComplaint()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/caretaker/ct_complaintReg");
            exit;
        }

        $caretakerId = AuthSession::profileId();
        $clientId = $_POST['client_id'] ?? null;
        $bookingId = $_POST['booking_id'] ?? null;

        if (!$clientId || !$bookingId) {
            $_SESSION['error'] = "Invalid request.";
            header("Location: " . URLROOT . "/caretaker/ct_complaintReg");
            exit;
        }

        // Validate booking belongs to caretaker
        $booking = $this->caretakerModel->getBookingById($bookingId);
        if (!$booking || (int)$booking['caretaker_id'] !== (int)$caretakerId) {
            $_SESSION['error'] = "Unauthorized access.";
            header("Location: " . URLROOT . "/caretaker/ct_complaintReg");
            exit;
        }

        // Prevent duplicate complaints
        if ($this->caretakerModel->complaintExists($bookingId, $caretakerId)) {
            $_SESSION['error'] = "Complaint already submitted for this booking.";
            header("Location: " . URLROOT . "/caretaker/ct_complaintReg");
            exit;
        }

        $data = [
            'booking_id'   => $bookingId,
            'client_id'    => $clientId,
            'caretaker_id' => $caretakerId,
            'complaint'    => $_POST['complaint'],
            'type'         => $_POST['type'] ?? 'service'
        ];

        $this->caretakerModel->addComplaint($data);

        $_SESSION['success'] = "Complaint submitted successfully!";
        header("Location: " . URLROOT . "/caretaker/ct_complaintReg");
        exit;
    }

    public function editComplaint($complaint_id = null)
    {
        if (!AuthSession::hasRole('caretaker')) {
            header("Location: " . URLROOT . "/auth/login");
            exit();
        }

        $complaint_id = (int) $complaint_id;
        if (!$complaint_id) {
            $_SESSION['error'] = "Invalid complaint ID";
            header("Location: " . URLROOT . "/caretaker/ct_complaints");
            exit;
        }

        $caretakerId = AuthSession::profileId();
        $complaint = $this->caretakerModel->getComplaintById($complaint_id);

        if (!$complaint || (int)$complaint['caretaker_id'] !== $caretakerId) {
            $_SESSION['error'] = "Complaint not found or unauthorized access";
            header("Location: " . URLROOT . "/caretaker/ct_complaints");
            exit;
        }

        // Get clients for dropdown
        $clients = $this->caretakerModel->getClientsByCaretaker($caretakerId);

        $data = [
            'complaint' => $complaint,
            'clients' => $clients
        ];

        $this->view('caretaker/ct_complaint_edit', $data);
    }

    public function updateComplaint()
    {
        if (!AuthSession::hasRole('caretaker')) {
            header("Location: " . URLROOT . "/auth/login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/caretaker/ct_complaints");
            exit;
        }

        $complaint_id = (int) $_POST['complaint_id'];
        $caretakerId = AuthSession::profileId();

        // Verify complaint belongs to caretaker
        $complaint = $this->caretakerModel->getComplaintById($complaint_id);
        if (!$complaint || (int)$complaint['caretaker_id'] !== $caretakerId) {
            $_SESSION['error'] = "Unauthorized access";
            header("Location: " . URLROOT . "/caretaker/ct_complaints");
            exit;
        }

        // Validate required fields
        $client_id = (int) $_POST['client_id'];
        $complaint_text = trim($_POST['complaint'] ?? '');
        $type = $_POST['type'] ?? 'service';

        if (!$client_id || empty($complaint_text)) {
            $_SESSION['error'] = "All fields are required";
            header("Location: " . URLROOT . "/caretaker/editComplaint/" . $complaint_id);
            exit;
        }
    
        // Update complaint
        $data = [
            'client_id' => $client_id,
            'complaint' => $complaint_text,
            'type' => $type
        ];

        $success = $this->caretakerModel->updateComplaint($complaint_id, $data);

        if ($success) {
            $_SESSION['success'] = "Complaint updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update complaint";
        }

        header("Location: " . URLROOT . "/caretaker/ct_complaints");
        exit;
    }

    

    public function deleteComplaint($complaint_id = null)
    {
        if (!AuthSession::hasRole('caretaker')) {
            header("Location: " . URLROOT . "/auth/login");
            exit();
        }

        $complaint_id = (int) $complaint_id;
        if (!$complaint_id) {
            $_SESSION['error'] = "Invalid complaint ID";
            header("Location: " . URLROOT . "/caretaker/ct_complaints");
            exit;
        }

        $caretakerId = AuthSession::profileId();

        // Verify complaint belongs to caretaker and can be deleted (only if pending)
        $complaint = $this->caretakerModel->getComplaintById($complaint_id);
        if (!$complaint || (int)$complaint['caretaker_id'] !== $caretakerId) {
            $_SESSION['error'] = "Complaint not found or unauthorized access";
            header("Location: " . URLROOT . "/caretaker/ct_complaints");
            exit;
        }

        // Only allow deletion if complaint is still pending
        if ($complaint['status'] !== 'Pending') {
            $_SESSION['error'] = "Cannot delete complaint that is already being processed";
            header("Location: " . URLROOT . "/caretaker/ct_complaints");
            exit;
        }

        // Delete complaint
        $success = $this->caretakerModel->deleteComplaint($complaint_id);

        if ($success) {
            $_SESSION['success'] = "Complaint deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete complaint";
        }

        header("Location: " . URLROOT . "/caretaker/ct_complaints");
        exit;
    }
}