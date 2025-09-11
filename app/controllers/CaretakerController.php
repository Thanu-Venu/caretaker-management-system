<?php
class CaretakerController extends Controller {

    public function ct_dashboard() {
        $this->view("caretaker/ct_dashboard");
    }

     public function ct_editprofile() {
        $this->view("caretaker/ct_editprofile");
    }

     public function ct_leave() {
        $this->view("caretaker/ct_leave");
    }

     
     public function ct_booking() {
         $this->view("caretaker/ct_booking");
     }

     public function ct_schedule() {
         $this->view("caretaker/ct_schedule");
     }
     
     public function ct_leaveHistory() {
         $this->view("caretaker/ct_leaveHistory");
     }

     public function ct_complaints() {
         $this->view("caretaker/ct_complaints");
     }

     public function ct_reports() {
         $this->view("caretaker/ct_reports");
     }

      public function ct_settings() {
         $this->view("caretaker/ct_settings");
     }

      public function ct_reviews() {
         $this->view("caretaker/ct_reviews");
     }
    
}