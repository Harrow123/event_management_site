<?php
require_once __DIR__ . '/../Models/Event.php';
require_once __DIR__ . '/../Models/Category.php';

class EventController {
    private $twig;
    private $eventModel;
    private $categoryModel;
    private $validator;
    private $baseUrl;

    public function __construct($twig, $pdo,$baseUrl) {
        $this->twig = $twig;
        $this->eventModel = new Event($pdo);
        $this->categoryModel = new Category($pdo);
        require_once __DIR__ . '/../Utils/Validator.php';
        $this->validator = new Validator();
        $this->baseUrl = $baseUrl;
    }

    public function listEvents($twig) {
        // Fetch events from the Event model
        $events = $this->eventModel->getAllEvents();

        // Append the image URL to each event
        foreach ($events as $key => $event) {
            $events[$key]['image_url'] = 'public/assets/images/event_images/' . $event['image_path'];
        }

        // Fetch all categories from the Category model
        $categories = $this->categoryModel->getAllCategories();

        // Render the list.html.twig with fetched events
        echo $this->twig->render('events/list.html.twig', ['events' => $events, 'categories' => $categories]);
    }

    // public function viewEvent($eventId) {
    //     // Fetch event details from the Event model
    //     $event = $this->eventModel->getEventById($eventId);
    //     // $events['image_url'] = 'public/assets/images/event_images/' . $event['image_path'];
    //     $event['image_url'] = $this->baseUrl . 'public/assets/images/event_images/' . $event['image_path'];
    //     // echo $events['image_url'];
    //     // exit();

    //     // Render the detail.html.twig with event details
    //     echo $this->twig->render('events/details.html.twig', ['event' => $event]);
    // }

    public function viewEvent($eventId) {
        $isAttending = $this->eventModel->isAttending($eventId, $_SESSION['user_id'] ?? null);

        // Fetch event details
        $event = $this->eventModel->getEventById($eventId); // Fetch event from the database based on $eventId

        $event['image_url'] = 'http://localhost/event_management_site/public/assets/images/event_images/' . $event['image_path'];
    
        // Check if the current user is the organizer
        $isOrganizer = ($_SESSION['user_id'] ?? null) == $event['organizer_id'];
    
        // Fetch attendees
        $attendees = $this->eventModel->getAttendees($eventId, ); // Fetch attendees from the database

        // if ($isOrganizer) {
        //     // The current user is the organizer, show attendees
        //     $attendees = $this->eventModel->getAttendees($eventId, );
        // }
    
        // Render the view with event details and attendees
        echo $this->twig->render('events/details.html.twig', [
            'event' => $event,
            'isOrganizer' => $isOrganizer,
            'attendees' => $attendees,
            'isAttending' => $isAttending
        ]);
    }   

    public function viewUserEvents() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            // Redirect to login or handle not logged in user
        }
    
        $createdEvents = $this->eventModel->getUserCreatedEvents($userId);
        $attendedEvents = $this->eventModel->getUserAttendedEvents($userId);

        // Append the image URL to each created event
        foreach ($createdEvents as $key => $event) {
            $createdEvents[$key]['image_url'] = '/event_management_site/public/assets/images/event_images/' . $event['image_path'];
        }

        // Append the image URL to each attended event
        foreach ($attendedEvents as $key => $event) {
            $attendedEvents[$key]['image_url'] = '/event_management_site/public/assets/images/event_images/' . $event['image_path'];
        }
    
        echo $this->twig->render('users/my-events.html.twig', [
            'createdEvents' => $createdEvents,
            'attendedEvents' => $attendedEvents
        ]);
    }    
    
    public function attendEvent($eventId) {
        // Check if the user is logged in
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            // User is not logged in, redirect to login page or show an error message
            // Redirect to login page example
            header('Location: login');
            exit();
        }
    
        // Fetch event details to check if the event exists and if the user is the organizer
        $event = $this->eventModel->getEventById($eventId);
        if (!$event) {
            // Event does not exist, handle the error
            // Redirect to error page or show an error message
            exit('Event does not exist.');
        }
    
        if ($event['organizer_id'] == $userId) {
            // User is the organizer of the event, they cannot attend it
            // Redirect or show an error message
            exit('Organizers cannot attend their own event.');
        }
    
        // Check if the user is already attending the event
        if ($this->eventModel->isUserAttending($eventId, $userId)) {
            // User is already attending the event
            // Redirect or show an error message
            exit('You are already attending this event.');
        }
    
        // Insert a record into the Bookings table
        $success = $this->eventModel->attendEvent($eventId, $userId);
        if ($success) {
            // Redirect to the event details page with a success message
            header('Location: /event_management_site/events/details/' . $eventId);
        } else {
            // Handle the case where the booking failed
            exit('Failed to attend the event. Please try again.');
        }
    }   
    
    public function cancelAttendance($eventId) {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            // Redirect to login if user is not logged in
            header('Location: /login');
            exit();
        }

        if ($this->eventModel->cancelEventAttendance($eventId, $userId)) {
            // Redirect to the event details page with a success message
            header("Location: /event_management_site/events/details/{$eventId}");
        } else {
            // Handle the error, e.g., attendance record not found
            // Redirect to an error page or display an error message
            exit('Error canceling attendance.');
        }
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
            return 'assets/images/event/event_images/' . $uniqueFileName; // Return the relative path to the image
        } else {
            // Handle file upload error, e.g., return an error message or use a default image path
            return 'default.jpg';
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
            $startTime = $this->validator::sanitizeInput($_POST['start_time']);
            $endDate = $this->validator::sanitizeInput($_POST['end_date']);
            $endTime = $this->validator::sanitizeInput($_POST['end_time']);
            $venue = $this->validator::sanitizeInput($_POST['venue']);
            $categoryID = $_POST['category_id'] ?? null;
            $image = isset($_FILES['event_image']) ? $_FILES['event_image'] : null;
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

            // Check if the user is an admin
            $isAdmin = $_SESSION['is_admin'] ?? false;
    
            // Validate and sanitize the data as needed
            // if (!empty($_FILES['event_image']['name'])) {
            //     $imagePath = $this->uploadImage($_FILES['event_image']);
            // } else {
            //     $imagePath = 'default.png'; // Use a default image if none is uploaded
            // }
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                // File is uploaded and no errors
                $image = $_FILES['event_image'];
                $imagePath = $this->uploadImage($image);
            } else {
                // No file is uploaded or there is an error, use default image
                $imagePath = 'public/assets/images/event_images/default.png';
            }            
            // Combine date and time into datetime format for MySQL
            $startDateTime = date('Y-m-d H:i:s', strtotime("$startDate $startTime"));
            $endDateTime = date('Y-m-d H:i:s', strtotime("$endDate $endTime"));

            // Use the uploadImage function to handle image upload
            $imagePath = $this->uploadImage($image);
    
            // Insert event details into the Events table
            $eventData = [
                'title' => $title,
                'description' => $description,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
                'venue' => $venue,
                'organizer_id' => $_SESSION['user_id'],
                'is_approved' => $isAdmin ? 1 : 0,
                // 'image_path' => $image,
                'image_path' => $imagePath,
                'is_featured' => $isFeatured,
            ];

            // echo $eventData;
            // var_dump($eventData);
            // exit();

            // Get selected category IDs from the form
            // $categoryIds = $_POST['categories'] ?? [];
    
            $result = $this->eventModel->createEvent($eventData, $categoryID);
            // var_dump($result); // Debugging line
            // exit;
    
            if ($result['success']) {
                $eventId = $result['eventId'];
                // Event creation successful, you can redirect to the event details page or show a success message
                header('Location: ' . $this->baseUrl . 'events/details/' . $eventId); // Assuming you have a route for event details
                exit;
            } else {
                // Event creation failed, handle the error or display an error message
                echo "Event creation failed.";
            }
        } else {
            // Display the form for creating events
            echo $this->twig->render('events/create.html.twig', ['categories' => $categories]);
            // Optionally, show the error message from the model
            if (isset($result['error'])) {
                echo ' Error: ' . $result['error'];
            }
        }
    }    

    public function deleteEvent($eventId) {
        // Validate and create event
    }
}
