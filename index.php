<?php
require_once "app/Config/database.php";
require_once "app/Controllers/UserController.php";

$uri = $_SERVER['REQUEST_URI'];

// Simple router example
switch ($uri) {
    case '/profile':
        $userId = $_GET['id'] ?? 1; // default to user ID 1
        $controller = new UserController($pdo);
        $controller->getUserProfile($userId);
        break;
    // More routes here
}
