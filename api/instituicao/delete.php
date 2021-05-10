<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if (strtoupper($_SERVER["REQUEST_METHOD"]) !== "DELETE") {
    http_response_code(405);
    exit();
}

include_once '../objects/instituicao.php';

$data = json_decode(file_get_contents("php://input"));

$data_incomplete = empty($data->id);

if ($data_incomplete) {
    http_response_code(400);

    echo json_encode(array("message" => "Unable to delete Instituicao. No id informed."));
} else {
    $obj = new Instituicao();

    $obj->id = $data->id;

    if (!$obj->delete()) {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to delete Instituicao."));
    } else {
        http_response_code(200);
        echo json_encode(array("message" => "Instituicao was deleted."));
    }
}
