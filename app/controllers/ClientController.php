<?php

class ClientController extends Controller {
     private $clientModel;
    private $serviceOptions;

public function __construct() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }

    $this->clientModel = $this->model('ClientModel');
    $user = $this->clientModel->getClientById($_SESSION['user']['id']);

    if (!$user) {
        session_destroy();
        header("Location: index.php?url=auth/login");
        exit;
    }

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
        'notifications'  => $this->clientModel->getClientNotifications($clientId),
        'assignedCaretaker' => $this->clientModel->getAssignedCaretaker($clientId)
    ];

    $this->view("client/c_dashboard", $data);
}

     public function c_profile() {
    
    if (!isset($_SESSION['user'])) {
        
        header("Location: index.php?url=auth/login");
        exit;
    }

    $user = $_SESSION['user'];

    // pass user info to the view
    $this->view("client/c_profile", ['user' => $user]);
}

public function c_find1() {
    $caretakerModel = $this->model('CaretakerModel');
    $allCaretakers = $caretakerModel->getCaretakers();

    $this->view("client/c_find1", [
        'allCaretakers' => $allCaretakers
    ]);
}

public function c_find() {
    $caretakerModel = $this->model('CaretakerModel');
    $caretakers = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $caretakers = $caretakerModel->getAvailableCaretakers(
    $_POST['service_type'],
    $_POST['start_date'],
    $_POST['preferred_time'],
    $_POST['basis'],
    $_POST['duration']
);

    }

    $this->view("client/c_find", [
        'caretakers' => $caretakers
    ]);
}





  
    public function c_feedback() {
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




  


    public function c_payment() {
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

    public function c_paymentHistory() {
        $clientId = $_SESSION['user']['id'] ?? null;
        if (!$clientId) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        $payments = $this->clientModel->getPaymentsByClient($clientId);
        $this->view("client/c_paymentHistory", ['payments' => $payments]);
    }


    public function c_paymentSuccess() {
        $paymentId = $_GET['payment_id'] ?? null;
        $this->view("client/c_paymentSuccess", ['payment_id' => $paymentId]);
    }

   /* public function c_makePayment() {
        $this->view("client/c_makePayment");
    }*/

    public function c_complaintReg() {
        $this->view("client/c_complaintReg");
    }

   public function c_pastBookings() {
     
    $clientId = $_SESSION['user']['id'];
    
    $bookings = $this->clientModel->getPastBookings($clientId);
     $bookings = $this->clientModel->getPastBookingsWithFeedback($clientId);

    $this->view("client/c_pastBookings", ['bookings' => $bookings]);
}


    public function c_upcomingBookings() {
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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . URLROOT . "/client/c_upcomingBookings");
        exit;
    }

    $bookingId = $_POST['booking_id'];
    $newDate   = $_POST['new_date'];
    $newTime   = $_POST['new_time'];
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

    public function c_cancelledBookings() {
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
    $caretakerModel = $this->model('CaretakerModel');

    // Get caretaker ID from URL
    $caretakerId = $_GET['id'] ?? null;
    if (!$caretakerId) {
        header("Location: " . URLROOT . "/client/c_find1");
        exit;
    }

    // Fetch caretaker details
    $caretaker = $caretakerModel->getCaretakerById($caretakerId);

    // Pre-fill data from GET parameters (from search popup)
    $prefill = [
        'basis'    => $_GET['basis'] ?? '',
        'duration' => intval($_GET['duration'] ?? 1),
        'date'     => $_GET['date'] ?? '',
        'time'     => $_GET['time'] ?? '',
        'customization_hours' => intval($_GET['customization_hours'] ?? 0)
    ];

    // ---- PHP Price Calculation (automatic) ----
    $priceRates = [
        "Hourly"  => 500,
        "Daily"   => 3000,
        "Monthly" => 40000,
        "Yearly"  => 450000
    ];

    $timePriceModifier = [
        "Full Time (8am - 5pm)" => 1.0,
        "Morning (8am - 12pm)"  => 0.6,
        "Evening (1pm - 5pm)"   => 0.6,
        "Night (6pm - 10pm)"    => 1.2
    ];

    $modifier = $timePriceModifier[$prefill['time']] ?? 1;
    $customizationFee = max(0, $prefill['customization_hours']) * 300;
    $total_payment = (($priceRates[$prefill['basis']] ?? 0) * $prefill['duration'] * $modifier) + $customizationFee;

    // Pass data to view
    $data = [
        'caretaker'      => $caretaker,
        'prefill'        => $prefill,
        'serviceOptions' => [
            "Elder Care" => ["Hourly","Daily","Monthly","Yearly"],
            "Babysitter" => ["Hourly","Daily","Monthly"],
            "Maid"       => ["Hourly","Daily","Monthly"],
            "Disability Support" => ["Hourly","Daily","Monthly"]
        ],
        'total_payment'  => $total_payment // <-- automatically calculated
    ];

    $this->view('client/c_book', $data);
}



public function bookCaretaker()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        // ---- PHP Price Calculation ----
        $priceRates = [
            "Hourly"  => 500,
            "Daily"   => 3000,
            "Monthly" => 40000,
            "Yearly"  => 450000
        ];

        $timePriceModifier = [
            "Full Time (8am - 5pm)" => 1.0,
            "Morning (8am - 12pm)"  => 0.6,
            "Evening (1pm - 5pm)"   => 0.6,
            "Night (6pm - 10pm)"    => 1.2
        ];

        $modifier = $timePriceModifier[$preferred_time] ?? 1;
        $customizationFee = max(0, $customizationHours) * 300;
        $total_payment = (($priceRates[$basis] ?? 0) * $duration * $modifier) + $customizationFee;

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

        if ($bookingId) {
            // Send notification to HR (Manager) - only to first/primary manager
            require_once APPROOT . '/models/NotificationModel.php';
            $notifModel = new NotificationModel();
            
            // Get first HR/Manager user
            $hr_users = $notifModel->getHRUsers();
            
            if (!empty($hr_users)) {
                $client_name = $_SESSION['user']['name'] ?? $_SESSION['user']['username'];
                // Send to only the first manager
                $hr_user = $hr_users[0];
                $notifModel->addNotification(
                    $hr_user['id'],
                    'Manager',
                    'New Booking Request',
                    "Client $client_name has submitted a new booking request (ID: $bookingId)",
                    URLROOT . "/hr/index"
                );
            }

            // Redirect with booking ID
            header("Location: " . URLROOT . "/client/c_bookingConfirm?booking_id=" . $bookingId);
            exit;
        } else {
            die("Booking failed");
        }

    } else {
        // If not POST, redirect to find caretakers
        header("Location: " . URLROOT . "/client/c_find1");
        exit;
    }
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


    public function c_bookingConfirm() {
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

     public function c_paymentPage() {
        $this->view("client/c_paymentPage");
    }



/* ================= PROCESS PAYMENT ================= */

/* ================= PROCESS PAYMENT ================= */
public function processPayment() {
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






     public function c_settings() {
       if (!isset($_SESSION['user'])) {
          header("Location: index.php?url=auth/login");
          exit;
               }

      $user = $_SESSION['user']; // <--- assign it here

      $this->view("client/c_settings", ['user' => $user]);
    }

    public function editClientDetails()
{
    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }

    $user = $_SESSION['user'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // KEEP OLD IMAGE
        $profileImage = $user['profile_image'] ?? 'default.png';

        // IF NEW IMAGE SELECTED
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
        header("Location: index.php?url=Client/c_settings");
        exit();
    }
}


    public function editPasswordDetails()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=auth/login");
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
            header("Location: index.php?url=Client/c_settings");
            exit();
        }
    }


     public function c_contactCT() {
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

     public function c_complaintlist() {
        $complaintModel = $this->model('ComplaintModel');
        $client_name = $_SESSION['user']['name'];
        $complaints = $complaintModel->getComplaintsByClient($client_name);
        
        $this->view("client/c_complaintlist", ['complaints' => $complaints]);
    }
 public function c_announcement() {
    $announcementModel = $this->model('AnnouncementModel');
    $announcements = $announcementModel->getClientAnnouncements();

    $this->view("client/c_announcement", $announcements);
    }


   
}