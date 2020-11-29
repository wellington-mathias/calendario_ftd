<?php
class Evento {

    // database connection and table name
    private $conn;

    // object properties
    public $id;
    public $tipo_evento_id;
    public $tipo_evento_descricao;
    public $dt_inicio;
    public $dt_fim;
    public $titulo;
    public $descricao;
    public $uf;
    public $dia_letivo;
    public $dt_criacao;
    public $dt_alteracao;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    
    // read all eventos
    function read() {
        // select all query
        $query = "SELECT
                    e.id,
                    e.tipo_evento AS tipo_evento_id,
                    te.descricao AS tipo_evento_descricao,
                    e.dt_inicio,
                    e.dt_fim,
                    e.titulo,
                    e.descricao,
                    e.uf,
                    e.dia_letivo,
                    e.dt_criacao,
                    e.dt_alteracao
                FROM eventos e
                INNER JOIN tipo_eventos te ON (te.id = e.tipo_evento)
                ORDER BY e.dt_criacao DESC, e.id DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

   // create method
    function create() {
        // query to insert record
        $query = "INSERT INTO eventos
                    SET
                        tipo_evento = :tipo_evento,
                        dt_inicio = :dt_inicio,
                        dt_fim = :dt_fim,
                        titulo = :titulo,
                        descricao = :descricao,
                        uf = :uf,
                        dia_letivo = :dia_letivo";
    
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->tipo_evento_id = htmlspecialchars(strip_tags($this->tipo_evento_id));
        $this->dt_inicio = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio))), "Y-m-d");
        $this->dt_fim = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim))), "Y-m-d");
        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->descricao = (is_null ($this->descricao)) ? null : htmlspecialchars(strip_tags($this->descricao));
        $this->uf = (is_null ($this->uf)) ? null : strtoupper(htmlspecialchars(strip_tags($this->uf)));
        $this->dia_letivo = (int) htmlspecialchars(strip_tags($this->dia_letivo));
    
        // bind values
        $stmt->bindParam(":tipo_evento", $this->tipo_evento_id);
        $stmt->bindParam(":dt_inicio", $this->dt_inicio);
        $stmt->bindParam(":dt_fim", $this->dt_fim);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":uf", $this->uf);
        $stmt->bindParam(":dia_letivo", $this->dia_letivo);

   
        // execute query
        if(!$stmt->execute()){
            return false;
        }
    
        return true;
    }

    // update method
    function update(){
    
        // update query
        $query = "UPDATE eventos
                    SET
                        tipo_evento = :tipo_evento,
                        dt_inicio = :dt_inicio,
                        dt_fim = :dt_fim,
                        titulo = :titulo,
                        descricao = :descricao,
                        uf = :uf,
                        dia_letivo = :dia_letivo,
                        dt_alteracao = :dt_alteracao
                    WHERE id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->tipo_evento_id = htmlspecialchars(strip_tags($this->tipo_evento_id));
        $this->dt_inicio = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio))), "Y-m-d");
        $this->dt_fim = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim))), "Y-m-d");
        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->descricao = (is_null ($this->descricao)) ? null : htmlspecialchars(strip_tags($this->descricao));
        $this->uf = (is_null ($this->uf)) ? null : strtoupper(htmlspecialchars(strip_tags($this->uf)));
        $this->dia_letivo = (int) htmlspecialchars(strip_tags($this->dia_letivo));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind new values
        $stmt->bindParam(":tipo_evento", $this->tipo_evento_id);
        $stmt->bindParam(":dt_inicio", $this->dt_inicio);
        $stmt->bindParam(":dt_fim", $this->dt_fim);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":uf", $this->uf);
        $stmt->bindParam(":dia_letivo", $this->dia_letivo);
        $stmt->bindParam(":dt_alteracao", $this->dt_alteracao);
        $stmt->bindParam(":id", $this->id);

        // execute the query
        if(!$stmt->execute()) {
            return false;
        }
    
        return true;
    }
}
?>