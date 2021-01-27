<?php
class Database {

    private $host = "calendario_ftd.mysql.dbaas.com.br";
    private $username = "calendario_ftd";
    private $password = "cpcinfo";
/* 
    private $host = "localhost";
    private $username = "root";
    private $password = ""; */

    private $db_name = "calendario_ftd";
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