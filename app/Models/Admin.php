<?php
class Admin {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE username = ? and is_admin");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session or token as per your session handling logic
            $_SESSION['admin_id'] = $user['user_id'];
            return true;
        }
        return false;
    }
}