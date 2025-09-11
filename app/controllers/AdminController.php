<?php
class AdminController extends Controller {

    private $caretakerModel;

    public function __construct() {
        // Load caretaker model once
        $this->caretakerModel = $this->model('CaretakerModel');
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
<<<<<<< HEAD
        $this->view("admin/ad_caretakers"); 
=======
    $caretakers = $this->caretakerModel->getCaretakers(); // ✅ use the initialized property
    $this->view("admin/ad_caretakers", ['caretakers'=>$caretakers]);
}

     public function ad_announcement() {
        $this->view("admin/ad_announcement"); 
>>>>>>> 0c7983bba4a11fb59e245f151d7232f48bed7f8e
    }

    public function ad_clients() {
        $this->view("admin/ad_clients"); 
    }

    public function ad_users() {
        $this->view("admin/ad_users"); 
    }
    
    public function ad_feedback() {
        $this->view("admin/ad_feedback"); 
    }

   


}
