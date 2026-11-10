<?php

class Objects {
    // db stuff
    private $conn;
    private $table = 'objects';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function list() {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function read($object_id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE object_id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $object_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        $stmt->execute([$object_id]);
        return $row;
    }
}
?>