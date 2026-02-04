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
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $limit  = 10;
    $offset = ($page - 1) * $limit;

    $type   = $_GET['type'] ?? 'All';
    $status = $_GET['status'] ?? 'All';

    $leaveModel = $this->model('AdminLeaveModel');

    $leaves = $leaveModel->getLeavesPaginatedFiltered($limit, $offset, $type, $status);
    $totalLeaves = $leaveModel->getTotalLeavesFiltered($type, $status);

    $totalPages = (int) ceil($totalLeaves / $limit);
    if ($totalPages < 1) $totalPages = 1;

    $data = [
        'leaves'       => $leaves,
        'currentPage'  => $page,
        'totalPages'   => $totalPages,
        'selectedType' => $type,
        'selectedStatus' => $status
    ];

    $this->view('admin/ad_leave', $data);
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
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $limit  = 10; // logs per page
    $offset = ($page - 1) * $limit;

    $historyModel = $this->model('HistoryModel'); // change to your model name

    $logs = $historyModel->getLogsPaginated($limit, $offset);
    $totalLogs = $historyModel->getTotalLogs();

    $totalPages = (int) ceil($totalLogs / $limit);
    if ($totalPages < 1) $totalPages = 1;

    $data = [
        'logs' => $logs,
        'currentPage' => $page,
        'totalPages' => $totalPages
    ];

    $this->view('admin/ad_history', $data);
}


    public function ad_caretakers()
{
    header("Location: " . URLROOT . "/CaretakerCRUD/list?page=1");
    exit;
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

    public function ad_bookings()
{
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $limit  = 10;
    $offset = ($page - 1) * $limit;

    $clientModel = $this->model('ClientModel'); // or new ClientModel();

    $bookings = $clientModel->getBookingsPaginated($limit, $offset);
    $totalBookings = $clientModel->getTotalBookings();

    $totalPages = (int) ceil($totalBookings / $limit);
    if ($totalPages < 1) $totalPages = 1;

    $data = [
        'bookings'     => $bookings,
        'currentPage'  => $page,
        'totalPages'   => $totalPages,
        'totalRecords' => $totalBookings
    ];

    $this->view('admin/ad_bookings', $data);
}

}