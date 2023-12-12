<?php
class AdminController {
    private $twig;
    private $eventModel;
    private $userModel;
    private $validator;
    private $adminModel;
    private $categoryModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->eventModel = new Event($pdo);
        $this->userModel = new User($pdo);
        require_once __DIR__ . '/../Models/Admin.php';
        $this->adminModel = new Admin($pdo);
        require_once __DIR__ . '/../Utils/Validator.php';
        $this->validator = new Validator();
        $this->adminModel = new Admin($pdo);
        require_once __DIR__ . '/../Models/Category.php';
        $this->categoryModel = new Category($pdo);
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
            if ($this->adminModel->login($username, $password)) {
                // Set the user ID in the session
                // Redirect to home page after successful login
                header('Location: /event_management_site/admin');
                exit;
            } else {
                $validationErrors[] = 'Invalid credentials. Please try again.';
            }
        }
    
        // If validation errors occurred or login failed, show login form with errors
        $this->showLoginPage(implode('<br>', $validationErrors));
    }

    public function dashboard() {
        $totalEvents = $this->eventModel->getTotalEvents();
        $totalUsers = $this->userModel->getTotalUsers();
        $approvedEvents = $this->eventModel->getApprovedEventsCount();
        $pendingEvents = $this->eventModel->getPendingEventsCount();

        // Render the dashboard.html.twig with the dashboard data
        echo $this->twig->render('admin/dashboard.html.twig', [
            'totalEvents' => $totalEvents,
            'totalUsers' => $totalUsers,
            'approvedEvents' => $approvedEvents,
            'pendingEvents' => $pendingEvents
        ]);
    }

    public function listEvents() {
        // Fetch events from the Event model
        $events = $this->eventModel->getAllEvents();

        // Append the image URL to each event
        foreach ($events as $key => $event) {
            $events[$key]['image_url'] = 'public/assets/images/event_images/' . $event['image_path'];
        }

        // Fetch all categories from the Category model
        $categories = $this->categoryModel->getAllCategories();

        // Render the list.html.twig with fetched events
        echo $this->twig->render('admin/list-events.html.twig', ['events' => $events, 'categories' => $categories]);
    }

    public function eventDetails($eventId) {
        $event = $this->eventModel->getEventById($eventId);
        $event['image_url'] = 'http://localhost/event_management_site/public/assets/images/event_images/' . $event['image_path'];
        echo $this->twig->render('admin/event-details.html.twig', ['event' => $event]);
    }

    public function updateEvent($eventId, $eventData) {
        // Process and update an event
    }

    public function deleteEvent($eventId) {
        // Delete an event
    }

    public function listUsers() {
        // Fetch and render the list of users
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('admin/list-users.html.twig', ['users' => $users]);

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

    // Method to approve an unapproved event
    public function approveEvent($eventId) {
        $this->eventModel->approveEventById($eventId);
        // Redirect back to the event list or show confirmation
        header('Location: /admin/events');
        exit();
    }

    // Method to show the edit event page
    public function editEventPage($eventId) {
        $event = $this->eventModel->getEventById($eventId);
        // pass the categories to the edit page
        $categories = $this->categoryModel->getAllCategories();
        echo $this->twig->render('admin/edit-event.html.twig', ['event' => $event, 'categories' => $categories]);
    }

    // Method to edit an event
    public function editEvent($eventId, $eventData) {
        $this->eventModel->updateEventById($eventId, $eventData);
        // Redirect back to the event list or show confirmation
        header('Location: /admin/events');
        exit();
    }

    // Method to show the edit user page
    public function editUserPage($userId) {
        $user = $this->userModel->getUserById($userId);
        $user['profile_picture'] = 'public/assets/images/profile_pictures/' . $user['profile_picture']; // Add the profile to the user data
        echo $this->twig->render('admin/edit-user.html.twig', ['user' => $user]);
    }

    // Method to edit a user
    public function editUser($userId, $userData) {
        // Exclude email and profile picture from the data to be updated
        unset($userData['email'], $userData['profile_picture']);
        $this->userModel->updateUserById($userId, $userData);
        // Redirect back to the user list or show confirmation
        header('Location: /event_management_site/admin/users');
        exit();
    }

    // Method to create an event
    public function createEvent($eventData) {
        // Admin-created events are automatically approved
        $eventData['is_approved'] = 1;
        $this->eventModel->createEvent($eventData);
        // Redirect back to the event list or show confirmation
        header('Location: /admin/events');
        exit();
    }

    public function showCreateEventPage() {
        // Ensure the user is authorized and is an admin
        if ($this->userModel->isAdmin($_SESSION['user_id'])) {
            echo $this->twig->render('admin/create-event.html.twig');
        } else {
            // Handle unauthorized access, maybe redirect to login or give an error message
            header('Location: /event_management_site/admin/login');
            exit();
        }
    }


    // Additional admin methods as needed...
}
