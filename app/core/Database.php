<?php
class Database {
    public $conn;

    public function __construct() {

        $this->conn = new mysqli("localhost", "root", "Nadu@2002", "smartcare");

        $host = "localhost";
        $user = "root";
        $pass = "Nadu@2002";            
        $dbname = "smartcare";  

        $this->conn = new mysqli($host, $user, $pass, $dbname);


        if ($this->conn->connect_error) {
            die("DB Connection failed: " . $this->conn->connect_error);
        }
    }
}