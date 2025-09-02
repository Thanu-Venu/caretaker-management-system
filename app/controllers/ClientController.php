<?php
class ClientController extends Controller {

    public function c_dashboard() {
        $this->view("client/c_dashboard");
    }

    public function c_payment() {
        $this->view("client/c_payment");
    }

    public function c_paymentSuccess() {
        $this->view("client/c_paymentSuccess");
    }
}
