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

    public function showLoginPage() {
        echo $this->twig->render('auth/login.html.twig');
    }

    public function login($username, $password) {
        if (Validator::isNotEmpty($username) && Validator::isNotEmpty($password)) {
            if ($this->userModel->login($username, $password)) {
                // Redirect to dashboard or home page
                header('Location: /');
                exit;
            } else {
                // Show login error
                echo $this->twig->render('auth/login.html.twig', ['error' => 'Invalid credentials.']);
            }
        } else {
            // Show input error
            echo $this->twig->render('auth/login.html.twig', ['error' => 'Please enter username and password.']);
        }
    }

    // public function showRegistrationPage() {
    //     echo $this->twig->render('auth/register.html.twig');
    // }

    public function showRegistrationPage($error = null, $userData = null) {
        echo $this->twig->render('auth/register.html.twig', ['error' => $error, 'userData' => $userData]);
    }
    

    public function register($userData) {
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
    
        if (!$this->validator::validateEmail($email)) {
            $validationErrors[] = 'Invalid email address.';
        }
    
        if (!$this->validator::validatePasswordStrength($password)) {
            $validationErrors[] = 'Password must be at least 6 characters long and contain numbers and letters.';
        }
    
        if (!$this->validator::validateUsername($username)) {
            $validationErrors[] = 'Username must be alphanumeric and 5-15 characters long.';
        }
    
        if ($password !== $confirmPassword) {
            $validationErrors[] = 'Passwords do not match.';
        }
    
        if (empty($validationErrors)) {
            // Handle profile picture upload
            $targetDir = __DIR__ . '/../../public/uploads/'; // Specify your upload directory
            $targetFile = $targetDir . basename($profilePicture);
    
            // Move the uploaded file to the specified directory
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                // File upload successful, proceed with registration
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $this->userModel->register($name, $username, $email, $hashedPassword, $gender, $address, $profilePicture);
                // Redirect after successful registration
                header('Location: /auth/login');
                exit;
            } else {
                $this->showRegistrationPage(implode('<br>', $validationErrors), $userData);
            }
        }
    
        // Validation errors occurred or file upload failed, show registration page with errors
        $this->showRegistrationPage(implode('<br>', $validationErrors));
    }
    
}
