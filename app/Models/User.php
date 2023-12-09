<?php
class User {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function register($name, $username, $email, $password, $gender, $address) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO Users (name, username, email, password, gender, address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $username, $email, $hashedPassword, $gender, $address]);
        // Additional error handling and checks can be added
    }   

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session or token as per your session handling logic
            return true;
        }
        return false;
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
