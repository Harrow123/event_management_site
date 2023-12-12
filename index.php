<?php
require_once "app/Config/database.php";
require_once "app/Controllers/UserController.php";
require_once "app/Controllers/HomeController.php";
require_once "app/Controllers/AuthController.php";
require_once "app/Controllers/AdminController.php";
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
$uriParts = explode('/', $uri);

$authController = new AuthController($twig, $pdo);
$categoryController = new CategoryController($pdo);
$eventModel = new Event($pdo);
$adminController = new AdminController($twig, $pdo);

// Debugging: Print the modified URI
// echo "Modified URI: " . $uri . "<br>";

$isAdmin = strpos($uri, 'admin') === 0;

if ($isAdmin) {
    include 'app/Views/layouts/admin-header.php';

} else {
    include 'app/Views/layouts/header.php';
}

// Include the header
// include 'app/Views/layouts/header.php';

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
        $controller = new EventController($twig, $pdo, $base_url);
        $controller -> listEvents($twig);
        break;
    case 'events/create':
        $controller = new EventController($twig, $pdo, $base_url);
        $controller->createEvent(); // Create a new event
        break;
    // case 'events/details/{event_id}':
    //     // Handle event details page, pass event ID and fetch event details
    //     $eventId = $_GET['event_id'];
    //     $controller = new EventController($twig, $pdo);
    //     $controller->viewEvent($eventId);
    //     break;
    case preg_match('/^event_management_site\/events\/details\/(\d+)$/', $uri, $matches) ? true : false:
        $eventId = $matches[1];
        $controller = new EventController($twig, $pdo, $base_url);
        $controller->viewEvent($eventId);
        break;
    
    case preg_match('/^events\/attend\/(\d+)$/', $uri, $matches) ? true : false:
        $eventId = $matches[1];
        $controller = new EventController($twig, $pdo, $base_url);
        $controller->attendEvent($eventId);
        break;
    case preg_match('/^events\/cancel-attendance\/(\d+)$/', $uri, $matches) ? true : false:
        $eventId = $matches[1];
        $controller = new EventController($twig, $pdo, $base_url);
        $controller->cancelAttendance($eventId);
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

    // Admin routes
    case 'admin':
    case 'admin/':
        $controller = new AdminController($twig, $pdo);
        $controller->dashboard();
        break;
    case 'admin/hash-password':
        $controller = new AdminController($twig, $pdo);
        $controller->hashPassword();
        break;
    case 'admin/login':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $adminController->login($_POST['username'], $_POST['password']);
        } else {
            $adminController->showLoginPage();
        }    
        break;
    case 'admin/logout':
        $controller = new Authentication();
        $controller->logout();
        break;
    // case 'admin/events':
    //     $controller = new AdminController($twig, $pdo);
    //     $currentPage = $_GET['page'] ?? 1;
    //     $controller->listEvents($currentPage);
    //     break;
    // case 'admin/events/details/{event_id}':
    //     $controller = new AdminController($twig, $pdo);
    //     $eventId = $_GET['event_id'] ?? null;
    //     if ($eventId) {
    //         $controller->eventDetails($eventId);
    //     }
    //     break;
    case 'admin/events/create':
        $controller = new AdminController($twig, $pdo);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $controller->createEvent($_POST);
        } else {
            $controller->showCreateEventPage();
        }
        break;
    case 'admin/events/approve':
        $eventId = $_GET['event_id'] ?? null;
        if ($eventId) {
            $controller = new AdminController($twig, $pdo);
            $controller->approveEvent($eventId);
        }
        break;
    case 'admin/users':
        $controller = new AdminController($twig, $pdo);
        $controller->listUsers();
        break;
    case 'admin/users/edit':
        // Assuming you have a user ID passed as a GET parameter
        $userId = $_GET['user_id'] ?? null;
        if ($userId) {
            $controller = new AdminController($twig, $pdo);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->editUser($userId, $_POST);
            } else {
                $controller->editUserPage($userId);
            }
        }
        break;
        case 'admin/users/create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Handle POST request for creating a new user
                $adminController->createUser($_POST);
            } else {
                // Display the create user page
                $adminController->createUserPage();
            }
            break;
    default:
        // Handle dynamic routes for events, categories, and users
        if (preg_match('#^admin/events$#', parse_url($uri, PHP_URL_PATH))) {
            $controller = new AdminController($twig, $pdo);
            // Use $_GET to retrieve the 'page' query parameter
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $controller->listEvents($currentPage);
        }
        else if (preg_match('/^events\/details\/(\d+)$/', $uri, $matches)) {
            // Dynamic route for event details
            $eventId = $matches[1];
            $controller = new EventController($twig, $pdo, $base_url);
            $controller->viewEvent($eventId);
        } 
        else if (preg_match('#^admin/events/details/(\d+)$#', $uri, $matches)) {
            $eventId = $matches[1];
            $controller = new AdminController($twig, $pdo);
            $controller->eventDetails($eventId);
        }
        else if (preg_match('#^admin/events/edit/(\d+)$#', $uri, $matches)) {
            $eventId = $matches[1];
            $controller = new AdminController($twig, $pdo);
        
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Handle POST request for event editing
                $controller->editEvent($eventId, $_POST);
            } else {
                // Show the event edit page
                $controller->editEventPage($eventId);
            }
        }
        else if (preg_match('#^admin/events/approve/(\d+)$#', parse_url($uri, PHP_URL_PATH), $matches)) {
            $eventId = $matches[1];
            $currentPage = $_GET['page'] ?? 1; // Get the current page from the query string
            $controller = new AdminController($twig, $pdo);
            $controller->approveEvent($eventId, $currentPage);
        } 
        else if (preg_match('#^admin/events/disapprove/(\d+)$#', parse_url($uri, PHP_URL_PATH), $matches)) {
            $eventId = $matches[1];
            $currentPage = $_GET['page'] ?? 1; // Get the current page from the query string
            $controller = new AdminController($twig, $pdo);
            $controller->disapproveEvent($eventId, $currentPage);
        }
        else if (preg_match('#^admin/events/delete/(\d+)$#', parse_url($uri, PHP_URL_PATH), $matches)) {
            $eventId = $matches[1];
            $currentPage = $_GET['page'] ?? 1; // Get the current page from the query string
            $controller = new AdminController($twig, $pdo);
            $controller->deleteEvent($eventId, $currentPage);
        }        
        elseif (preg_match('#^admin/users/edit/(\d+)$#', $uri, $matches)) {
            $userId = $matches[1];
            $controller = new AdminController($twig, $pdo);
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $controller->editUser($userId, $_POST);
            } else {
                $controller->editUserPage($userId);
            }
        }
        else {
            // No route matched, display page not found
            echo "<p>Page not found</p>";
        }
        break;
}

// Include the footer
include 'app/Views/layouts/footer.php';

// var_dump($twig);
