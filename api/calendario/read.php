<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if(strtoupper($_SERVER["REQUEST_METHOD"]) !== "GET") {
    http_response_code(405);
    exit();
}

// includes
include_once '../objects/calendario.php';

if (isset($_GET['id'])) {
    readOne();
} else {
    readAll();
}

function readAll() {
    // Retrieve object
    $calendario = new Calendario();

    // query objects
    $calendarios = $calendario->read();
    
    // check if more than 0 record found
    if (count($calendarios) == 0) {
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user no object found
        echo json_encode(array("message" => "Nenhum Calendario encontrado."));
    } else {
        // objects array
        $objects_arr = array();
        $objects_arr["calendarios"] = array();
    
        foreach($calendarios as $calendario) {

            $instituicao_item = array(
                "id" => $calendario->instituicao->id,
                "nome" => $calendario->instituicao->nome,
                "logo" => $calendario->instituicao->logo,
                "uf" => $calendario->instituicao->uf,
                "dt_criacao" => $calendario->instituicao->dt_criacao,
                "dt_alteracao" => $calendario->instituicao->dt_alteracao
            );

            $calendario_item = array(
                "id" => $calendario->id,
                "ano_referencia" => $calendario->ano_referencia,
                "dt_inicio_ano_letivo" => $calendario->dt_inicio_ano_letivo,
                "dt_fim_ano_letivo" => $calendario->dt_fim_ano_letivo,
                "dt_inicio_recesso" => $calendario->dt_inicio_recesso,
                "dt_fim_recesso" => $calendario->dt_fim_recesso,
                "dt_criacao" => $calendario->dt_criacao,
                "dt_alteracao" => $calendario->dt_alteracao,
                "instituicao" =>$instituicao_item
            );

            array_push($objects_arr["calendarios"], $calendario_item);
        }
    
        // set response code - 200 OK
        http_response_code(200);
    
        // show objects data in json format
        echo json_encode($objects_arr);
    }
}

function readOne() {
    if (empty($_GET['id'])) {
        // set response code - 400 bad request
        http_response_code(400);
        
        // tell the user
        echo json_encode(array("message" => "Unable to read Calendario. No id informed."));
    } else {
        // Retrieve object
        $calendario = new Calendario();

        $calendario->id = $_GET['id'];

        // read the details of calendario to be edited
        $calendario = $calendario->readOne();

        // check if the object is not null
        if($calendario == null)  {
            // set response code - 404 Not found
            http_response_code(404);
        
            // tell the user object does not exist
            echo json_encode(array("message" => "Calendario does not exist."));
        } else {
            $instituicao_item = array(
                "id" => $calendario->instituicao->id,
                "nome" => $calendario->instituicao->nome,
                "logo" => $calendario->instituicao->logo,
                "uf" => $calendario->instituicao->uf,
                "dt_criacao" => $calendario->instituicao->dt_criacao,
                "dt_alteracao" => $calendario->instituicao->dt_alteracao
            );

            $calendario_item = array(
                "id" => $calendario->id,
                "ano_referencia" => $calendario->ano_referencia,
                "dt_inicio_ano_letivo" => $calendario->dt_inicio_ano_letivo,
                "dt_fim_ano_letivo" => $calendario->dt_fim_ano_letivo,
                "dt_inicio_recesso" => $calendario->dt_inicio_recesso,
                "dt_fim_recesso" => $calendario->dt_fim_recesso,
                "dt_criacao" => $calendario->dt_criacao,
                "dt_alteracao" => $calendario->dt_alteracao,
                "instituicao" =>$instituicao_item
            );
        
            // set response code - 200 OK
            http_response_code(200);
        
            // make it json format
            echo json_encode($calendario_item);
        }
    }
}
?>