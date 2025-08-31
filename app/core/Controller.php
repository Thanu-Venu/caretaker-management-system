<?php
class Controller {
    public function view($view, $data = []) {
    require_once APPROOT . "/views/" . $view . ".php";
}

}


