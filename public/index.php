<?php
require_once "../app/Config/database.php";
require_once "../app/Controllers/UserController.php";
require_once "../app/Controllers/HomeController.php";

$uri = trim($_SERVER['REQUEST_URI'], '/');

// Include the header
include '../app/Views/layouts/header.php';

// Simple router example
switch ($uri) {
    case '':
        case 'home':
            $controller = new HomeController();
            $controller->index();
            break;
    case 'users/profile':
        $userId = $_GET['id'] ?? 1; // default to user ID 1
        $controller = new UserController($pdo);
        $controller->getUserProfile($userId);
        break;
    // More routes here
    default:
        // Page not found or default case
        echo "<p>Page not found</p>";
}

// Include the footer
include '../app/Views/layouts/footer.php';
