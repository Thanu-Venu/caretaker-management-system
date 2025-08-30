<?php
class AdminController extends Controller {

    public function index() {
        $this->view("admin/ad_dashboard");
    }

    public function ad_Leave() {
        $this->view("admin/ad_leave");
    }

    
}
