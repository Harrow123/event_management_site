<?php
class HomeController { 
    private $categoryController;
    private $eventModel;
    private $twig;

    public function __construct($twig, $categoryController, $eventModel) {
        $this->twig = $twig;
        $this->categoryController = $categoryController;
        $this->eventModel = $eventModel;
    }

    public function index() {
        // Fetch categories using the CategoryController
        $categories = $this->categoryController->getAllCategories(); 

        // Fetch featured events from the database using the EventModel
        $featuredEvents = $this->eventModel->getFeaturedEvents();

        // Render the home view using Twig
        echo $this->twig->render('home/index.html.twig', ['featuredEvents' => $featuredEvents, 'categories' => $categories]);
    }
}
