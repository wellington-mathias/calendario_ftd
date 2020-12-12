<?php
include_once '../objects/crud_object.php';
include_once '../objects/tipo_usuario.php';

class Usuario extends CrudObject {
    // object properties
    public $id;
    public $tipo_usuario;
    public $nome;
    public $email;
    public $login;
    public $senha;
    public $login_ftd;
    public $senha_ftd;
    public $dt_criacao;
    public $dt_alteracao;

    public function __constructor() {
        parent::__construct();
    }

   // create method
   function create() {
        // query to insert record
        $query = "INSERT INTO usuario
                    SET
                        usuario_tipo_id = :tipo_usuario_id,
                        nome = :nome,
                        email = :email,
                        login = :login,
                        senha = :senha,
                        login_ftd = :login_ftd,
                        senha_ftd = :senha_ftd";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->tipo_usuario->id = (int) htmlspecialchars(strip_tags($this->tipo_usuario->id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = (is_null ($this->email)) ? null : htmlspecialchars(strip_tags($this->email));
        $this->login = (is_null ($this->login)) ? null : htmlspecialchars(strip_tags($this->login));
        $this->senha = (is_null ($this->senha)) ? null : password_hash(htmlspecialchars(strip_tags($this->senha)), PASSWORD_DEFAULT);
        $this->login_ftd = (is_null ($this->login_ftd)) ? null : htmlspecialchars(strip_tags($this->login_ftd));
        $this->senha_ftd = (is_null ($this->senha_ftd)) ? null : password_hash(htmlspecialchars(strip_tags($this->senha_ftd)), PASSWORD_DEFAULT);


        // bind values
        $stmt->bindParam(":tipo_usuario_id", $this->tipo_usuario->id);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":login", $this->login);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":login_ftd", $this->login_ftd);
        $stmt->bindParam(":senha_ftd", $this->senha_ftd);


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
                    u.usuario_tipo_id AS tipo_usuario_id,
                    tu.descricao AS tipo_usuario_descricao,
                    u.nome,
                    u.email,
                    u.dt_criacao,
                    u.dt_alteracao
                FROM usuario u
                INNER JOIN usuario_tipo tu ON (tu.id = u.usuario_tipo_id)
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

                $tipo_usuario = new TipoUsuario();

                $tipo_usuario->id = $tipo_usuario_id;
                $tipo_usuario->descricao = html_entity_decode($tipo_usuario_descricao);
                
                $usuario = new Usuario();

                $usuario->id = $id;
                $usuario->tipo_usuario = $tipo_usuario;
                $usuario->nome = html_entity_decode($nome);
                $usuario->email = (is_null ($email)) ? null: html_entity_decode($email);
                $usuario->dt_criacao = $dt_criacao;
                $usuario->dt_alteracao = $dt_alteracao;

                array_push($objects_arr, $usuario);
            }
        }

        return $objects_arr;
    }

    // read one product
    function readOne() {
        // query to read single record
        $query = "SELECT
                    u.id,
                    u.usuario_tipo_id AS tipo_usuario_id,
                    tu.descricao AS tipo_usuario_descricao,
                    u.nome,
                    u.email,
                    u.dt_criacao,
                    u.dt_alteracao
                    FROM usuario u
                INNER JOIN usuario_tipo tu ON (tu.id = u.usuario_tipo_id)
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

            $tipo_usuario = new TipoUsuario();
            $tipo_usuario->id = $tipo_usuario_id;
            $tipo_usuario->descricao = html_entity_decode($tipo_usuario_descricao);

            $this->tipo_usuario = $tipo_usuario;
            $this->nome = html_entity_decode($nome);
            $this->email = (is_null ($email)) ? null: html_entity_decode($email);
            $this->dt_criacao = $dt_criacao;
            $this->dt_alteracao = $dt_alteracao;
        }
        
        return $this;
    }

    // update method
    function update() {
        // update query
        $query = "UPDATE usuario
                    SET
                        usuario_tipo_id = :tipo_usuario_id,
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
        $stmt->bindParam(":tipo_usuario_id", $this->tipo_usuario->id);
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
        $query = "DELETE FROM usuario WHERE id = ?";
    
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

    function login($env, $user, $pass) {
        $query = null;

        switch ($env) {
            case 'ADMIN':
                $query = "SELECT
                                u.id,
                                u.usuario_tipo_id AS tipo_usuario_id,
                                tu.descricao AS tipo_usuario_descricao,
                                u.nome,
                                u.email,
                                u.login,
                                u.senha,
                                u.dt_criacao,
                                u.dt_alteracao
                            FROM usuario AS u
                            INNER JOIN usuario_tipo AS tu ON (tu.id = u.usuario_tipo_id)
                            WHERE login = :login
                                AND usuario_tipo_id = 1
                            LIMIT 0, 1";

                // sanitize
                $user = htmlspecialchars(strip_tags($user));

                break;
            case 'SITE':
                $query = "SELECT
                                u.id,
                                u.usuario_tipo_id AS tipo_usuario_id,
                                tu.descricao AS tipo_usuario_descricao,
                                u.nome,
                                u.email,
                                u.login_ftd AS login,
                                u.senha_ftd AS senha,
                                u.dt_criacao,
                                u.dt_alteracao
                            FROM usuario AS u
                            INNER JOIN usuario_tipo AS tu ON (tu.id = u.usuario_tipo_id)
                            WHERE login_ftd = :login
                                AND usuario_tipo_id = 2
                            LIMIT 0, 1";

                // sanitize
                $user = htmlspecialchars(strip_tags($user));
                break;
            default:
                return null;
            break;
        }

        // prepare query statement
        $stmt = $this->conn->prepare( $query );

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

            $tipo_usuario = new TipoUsuario();
            $tipo_usuario->id = $tipo_usuario_id;
            $tipo_usuario->descricao = html_entity_decode($tipo_usuario_descricao);

            $this->tipo_usuario = $tipo_usuario;
            $this->id = $id;
            $this->nome = html_entity_decode($nome);
            $this->email = (is_null ($email)) ? null: html_entity_decode($email);
            $this->login = $login;
            $this->senha = $senha;
            $this->dt_criacao = $dt_criacao;
            $this->dt_alteracao = $dt_alteracao;
        }
        
        return $this;
        
    }
}
?>