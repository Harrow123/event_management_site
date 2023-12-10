<?php
class Event {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Properties like $title, $description, $date, etc.

    public function getAllEvents() {
        // Fetch all events from the database
        $stmt = $this->db->query("SELECT * FROM events");
        return $stmt->fetchAll();
    }

    public function getEventById($eventId) {
        // Fetch a single event by its ID
        $stmt = $this->db->prepare("SELECT * FROM Events WHERE event_id = ?");
        $stmt->execute([$eventId]);
        return $stmt->fetch();
    }

    public function createEvent($eventData, $categoryIds) {
        // Extract event data from the $eventData array
        $title = $eventData['title'];
        $description = $eventData['description'];
        $startDate = $eventData['start_date'];
        $endDate = $eventData['end_date'];
        $venue = $eventData['venue'];
        $organizerId = $eventData['organizer_id']; // Assuming you have the organizer's user_id
        
        // Validate event data (you can add more validation as needed)
        if (empty($title) || empty($description) || empty($startDate) || empty($endDate) || empty($venue) || empty($organizerId)) {
            // Handle validation error, e.g., return an error message or redirect to the form with errors
            return ['success' => false, 'error' => 'Please fill in all required fields.'];
        }
        
        try {
            // Begin a database transaction (assuming you have a database connection)
            $this->db->beginTransaction();
            
            // Insert event details into the Events table
            $stmt = $this->db->prepare("INSERT INTO Events (title, description, start_date, end_date, venue, organizer_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $startDate, $endDate, $venue, $organizerId]);
            
            // Check if the insertion was successful
            if ($stmt->rowCount() === 0) {
                // Rollback the transaction and return an error if insertion failed
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Failed to create the event.'];
            }
            
            // Get the event_id of the newly inserted event
            $eventId = $this->db->lastInsertId();
            
            // Map the event to categories in the Event_Category_Mapping table
            foreach ($categoryIds as $categoryId) {
                if (!$this->mapEventToCategory($eventId, $categoryId)) {
                    // Rollback the transaction and return an error if mapping failed
                    $this->db->rollBack();
                    return ['success' => false, 'error' => 'Failed to map event to categories.'];
                }
            }
            
            // Commit the transaction as all operations were successful
            $this->db->commit();
            
            // Return success
            return ['success' => true, 'message' => 'Event created successfully.'];
        } catch (PDOException $e) {
            // Handle any database errors (e.g., duplicate event title, constraint violations)
            // You can log the error or perform other error handling as needed
            // Rollback the transaction if an error occurs
            $this->db->rollBack();
            return ['success' => false, 'error' => 'An error occurred while creating the event.'];
        }
    }    

    public function mapEventToCategory($eventId, $categoryId) {
        try {
            // Prepare the SQL statement for inserting into Event_Category_Mapping table
            $stmt = $this->db->prepare("INSERT INTO Event_Category_Mapping (event_id, category_id) VALUES (?, ?)");
            
            // Bind parameters and execute the statement
            $stmt->execute([$eventId, $categoryId]);
            
            // Check if the insertion was successful
            if ($stmt->rowCount() > 0) {
                return true; // Return true on success
            } else {
                return false; // Return false on failure
            }
        } catch (PDOException $e) {
            // Handle any database errors (e.g., duplicate mappings) here
            // You can log the error or perform other error handling as needed
            return false; // Return false if an error occurs
        }
    }
    

    public function updateEvent($eventId, $eventData) {
        // Validate and update the specified event
    }

    public function deleteEvent($eventId) {
        // Delete the specified event
    }

    public function approveEvent($eventId) {
        // Set is_approved to true for the event
    }
}
