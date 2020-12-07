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

// includes
include_once '../objects/instituicao.php';

// get data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
$data_incomplete = empty($data->id) && empty($data->nome) && empty($data->logo) && empty($data->uf);

if($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to update Instituicao. Data is incomplete."));
} else {
    // prepare object
    $obj = new Instituicao();

    // set ID property to be edited
    $obj->id = $data->id;
    
    // set property values
    $obj->nome = $data->nome;
    $obj->logo = $data->logo;
    $obj->uf = $data->uf;
    
    // update the object
    if (!$obj->update()) {
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to Instituicao usuario."));
    } else {
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "Instituicao was updated."));
    }
}
?>