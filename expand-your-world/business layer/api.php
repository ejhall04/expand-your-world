<?php
require_once '..\..\API-setup\initialize.php';
require_once 'ApiController.php';
//headers
$allowedOrigins = [
    'http://127.0.0.1:8080',  
    //'https://www.cincymuseum.org/', this needs to be the website's url
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Vary: Origin');
    header('Access-Control-Allow-Credentials: true');
}
header("Content-Type: application/json");

// if no action is specified, end the script
$action = isset($_GET['action']) ? $_GET['action'] : die();
$method = $_SERVER['REQUEST_METHOD'];
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : NULL;

try {
    // tells controller to handle request
    $controller = new ApiController($db);
    $response = $controller->handle(
        $_SERVER['REQUEST_METHOD'] ?? '',
        $_GET['action'] ?? '',
        $_COOKIE['user_id'] ?? null,
        $_GET
    );
    if (isset($response['exception'])) {
        throw new Exception($response['exception']);
    }
    // sends cookie if there is one to send
    if (isset($response['cookie'])) {
        setcookie('user_id', $response['cookie'], [
            'HttpOnly' => true,
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax',
            'path' => '/',
        ]);
    }

    // sends response
    if (isset($response['body'])) {
        echo json_encode($response['body']);
    }
}catch(Exception $e){
    echo json_encode(array('exception' => 'Exception: ' . $e->getMessage()));
    die();
}