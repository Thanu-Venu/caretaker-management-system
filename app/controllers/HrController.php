<?php
class HrController extends Controller {

    public function hr_dashboard() {
        $this->view("hr/hr_dashboard");
    }
        public function hr_complaint() {
        $this->view("hr/hr_complaint");
    }
    

    
}