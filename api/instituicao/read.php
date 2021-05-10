<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

if (strtoupper($_SERVER["REQUEST_METHOD"]) !== "GET") {
    http_response_code(405);
    exit();
}

include_once '../objects/instituicao.php';

if (isset($_GET['id'])) {
    readOne();
} else {
    readAll();
}

function readAll()
{
    $instituicao = new Instituicao();

    $instituicoes = $instituicao->read();
    if (count($instituicoes) == 0) {
        http_response_code(404);

        echo json_encode(array("message" => "Nenhuma Instituicao encontrada."));
    } else {
        $objects_arr = array();
        $objects_arr["instituicoes"] = array();

        foreach ($instituicoes as $instituicao) {

            $instituicao_item = array(
                "id" => $instituicao->id,
                "nome" => $instituicao->nome,
                "logo" => $instituicao->logo_content_type . $instituicao->logo,
                "uf" => $instituicao->uf,
                "dt_criacao" => $instituicao->dt_criacao,
                "dt_alteracao" => $instituicao->dt_alteracao
            );

            array_push($objects_arr["instituicoes"], $instituicao_item);
        }

        http_response_code(200);
        echo json_encode($objects_arr);
    }
}

function readOne()
{
    if (empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to read Instituicao. No id informed."));
    } else {
        $instituicao = new Instituicao();

        $instituicao->id = $_GET['id'];
        $instituicao = $instituicao->readOne();

        if ($instituicao == null) {
            http_response_code(404);
            echo json_encode(array("message" => "Instituicao does not exist."));
        } else {
            $instituicao_item = array(
                "id" => $instituicao->id,
                "nome" => $instituicao->nome,
                "logo" => $instituicao->logo_content_type . $instituicao->logo,
                "uf" => $instituicao->uf,
                "dt_criacao" => $instituicao->dt_criacao,
                "dt_alteracao" => $instituicao->dt_alteracao
            );

            http_response_code(200);
            echo json_encode($instituicao_item);
        }
    }
}
