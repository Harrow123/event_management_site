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

    public function showRegistrationPage() {
        echo $this->twig->render('auth/register.html.twig');
    }

    public function register($userData) {
        if (Validator::validateEmail($userData['email']) &&
            Validator::validatePasswordStrength($userData['password']) &&
            Validator::validateUsername($userData['username'])) {

            // Additional validation checks...

            $this->userModel->register(
                $userData['name'],
                $userData['username'],
                $userData['email'],
                $userData['password'],
                $userData['gender'],
                $userData['address']
            );

            // Redirect after successful registration
            header('Location: /login');
            exit;
         } else {
            // Show registration error
            echo $this->twig->render('auth/register.html.twig', ['error' => 'Invalid input.']);
        }
    }
}
