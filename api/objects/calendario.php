<?php
include_once '../objects/crud_object.php';
include_once '../objects/instituicao.php';

class Calendario extends CrudObject {
    // object properties
    public $id;
    public $ano_referencia;
    public $dt_inicio_ano_letivo;
    public $dt_fim_ano_letivo;
    public $dt_inicio_recesso;
    public $dt_fim_recesso;
    public $dt_criacao;
    public $dt_alteracao;
    public $instituicao;

    // constructor with $db as database connection
    public function __constructor() {
        parent::__construct();
    }

   // create method
   function create() {
        // query to insert record
        $query = "INSERT INTO calendario
                    SET
                        ano_referencia = :ano_referencia,
                        dt_inicio_ano_letivo = :dt_inicio_ano_letivo,
                        dt_fim_ano_letivo = :dt_fim_ano_letivo,
                        dt_inicio_recesso = :dt_inicio_recesso,
                        dt_fim_recesso = :dt_fim_recesso,
                        instituicao_id = :instituicao_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ano_referencia = (int) htmlspecialchars(strip_tags($this->ano_referencia));
        $this->dt_inicio_ano_letivo = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio_ano_letivo))), "Y-m-d");
        $this->dt_fim_ano_letivo = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim_ano_letivo))), "Y-m-d");
        $this->dt_inicio_recesso = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio_recesso))), "Y-m-d");
        $this->dt_fim_recesso = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim_recesso))), "Y-m-d");
        $this->instituicao->id = (int) htmlspecialchars(strip_tags($this->instituicao->id));

        // bind values
        $stmt->bindParam(":ano_referencia", $this->ano_referencia);
        $stmt->bindParam(":dt_inicio_ano_letivo", $this->dt_inicio_ano_letivo);
        $stmt->bindParam(":dt_fim_ano_letivo", $this->dt_fim_ano_letivo);
        $stmt->bindParam(":dt_inicio_recesso", $this->dt_inicio_recesso);
        $stmt->bindParam(":dt_fim_recesso", $this->dt_fim_recesso);
        $stmt->bindParam(":instituicao_id", $this->instituicao->id);

        // execute query
        if (!$stmt->execute()) {
            return false;
        } else {
            $this->id = $this->conn->lastInsertId();

            return true;
        }
    }


    // read all
    function read() {
        // select all query
        $query = "SELECT
                    a.id,
                    a.ano_referencia,
                    a.dt_inicio_ano_letivo,
                    a.dt_fim_ano_letivo,
                    a.dt_inicio_recesso,
                    a.dt_fim_recesso,
                    a.dt_criacao,
                    a.dt_alteracao,
                    b.id AS instituicao_id,
                    b.nome AS instituicao_nome,
                    b.logo AS instituicao_logo,
                    b.uf AS instituicao_uf,
                    b.dt_criacao AS instituicao_dt_criacao,
                    b.dt_alteracao AS instituicao_dt_alteracao
                FROM calendario a
                INNER JOIN instituicao b ON (b.id = a.instituicao_id)
                ORDER BY a.dt_criacao DESC, a.id DESC";

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

                $instituicao = new Instituicao();
                
                $instituicao->id = $instituicao_id;
                $instituicao->nome = html_entity_decode($instituicao_nome);
                $instituicao->logo = html_entity_decode($instituicao_logo);
                $instituicao->uf = strtoupper(html_entity_decode($instituicao_uf));
                $instituicao->dt_criacao = $instituicao_dt_criacao;
                $instituicao->dt_alteracao = $instituicao_dt_alteracao;
                
                $calendario = new Calendario();

                $calendario->id = $id;
                $calendario->ano_referencia = $ano_referencia;
                $calendario->dt_inicio_ano_letivo = $dt_inicio_ano_letivo;
                $calendario->dt_fim_ano_letivo = $dt_fim_ano_letivo;
                $calendario->dt_inicio_recesso = $dt_inicio_recesso;
                $calendario->dt_fim_recesso = $dt_fim_recesso;
                $calendario->dt_criacao = $dt_criacao;
                $calendario->dt_alteracao = $dt_alteracao;
                $calendario->instituicao = $instituicao;

                array_push($objects_arr, $calendario);
            }
        }

        return $objects_arr;
    }

    // read one product
    function readOne() {
        // query to read single record
        $query = "SELECT
                    a.id,
                    a.ano_referencia,
                    a.dt_inicio_ano_letivo,
                    a.dt_fim_ano_letivo,
                    a.dt_inicio_recesso,
                    a.dt_fim_recesso,
                    a.dt_criacao,
                    a.dt_alteracao,
                    b.id AS instituicao_id,
                    b.nome AS instituicao_nome,
                    b.logo AS instituicao_logo,
                    b.uf AS instituicao_uf,
                    b.dt_criacao AS instituicao_dt_criacao,
                    b.dt_alteracao AS instituicao_dt_alteracao
                FROM calendario a
                INNER JOIN instituicao b ON (b.id = a.instituicao_id)
                WHERE a.id = ?
                LIMIT 0,1";
    
        // prepare query statement
        $stmt = $this->conn->prepare( $query );

        // sanitize
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
    
        // bind id of product to be updated
        $stmt->bindParam(1, $this->id);
    
        // execute query
        $stmt->execute();
       
        $num = $stmt->rowCount();

        // check if the object is not null
        if ($num == 0) {
            return null;
        } else {
            // get retrieved row
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            extract($row);

            $instituicao = new Instituicao();
                
            $instituicao->id = $instituicao_id;
            $instituicao->nome = html_entity_decode($instituicao_nome);
            $instituicao->logo = html_entity_decode($instituicao_logo);
            $instituicao->uf = strtoupper(html_entity_decode($instituicao_uf));
            $instituicao->dt_criacao = $instituicao_dt_criacao;
            $instituicao->dt_alteracao = $instituicao_dt_alteracao;
            
            $this->ano_referencia = $ano_referencia;
            $this->dt_inicio_ano_letivo = $dt_inicio_ano_letivo;
            $this->dt_fim_ano_letivo = $dt_fim_ano_letivo;
            $this->dt_inicio_recesso = $dt_inicio_recesso;
            $this->dt_fim_recesso = $dt_fim_recesso;
            $this->dt_criacao = $dt_criacao;
            $this->dt_alteracao = $dt_alteracao;
            $this->instituicao = $instituicao;
        }
        
        return $this;
    }

    // update method
    function update() {
        // update query
        $query = "UPDATE calendario
                    SET
                        ano_referencia = :ano_referencia,
                        dt_inicio_ano_letivo = :dt_inicio_ano_letivo,
                        dt_fim_ano_letivo = :dt_fim_ano_letivo,
                        dt_inicio_recesso = :dt_inicio_recesso,
                        dt_fim_recesso = :dt_fim_recesso,
                        instituicao_id = :instituicao_id
                    WHERE id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ano_referencia = (int) htmlspecialchars(strip_tags($this->ano_referencia));
        $this->dt_inicio_ano_letivo = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio_ano_letivo))), "Y-m-d");
        $this->dt_fim_ano_letivo = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim_ano_letivo))), "Y-m-d");
        $this->dt_inicio_recesso = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio_recesso))), "Y-m-d");
        $this->dt_fim_recesso = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim_recesso))), "Y-m-d");
        $this->instituicao->id = (int) htmlspecialchars(strip_tags($this->instituicao->id));
        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":ano_referencia", $this->ano_referencia);
        $stmt->bindParam(":dt_inicio_ano_letivo", $this->dt_inicio_ano_letivo);
        $stmt->bindParam(":dt_fim_ano_letivo", $this->dt_fim_ano_letivo);
        $stmt->bindParam(":dt_inicio_recesso", $this->dt_inicio_recesso);
        $stmt->bindParam(":dt_fim_recesso", $this->dt_fim_recesso);
        $stmt->bindParam(":instituicao_id", $this->instituicao->id);
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
        $query = "DELETE FROM calendario WHERE id = ?";
    
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