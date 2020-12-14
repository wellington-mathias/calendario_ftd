<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if(strtoupper($_SERVER["REQUEST_METHOD"]) !== "POST") {
    http_response_code(405);
    exit();
}

// include database and object files
include_once '../config/database.php';
include_once '../objects/usuario.php';
include_once '../objects/tipo_usuario.php';
include_once '../objects/instituicao.php';

// prepare usuario object
$usuario = new Usuario();
$usuario->tipo_usuario = new TipoUsuario();
$usuario->instituicao = new Instituicao();

// get data to be updated
$data = json_decode(file_get_contents("php://input"));

$data_incomplete = empty($data->id) && empty($data->nome);

if($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to update usuario. Data is incomplete."));
} else {
    // set ID property to be edited
    $usuario->id = $data->id;
    
    // set property values
    $usuario->nome = $data->nome;
    $usuario->dt_alteracao = date('Y-m-d H:i:s');
    $usuario->tipo_usuario->id = $data->tipo_usuario->id;
    $usuario->instituicao->id = empty($data->instituicao) || empty($data->instituicao->id)  ? null : $data->instituicao->id;
    
    // update the usuario
    if (!$usuario->update()) {
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to update usuario."));
    } else {
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "Evento was updated."));
    }
}
?>