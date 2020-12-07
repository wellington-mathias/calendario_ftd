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
include_once '../objects/calendario.php';
include_once '../objects/instituicao.php';

// get posted data
$data = json_decode(file_get_contents("php://input"));
  
// make sure data is not empty
$data_incomplete = empty($data->ano_referencia);
$data_incomplete = $data_incomplete && empty($data->dt_inicio_ano_letivo) && empty($data->dt_fim_ano_letivo);
$data_incomplete = $data_incomplete && empty($data->dt_inicio_recesso) && empty($data->dt_fim_recesso);
$data_incomplete = $data_incomplete && empty($data->instituicao);

if($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to create Calendario. Data is incomplete."));
} else {
    $calendario = new Calendario();

    // set calendario property values
    $calendario->ano_referencia = $data->ano_referencia;
    $calendario->dt_inicio_ano_letivo = $data->dt_inicio_ano_letivo;
    $calendario->dt_fim_ano_letivo = $data->dt_fim_ano_letivo;
    $calendario->dt_inicio_recesso = $data->dt_inicio_recesso;
    $calendario->dt_fim_recesso = $data->dt_fim_recesso;
    $calendario->instituicao =  $data->instituicao;

    if ($calendario->instituicao->id == null) {
        $data_incomplete = empty($data->instituicao->nome) && empty($data->instituicao->logo) && empty($data->instituicao->uf);

        if ($data_incomplete) {
            // set response code - 400 bad request
            http_response_code(400);
            
            // tell the user
            echo json_encode(array("message" => "Unable to create tipo de usuario. Data is incomplete."));

            die();
        } else {
            // set response code - 400 bad request
            http_response_code(400);

            // tell the user
            echo json_encode(array("message" => "Create Instituicao not defined yet."));

            die();
        }
    }
    
    // create the calendario
    if(!$calendario->create()) {
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to create calendario."));
    } else {
  
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(
            array (
                "id" => $calendario->id,
                "message" => "Calendario was created.")
        );
    }
}
?>