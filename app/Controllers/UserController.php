<?php
require_once "../Models/User.php";

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function getUserProfile($userId) {
        $user = $this->userModel->getUserById($userId);
        // Load view with user data
        include '../Views/users/profile.php';
    }
}
