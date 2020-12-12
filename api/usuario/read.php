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
include_once '../objects/usuario.php';

if (isset($_GET['id'])) {
    readOne();
} else if (isset($_GET['tipo'])) {
    readByType();
} else {
    readAll();
}

function getObject() {
    // get database connection
    $database = new Database();
    $db = $database->getConnection();

    // prepare usuario object
    return new Usuario($db);
}

function readAll() {
    // Retrieve the usuario object
    $usuario = getObject();

    // query objects
    $usuarios = $usuario->read();
    
    // check if more than 0 record found
    if (count($usuarios) == 0) {
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user no object found
        echo json_encode(array("message" => "Nenhum usuario encontrado."));
    } else {
        // objects array
        $objects_arr = array();
        $objects_arr["usuarios"] = array();

        foreach($usuarios as $usuario) {
            $tipo_usuario_item = array(
                "id" => $usuario->tipo_usuario->id,
                "descricao" => $usuario->tipo_usuario->descricao
            );

            $usuario_item = array(
                "id" => $usuario->id,
                "tipo_usuario" =>$tipo_usuario_item,
                "nome" => $usuario->nome,
                "email" => $usuario->email,
                "dt_criacao" => $usuario->dt_criacao,
                "dt_alteracao" => $usuario->dt_alteracao
            );
    
            array_push($objects_arr["usuarios"], $usuario_item);
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
        echo json_encode(array("message" => "Unable to read usuario. No id informed."));
    } else {
        // Retrieve the usuario object
        $usuario = getObject();

        $usuario->id = $_GET['id'];

        // read the details of usuario to be edited
        $usuario = $usuario->readOne();

        // check if the object is not null
        if ($usuario == null) {
            // set response code - 404 Not found
            http_response_code(404);
        
            // tell the user usuario does not exist
            echo json_encode(array("message" => "usuario does not exist."));
        } else {
            $tipo_usuario_item = array(
                "id" => $usuario->tipo_usuario->id,
                "descricao" => $usuario->tipo_usuario->descricao
            );

            $usuario_item = array(
                "id" => $usuario->id,
                "tipo_usuario" =>$tipo_usuario_item,
                "nome" => $usuario->nome,
                "email" => $usuario->email,
                "dt_criacao" => $usuario->dt_criacao,
                "dt_alteracao" => $usuario->dt_alteracao
            );
        
            // set response code - 200 OK
            http_response_code(200);
        
            // make it json format
            echo json_encode($usuario_item);
        }
    }
}

function readByType() {
    // Retrieve the usuario object
    $usuario = getObject();

    $tipo_id = $_GET['tipo'];

    // query objects
    $usuarios = $usuario->readByType($tipo_id);
    
    // check if more than 0 record found
    if (count($usuarios) == 0) {
        // set response code - 404 Not found
        http_response_code(404);
    
        // tell the user no object found
        echo json_encode(array("message" => "Nenhum usuario encontrado."));
    } else {
        // objects array
        $objects_arr = array();
        $objects_arr["usuarios"] = array();

        foreach($usuarios as $usuario) {
            $tipo_usuario_item = array(
                "id" => $usuario->tipo_usuario->id,
                "descricao" => $usuario->tipo_usuario->descricao
            );

            $usuario_item = array(
                "id" => $usuario->id,
                "tipo_usuario" =>$tipo_usuario_item,
                "nome" => $usuario->nome,
                "email" => $usuario->email,
                "dt_criacao" => $usuario->dt_criacao,
                "dt_alteracao" => $usuario->dt_alteracao
            );
    
            array_push($objects_arr["usuarios"], $usuario_item);
        }
    
        // set response code - 200 OK
        http_response_code(200);
    
        // show objects data in json format
        echo json_encode($objects_arr);
    }
}
?>