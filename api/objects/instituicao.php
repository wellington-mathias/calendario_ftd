<?php
include_once '../objects/crud_object.php';

class Instituicao extends CrudObject {
    // object properties
    public $id;
    public $nome;
    public $logo;
    public $logo_content_type;
    public $uf;
    public $dt_criacao;
    public $dt_alteracao;

    public function __constructor() {
        parent::__construct();
    }

   // create method
   function create() {
        // query to insert record
        $query = "INSERT INTO instituicao
                    SET
                        nome = :nome,
                        logo = :logo,
                        logo_content_type = :logo_content_type,
                        uf = :uf";

        $stmt = $this->conn->prepare($query);

        // sanitize
        if (!is_null($this->nome)) $this->nome = htmlspecialchars(strip_tags($this->nome));
        if (!is_null($this->uf)) $this->uf = strtoupper(htmlspecialchars(strip_tags($this->uf)));

        // bind values
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":logo", $this->logo);
        $stmt->bindParam(":logo_content_type", $this->logo_content_type);
        $stmt->bindParam(":uf", $this->uf);

        // execute query
        if (!$stmt->execute()) {
            return false;
        } else {
            $this->id = $this->conn->lastInsertId();

            return true;
        }
    }
    
    // read all usuarios
    public function  read() {
        // select all query
        $query = "SELECT
                    u.id,
                    u.nome,
                    u.logo,
                    u.logo_content_type,
                    u.uf,
                    u.dt_criacao,
                    u.dt_alteracao
                FROM instituicao u
                ORDER BY u.dt_criacao DESC, u.id DESC";

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

                $instituicao->id = $id;
                $instituicao->nome = html_entity_decode($nome);
                $instituicao->logo = (is_null($logo)) ? null : "base64," .  base64_encode($logo);
                $instituicao->logo_content_type = (is_null($logo_content_type)) ? null : "data:" . $logo_content_type . ";";
                $instituicao->uf = strtoupper(html_entity_decode($uf));
                $instituicao->dt_criacao = $dt_criacao;
                $instituicao->dt_alteracao = $dt_alteracao;

                array_push($objects_arr, $instituicao);
            }
        }

        return $objects_arr;
    }

    // read one product
    function readOne() {
        // query to read single record
        $query = "SELECT
                    u.id,
                    u.nome,
                    u.logo,
                    u.logo_content_type,
                    u.uf,
                    u.dt_criacao,
                    u.dt_alteracao
                FROM instituicao u
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
       
        $num = $stmt->rowCount();

        // check if the object is not null
        if ($num == 0) {
            return null;
        } else {
            // get retrieved row
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            extract($row);

            $this->nome = html_entity_decode($nome);
            $this->logo = (is_null($logo)) ? null : "base64," .  base64_encode($logo);
            $this->logo_content_type = (is_null($logo_content_type)) ? null : "data:" . $logo_content_type . ";";
            $this->uf = strtoupper(html_entity_decode($uf));
            $this->dt_criacao = $dt_criacao;
            $this->dt_alteracao = $dt_alteracao;
        }
        
        return $this;
    }

    // update method
    function update() {
        // update query
        $doUpdate = !is_null($this->nome) || !is_null($this->uf) || !is_null($this->logo);
        $queryData = "";

        if ($doUpdate) {
            if (!is_null($this->nome)) {
                $queryData .= strlen($queryData) > 0 ? ", nome = :nome" : "nome = :nome";
            }
    
            if (!is_null($this->uf)) {
                $queryData .= strlen($queryData) > 0 ? ", uf = :uf" : "uf = :uf";
            }
    
            if (!is_null($this->logo)) {
                $queryData .= strlen($queryData) > 0 ? ", logo = :logo" : "logo = :logo";
            }

            if (!is_null($this->logo_content_type)) {
                $queryData .= strlen($queryData) > 0 ? ", logo_content_type = :logo_content_type" : "logo_content_type = :logo_content_type";
            }

            $query = "UPDATE instituicao SET " . $queryData . " WHERE id = :id";

            // prepare query statement
            $stmt = $this->conn->prepare($query);

            // bind values
            if (!is_null($this->nome)) {
                $this->nome = htmlspecialchars(strip_tags($this->nome));
                $stmt->bindParam(":nome", $this->nome);
            }

            if (!is_null($this->uf)) {
                $this->uf = strtoupper(htmlspecialchars(strip_tags($this->uf)));
                $stmt->bindParam(":uf", $this->uf);
            }

            if (!is_null($this->logo)) {
                $stmt->bindParam(":logo", $this->logo);
            }

            if (!is_null($this->logo_content_type)) {
                $stmt->bindParam(":logo_content_type", $this->logo_content_type);
            }
            
            $stmt->bindParam(":id", $this->id);

            // execute the query
            if(!$stmt->execute()) {
                return false;
            }
        }
    
        return true;
    }

    // delete the product
    function delete() {
        // delete query
        $query = "DELETE FROM instituicao WHERE id = ?";
    
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