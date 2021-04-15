<?php
include_once '../config/database.php';

abstract class CrudObject
{
    // database connection and table name
    protected $conn;

    // constructor with $db as database connection
    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();

        $this->conn = $db;
    }
}
