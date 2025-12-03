<?php
class Database {
    public $conn;

    public function __construct() {


        $this->conn = new mysqli("localhost", "root", "", "smartcare");

        $host = "localhost";
        $user = "root";
        $pass = "";            
        $dbname = "smartcare";  

        $this->conn = new mysqli($host, $user, $pass, $dbname);


        if ($this->conn->connect_error) {
            die("DB Connection failed: " . $this->conn->connect_error);
        }
    }
}