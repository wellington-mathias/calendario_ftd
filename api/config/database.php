<?php
class Database {
    public $conn = 1;

    public function getConnection() {
        
        $this->host = getenv('DB_HOST_CALENDARIOFTD') === false ? '127.0.0.1' : getenv('DB_HOST_CALENDARIOFTD');
        $this->username = getenv('DB_USER_CALENDARIOFTD') === false ? 'root' : getenv('DB_USER_CALENDARIOFTD');
        $this->password = getenv('DB_PASSWORD_CALENDARIOFTD') === false ? '' : getenv('DB_PASSWORD_CALENDARIOFTD');
        $this->db_name = getenv('DB_DBNAME_CALENDARIOFTD') === false ? 'calendario_ftd' : getenv('DB_DBNAME_CALENDARIOFTD');
        
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (\Throwable $exception) {
            echo "Connection error: " . $exception->getMessage();   
        }
        
        return $this->conn;
    }
}
