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
  
    public function c_paymentHistory() {
        $this->view("client/c_paymentHistory");
    }


    public function c_paymentSuccess() {
        $this->view("client/c_paymentSuccess");
    }

    public function c_makePayment() {
        $this->view("client/c_makePayment");
    }

    public function c_complaintReg() {
        $this->view("client/c_complaintReg");
    }

    public function c_pastBookings() {
        $this->view("client/c_pastBookings");
    }

    public function c_upcomingBookings() {
        $this->view("client/c_upcomingBookings");
    }

    public function c_book() {
        $this->view("client/c_book");
    }

     public function c_cancelledBookings() {
        $this->view("client/c_cancelledBookings");
    }

    public function c_ctprofileview() {
        $this->view("client/c_ctprofileview");
    }

    public function c_bookingConfirm() {
        $this->view("client/c_bookingConfirm");
    }

     public function c_paymentPage() {
        $this->view("client/c_paymentPage");
    }

     public function c_settings() {
        $this->view("client/c_settings");
    }




}
