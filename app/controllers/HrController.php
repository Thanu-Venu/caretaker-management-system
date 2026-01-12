<?php
class HrController extends Controller {
    private $userModel;
    private $caretakerModel;


    public function __construct() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }
    $this->userModel = $this->model('UserModel');
    $this->caretakerModel = $this->model('CaretakerModel');

        

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
        $caretakers = $this->caretakerModel->getCaretakersForHR();

        $data = [
            'caretakers' => $caretakers
        ];

        $this->view("hr/hr_managect", $data);
    }

    public function updateAvailability() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $_POST['id'],
                'availability' => $_POST['availability'],
                'location' => $_POST['location'],
                'check_in' => $_POST['check_in'],
                'check_out' => $_POST['check_out']
            ];

            $this->caretakerModel->updateAvailability($data);
            header("Location: index.php?url=hr/hr_managect");
            exit;
        }
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
    
}