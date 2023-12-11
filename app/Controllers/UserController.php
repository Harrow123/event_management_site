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

        // Append the image URL to each event
        $user['profile_picture'] = '../public/assets/images/profile_images/' . $user['profile_picture'];

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

        $userId = $_SESSION['user_id'];
    
        // Validate name - ensure it's not empty
        if (empty($userData['name'])) {
            $errors[] = 'Name is required.';
        } else {
            // Additional checks can be added here, like length, characters, etc.
        }

        // Check if email is unique
        if (!empty($userData['email']) && $this->userModel->emailExists($userData['email'], $userId)) {
            $errors[] = 'Email is already in use by another account.';
        }

        // Check if username is unique
        if (!empty($userData['username']) && $this->userModel->usernameExists($userData['username'], $userId)) {
            $errors[] = 'Username is already in use by another account.';
        }

    
        // Validate email
        if (empty($userData['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
        
        // Password validation (if needed)
        if (!empty($userData['password']) && !empty($userData['confirm_password'])) {
            if ($userData['password'] !== $userData['confirm_password']) {
                $errors[] = 'Passwords do not match.';
            }
            // Additional password strength checks can be added here
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
        // Debugging: Output the errors
        error_log(print_r($errors, true));
        return $errors;
    }

    private function moveImageToPermanentStorage($image, $userId) {
        // $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/profile_images/';
        $uploadDir = realpath(__DIR__ . '/../../public/assets/images/profile_images') . DIRECTORY_SEPARATOR;
    
        // Create the directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    
        // Append user ID to filename to make it unique
        $filename = $userId . '-' . uniqid() . '-' . basename($image['name']);
        $destination = $uploadDir . $filename;

        // echo $destination;
        // exit;
    
        if (move_uploaded_file($image['tmp_name'], $destination)) {
            return $filename;
        } else {
            throw new Exception('File upload failed.');
        }
    }
    
    private function isValidImage($image) {
        $allowedTypes = ['image/jpeg', 'image/png',];
        $maxSize = 5000000; // 5 Megabytes, for example
    
        // Check if the file type is allowed
        if (!in_array($image['type'], $allowedTypes)) {
            return false;
        }
    
        // Check if the file size is within the limit
        if ($image['size'] > $maxSize) {
            return false;
        }
    
        // Optionally, you can check for image dimensions
        // list($width, $height) = getimagesize($image['tmp_name']);
        // if ($width > $maxWidth || $height > $maxHeight) {
        //     return false;
        // }
    
        return true;
    }
    

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collecting user data from the POST request
            $user = [
                'name' => $_POST['name'],
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'gender' => $_POST['gender'],
                'address' => $_POST['address'],
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
                'profile_picture' => '', 
                // Add more fields as needed
            ];
            error_log('Before validation');
            // Validate the user input
            $validationErrors = $this->validateUserProfileData($user);
            error_log('After validation: ' . print_r($validationErrors, true));
    
            if (empty($validationErrors)) {
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                    $image = $_FILES['profile_picture'];
            
                    if ($this->isValidImage($image)) {
                        $userId = $_SESSION['user_id'];
                        $filename = $this->moveImageToPermanentStorage($image, $userId);
                        $user['profile_picture'] = $filename;
                    } else {
                        $validationErrors[] = 'Invalid image file.';
                    }
                }

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
