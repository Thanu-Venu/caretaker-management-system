<?php

class ClientController extends Controller
{
    private $clientModel;
    private $serviceOptions;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'client') {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $this->clientModel = $this->model('ClientModel');
        $user = $this->clientModel->getClientById($_SESSION['user']['id']);

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


    public function c_dashboard()
    {
        $clientId = $_SESSION['user']['id'];

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
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'client') {
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
        $clientId = $_SESSION['user']['id'] ?? null;
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

        $bookingId = $_POST['booking_Id'];
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
            'client_id'    => $_SESSION['user']['id'],
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

        if (!$booking_id) {
            $_SESSION['error'] = "No booking selected";
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Get booking details
        $booking = $this->clientModel->getBookingById($booking_id);

        if (!$booking) {
            $_SESSION['error'] = "Booking not found";
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Calculate payment info
        require_once APPROOT . '/controllers/PaymentController.php';
        $payment_calc = PaymentController::calculateAdvanceFromBooking($booking);

        $this->view("client/c_payment", [
            'booking' => $booking,
            'payment_calc' => $payment_calc
        ]);
    }

    public function c_paymentHistory()
    {
        $clientId = $_SESSION['user']['id'] ?? null;
        if (!$clientId) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $payments = $this->clientModel->getPaymentsByClient($clientId);
        $this->view("client/c_paymentHistory", ['payments' => $payments]);
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
        $clientId = $_SESSION['user']['id'];
        $bookings = $this->clientModel->getPastBookingsWithFeedback($clientId);
        $this->view("client/c_pastBookings", ['bookings' => $bookings]);
    }


    public function c_upcomingBookings()
    {
        $clientId = $_SESSION['user']['id'];
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
        $clientId = $_SESSION['user']['id'];
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
        $clientId = (int)($_SESSION['user']['id'] ?? 0);

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

        if (!$bookingId || empty($reason)) {
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Call model
        $this->clientModel->cancelBooking($bookingId, $reason);

        // Redirect back to upcoming bookings
        header("Location: " . URLROOT . "/client/c_upcomingBookings");
        exit;
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

        $clientId = $_SESSION['user']['id'];
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
        $caretakerModel = $this->model('CaretakerModel');
        $available = $caretakerModel->getAvailableCaretakers(
            $booking['service_type'],
            $newDate,
            $booking['preferred_time'],
            $booking['basis'],
            $booking['duration']
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
        if (!$bookingId) {
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        $booking = $this->clientModel->getBookingById($bookingId);
        if (!$booking) {
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Ensure PaymentController is available and compute advance
        require_once APPROOT . '/controllers/PaymentController.php';
        $payment_calc = PaymentController::calculateAdvanceFromBooking($booking);

        $this->view('client/c_makePayment', [
            'booking' => $booking,
            'payment_calc' => $payment_calc
        ]);
    }

    public function c_cancelledBookings()
    {
        $clientId = $_SESSION['user']['id'];

        // Use the correct model method
        $bookings = $this->clientModel->getCancelledBookings($clientId);

        $this->view("client/c_cancelledBookings", ['bookings' => $bookings]);
    }


    /* ================= PAYMENT SUCCESS ================= */
    public function paymentSuccess()
    {
        $bookingId = $_GET['booking_id'];

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
                'Monthly' => 45000,
                'Yearly'  => 500000,
            ],
            'Babysitter' => [
                'Daily'   => 3200,
                'Monthly' => 42000,
                'Yearly'  => 480000,
            ],
            'Maid' => [
                'Hourly'  => 500,
                'Daily'   => 3000,
                'Monthly' => 38000,
                'Yearly'  => 450000,
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
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }

        // Fetch caretaker details
        $caretaker = (array) $caretakerModel->getCaretakerById($caretakerId);
        // Pre-fill data from GET parameters (from search popup)
        $prefill = [
            'basis'    => $_GET['basis'] ?? '',
            'duration' => intval($_GET['duration'] ?? 1),
            'date'     => $_GET['date'] ?? '',
            'time'     => $_GET['time'] ?? '',
            'customization_hours' => intval($_GET['customization_hours'] ?? 0),
            'customization_apply' => $_GET['customization_apply'] ?? 'once',
            'service_type' => $_GET['service_type'] ?? ($caretaker->service_type ?? ''),
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
        $service_type    = $_POST['service_type'];
        $basis           = $_POST['basis'];
        $duration        = intval($_POST['duration']);
        $preferred_time  = $_POST['preferred_time'];
        $booking_date    = $_POST['booking_date'];
        $district        = $_POST['district'];
        $street          = $_POST['street'];
        $address_line1   = $_POST['address_line1'];
        $address_line2   = $_POST['address_line2'];
        $postal_code     = $_POST['postal_code'];
        $customization   = $_POST['customization'];
        $customizationHours = intval($_POST['customization_hours'] ?? 0);
        $caretaker_id    = $_POST['caretaker_id'];
        $client_id       = $_SESSION['user']['id'];

        if ($caretaker_id <= 0 || $client_id <= 0 || $service_type === '' || $basis === '' || $booking_date === '' || $preferred_time === '') {
            $_SESSION['error'] = "Missing booking information.";
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }
        $allowed = [
            "Elder Care" => ["Monthly", "Yearly"],
            "Babysitter" => ["Daily", "Monthly", "Yearly"],
            "Maid"       => ["Hourly", "Daily", "Monthly", "Yearly"],
        ];

        if (!isset($allowed[$service_type]) || !in_array($basis, $allowed[$service_type], true)) {
            $_SESSION['error'] = "Invalid basis selected for this service.";
            header("Location: " . URLROOT . "/client/c_find1");
            exit;
        }
        $customizationApply = $_POST['customization_apply'] ?? 'once';
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
            'customization'  => $customization,
            'customization_hours' => $customizationHours,
            'customization_price' => $customizationFee,
            'total_payment'  => $total_payment,
            'status'         => 'Requested'
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
        $booking = $this->clientModel->getBookingById($bookingId);

        if (!$booking) {
            die("Booking not found");
        }

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
        $clientId = $_SESSION['user']['id'];
        $amount = $_POST['amount'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? null;

        $bookingId = is_string($bookingId) ? trim($bookingId) : $bookingId;
        $paymentMethod = is_string($paymentMethod) ? trim($paymentMethod) : $paymentMethod;

        if (empty($bookingId) || $amount === null || $amount === '' || empty($paymentMethod)) {
            $_SESSION['error'] = "Invalid payment data";
            header("Location: " . URLROOT . "/client/c_makePayment?booking_id=" . $bookingId);
            exit;
        }

        // Get booking details
        $booking = $this->clientModel->getBookingById($bookingId);
        if (!$booking) {
            $_SESSION['error'] = "Booking not found";
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        // Calculate advance on server (do not trust client amount)
        require_once APPROOT . '/controllers/PaymentController.php';
        $payment_calc = PaymentController::calculateAdvanceFromBooking($booking);
        $amount = $payment_calc['advance'] ?? $amount;

        // Save payment record
        $paymentData = [
            'booking_id' => $bookingId,
            'client_id' => $clientId,
            'caretaker_id' => $booking['caretaker_id'],
            'total_booking_amount' => $booking['total_payment'],
            'customization_price' => $booking['customization_price'] ?? 0,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'payment_type' => 'advance'
        ];

        $paymentId = $this->clientModel->savePayment($paymentData);

        if ($paymentId) {
            // Update booking status to Advance_Paid
            $this->clientModel->updateBookingStatus($bookingId, 'Advance_Paid');

            // Send notification to HR (Manager) - only to first/primary manager
            require_once APPROOT . '/models/NotificationModel.php';
            $notifModel = new NotificationModel();
            $hr_users = $notifModel->getHRUsers();

            if (!empty($hr_users)) {
                $clientName = $_SESSION['user']['name'] ?? $_SESSION['user']['username'];
                $message = "Advance payment received from client {$clientName} (ID: {$clientId}) - Rs. " . number_format($amount, 2) . " for booking #{$bookingId}.";

                // Send to only the first manager
                $hr_user = $hr_users[0];
                $notifModel->addNotification(
                    $hr_user['id'],
                    'Manager',
                    'Advance Payment Received',
                    $message,
                    URLROOT . "/hr/pendingPayments"
                );
            }

            $_SESSION['success'] = "Payment submitted successfully! Waiting for HR approval.";
            header("Location: " . URLROOT . "/client/c_paymentSuccess?payment_id=" . $paymentId);
            exit;
        } else {
            $_SESSION['error'] = "Payment processing failed";
            header("Location: " . URLROOT . "/client/c_makePayment?booking_id=" . $bookingId);
            exit;
        }
    }
    public function c_settings()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $user = $_SESSION['user']; // <--- assign it here

        $this->view("client/c_settings", ['user' => $user]);
    }

    public function editClientDetails()
    {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'client') {
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
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'client') {
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
        $paymentId = $_GET['payment_id'] ?? null;
        $caretakerId = $_GET['caretaker_id'] ?? null;
        $bookingId = $_GET['booking_id'] ?? null;

        // Try to get caretaker from payment
        if ($paymentId) {
            $payment = $this->clientModel->getPaymentById($paymentId);
            if ($payment && !empty($payment['caretaker_id'])) {
                $caretakerId = $payment['caretaker_id'];
            }
        }

        // Try to get caretaker from booking
        if (!$caretakerId && $bookingId) {
            $booking = $this->clientModel->getBookingById($bookingId);
            if ($booking && !empty($booking['caretaker_id'])) {
                $caretakerId = $booking['caretaker_id'];
            }
        }

        // If still no caretaker_id, get from most recent booking
        if (!$caretakerId) {
            $clientId = $_SESSION['user']['id'];
            $recentBookings = $this->clientModel->getRecentBookings($clientId);
            if (!empty($recentBookings[0]['caretaker_id'])) {
                $caretakerId = $recentBookings[0]['caretaker_id'];
            }
        }

        if ($caretakerId) {
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
        $client_id = $_SESSION['user']['id'];
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
