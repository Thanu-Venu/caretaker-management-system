<?php
class Database {
    public $conn;

    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "smartcare");
        if ($this->conn->connect_error) {
            die("DB Connection failed: " . $this->conn->connect_error);
        }
    }
}
?>
