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
            header("Location: index.php?url=auth/login");
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
            header("Location: index.php?url=auth/login");
            exit;
        }

        $_SESSION['user'] = $user;
    }

    public function ct_dashboard()
    {
        $userId = AuthSession::profileId();

        // Get caretaker details
        $caretakerModel = $this->model('CaretakerModel');
        $caretaker = $caretakerModel->getCaretakerById($userId);
        
        $avgRating = $caretakerModel->getAverageRating($userId);
        if ($caretaker) {
            $caretaker['rating'] = $avgRating;
        }

        // Leaves
        $leaves = $this->leaveModel->getLeavesByUser($userId);
        $leaveMonthlySummary = $this->leaveModel->getCurrentMonthLeaveSummary($userId, true);

        // Upcoming Bookings
        $upcoming = $caretakerModel->getUpcomingBookings($userId);

        // Latest profile change request status
        $latestProfileChangeRequest = $this->profileChangeRequestModel->getLatestRequestByCaretaker($userId);

        // Active + completed bookings for calendar/stats computation
        $allBookings = $caretakerModel->getAllActiveBookings($userId);

        $monthStart = new DateTime(date('Y-m-01'));
        $monthEnd = new DateTime(date('Y-m-t'));
        $today = new DateTime(date('Y-m-d'));

        $workingDaysThisMonth = 0;
        $activeBookingsThisMonth = 0;
        $completedThisMonth = 0;
        $workingDateSet = [];

        foreach ($allBookings as $booking) {
            $start = new DateTime($booking['booking_date']);
            $end = $this->getBookingInclusiveEndDate($booking);
            $status = $booking['status'] ?? '';

            if ($status === 'Accepted' && $start <= $monthEnd && $end >= $monthStart) {
                $activeBookingsThisMonth++;
            }

            if ($status === 'Completed' && $end >= $monthStart && $end <= $monthEnd) {
                $completedThisMonth++;
            }

            $overlapStart = $start > $monthStart ? clone $start : clone $monthStart;
            $overlapEnd = $end < $monthEnd ? clone $end : clone $monthEnd;

            if ($overlapStart <= $overlapEnd) {
                $workingDaysThisMonth += (int)$overlapStart->diff($overlapEnd)->format('%a') + 1;

                $cursor = clone $overlapStart;
                while ($cursor <= $overlapEnd) {
                    $workingDateSet[$cursor->format('Y-m-d')] = true;
                    $cursor->modify('+1 day');
                }
            }
        }

        $isCurrentlyAvailable = (($caretaker['status'] ?? 'Active') === 'Active');

        $monthlyStats = [
            'is_available' => $isCurrentlyAvailable,
            'active_bookings' => $activeBookingsThisMonth,
            'working_days' => $workingDaysThisMonth,
            'completed_bookings' => $completedThisMonth,
            'rating' => (float)($caretaker['rating'] ?? 0)
        ];

        // Pass everything to view
        $this->view("caretaker/ct_dashboard", [
            'caretaker' => $caretaker,
            'leaves' => $leaves,
            'upcoming' => $upcoming,
            'latestProfileChangeRequest' => $latestProfileChangeRequest,
            'leaveMonthlySummary' => $leaveMonthlySummary,
            'workingDates' => array_keys($workingDateSet),
            'monthlyStats' => $monthlyStats,
            'calendarYear' => (int)$today->format('Y'),
            'calendarMonth' => (int)$today->format('n')
        ]);
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

            // Set color based on status
            $backgroundColor = '#4CAF50'; // green for accepted
            $borderColor = '#45a049';

            if ($booking['status'] === 'Completed') {
                $backgroundColor = '#6c757d'; // gray for completed
                $borderColor = '#5a6268';
            } elseif ($booking['status'] === 'Payment_Requested') {
                $backgroundColor = '#ffc107'; // yellow for payment requested
                $borderColor = '#e0a800';
            } elseif ($booking['status'] === 'Advance_Paid') {
                $backgroundColor = '#17a2b8'; // blue for advance paid
                $borderColor = '#138496';
            }

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
    $resolvedComplaints = $this->caretakerModel->getResolvedComplaintsByCaretaker($caretakerId);

    $filters = [
        'status' => trim((string) ($_GET['complaint_status'] ?? '')),
        'service_type' => trim((string) ($_GET['complaint_service'] ?? '')),
    ];

    $serviceTypeOptions = [];
    $statusOptions = [];
    foreach ($resolvedComplaints as $complaint) {
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

    $resolvedComplaints = array_values(array_filter($resolvedComplaints, static function ($complaint) use ($filters) {
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

    $data = [
        'clients' => $this->caretakerModel->getClients($caretakerId),
        'resolvedComplaints' => $resolvedComplaints,
        'filters' => $filters,
        'serviceTypeOptions' => $serviceTypeOptions,
        'statusOptions' => $statusOptions,
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
            'client_id' => $_POST['client_id'], // ✅ CHANGE THIS
            'service_type' => $_POST['service_type'],
            'service_date' => $_POST['service_date'], // ✅ CHANGE THIS
            'description' => $_POST['description']
        ];

        $this->caretakerModel->addComplaint($data);

     
    }
}
public function saveComplaint()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $data = [
            'caretaker_id' => AuthSession::profileId(),
            'client_id' => $_POST['client_id'],
            'service_type' => $_POST['service_type'],
            'service_date' => $_POST['service_date'],
            'description' => $_POST['description']
        ];

        $this->caretakerModel->addComplaint($data);

        echo "success";
    }
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
            header("Location: index.php?url=auth/login");
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
            header("Location: " . URLROOT . "/auth/login");
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
        if ($ok) {
            require_once APPROOT . '/models/NotificationModel.php';
            $notif = new NotificationModel();
            $notif->notifyAdmins(
                'Caretaker Profile Update Request',
                'Caretaker ' . ($user['name'] ?? 'Unknown') . ' submitted a profile update request.',
                URLROOT . '/admin/ad_profile_requests'
            );

            $_SESSION['success'] = "Profile change request submitted. Admin approval is required.";
        } else {
            $_SESSION['error'] = "Could not submit profile change request.";
        }

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
        $clients = $this->clientModel->getAllClient();
        $complaints = $this->complaintModel->getAllComplaints();

        $this->view('caretaker/complaints', [
            'clients' => $clients,
            'complaints' => $complaints
        ]);
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
}
