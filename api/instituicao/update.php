<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if(strtoupper($_SERVER["REQUEST_METHOD"]) !== "POST") {
    send_message(405, null);
}

// includes
include_once '../objects/instituicao.php';
include_once '../shared/utilities.php';

// get data
$data = json_decode($_POST["json_data"]);
$upload_name = "logo";

// make sure data is not empty
$data_incomplete = empty($data->nome) || empty($data->uf) || empty($_FILES[$upload_name]) || empty($data->id);

if ($data_incomplete) {
    send_message(400, array("message" => "Unable to create Instituicao. Data is incomplete."));
} else {
    $file = $_FILES[$upload_name];

    validateUpload($file);

    $file_content = file_get_contents($file['tmp_name']);

    // prepare object
    $obj = new Instituicao();

    // set ID property to be edited
    $obj->id = $data->id;
    
    // set property values
    $obj->nome = $data->nome;
    $obj->logo = file_get_contents($file['tmp_name']);
    $obj->logo_content_type = $file['type'];
    $obj->uf = $data->uf;
    
    // update the object
    if (!$obj->update()) {
        // set response code - 503 service unavailable
        send_message(503, array("message" => "Unable to update Instituicao."));
    } else {
        // set response code - 200 ok
        send_message(200, array ("id" => $obj->id, "message" => "Instituicao was updated."));
    }
}

function send_message($http_code, $response_data) {
    // set response code
    http_response_code($http_code);
    
    if ($response_data != null) {
        // tell the user
        echo json_encode($response_data);
    }

    exit();
}

function emptyUpload($filename) {
    if (empty($_FILES[$filename])) {
        return true;
    }

    return false;
}

function validateUpload($file) {
    $utilities = new Utilities();

    $hasError = false;
    $errorMessage = null;

    if ($file['error'] != 0) {
        $hasError = true;
        $errorMessage = $utilities->validateErrorMessage($file);
    }  else if (!$utilities->validateFileType($file['type'], array("image/jpeg", "image/png", "image/gif"))) {
        $hasError = true;
        $errorMessage = "O formato de arquivo '" . $file['type']  . "' e invalido";
    } else if (!$utilities->validateFileSize($file['size'], 2048000)) {
        $hasError = true;
        $errorMessage = "O arquivo enviado excede o limite maximo permitido de 2MB";
    } else if (!file_exists($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"])) {
        $hasError = true;
        $errorMessage = "Falha ao obter dados do arquivo";
    }

    if ($hasError) {
        send_message(400, array("message" => $errorMessage));
    }
}
?>