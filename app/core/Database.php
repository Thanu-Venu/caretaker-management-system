<?php
class Database {
    public $conn;

    public function __construct() {
        $host = "localhost";
        $user = "root";
        $pass = ""; // No password for local XAMPP setup
        $dbname = "smartcare";

        $this->conn = new mysqli($host, $user, $pass, $dbname);

        if ($this->conn->connect_error) {
            die("DB Connection failed: " . $this->conn->connect_error);
        }
    }

}