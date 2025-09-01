<?php
class ClientController extends Controller {

    public function c_dashboard() {
        $this->view("client/c_dashboard");
    }
    public function c_find() {
        $this->view("client/c_find");
    }

    
}
