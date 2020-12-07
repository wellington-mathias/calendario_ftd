<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if(strtoupper($_SERVER["REQUEST_METHOD"]) !== "PUT") {
    http_response_code(405);
    exit();
}

include_once '../objects/usuario.php';
include_once '../objects/tipo_usuario.php';

// get posted data
$data = json_decode(file_get_contents("php://input"));
  
// make sure data is not empty
$data_incomplete = empty($data->tipo_usuario) && empty($data->nome);

if (empty($data->login) && empty($data->password)) {
    $data_incomplete = $data_incomplete && (empty($data->login_ftd) && empty($data->login_ftd));
}

if($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to create usuario. Data is incomplete."));
} else {
    $messages = array("message" => array());

    $usuario = new Usuario();

    // set usuario property values
    $usuario->tipo_usuario = $data->tipo_usuario;
    $usuario->nome = $data->nome;
    $usuario->email = empty($data->email) ? null : $data->email;
    $usuario->login = empty($data->login) ? null : $data->login;
    $usuario->senha = empty($data->password) ? null : $data->password; 
    $usuario->login_ftd = empty($data->login_ftd) ? null : $data->login_ftd;
    $usuario->senha_ftd = empty($data->password_ftd) ? null : $data->password_ftd;

    if ($usuario->tipo_usuario->id == null) {
        $data_incomplete = empty($data->tipo_usuario->descricao);

        if ($data_incomplete) {
            // set response code - 400 bad request
            http_response_code(400);
            
            // tell the user
            echo json_encode(array("message" => "Unable to create tipo de usuario. Data is incomplete."));

            die();
        } else {
            $tipo_usuario = new TipoUsuario();

            $tipo_usuario->descricao = $usuario->tipo_usuario->descricao;

            if(!$tipo_usuario->create()) {
                // set response code - 503 service unavailable
                http_response_code(503);
          
                // tell the user
                echo json_encode(array("message" => "Unable to create tipo de usuario."));

                die();
            } else {
                $usuario->tipo_usuario->id = $tipo_usuario->id;
            }
        }
    }

    // create the usuario
    if(!$usuario->create()) {
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to create usuario."));
    } else {
  
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(
            array (
                "id" => $usuario->id,
                "message" => "Usuario was created.")
        );
    }
}
?>