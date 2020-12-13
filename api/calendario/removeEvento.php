<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if(strtoupper($_SERVER["REQUEST_METHOD"]) !== "DELETE") {
    send_message(405, null);
}

// includes
include_once '../objects/calendario.php';
include_once '../objects/evento.php';

// get posted data
$data = json_decode(file_get_contents("php://input"));
  
// make sure data is not empty
$data_incomplete = empty($data->id) || empty($data->evento_id);

if($data_incomplete) {
    send_message(400, array("message" => "Unable to remove Evento to Calendario. Data is incomplete."));
} else {
    $evento = new Evento();
    $evento->id = $data->evento_id;
    
    if(!$evento->readOne())  {
        send_message(404, array("message" => "Evento doesn't exist."));
    } else {
        $obj = new Evento();

        $evento = $obj->readOneByCalendario($data->id, $data->evento_id);

        if ($evento == null) {
            // Evento already removed
            send_message(200, array("message" => "Evento already removed."));
        } else {
            $calendario = new Calendario();
            $calendario->id = $data->id;

            if (!$calendario->removeEvento($data->evento_id)) {
                send_message(400, array("message" => "Can't remove Evento. An error has been occurred."));
            } else {
                send_message(200, array("message" => "Evento removed."));
            }
        }
    }
}

function send_message($http_code, $response_data) {
    // set response code - 400 bad request
    http_response_code($http_code);
    
    if ($response_data != null) {
        // tell the user
        echo json_encode($response_data);
    }

    exit();
}
?>