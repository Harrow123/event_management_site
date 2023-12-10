<?php
require_once __DIR__ . "/../Models/User.php";

class UserController {
    private $userModel;
    private $twig;

    public function __construct($twig,$db) {
        $this->twig = $twig;
        $this->userModel = new User($db);
    }

    public function getUserProfile($userId) {
        $user = $this->userModel->getUserById($userId);
        // Load view with user data
        include __DIR__ . '/../Views/users/profile.php';
    }

    public function userEvents($userId) {
        // Fetch user's events based on $userId (e.g., ongoing, past, attended)
        $userEvents = $this->userModel->getUserEvents($userId);
    
        // Render the user events view with $userEvents data
        echo $this->twig->render('users/events.html.twig', ['userEvents' => $userEvents]);
    }
    
}
