<?php
class Database {
    public $conn;

    public function __construct() {
        $host = "localhost";
        $user = "root";
        $pass = "Thanuvenu"; // Default XAMPP password is an empty string
        $dbname = "smartcare";

        $this->conn = new mysqli($host, $user, $pass, $dbname);

        if ($this->conn->connect_error) {
            die("DB Connection failed: " . $this->conn->connect_error);
        }
    }

}