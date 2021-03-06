<?php
include_once '../objects/crud_object.php';

class Instituicao extends CrudObject {
    // object properties
    public $id;
    public $nome;
    public $logo;
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
                        uf = :uf";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->logo = htmlspecialchars(strip_tags($this->logo));
        $this->uf = strtoupper(htmlspecialchars(strip_tags($this->uf)));

        // bind values
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":logo", $this->logo);
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
                $instituicao->logo = html_entity_decode($logo);
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
            $this->logo = html_entity_decode($logo);
            $this->uf = strtoupper(html_entity_decode($uf));
            $this->dt_criacao = $dt_criacao;
            $this->dt_alteracao = $dt_alteracao;
        }
        
        return $this;
    }

    // update method
    function update() {
        // update query
        $query = "UPDATE instituicao
                    SET
                        nome = :nome,
                        logo = :logo,
                        uf = :uf
                    WHERE id = :id";
    
        //echo $query;
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->logo = htmlspecialchars(strip_tags($this->logo));
        $this->uf = strtoupper(htmlspecialchars(strip_tags($this->uf)));
        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":logo", $this->logo);
        $stmt->bindParam(":uf", $this->uf);
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