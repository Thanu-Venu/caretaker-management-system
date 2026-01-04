<?php
require_once "../app/models/CaretakerModel.php";
session_start();
class CaretakerController extends Controller {

    private $leaveModel;
    private $caretakerModel;

     public function __construct() {
        $this->leaveModel = $this->model('LeaveModel');
        $this->caretakerModel = $this->model('CaretakerModel');
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


     public function editCaretakerDetails() {
          if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        $user = $_SESSION['user'];

        // Update user details
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updatedData = [
                'name' => $_POST['name'] ?? $user['name'],
                'email' => $_POST['email'] ?? $user['email'],
                'phone' => $_POST['phone'] ?? $user['phone'],
            ];


            $this->caretakerModel->updateCaretaker($user['id'], $updatedData);
            $_SESSION['user'] = array_merge($user, $updatedData);
            $user = $_SESSION['user'];
        $this->view("caretaker/ct_settings", ['user' => $user]);
            exit;
        }

        $this->view("caretaker/ct_settings", ['user' => $user]);
    }

      public function ct_reviews() {
         $this->view("caretaker/ct_reviews");
     }


    }