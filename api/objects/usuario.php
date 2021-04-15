<?php
include_once '../objects/crud_object.php';
include_once '../objects/tipo_usuario.php';
include_once '../objects/instituicao.php';

class Usuario extends CrudObject
{
    // object properties
    public $id;
    public $nome;
    public $email;
    public $login;
    public $senha;
    public $login_ftd;
    public $senha_ftd;
    public $dt_criacao;
    public $dt_alteracao;
    public $tipo_usuario;
    public $instituicao;

    public function __constructor()
    {
        parent::__construct();
    }

    // create method
    function create()
    {
        // query to insert record
        $query = "INSERT INTO usuario
                    SET
                        nome = :nome,
                        email = :email,
                        login = :login,
                        senha = :senha,
                        login_ftd = :login_ftd,
                        senha_ftd = :senha_ftd,
                        usuario_tipo_id = :tipo_usuario_id,
                        instituicao_id = :instituicao_id";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->nome = (is_null($this->nome)) ? null : htmlspecialchars(strip_tags($this->nome));
        $this->email = (is_null($this->email)) ? null : htmlspecialchars(strip_tags($this->email));
        $this->login = (is_null($this->login)) ? null : htmlspecialchars(strip_tags($this->login));
        $this->senha = (is_null($this->senha)) ? null : password_hash(htmlspecialchars(strip_tags($this->senha)), PASSWORD_DEFAULT);
        $this->login_ftd = (is_null($this->login_ftd)) ? null : htmlspecialchars(strip_tags($this->login_ftd));
        $this->senha_ftd = (is_null($this->senha_ftd)) ? null : password_hash(htmlspecialchars(strip_tags($this->senha_ftd)), PASSWORD_DEFAULT);
        $this->tipo_usuario->id = (int) htmlspecialchars(strip_tags($this->tipo_usuario->id));
        $this->instituicao->id = (int) (is_null($this->instituicao->id)) ? null : htmlspecialchars(strip_tags($this->instituicao->id));


        // bind values
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":login", $this->login);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":login_ftd", $this->login_ftd);
        $stmt->bindParam(":senha_ftd", $this->senha_ftd);
        $stmt->bindParam(":tipo_usuario_id", $this->tipo_usuario->id);
        $stmt->bindParam(":instituicao_id", $this->instituicao->id);


        // execute query
        if (!$stmt->execute()) {
            return false;
        } else {
            $this->id = $this->conn->lastInsertId();

            return true;
        }
    }

    // read all usuarios
    public function  read()
    {
        // select all query
        $query = "SELECT
                    a.id,
                    a.nome,
                    a.email,
                    a.dt_criacao,
                    a.dt_alteracao,
                    b.id AS usuario_tipo_id,
                    b.descricao AS usuario_tipo_descricao,
                    c.id AS instituicao_id,
                    c.nome AS instituicao_nome,
                    c.logo AS instituicao_logo,
                    c.logo_content_type AS instituicao_logo_content_type,
                    c.uf AS instituicao_uf,
                    c.dt_criacao AS instituicao_dt_criacao,
                    c.dt_alteracao AS instituicao_dt_alteracao
                FROM usuario AS a
                INNER JOIN usuario_tipo AS b ON (b.id = a.usuario_tipo_id)
                LEFT OUTER JOIN instituicao c ON (c.id = a.instituicao_id)
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
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);

                $usuario = new Usuario();
                $usuario->id = $id;
                $usuario->nome = (is_null($nome)) ? null : html_entity_decode($nome);
                $usuario->email = (is_null($email)) ? null : html_entity_decode($email);
                $usuario->dt_criacao = $dt_criacao;
                $usuario->dt_alteracao = $dt_alteracao;

                $usuario->tipo_usuario = new TipoUsuario();
                $usuario->tipo_usuario->id = $usuario_tipo_id;
                $usuario->tipo_usuario->descricao = html_entity_decode($usuario_tipo_descricao);

                if ($instituicao_id == null) {
                    $usuario->instituicao = null;
                } else {
                    $usuario->instituicao = new Instituicao();
                    $usuario->instituicao->id = $instituicao_id;
                    $usuario->instituicao->nome = (is_null($instituicao_nome)) ? null : html_entity_decode($instituicao_nome);
                    $usuario->instituicao->logo = (is_null($instituicao_logo)) ? null : "base64," .  base64_encode($instituicao_logo);
                    $usuario->instituicao->logo_content_type = (is_null($instituicao_logo_content_type)) ? null : "data:" . $instituicao_logo_content_type . ";";
                    $usuario->instituicao->uf = (is_null($instituicao_uf)) ? null : strtoupper(html_entity_decode($instituicao_uf));
                    $usuario->instituicao->dt_criacao = $instituicao_dt_criacao;
                    $usuario->instituicao->dt_alteracao = $instituicao_dt_alteracao;
                }

                array_push($objects_arr, $usuario);
            }
        }

        return $objects_arr;
    }

    // read one product
    function readOne()
    {
        // query to read single record
        $query = "SELECT
                    a.id,
                    a.nome,
                    a.email,
                    a.dt_criacao,
                    a.dt_alteracao,
                    b.id AS usuario_tipo_id,
                    b.descricao AS usuario_tipo_descricao,
                    c.id AS instituicao_id,
                    c.nome AS instituicao_nome,
                    c.logo AS instituicao_logo,
                    c.logo_content_type AS instituicao_logo_content_type,
                    c.uf AS instituicao_uf,
                    c.dt_criacao AS instituicao_dt_criacao,
                    c.dt_alteracao AS instituicao_dt_alteracao
                FROM usuario AS a
                INNER JOIN usuario_tipo AS b ON (b.id = a.usuario_tipo_id)
                LEFT OUTER JOIN instituicao c ON (c.id = a.instituicao_id)
                WHERE a.id = ?
                LIMIT 0,1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

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

            $this->nome = (is_null($nome)) ? null : html_entity_decode($nome);
            $this->email = (is_null($email)) ? null : html_entity_decode($email);
            $this->dt_criacao = $dt_criacao;
            $this->dt_alteracao = $dt_alteracao;

            $this->tipo_usuario = new TipoUsuario();
            $this->tipo_usuario->id = $usuario_tipo_id;
            $this->tipo_usuario->descricao = html_entity_decode($usuario_tipo_descricao);

            if ($instituicao_id == null) {
                $this->instituicao = null;
            } else {
                $this->instituicao = new Instituicao();
                $this->instituicao->id = $instituicao_id;
                $this->instituicao->nome = (is_null($instituicao_nome)) ? null : html_entity_decode($instituicao_nome);
                $this->instituicao->logo = (is_null($instituicao_logo)) ? null : "base64," .  base64_encode($instituicao_logo);
                $this->instituicao->logo_content_type = (is_null($instituicao_logo_content_type)) ? null : "data:" . $instituicao_logo_content_type . ";";
                $this->instituicao->uf = (is_null($instituicao_uf)) ? null : strtoupper(html_entity_decode($instituicao_uf));
                $this->instituicao->dt_criacao = $instituicao_dt_criacao;
                $this->instituicao->dt_alteracao = $instituicao_dt_alteracao;
            }
        }

        return $this;
    }

    // read all usuarios
    public function  readByType($tipo_id)
    {
        // select all query
        $query = "SELECT
                    a.id,
                    a.nome,
                    a.email,
                    a.dt_criacao,
                    a.dt_alteracao,
                    b.id AS usuario_tipo_id,
                    b.descricao AS usuario_tipo_descricao,
                    c.id AS instituicao_id,
                    c.nome AS instituicao_nome,
                    c.logo AS instituicao_logo,
                    c.logo_content_type AS instituicao_logo_content_type,
                    c.uf AS instituicao_uf,
                    c.dt_criacao AS instituicao_dt_criacao,
                    c.dt_alteracao AS instituicao_dt_alteracao
                FROM usuario AS a
                INNER JOIN usuario_tipo AS b ON (b.id = a.usuario_tipo_id)
                LEFT OUTER JOIN instituicao c ON (c.id = a.instituicao_id)
                WHERE a.usuario_tipo_id = :tipo_id
                ORDER BY a.dt_criacao DESC, a.id DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $tipo_id = (int) htmlspecialchars(strip_tags($tipo_id));

        // bind new values
        $stmt->bindParam(":tipo_id", $tipo_id);

        // execute query
        $stmt->execute();

        // objects array
        $objects_arr = array();

        // check if more than 0 record found
        if ($stmt->rowCount() > 0) {
            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);

                $usuario = new Usuario();
                $usuario->id = $id;
                $usuario->nome = (is_null($nome)) ? null : html_entity_decode($nome);
                $usuario->email = (is_null($email)) ? null : html_entity_decode($email);
                $usuario->dt_criacao = $dt_criacao;
                $usuario->dt_alteracao = $dt_alteracao;

                $usuario->tipo_usuario = new TipoUsuario();
                $usuario->tipo_usuario->id = $usuario_tipo_id;
                $usuario->tipo_usuario->descricao = html_entity_decode($usuario_tipo_descricao);

                if ($instituicao_id == null) {
                    $usuario->instituicao = null;
                } else {
                    $usuario->instituicao = new Instituicao();
                    $usuario->instituicao->id = $instituicao_id;
                    $usuario->instituicao->nome = (is_null($instituicao_nome)) ? null : html_entity_decode($instituicao_nome);
                    $usuario->instituicao->logo = (is_null($instituicao_logo)) ? null : "base64," .  base64_encode($instituicao_logo);
                    $usuario->instituicao->logo_content_type = (is_null($instituicao_logo_content_type)) ? null : "data:" . $instituicao_logo_content_type . ";";
                    $usuario->instituicao->uf = (is_null($instituicao_uf)) ? null : strtoupper(html_entity_decode($instituicao_uf));
                    $usuario->instituicao->dt_criacao = $instituicao_dt_criacao;
                    $usuario->instituicao->dt_alteracao = $instituicao_dt_alteracao;
                }

                array_push($objects_arr, $usuario);
            }
        }

        return $objects_arr;
    }

    // update method
    function update()
    {
        // update query
        $query = "UPDATE usuario
                    SET
                        nome = :nome,
                        email = :email,
                        dt_alteracao = :dt_alteracao,
                        usuario_tipo_id = :tipo_usuario_id,
                        instituicao_id = :instituicao_id
                    WHERE id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->nome = (is_null($this->nome)) ? null : htmlspecialchars(strip_tags($this->nome));
        $this->email = (is_null($this->email)) ? null : htmlspecialchars(strip_tags($this->email));
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $this->tipo_usuario->id = (int) htmlspecialchars(strip_tags($this->tipo_usuario->id));
        $this->instituicao->id = (int) (is_null($this->instituicao->id)) ? null : htmlspecialchars(strip_tags($this->instituicao->id));

        // bind new values
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":dt_alteracao", $this->dt_alteracao);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":tipo_usuario_id", $this->tipo_usuario->id);
        $stmt->bindParam(":instituicao_id", $this->instituicao->id);

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
        $query = "DELETE FROM usuario WHERE id = ?";

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

    function login($env, $user)
    {
        $query = null;

        switch ($env) {
            case 'ADMIN':
                $query = "SELECT
                                a.id,
                                a.nome,
                                a.email,
                                a.login,
                                a.senha,
                                a.dt_criacao,
                                a.dt_alteracao,
                                b.id AS usuario_tipo_id,
                                b.descricao AS usuario_tipo_descricao,
                                c.id AS instituicao_id,
                                c.nome AS instituicao_nome,
                                c.logo AS instituicao_logo,
                                c.logo_content_type AS instituicao_logo_content_type,
                                c.uf AS instituicao_uf,
                                c.dt_criacao AS instituicao_dt_criacao,
                                c.dt_alteracao AS instituicao_dt_alteracao
                            FROM usuario AS a
                            INNER JOIN usuario_tipo AS b ON (b.id = a.usuario_tipo_id)
                            LEFT OUTER JOIN instituicao c ON (c.id = a.instituicao_id)
                            WHERE a.login = :login
                                AND a.usuario_tipo_id = 1
                            LIMIT 0, 1";

                // sanitize
                $user = htmlspecialchars(strip_tags($user));

                break;
            case 'SITE':
                $query = "SELECT
                                a.id,
                                a.nome,
                                a.email,
                                a.login_ftd AS login,
                                a.senha_ftd AS senha,
                                a.dt_criacao,
                                a.dt_alteracao,
                                b.id AS usuario_tipo_id,
                                b.descricao AS usuario_tipo_descricao,
                                c.id AS instituicao_id,
                                c.nome AS instituicao_nome,
                                c.logo AS instituicao_logo,
                                c.logo_content_type AS instituicao_logo_content_type,
                                c.uf AS instituicao_uf,
                                c.dt_criacao AS instituicao_dt_criacao,
                                c.dt_alteracao AS instituicao_dt_alteracao
                            FROM usuario AS a
                            INNER JOIN usuario_tipo AS b ON (b.id = a.usuario_tipo_id)
                            LEFT OUTER JOIN instituicao c ON (c.id = a.instituicao_id)
                            WHERE a.login_ftd = :login
                                AND a.usuario_tipo_id = 2
                            LIMIT 0, 1";

                // sanitize
                $user = htmlspecialchars(strip_tags($user));
                break;
            default:
                return null;
                break;
        }

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind data
        $stmt->bindParam(":login", $user);

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

            $this->id = $id;
            $this->nome = (is_null($nome)) ? null : html_entity_decode($nome);
            $this->email = (is_null($email)) ? null : html_entity_decode($email);
            $this->login = $login;
            $this->senha = $senha;
            $this->dt_criacao = $dt_criacao;
            $this->dt_alteracao = $dt_alteracao;

            $this->tipo_usuario = new TipoUsuario();
            $this->tipo_usuario->id = $usuario_tipo_id;
            $this->tipo_usuario->descricao = html_entity_decode($usuario_tipo_descricao);

            if ($instituicao_id == null) {
                $this->instituicao = null;
            } else {
                $this->instituicao = new Instituicao();
                $this->instituicao->id = $instituicao_id;
                $this->instituicao->nome = (is_null($instituicao_nome)) ? null : html_entity_decode($instituicao_nome);
                $this->instituicao->logo = (is_null($instituicao_logo)) ? null : "base64," .  base64_encode($instituicao_logo);
                $this->instituicao->logo_content_type = (is_null($instituicao_logo_content_type)) ? null : "data:" . $instituicao_logo_content_type . ";";
                $this->instituicao->uf = (is_null($instituicao_uf)) ? null : strtoupper(html_entity_decode($instituicao_uf));
                $this->instituicao->dt_criacao = $instituicao_dt_criacao;
                $this->instituicao->dt_alteracao = $instituicao_dt_alteracao;
            }
        }

        return $this;
    }
}
