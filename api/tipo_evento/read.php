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

// include database and object files
include_once '../config/database.php';
include_once '../objects/tipo_evento.php';

if (isset($_GET['id'])) {
    readOne();
} else {
    readAll();
}

function getObject() {
    // get database connection
    $database = new Database();
    $db = $database->getConnection();

    // prepare evento object
    return new TipoEvento($db);
}

function readAll() {
    // Retrieve the evento object
    $tipo_evento = getObject();

    // query
    $stmt = $tipo_evento->read();
    $num = $stmt->rowCount();
    
    // check if more than 0 record found
    if ($num == 0) {
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user no eventos found
        echo json_encode(array("message" => "Nenhum tipo de evento encontrado."));
    } else {
        // eventos array
        $tipo_eventos_arr = array();
    
        // retrieve our table contents
        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row
            // this will make $row['name'] to
            // just $name only
            extract($row);
    
            $tipo_evento_item = array(
                "id" => $id,
                "descricao" => html_entity_decode($descricao)
            );

            array_push($tipo_eventos_arr, $tipo_evento_item);
        }
    
        // set response code - 200 OK
        http_response_code(200);
    
        // show eventos data in json format
        echo json_encode($tipo_eventos_arr);
    }
}

function readOne() {
    if (empty($_GET['id'])) {
        // set response code - 400 bad request
        http_response_code(400);
        
        // tell the user
        echo json_encode(array("message" => "Unable to read tipo de evento. No id informed."));
    } else {
        // Retrieve the object
        $tipo_evento = getObject();

        $tipo_evento->id = $_GET['id'];

        // read the details of evento to be edited
        $tipo_evento->readOne();

        // check if the object is not null
        if ($tipo_evento->descricao == null)  {
            // set response code - 404 Not found
            http_response_code(404);
        
            // tell the user evento does not exist
            echo json_encode(array("message" => "evento does not exist."));
        } else {
            $tipo_evento_item=array(
                "id" => $tipo_evento->id,
                "descricao" => html_entity_decode($tipo_evento->descricao)
            );
        
            // set response code - 200 OK
            http_response_code(200);
        
            // make it json format
            echo json_encode($tipo_evento_item);
        }
    }
}
?>