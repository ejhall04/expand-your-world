<?php

class Tours {
    // db stuff
    private $conn;
    private $table = 'tours';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($tour_id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . 'id = ' . $tour_id;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);;
    }
    public function list() {
        $query = 'SELECT id, name, description FROM ' . $this->table . ' ORDER BY name';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_object_by_position($tour_id, $position) {
        $query = 'SELECT * FROM tours_objects WHERE tour_id = ? AND position = ?;';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tour_id, $position]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function get_next_object($tour_id, $current_object_id) {
        $query = 'SELECT * FROM tours_objects WHERE tour_id = ? AND position = (SELECT position + 1 FROM tours_objects WHERE tour_id = ? AND object_id = ?)';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$tour_id, $tour_id, $current_object_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
?>