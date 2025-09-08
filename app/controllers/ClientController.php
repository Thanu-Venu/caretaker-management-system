<?php
class ClientController extends Controller {

    public function c_dashboard() {
        $this->view("client/c_dashboard");
    }
    public function c_find() {
        $this->view("client/c_find");
    }
  
    public function c_feedback() {
        $this->view("client/c_feedback");
    }
  
    public function c_payment() {
        $this->view("client/c_payment");
    }

    public function c_paymentSuccess() {
        $this->view("client/c_paymentSuccess");
    }

    public function c_confirmBooking() {
        $this->view("client/c_confirmBooking");
    }

    public function c_complaintReg() {
        $this->view("client/c_complaintReg");
    }

    public function c_book() {
        $this->view("client/c_book");
    }

    public function c_upcomingBookings() {
        $this->view("client/c_upcomingBookings");
    }

    public function c_pastBookings() {
        $this->view("client/c_pastBookings");
    }
}
