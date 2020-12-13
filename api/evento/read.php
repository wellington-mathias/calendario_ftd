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
include_once '../objects/evento.php';

if (isset($_GET['id'])) {
    readOne();
} else {
    readAll();
}

function getObject() {
    // prepare evento object
    return new Evento();
}

function readAll() {
    // Retrieve the evento object
    $evento = getObject();

    // query eventos
    $stmt = $evento->read();
    $num = $stmt->rowCount();
    
    // check if more than 0 record found
    if ($num == 0) {
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user no eventos found
        echo json_encode(array("message" => "Nenhum evento encontrado."));
    } else {
        // eventos array
        $eventos_arr = array();
        $eventos_arr["eventos"] = array();
    
        // retrieve our table contents
        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row
            // this will make $row['name'] to
            // just $name only
            extract($row);
    
            $tipo_evento_item = array(
                "id" => $tipo_evento_id,
                "descricao" => $tipo_evento_descricao
            );

            $evento_item=array(
                "id" => $id,
                "tipo_evento" => $tipo_evento_item,
                "dt_inicio" => $dt_inicio,
                "dt_fim" => $dt_fim,
                "titulo" => (is_null ($titulo)) ? null: html_entity_decode($titulo),
                "descricao" => (is_null ($descricao)) ? null: html_entity_decode($descricao),
                "uf" => (is_null ($uf)) ? null: strtoupper($uf),
                "dia_letivo" => (bool) $dia_letivo,
                "dt_criacao" => $dt_criacao,
                "dt_alteracao" => $dt_alteracao
            );
    
            array_push($eventos_arr["eventos"], $evento_item);
        }
    
        // set response code - 200 OK
        http_response_code(200);
    
        // show eventos data in json format
        echo json_encode($eventos_arr);
    }
}

function readOne() {
    if (empty($_GET['id'])) {
        // set response code - 400 bad request
        http_response_code(400);
        
        // tell the user
        echo json_encode(array("message" => "Unable to read evento. No id informed."));
    } else {
        // Retrieve the evento object
        $evento = getObject();

        $evento->id = $_GET['id'];

        // read the details of evento to be edited
        $evento->readOne();

        // check if the object is not null
        if($evento->titulo == null)  {
            // set response code - 404 Not found
            http_response_code(404);
        
            // tell the user evento does not exist
            echo json_encode(array("message" => "evento does not exist."));
        } else {
            $tipo_evento_item = array(
                "id" => $evento->tipo_evento_id,
                "descricao" => $evento->tipo_evento_descricao
            );

            $evento_item=array(
                "id" => $evento->id,
                "tipo_evento" => $tipo_evento_item,
                "dt_inicio" => $evento->dt_inicio,
                "dt_fim" => $evento->dt_fim,
                "titulo" => html_entity_decode($evento->titulo),
                "descricao" => (is_null ($evento->descricao)) ? null: html_entity_decode($evento->descricao),
                "uf" => (is_null ($evento->uf)) ? null: strtoupper($evento->uf),
                "dia_letivo" => (bool) $evento->dia_letivo,
                "dt_criacao" => $evento->dt_criacao,
                "dt_alteracao" => $evento->dt_alteracao
            );
        
            // set response code - 200 OK
            http_response_code(200);
        
            // make it json format
            echo json_encode($evento_item);
        }
    }
}
?>