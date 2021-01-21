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
include_once '../objects/instituicao.php';

if (isset($_GET['id'])) {
    readOne();
} else {
    readAll();
}

function readAll() {
    // Retrieve object
    $instituicao = new Instituicao();

    // query objects
    $instituicoes = $instituicao->read();
    
    // check if more than 0 record found
    if (count($instituicoes) == 0) {
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user no object found
        echo json_encode(array("message" => "Nenhuma Instituicao encontrada."));
    } else {
        // objects array
        $objects_arr = array();
        $objects_arr["instituicoes"] = array();

        foreach($instituicoes as $instituicao) {

            $instituicao_item = array(
                "id" => $instituicao->id,
                "nome" => $instituicao->nome,
                "logo" => "data:" . $instituicao->logo_content_type . ";base64," .  base64_encode($instituicao->logo),
                "uf" => $instituicao->uf,
                "dt_criacao" => $instituicao->dt_criacao,
                "dt_alteracao" => $instituicao->dt_alteracao
            );
    
            array_push($objects_arr["instituicoes"], $instituicao_item);
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
        echo json_encode(array("message" => "Unable to read Instituicao. No id informed."));
    } else {
        // Retrieve the object
        $instituicao = new Instituicao();

        $instituicao->id = $_GET['id'];

        // read the details of usuario to be edited
        $instituicao = $instituicao->readOne();

        // check if the object is not null
        if ($instituicao == null) {
            // set response code - 404 Not found
            http_response_code(404);
        
            // tell the user object does not exist
            echo json_encode(array("message" => "Instituicao does not exist."));
        } else {
            $instituicao_item = array(
                "id" => $instituicao->id,
                "nome" => $instituicao->nome,
                "logo" => "data:" . $instituicao->logo_content_type . ";base64," .  $instituicao->logo,
                "uf" => $instituicao->uf,
                "dt_criacao" => $instituicao->dt_criacao,
                "dt_alteracao" => $instituicao->dt_alteracao
            );
        
            // set response code - 200 OK
            http_response_code(200);
        
            // make it json format
            echo json_encode($instituicao_item);
        }
    }
}
?>