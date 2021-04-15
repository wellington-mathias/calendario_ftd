
<?php
include_once '../config/database.php';

class TipoUsuario
{

    // database connection and table name
    private $conn;

    // object properties
    public $id;
    public $descricao;

    // constructor with $db as database connection
    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();

        $this->conn = $db;
    }

    // create method
    function create()
    {
        // query to insert record
        $query = "INSERT INTO usuario_tipo SET descricao = :descricao";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));

        // bind values
        $stmt->bindParam(":descricao", $this->descricao);

        // execute query
        if (!$stmt->execute()) {
            return false;
        } else {
            $this->id = $this->conn->lastInsertId();

            return true;
        }
    }

    // read all eventos
    function read()
    {
        // select all query
        $query = "SELECT id, descricao FROM usuario_tipo ORDER BY id DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    // read one
    function readOne()
    {
        // query to read single record
        $query = "SELECT id, descricao FROM usuario_tipo WHERE id = ? LIMIT 0,1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        // bind id to be selected
        $stmt->bindParam(1, $this->id);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($row)) {
            // set values to object properties
            $this->descricao = $row["descricao"];
        }
    }

    // update method
    function update()
    {
        // update query
        $query = "UPDATE usuario_tipo SET descricao = :descricao WHERE id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        // bind new values
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":id", $this->id);

        // execute the query
        if (!$stmt->execute()) {
            return false;
        }

        return true;
    }

    // delete the product
    function delete()
    {
        // delete query
        $query = "DELETE FROM usuario_tipo WHERE id = ?";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        // bind id of record to delete
        $stmt->bindParam(1, $this->id);

        // execute query
        if (!$stmt->execute()) {
            return false;
        }

        return true;
    }

    function toArray()
    {
        $array = array(
            "id" => $this->id,
            "nome" => $this->descricao
        );

        return $array;
    }
}
