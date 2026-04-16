<?php
class Database
{
    public $conn;

    public function __construct()
    {
        $host = "localhost";
        $user = "root";
        $pass = "Thanuvenu";
        $dbname = "smartcare1";

        $this->conn = new mysqli($host, $user, $pass, $dbname);

        if ($this->conn->connect_error) {
            die("DB Connection failed: " . $this->conn->connect_error);
        }
    }
}
