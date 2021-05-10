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
include_once '../objects/calendario.php';

if (isset($_GET['id'])) {
    readOne();
} elseif (isset($_GET['usuario'])) {
    readByUser($_GET['usuario']);
} else {
    readAll();
}

function readAll()
{
    // Retrieve object
    $calendario = new Calendario();

    // query objects
    $calendarios = $calendario->read();

    // check if more than 0 record found
    if (count($calendarios) == 0) {
        sendMessage(404, array("message" => "Nenhum Calendario encontrado."));
    } else {
        // objects array
        $objects_arr = array();
        $objects_arr["calendarios"] = array();

        foreach ($calendarios as $calendario) {
            array_push($objects_arr["calendarios"], getDataAsArray($calendario));
        }

        sendMessage(200, $objects_arr);
    }
}

function readOne()
{
    if (empty($_GET['id'])) {
        sendMessage(400, array("message" => "Unable to read Calendario. No id informed."));
    } else {
        // Retrieve object
        $calendario = new Calendario();

        $calendario->id = $_GET['id'];

        // read the details of calendario to be edited
        $calendario = $calendario->readOne();

        // check if the object is not null
        if ($calendario == null) {
            sendMessage(404, array("message" => "Calendario does not exist."));
        } else {
            sendMessage(200, getDataAsArray($calendario));
        }
    }
}

function readByUser($usuario_id)
{
    if (empty($usuario_id)) {
        sendMessage(400, array("message" => "Unable to read Calendarios. No User informed."));
    } else {
        // Retrieve object
        $calendario = new Calendario();

        // query objects
        $calendarios = $calendario->readByUser($usuario_id);

        // check if more than 0 record found
        if (count($calendarios) == 0) {
            sendMessage(404, array("message" => "Nenhum Calendario encontrado."));
        } else {
            // objects array
            $objects_arr = array();
            $objects_arr["calendarios"] = array();

            foreach ($calendarios as $calendario) {
                array_push($objects_arr["calendarios"], getDataAsArray($calendario));
            }

            sendMessage(200, $objects_arr);
        }
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
    $logoInst = $dataAsObject->usuario->instituicao->logo_content_type . $dataAsObject->usuario->instituicao->logo;
    return array(
        "id" => $dataAsObject->id,
        "ano_referencia" => $dataAsObject->ano_referencia,
        "dt_inicio_ano_letivo" => $dataAsObject->dt_inicio_ano_letivo,
        "dt_fim_ano_letivo" => $dataAsObject->dt_fim_ano_letivo,
        "dt_inicio_recesso" => $dataAsObject->dt_inicio_recesso,
        "dt_fim_recesso" => $dataAsObject->dt_fim_recesso,
        "qtde_volumes_1o_ano" => $dataAsObject->qtde_volumes_1o_ano,
        "qtde_volumes_2o_ano" => $dataAsObject->qtde_volumes_2o_ano,
        "qtde_volumes_3o_ano" => $dataAsObject->qtde_volumes_3o_ano,
        "revisao_volume_3o_ano" => $dataAsObject->revisao_volume_3o_ano,
        "dt_criacao" => $dataAsObject->dt_criacao,
        "dt_alteracao" => $dataAsObject->dt_alteracao,
        "usuario" => array(
            "id" => $dataAsObject->usuario->id,
            "nome" => $dataAsObject->usuario->nome,
            "email" =>  $dataAsObject->usuario->email,
            "dt_criacao" => $dataAsObject->usuario->dt_criacao,
            "dt_alteracao" => $dataAsObject->usuario->dt_alteracao,
            "tipo_usuario" => array(
                "id" => $dataAsObject->usuario->tipo_usuario->id,
                "descricao" => $dataAsObject->usuario->tipo_usuario->descricao
            ),
            "instituicao" => array(
                "id" => $dataAsObject->usuario->instituicao->id,
                "nome" => $dataAsObject->usuario->instituicao->nome,
                "logo" => $logoInst,
                "uf" => $dataAsObject->usuario->instituicao->uf,
                "dt_criacao" => $dataAsObject->usuario->instituicao->dt_criacao,
                "dt_alteracao" => $dataAsObject->usuario->instituicao->dt_alteracao
            )
        )
    );

}
