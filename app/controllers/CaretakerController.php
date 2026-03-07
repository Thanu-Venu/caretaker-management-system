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

        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $this->leaveModel = $this->model('LeaveModel');
        $this->caretakerModel = $this->model('CaretakerModel'); // lowercase property
        $this->clientModel = $this->model("ClientModel");
        $this->complaintModel = $this->model("ComplaintModel");
        $this->profileChangeRequestModel = $this->model("ProfileChangeRequestModel");

        // Revalidate caretaker from DB
        $user = $this->caretakerModel->getCaretakerById($_SESSION['user']['id']); // lowercase usage
        if (!$user) {
            session_destroy();
            header("Location: index.php?url=auth/login");
            exit;
        }

        $_SESSION['user'] = $user;
    }

    public function ct_dashboard()
    {
        $userId = $_SESSION['user']['id'];

        // Get caretaker details
        $caretakerModel = $this->model('CaretakerModel');
        $caretaker = $caretakerModel->getCaretakerById($userId);

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

        $caretakerId = $_SESSION['user']['id'];
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
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
            die("Caretaker not logged in");
        }

        $leaveModel = $this->model('LeaveModel');
        $userId = $_SESSION['user']['id'];
        $leaves = $leaveModel->getLeavesByUser($userId);
        $monthlySummary = $leaveModel->getCurrentMonthLeaveSummary($userId, true);

        $this->view('caretaker/ct_leave', [
            'leaves' => $leaves,
            'monthlySummary' => $monthlySummary,
            'success' => $_SESSION['leave_success'] ?? '',
            'warning' => $_SESSION['leave_warning'] ?? ''
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

        // Just pass the booking_date and preferred_time as they are
        $this->view('caretaker/ct_booking', [
            'ongoing' => $ongoing,
            'upcoming' => $upcoming,
            'past' => $past
        ]);
    }

    public function ct_schedule()
    {
        $this->view("caretaker/ct_schedule");
    }

    public function getScheduleEvents()
    {
        header('Content-Type: application/json');

        $caretakerId = $_SESSION['user']['id'];
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
            } elseif ($basis === 'monthly') {
                $inclusiveEndDate->modify('+' . $duration . ' month')->modify('-1 day');
            } elseif ($basis === 'yearly') {
                $inclusiveEndDate->modify('+' . $duration . ' year')->modify('-1 day');
            } else {
                // Daily and fallback handling.
                $inclusiveEndDate->modify('+' . ($duration - 1) . ' day');
            }

            // FullCalendar end date is exclusive for all-day ranges.
            $exclusiveEndDate = clone $inclusiveEndDate;
            $exclusiveEndDate->modify('+1 day');
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

            // Set color based on status (only Accepted or Completed)
            $backgroundColor = '#4CAF50'; // green for accepted
            $borderColor = '#45a049';

            if ($booking['status'] === 'Completed') {
                $backgroundColor = '#6c757d'; // gray for completed
                $borderColor = '#5a6268';
            }

            $events[] = [
                'id' => $booking['booking_id'],
                'title' => $booking['client_name'] . ' - ' . $booking['service_type'] . $titleSuffix,
                'start' => $startDate->format('Y-m-d'),
                'end' => $exclusiveEndDate->format('Y-m-d'),
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
                    'status' => $booking['status']
                ]
            ];
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
        $caretakerId = $_SESSION['user']['id'];
        $complaints = $this->complaintModel->getComplaintsByCaretaker($caretakerId);

        $this->view('caretaker/ct_complaints', [
            'complaints' => $complaints
        ]);
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
                'caretaker_id' => $_SESSION['user']['id'],
                'client_name' => $_POST['client_name'],
                'service_type' => $_POST['service_type'],
                'date_of_service' => $_POST['date_of_service'],
                'description' => $_POST['description']
            ];

            $this->caretakerModel->addComplaint($data);

            echo "success";
        }
    }

    public function ct_reports()
    {
        $this->view("caretaker/ct_reports");
    }

    public function ct_settings()
    {
        if (!isset($_SESSION['user'])) {
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

        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
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
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
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
        if (!isset($_SESSION['user'])) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $caretakerId = $_SESSION['user']['id'];

        $caretakerModel = $this->model('CaretakerModel');
        $feedbacks = $caretakerModel->getCaretakerFeedbacks($caretakerId);

        $this->view("caretaker/ct_reviews", [
            'feedbacks' => $feedbacks
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
                'caretaker_id' => $_SESSION['user']['id']
            ];

            $this->complaintModel->addComplaint($data);

            header("Location: " . URLROOT . "/complaint/index");
        }
    }

    public function ct_announcement()
    {
        $announcementModel = $this->model('AnnouncementModel');
        $announcements = $announcementModel->getCaretakerAnnouncements();

        $this->view("caretaker/ct_announcement", $announcements);
    }
}
