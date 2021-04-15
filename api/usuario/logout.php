<?php
// Initialize the session
session_start();

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if (strtoupper($_SERVER["REQUEST_METHOD"]) !== "GET" && strtoupper($_SERVER["REQUEST_METHOD"]) !== "POST") {
    http_response_code(405);
    exit();
}

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

$response = array(
    "sucess" => true,
    "message" => "You have successfully logged out"
);

// set response code - 200 OK
http_response_code(200);

// make it json format
echo json_encode($response);
