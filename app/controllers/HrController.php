<?php
class HrController extends Controller {

    public function hr_dashboard() {
        $this->view("hr/hr_dashboard");
    }
        public function hr_complaint() {
        $this->view("hr/hr_complaint");
    }
    

    public function hr_addct() {
        $this->view("hr/hr_addct");
    }    

    public function hr_managect() {
        $this->view("hr/hr_managect");
    }

    public function hr_history() {
        $this->view("hr/hr_history");
    }

    public function hr_leave() {
        $this->view("hr/hr_leave");
    }    
    
}