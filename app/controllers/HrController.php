<?php
class HrController extends Controller {

    public function hr_dashboard() {
        $this->view("hr/hr_dashboard");
    }

    public function hr_addct() {
        $this->view("hr/hr_addct");
    }    

    public function hr_managect() {
        $this->view("hr/hr_managect");
    }


    
}