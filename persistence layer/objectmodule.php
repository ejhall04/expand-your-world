<?php

class Objects {
    // db stuff
    private $conn;
    private $table = 'objects';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($object_id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE object_id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$object_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
}
?>