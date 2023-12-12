<?php
class Admin {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE username = ? and is_admin=1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // echo $password;
        // exit();
        if ($user && password_verify($password, $user['password'])) {
            // Set session or token as per your session handling logic
            $_SESSION['admin_id'] = $user['user_id'];
            $_SESSION['is_admin'] = true;
            return true;
        }
        return false;
    }
}