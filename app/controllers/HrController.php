<?php
class HrController extends Controller {
    private $userModel;

    public function __construct() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }
    $this->userModel = $this->model('UserModel');
        

    // Revalidate caretaker from DB
    $user = $this->userModel->getUserById($_SESSION['user']['id']); // lowercase usage
    if (!$user) {
        session_destroy();
        header("Location: index.php?url=auth/login");
        exit;
    }

    $_SESSION['user'] = $user;
}
    public function hr_dashboard() {
        $this->view("hr/hr_dashboard");
    }
        public function hr_complaint() {
        $this->view("hr/hr_complaint");
    }
    

    public function hr_addct() {
        $this->view("hr/hr_addct");
    }    

    public function hr_managect() {
        $this->view("hr/hr_managect");
    }

    public function hr_history() {
        $this->view("hr/hr_history");
    }

    public function hr_leave() {
        $this->view("hr/hr_leave");
    }
    
    public function hr_schedule() {
        $this->view("hr/hr_schedule");
    }

    public function hr_pending_request() {
        $this->view("hr/hr_pending_request");
    }

     public function hr_feedback() {
        $this->view("hr/hr_feedback");
    }

    public function hr_reports() {
        $this->view("hr/hr_reports");
    }

     public function hr_settings() {
        $this->view("hr/hr_settings");
    }

    public function hr_announcement() {
    $announcementModel = $this->model('AnnouncementModel');
    $announcements = $announcementModel->getUserAnnouncements();

    $this->view("hr/hr_announcement", $announcements);
    }
    
}