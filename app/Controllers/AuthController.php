<?php
class AuthController {
    private $twig;
    private $userModel;
    private $validator;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->userModel = new User($pdo);

        require_once __DIR__ . '/../Utils/Validator.php';
        $this->validator = new Validator();
    }

    public function showLoginPage($error = null) {
        echo $this->twig->render('auth/login.html.twig', ['error' => $error]);
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
                header('Location: ' . $base_url);
                exit;
            } else {
                $validationErrors[] = 'Invalid credentials. Please try again.';
            }
        }
    
        // If validation errors occurred or login failed, show login form with errors
        $this->showLoginPage(implode('<br>', $validationErrors));
    }

    public function showRegistrationPage($error = null, $userData = null) {
        echo $this->twig->render('auth/register.html.twig', ['error' => $error, 'userData' => $userData]);
    }    

    public function register($userData) {
        // Check if the form has been submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Retrieve and sanitize user data
            $name = $userData['name'];
            $username = $userData['username'];
            $email = $userData['email'];
            $password = $userData['password'];
            $confirmPassword = $userData['confirm_password'];
            $gender = $userData['gender'];
            $address = $userData['address'];
            $profilePicture = $_FILES['profile_picture']['name'];

            // Perform validation checks
            $validationErrors = [];

            if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($gender) || empty($address)) {
                $validationErrors[] = 'Please fill in all required fields.';
            }

            if (!$this->validator::validateEmail($email)) {
                $validationErrors[] = 'Invalid email address.';
            }
        
            if (!$this->validator::validatePasswordStrength($password)) {
                $validationErrors[] = 'Password must be at least 6 characters long and contain numbers and letters.';
            }
        
            if (!$this->validator::validateUsername($username)) {
                $validationErrors[] = 'Username must be alphanumeric and 3-15 characters long.';
            }
        
            if ($password !== $confirmPassword) {
                $validationErrors[] = 'Passwords do not match.';
            }

            if (empty($validationErrors)) {
                // Handle profile picture upload
                $profilePicture = $this->uploadProfilePicture($profilePicture);
                // echo $profilePicture;
                // exit();
                
                // Generate a unique UID
                $uniqueUid = uniqid();

                // Append the UID to the profile picture filename to avoid confusion
                $profilePictureFilename = $uniqueUid . '_' . $profilePicture;

                $this->userModel->register($name, $username, $email, $password, $gender, $address, $profilePictureFilename);

                // Redirect after successful registration
                header('Location: login');
            }else {
            // Validation errors occurred or file upload failed, show registration page with errors
            $this->showRegistrationPage(implode('<br>', $validationErrors), $userData);
        }
        }else {
            // The form has not been submitted yet, display an empty registration form
            $this->showRegistrationPage();
        }
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
}
