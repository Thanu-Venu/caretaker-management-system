<?php
class Controller {
    public function view($view, $data = []) {
    require_once APPROOT . "/views/" . $view . ".php";
}
public function hr_complaint() {
$this->view("hr/hr_complaint");
}

 public function model($model) {
        require_once APPROOT . '/models/' . $model . '.php';
        return new $model();
    }

}


