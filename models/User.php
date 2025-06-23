<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Không lưu password vào session
            return $user;
        }
        return false;
    }

    public function create($username, $password, $full_name, $email, $role = 'staff') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (username, password, full_name, email, role) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$username, $hashed_password, $full_name, $email, $role]);
    }

    public function update($id, $full_name, $email, $role) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET full_name=?, email=?, role=? WHERE id=?");
        return $stmt->execute([$full_name, $email, $role, $id]);
    }

    public function changePassword($id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET password=? WHERE id=?");
        return $stmt->execute([$hashed_password, $id]);
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT id, username, full_name, email, role, created_at FROM {$this->table} ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, username, full_name, email, role, created_at FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        return $stmt->execute([$id]);
    }
}
?> 