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

// includes
include_once '../objects/evento.php';

if (isset($_GET['id'])) {
    readOne();
} elseif (isset($_GET['tipo_evento']) || isset($_GET['calendario']) || isset($_GET['uf']) || isset($_GET['dia_letivo'])) {
    search();
} else {
    readAll();
}

function readAll()
{
    // Retrieve the evento object
    $evento = new Evento();

    // query eventos
    $eventos = $evento->read();

    // check if more than 0 record found
    if (count($eventos) == 0) {
        sendMessage(404, array("message" => "Nenhum evento encontrado."));
    } else {
        // objects array
        $objects_arr = array();
        $objects_arr["eventos"] = array();

        foreach ($eventos as $evento) {
            array_push($objects_arr["eventos"], getDataAsArray($evento));
        }

        sendMessage(200, $objects_arr);
    }
}

function readOne()
{
    if (empty($_GET['id'])) {
        sendMessage(400, array("message" => "Unable to read evento. No id informed."));
    } else {
        // Retrieve the evento object
        $evento = new Evento();

        $evento->id = $_GET['id'];

        // read the details of evento to be edited
        $evento->readOne();

        // check if the object is not null
        if ($evento->titulo == null) {
            sendMessage(404, array("message" => "evento does not exist."));
        } else {
            sendMessage(200, getDataAsArray($evento));
        }
    }
}

function search()
{
    $tipo_evento_id = (!isset($_GET['tipo_evento']) || empty($_GET['tipo_evento'])) ? null : (int) $_GET['tipo_evento'];
    $uf = (!isset($_GET['uf'])) ? null : strtoupper(trim(htmlspecialchars(strip_tags($_GET['uf']))));
    $calendario_id = (!isset($_GET['calendario']) || empty($_GET['calendario'])) ? null : (int) $_GET['calendario'];
    $dia_letivo = (!isset($_GET['dia_letivo']) || empty($_GET['dia_letivo'])) ? null : filter_var($_GET['dia_letivo'], FILTER_VALIDATE_BOOLEAN);

    // Retrieve the evento object
    $evento = new Evento();

    // query eventos
    $eventos = $evento->search($tipo_evento_id, $uf, $calendario_id, $dia_letivo);

    // check if more than 0 record found
    if (count($eventos) == 0) {
        sendMessage(404, array("message" => "Nenhum evento encontrado."));
    } else {
        // objects array
        $objects_arr = array();
        $objects_arr["eventos"] = array();

        foreach ($eventos as $evento) {
            array_push($objects_arr["eventos"], getDataAsArray($evento));
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
        "dt_inicio" => $dataAsObject->dt_inicio,
        "dt_fim" => $dataAsObject->dt_fim,
        "titulo" => html_entity_decode($dataAsObject->titulo),
        "descricao" => (is_null($dataAsObject->descricao)) ? null : html_entity_decode($dataAsObject->descricao),
        "uf" => (is_null($dataAsObject->uf)) ? null : strtoupper($dataAsObject->uf),
        "dia_letivo" => (bool) $dataAsObject->dia_letivo,
        "dt_criacao" => $dataAsObject->dt_criacao,
        "dt_alteracao" => $dataAsObject->dt_alteracao,
        "tipo_evento" => array(
            "id" => $dataAsObject->tipo_evento_id,
            "descricao" => $dataAsObject->tipo_evento_descricao
        )
    );

    return $dataAsArray;
}
