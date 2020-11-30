<?php
class Database {
    private $host = "localhost";
    private $db_name = "calendario_ftd";
    private $username = "root";
    private $password = "";
    public $conn = 1;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (\Throwable $th) {
            echo "Connection error: " . $exception->getMessage();   
        }

        return $this->conn;
    }
}
?>