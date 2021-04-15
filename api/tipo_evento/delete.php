<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if (strtoupper($_SERVER["REQUEST_METHOD"]) !== "DELETE") {
    http_response_code(405);
    exit();
}

// include database and object files
include_once '../config/database.php';
include_once '../objects/tipo_evento.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare object
$tipo_evento = new TipoEvento($db);

// get id
$data = json_decode(file_get_contents("php://input"));

if (empty($data->id)) {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Unable to delete tipo de evento. No id informed."));
} else {
    // set id to be deleted
    $tipo_evento->id = $data->id;

    // delete the evento
    if (!$tipo_evento->delete()) {
        // set response code - 503 service unavailable
        http_response_code(503);

        // tell the user
        echo json_encode(array("message" => "Unable to delete tipo de evento."));
    } else {
        // set response code - 200 ok
        http_response_code(200);

        // tell the user
        echo json_encode(array("message" => "Tipo de Evento was deleted."));
    }
}
