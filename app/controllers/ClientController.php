<?php

require_once APPROOT . '/core/PayHereHelper.php';

class ClientController extends Controller
{
    private $clientModel;
    private $serviceOptions;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!AuthSession::hasRole('client')) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $this->clientModel = $this->model('ClientModel');
        $user = $this->clientModel->getClientById(AuthSession::profileId());

        if (!$user) { // user deleted
            session_destroy();
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        // Update session with latest data
        $_SESSION['user'] = $user;

        // <-- ADD THIS
        $this->serviceOptions = [
            "Elder Care" => ["Hourly", "Daily", "Monthly", "Yearly"],
            "Babysitter" => ["Hourly", "Daily", "Monthly", "Yearly"],
            "Maid" => ["Hourly", "Daily", "Monthly", "Yearly"],
            "Disability Support" => ["Hourly", "Daily", "Monthly", "Yearly"]
        ];
    }

    /**
     * Validate that a booking belongs to the current client
     * Prevents IDOR attacks by verifying ownership before operations
     *
     * @param int $bookingId The booking ID to validate
     * @param int $clientId The client ID to check ownership against
     * @param string $redirectUrl URL to redirect to on failure
     * @return array|null Returns booking data if valid, redirects and exits if invalid
     */
    private function assertBookingOwnership($bookingId, $clientId, $redirectUrl = null)
    {
        if (!$bookingId || !$clientId) {
            $_SESSION['error'] = "Invalid request";
            header("Location: " . ($redirectUrl ?? URLROOT . "/client/c_dashboard"));
            exit;
        }

        $booking = $this->clientModel->getBookingById($bookingId);

        if (!$booking) {
            $_SESSION['error'] = "Booking not found";
            header("Location: " . ($redirectUrl ?? URLROOT . "/client/c_dashboard"));
            exit;
        }

        // Check ownership
        if ((int)$booking['client_id'] !== (int)$clientId) {
            $_SESSION['error'] = "Unauthorized access to booking";
            header("Location: " . ($redirectUrl ?? URLROOT . "/client/c_dashboard"));
            exit;
        }

        return $booking;
    }

    public function c_dashboard()
    {
        $clientId = AuthSession::profileId();

        $data = [
            'activeBookings' => $this->clientModel->getActiveBookingsCount($clientId),
            'caretakers'     => $this->clientModel->getAssignedCaretakersCount($clientId),
            'totalSpent'     => $this->clientModel->getTotalSpent($clientId),
            'avgRating'      => $this->clientModel->getAverageRatingGiven($clientId),
            'recentBookings' => $this->clientModel->getRecentBookings($clientId),
            'assignedCaretaker' => $this->clientModel->getAssignedCaretaker($clientId),
            'pendingAdvance' => $this->clientModel->getAdvancePaymentPendingBookings($clientId),
            'notifications' => $this->clientModel->getAllClientNotifications($clientId)

        ];

        $this->view("client/c_dashboard", $data);
    }

    public function c_profile()
    {
        if (!AuthSession::hasRole('client')) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $this->view("client/c_profile");
    }

    public function c_find1()
    {
        $caretakerModel = $this->model('CaretakerModel');
        // only active caretakers should be shown to clients
        $allCaretakers = $caretakerModel->getActiveCaretakers();
        $locations = $caretakerModel->getDistinctLocations();

        // build service->locations map
        $services = ['Elder Care', 'Babysitter', 'Maid'];
        $serviceLocations = [];
        foreach ($services as $sv) {
            $serviceLocations[$sv] = $caretakerModel->getLocationsByService($sv);
        }

        $this->view("client/c_find1", [
            'allCaretakers' => $allCaretakers,
            'locations' => $locations,
            'serviceLocations' => $serviceLocations
        ]);
    }

    public function c_find()
    {
        $caretakerModel = $this->model('CaretakerModel');
        $caretakers = [];
        $locations = $caretakerModel->getDistinctLocations();

        // service locations map for popup on results
        $services = ['Elder Care', 'Babysitter', 'Maid'];
        $serviceLocations = [];
        foreach ($services as $sv) {
            $serviceLocations[$sv] = $caretakerModel->getLocationsByService($sv);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $location = $_POST['location'] ?? '';
            $caretakers = $caretakerModel->getAvailableCaretakers(
                $_POST['service_type'],
                $_POST['start_date'],
                $_POST['preferred_time'],
                $_POST['basis'],
                $_POST['duration'],
                $location
            );
        }

        $this->view("client/c_find", [
            'caretakers' => $caretakers,
            'locations' => $locations,
            'serviceLocations' => $serviceLocations
        ]);
    }

    public function c_feedback()
    {
        $clientId = AuthSession::profileId() ?: null;
        if (!$clientId) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $feedbackModel = $this->model('FeedbackModel');
        $feedbacks = $feedbackModel->getByClient($clientId);

        $this->view("client/c_feedback", ['feedbacks' => $feedbacks]);
    }

    public function submitFeedback()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/client/c_pastBookings");
            exit;
        }

        $clientModel = $this->model('ClientModel');
        $clientId = AuthSession::profileId();

        $bookingId = $_POST['booking_Id'];

        // Validate booking ownership (prevents IDOR)
        $this->assertBookingOwnership($bookingId, $clientId, URLROOT . "/client/c_pastBookings");

        $caretakerId = $clientModel->getCaretakerIdByBooking($bookingId);

        if (!$caretakerId) {
            $_SESSION['error'] = "Invalid booking.";
            header("Location: " . URLROOT . "/client/c_pastBookings");
            exit;
        }

        // Prevent duplicate feedback
        if ($clientModel->feedbackExists($bookingId)) {
            $_SESSION['error'] = "Feedback already submitted.";
            header("Location: " . URLROOT . "/client/c_pastBookings");
            exit;
        }

        $data = [
            'booking_id'   => $bookingId,
            'client_id'    => $clientId,
            'caretaker_id' => $caretakerId,
            'rating'       => $_POST['rating'],
            'feedback'     => $_POST['feedback']
        ];

        $clientModel->addFeedback($data);

        $_SESSION['success'] = "Feedback submitted successfully!";
        header("Location: " . URLROOT . "/client/c_pastBookings");
        exit;
    }

    public function c_payment()
    {
        $booking_id = $_GET['booking_id'] ?? null;
        $recurringPaymentId = $_GET['recurring_payment_id'] ?? null;
        $clientId = AuthSession::profileId();

        if (!$booking_id) {
            $_SESSION['error'] = "No booking selected";
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Validate booking ownership (prevents IDOR)
        $booking = $this->assertBookingOwnership($booking_id, $clientId, URLROOT . "/client/c_upcomingBookings");

        // Calculate payment info
        require_once APPROOT . '/controllers/PaymentController.php';
        $payment_calc = PaymentController::calculatePaymentDetails($booking);
        $payment_calc['advance'] = $payment_calc['advance_amount'] ?? 0;
        $payment_calc['remaining'] = $payment_calc['remaining_balance'] ?? 0;
        $payment_calc['notes'] = $payment_calc['description'] ?? '';

        $recurringPayment = null;
        if (!empty($recurringPaymentId)) {
            $recurringPayment = $this->clientModel->getRecurringPaymentByIdForClient(
                (int)$recurringPaymentId,
                (int)AuthSession::profileId(),
                (int)$booking_id
            );

            if (!$recurringPayment || !in_array($recurringPayment['status'], ['pending', 'overdue'], true)) {
                $_SESSION['error'] = "Invalid recurring payment request";
                header("Location: " . URLROOT . "/client/c_upcomingBookings");
                exit;
            }
        }

        $this->view("client/c_payment", [
            'booking' => $booking,
            'payment_calc' => $payment_calc,
            'recurring_payment' => $recurringPayment
        ]);
    }

    public function c_paymentHistory()
    {
        // Keep backward compatibility with old menu/links.
        header("Location: " . URLROOT . "/client/payments?tab=paid_history");
        exit;

        // Legacy code (intentionally unreachable after redirect)
        $clientId = AuthSession::profileId() ?: null;
        if (!$clientId) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $payments = $this->clientModel->getPaymentsByClient($clientId);
        $this->view("client/c_paymentHistory", ['payments' => $payments]);
    }

    public function payments()
    {
        $clientId = AuthSession::profileId() ?: null;
        if (!$clientId) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $allowedTabs = ['all', 'due', 'upcoming', 'history'];
        $tab = $_GET['tab'] ?? 'all';
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'all';
        }

        $filters = [
            'tab' => $tab,
            'search' => trim((string)($_GET['search'] ?? '')),
            'status' => trim((string)($_GET['status'] ?? 'all')),
            'service_type' => trim((string)($_GET['service_type'] ?? 'all')),
            'booking_status' => trim((string)($_GET['booking_status'] ?? 'all')),
            'from_date' => trim((string)($_GET['from_date'] ?? '')),
            'to_date' => trim((string)($_GET['to_date'] ?? '')),
        ];

        $summary = $this->clientModel->getClientPaymentSummary((int)$clientId);
        $actionItems = $this->clientModel->getClientActionRequiredPayments((int)$clientId);
        $bookingOverview = $this->clientModel->getClientBookingPaymentOverview((int)$clientId);
        $history = $this->clientModel->getClientPaymentHistoryDetailed((int)$clientId);

        $todayTs = strtotime(date('Y-m-d'));
        $upcomingWindowTs = strtotime('+30 days', $todayTs);

        $filteredAction = array_values(array_filter($actionItems, function ($item) use ($filters, $todayTs, $upcomingWindowTs) {
            $search = strtolower($filters['search']);
            if ($search !== '') {
                $haystack = strtolower(
                    (string)$item['booking_id'] . ' ' .
                        (string)$item['service_type'] . ' ' .
                        (string)$item['caretaker_name']
                );
                if (strpos($haystack, $search) === false) {
                    return false;
                }
            }

            if ($filters['service_type'] !== 'all' && strcasecmp((string)$item['service_type'], (string)$filters['service_type']) !== 0) {
                return false;
            }

            if ($filters['status'] !== 'all' && strcasecmp((string)$item['payment_status'], (string)$filters['status']) !== 0) {
                return false;
            }

            if ($filters['booking_status'] !== 'all' && strcasecmp((string)$item['booking_status'], (string)$filters['booking_status']) !== 0) {
                return false;
            }

            $dueTs = !empty($item['due_date']) ? strtotime((string)$item['due_date']) : null;
            if (!empty($filters['from_date']) && $dueTs !== null && $dueTs < strtotime($filters['from_date'])) {
                return false;
            }
            if (!empty($filters['to_date']) && $dueTs !== null && $dueTs > strtotime($filters['to_date'])) {
                return false;
            }

            if ($filters['tab'] === 'due') {
                return (string)$item['payment_status'] === 'overdue' || ((int)$item['days_delta'] <= 0 && (string)$item['payment_status'] !== 'advance_required');
            }

            if ($filters['tab'] === 'upcoming') {
                if ($dueTs === null) {
                    return false;
                }
                return ((string)$item['payment_status'] === 'pending') && $dueTs > $todayTs && $dueTs <= $upcomingWindowTs;
            }

            if ($filters['tab'] === 'history') {
                return false;
            }

            return true;
        }));

        $filteredBookings = array_values(array_filter($bookingOverview, function ($item) use ($filters) {
            $search = strtolower($filters['search']);
            if ($search !== '') {
                $haystack = strtolower(
                    (string)$item['booking_id'] . ' ' .
                        (string)$item['service_type'] . ' ' .
                        (string)$item['caretaker_name']
                );
                if (strpos($haystack, $search) === false) {
                    return false;
                }
            }

            if ($filters['service_type'] !== 'all' && strcasecmp((string)$item['service_type'], (string)$filters['service_type']) !== 0) {
                return false;
            }

            if ($filters['booking_status'] !== 'all' && strcasecmp((string)$item['status'], (string)$filters['booking_status']) !== 0) {
                return false;
            }

            return true;
        }));

        $filteredHistory = array_values(array_filter($history, function ($item) use ($filters) {
            $search = strtolower($filters['search']);
            if ($search !== '') {
                $haystack = strtolower(
                    (string)$item['booking_id'] . ' ' .
                        (string)$item['service_type'] . ' ' .
                        (string)$item['caretaker_name']
                );
                if (strpos($haystack, $search) === false) {
                    return false;
                }
            }

            if ($filters['service_type'] !== 'all' && strcasecmp((string)$item['service_type'], (string)$filters['service_type']) !== 0) {
                return false;
            }

            if ($filters['status'] !== 'all' && strcasecmp((string)$item['status'], (string)$filters['status']) !== 0) {
                return false;
            }

            $paidTs = !empty($item['paid_at']) ? strtotime((string)$item['paid_at']) : null;
            if (!empty($filters['from_date']) && $paidTs !== null && $paidTs < strtotime($filters['from_date'])) {
                return false;
            }
            if (!empty($filters['to_date']) && $paidTs !== null && $paidTs > strtotime($filters['to_date'] . ' 23:59:59')) {
                return false;
            }

            if ($filters['tab'] === 'history') {
                return strtolower((string)$item['status']) === 'approved';
            }

            return true;
        }));

        $this->view('client/c_payments', [
            'summary' => $summary,
            'action_items' => $filteredAction,
            'booking_overview' => $filteredBookings,
            'payment_history' => $filteredHistory,
            'filters' => $filters
        ]);
    }

    public function paymentDetails($bookingId = null)
    {
        $clientId = AuthSession::profileId() ?: null;
        if (!$clientId) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        if ($bookingId === null) {
            $bookingId = $_GET['booking_id'] ?? null;
        }

        $bookingId = (int)$bookingId;
        if ($bookingId <= 0) {
            header("Location: " . URLROOT . "/client/payments");
            exit;
        }

        // Validate booking ownership (prevents IDOR)
        $booking = $this->assertBookingOwnership($bookingId, $clientId, URLROOT . "/client/payments");

        $timelineData = $this->clientModel->getBookingPaymentTimelineData((int)$clientId, $bookingId);

        $timelineEvents = [];
        $payments = $timelineData['payments'] ?? [];
        $recurring = $timelineData['recurring'] ?? [];

        $advancePayment = null;
        foreach ($payments as $p) {
            if (($p['payment_type'] ?? '') === 'advance') {
                $advancePayment = $p;
                break;
            }
        }

        if ($advancePayment) {
            $advanceStatus = strtolower((string)$advancePayment['status']);
            $timelineEvents[] = [
                'label' => 'Advance Payment',
                'status' => $advanceStatus === 'approved' ? 'paid' : $advanceStatus,
                'date' => $advancePayment['approved_at'] ?: $advancePayment['created_at'],
                'note' => 'Amount: LKR ' . number_format((float)$advancePayment['amount'], 2)
            ];
        } elseif (($booking['status'] ?? '') === 'Payment_Requested') {
            $timelineEvents[] = [
                'label' => 'Advance Payment Required',
                'status' => 'due_soon',
                'date' => $booking['service_start_date'] ?? null,
                'note' => 'Please complete the advance payment to start the service.'
            ];
        }

        $advanceMonths = (int)($booking['advance_months'] ?? 0);
        $basis = strtolower((string)($booking['basis'] ?? ''));
        if (in_array($basis, ['monthly', 'yearly'], true) && $advanceMonths > 0) {
            for ($i = 1; $i <= $advanceMonths; $i++) {
                $timelineEvents[] = [
                    'label' => 'Month ' . $i,
                    'status' => 'paid',
                    'date' => null,
                    'note' => 'Covered by advance payment'
                ];
            }
        }

        $todayTs = strtotime(date('Y-m-d'));
        $nextPayable = null;

        foreach ($recurring as $rp) {
            $status = strtolower((string)$rp['status']);
            $dueTs = strtotime((string)$rp['due_date']);
            $days = (int)(($dueTs - $todayTs) / 86400);

            $eventStatus = 'upcoming';
            if ($status === 'paid') {
                $eventStatus = 'paid';
            } elseif ($status === 'cancelled') {
                $eventStatus = 'cancelled';
            } elseif ($status === 'overdue') {
                $eventStatus = 'overdue';
            } elseif ($days <= 7) {
                $eventStatus = 'due_soon';
            }

            $timelineEvents[] = [
                'label' => 'Cycle ' . (int)$rp['cycle_number'] . ' (' . $rp['cycle_type'] . ')',
                'status' => $eventStatus,
                'date' => $rp['status'] === 'paid' ? ($rp['paid_at'] ?: $rp['due_date']) : $rp['due_date'],
                'note' => 'Amount: LKR ' . number_format((float)$rp['amount'], 2) .
                    (!empty($rp['grace_period_end']) ? (' | Grace Ends: ' . $rp['grace_period_end']) : '')
            ];

            if ($nextPayable === null && in_array($status, ['pending', 'overdue'], true)) {
                $canPay = false;
                if ($status === 'overdue') {
                    $canPay = empty($rp['grace_period_end']) || strtotime((string)$rp['grace_period_end']) >= $todayTs;
                } else {
                    $canPay = $days <= 7;
                }

                if ($canPay) {
                    $nextPayable = $rp;
                }
            }
        }

        $this->view('client/c_paymentDetails', [
            'booking' => $booking,
            'timeline_events' => $timelineEvents,
            'payments' => $payments,
            'recurring' => $recurring,
            'next_payable' => $nextPayable
        ]);
    }


    public function c_paymentSuccess()
    {
        $paymentId = $_GET['payment_id'] ?? null;
        $this->view("client/c_paymentSuccess", ['payment_id' => $paymentId]);
    }

    /* public function c_makePayment() {
        $this->view("client/c_makePayment");
    }*/

    public function c_complaintReg()
    {
        $this->view("client/c_complaintReg");
    }

    public function c_pastBookings()
    {
        $clientId = AuthSession::profileId();
        $bookings = $this->clientModel->getPastBookingsWithFeedback($clientId);
        $this->view("client/c_pastBookings", ['bookings' => $bookings]);
    }


    public function c_upcomingBookings()
    {
        $clientId = AuthSession::profileId();
        $bookings = $this->clientModel->getUpcomingBookings($clientId);
        $pendingAdvance = $this->clientModel->getAdvancePaymentPendingBookings($clientId);

        $this->view("client/c_upcomingBookings", [
            'bookings' => $bookings,
            'pendingAdvance' => $pendingAdvance
        ]);
    }

    // New ongoing bookings page for client
    public function c_ongoingBookings()
    {
        $clientId = AuthSession::profileId();
        $bookings = $this->clientModel->getOngoingBookings($clientId);

        $this->view("client/c_ongoingBookings", ['bookings' => $bookings]);
    }

    // Handle change caretaker request from client (client-driven)
    public function requestCaretakerChange()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/client/myBookings");
            exit;
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $newCaretakerId = (int)($_POST['new_caretaker_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        $clientId = (int)AuthSession::profileId();

        if ($bookingId <= 0 || $newCaretakerId <= 0 || $clientId <= 0 || $reason === '') {
            $_SESSION['error'] = "Missing details.";
            header("Location: " . URLROOT . "/client/myBookings");
            exit;
        }

        // ✅ get booking details and validate ownership + status
        $booking = $this->clientModel->getBookingById($bookingId); // must return caretaker_id, client_id, status
        if (!$booking || (int)$booking['client_id'] !== $clientId) {
            $_SESSION['error'] = "Invalid booking.";
            header("Location: " . URLROOT . "/client/c_ongoingBookings");
            exit;
        }

        // ✅ Only allow if booking is Accepted
        if (strtolower(trim($booking['status'])) !== 'accepted') {
            $_SESSION['error'] = "Change request allowed only for Accepted bookings.";
            header("Location: " . URLROOT . "/client/c_ongoingBookings");
            exit;
        }

        // ✅ prevent same caretaker
        if ((int)$booking['caretaker_id'] === $newCaretakerId) {
            $_SESSION['error'] = "Please select a different caretaker.";
            header("Location: " . URLROOT . "/client/c_ongoingBookings");
            exit;
        }

        require_once APPROOT . '/models/ChangeRequestModel.php';
        $cr = new ChangeRequestModel();

        $ok = $cr->createRequest([
            'booking_id' => $bookingId,
            'client_id' => $clientId,
            'old_caretaker_id' => (int)$booking['caretaker_id'],
            'new_caretaker_id' => $newCaretakerId,
            'reason' => $reason
        ]);

        if (!$ok) {
            $_SESSION['error'] = "Already requested / failed.";
            header("Location: " . URLROOT . "/client/c_ongoingBookings");
            exit;
        }

        $_SESSION['success'] = "Change request submitted.";
        header("Location: " . URLROOT . "/client/c_ongoingBookings");
        exit;

        // after createRequest() success
        require_once APPROOT . '/models/NotificationModel.php';
        $notif = new NotificationModel();

        // notify HR/Manager users
        $hrUsers = $notif->getHRUsers(); // you already use this elsewhere

        if (!empty($hrUsers)) {
            $summary = $this->clientModel->getBookingSummaryForNotification($bookingId);

            $msg = "New caregiver change request.\n";
            if ($summary) {
                $msg .= "Booking #{$summary['booking_id']} | {$summary['service_type']}\n";
                $msg .= "Client: {$summary['client_name']} ({$summary['client_email']})\n";
                $msg .= "Date: {$summary['booking_date']} | Time: {$summary['preferred_time']}\n";
                $msg .= "Duration: {$summary['duration']} {$summary['basis']}\n";
                $msg .= "Old caregiver: {$summary['caretaker_name']}\n";
                $msg .= "Requested caregiver ID: {$newCaretakerId}\n";
            }
            $msg .= "Reason: {$reason}";

            // notify all HRs (or first one)
            foreach ($hrUsers as $hr) {
                $notif->addNotification(
                    $hr['id'],
                    'Manager',
                    'Caregiver Change Request',
                    $msg,
                    URLROOT . "/hr/changeRequests"
                );
            }
        }
    }
    public function fetchAvailableCaretakers()
    {
        // stop any previous output
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');

        // suppress warnings only for this endpoint (optional)
        $prev = error_reporting(0);

        $bookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
        $clientId = AuthSession::profileId();

        if ($bookingId <= 0) {
            error_reporting($prev);
            echo json_encode(['error' => 'missing booking_id']);
            exit;
        }

        $booking = $this->clientModel->getBookingById($bookingId);
        if (!$booking) {
            error_reporting($prev);
            echo json_encode(['error' => 'booking not found']);
            exit;
        }

        // Validate booking ownership (prevents IDOR)
        if ((int)$booking['client_id'] !== (int)$clientId) {
            error_reporting($prev);
            echo json_encode(['error' => 'unauthorized access']);
            exit;
        }

        $caretakerModel = $this->model('CaretakerModel');
        $location = $booking['district'] ?? '';

        $list = $caretakerModel->getAvailableCaretakers(
            $booking['service_type'],
            $booking['booking_date'],
            $booking['preferred_time'],
            $booking['basis'],
            $booking['duration'],
            $location
        );

        if (empty($list)) {
            $list = $caretakerModel->getAvailableCaretakers(
                $booking['service_type'],
                $booking['booking_date'],
                $booking['preferred_time'],
                $booking['basis'],
                $booking['duration'],
                $location,
            );
        }

        // Remove current caretaker
        $list = array_filter($list, function ($c) use ($booking) {
            return (int)$c['id'] !== (int)$booking['caretaker_id'];
        });

        // Re-index + keep only id and name
        $result = array_values(array_map(function ($c) {
            return [
                'id' => (int)$c['id'],
                'name' => $c['name'],
                'rating' => (float)($c['rating'] ?? 0),
                'rating_count' => (int)($c['rating_count'] ?? 0),
                'experience_years' => (int)($c['experience'] ?? 0),
                'qualification' => $c['qualifications'] ?? '',
                'profile_image' => $c['profile_image'] ?? 'default.png'
            ];
        }, $list));
        error_reporting($prev);
        echo json_encode($result);
        exit; // ✅ MOST IMPORTANT: prevent any extra output appended after JSON
    }


    /* ================= CANCEL BOOKING ================= */
    public function cancelBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        $bookingId = $_POST['booking_id'] ?? null;
        $reason    = $_POST['reason'] ?? '';
        $clientId = AuthSession::profileId();

        if (!$bookingId || empty($reason)) {
            $_SESSION['error'] = 'Invalid booking or reason not provided.';
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Validate booking ownership (prevents IDOR)
        $this->assertBookingOwnership($bookingId, $clientId, URLROOT . "/client/c_upcomingBookings");

        // Load refund calculation service
        require_once APPROOT . '/core/RefundCalculationService.php';
        $refundService = new RefundCalculationService();

        // Calculate refund
        $refundCalculation = $refundService->calculateRefund($bookingId, $reason, false);

        if (!$refundCalculation['success']) {
            $_SESSION['error'] = $refundCalculation['message'] ?? 'Failed to calculate refund.';
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Cancel the booking
        $cancelled = $this->clientModel->cancelBooking($bookingId, $reason);

        if ($cancelled) {
            // Create refund record
            $refundResult = $refundService->createRefundRecord($refundCalculation);

            if ($refundResult['success']) {
                // Cancel future recurring payments
                $this->cancelFutureRecurringPayments($bookingId);

                // Send notifications
                $this->sendCancellationNotifications($bookingId, $refundCalculation);

                $_SESSION['success'] = 'Booking cancelled successfully. ';

                if ($refundCalculation['refund_amount'] > 0) {
                    $_SESSION['success'] .= 'A refund of LKR ' . number_format($refundCalculation['refund_amount'], 2) .
                        ' will be processed after HR approval.';
                } else {
                    $_SESSION['success'] .= 'No refund applicable for this cancellation.';
                }
            } else {
                $_SESSION['error'] = 'Booking cancelled but refund record creation failed. Please contact support.';
            }
        } else {
            $_SESSION['error'] = 'Failed to cancel booking. Please try again.';
        }

        // Redirect back to upcoming bookings
        header("Location: " . URLROOT . "/client/c_upcomingBookings");
        exit;
    }

    /**
     * Cancel future recurring payments for a cancelled booking
     */
    private function cancelFutureRecurringPayments($bookingId)
    {
        require_once APPROOT . '/core/RecurringPaymentService.php';
        $recurringService = new RecurringPaymentService();

        // This method should cancel all pending/overdue recurring payments
        $sql = "UPDATE recurring_payments
                SET status = 'cancelled'
                WHERE booking_id = ?
                AND status IN ('pending', 'overdue')";

        $db = new Database();
        $conn = $db->conn;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Send cancellation notifications to relevant parties
     */
    private function sendCancellationNotifications($bookingId, $refundCalculation)
    {
        require_once APPROOT . '/models/NotificationModel.php';
        $notificationModel = new NotificationModel();

        // Get booking details
        $booking = $this->clientModel->getBookingById($bookingId);
        if (!$booking) return;

        $clientId = $booking['client_id'];
        $caretakerId = $booking['caretaker_id'];
        $refundAmount = $refundCalculation['refund_amount'];

        // Notify client
        $clientMessage = "Your booking #{$bookingId} has been cancelled.\n\n" .
            "Service: {$booking['service_type']}\n" .
            "Booking Date: {$booking['booking_date']}\n\n";

        if ($refundAmount > 0) {
            $clientMessage .= "Refund Amount: LKR " . number_format($refundAmount, 2) . "\n" .
                "Your refund is pending HR approval and will be processed shortly.";
        } else {
            $clientMessage .= "No refund applicable for this cancellation based on our refund policy.";
        }

        $notificationModel->addNotification(
            $clientId,
            'client',
            'Booking Cancelled',
            $clientMessage,
            URLROOT . "/client/c_cancelledBookings"
        );

        // Notify caretaker
        $caretakerMessage = "Booking #{$bookingId} has been cancelled by the client.\n\n" .
            "Service: {$booking['service_type']}\n" .
            "Booking Date: {$booking['booking_date']}\n\n" .
            "This booking is no longer active.";

        $notificationModel->addNotification(
            $caretakerId,
            'caretaker',
            'Booking Cancelled',
            $caretakerMessage,
            URLROOT . "/caretaker/ct_bookings"
        );

        // Notify HR
        $hrMessage = "Booking #{$bookingId} has been cancelled.\n\n" .
            "Client ID: {$clientId}\n" .
            "Service: {$booking['service_type']}\n" .
            "Caretaker ID: {$caretakerId}\n\n";

        if ($refundAmount > 0) {
            $hrMessage .= "Refund Amount: LKR " . number_format($refundAmount, 2) . "\n" .
                "Action Required: Approve and process refund.";
        } else {
            $hrMessage .= "No refund applicable.";
        }

        $notificationModel->addNotification(
            5, // HR Manager ID
            'Manager',
            'Booking Cancellation - Action Required',
            $hrMessage,
            URLROOT . "/hr/refunds"
        );
    }

    /* ================= RESCHEDULE BOOKING ================= */
    public function rescheduleBooking()
    {
        // Request-based workflow: client submits reschedule request to HR for approval
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $newDate = trim($_POST['new_date'] ?? '');
        $reason = trim($_POST['reason'] ?? '');

        // Basic sanity check
        if (!$bookingId || !$newDate || strtotime($newDate) === false) {
            $_SESSION['error'] = "Please provide a valid booking ID and date.";
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        $clientId = AuthSession::profileId();
        $rrModel = $this->model('RescheduleRequestModel');

        // ============ COMPREHENSIVE VALIDATION ============
        // Use the new canReschedule() method which validates everything in order
        $validation = $rrModel->canReschedule($bookingId, $clientId, $newDate);

        if (!$validation['valid']) {
            // Validation failed - return with specific error message
            $_SESSION['error'] = $validation['error'];
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Extract the validated booking from the result
        $booking = $validation['booking'];

        // Additional validation: Check caretaker availability for the new date
        // EXCLUDE the current booking from availability check since we're rescheduling it
        $caretakerModel = $this->model('CaretakerModel');
        $available = $caretakerModel->getAvailableCaretakers(
            $booking['service_type'],
            $newDate,
            $booking['preferred_time'],
            $booking['basis'],
            $booking['duration'],
            '',
            $bookingId  // Exclude current booking from conflict check
        );

        $availableIds = array_column($available, 'id');
        if (!in_array($booking['caretaker_id'], $availableIds)) {
            $_SESSION['error'] = "The assigned caregiver is not available on the requested new date and time.";
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Check if caretaker is on leave during the new date
        $leaveModel = $this->model('HRLeaveModel');
        if ($leaveModel->isCaretakerOnLeave($booking['caretaker_id'], $newDate)) {
            $_SESSION['error'] = "The assigned caregiver is on leave during the requested date.";
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // ============ ALL VALIDATIONS PASSED - CREATE REQUEST ============
        $requestData = [
            'booking_id' => $bookingId,
            'client_id' => $clientId,
            'old_date' => $booking['booking_date'],
            'new_date' => $newDate,
            'reason' => $reason
        ];

        $rrModel->createRequest($requestData);

        // Update booking status to indicate reschedule is pending
        $this->clientModel->updateBookingStatus($bookingId, 'Reschedule_Requested');

        // Notify HR users about the new reschedule request
        require_once APPROOT . '/models/NotificationModel.php';
        $notif = new NotificationModel();
        $hrs = $notif->getHRUsers();

        if (!empty($hrs)) {
            $clientName = $_SESSION['user']['name'] ?? $_SESSION['user']['username'];
            $msg = "Client {$clientName} has requested to reschedule booking #{$bookingId} " .
                "from {$booking['booking_date']} to {$newDate}.";

            // Notify the first HR user (or loop through all if needed)
            $hrUser = $hrs[0];
            $notif->addNotification(
                $hrUser['id'],
                'Manager',
                'Reschedule Request',
                $msg,
                URLROOT . "/hr/rescheduleRequests"
            );
        }

        $_SESSION['success'] = "Reschedule request submitted successfully. HR will review and respond soon.";

        // Redirect back to the referring page (upcoming or ongoing bookings)
        $redirect = URLROOT . "/client/c_upcomingBookings";
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'c_ongoingBookings') !== false) {
            $redirect = URLROOT . "/client/c_ongoingBookings";
        }
        header("Location: " . $redirect);
        exit;
    }

    /* ================= PAYMENT PAGE ================= */

    public function c_makePayment()
    {
        $bookingId = $_GET['booking_id'] ?? null;
        $recurringPaymentId = $_GET['recurring_payment_id'] ?? null;
        $clientId = AuthSession::profileId();

        if (!$bookingId) {
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Validate booking ownership (prevents IDOR)
        $booking = $this->assertBookingOwnership($bookingId, $clientId, URLROOT . "/client/c_upcomingBookings");

        // Ensure PaymentController is available and compute advance
        require_once APPROOT . '/controllers/PaymentController.php';
        $payment_calc = PaymentController::calculatePaymentDetails($booking);
        $payment_calc['advance'] = $payment_calc['advance_amount'] ?? 0;
        $payment_calc['remaining'] = $payment_calc['remaining_balance'] ?? 0;
        $payment_calc['notes'] = $payment_calc['description'] ?? '';

        $recurringPayment = null;
        if (!empty($recurringPaymentId)) {
            $recurringPayment = $this->clientModel->getRecurringPaymentByIdForClient(
                (int)$recurringPaymentId,
                (int)AuthSession::profileId(),
                (int)$bookingId
            );

            if (!$recurringPayment || !in_array($recurringPayment['status'], ['pending', 'overdue'], true)) {
                $_SESSION['error'] = "Invalid recurring payment request";
                header("Location: " . URLROOT . "/client/c_upcomingBookings");
                exit;
            }
        }

        $this->view('client/c_makePayment', [
            'booking' => $booking,
            'payment_calc' => $payment_calc,
            'recurring_payment' => $recurringPayment
        ]);
    }

    public function c_cancelledBookings()
    {
        $clientId = AuthSession::profileId();

        // Use the correct model method
        $bookings = $this->clientModel->getCancelledBookings($clientId);

        $this->view("client/c_cancelledBookings", ['bookings' => $bookings]);
    }


    /* ================= PAYMENT SUCCESS ================= */
    public function paymentSuccess()
    {
        $bookingId = $_GET['booking_id'] ?? null;
        $clientId = AuthSession::profileId();

        if (!$bookingId) {
            header("Location: " . URLROOT . "/client/c_dashboard");
            exit;
        }

        // Validate booking ownership (prevents IDOR)
        $this->assertBookingOwnership($bookingId, $clientId, URLROOT . "/client/c_dashboard");

        $this->clientModel->markAsPaid($bookingId);

        header("Location: " . URLROOT . "/client/c_paymentSuccess");
        exit;
    }

    private function calcTotalPayment(
        string $serviceType,
        string $basis,
        int $duration,
        string $preferredTime,
        int $customizationHours,
        string $customizationApply = 'once' // once | per_unit
    ): float {

        $serviceBasisRates = [
            'Elder Care' => [
                'Monthly' => 50000,
                'Yearly'  => 550000,
            ],
            'Babysitter' => [
                'Daily'   => 2200,
                'Monthly' => 45000,
                'Yearly'  => 500000,
            ],
            'Maid' => [
                'Hourly'  => 500,
                'Daily'   => 2000,
                'Monthly' => 38000,
                'Yearly'  => 420000,
            ],
        ];

        $timePriceModifier = [
            "Full Time (8am - 5pm)" => 1.0,
            "Morning (8am - 12pm)"  => 0.6,
            "Evening (1pm - 5pm)"   => 0.6,
            "Night (6pm - 10pm)"    => 1.2
        ];

        $duration = max(1, (int)$duration);
        $customizationHours = max(0, (int)$customizationHours);

        $rate = $serviceBasisRates[$serviceType][$basis] ?? 0;
        $modifier = $timePriceModifier[$preferredTime] ?? 1.0;

        // customization multiplier (same logic you had in c_book)
        $customizationMultiplier = 1;
        if ($customizationApply === 'per_unit') {
            switch ($basis) {
                case "Hourly":
                    $customizationMultiplier = 1;
                    break;
                case "Daily":
                    $customizationMultiplier = $duration;
                    break;
                case "Monthly":
                    $customizationMultiplier = $duration * 30;
                    break;
                case "Yearly":
                    $customizationMultiplier = $duration * 365;
                    break;
                default:
                    $customizationMultiplier = 1;
            }
        }

        $customizationFee = $customizationHours * 300 * $customizationMultiplier;

        $base = ($rate * $duration * $modifier);

        return $base + $customizationFee;
    }

    public function c_book()
    {
        $caretakerModel = $this->model('CaretakerModel');

        // Get caretaker ID from URL
        $caretakerId = $_GET['id'] ?? null;
        if (!$caretakerId) {
            $_SESSION['error'] = "No caretaker selected. Please select a caretaker from the search results.";
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }

        // Fetch caretaker details
        $caretaker = $caretakerModel->getCaretakerById($caretakerId);

        // Check if caretaker exists
        if (!$caretaker || empty($caretaker)) {
            $_SESSION['error'] = "Selected caretaker not found. They may have been removed from the system.";
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }

        $caretaker = (array) $caretaker;

        // Normalize incoming basis values from search flow (e.g., "hourly" -> "Hourly").
        $rawBasis = trim((string)($_GET['basis'] ?? ''));
        $basisMap = [
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
        ];
        $normalizedBasis = $basisMap[strtolower($rawBasis)] ?? $rawBasis;

        $serviceTypeRaw = trim((string)($_GET['service_type'] ?? ($caretaker['service_type'] ?? '')));
        $serviceTypeMap = [
            'elder care' => 'Elder Care',
            'babysitter' => 'Babysitter',
            'maid' => 'Maid',
            'disability support' => 'Disability Support',
        ];
        $serviceType = $serviceTypeMap[strtolower($serviceTypeRaw)] ?? $serviceTypeRaw;
        // Pre-fill data from GET parameters (from search popup)
        $prefill = [
            'basis'    => $normalizedBasis,
            'duration' => intval($_GET['duration'] ?? 1),
            'date'     => $_GET['date'] ?? '',
            'time'     => $_GET['time'] ?? '',
            'customization_hours' => intval($_GET['customization_hours'] ?? 0),
            'customization_apply' => $_GET['customization_apply'] ?? 'per_unit',
            'service_type' => $serviceType,
        ];
        $total_payment = $this->calcTotalPayment(
            (string)$prefill['service_type'],
            (string)$prefill['basis'],
            (int)$prefill['duration'],
            (string)$prefill['time'],
            (int)$prefill['customization_hours'],
            (string)$prefill['customization_apply']
        );
        // Pass data to view
        $data = [
            'caretaker'      => $caretaker,
            'prefill'        => $prefill,
            'serviceOptions' => [
                "Elder Care" => ["Monthly", "Yearly"],
                "Babysitter" => ["Daily", "Monthly", "Yearly"],
                "Maid"       => ["Hourly", "Daily", "Monthly", "Yearly"],
                "Disability Support" => ["Hourly", "Daily", "Monthly", "Yearly"],
            ],
            'total_payment'  => $total_payment // <-- automatically calculated
        ];

        $this->view('client/c_book', $data);
    }



    public function bookCaretaker()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }
        $service_type    = trim((string)($_POST['service_type'] ?? ''));
        $rawBasis        = trim((string)($_POST['basis'] ?? ''));
        $basisMap = [
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
        ];
        $basis           = $basisMap[strtolower($rawBasis)] ?? $rawBasis;
        $duration        = intval($_POST['duration']);
        $preferred_time  = trim((string)($_POST['preferred_time'] ?? ''));
        $booking_date    = trim((string)($_POST['booking_date'] ?? ''));

        // Validate daily booking limit (max 30 days)
        require_once APPROOT . '/controllers/PaymentController.php';
        $validation = PaymentController::validateBooking([
            'basis' => $basis,
            'duration' => $duration
        ]);

        if (!$validation['valid']) {
            $_SESSION['error'] = $validation['message'];
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }

        $district        = $_POST['district'];
        $street          = $_POST['street'];
        $address_line1   = $_POST['address_line1'];
        $address_line2   = $_POST['address_line2'];
        $postal_code     = $_POST['postal_code'];
        $customization   = $_POST['customization'];
        $customizationHours = intval($_POST['customization_hours'] ?? 0);
        $caretaker_id    = $_POST['caretaker_id'] ?? 0;
        $client_id       = AuthSession::profileId();

        $returnToBookUrl = URLROOT . "/client/c_book?id=" . (int)$caretaker_id
            . "&service_type=" . rawurlencode((string)$service_type)
            . "&basis=" . rawurlencode((string)$basis)
            . "&duration=" . rawurlencode((string)$duration)
            . "&date=" . rawurlencode((string)$booking_date)
            . "&time=" . rawurlencode((string)$preferred_time);

        // Detailed validation with specific error messages
        if ($caretaker_id <= 0) {
            $_SESSION['error'] = "Invalid caretaker selection. Please select a caretaker from the search results.";
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }

        if ($client_id <= 0) {
            $_SESSION['error'] = "Session expired. Please log in again.";
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        if (empty($service_type) || empty($basis) || empty($booking_date) || empty($preferred_time)) {
            $_SESSION['error'] = "Please fill in all required booking fields (service, basis, date, and time).";
            header("Location: " . $returnToBookUrl);
            exit;
        }

        // Verify caretaker still exists
        $caretakerModel = $this->model('CaretakerModel');
        $caretakerCheck = $caretakerModel->getCaretakerById($caretaker_id);
        if (!$caretakerCheck) {
            $_SESSION['error'] = "Selected caretaker no longer available. Please choose another caretaker.";
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }
        $allowed = [
            "Elder Care" => ["Monthly", "Yearly"],
            "Babysitter" => ["Daily", "Monthly", "Yearly"],
            "Maid"       => ["Hourly", "Daily", "Monthly", "Yearly"],
            "Disability Support" => ["Hourly", "Daily", "Monthly", "Yearly"],
        ];

        if (!isset($allowed[$service_type]) || !in_array($basis, $allowed[$service_type], true)) {
            $_SESSION['error'] = "Invalid basis selected for this service.";
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }

        // Final server-side overlap checks (cannot be bypassed via dev tools / forged requests).
        $caretakerConflict = $this->clientModel->hasCaretakerBookingConflict(
            (int)$caretaker_id,
            (string)$booking_date,
            (string)$preferred_time,
            (string)$basis,
            (int)$duration
        );

        if ($caretakerConflict) {
            $_SESSION['error'] = "Selected caregiver is no longer available for the chosen date/time. Please choose another slot or caregiver.";
            header("Location: " . $returnToBookUrl);
            exit;
        }

        $customizationApply = $_POST['customization_apply'] ?? 'per_unit';
        $total_payment = $this->calcTotalPayment(
            $service_type,
            $basis,
            (int)$duration,
            (string)$preferred_time,
            (int)$customizationHours,
            (string)$customizationApply
        );
        $base_without_custom = $this->calcTotalPayment(
            $service_type,
            $basis,
            (int)$duration,
            (string)$preferred_time,
            0,
            'once'
        );

        $customizationFee = $total_payment - $base_without_custom;

        // Calculate payment details
        $paymentDetails = PaymentController::calculatePaymentDetails([
            'basis' => $basis,
            'duration' => $duration,
            'total_payment' => $total_payment,
            'service_start_date' => $booking_date
        ]);

        // ---- Store booking ----
        $bookingData = [
            'client_id'      => $client_id,
            'caretaker_id'   => $caretaker_id,
            'service_type'   => $service_type,
            'basis'          => $basis,
            'duration'       => $duration,
            'preferred_time' => $preferred_time,
            'district'       => $district,
            'street'         => $street,
            'address_line1'  => $address_line1,
            'address_line2'  => $address_line2,
            'postal_code'    => $postal_code,
            'booking_date'   => $booking_date,
            'service_start_date' => $booking_date,
            'customization'  => $customization,
            'customization_hours' => $customizationHours,
            'customization_price' => $customizationFee,
            'total_payment'  => $total_payment,
            'status'         => 'Requested',
            'advance_months' => $paymentDetails['advance_months'],
            'total_months'   => $paymentDetails['total_months'],
            'advance_balance' => $paymentDetails['advance_balance']
        ];

        $bookingId = $this->clientModel->createBooking($bookingData);

        if (!$bookingId) {
            $_SESSION['error'] = "Failed to create booking. Please try again.";
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }

        require_once APPROOT . '/models/NotificationModel.php';
        $notifModel = new NotificationModel();
        $hr_users = $notifModel->getHRUsers();
        if (!empty($hr_users)) {

            // ✅ get booking summary for message
            $summary = $this->clientModel->getBookingSummaryForNotification($bookingId);

            $msg = "New booking placed.\n";
            if ($summary) {
                $msg .= "Booking #{$summary['booking_id']} | {$summary['service_type']}| \n";
                $msg .= "Client: {$summary['client_name']} ({$summary['client_email']}) |\n";
                $msg .= "Date: {$summary['booking_date']} | Time: {$summary['preferred_time']} | \n";
                $msg .= "Duration: {$summary['duration']} {$summary['basis']} | \n";
                $msg .= "Location: {$summary['district']}, {$summary['street']} | \n";
                $msg .= "Total: LKR " . number_format((float)$summary['total_payment'], 0) . "\n";
                $msg .= "Caretaker: " . ($summary['caretaker_name'] ?? "Not assigned") . "\n";
            } else {
                $msg .= "Booking ID: {$bookingId}";
            }

            $hr_user = $hr_users[0];

            // ✅ best: link directly to a booking details page
            $notifModel->addNotification(
                $hr_user['id'],
                'Manager',
                'New Booking Request',
                $msg,
                URLROOT . "/hr/hr_pending_request?booking_id=" . $bookingId
            );
        }

        header("Location: " . URLROOT . "/client/c_bookingConfirm?booking_id=" . $bookingId);
        exit;
    }

    public function c_ctprofileview()
    {
        if (!isset($_GET['id'])) {
            header("Location: index.php?url=client/c_find1");
            exit;
        }

        $caretakerModel = $this->model('CaretakerModel');
        $caretaker = $caretakerModel->getCaretakerById($_GET['id']);

        if (!$caretaker) {
            die("Caretaker not found");
        }

        $this->view("client/c_ctprofileview", [
            'caretaker' => $caretaker
        ]);
    }


    public function c_bookingConfirm()
    {
        if (!isset($_GET['booking_id'])) {
            header("Location: " . URLROOT . "/client/c_dashboard");
            exit;
        }

        $bookingId = $_GET['booking_id'];
        $clientId = AuthSession::profileId();

        // Validate booking ownership (prevents IDOR)
        $booking = $this->assertBookingOwnership($bookingId, $clientId, URLROOT . "/client/c_dashboard");

        $this->view("client/c_bookingConfirm", ['booking' => $booking]);
    }

    public function c_paymentPage()
    {
        $this->view("client/c_paymentPage");
    }

    /* ================= PROCESS PAYMENT ================= */
    public function processPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        $bookingId = $_POST['booking_id'] ?? null;
        $recurringPaymentId = $_POST['recurring_payment_id'] ?? null;
        $clientId = AuthSession::profileId();
        $amount = $_POST['amount'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? null;

        $bookingId = is_string($bookingId) ? trim($bookingId) : $bookingId;
        $paymentMethod = is_string($paymentMethod) ? trim($paymentMethod) : $paymentMethod;

        if (empty($bookingId) || $amount === null || $amount === '' || empty($paymentMethod)) {
            $_SESSION['error'] = "Invalid payment data";
            header("Location: " . URLROOT . "/client/c_makePayment?booking_id=" . $bookingId);
            exit;
        }

        // Validate booking ownership (prevents IDOR)
        $booking = $this->assertBookingOwnership($bookingId, $clientId, URLROOT . "/client/c_upcomingBookings");

        $paymentType = 'advance';
        $dueDate = null;

        if (!empty($recurringPaymentId)) {
            $recurringPayment = $this->clientModel->getRecurringPaymentByIdForClient(
                (int)$recurringPaymentId,
                (int)$clientId,
                (int)$bookingId
            );

            if (!$recurringPayment || !in_array($recurringPayment['status'], ['pending', 'overdue'], true)) {
                $_SESSION['error'] = "Recurring payment not found or already paid";
                header("Location: " . URLROOT . "/client/c_upcomingBookings");
                exit;
            }

            // Server-authoritative amount for recurring installment.
            $amount = (float)$recurringPayment['amount'];
            $dueDate = $recurringPayment['due_date'];
            $paymentType = 'reminder';
        } else {
            // Calculate advance on server (do not trust client amount)
            require_once APPROOT . '/controllers/PaymentController.php';
            $payment_calc = PaymentController::calculatePaymentDetails($booking);
            $payment_calc['advance'] = $payment_calc['advance_amount'] ?? 0;
            $amount = $payment_calc['advance'] ?? $amount;
        }

        // Save payment record
        $paymentData = [
            'booking_id' => $bookingId,
            'client_id' => $clientId,
            'caretaker_id' => $booking['caretaker_id'],
            'total_booking_amount' => $booking['total_payment'],
            'customization_price' => $booking['customization_price'] ?? 0,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_type' => $paymentType,
            'due_date' => $dueDate
        ];

        $paymentId = $this->clientModel->savePayment($paymentData);

        if ($paymentId) {
            if (!PayHereHelper::isConfigured()) {
                $_SESSION['error'] = "PayHere sandbox configuration is missing";
                header("Location: " . URLROOT . "/client/c_makePayment?booking_id=" . $bookingId);
                exit;
            }

            $orderId = 'CMA-' . $paymentId . '-' . time();
            $this->clientModel->setPayHereOrderId((int)$paymentId, $orderId);

            $amountFormatted = PayHereHelper::formatAmount($amount);
            $currency = defined('PAYHERE_CURRENCY') ? PAYHERE_CURRENCY : 'LKR';
            $merchantId = defined('PAYHERE_MERCHANT_ID') ? PAYHERE_MERCHANT_ID : '';
            $merchantSecret = defined('PAYHERE_MERCHANT_SECRET') ? PAYHERE_MERCHANT_SECRET : '';
            $returnUrl = defined('PAYHERE_RETURN_URL') ? PAYHERE_RETURN_URL : '';
            $cancelUrl = defined('PAYHERE_CANCEL_URL') ? PAYHERE_CANCEL_URL : '';
            $notifyUrl = defined('PAYHERE_NOTIFY_URL') ? PAYHERE_NOTIFY_URL : '';
            $gatewayUrl = defined('PAYHERE_API_URL') ? PAYHERE_API_URL : '';

            if ($merchantId === '' || $merchantSecret === '' || $returnUrl === '' || $cancelUrl === '' || $notifyUrl === '' || $gatewayUrl === '') {
                $_SESSION['error'] = "PayHere configuration is incomplete";
                header("Location: " . URLROOT . "/client/c_makePayment?booking_id=" . $bookingId);
                exit;
            }

            $hash = PayHereHelper::buildCheckoutHash($merchantId, $orderId, $amountFormatted, $currency, $merchantSecret);

            $fullName = trim((string)($_SESSION['user']['name'] ?? 'Client User'));
            $nameParts = preg_split('/\s+/', $fullName);
            $firstName = $nameParts[0] ?? 'Client';
            $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'User';

            $payhereData = [
                'merchant_id' => $merchantId,
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
                'notify_url' => $notifyUrl,
                'order_id' => $orderId,
                'items' => 'Booking #' . $bookingId . ' - ' . ($paymentType === 'advance' ? 'Advance Payment' : 'Recurring Installment'),
                'currency' => $currency,
                'amount' => $amountFormatted,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => (string)($_SESSION['user']['email'] ?? 'client@example.com'),
                'phone' => (string)($_SESSION['user']['phone'] ?? '0000000000'),
                'address' => (string)($booking['address_line1'] ?? 'Not Provided'),
                'city' => (string)($booking['district'] ?? 'Colombo'),
                'country' => 'Sri Lanka',
                'hash' => $hash,
            ];

            $this->view('client/c_payhereRedirect', [
                'gateway_url' => $gatewayUrl,
                'payhere' => $payhereData
            ]);
            return;
        } else {
            $_SESSION['error'] = "Payment processing failed";
            header("Location: " . URLROOT . "/client/c_makePayment?booking_id=" . $bookingId);
            exit;
        }
    }
    public function c_settings()
    {
        if (!AuthSession::isLoggedIn()) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $user = $_SESSION['user']; // <--- assign it here

        $this->view("client/c_settings", ['user' => $user]);
    }

    public function editClientDetails()
    {
        if (!AuthSession::hasRole('client')) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $user = $_SESSION['user'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $profileImage = $user['profile_image'] ?? 'default.png';

            if (!empty($_FILES['profile_image']['name'])) {

                $fileName = time() . "_" . basename($_FILES['profile_image']['name']);
                $targetPath = APPROOT . "/../public/uploads/" . $fileName;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                    $profileImage = $fileName;
                }
            }

            // ADD IMAGE INTO POST DATA
            $_POST['profile_image'] = $profileImage;

            // UPDATE CLIENT
            $this->clientModel->updateClient($user['id'], $_POST);

            // REFRESH SESSION
            $_SESSION['user'] = $this->clientModel->getClientById($user['id']);

            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: " . URLROOT . "/client/c_settings");
            exit();
        }
    }

    public function editPasswordDetails()
    {
        if (!AuthSession::hasRole('client')) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $user = $_SESSION['user'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['new-password'] !== $_POST['confirm-password']) {
                die("Error: Passwords do not match.");
            }

            $_POST['password'] = password_hash($_POST['new-password'], PASSWORD_DEFAULT);

            $this->clientModel->updateClientPassword($user['id'], $_POST['password']);


            $_SESSION['success'] = "Password updated successfully!";
            header("Location: " . URLROOT . "/client/c_settings");
            exit();
        }
    }


    public function c_contactCT()
    {
        $caretaker = null;
        $clientId = AuthSession::profileId();
        $bookingId = $_GET['booking_id'] ?? null;
        $caretakerId = null;
        $hasAccess = false;

        // Primary method: Get from booking (with payment verification)
        if ($bookingId) {
            $booking = $this->clientModel->getBookingById($bookingId);

            // Security Check 1: Verify booking belongs to logged-in client
            if ($booking && (int)$booking['client_id'] === (int)$clientId) {
                // Security Check 2: Verify advance payment has been made
                $advancePaidStatuses = ['Advance_Paid', 'Accepted', 'Reschedule_Requested', 'Change_Requested', 'Completed', 'Paid'];

                if (in_array($booking['status'], $advancePaidStatuses)) {
                    $caretakerId = $booking['caretaker_id'];
                    $hasAccess = true;
                } else {
                    $_SESSION['error'] = "Caretaker contact details are only available after advance payment has been made.";
                }
            } else {
                $_SESSION['error'] = "Unauthorized access to booking details.";
            }
        }

        // Fallback: Get from most recent paid booking if no booking_id provided
        if (!$hasAccess && !$bookingId) {
            $recentBookings = $this->clientModel->getRecentBookings($clientId);
            foreach ($recentBookings as $recentBooking) {
                $advancePaidStatuses = ['Advance_Paid', 'Accepted', 'Reschedule_Requested', 'Change_Requested', 'Completed', 'Paid'];
                if (in_array($recentBooking['status'], $advancePaidStatuses)) {
                    $caretakerId = $recentBooking['caretaker_id'];
                    $hasAccess = true;
                    break;
                }
            }

            if (!$hasAccess) {
                $_SESSION['error'] = "No caretaker details available. Please make advance payment for a booking first.";
            }
        }

        // Get caretaker details if access is granted
        if ($hasAccess && $caretakerId) {
            $caretakerModel = $this->model('CaretakerModel');
            $caretaker = $caretakerModel->getCaretakerById($caretakerId);
        }

        $this->view("client/c_contactCT", ['caretaker' => $caretaker]);
    }

    public function c_complaintlist()
    {
        $complaintModel = $this->model('ComplaintModel');
        $clientModel = $this->model('ClientModel');
        $client_name = $_SESSION['user']['name'];
        $client_id = AuthSession::profileId();
        $complaints = $complaintModel->getComplaintsByClient($client_name);
        $caretakers = $clientModel->getBookedCaretakersByClient($client_id);
        $data = [
            'complaints' => $complaints,
            'caretakers' => $caretakers
        ];
        $this->view("client/c_complaintlist", $data);
    }
    public function c_announcement()
    {
        $announcementModel = $this->model('AnnouncementModel');
        $announcements = $announcementModel->getClientAnnouncements();

        $this->view("client/c_announcement", $announcements);
    }
}
