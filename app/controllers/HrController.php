<?php
class HrController extends Controller {

    private $userModel;

    private $caretakerModel;
    private $userModel;

    private $clientModel;
    private $hrLeaveModel;

    public function __construct()
    {
        // Load caretaker model once
        $this->caretakerModel = $this->model('CaretakerModel');

        $this->userModel = $this->model('UserModel');
        $this->clientModel = $this->model('ClientModel');
        $this->hrLeaveModel = $this->model('HRLeaveModel');
    } 

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
        $caretakers = $this->caretakerModel->getCaretakers(); // ✅ use the initialized property
        $this->view("hr/hr_addct", ['caretakers' => $caretakers]);
    }    

    public function hr_managect() {
        $this->view("hr/hr_managect");
    }

    public function hr_history() {
        $this->view("hr/hr_history");
    }

    public function hr_leave() {
        $leaves = $this->hrLeaveModel->getAllLeaves();
        $this->view("hr/hr_leave", ['leaves' => $leaves]);
    }

    public function update_leave_status($id, $status) {
        $this->hrLeaveModel->updateLeaveStatus($id, $status); // update in DB
        header('Location: ' . URLROOT . '/hr/hr_leave'); // redirect back to admin leave page
        exit();
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
    
}