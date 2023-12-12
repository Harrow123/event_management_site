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
        require_once __DIR__ . '/../Utils/ImageUploader.php';
        $this->imageUploader = new ImageUploader();
    }

    public function showLoginPage($error = null) {
        echo $this->twig->render('admin/admin_login.html.twig', ['error' => $error]);
    }

    private function uploadProfilePicture($file) {
        $uploadDirectory = 'public/uploads/';
        $defaultPicture = 'default.png';
    
        if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
            $tmpName = $file['tmp_name'];
            $fileName = $file['name'];
    
            // Generate a unique filename using a combination of UID and the original filename
            $uniqueUid = uniqid();
            $uniqueFileName = $uniqueUid . '_' . $fileName;
    
            // Move the uploaded file to the upload directory
            move_uploaded_file($tmpName, $uploadDirectory . $uniqueFileName);
    
            return $uniqueFileName;
        }
    
        // If no file is uploaded or an error occurred, use the default picture
        return $defaultPicture;
    }

    public function createUser($userData) {
        // Perform server-side validation
        $errors = $this->validateUserData($userData);
        if (!empty($errors)) {
            // Re-render the form with error messages
            echo $this->twig->render('admin/create-user.html.twig', ['userData' => $userData, 'errors' => $errors]);
            return;
        }

        // Check if passwords match
        if ($userData['password'] !== $userData['confirm_password']) {
            $errors[] = 'Passwords do not match.';
            echo $this->twig->render('admin/create-user.html.twig', ['userData' => $userData, 'errors' => $errors]);
            return;
        }

        // Hash the password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        $userData['password'] = $hashedPassword;

        // Process file upload if necessary
        // ... (handle profile picture upload)

        // Call the model method to insert the user into the database
        $result = $this->userModel->createUser($userData);
        
        if ($result) {
            // Redirect to user list or display success message
            header('Location: /event_management_site/admin/users');
            exit();
        } else {
            // Handle the error case
            $errors[] = 'An error occurred while creating the user.';
            echo $this->twig->render('admin/create-user.html.twig', ['userData' => $userData, 'errors' => $errors]);
        }
    }

    private function validateUserData($userData) {
        $errors = [];
    
        // Check for required fields
        $requiredFields = ['name', 'username', 'email', 'gender', 'address'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                $errors[] = "The {$field} field is required.";
            }
        }
    
        // Validate email format
        if (!empty($userData['email']) && !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "The email address is not a valid format.";
        }
    
        // Check if username is already taken
        $existingUser = $this->userModel->findByUsername($userData['username']);
        if ($existingUser) {
            $errors[] = "The username is already taken.";
        }
    
        // Validate gender selection
        $validGenders = ['Male', 'Female', 'Other'];
        if (!in_array($userData['gender'], $validGenders)) {
            $errors[] = "Please select a valid gender.";
        }
    
        // Additional validations can be added here
    
        return $errors;
    }

    public function hashPassword(){
        $newPassword = "@dministrat0r";
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        echo $hashedPassword;
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

    public function listEvents($page = 1) {
        $limit = 10; // Number of events per page
        $offset = ($page - 1) * $limit;
        $totalEvents = $this->eventModel->getTotalEventsCount();
        $totalPages = ceil($totalEvents / $limit);

        // Fetch events from the Event model
        // $events = $this->eventModel->getAllEventsWithOrganizer();
        $events = $this->eventModel->getEventsForPage($limit, $offset);

        // Append the image URL to each event
        foreach ($events as $key => $event) {
            $events[$key]['image_url'] = 'public/assets/images/event_images/' . $event['image_path'];
        }

        // Fetch all categories from the Category model
        $categories = $this->categoryModel->getAllCategories();

        // Render the list.html.twig with fetched events
        echo $this->twig->render('admin/list-events.html.twig', [
            'events' => $events, 
            'categories' => $categories,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ]);
    }

    public function eventDetails($eventId) {
        $event = $this->eventModel->getEventById($eventId);
        $event['image_url'] = 'http://localhost/event_management_site/public/assets/images/event_images/' . $event['image_path'];
        echo $this->twig->render('admin/event-details.html.twig', ['event' => $event]);
    }

    public function updateEvent($eventId, $eventData) {
        // Process and update an event
    }

    public function listUsers() {
        // Fetch and render the list of users
        $users = $this->userModel->getAllUsers();
        echo $this->twig->render('admin/list-users.html.twig', ['users' => $users]);

    }

    public function createUserPage() {
        // Render the create user page
        echo $this->twig->render('admin/create-user.html.twig');
    }

    public function updateUser($userId, $userData) {
        // Process and update a user
    }

    // Method to approve an unapproved event
    public function approveEvent($eventId, $currentPage) {
        $this->eventModel->approveEventById($eventId);
        header('Location: /event_management_site/admin/events?page=' . $currentPage);
        exit();
    }

    // Method to disapprove an approved event
    public function disapproveEvent($eventId, $currentPage) {
        $this->eventModel->setEventApprovalStatus($eventId, 0);
        header('Location: /event_management_site/admin/events?page=' . $currentPage);
        exit();
    }

    public function deleteEvent($eventId, $currentPage) {
        $this->eventModel->deleteEventById($eventId);
        header('Location: /event_management_site/admin/events?page=' . $currentPage);
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
        header('Location: admin/events');
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
        $errors = [];
        // echo $eventData;
        // exit();

        // Validate required fields
        if (empty($eventData['title'])) {
            $errors[] = 'Title is required.';
        }
        if (empty($eventData['description'])) {
            $errors[] = 'Description is required.';
        }

        // // Check for image upload errors
        //  // Handle image upload if a file is provided
        //  if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        //     $imagePath = $this->imageUploader->uploadImage($_FILES['event_image'], 'event_images');
        //     if ($imagePath) {
        //         $eventData['image_path'] = $imagePath;
        //     } else {
        //         $errors[] = 'Image upload failed.';
        //     }
        // } else {
        //     $eventData['image_path'] = 'default.png'; // Default image or handle as per your application's logic
        // }

        // Handle image upload if a file is provided
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->imageUploader->uploadImage($_FILES['event_image'], 'event_images');
            if ($imagePath) {
                $eventData['image_path'] = $imagePath;
            } else {
                // If the image upload fails, use the default image
                $eventData['image_path'] = 'default.jpg';
                $errors[] = 'Image upload failed, default image will be used.';
            }
        } else {
            // If no file is selected, use the default image
            $eventData['image_path'] = 'default.jpg';
        }

        // echo $eventData['image_path'];
        // exit();

        // $eventData['image_path'] = $imagePath;
        $eventData['is_featured'] = $eventData['is_featured'] ?? 0;
        $eventData['is_approved'] = 1;

        if (empty($errors)) {
            $categoryId = $eventData['category_id'];
            $result = $this->eventModel->createEvent($eventData, $categoryId);

            if ($result['success']) {
                header('Location: /event_management_site/admin/events');
                exit();
            }else {
                // Handle the error during event creation
                $errors[] = "Event creation failed.";
            }
        }

        // // Admin-created events are automatically approved
        // $eventData['is_approved'] = 1;
        // $categoryId = $eventData['category_id'];
        // $this->eventModel->createEvent($eventData, $categoryId);
        // // Redirect back to the event list or show confirmation
        // header('Location: /event_management_site/admin/events');
        // exit();

        // If there are errors, pass them to the view
        $categories = $this->categoryModel->getAllCategories();
        echo $this->twig->render('admin/create-event.html.twig', ['errors' => $errors, 'categories' => $categories]);
    }

    public function showCreateEventPage() {
        // Ensure the user is authorized and is an admin
        if ($this->userModel->isAdmin($_SESSION['admin_id'])) {
            // Fetch all categories from the Category model
            $categories = $this->categoryModel->getAllCategories();
            echo $this->twig->render('admin/create-event.html.twig', ['categories' => $categories,]);
        } else {
            // Handle unauthorized access, maybe redirect to login or give an error message
            header('Location: /event_management_site/admin/login');
            exit();
        }
    }

    // Additional admin methods as needed...
}
