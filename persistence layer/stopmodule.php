<?php

class Stops {
    // db stuff
    private $conn;
    private $table = 'stops';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function start($user_id) {
        $tours = new Tours($this->conn);
        $sessions = new Sessions($this->conn);
        $stop = $this->get_stop_from_user($user_id);
        if($stop){
            $session = $sessions->get_active_session($user_id);
            $object_id = $tours->get_next_object($session['tour_id'], $stop['object_id'])['object_id'];
            $query = 'INSERT INTO ' . $this->table . ' (session_id, object_id, time_of_stop) VALUES (?, ?, NOW())';
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$session['session_id'], $object_id]);
        }else{
            $sessions = new Sessions($this->conn);
            $session = $sessions->get_active_session($user_id);
            $object = $tours->get_object_by_position($session['tour_id'], 1);
            $object_id = $object['object_id'];
            $session_id = $session['session_id'];
            $query = 'INSERT INTO ' . $this->table . ' (session_id, object_id, time_of_stop) VALUES (?, ?, NOW())';
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$session_id, $object_id]);
        }
        
    }
    public function feedback($user_id, $feedback) {
        $stop = $this->get_stop_from_user($user_id);
        $session_id = $stop['session_id'];
        $object_id = $stop['object_id'];
        $query = 
        'UPDATE ' . $this->table . ' 
        SET feedback = ? 
        WHERE session_id = ? AND 
            object_id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$feedback, $session_id, $object_id]);
    }
    
    public function get_stop_from_user($user_id) {
        // Get the session_id for the user
        $query = 'SELECT session_id FROM sessions WHERE user_id = ? AND active = TRUE';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        $session_id = $stmt->fetch(PDO::FETCH_ASSOC)['session_id'];

        $query = 'SELECT * FROM ' . $this->table . ' WHERE session_id = ? AND 
        time_of_stop = (SELECT MAX(time_of_stop) 
                    FROM ' . $this->table . ' 
                    WHERE session_id = ?)';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$session_id, $session_id]);
        $stop = $stmt->fetch(PDO::FETCH_ASSOC);
        return $stop;
    }
}
?>