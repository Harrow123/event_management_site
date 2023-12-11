<?php
require_once __DIR__ . "/../Models/User.php";

class UserController {
    private $userModel;
    private $twig;

    public function __construct($twig,$db) {
        $this->twig = $twig;
        $this->userModel = new User($db);
    }

    public function showEditProfilePage($userData = [], $validationErrors = []) {
        echo $this->twig->render('users/edit-profile.html.twig', [
            'user' => $userData,
            'validationErrors' => $validationErrors,
        ]);
    }    

    public function getUserProfile($userId) {
        $user = $this->userModel->getUserById($userId);
        // Load view with user data
        echo $this->twig->render('users/profile.html.twig', ['user' => $user]);
    }
    

    public function userEvents($userId) {
        // Fetch user's events based on $userId (e.g., ongoing, past, attended)
        $userEvents = $this->userModel->getUserEvents($userId);
    
        // Render the user events view with $userEvents data
        echo $this->twig->render('users/events.html.twig', ['userEvents' => $userEvents]);
    }

    private function validateUserProfileData($userData) {
        $errors = [];
    
        // Validate name - ensure it's not empty
        if (empty($userData['name'])) {
            $errors[] = 'Name is required.';
        } else {
            // Additional checks can be added here, like length, characters, etc.
        }
    
        // Validate email
        if (empty($userData['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
    
        // Validate gender - assuming your application requires this field
        if (empty($userData['gender'])) {
            $errors[] = 'Gender is required.';
        } elseif (!in_array($userData['gender'], ['male', 'female', 'other'])) {
            $errors[] = 'Invalid gender specified.';
        }
    
        // Validate address - this can be as simple or complex as needed
        if (empty($userData['address'])) {
            $errors[] = 'Address is required.';
        } else {
            // Additional checks for address can be added here, such as length.
        }
    
        // Additional fields can be validated here...
    
        return $errors;
    }
    

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collecting user data from the POST request
            $user = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'gender' => $_POST['gender'],
                'address' => $_POST['address'],
                // Add more fields as needed
            ];
    
            // Validate the user input
            $validationErrors = $this->validateUserProfileData($user);
    
            if (empty($validationErrors)) {
                // Update the user's profile data in the User model
                if ($this->userModel->updateUserProfile($_SESSION['user_id'], $user)) {
                    // Profile updated successfully, redirect to the profile page
                    header('Location: profile');
                    exit;
                } else {
                    // Handle failed update (e.g., database issues)
                    $validationErrors[] = 'Failed to update profile. Please try again.';
                }
            }
    
            // If validation errors occurred or the update failed, show the edit profile page with errors
            $this->showEditProfilePage($user, $validationErrors);
        } else {
            // For a GET request, show the edit profile page with current user data
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            $this->showEditProfilePage($user);
        }
    }
    
}
