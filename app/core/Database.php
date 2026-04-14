<?php
class Database {
    public $conn;

    public function __construct() {
<<<<<<< HEAD


        $this->conn = new mysqli("localhost", "root", "Thanuvenu", "smartcare");


     

        $host = "localhost";
        $user = "root";
        $pass = "Thanuvenu";            
        $dbname = "smartcare";  
=======
        $host = "localhost";
        $user = "root";
        $pass = "Thanuvenu"; // Default XAMPP password is an empty string
        $dbname = "smartcare";
>>>>>>> 62de69af75adfb5fba34de87af93d9cd9d508008

        $this->conn = new mysqli($host, $user, $pass, $dbname);

        if ($this->conn->connect_error) {
            die("DB Connection failed: " . $this->conn->connect_error);
        }
    }

}