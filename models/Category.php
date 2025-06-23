<?php
class Category {
    private $conn;
    private $table = "categories";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $description) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (name, description) VALUES (?, ?)");
        return $stmt->execute([$name, $description]);
    }

    public function update($id, $name, $description) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET name=?, description=? WHERE id=?");
        return $stmt->execute([$name, $description, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?> 