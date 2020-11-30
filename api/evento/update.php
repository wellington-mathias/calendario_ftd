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
include_once '../objects/evento.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare evento object
$evento = new Evento($db);

// get id of evento to be edited
$data = json_decode(file_get_contents("php://input"));

$data_incomplete = empty($data->tipo_evento) && empty($data->dt_inicio) && empty($data->dt_fim) && empty($data->titulo) && empty($data->dia_letivo);

if($data_incomplete) {
    // tell the user data is incomplete
  
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to update evento. Data is incomplete."));
} else {
    // set ID property of evento to be edited
    $evento->id = $data->id;
    
    // set evento property values
    $evento->tipo_evento_id = $data->tipo_evento->id;
    $evento->dt_inicio = $data->dt_inicio;
    $evento->dt_fim = $data->dt_fim;
    $evento->titulo = $data->titulo;
    $evento->descricao = $data->descricao;
    $evento->uf = $data->uf;
    $evento->dia_letivo = $data->dia_letivo;
    $evento->dt_alteracao = date('Y-m-d H:i:s');
    
    // update the evento
    if (!$evento->update()) {
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to update evento."));
    } else {
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "Evento was updated."));
    }
}
?>