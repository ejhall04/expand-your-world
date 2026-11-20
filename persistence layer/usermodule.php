<?php

class Users {
    // db stuff
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = 'SET @id = UUID();';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $query = 'INSERT INTO ' . $this->table . ' (id, created_at) VALUES (@id, NOW());';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $query = 'SELECT id FROM '. $this->table . ' WHERE id = @id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
?>