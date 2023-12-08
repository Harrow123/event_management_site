<?php
class HomeController {
    public function index() {
        // Fetch featured events or any other necessary data
        $featuredEvents = []; // Assume this is fetched from the model
        
        // Load the home view
        include __DIR__ . '/../Views/home/index.php';
    }
}
