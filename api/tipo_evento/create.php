<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if (strtoupper($_SERVER["REQUEST_METHOD"]) !== "PUT") {
    http_response_code(405);
    exit();
}

include_once '../config/database.php';
include_once '../objects/tipo_evento.php';

$database = new Database();
$db = $database->getConnection();

$tipo_evento = new TipoEvento($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
$data_incomplete = empty($data->descricao);

if ($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Unable to create tipo de evento. Data is incomplete."));
} else {
    // set evento property values
    $tipo_evento->descricao = $data->descricao;

    // create the evento
    if (!$tipo_evento->create()) {
        // set response code - 503 service unavailable
        http_response_code(503);

        // tell the user
        echo json_encode(array("message" => "Unable to create tipo de evento."));
    } else {

        // set response code - 201 created
        http_response_code(201);

        // tell the user
        echo json_encode(array("message" => "tipo de evento was created."));
    }
}
