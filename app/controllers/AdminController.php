<?php
class AdminController extends Controller {

    public function index() {
        $this->view("admin/ad_dashboard");
    }

    public function indexLeave() {
        $this->view("admin/ad_leave");
    }

    
}
