<?php
class AdminController extends Controller {

    private $caretakerModel;
    private $userModel;

    public function __construct() {
        // Load caretaker model once
        $this->caretakerModel = $this->model('CaretakerModel');

           $this->userModel = $this->model('UserModel'); 
    }
  
    public function ad_dashboard() {
        $this->view("admin/ad_dashboard");
    }
    
    public function ad_leave() {
        $this->view("admin/ad_leave");
    }
    public function ad_announcement() {
        $this->view("admin/ad_announcement");
    }
    public function ad_history() {
        $this->view("admin/ad_history");
    }

    public function ad_caretakers() {

    $this->view("admin/ad_caretakers");
}

  
    public function ad_clients() {
        $this->view("admin/ad_clients"); 
    }

    public function ad_users() {
    $users = $this->userModel->getAllUsers(); // ✅ use the initialized property
        $this->view("admin/ad_users", ['users' => $users]); 
    }
    

    public function ad_feedback() {
        $this->view("admin/ad_feedback"); 
    }
    
     public function ad_bookings() {
        $this->view("admin/ad_bookings"); 
    }

     public function ad_settings() {
        $this->view("admin/ad_settings"); 
    }

     public function ad_reports() {
        $this->view("admin/ad_reports"); 
    }

     public function ad_payments() {
        $this->view("admin/ad_payments"); 
    }




   


}