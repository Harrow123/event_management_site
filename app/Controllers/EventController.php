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
    public function createEvent() {
        // Fetch categories from the database (you should have a method in your model for this)
        $categories = $this->categoryModel->getAllCategories(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve and sanitize event data
            $title = $this->validator::sanitizeInput($_POST['title']);
            $description = $this->validator::sanitizeInput($_POST['description']);
            $startDate = $this->validator::sanitizeInput($_POST['start_date']);
            $endDate = $this->validator::sanitizeInput($_POST['end_date']);
            $venue = $this->validator::sanitizeInput($_POST['venue']);
            $categories = $_POST['categories'];
    
            // Validate and sanitize the data as needed
    
            // Insert event details into the Events table
            $eventData = [
                'title' => $title,
                'description' => $description,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'venue' => $venue,
                'organizer_id' => $_SESSION['user_id'],
                'is_approved' => 0,
            ];

            // Get selected category IDs from the form
            $categoryIds = $_POST['categories'] ?? [];
    
            $eventId = $this->eventModel->createEvent($eventData);
    
            if ($eventId) {
                // Insert category mappings into the Event_Category_Mapping table
                foreach ($categoryIds as $category_id) {
                    $this->eventModel->mapEventToCategory($eventId, $category_id);
                }
    
                // Event creation successful, you can redirect to the event details page or show a success message
                header('Location: /events/' . $eventId); // Assuming you have a route for event details
                exit;
            } else {
                // Event creation failed, handle the error or display an error message
                echo "Event creation failed.";
            }
        } else {
            // Display the form for creating events
            echo $this->twig->render('events/create.html.twig', ['categories' => $categories]);
        }
    }    

    public function deleteEvent($eventId) {
        // Validate and create event
    }
}
