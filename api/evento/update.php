<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if (strtoupper($_SERVER["REQUEST_METHOD"]) !== "POST") {
    http_response_code(405);
    exit();
}

// includes
include_once '../objects/evento.php';

// get data to be updated
$data = json_decode(file_get_contents("php://input"));

$data_incomplete = empty($data->id) && empty($data->tipo_evento->id) && empty($data->dt_inicio) && empty($data->dt_fim) && empty($data->titulo) && empty($data->dia_letivo);

if ($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Unable to update evento. Data is incomplete."));
} else {
    // prepare evento object
    $evento = new Evento();

    // set ID property to be edited
    $evento->id = $data->id;

    // set property values
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
