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
include_once '../objects/tipo_evento.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare evento object
$evento = new TipoEvento($db);

// get data to be updated
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
$data_incomplete = empty($data->id) && empty($data->descricao);

if($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to update tipo de evento. Data is incomplete."));
} else {
    // set ID property to be edited
    $evento->id = $data->id;
    
    // set property values
    $evento->descricao = $data->descricao;
    
    // update the evento
    if (!$evento->update()) {
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to update o tipo de evento."));
    } else {
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "Tipo de Evento was updated."));
    }
}
?>