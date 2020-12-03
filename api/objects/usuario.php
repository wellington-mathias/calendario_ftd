<?php
class Usuario {

    // database connection and table name
    private $conn;

    // object properties
    public $id;
    public $tipo_usuario;
    public $nome;
    public $chave;
    public $senha;
    public $dt_criacao;
    public $dt_alteracao;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

   // create method
   function create() {
        // query to insert record
        $query = "INSERT INTO usuarios
                    SET
                        tipo_usuario = :tipo_usuario,
                        nome = :nome,
                        chave = :chave,
                        senha = :senha";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->tipo_usuario->id = (int) htmlspecialchars(strip_tags($this->tipo_usuario->id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->chave = htmlspecialchars(strip_tags($this->chave));
        $this->senha = password_hash(htmlspecialchars(strip_tags($this->senha)), PASSWORD_DEFAULT);

        // bind values
        $stmt->bindParam(":tipo_usuario", $this->tipo_usuario->id);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":chave", $this->chave);
        $stmt->bindParam(":senha", $this->senha);


        // execute query
        if (!$stmt->execute()) {
            return false;
        } else {
            $this->id = $this->conn->lastInsertId();

            return true;
        }
    }
    
    // read all usuarios
    function read() {
        // select all query
        $query = "SELECT
                    u.id,
                    u.tipo_usuario AS tipo_usuario_id,
                    tu.descricao AS tipo_usuario_descricao,
                    u.nome,
                    u.dt_criacao,
                    u.dt_alteracao
                FROM usuarios u
                INNER JOIN tipo_usuarios tu ON (tu.id = u.tipo_usuario)
                ORDER BY u.dt_criacao DESC, u.id DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    // read one product
    function readOne() {
        // query to read single record
        $query = "SELECT
                    u.id,
                    u.tipo_usuario AS tipo_usuario_id,
                    tu.descricao AS tipo_usuario_descricao,
                    u.nome,
                    u.dt_criacao,
                    u.dt_alteracao
                    FROM usuarios u
                INNER JOIN tipo_usuarios tu ON (tu.id = u.tipo_usuario)
                WHERE u.id = ?
                LIMIT 0,1";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        // sanitize
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
    
        // bind id of product to be updated
        $stmt->bindParam(1, $this->id);
    
        // execute query
        $stmt->execute();

        return $stmt;
    }

    // update method
    function update() {
        // update query
        $query = "UPDATE usuarios
                    SET
                        tipo_usuario = :tipo_usuario,
                        nome = :nome,
                        dt_alteracao = :dt_alteracao
                    WHERE id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->tipo_usuario->id = (int) htmlspecialchars(strip_tags($this->tipo_usuario->id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        // bind new values
        $stmt->bindParam(":tipo_usuario", $this->tipo_usuario->id);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":dt_alteracao", $this->dt_alteracao);
        $stmt->bindParam(":id", $this->id);

        // execute the query
        if(!$stmt->execute()) {
            return false;
        }
    
        return true;
    }

    // delete the product
    function delete() {
        // delete query
        $query = "DELETE FROM usuarios WHERE id = ?";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
    
        // bind id of record to delete
        $stmt->bindParam(1, $this->id);
    
        // execute query
        if(!$stmt->execute()) {
            return false;
        }
    
        return true;
    }
}
?>