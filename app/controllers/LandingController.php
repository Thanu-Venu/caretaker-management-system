<?php
session_start();

class LandingController {

    public function home() {
        $this->view("landing/home");
    }

    public function view($view, $data = []) {
        require_once "../app/views/" . $view . ".php";
    }
   
}
