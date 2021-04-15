<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if (strtoupper($_SERVER["REQUEST_METHOD"]) !== "GET") {
    sendMessage(405, null);
}

// include database and object files
include_once '../config/database.php';
include_once '../objects/usuario.php';

if (isset($_GET['id'])) {
    readOne();
} elseif (isset($_GET['tipo'])) {
    readByType();
} else {
    readAll();
}

function getObject()
{
    // get database connection
    $database = new Database();
    $db = $database->getConnection();

    // prepare usuario object
    return new Usuario($db);
}

function readAll()
{
    // Retrieve the usuario object
    $usuario = getObject();

    // query objects
    $usuarios = $usuario->read();

    // check if more than 0 record found
    if (count($usuarios) == 0) {
        sendMessage(404, array("message" => "Nenhum usuario encontrado."));
    } else {
        // objects array
        $objects_arr = array();
        $objects_arr["usuarios"] = array();

        foreach ($usuarios as $usuario) {
            array_push($objects_arr["usuarios"], getDataAsArray($usuario));
        }

        sendMessage(200, $objects_arr);
    }
}

function readOne()
{
    if (empty($_GET['id'])) {
        sendMessage(400, array("message" => "Unable to read usuario. No id informed."));
    } else {
        // Retrieve the usuario object
        $usuario = getObject();

        $usuario->id = $_GET['id'];

        // read the details of usuario to be edited
        $usuario = $usuario->readOne();

        // check if the object is not null
        if ($usuario == null) {
            sendMessage(404, array("message" => "usuario does not exist."));
        } else {
            sendMessage(200, getDataAsArray($usuario));
        }
    }
}

function readByType()
{
    // Retrieve the usuario object
    $usuario = getObject();

    $tipo_id = $_GET['tipo'];

    // query objects
    $usuarios = $usuario->readByType($tipo_id);

    // check if more than 0 record found
    if (count($usuarios) == 0) {
        sendMessage(404, array("message" => "Nenhum usuario encontrado."));
    } else {
        // objects array
        $objects_arr = array();
        $objects_arr["usuarios"] = array();

        foreach ($usuarios as $usuario) {
            array_push($objects_arr["usuarios"], getDataAsArray($usuario));
        }

        sendMessage(200, $objects_arr);
    }
}

function sendMessage($http_code, $response_data)
{
    // set response code - 400 bad request
    http_response_code($http_code);

    if ($response_data != null) {
        // tell the user
        echo json_encode($response_data);
    }

    exit();
}

function getDataAsArray($dataAsObject)
{
    $dataAsArray = array(
        "id" => $dataAsObject->id,
        "nome" => $dataAsObject->nome,
        "email" =>  $dataAsObject->email,
        "dt_criacao" => $dataAsObject->dt_criacao,
        "dt_alteracao" => $dataAsObject->dt_alteracao,
        "tipo_usuario" => array(
            "id" => $dataAsObject->tipo_usuario->id,
            "descricao" => $dataAsObject->tipo_usuario->descricao
        )
    );

    if ($dataAsObject->instituicao != null) {
        $dataAsArray["instituicao"] = array(
            "id" => $dataAsObject->instituicao->id,
            "nome" => $dataAsObject->instituicao->nome,
            "logo" => $dataAsObject->instituicao->logo_content_type .  $dataAsObject->instituicao->logo,
            "uf" => $dataAsObject->instituicao->uf,
            "dt_criacao" => $dataAsObject->instituicao->dt_criacao,
            "dt_alteracao" => $dataAsObject->instituicao->dt_alteracao
        );
    }

    return $dataAsArray;
}
