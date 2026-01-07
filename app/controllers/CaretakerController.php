<?php

class CaretakerController extends Controller {

    private $leaveModel;
    private $caretakerModel;
     public function __construct() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: index.php?url=auth/login");
        exit;
    }

    $this->leaveModel = $this->model('LeaveModel');
    $this->caretakerModel = $this->model('CaretakerModel'); // lowercase property

    // Revalidate caretaker from DB
    $user = $this->caretakerModel->getCaretakerById($_SESSION['user']['id']); // lowercase usage
    if (!$user) {
        session_destroy();
        header("Location: index.php?url=auth/login");
        exit;
    }

    $_SESSION['user'] = $user;
}

    public function ct_dashboard() {
        $this->view("caretaker/ct_dashboard");
    }

     public function ct_editprofile() {
        $this->view("caretaker/ct_editprofile");
    }

     public function ct_leave() {
    if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'caretaker') {
        die("Caretaker not logged in");
    }

    $leaveModel = $this->model('LeaveModel');
    $userId = $_SESSION['user']['id'];
    $leaves = $leaveModel->getLeavesByUser($userId);

    $this->view('caretaker/ct_leave', ['leaves' => $leaves]);
}


     
     public function ct_booking() {
         $this->view("caretaker/ct_booking");
     }

     public function ct_schedule() {
         $this->view("caretaker/ct_schedule");
     }
     
     public function ct_leaveHistory() {
         $this->view("caretaker/ct_leaveHistory");
     }

     public function ct_complaints() {
         $this->view("caretaker/ct_complaints");
     }

     public function ct_reports() {
         $this->view("caretaker/ct_reports");
     }

      public function ct_settings() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $user = $_SESSION['user'];

        // pass user info to the view
        $this->view("caretaker/ct_settings", ['user' => $user]);
         
     }

      public function ct_reviews() {
         $this->view("caretaker/ct_reviews");
     }
    
}