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
        $image_url = "";

        // Append the image URL to each event
        foreach ($events as $key => $event) {
            $events[$key]['image_url'] = 'public/assets/images/event_images/' . $event['image_path'];
        }

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

    private function uploadImage($file) {
        // Define the upload directory
        $uploadDirectory = __DIR__ . '/../../public/image/event/uploads/';
    
        // Check if the upload directory exists; if not, create it
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }
    
        // Generate a unique filename using a combination of UID and the original filename
        $uniqueUid = uniqid();
        $uniqueFileName = $uniqueUid . '_' . $file['name'];
    
        // Move the uploaded file to the upload directory
        $targetFilePath = $uploadDirectory . $uniqueFileName;
    
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return 'image/event/uploads/' . $uniqueFileName; // Return the relative path to the image
        } else {
            // Handle file upload error, e.g., return an error message or use a default image path
            return 'image/event/uploads/default.png';
        }
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
            $image = $_FILES['event_image'];
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    
            // Validate and sanitize the data as needed
            if (!empty($_FILES['event_image']['name'])) {
                $imagePath = $this->uploadImage($_FILES['event_image']);
            } else {
                $imagePath = 'default.png'; // Use a default image if none is uploaded
            }

            // Use the uploadImage function to handle image upload
            $imagePath = $this->uploadImage($image);
    
            // Insert event details into the Events table
            $eventData = [
                'title' => $title,
                'description' => $description,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'venue' => $venue,
                'organizer_id' => $_SESSION['user_id'],
                'is_approved' => 0,
                'image_path' => $image,
                'is_featured' => $isFeatured,
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
