<?php
// Initialize the session
session_start();

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if(strtoupper($_SERVER["REQUEST_METHOD"]) !== "POST") {
    http_response_code(405);
    exit();
}
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    $response = json_decode($_SESSION["userResponse"]);
    $response->message = "An User is already logged in!";

    // make it json format
    echo json_encode($response);
    
} else {
    // include database and object files
    include_once '../config/database.php';
    include_once '../objects/usuario.php';

    // GET DATA FORM REQUEST
    $data = json_decode(file_get_contents("php://input"));

    $data_incomplete = !isset($data->ambiente) || !isset($data->usuario) || !isset($data->senha) || empty(trim($data->ambiente)) || empty(trim($data->usuario)) || empty(trim($data->senha));

    if ($data_incomplete) {
        // set response code - 400 bad request
        http_response_code(422);

        // tell the user
        echo json_encode(array(
            "sucess" => false,
            "message" => 'Please Fill in all Required Fields!'
        ));
    } else {
        $ambiente = strtoupper(trim($data->ambiente));
        $login = trim($data->usuario);
        $password = trim($data->senha);
        /*    
        // CHECKING THE EMAIL FORMAT (IF INVALID FORMAT)
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // set response code - 400 bad request
            http_response_code(422);

            // tell the user
            echo json_encode(array(
                "sucess" => false,
                "message" => 'Invalid Email Address!'
            ));

        // IF PASSWORD IS LESS THAN 8 THE SHOW THE ERROR
        } else if(strlen($password) < 8) {
            // set response code - 400 bad request
            http_response_code(422);

            // tell the user
            echo json_encode(array(
                "sucess" => false,
                "message" => 'Your password must be at least 8 characters long!'
            ));
        */
    
        // THE USER IS ABLE TO PERFORM THE LOGIN ACTION
        
        $obj = new Usuario();

        $usuario = $obj->login($ambiente, $login);
        
        if ($usuario == null) {
            // set response code - 400 bad request
            http_response_code(422);

            // tell the user
            echo json_encode(array(
                "sucess" => false,
                "message" => 'Invalid User or Enviroment!'
            ));
        } else {
            if (!password_verify( $password , $usuario->senha)) {
                // set response code - 400 bad request
                http_response_code(422);

                // tell the user
                echo json_encode(array(
                    "sucess" => false,
                    "message" => 'Invalid Password!'
                ));
            } else {
                $response = array(
                    "sucess" => true,
                    "message" => "You have successfully logged in.",
                    "usuario" => array(
                        "id" => $usuario->id,
                        "nome" => $usuario->nome,
                        "email" => $usuario->email, 
                        "dt_criacao" => $usuario->dt_criacao,
                        "dt_alteracao" => $usuario->dt_alteracao,
                        "tipo_usuario" => array(
                            "id" => $usuario->tipo_usuario->id,
                        	"descricao" => $usuario->tipo_usuario->descricao
                        )
                    )
                );

                if ($usuario->instituicao != null) {
                    $response["usuario"]["instituicao"] = array(
                        "id" => $usuario->instituicao->id,
                        "nome" => $usuario->instituicao->nome,
                        "logo" => $usuario->instituicao->logo_content_type . $usuario->instituicao->logo,
                        "uf" => $usuario->instituicao->uf,
                        "dt_criacao" => $usuario->instituicao->dt_criacao,
                        "dt_alteracao" => $usuario->instituicao->dt_alteracao
                    );
            
                }

                // set response code - 200 OK
                http_response_code(200);

                $json_response = json_encode($response);

                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["userResponse"] = $json_response;

                // make it json format
                echo $json_response;
            }
        }
    }
}
?>