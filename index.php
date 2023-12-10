<?php
require_once "app/Config/database.php";
require_once "app/Controllers/UserController.php";
require_once "app/Controllers/HomeController.php";
require_once "app/Controllers/AuthController.php";
require_once "app/Controllers/EventController.php";
require_once "app/Utils/Authentication.php";

$authentication = new Authentication();

$twig = require_once 'bootstrap.php';

// Base URL for routing
$base_url = '/event_management_site/';

// Debugging: Print the original URI
// echo "Original URI: " . $_SERVER['REQUEST_URI'] . "<br>";

$uri = str_replace($base_url, '', $_SERVER['REQUEST_URI']);
$authController = new AuthController($twig, $pdo);

// Debugging: Print the modified URI
// echo "Modified URI: " . $uri . "<br>";

// Include the header
include 'app/Views/layouts/header.php';

// Simple router example
switch ($uri) {
    case '':
    case '/':
        $controller = new HomeController();
        $controller->index($twig);
        break;
    case 'contact':
        include 'app/Views/contact.php';
        break;
    case 'about':
        include 'app/Views/about.php';
        break;
    case 'users/profile':
        $userId = $_GET['id'] ?? 1; // default to user ID 1
        $controller = new UserController($pdo);
        $controller->getUserProfile($userId);
        break;
    // More routes here
    case 'auth/login':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $authController->login($_POST['username'], $_POST['password']);
        } else {
            $authController->showLoginPage();
        }
        break;
    case 'auth/register':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $authController->register($_POST);
        } else {
            $authController->showRegistrationPage();
        }
        break;
    case 'events':
        $controller = new EventController($twig, $pdo);
        $controller -> listEvents();
        break;
    default:
        // Page not found or default case
        echo "<p>Page not found</p>";
}

// Include the footer
include 'app/Views/layouts/footer.php';

// var_dump($twig);
