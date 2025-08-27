<?php
class AuthController extends Controller {
    public function login() {
        $this->view("auth/login");
    }

    public function register() {
        $this->view("auth/register");
    }
}
