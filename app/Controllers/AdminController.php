<?php
class AdminController {
    private $twig;
    private $eventModel;
    private $userModel;
    private $validator;
    private $adminModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->eventModel = new Event($pdo);
        $this->userModel = new User($pdo);
        require_once __DIR__ . '/../Models/Admin.php';
        $this->adminModel = new Admin($pdo);
        require_once __DIR__ . '/../Utils/Validator.php';
        $this->validator = new Validator();
    }

    public function showLoginPage($error = null) {
        echo $this->twig->render('admin/admin_login.html.twig', ['error' => $error]);
    }

    public function login($username, $password) {
        $username = trim($username);
        $password = trim($password);

        // Perform validation checks
        $validationErrors = [];

        if (empty($username) || empty($password)) {
            $validationErrors[] = 'Please enter both username and password.';
        }
    
        if (!$this->validator::validateUsername($username)) {
            $validationErrors[] = 'Invalid username format.';
        }

        if (!$this->validator::validatePasswordStrength($password)) {
            $validationErrors[] = 'Invalid password format.';
        }

        if (empty($validationErrors)) {
            if ($this->userModel->login($username, $password)) {
                // Set the user ID in the session
                

                // Redirect to home page after successful login
                header('Location: /event_management_site');
                exit;
            } else {
                $validationErrors[] = 'Invalid credentials. Please try again.';
            }
        }
    
        // If validation errors occurred or login failed, show login form with errors
        $this->showLoginPage(implode('<br>', $validationErrors));
    }

    public function dashboard(){
        echo $this->twig->render('admin/dashboard.html.twig');
    }

    public function listEvents() {
        // Fetch and render the list of events
    }

    public function createEvent($eventData) {
        // Process and create a new event
    }

    public function updateEvent($eventId, $eventData) {
        // Process and update an event
    }

    public function deleteEvent($eventId) {
        // Delete an event
    }

    public function listUsers() {
        // Fetch and render the list of users
    }

    public function createUser($userData) {
        // Process and create a new user
    }

    public function updateUser($userId, $userData) {
        // Process and update a user
    }

    public function deleteUser($userId) {
        // Delete a user
    }

    public function approveEvent($eventId) {
        $this->eventModel->approveEvent($eventId);
        // Redirect or show confirmation
    }

    // Additional admin methods as needed...
}
