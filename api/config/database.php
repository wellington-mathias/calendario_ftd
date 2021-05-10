<?php
class Database
{
    public $conn = 1;

    public function getConnection()
    {

        
        if (getenv('DB_HOST_CALENDARIOFTD') === false) {
            $this->host = '127.0.0.1';
        } else {
            $this->host = getenv('DB_HOST_CALENDARIOFTD');
        }
        if (getenv('DB_USER_CALENDARIOFTD') === false) {
            $this->username = 'root';
        } else {
            $this->username = getenv('DB_USER_CALENDARIOFTD');
        }

        if (getenv('DB_PASSWORD_CALENDARIOFTD') === false) {
            $this->password = '';
        } else {
            $this->password = getenv('DB_PASSWORD_CALENDARIOFTD');
        }
        if (getenv('DB_DBNAME_CALENDARIOFTD') === false) {
            $this->db_name = 'calendario_ftd';
        } else {
            $this->db_name = getenv('DB_DBNAME_CALENDARIOFTD');
        }
        
        /* 
        
        $this->host = getenv('DB_HOST_CALENDARIOFTD') === false ? 'calendario_ftd.mysql.dbaas.com.br' : getenv('DB_HOST_CALENDARIOFTD');
        $this->username = getenv('DB_USER_CALENDARIOFTD') === false ? 'calendario_ftd' : getenv('DB_USER_CALENDARIOFTD');
        $this->password = getenv('DB_PASSWORD_CALENDARIOFTD') === false ? 'cpcinfo' : getenv('DB_PASSWORD_CALENDARIOFTD');
        $this->db_name = getenv('DB_DBNAME_CALENDARIOFTD') === false ? 'calendario_ftd' : getenv('DB_DBNAME_CALENDARIOFTD');

         */
        $this->conn = null;

        try {
            $str = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($str, $this->username, $this->password);
            $this->conn->exec("set names utf8");

        } catch (\Throwable $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
