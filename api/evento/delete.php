<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if(strtoupper($_SERVER["REQUEST_METHOD"]) !== "DELETE") {
    http_response_code(405);
    exit();
}
  
// include database and object files
include_once '../config/database.php';
include_once '../objects/evento.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection();
  
// prepare evento object
$evento = new Evento($db);
  
// get evento id
$data = json_decode(file_get_contents("php://input"));

if(empty($data->id)) {
    // set response code - 400 bad request
    http_response_code(400);
    
    // tell the user
    echo json_encode(array("message" => "Unable to delete evento. No id informed."));
} else {
    // set evento id to be deleted
    $evento->id = $data->id;
    
    // delete the evento
    if(!$evento->delete()) {
        // set response code - 503 service unavailable
        http_response_code(503);
    
        // tell the user
        echo json_encode(array("message" => "Unable to delete evento."));
    } else {
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(array("message" => "Evento was deleted."));
    }
}
?>