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
     
    
}