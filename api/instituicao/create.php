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

// includes
include_once '../objects/instituicao.php';

// get data
$data = json_decode(file_get_contents("php://input"));
  
// make sure data is not empty
$data_incomplete = empty($data->nome) && empty($data->logo) && empty($data->uf);

if($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to create Instituicao. Data is incomplete."));
} else {
    // prepare object
    $obj = new Instituicao();

    // set instituicao property values
    $obj->nome = $data->nome;
    $obj->logo = $data->logo;
    $obj->uf = $data->uf;

    // create the object
    if(!$obj->create()) {
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to create Instituicao."));
    } else {
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(
            array (
                "id" => $obj->id,
                "message" => "Instituicao was created.")
        );
    }
}
?>