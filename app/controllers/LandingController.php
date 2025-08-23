<?php
class LandingController {

    public function index() {
        $this->view("landing/home");
    }

    public function view($view, $data = []) {
        require_once "../app/views/" . $view . ".php";
    }
}
