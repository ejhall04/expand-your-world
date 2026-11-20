<?php
include_once '..\..\API-setup\initialize.php';
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
$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : "NO USER ID";

try{
    switch ($method) {
        case 'POST':
            if($action == 'scan' && $user_id != 'NO USER ID') {
                $sessions = new Sessions($db);
                $tours = new Tours($db);
                $stops = new Stops($db);
                $stop = $stops->get_stop_from_user($user_id);
                $object_id = isset($_GET['object_id']) ? $_GET['object_id'] : die();
                $active_session = $sessions->get_active_session($user_id);
                $tour_id = $active_session['tour_id'] ? $active_session['tour_id'] : die() ;
                if($stop){
                    $current_object_id = $stop['object_id'];
                    $next_object = $tours->get_next_object($tour_id, $current_object_id);
                }else{
                    $next_object = $tours->get_object_by_position($tour_id, 1);
                }
                if( $next_object['object_id'] == $object_id) {
                    $objects = new Objects($db);
                    $stops->start($user_id);
                    $object = $objects->read($object_id);
                    echo json_encode($object);

                } else{
                    echo json_encode(array('Message' => 'That is not the next item in your tour ' . $next_object['object_id'] . ' is'));
                }
            }else if($action == 'createUser') {
                if($user_id != 'NO USER ID'){
                    $users = new Users($db);
                    $user_id = $users->create()['id'];
                    setcookie(
                        'user_id',
                        $user_id,
                        [
                            'HttpOnly' => true,
                            'secure' => true,
                            'samesite' => 'Lax',
                            'path' => '/',
                        ]
                    );
                }
            }else if($action == 'startSession' && $user_id != 'NO USER ID') {
                $tour_id = isset($_GET['tour_id']) ? $_GET['tour_id'] : die();
                if($tour_id){
                    $sessions = new Sessions($db);
                    $session = $sessions->get_active_session($user_id);
                    if(!$session){
                        $sessions->start_new_session($user_id, $tour_id);
                        echo json_encode(array('message' => 'Created successfully'));
                    } else{
                        throw new error("You are already in a tour");
                    }
                } 
            }else if ($action == 'endSession' && $user_id != 'NO USER ID') {
                $sessions = new Sessions($db);
                $session = $sessions->get_active_session($user_id);
                if($sessions->get_active_session($user_id)){
                    $sessions->end_active_session($user_id);
                    echo json_encode(array('message' => 'Session ended successfully'));
                }else{
                    echo json_encode(array('message' => 'There are no active sessions'));
                }
            }
            break;
        
        case 'GET':
            # code...
            break;
        default:
            break;
    }
    
}catch(Exception $e){
    echo json_encode(array('Error' => 'Error: ' . $e->getMessage()));
    die();
}