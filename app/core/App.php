<?php
class App {
    protected $controller = "LandingController";
    protected $method = "home";
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // controller
        if(file_exists("../app/controllers/" . ucfirst($url[0]) . "Controller.php")) {
            $this->controller = ucfirst($url[0]) . "Controller";
            unset($url[0]);
        }

        require_once "../app/controllers/" . $this->controller . ".php";
        $this->controller = new $this->controller;

        // method
       // method
    if(isset($url[1])) {
    if(method_exists($this->controller, $url[1])) {
        $this->method = $url[1];
        unset($url[1]);
    } else {
        die("Error: Method " . $url[1] . " does not exist in " . get_class($this->controller));
    }
}


        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if(isset($_GET['url'])) {
            return explode("/", filter_var(rtrim($_GET['url'], "/"), FILTER_SANITIZE_URL));
        }
        return ["landing","home"]; // default page
    }
}
