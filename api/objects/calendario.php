<?php
include_once '../objects/crud_object.php';
include_once '../objects/usuario.php';
include_once '../objects/tipo_usuario.php';
include_once '../objects/instituicao.php';
include_once '../objects/evento.php';

class Calendario extends CrudObject {
    // object properties
    public $id;
    public $ano_referencia;
    public $dt_inicio_ano_letivo;
    public $dt_fim_ano_letivo;
    public $dt_inicio_recesso;
    public $dt_fim_recesso;
    public $qtde_volumes_1o_ano;
    public $qtde_volumes_2o_ano;
    public $qtde_volumes_3o_ano;
    public $revisao_volume_3o_ano;
    public $dt_criacao;
    public $dt_alteracao;
    public $usuario;

    // constructor with $db as database connection
    public function __constructor() {
        parent::__construct();
    }

    // create method
    public function create() {
        // query to insert record
        $query = "INSERT INTO calendario
                    SET
                        ano_referencia = :ano_referencia,
                        dt_inicio_ano_letivo = :dt_inicio_ano_letivo,
                        dt_fim_ano_letivo = :dt_fim_ano_letivo,
                        dt_inicio_recesso = :dt_inicio_recesso,
                        dt_fim_recesso = :dt_fim_recesso,
                        qtde_volumes_1o_ano = :qtde_volumes_1o_ano,
                        qtde_volumes_2o_ano = :qtde_volumes_2o_ano,
                        qtde_volumes_3o_ano = :qtde_volumes_3o_ano,
                        revisao_volume_3o_ano = :revisao_volume_3o_ano,
                        usuario_id = :usuario_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ano_referencia = (int) htmlspecialchars(strip_tags($this->ano_referencia));
        $this->dt_inicio_ano_letivo = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio_ano_letivo))), "Y-m-d");
        $this->dt_fim_ano_letivo = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim_ano_letivo))), "Y-m-d");
        $this->dt_inicio_recesso = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio_recesso))), "Y-m-d");
        $this->dt_fim_recesso = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim_recesso))), "Y-m-d");
        $this->qtde_volumes_1o_ano = (int) htmlspecialchars(strip_tags($this->qtde_volumes_1o_ano));
        $this->qtde_volumes_2o_ano = (int) htmlspecialchars(strip_tags($this->qtde_volumes_2o_ano));
        $this->qtde_volumes_3o_ano = (int) htmlspecialchars(strip_tags($this->qtde_volumes_3o_ano));
        $this->revisao_volume_3o_ano = (int) htmlspecialchars(strip_tags($this->revisao_volume_3o_ano));
        $this->usuario->id = (int) htmlspecialchars(strip_tags($this->usuario->id));

        // bind values
        $stmt->bindParam(":ano_referencia", $this->ano_referencia);
        $stmt->bindParam(":dt_inicio_ano_letivo", $this->dt_inicio_ano_letivo);
        $stmt->bindParam(":dt_fim_ano_letivo", $this->dt_fim_ano_letivo);
        $stmt->bindParam(":dt_inicio_recesso", $this->dt_inicio_recesso);
        $stmt->bindParam(":dt_fim_recesso", $this->dt_fim_recesso);
        $stmt->bindParam(":qtde_volumes_1o_ano", $this->qtde_volumes_1o_ano);
        $stmt->bindParam(":qtde_volumes_2o_ano", $this->qtde_volumes_2o_ano);
        $stmt->bindParam(":qtde_volumes_3o_ano", $this->qtde_volumes_3o_ano);
        $stmt->bindParam(":revisao_volume_3o_ano", $this->revisao_volume_3o_ano);
        $stmt->bindParam(":usuario_id", $this->usuario->id);

        // execute query
        if (!$stmt->execute()) {
            return false;
        } else {
            $this->id = $this->conn->lastInsertId();

            return true;
        }
    }

    // read all
    public function read() {
        // select all query
        $query = "SELECT
                        a.id,
                        a.ano_referencia,
                        a.dt_inicio_ano_letivo,
                        a.dt_fim_ano_letivo,
                        a.dt_inicio_recesso,
                        a.dt_fim_recesso,
                        a.qtde_volumes_1o_ano,
                        a.qtde_volumes_2o_ano,
                        a.qtde_volumes_3o_ano,
                        a.revisao_volume_3o_ano,
                        a.dt_criacao,
                        a.dt_alteracao,
                        a.usuario_id,
                        b.nome AS usuario_nome,
                        b.email AS usuario_email,
                        b.dt_criacao AS usuario_dt_criacao,
                        b.dt_alteracao AS usuario_dt_alteracao,
                        c.id AS usuario_tipo_id,
                        c.descricao AS usuario_tipo_descricao,
                        d.id AS instituicao_id,
                        d.nome AS instituicao_nome,
                        d.logo AS instituicao_logo,
                        d.logo_content_type AS instituicao_logo_content_type,
                        d.uf AS instituicao_uf,
                        d.dt_criacao AS instituicao_dt_criacao,
                        d.dt_alteracao AS instituicao_dt_alteracao
                    FROM calendario a
                    INNER JOIN usuario b ON (b.id = a.usuario_id)
                    INNER JOIN usuario_tipo c ON (c.id = b.usuario_tipo_id)
                    INNER JOIN instituicao d ON (d.id = b.instituicao_id)
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

                $calendario = new Calendario();
                
                $calendario->id = $id;
                $calendario->ano_referencia = $ano_referencia;
                $calendario->dt_inicio_ano_letivo = $dt_inicio_ano_letivo;
                $calendario->dt_fim_ano_letivo = $dt_fim_ano_letivo;
                $calendario->dt_inicio_recesso = $dt_inicio_recesso;
                $calendario->dt_fim_recesso = $dt_fim_recesso;
                $calendario->qtde_volumes_1o_ano = $qtde_volumes_1o_ano;
                $calendario->qtde_volumes_2o_ano = $qtde_volumes_2o_ano;
                $calendario->qtde_volumes_3o_ano = $qtde_volumes_3o_ano;
                $calendario->revisao_volume_3o_ano = $revisao_volume_3o_ano;
                $calendario->dt_criacao = $dt_criacao;
                $calendario->dt_alteracao = $dt_alteracao;

                $calendario->usuario = new Usuario();
                $calendario->usuario->id = $usuario_id;
                $calendario->usuario->nome = html_entity_decode($usuario_nome);
                $calendario->usuario->email = (is_null ($usuario_email)) ? null: html_entity_decode($usuario_email);
                $calendario->usuario->dt_criacao = $usuario_dt_criacao;
                $calendario->usuario->dt_alteracao = $usuario_dt_alteracao;

                $calendario->usuario->tipo_usuario = new TipoUsuario();
                $calendario->usuario->tipo_usuario->id = $usuario_tipo_id;
                $calendario->usuario->tipo_usuario->descricao = html_entity_decode($usuario_tipo_descricao);

                $calendario->usuario->instituicao = new Instituicao();
                $calendario->usuario->instituicao->id = $instituicao_id;
                $calendario->usuario->instituicao->nome = html_entity_decode($instituicao_nome);
                $calendario->usuario->instituicao->logo = base64_encode($instituicao_logo);
                $calendario->usuario->instituicao->logo_content_type = $instituicao_logo_content_type;
                $calendario->usuario->instituicao->uf = strtoupper(html_entity_decode($instituicao_uf));
                $calendario->usuario->instituicao->dt_criacao = $instituicao_dt_criacao;
                $calendario->usuario->instituicao->dt_alteracao = $instituicao_dt_alteracao;

                array_push($objects_arr, $calendario);
            }
        }

        return $objects_arr;
    }

    // read one product
    public function readOne() {
        // query to read single record
        $query = "SELECT
                    a.id,
                    a.ano_referencia,
                    a.dt_inicio_ano_letivo,
                    a.dt_fim_ano_letivo,
                    a.dt_inicio_recesso,
                    a.dt_fim_recesso,
                    a.qtde_volumes_1o_ano,
                    a.qtde_volumes_2o_ano,
                    a.qtde_volumes_3o_ano,
                    a.revisao_volume_3o_ano,
                    a.dt_criacao,
                    a.dt_alteracao,
                    a.usuario_id,
                    b.nome AS usuario_nome,
                    b.email AS usuario_email,
                    b.dt_criacao AS usuario_dt_criacao,
                    b.dt_alteracao AS usuario_dt_alteracao,
                    c.id AS usuario_tipo_id,
                    c.descricao AS usuario_tipo_descricao,
                    d.id AS instituicao_id,
                    d.nome AS instituicao_nome,
                    d.logo AS instituicao_logo,
                    d.logo_content_type AS instituicao_logo_content_type,
                    d.uf AS instituicao_uf,
                    d.dt_criacao AS instituicao_dt_criacao,
                    d.dt_alteracao AS instituicao_dt_alteracao
                FROM calendario a
                INNER JOIN usuario b ON (b.id = a.usuario_id)
                INNER JOIN usuario_tipo c ON (c.id = b.usuario_tipo_id)
                INNER JOIN instituicao d ON (d.id = b.instituicao_id)
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

            $this->ano_referencia = $ano_referencia;
            $this->dt_inicio_ano_letivo = $dt_inicio_ano_letivo;
            $this->dt_fim_ano_letivo = $dt_fim_ano_letivo;
            $this->dt_inicio_recesso = $dt_inicio_recesso;
            $this->dt_fim_recesso = $dt_fim_recesso;
            $this->qtde_volumes_1o_ano = $qtde_volumes_1o_ano;
            $this->qtde_volumes_2o_ano = $qtde_volumes_2o_ano;
            $this->qtde_volumes_3o_ano = $qtde_volumes_3o_ano;
            $this->revisao_volume_3o_ano = $revisao_volume_3o_ano;
            $this->dt_criacao = $dt_criacao;
            $this->dt_alteracao = $dt_alteracao;

            $this->usuario = new Usuario();
            $this->usuario->id = $usuario_id;
            $this->usuario->nome = html_entity_decode($usuario_nome);
            $this->usuario->email = (is_null ($usuario_email)) ? null: html_entity_decode($usuario_email);
            $this->usuario->dt_criacao = $usuario_dt_criacao;
            $this->usuario->dt_alteracao = $usuario_dt_alteracao;

            $this->usuario->tipo_usuario = new TipoUsuario();
            $this->usuario->tipo_usuario->id = $usuario_tipo_id;
            $this->usuario->tipo_usuario->descricao = html_entity_decode($usuario_tipo_descricao);

            $this->usuario->instituicao = new Instituicao();
            $this->usuario->instituicao->id = $instituicao_id;
            $this->usuario->instituicao->nome = html_entity_decode($instituicao_nome);
            $this->usuario->instituicao->logo = base64_encode($instituicao_logo);
            $this->usuario->instituicao->logo_content_type = $instituicao_logo_content_type;
            $this->usuario->instituicao->uf = strtoupper(html_entity_decode($instituicao_uf));
            $this->usuario->instituicao->dt_criacao = $instituicao_dt_criacao;
            $this->usuario->instituicao->dt_alteracao = $instituicao_dt_alteracao;
        }
        
        return $this;
    }

    // read all
    public function readByUser($usuario_id) {
        // select all query
        $query = "SELECT
                        a.id,
                        a.ano_referencia,
                        a.dt_inicio_ano_letivo,
                        a.dt_fim_ano_letivo,
                        a.dt_inicio_recesso,
                        a.dt_fim_recesso,
                        a.qtde_volumes_1o_ano,
                        a.qtde_volumes_2o_ano,
                        a.qtde_volumes_3o_ano,
                        a.revisao_volume_3o_ano,
                        a.dt_criacao,
                        a.dt_alteracao,
                        a.usuario_id,
                        b.nome AS usuario_nome,
                        b.email AS usuario_email,
                        b.dt_criacao AS usuario_dt_criacao,
                        b.dt_alteracao AS usuario_dt_alteracao,
                        c.id AS usuario_tipo_id,
                        c.descricao AS usuario_tipo_descricao,
                        d.id AS instituicao_id,
                        d.nome AS instituicao_nome,
                        d.logo AS instituicao_logo,
                        d.logo_content_type AS instituicao_logo_content_type,
                        d.uf AS instituicao_uf,
                        d.dt_criacao AS instituicao_dt_criacao,
                        d.dt_alteracao AS instituicao_dt_alteracao
                    FROM calendario a
                    INNER JOIN usuario b ON (b.id = a.usuario_id)
                    INNER JOIN usuario_tipo c ON (c.id = b.usuario_tipo_id)
                    INNER JOIN instituicao d ON (d.id = b.instituicao_id)
                    WHERE a.usuario_id = ?
                    ORDER BY a.dt_criacao DESC, a.id DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $usuario_id = (int) htmlspecialchars(strip_tags($usuario_id));
    
        // bind id of product to be updated
        $stmt->bindParam(1, $usuario_id);

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

                $calendario = new Calendario();
                
                $calendario->id = $id;
                $calendario->ano_referencia = $ano_referencia;
                $calendario->dt_inicio_ano_letivo = $dt_inicio_ano_letivo;
                $calendario->dt_fim_ano_letivo = $dt_fim_ano_letivo;
                $calendario->dt_inicio_recesso = $dt_inicio_recesso;
                $calendario->dt_fim_recesso = $dt_fim_recesso;
                $calendario->qtde_volumes_1o_ano = $qtde_volumes_1o_ano;
                $calendario->qtde_volumes_2o_ano = $qtde_volumes_2o_ano;
                $calendario->qtde_volumes_3o_ano = $qtde_volumes_3o_ano;
                $calendario->revisao_volume_3o_ano = $revisao_volume_3o_ano;
                $calendario->dt_criacao = $dt_criacao;
                $calendario->dt_alteracao = $dt_alteracao;

                $calendario->usuario = new Usuario();
                $calendario->usuario->id = $usuario_id;
                $calendario->usuario->nome = html_entity_decode($usuario_nome);
                $calendario->usuario->email = (is_null ($usuario_email)) ? null: html_entity_decode($usuario_email);
                $calendario->usuario->dt_criacao = $usuario_dt_criacao;
                $calendario->usuario->dt_alteracao = $usuario_dt_alteracao;

                $calendario->usuario->tipo_usuario = new TipoUsuario();
                $calendario->usuario->tipo_usuario->id = $usuario_tipo_id;
                $calendario->usuario->tipo_usuario->descricao = html_entity_decode($usuario_tipo_descricao);

                $calendario->usuario->instituicao = new Instituicao();
                $calendario->usuario->instituicao->id = $instituicao_id;
                $calendario->usuario->instituicao->nome = html_entity_decode($instituicao_nome);
                $calendario->usuario->instituicao->logo = base64_encode($instituicao_logo);
                $calendario->usuario->instituicao->logo_content_type = $instituicao_logo_content_type;
                $calendario->usuario->instituicao->uf = strtoupper(html_entity_decode($instituicao_uf));
                $calendario->usuario->instituicao->dt_criacao = $instituicao_dt_criacao;
                $calendario->usuario->instituicao->dt_alteracao = $instituicao_dt_alteracao;

                array_push($objects_arr, $calendario);
            }
        }

        return $objects_arr;
    }

    // update method
    public function update() {
        // update query
        $query = "UPDATE calendario
                    SET
                        ano_referencia = :ano_referencia,
                        dt_inicio_ano_letivo = :dt_inicio_ano_letivo,
                        dt_fim_ano_letivo = :dt_fim_ano_letivo,
                        dt_inicio_recesso = :dt_inicio_recesso,
                        dt_fim_recesso = :dt_fim_recesso,
                        qtde_volumes_1o_ano = :qtde_volumes_1o_ano,
                        qtde_volumes_2o_ano = :qtde_volumes_2o_ano,
                        qtde_volumes_3o_ano = :qtde_volumes_3o_ano,
                        revisao_volume_3o_ano = :revisao_volume_3o_ano,
                        usuario_id = :usuario_id
                    WHERE id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->ano_referencia = (int) htmlspecialchars(strip_tags($this->ano_referencia));
        $this->dt_inicio_ano_letivo = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio_ano_letivo))), "Y-m-d");
        $this->dt_fim_ano_letivo = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim_ano_letivo))), "Y-m-d");
        $this->dt_inicio_recesso = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_inicio_recesso))), "Y-m-d");
        $this->dt_fim_recesso = date_format(date_create_from_format("Y-m-d", htmlspecialchars(strip_tags($this->dt_fim_recesso))), "Y-m-d");
        $this->qtde_volumes_1o_ano = (int) htmlspecialchars(strip_tags($this->qtde_volumes_1o_ano));
        $this->qtde_volumes_2o_ano = (int) htmlspecialchars(strip_tags($this->qtde_volumes_2o_ano));
        $this->qtde_volumes_3o_ano = (int) htmlspecialchars(strip_tags($this->qtde_volumes_3o_ano));
        $this->revisao_volume_3o_ano = (int) htmlspecialchars(strip_tags($this->revisao_volume_3o_ano));
        $this->usuario->id = (int) htmlspecialchars(strip_tags($this->usuario->id));
        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":ano_referencia", $this->ano_referencia);
        $stmt->bindParam(":dt_inicio_ano_letivo", $this->dt_inicio_ano_letivo);
        $stmt->bindParam(":dt_fim_ano_letivo", $this->dt_fim_ano_letivo);
        $stmt->bindParam(":dt_inicio_recesso", $this->dt_inicio_recesso);
        $stmt->bindParam(":dt_fim_recesso", $this->dt_fim_recesso);
        $stmt->bindParam(":qtde_volumes_1o_ano", $this->qtde_volumes_1o_ano);
        $stmt->bindParam(":qtde_volumes_2o_ano", $this->qtde_volumes_2o_ano);
        $stmt->bindParam(":qtde_volumes_3o_ano", $this->qtde_volumes_3o_ano);
        $stmt->bindParam(":revisao_volume_3o_ano", $this->revisao_volume_3o_ano);
        $stmt->bindParam(":usuario_id", $this->usuario->id);
        $stmt->bindParam(":id", $this->id);

        // execute the query
        if(!$stmt->execute()) {
            return false;
        }
    
        return true;
    }

    // delete the product
    public function delete() {
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

    public function addEvento($evento_id) {
        // query to insert record
        $query = "INSERT INTO calendario_evento SET
                    calendario_id = :calendario_id,
                    evento_id = :evento_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $evento_id = (int) htmlspecialchars(strip_tags($evento_id));

        // bind values
        $stmt->bindParam(":calendario_id", $this->id);
        $stmt->bindParam(":evento_id", $evento_id);

        // execute query
        if (!$stmt->execute()) {
            return false;
        }

        return true;
    }

    public function removeEvento($evento_id) {
        // query to insert record
        $query = "DELETE FROM calendario_evento
                    WHERE calendario_id = :calendario_id
                    AND evento_id = :evento_id";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $evento_id = (int) htmlspecialchars(strip_tags($evento_id));

        // bind values
        $stmt->bindParam(":calendario_id", $this->id);
        $stmt->bindParam(":evento_id", $evento_id);

        // execute query
        if (!$stmt->execute()) {
            return false;
        }

        return true;
    }
}
?>