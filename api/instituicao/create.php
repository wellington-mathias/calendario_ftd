<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");
if (strtoupper($_SERVER["REQUEST_METHOD"]) !== "POST") {
    sendMessage(405, null);
}

include_once '../objects/instituicao.php';
include_once '../shared/utilities.php';

$obj = new Instituicao();
$obj->nome = empty($_POST["nome"]) ? null : trim($_POST["nome"]);
$obj->uf = empty($_POST["uf"]) ? null : trim($_POST["uf"]);
$file = validateUpload("logo");
if (is_null($file)) {
    $obj->logo = null;
    $obj->logo_content_type = null;
} else {
    $obj->logo = file_get_contents($file['tmp_name']);
    $obj->logo_content_type = $file['type'];
}

if (!$obj->create()) {
    sendMessage(503, array("message" => "Unable to create Instituicao."));
} else {
    sendMessage(201, array("id" => $obj->id, "message" => "Instituicao was created."));
}
function sendMessage($http_code, $response_data)
{
    http_response_code($http_code);
    if ($response_data != null) {
        echo json_encode($response_data);
    }
    exit();
}
function validateUpload($filename)
{
    $utilities = new Utilities();
    if ($utilities->emptyUpload($filename)) {
        return null;
    } else {
        $hasError = false;
        $errorMessage = null;
        $file = $_FILES[$filename];
        if ($file['error'] == 4) {
            return null;
        } else {
            if ($file['error'] != 0) {
                $hasError = true;
                $errorMessage = $utilities->validateErrorMessage($file);
            } elseif (!$utilities->validateFileType($file['type'], array("image/jpeg", "image/png", "image/gif"))) {
                $hasError = true;
                $errorMessage = "O formato de arquivo '" . $file['type']  . "' e invalido";
            } elseif (!$utilities->validateFileSize($file['size'], 512000)) {
                $hasError = true;
                $errorMessage = "O arquivo enviado excede o limite maximo permitido de 500KB";
            } elseif (!file_exists($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"])) {
                $hasError = true;
                $errorMessage = "Falha ao obter dados do arquivo";
            }
            if ($hasError) {
                sendMessage(400, array("message" => $errorMessage));
            }
            return $file;
        }
    }
}
