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

// includes
include_once '../objects/evento.php';

// get posted data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
$data_incomplete = empty($data->tipo_evento->id) && empty($data->dt_inicio) && empty($data->dt_fim) && empty($data->titulo) && empty($data->dia_letivo);

if ($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Unable to create evento. Data is incomplete."));
} else {
    $evento = new Evento();

    // set evento property values
    $evento->tipo_evento_id = $data->tipo_evento->id;
    $evento->dt_inicio = $data->dt_inicio;
    $evento->dt_fim = $data->dt_fim;
    $evento->titulo = $data->titulo;
    $evento->descricao = $data->descricao;
    $evento->uf =  $data->uf;
    $evento->dia_letivo = $data->dia_letivo;

    // create the evento
    if (!$evento->create()) {
        // set response code - 503 service unavailable
        http_response_code(503);

        // tell the user
        echo json_encode(array("message" => "Unable to create evento."));
    } else {

        // set response code - 201 created
        http_response_code(201);

        // tell the user
        echo json_encode(
            array(
                "id" => $evento->id,
                "message" => "Evento was created."
            )
        );
    }
}
