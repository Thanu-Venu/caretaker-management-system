<?php
class AdminController extends Controller {

    public function ad_dashboard() {
        $this->view("admin/ad_dashboard");
    }
    
    public function ad_leave() {
        $this->view("admin/ad_leave");
    }

    public function ad_history(){
        $this->view("admin/ad_history");
    }

    public function ad_caretakers() {
        $this->view("admin/ad_caretakers");
    }
     public function ad_announcement() {
        $this->view("admin/ad_announcement"); 
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