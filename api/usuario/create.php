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

include_once '../config/database.php';
include_once '../objects/usuario.php';

$database = new Database();
$db = $database->getConnection();

$usuario = new Usuario($db);
  
// get posted data
$data = json_decode(file_get_contents("php://input"));
  
// make sure data is not empty
$data_incomplete = empty($data->tipo_usuario->id) && empty($data->nome) && empty($data->login) && empty($data->password);

if($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to create usuario. Data is incomplete."));
} else {
    // set usuario property values
    $usuario->tipo_usuario = $data->tipo_usuario;
    $usuario->nome = $data->nome;
    $usuario->chave = $data->login;
    $usuario->senha = $data->password; 
    
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