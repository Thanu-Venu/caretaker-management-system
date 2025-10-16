<?php
class Database {
    public $conn;

    public function __construct() {
<<<<<<< HEAD
        $this->conn = new mysqli("localhost", "root", "", "smartcare");
=======
        $host = "localhost";
        $user = "root";
        $pass = "Thanuvenu";            
        $dbname = "smartcare";  

        $this->conn = new mysqli($host, $user, $pass, $dbname);

>>>>>>> origin/main
        if ($this->conn->connect_error) {
            die("DB Connection failed: " . $this->conn->connect_error);
        }
    }
}
?>
