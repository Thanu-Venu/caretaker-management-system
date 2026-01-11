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
    $user = $_SESSION['user'];
    $caretakerId = $user['id'];

    $caretakerModel = $this->model('CaretakerModel');

    // Fetch bookings directly from DB
    $upcoming = $caretakerModel->getUpcomingBookings($caretakerId);
    $past = $caretakerModel->getPastBookings($caretakerId);

    // Just pass the booking_date and preferred_time as they are
    $this->view('caretaker/ct_booking', [
        'upcoming' => $upcoming,
        'past' => $past
    ]);
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
        header("Location: index.php?url=Auth/login");
        exit;
    }

    $user = $_SESSION['user'];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        // KEEP OLD IMAGE
        $profileImage = $user['profile_image'] ?? 'default.png';

        // IF NEW IMAGE SELECTED
        if (!empty($_FILES['profile_image']['name'])) {

            $fileName = time() . "_" . basename($_FILES['profile_image']['name']);

            // Save inside public/uploads
            $targetPath = APPROOT . "/../public/uploads/" . $fileName;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
                $profileImage = $fileName;
            }
        }

        // Add new image name to POST data
        $_POST['profile_image'] = $profileImage;

        // Update DB
        $this->caretakerModel->updateProfileCaretaker($user['id'], $_POST);

        // Refresh session user
        $_SESSION['user'] = $this->caretakerModel->getCaretakerById($user['id']);

        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: index.php?url=Caretaker/ct_settings");
        exit();
    }
}



     public function editPasswordDetails() {

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=Auth/login");
            exit();
        }

        $user = $_SESSION['user'];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $newPassword     = $_POST['new-password'];
            $confirmPassword = $_POST['confirm-password'];

            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = "Passwords do not match!";
                header("Location: index.php?url=Caretaker/ct_settings");
                exit();
            }

            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update DB
            $this->caretakerModel->updateCaretakerPassword($user['id'], $hashedPassword);

            // Success
            $_SESSION['success'] = "Password updated successfully!";
            header("Location: index.php?url=Caretaker/c_settings");
            exit();
        }
    }



      public function ct_reviews() {
         $this->view("caretaker/ct_reviews");
     }
    
}