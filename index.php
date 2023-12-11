<?php
require_once "app/Config/database.php";
require_once "app/Controllers/UserController.php";
require_once "app/Controllers/HomeController.php";
require_once "app/Controllers/AuthController.php";
require_once "app/Controllers/EventController.php";
require_once "app/Utils/Authentication.php";
require_once "app/Controllers/CategoryController.php";

$authentication = new Authentication();

$twig = require_once 'bootstrap.php';

// Base URL for routing
$base_url = '/event_management_site/';

// Debugging: Print the original URI
// echo "Original URI: " . $_SERVER['REQUEST_URI'] . "<br>";

$uri = str_replace($base_url, '', $_SERVER['REQUEST_URI']);
$authController = new AuthController($twig, $pdo);
$categoryController = new CategoryController($pdo);
$eventModel = new Event($pdo);

// Debugging: Print the modified URI
// echo "Modified URI: " . $uri . "<br>";

// Include the header
include 'app/Views/layouts/header.php';

switch ($uri) {
    case '':
    case '/':
        $controller = new HomeController($twig, $categoryController, $eventModel);
        $controller->index();
        break;
    case 'contact':
        include 'app/Views/contact.php';
        break;
    case 'about':
        include 'app/Views/about.php';
        break;
    case 'users/profile':
        $userId = $_SESSION['user_id'];
        $controller = new UserController($twig,$pdo);
        $controller->getUserProfile($userId);
        break;
    case 'users/edit-profile':
        $userId = $_SESSION['user_id'];
        $controller = new UserController($twig,$pdo);
        $controller->updateProfile();
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
        $controller -> listEvents($twig);
        break;
    case 'events/create':
        $controller = new EventController($twig, $pdo);
        $controller->createEvent(); // Create a new event
        break;
    case 'events/details/{event_id}':
        // Handle event details page, pass event ID and fetch event details
        $eventId = $_GET['event_id'] ?? 1; // Get the event ID from the query parameter
        $controller = new EventController($twig, $pdo);
        $controller->eventDetails($eventId);
        break;
    case 'users/events':
        // Handle user's events page, including ongoing, past, and attended events
        $userId = $_GET['id'] ?? 1; // Get the user ID from the query parameter
        $controller = new UserController($twig,$pdo);
        $controller->userEvents($userId);
        break;
    case 'auth/logout':
        $controller = new Authentication();
        $controller->logout();
        break;
    default:
        // Page not found or default case
        echo "<p>Page not found</p>";
}

// Include the footer
include 'app/Views/layouts/footer.php';

// var_dump($twig);
