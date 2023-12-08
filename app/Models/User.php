<?php
class User {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
