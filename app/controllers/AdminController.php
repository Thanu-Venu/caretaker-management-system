<?php
class AdminController extends Controller
{

    private $caretakerModel;
    private $userModel;

    private $announcementModel;

    private $clientModel;
    private $adminLeaveModel;

   
     public function __construct() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }

     $this->caretakerModel = $this->model('CaretakerModel');

        $this->userModel = $this->model('UserModel');
        $this->announcementModel = $this->model('AnnouncementModel');
        $this->clientModel = $this->model('ClientModel');
        $this->adminLeaveModel = $this->model('AdminLeaveModel');

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
        $this->view("admin/ad_dashboard");
    }

    public function ad_leave()
{
    $leaves = $this->adminLeaveModel->getAllLeaves(); // Fetch caretakers' leaves
    $this->view("admin/ad_leave", ['leaves' => $leaves]);
}

public function update_leave_status($id, $status)
{
    $this->adminLeaveModel->updateLeaveStatus($id, $status); // update in DB
    header('Location: ' . URLROOT . '/admin/ad_leave'); // redirect back to admin leave page
    exit();
}




    public function ad_history()
    {
        $this->view("admin/ad_history");
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
        $this->view("admin/ad_feedback");
    }

    public function ad_bookings()
    {
        $this->view("admin/ad_bookings");
    }

   public function ad_settings() {
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