<?php
class User {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function register($name, $username, $email, $password, $gender, $address, $profile_picture) {
        // Check if the email is already registered
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            return "Email is already registered.";
        }

        // Check if the username is already taken
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            return "Username is already taken.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO Users (name, username, email, password, gender, address, profile_picture, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $username, $email, $hashedPassword, $gender, $address, 0]);
        // Additional error handling and checks can be added

         // Check if the registration was successful
        if ($stmt->rowCount() > 0) {
            return "Registration successful."; // You can also return a success message or status code
        } else {
            return "Registration failed. Please try again later."; // Registration failed for some reason
        }
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
