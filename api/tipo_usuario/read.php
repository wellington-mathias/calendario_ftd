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
include_once '../objects/tipo_usuario.php';

if (isset($_GET['id'])) {
    readOne();
} else {
    readAll();
}

function getObject() {
    // get database connection
    $database = new Database();
    $db = $database->getConnection();

    // prepare usuario object
    return new TipoUsuario($db);
}

function readAll() {
    // Retrieve the usuario object
    $tipo_usuario = getObject();

    // query
    $stmt = $tipo_usuario->read();
    $num = $stmt->rowCount();
    
    // check if more than 0 record found
    if ($num == 0) {
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user no usuarios found
        echo json_encode(array("message" => "Nenhum tipo de usuario encontrado."));
    } else {
        // usuarios array
        $tipo_usuarios_arr = array();
    
        // retrieve our table contents
        // fetch() is faster than fetchAll()
        // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract row
            // this will make $row['name'] to
            // just $name only
            extract($row);
    
            $tipo_usuario_item = array(
                "id" => $id,
                "descricao" => html_entity_decode($descricao)
            );

            array_push($tipo_usuarios_arr, $tipo_usuario_item);
        }
    
        // set response code - 200 OK
        http_response_code(200);
    
        // show usuarios data in json format
        echo json_encode($tipo_usuarios_arr);
    }
}

function readOne() {
    if (empty($_GET['id'])) {
        // set response code - 400 bad request
        http_response_code(400);
        
        // tell the user
        echo json_encode(array("message" => "Unable to read tipo de usuario. No id informed."));
    } else {
        // Retrieve the object
        $tipo_usuario = getObject();

        $tipo_usuario->id = $_GET['id'];

        // read the details of usuario to be edited
        $tipo_usuario->readOne();

        // check if the object is not null
        if ($tipo_usuario->descricao == null)  {
            // set response code - 404 Not found
            http_response_code(404);
        
            // tell the user usuario does not exist
            echo json_encode(array("message" => "tipo de usuario does not exist."));
        } else {
            $tipo_usuario_item=array(
                "id" => $tipo_usuario->id,
                "descricao" => html_entity_decode($tipo_usuario->descricao)
            );
        
            // set response code - 200 OK
            http_response_code(200);
        
            // make it json format
            echo json_encode($tipo_usuario_item);
        }
    }
}
?>