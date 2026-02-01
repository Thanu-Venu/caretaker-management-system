<?php

class ClientController extends Controller {
     private $clientModel;
    public function __construct() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }

    // Re-validate user from database
    $this->clientModel = $this->model('ClientModel');
    $user = $this->clientModel->getClientById($_SESSION['user']['id']);

    if (!$user) { // user deleted
        session_destroy();
        header("Location: index.php?url=auth/login");
        exit;
    }

    // Update session with latest data
    $_SESSION['user'] = $user;
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
        'notifications'  => $this->clientModel->getClientNotifications($clientId)
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

   public function c_find()
{
    $caretakerModel = $this->model('CaretakerModel');
    $caretakers = $caretakerModel->getCaretakers();

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
        $this->view("client/c_payment");
    }

    public function c_paymentHistory() {
        $this->view("client/c_paymentHistory");
    }


    public function c_paymentSuccess() {
        $this->view("client/c_paymentSuccess");
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

        $this->view('client/c_makePayment', [
            'booking' => $booking
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
    // 1️⃣ Check if user is logged in
    if (!isset($_SESSION['user'])) {
        header("Location: " . URLROOT . "/public/?url=auth/login");
        exit;
    }

    // 2️⃣ Check if caretaker ID is provided
    if (!isset($_GET['id'])) {
        die("Caretaker ID missing");
    }

    $caretakerId = $_GET['id'];

    // 3️⃣ Load model
    $clientModel = $this->model('ClientModel');

    // 4️⃣ Get caretaker details
    $caretaker = $clientModel->getCaretakerById($caretakerId);

    if (!$caretaker) {
        die("Caretaker not found");
    }

    // 5️⃣ Define service-dependent options
    $serviceOptions = [
        "Elder Care" => ["Monthly", "Yearly"],
        "Babysitter"   => ["Daily", "Weekly", "Monthly", "Yearly"],
        "Maid"         => ["Hourly", "Daily", "Weekly", "Monthly", "Yearly"],
        "Disability Support" => ["Daily", "Weekly", "Monthly"]
    ];

    // 6️⃣ Define base price rates
    $priceRates = [
        "Hourly"  => 500,
        "Daily"   => 3000,
        "Weekly"  => 15000,
        "Monthly" => 40000,
        "Yearly"  => 450000
    ];

    // 7️⃣ Send data to view (for GET request only)
    $this->view('client/c_book', [
        'caretaker'      => $caretaker,
        'serviceOptions' => $serviceOptions,
        'priceRates'     => $priceRates
    ]);
}


public function bookCaretaker()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $service_type    = $_POST['service_type'];
        $basis           = $_POST['basis'];
        $duration        = intval($_POST['duration']);
        $preferred_time  = $_POST['preferred_time'];
        $booking_date    = $_POST['booking_date'];
        $service_location = $_POST['service_location'];
        $customization    = $_POST['customization'];
        $caretaker_id    = $_POST['caretaker_id'];
        $client_id       = $_SESSION['user']['id']; // use session ID

        // ---- PHP Price Calculation ----
        $priceRates = [
            "Hourly"  => 500,
            "Daily"   => 3000,
            "Weekly"  => 15000,
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

        // Calculate total payment in PHP
        $total_payment = ($priceRates[$basis] ?? 0) * $duration * $modifier;

        // ---- Store booking ----
        $bookingData = [
            'client_id'      => $client_id,
            'caretaker_id'   => $caretaker_id,
            'service_type'   => $service_type,
            'basis'          => $basis,
            'duration'       => $duration,
            'preferred_time' => $preferred_time,
            'booking_date'   => $booking_date,
             'service_location' => $service_location,
            'customization'    => $customization,
            'total_payment'  => $total_payment,
            'status'         => 'Pending'
        ];

        $bookingId = $this->clientModel->createBooking($bookingData);

if ($bookingId) {
    // Send notification to HR
    $notification = [
        'message' => "New booking request from client ID ".$client_id,
        'role'    => 'HR'
    ];
    $this->clientModel->sendNotificationToHR($notification);

    // Redirect with booking ID
    header("Location: " . URLROOT . "/client/c_bookingConfirm?booking_id=" . $bookingId);
    exit;
} else {
    die("Booking failed");
}

    } else {
        // If not POST, redirect to find caretakers
        header("Location: " . URLROOT . "/client/c_find");
        exit;
    }
}



 


    public function c_ctprofileview()
{
    if (!isset($_GET['id'])) {
        header("Location: index.php?url=client/c_find");
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
        $this->view("client/c_contactCT");
    }

     public function c_complaintlist() {
        $this->view("client/c_complaintlist");
    }
 public function c_announcement() {
    $announcementModel = $this->model('AnnouncementModel');
    $announcements = $announcementModel->getClientAnnouncements();

    $this->view("client/c_announcement", $announcements);
    }


   
}