<?php
include_once '../config/database.php';

class TipoUsuario
{

    private $conn;

    public $id;
    public $descricao;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();

        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO usuario_tipo SET descricao = :descricao";

        $stmt = $this->conn->prepare($query);

        $this->descricao = htmlspecialchars(strip_tags($this->descricao));

        $stmt->bindParam(":descricao", $this->descricao);

        if (!$stmt->execute()) {
            return false;
        } else {
            $this->id = $this->conn->lastInsertId();

            return true;
        }
    }

    public function read()
    {
        $query = "SELECT id, descricao FROM usuario_tipo ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT id, descricao FROM usuario_tipo WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($row)) {
            $this->descricao = $row["descricao"];
        }
    }

    public function update()
    {
        $query = "UPDATE usuario_tipo SET descricao = :descricao WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":id", $this->id);

        if (!$stmt->execute()) {
            return false;
        }

        return true;
    }

    public function delete()
    {
        $query = "DELETE FROM usuario_tipo WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        if (!$stmt->execute()) {
            return false;
        }

        return true;
    }

    public function toArray()
    {
        $array = array(
            "id" => $this->id,
            "nome" => $this->descricao
        );

        return $array;
    }
}
