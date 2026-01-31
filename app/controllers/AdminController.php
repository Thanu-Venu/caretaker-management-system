<?php
require_once APPROOT . '/models/ComplaintModel.php';

class AdminController extends Controller
{

    private $caretakerModel;
    private $userModel;

    private $announcementModel;

    private $clientModel;
    private $adminLeaveModel;

    private $complaintModel;

    private $feedbackModel;

    private $historyModel;
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $this->caretakerModel = $this->model('CaretakerModel');

        $this->userModel = $this->model('UserModel');
        $this->announcementModel = $this->model('AnnouncementModel');
        $this->clientModel = $this->model('ClientModel');
        $this->adminLeaveModel = $this->model('AdminLeaveModel');
        $this->complaintModel = $this->model('ComplaintModel');
        $this->feedbackModel = $this->model('FeedbackModel');
        $this->historyModel = $this->model('HistoryModel');
        // Revalidate caretaker from DB
        $user = $this->userModel->getUserById($_SESSION['user']['id']); // lowercase usage
        if (!$user) {
            session_destroy();
            header("Location: index.php?url=auth/login");
            exit;
        }

        $_SESSION['user'] = $user;
    }

    public function ad_dashboard()
{
    // Top stats
    $totalCaregivers = $this->caretakerModel->countCaretakers();
    $totalClients    = $this->clientModel->countClients();
    $upcomingBookings= $this->clientModel->countUpcomingBookings(); 
    $pendingLeaves   = $this->adminLeaveModel->countPendingLeaves();
    //$monthlyPayments = $this->clientModel->getMonthlyPaymentsTotal(); // implement based on your payments table (or bookings)

    // Recent activity (history table)
    $recentLogs = $this->historyModel->getRecentLogs(8); // last 8

    // Charts data (example: last 4 weeks bookings, last 6 months engagement)
    $bookingStats = $this->clientModel->getBookingsLast4Weeks();     // labels + values
    $engagement   = $this->clientModel->getClientEngagementLast6Months(); // labels + values

    $this->view("admin/ad_dashboard", [
        'stats' => [
            'totalCaregivers' => $totalCaregivers,
            'totalClients' => $totalClients,
            'upcomingBookings' => $upcomingBookings,
            'pendingLeaves' => $pendingLeaves,
            //'monthlyPayments' => $monthlyPayments,
        ],
        'recentLogs' => $recentLogs,
        'bookingStats' => $bookingStats,
        'engagement' => $engagement
    ]);
}


    public function ad_leave()
    {
        $leaves = $this->adminLeaveModel->getAllLeaves(); // Fetch caretakers' leaves
        $this->view("admin/ad_leave", ['leaves' => $leaves]);
    }

   public function update_leave_status($id, $status)
{
    // 1️⃣ Update DB
    $this->adminLeaveModel->updateLeaveStatus($id, $status);

    // 2️⃣ Log admin action
    $this->historyModel->log([
        'user_id' => $_SESSION['user']['id'],
        'username' => $_SESSION['user']['username'],   // adjust if needed
        'role' => $_SESSION['user']['role'],
        'action' => "Updated leave status to $status (Leave ID: $id)",
        'section' => "Leaves"
    ]);

    // 3️⃣ Redirect
    header('Location: ' . URLROOT . '/admin/ad_leave');
    exit();
}


    public function ad_history()
    {
        $logs = $this->historyModel->getAdminLogs();
        $this->view("admin/ad_history", ['logs' => $logs]);
    }

    public function ad_caretakers()
    {
        $caretakers = $this->caretakerModel->getCaretakers(); // ✅ use the initialized property
        $this->view("admin/ad_caretakers", ['caretakers' => $caretakers]);
    }

    public function ad_announcement()
    {
        $announcements = $this->announcementModel->getAllAnnouncements();
        $this->view("admin/ad_announcement", [
            'announcements' => $announcements
        ]);
    }
    public function ad_clients()
    {
        $clients = $this->clientModel->getAllClients();
        $this->view("admin/ad_clients", ['clients' => $clients]);
    }

    public function ad_users()
    {
        $users = $this->userModel->getAllUsers(); // ✅ use the initialized property
        $this->view("admin/ad_users", ['users' => $users]);
    }

    public function ad_feedback()
    {
        $complaints = $this->complaintModel->getAllComplaints();
        $feedbacks = $this->feedbackModel->getAll();

        $this->view("admin/ad_feedback", [
            'complaints' => $complaints,
            'feedbacks' => $feedbacks
        ]);
    }

    public function ad_bookings()
    {
        $bookings=$this->clientModel->getAllBookingsAdmin();
        $this->view("admin/ad_bookings", ['bookings' => $bookings]);
    }

    public function ad_settings()
    {
        // Session already started in constructor
        if (!isset($_SESSION['user'])) {
            header("Location: " . URLROOT . "/auth/login");
            exit;
        }

        // Optional: allow only admin role
        if ($_SESSION['user']['role'] !== 'admin') {
            die("Access denied. Only admin can access this page.");
        }

        // Use session user directly
        $user = $_SESSION['user'];

        $this->view('admin/ad_settings', ['user' => $user]);
    }

    public function ad_reports()
    {
        $this->view("admin/ad_reports");
    }

    public function ad_payments()
    {
        $this->view("admin/ad_payments");
    }

}