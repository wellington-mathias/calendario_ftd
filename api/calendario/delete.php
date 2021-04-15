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

// includes
include_once '../objects/calendario.php';

// get data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
$data_incomplete = empty($data->id);

if ($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Unable to delete Calendario. No id informed."));
} else {
    // prepare object
    $obj = new Calendario();

    // set id to be deleted
    $obj->id = $data->id;

    // delete the calendario
    if (!$obj->delete()) {
        // set response code - 503 service unavailable
        http_response_code(503);

        // tell the user
        echo json_encode(array("message" => "Unable to delete Calendario."));
    } else {
        // set response code - 200 ok
        http_response_code(200);

        // tell the user
        echo json_encode(array("message" => "Calendario was deleted."));
    }
}
