<?php

class Tours {
    // db stuff
    private $conn;
    private $table = 'tours';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($tour_id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . 'tour_id = ' . $tour_id;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    
}
?>