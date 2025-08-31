<?php
class AdminController extends Controller {

    public function ad_dashboard() {
        $this->view("admin/ad_dashboard");
    }

    public function ad_leave() {
        $this->view("admin/ad_leave");
    }

    public function ad_ctmng() {
        $this->view("admin/ad_ctmng"); 
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

    public function ad_paymentsHistory() {
        $this->view("admin/ad_paymentsHistory");
    }

    public function ad_paymentsPending() {
        $this->view("admin/ad_paymentsPending");
    }

    public function ad_bookings() {
        $this->view("admin/ad_bookings");
    }


}
