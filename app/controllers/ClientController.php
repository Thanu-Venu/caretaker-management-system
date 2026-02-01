<?php

class ClientController extends Controller
{
    private $clientModel;
    private $notificationModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'client') {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        // Re-validate user from database
        $this->clientModel = $this->model('ClientModel');
        $this->notificationModel = $this->model('NotificationModel');
        $user = $this->clientModel->getClientById($_SESSION['user']['id']);

        if (!$user) { // user deleted
            session_destroy();
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        // Update session with latest data
        $_SESSION['user'] = $user;
    }


    public function c_dashboard()
    {
        $this->view("client/c_dashboard");
    }
    public function c_profile()
    {

        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'client') {

            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $user = $_SESSION['user'];

        // pass user info to the view
        $this->view("client/c_profile", ['user' => $user]);
    }

    public function c_find()
    {
        $caretakerModel = $this->model('CaretakerModel');
        $caretakers = $caretakerModel->getCaretakers();

        $this->view("client/c_find", [
            'caretakers' => $caretakers
        ]);
    }

    public function c_feedback()
    {
        $this->view("client/c_feedback");
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
            'booking_id' => $bookingId,
            'client_id' => $_SESSION['user']['id'],
            'caretaker_id' => $caretakerId,
            'rating' => $_POST['rating'],
            'feedback' => $_POST['feedback']
        ];

        $clientModel->addFeedback($data);
        // ✅ Notify ALL admins about new feedback
        $this->notificationModel->notifyAdmins(
            "New Feedback",
            "New feedback received (Booking ID: {$bookingId}, Rating: {$data['rating']}).",
            URLROOT . "/admin/ad_feedback"
            // If your admin URLs use front controller:
            // URLROOT . "/public/index.php?url=admin/ad_feedback"
        );


        $_SESSION['success'] = "Feedback submitted successfully!";
        header("Location: " . URLROOT . "/client/c_pastBookings");
        exit;
    }
    public function c_payment()
    {
        $this->view("client/c_payment");
    }

    public function c_paymentHistory()
    {
        $this->view("client/c_paymentHistory");
    }


    public function c_paymentSuccess()
    {
        $this->view("client/c_paymentSuccess");
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

        $this->view("client/c_upcomingBookings", ['bookings' => $bookings]);
    }

    public function upcomingBookings()
    {
        // Load the model
        $bookings = $this->clientModel->getBookingsByStatus('upcoming'); // fetch only upcoming bookings

        $data = [
            'bookings' => $bookings
        ];

        // Load the view and pass data
        $this->view('client/upcomingBookings', $data);
    }


    /* ================= CANCEL BOOKING ================= */
    public function cancelBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        $bookingId = $_POST['booking_id'] ?? null;
        $reason = $_POST['reason'] ?? '';

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/client/c_upcomingBookings");
            exit;
        }

        $bookingId = $_POST['booking_id'];
        $newDate = $_POST['new_date'];
        $newTime = $_POST['new_time'];
        $newDuration = $_POST['new_duration'];

        $this->clientModel->rescheduleBooking($bookingId, $newDate, $newTime, $newDuration);

        // Redirect back to upcoming bookings page
        header("Location: " . URLROOT . "/client/c_upcomingBookings");
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

        $this->view('client/c_makePayment', [
            'booking' => $booking
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
    public function c_book()
    {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'client') {
            header("Location: " . URLROOT . "/public/?url=auth/login");
            exit;
        }

        if (!isset($_GET['id'])) {
            die("Caretaker ID missing");
        }

        $caretakerId = (int) $_GET['id'];

        // Use the already-created model (no need to create again)
        $caretaker = $this->clientModel->getCaretakerById($caretakerId);

        if (!$caretaker) {
            die("Caretaker not found");
        }

        $serviceOptions = [
            "Elder Care" => ["Monthly", "Yearly"],
            "Babysitter" => ["Daily", "Monthly", "Yearly"],
            "Maid" => ["Hourly", "Daily", "Monthly", "Yearly"],
        ];

        $this->view('client/c_book', [
            'caretaker' => $caretaker,
            'serviceOptions' => $serviceOptions,

        ]);
    }


    public function bookCaretaker()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . URLROOT . "/client/c_find");
            exit;
        }

        $caretaker_id = (int) ($_POST['caretaker_id'] ?? 0);

        $ct = $this->clientModel->getCaretakerById($caretaker_id);
        if (!$ct) {
            $_SESSION['error'] = "Caretaker not found.";
            header("Location: " . URLROOT . "/client/c_find");
            exit;
        }

        $basis = $_POST['basis'] ?? '';
        $duration = (int) ($_POST['duration'] ?? 0);
        $preferred_time = $_POST['preferred_time'] ?? '';
        $booking_date = $_POST['booking_date'] ?? '';
        $district = $ct['location'];
        $street = $_POST['street'] ?? '';
        $address_line1 = $_POST['address_line1'] ?? '';
        $address_line2 = $_POST['address_line2'] ?? '';
        $postal_code = $_POST['postal_code'] ?? '';
        $customization = $_POST['customization'] ?? '';
        $caretaker_id = (int) ($_POST['caretaker_id'] ?? 0);
        $client_id = (int) ($_SESSION['user']['id'] ?? 0);

        if (!$caretaker_id || !$client_id || !$basis || $duration <= 0 || !$preferred_time || !$booking_date || !$district || !$address_line1) {
            $_SESSION['error'] = "Please fill all required fields.";
            header("Location: " . URLROOT . "/public/?url=client/c_book&id=" . $caretaker_id);
            exit;
        }

        $caretakerDistrict = strtolower(trim($ct['location'] ?? ''));
        $clientDistrict = strtolower(trim($district));

        if ($caretakerDistrict && $clientDistrict && $caretakerDistrict !== $clientDistrict) {
            $_SESSION['error'] = "This caretaker is available only in " . ($ct['location'] ?? 'their district') . ".";
            header("Location: " . URLROOT . "/public/?url=client/c_book&id=" . $caretaker_id);
            exit;
        }

        $service_type = $ct['service_type'];


        $minDate = date('Y-m-d', strtotime('+5 days'));
        if (strtotime($booking_date) < strtotime($minDate)) {
            $_SESSION['error'] = "Bookings must be made at least 5 days in advance. Earliest date is $minDate.";
            header("Location: " . URLROOT . "/public/?url=client/c_book&id=" . $caretaker_id);
            exit;
        }
        $serviceOptions = [
            "Elder Care" => ["Monthly", "Yearly"],
            "Babysitter" => ["Daily", "Monthly", "Yearly"],
            "Maid" => ["Hourly", "Daily", "Monthly", "Yearly"],

        ];
        // ✅ Rates per service
        $servicePriceRates = [
            "Elder Care" => ["Monthly" => 45000, "Yearly" => 500000],
            "Babysitter" => ["Daily" => 3200, "Monthly" => 42000, "Yearly" => 480000],
            "Maid" => ["Hourly" => 500, "Daily" => 3000, "Monthly" => 38000, "Yearly" => 450000]
        ];

        $timePriceModifier = [
            "Full Time (8am - 5pm)" => 1.0,
            "Morning (8am - 12pm)" => 0.6,
            "Evening (1pm - 5pm)" => 0.6,
            "Night (6pm - 10pm)" => 1.2
        ];


        // ✅ Validate basis belongs to this service type
        if (!isset($servicePriceRates[$service_type][$basis])) {
            $_SESSION['error'] = "Invalid basis selected for $service_type.";
            header("Location: " . URLROOT . "/public/?url=client/c_book&id=" . $caretaker_id);
            exit;
        }

        // ✅ Calculate end date for availability
        $end_date = $this->clientModel->calcEndDate($booking_date, $basis, $duration);

        // ✅ Availability check (server-side)
        if (!$this->clientModel->isCaretakerAvailable($caretaker_id, $booking_date, $end_date)) {
            $_SESSION['error'] = "Selected caretaker is not available from $booking_date to $end_date.";
            header("Location: " . URLROOT . "/public/?url=client/c_book&id=" . $caretaker_id);
            exit;
        }

        $rate = $servicePriceRates[$service_type][$basis];
        $modifier = $timePriceModifier[$preferred_time] ?? 1;
        $total_payment = $rate * $duration * $modifier;

        if ($total_payment <= 0) {
            $_SESSION['error'] = "Invalid total payment. Please check details again.";
            header("Location: " . URLROOT . "/public/?url=client/c_book&id=" . $caretaker_id);
            exit;
        }

        $bookingData = [
            'client_id' => $client_id,
            'caretaker_id' => $caretaker_id,
            'service_type' => $service_type,
            'basis' => $basis,
            'duration' => $duration,
            'preferred_time' => $preferred_time,
            'booking_date' => $booking_date,
            'district' => $district,
            'street' => $street,
            'address_line1' => $address_line1,
            'address_line2' => $address_line2,
            'postal_code' => $postal_code,
            'total_payment' => $total_payment,
            'end_date' => $end_date,
            'customization' => $customization,
            'status' => 'Pending'
        ];


        $bookingId = $this->clientModel->createBooking($bookingData);

        if (!$bookingId) {
            die("Booking failed");
        }

        // ✅ Notify admins
        $this->notificationModel->notifyAdmins(
            "New Booking",
            "New booking placed (Booking ID: $bookingId) by client ID $client_id.",
            URLROOT . "/admin/ad_bookings"
        );

        // ✅ Notify HR
        $this->clientModel->sendNotificationToHR([
            'message' => "New booking request from client ID " . $client_id,
            'role' => 'HR'
        ]);

        header("Location: " . URLROOT . "/public/?url=client/c_bookingConfirm&booking_id=" . $bookingId);
        exit;

    }

    public function checkAvailability()
    {
        if (ob_get_length())
            ob_end_clean();  // ✅ clear anything already printed
        header('Content-Type: application/json; charset=utf-8');
        ini_set('display_errors', 0);
        error_reporting(0);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Read inputs
        $caretakerId = (int) ($_POST['caretaker_id'] ?? 0);
        $startDate = $_POST['booking_date'] ?? '';
        $basis = $_POST['basis'] ?? '';
        $duration = (int) ($_POST['duration'] ?? 0);

        if (!$caretakerId || !$startDate || !$basis || $duration <= 0) {
            echo json_encode([
                'ok' => false,
                'available' => false,
                'message' => 'Missing fields',
                'start' => $startDate,
                'end' => $startDate,
                'alternatives' => []
            ]);
            exit;
        }
        $minDate = date('Y-m-d', strtotime('+3 days'));
        if ($startDate < $minDate) {
            echo json_encode([
                'ok' => true,
                'available' => false,
                'start' => $startDate,
                'end' => $startDate,
                'message' => "Bookings must be made at least 3 days in advance. Earliest date is $minDate.",
                'alternatives' => []
            ]);
            exit;
        }


        // Calculate period
        $endDate = $this->clientModel->calcEndDate($startDate, $basis, $duration);

        // Check availability
        $available = $this->clientModel->isCaretakerAvailable($caretakerId, $startDate, $endDate);

        // If not available, suggest alternatives (same service + preferably same location)
        $alternatives = [];
        if (!$available && method_exists($this->clientModel, 'getAlternativeCaretakers')) {
            $ct = $this->clientModel->getCaretakerById($caretakerId);
            $serviceType = $ct['service_type'] ?? null;
            $location = $ct['location'] ?? null;

            if ($serviceType) {
                $alternatives = $this->clientModel->getAlternativeCaretakers(
                    $caretakerId,
                    $serviceType,
                    $startDate,
                    $endDate,
                    $location,
                    6
                );
            }
        }

        echo json_encode([
            'ok' => true,
            'available' => $available,
            'start' => $startDate,
            'end' => $endDate,
            'alternatives' => $alternatives
        ]);
        exit;
    }
    public function c_ctprofileview()
    {
        if (!isset($_GET['id'])) {
            header("Location: " . URLROOT . "/client/c_find");
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

    public function c_settings()
    {
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'client') {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $user = $_SESSION['user'];

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
            header("Location: " . URLROOT . "/Client/c_settings");
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
        $this->view("client/c_contactCT");
    }

    public function c_complaintlist()
    {
        $this->view("client/c_complaintlist");
    }
    public function c_announcement()
    {
        $announcementModel = $this->model('AnnouncementModel');
        $announcements = $announcementModel->getClientAnnouncements();

        $this->view("client/c_announcement", $announcements);
    }



}