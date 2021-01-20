<?php
include_once '../objects/crud_object.php';

class Evento extends CrudObject {
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
    public function __construct() {
        parent::__construct();
    }

   // create method
   function create() {
        // query to insert record
        $query = "INSERT INTO evento
                    SET
                        dt_inicio = :dt_inicio,
                        dt_fim = :dt_fim,
                        titulo = :titulo,
                        descricao = :descricao,
                        uf = :uf,
                        dia_letivo = :dia_letivo,
                        evento_tipo_id = :evento_tipo_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->dt_inicio = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio))), "Y-m-d");
        $this->dt_fim = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim))), "Y-m-d");
        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->descricao = (is_null ($this->descricao)) ? null : htmlspecialchars(strip_tags($this->descricao));
        $this->uf = (is_null ($this->uf)) ? null : strtoupper(htmlspecialchars(strip_tags($this->uf)));
        $this->dia_letivo = (int) htmlspecialchars(strip_tags($this->dia_letivo));
        $this->tipo_evento_id = htmlspecialchars(strip_tags($this->tipo_evento_id));

        // bind values
        $stmt->bindParam(":dt_inicio", $this->dt_inicio);
        $stmt->bindParam(":dt_fim", $this->dt_fim);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":uf", $this->uf);
        $stmt->bindParam(":dia_letivo", $this->dia_letivo);
        $stmt->bindParam(":evento_tipo_id", $this->tipo_evento_id);


        // execute query
        if (!$stmt->execute()) {
            return false;
        } else {
            $this->id = $this->conn->lastInsertId();

            return true;
        }
    }
    
    // read all eventos
    function read() {
        // select all query
        $query = "SELECT
                    e.id,
                    te.id AS tipo_evento_id,
                    te.descricao AS tipo_evento_descricao,
                    e.dt_inicio,
                    e.dt_fim,
                    e.titulo,
                    e.descricao,
                    e.uf,
                    e.dia_letivo,
                    e.dt_criacao,
                    e.dt_alteracao
                FROM evento e
                INNER JOIN evento_tipo te ON (te.id = e.evento_tipo_id)
                ORDER BY e.dt_criacao DESC, e.id DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        // objects array
        $objects_arr = array();

        // check if more than 0 record found
        if ($stmt->rowCount() > 0) {
            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);

                $evento = new Evento();

                $evento->id = $id;
                $evento->dt_inicio = $dt_inicio;
                $evento->dt_fim = $dt_fim;
                $evento->titulo = (is_null ($titulo)) ? null: html_entity_decode($titulo);
                $evento->descricao = (is_null ($descricao)) ? null: html_entity_decode($descricao);
                $evento->uf = (is_null ($uf)) ? null: strtoupper($uf);
                $evento->dia_letivo = (bool) $dia_letivo;
                $evento->dt_criacao = $dt_criacao;
                $evento->dt_alteracao = $dt_alteracao;
                $evento->tipo_evento_id = $tipo_evento_id;
                $evento->tipo_evento_descricao = $tipo_evento_descricao;

                array_push($objects_arr, $evento);
            }
        }

        return $objects_arr;
    }

    // read one product
    function readOne() {
        // query to read single record
        $query = "SELECT
                    e.id,
                    te.id AS tipo_evento_id,
                    te.descricao AS tipo_evento_descricao,
                    e.dt_inicio,
                    e.dt_fim,
                    e.titulo,
                    e.descricao,
                    e.uf,
                    e.dia_letivo,
                    e.dt_criacao,
                    e.dt_alteracao
                    FROM evento e
                INNER JOIN evento_tipo te ON (te.id = e.evento_tipo_id)
                WHERE e.id = ?
                LIMIT 0,1";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        // sanitize
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
    
        // bind id of product to be updated
        $stmt->bindParam(1, $this->id);
    
        // execute query
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            return false;
        } else {
            // get retrieved row
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (is_array($row)) {
                // set values to object properties
                $this->tipo_evento_id = $row["tipo_evento_id"];
                $this->tipo_evento_descricao = $row["tipo_evento_descricao"];
                $this->dt_inicio = $row["dt_inicio"];
                $this->dt_fim = $row["dt_fim"];
                $this->titulo = $row["titulo"];
                $this->descricao = $row["descricao"];
                $this->uf = $row["uf"];
                $this->dia_letivo = $row["dia_letivo"];
                $this->dt_criacao = $row["dt_criacao"];
                $this->dt_alteracao = $row["dt_alteracao"];
            }

            return true;
        }
    }

    // read one product by calendario_id
    function readOneByCalendario($calendario_id, $evento_id) {
        // select all query
        $query = "SELECT
                        e.id,
                        te.id AS tipo_evento_id,
                        te.descricao AS tipo_evento_descricao,
                        e.dt_inicio,
                        e.dt_fim,
                        e.titulo,
                        e.descricao,
                        e.uf,
                        e.dia_letivo,
                        e.dt_criacao,
                        e.dt_alteracao
                    FROM evento e
                    INNER JOIN evento_tipo te ON (te.id = e.evento_tipo_id)
                    INNER JOIN calendario_evento ce ON (ce.evento_id = e.id)
                    WHERE ce.calendario_id = :calendario_id
                    AND ce.evento_id = :evento_id
                    ORDER BY e.dt_criacao DESC, e.id DESC
                    LIMIT 0,1";

        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        // sanitize
        $calendario_id = (int) htmlspecialchars(strip_tags($calendario_id));
        $evento_id = (int) htmlspecialchars(strip_tags($evento_id));

        // bind id of product to be updated
        $stmt->bindParam(":calendario_id", $calendario_id);
        $stmt->bindParam(":evento_id", $evento_id);

        // execute query
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            return null;
        } else {
            // get retrieved row
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!is_array($row)) {
                return null;
            } else {
                $evento = new Evento();
                
                // set values to object properties
                $evento->tipo_evento_id = $row["tipo_evento_id"];
                $evento->tipo_evento_descricao = $row["tipo_evento_descricao"];
                $evento->dt_inicio = $row["dt_inicio"];
                $evento->dt_fim = $row["dt_fim"];
                $evento->titulo = $row["titulo"];
                $evento->descricao = $row["descricao"];
                $evento->uf = $row["uf"];
                $evento->dia_letivo = $row["dia_letivo"];
                $evento->dt_criacao = $row["dt_criacao"];
                $evento->dt_alteracao = $row["dt_alteracao"];
            }
        }

        return $evento;
        
    }

    // search eventos
    public function search($evento_tipo_id, $uf, $calendario_id, $dia_letivo) {
        // select all query
        $query = "SELECT DISTINCT
                    e.id,
                    e.dt_inicio,
                    e.dt_fim,
                    e.titulo,
                    e.descricao,
                    e.uf,
                    e.dia_letivo,
                    e.dt_criacao,
                    e.dt_alteracao,
                    te.id AS tipo_evento_id,
                    te.descricao AS tipo_evento_descricao
                FROM evento e
                INNER JOIN evento_tipo te ON (te.id = e.evento_tipo_id)
                LEFT OUTER JOIN calendario_evento ce ON (ce.evento_id = e.id)";

        $where = "";
        $where .= $this->addWhereClause($where, $evento_tipo_id, "te.id", "evento_tipo_id", false);
        $where .= $this->addWhereClause($where, $uf, "e.uf", "uf", true);
        $where .= $this->addWhereClause($where, $calendario_id, "ce.calendario_id", "calendario_id", false);
        $where .= $this->addWhereClause($where, $dia_letivo, "e.dia_letivo", "dia_letivo", false);

        $query = $query . $where . " ORDER BY e.dt_criacao DESC, e.id DESC";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        if (!is_null($evento_tipo_id)) $stmt->bindParam(":evento_tipo_id", $evento_tipo_id);
        if (!is_null($uf) && !empty($uf) && strcmp($uf, "NULL") != 0) $stmt->bindParam(":uf", $uf);
        if (!is_null($calendario_id)) $stmt->bindParam(":calendario_id", $calendario_id);
        if (!is_null($dia_letivo)) $stmt->bindParam(":dia_letivo", $dia_letivo);

        // execute query
        $stmt->execute();

        // objects array
        $objects_arr = array();

        // check if more than 0 record found
        if ($stmt->rowCount() > 0) {
            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);

                $evento = new Evento();

                $evento->id = $id;
                $evento->dt_inicio = $dt_inicio;
                $evento->dt_fim = $dt_fim;
                $evento->titulo = (is_null ($titulo)) ? null: html_entity_decode($titulo);
                $evento->descricao = (is_null ($descricao)) ? null: html_entity_decode($descricao);
                $evento->uf = (is_null ($uf)) ? null: strtoupper($uf);
                $evento->dia_letivo = (bool) $dia_letivo;
                $evento->dt_criacao = $dt_criacao;
                $evento->dt_alteracao = $dt_alteracao;
                $evento->tipo_evento_id = $tipo_evento_id;
                $evento->tipo_evento_descricao = $tipo_evento_descricao;

                array_push($objects_arr, $evento);
            }
        }

        return $objects_arr;
    }

    private function addWhereClause($where, $data, $table_field, $binder_str, $nullable) {
        if (is_null($data)) {
            return "";
        } else {
            $returnStr = (empty($where) ? " WHERE " : " AND ");

            if ($nullable) {
                if (empty($data) || strcmp($data, "NULL") == 0) {
                    $returnStr .= $table_field ." IS NULL";
                } else {
                    $returnStr .= $table_field ." = :" . $binder_str;
                }
            } else {
                $returnStr .= $table_field ." = :" . $binder_str;
            }
            
            return $returnStr;
        }
    }

    // update method
    function update() {
        // update query
        $query = "UPDATE evento
                    SET
                        evento_tipo_id = :tipo_evento,
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
        $this->tipo_evento_id = (int) htmlspecialchars(strip_tags($this->tipo_evento_id));
        $this->dt_inicio = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio))), "Y-m-d");
        $this->dt_fim = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim))), "Y-m-d");
        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->descricao = (is_null ($this->descricao)) ? null : htmlspecialchars(strip_tags($this->descricao));
        $this->uf = (is_null ($this->uf)) ? null : strtoupper(htmlspecialchars(strip_tags($this->uf)));
        $this->dia_letivo = (int) htmlspecialchars(strip_tags($this->dia_letivo));
        $this->id = (int) htmlspecialchars(strip_tags($this->id));

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

    // delete the product
    function delete() {
        // delete query
        $query = "DELETE FROM evento WHERE id = ?";
    
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