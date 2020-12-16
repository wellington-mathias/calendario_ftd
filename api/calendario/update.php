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
include_once '../objects/calendario.php';

// get posted data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
$data_incomplete = empty($data->id) && empty($data->ano_referencia);
$data_incomplete = $data_incomplete && empty($data->dt_inicio_ano_letivo) && empty($data->dt_fim_ano_letivo);
$data_incomplete = $data_incomplete && empty($data->dt_inicio_recesso) && empty($data->dt_fim_recesso);
$data_incomplete = $data_incomplete && empty($data->usuario->id);

if($data_incomplete) {
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to update Calendario. Data is incomplete."));
} else {
     // prepare object
     $obj = new Calendario();

     // set ID property to be edited
     $obj->id = $data->id;

    // set property values
    $obj->ano_referencia = $data->ano_referencia;
    $obj->dt_inicio_ano_letivo = $data->dt_inicio_ano_letivo;
    $obj->dt_fim_ano_letivo = $data->dt_fim_ano_letivo;
    $obj->dt_inicio_recesso = $data->dt_inicio_recesso;
    $obj->dt_fim_recesso = $data->dt_fim_recesso;
    $obj->qtde_volumes_1o_ano = $data->qtde_volumes_1o_ano;
    $obj->qtde_volumes_2o_ano = $data->qtde_volumes_2o_ano;
    $obj->qtde_volumes_3o_ano = $data->qtde_volumes_3o_ano;
    $obj->revisao_volume_3o_ano = $data->revisao_volume_3o_ano;
    $obj->usuario = $data->usuario;
    
    // update the evento
    if (!$obj->update()) {
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to update Calendario."));
    } else {
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "Calendario was updated."));
    }
}
?>