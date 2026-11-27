<?php

class ApiController{
    private $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }
    public function handle($method, $action, $user_Id, array $query){
        if ($method === 'POST') {
            switch ($action) {
                case 'createUser':
                    return $this->createUser($user_Id);
                case 'scan':
                    return $this->scan($user_Id, $query['object_id'] ?? null);
                case 'startSession':
                    return $this->startSession($user_Id, $query['tour_id'] ?? null);
                case 'endSession':
                    return $this->endSession($user_Id);
                case 'submitStopFeedback':
                    return $this->submitStopFeedback($user_Id, $query['feedback'] ?? null);
                default:
                    return ['exception' => 'Unknown action'];
            }
        }else if ($method === 'GET') {
            switch ($action) {
                case 'listTours':
                    return $this->listTours();
                case 'getSession':
                    return $this->getActiveSession($user_Id);
                default:
                    return ['exception' => 'Unknown action'];
            }
        }
    }

    private function scan($user_id, $object_id){
        
        if($user_id == NULL) {
            return ['exception' => 'No user id supplied'];
        }
        
        $sessions = new Sessions($this->db);
        $active_session = $sessions->get_active_session($user_id);
        if($active_session == NULL){
            return ['exception' => 'No active session'];
        }
        $stops = new Stops($this->db);
        $stop = $stops->get_stop_from_user($user_id);
        if($object_id == NULL){ return ['exception' => 'Nothing scanned'];}

        $tour_id = $active_session['tour_id'];
        if($stop){
            $tours = new Tours($this->db);
            $current_object_id = $stop['object_id'];
            $next_object = $tours->get_next_object($tour_id, $current_object_id);
        }else{
            $tours = new Tours($this->db);
            $next_object = $tours->get_object_by_position($tour_id, 1);
        }
        if($next_object == NULL){
            return ['exception' => 'No next object found'];
        }
        if( $next_object['object_id'] == $object_id) {
            $objects = new Objects($this->db);
            $stops->start($user_id);
            $object = $objects->read($object_id);
            return ['body' => $object];

        } else{
            return ['exception' => 'That is not the next item in your tour ' . $next_object['object_id'] . ' is'];
        }
        
    }
            
    private function createUser($user_id){
        if($user_id == NULL){
            $users = new Users($this->db);
            $user_id = $users->create()['id'];
            return ['cookie' => $user_id];
        }else{
            return ['exception' => 'User already exists'];
        }
        
    }
    private function startSession($user_id, $tour_id){
        if($user_id == NULL || $tour_id == NULL) {
            return ['exception' => 'user id or tour id not set'];
        }
        if($tour_id != NULL){
            $sessions = new Sessions($this->db);
            $session = $sessions->get_active_session($user_id);
            if(!$session){
                $sessions->start_new_session($user_id, $tour_id);
                return ['body' => 'Created successfully'];
            } else{
                return ['exception' => 'You are already in a tour'];
            }
        }else{
            return ['exception' => 'No tour ID'];
        }
        
    }
        private function endSession($user_id){
            if($user_id == NULL){
                return ['exception' => 'No user ID'];
            }
            $sessions = new Sessions($this->db);
            $session = $sessions->get_active_session($user_id);
            if($session){
                $sessions->end_active_session($user_id);
                return ['body' => 'Session ended successfully'];
            }else{
                return ['body' => 'There are no active sessions'];
            }
        }
        private function listTours(){
            $tours = new Tours($this->db);
            $list = $tours -> list();
            return ['body' -> $list];
        }
        private function getActiveSession($user_id){
            $sessions = new Sessions($this->db);
            return ['body' => $sessions->get_active_session($user_id)];
        }
        private function submitStopFeedback($user_id, $feedback){
            if($user_id == false){
                return ['exception' => 'No user id'];
            }
            $sessions = new Sessions($this->db);
            $session = $sessions->get_active_session($user_id);
            if($session == false){
                return ['exception' => 'No active session'];
            }
            $stops = new Stops($this->db);
            $stop = $stops -> get_stop_from_user($user_id);
            if($stop == false){
                return ['exception' => 'No active stop'];
            }
            $stops->feedback($user_id, $feedback);
            
           

        }
    }