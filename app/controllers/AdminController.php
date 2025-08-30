<?php
class AdminController extends Controller {

    public function ad_dashboard() {
        $this->view("admin/ad_dashboard");
    }

    public function ad_ctmng() {
        $this->view("admin/ad_ctmng"); 
    }

}
