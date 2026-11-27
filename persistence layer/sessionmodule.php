<?php

class Sessions {
    // db stuff
    private $conn;
    private $table = 'sessions';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function start_new_session($user_id, $tour_id) {
        $query = 'INSERT INTO ' . $this->table . ' (tour_id, user_id, created_at, active) VALUES (?, ?, NOW(), TRUE)';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tour_id, $user_id]);
        return $stmt;
    }
    public function end_active_session($user_id) {
        $query = 'UPDATE ' . $this->table . ' SET active = NULL WHERE user_id = ?;';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt;
    }
    public function set_archetype($user_id, $archetype) {
        $query = 'UPDATE ' . $this->table . ' SET archetype = ? WHERE user_id = ? AND active = TRUE';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$archetype, $user_id]);
        return $stmt;
    }
    public function get_active_session($user_id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE user_id = ? AND active = TRUE';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
?>