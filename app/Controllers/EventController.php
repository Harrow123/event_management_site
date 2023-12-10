<?php
require_once __DIR__ . '/../Models/Event.php';
require_once __DIR__ . '/../Models/Category.php';

class EventController {
    private $twig;
    private $eventModel;
    private $categoryModel;

    public function __construct($twig, $pdo) {
        $this->twig = $twig;
        $this->eventModel = new Event($pdo);
        $this->categoryModel = new Category($pdo);
    }

    public function listEvents($twig) {
        // Fetch events from the Event model
        $events = $this->eventModel->getAllEvents();

        // Fetch all categories from the Category model
        $categories = $this->categoryModel->getAllCategories();

        // Render the list.html.twig with fetched events
        echo $this->twig->render('events/list.html.twig', ['events' => $events, 'categories' => $categories]);
    }

    public function viewEvent($twig, $eventId) {
        // Fetch event details from the Event model
        $event = $this->eventModel->getEventById($eventId);

        // Render the detail.html.twig with event details
        echo $this->twig->render('events/details.html.twig', ['event' => $event]);
    }

    // Add methods for creating, updating, and deleting events
    public function createEvent($eventData) {
        // Validate and create event
    }

    public function deleteEvent($eventId) {
        // Validate and create event
    }
}
