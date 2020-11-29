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
include_once '../objects/evento.php';
  
// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare evento object
$evento = new Evento($db);

// query eventos
$stmt = $evento->read();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if ($num == 0) {
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no eventos found
    echo json_encode(array("message" => "Nenhum evento encontrado."));
} else {
    // eventos array
    $eventos_arr = array();
    $eventos_arr["eventos"] = array();
  
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $tipo_evento_item = array(
            "id" => $tipo_evento_id,
            "descricao" => $tipo_evento_descricao
        );

        $evento_item=array(
            "id" => $id,
            "tipo_evento" => $tipo_evento_item,
            "dt_inicio" => $dt_inicio,
            "dt_fim" => $dt_fim,
            "titulo" => (is_null ($titulo)) ? null: html_entity_decode($titulo),
            "descricao" => (is_null ($descricao)) ? null: html_entity_decode($descricao),
            "uf" => (is_null ($uf)) ? null: strtoupper($uf),
            "dia_letivo" => (bool) $dia_letivo,
            "dt_criacao" => $dt_criacao,
            "dt_alteracao" => $dt_alteracao
        );
  
        array_push($eventos_arr["eventos"], $evento_item);
    }
  
    // set response code - 200 OK
    http_response_code(200);
  
    // show eventos data in json format
    echo json_encode($eventos_arr);
}
?>