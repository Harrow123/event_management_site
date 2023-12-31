<?php
class Event {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Properties like $title, $description, $date, etc.

    public function getAllEvents() {
        // Fetch all events from the database
        $stmt = $this->db->query("SELECT * FROM events WHERE is_approved");
        return $stmt->fetchAll();
    }

    public function getUserCreatedEvents($userId) {
        $stmt = $this->db->prepare("SELECT * FROM Events WHERE organizer_id = :userId ORDER BY start_date DESC");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
    
    public function getUserAttendedEvents($userId) {
        $stmt = $this->db->prepare("
            SELECT e.* FROM Events e
            JOIN Bookings b ON e.event_id = b.event_id
            WHERE b.user_id = :userId AND b.status = 'Attending'
            ORDER BY e.start_date DESC
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

    public function isUserAttending($eventId, $userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Bookings WHERE event_id = :eventId AND user_id = :userId");
        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // If the count is greater than 0, the user is attending the event
        return $stmt->fetchColumn() > 0;
    }

    public function attendEvent($eventId, $userId) {
        try {
            $stmt = $this->db->prepare("INSERT INTO Bookings (user_id, event_id, status) VALUES (:userId, :eventId, 'Attending')");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Handle exception (e.g., booking already exists)
            // You can log this error and/or return false to indicate failure
            return false;
        }
    }

    public function getAttendees($eventId) {
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.name, u.email
            FROM Bookings b
            JOIN Users u ON b.user_id = u.user_id
            WHERE b.event_id = :eventId AND b.status = 'Attending'
        ");
        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        
        $stmt->execute();

        // Fetch and return the list of attendees
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelEventAttendance($eventId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM Bookings WHERE event_id = :eventId AND user_id = :userId");
        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getTotalEventsCount() {
        $sql = "SELECT COUNT(*) FROM events";
        $stmt = $this->db->query($sql);
        return (int)$stmt->fetchColumn();
    }

    public function getEventsForPage($limit, $offset) {
        $sql = "SELECT e.*, u.name as organizer_name FROM events e JOIN users u ON e.organizer_id = u.user_id LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllEventsWithOrganizer() {
        // SQL query that joins the events table with the users table
        // to get the organizer's name. Adjust according to your database schema.
        $sql = "SELECT e.*, u.name as organizer_name FROM events e JOIN users u ON e.organizer_id = u.user_id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFeaturedEvents() {
        // Assuming your database query to fetch featured events
        // Modify the query according to your database structure
        $query = "SELECT * FROM Events WHERE is_featured = 1"; // You may have a column 'is_featured' to identify featured events
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventById($eventId) {
        // Fetch a single event by its ID
        $stmt = $this->db->prepare("SELECT * FROM Events WHERE event_id = ?");
        $stmt->execute([$eventId]);
        return $stmt->fetch();
    }

    public function isAttending($eventId, $userId) {
        if ($userId === null) {
            // If no user ID is provided, return false
            return false;
        }

        // Prepare the SQL query to check if the user is attending the event
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM Bookings WHERE event_id = :eventId AND user_id = :userId");
        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the count and return true if the count is greater than 0
        return $stmt->fetchColumn() > 0;
    }

    public function getEventImage($eventId) {
        $stmt = $this->db->prepare("SELECT image FROM Events WHERE event_id = ?");
        $stmt->execute([$eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            return $result['image_path'];
        } else {
            // Return a default image path or handle the case when no image is found
            return 'default.png';
        }
    }
    

    public function createEvent($eventData, $categoryId) {
        // Extract event data from the $eventData array
        $title = $eventData['title'];
        $description = $eventData['description'];
        $startDate = $eventData['start_date'];
        $endDate = $eventData['end_date'];
        $venue = $eventData['venue'];
        // $organizerId = $eventData['organizer_id'];
        $image_path =$eventData['image_path'];
        $isFeatured = $eventData['is_featured'];

        // echo $image_path;
        // exit();

        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            $organizerId = $_SESSION['admin_id'] ?? null; // Use 'admin_id' if available
            $isApproved = 1; // Set 'is_approved' to 1 for admin users
        
        } else {
            $organizerId = $_SESSION['user_id'] ?? null; // Fallback to 'user_id' for regular users
            $isApproved = 0; // Set 'is_approved' to 0 for regular users
        }
        
        // Validate event data (you can add more validation as needed)
        if (empty($title) || empty($description) || empty($startDate) || empty($endDate) || empty($venue) || empty($organizerId)) {
            // Handle validation error, e.g., return an error message or redirect to the form with errors
            return ['success' => false, 'error' => 'Please fill in all required fields.'];
        }
        
        try {
            // Begin a database transaction
            $this->db->beginTransaction();
            
            // Insert event details into the Events table
            $stmt = $this->db->prepare("INSERT INTO Events (title, description, start_date, end_date, venue, organizer_id, image_path, is_featured, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $startDate, $endDate, $venue, $organizerId, $image_path, $isFeatured, $isApproved]);
            
            // Check if the insertion was successful
            if ($stmt->rowCount() === 0) {
                // Rollback the transaction and return an error if insertion failed
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Failed to create the event.'];
            }
            
            // Get the event_id of the newly inserted event
            $eventId = $this->db->lastInsertId();

            if (!$this->mapEventToCategory($eventId, $categoryId)) {
                // Rollback the transaction and return an error if mapping failed
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Failed to map event to categories.'];
            }
            
            // Commit the transaction as all operations were successful
            $this->db->commit();
            
            // Return success
            return ['success' => true, 'message' => 'Event created successfully.', 'eventId' => $eventId];
        } catch (PDOException $e) {
            // Handle any database errors (e.g., duplicate event title, constraint violations)
            // You can log the error or perform other error handling as needed
            // Rollback the transaction if an error occurs
            $this->db->rollBack();
            return ['success' => false, 'error' => 'An error occurred while creating the event.'];
        }
    }  
    
    public function updateEventById($eventId, $eventData) {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            $query = "UPDATE Events SET title = :title, description = :description, start_date = :start_date, end_date = :end_date, venue = :venue WHERE event_id = :event_id";
            $stmt = $this->db->prepare($query);

            // Bind values
            $stmt->bindValue(':title', $eventData['title']);
            $stmt->bindValue(':description', $eventData['description']);
            $stmt->bindValue(':start_date', $eventData['start_date']);
            $stmt->bindValue(':end_date', $eventData['end_date']);
            $stmt->bindValue(':venue', $eventData['venue']);
            $stmt->bindValue(':event_id', $eventId);

            // Execute the statement
            $stmt->execute();
            
            // Commit transaction
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            // Roll back if there is an error
            $this->db->rollBack();
            throw $e;
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

    public function deleteEventCategoryMappings($eventId) {
        $stmt = $this->db->prepare("DELETE FROM event_category_mapping WHERE event_id = :eventId");
        return $stmt->execute([':eventId' => $eventId]);
    }

    public function deleteEventById($eventId) {
        try {
            // Begin transaction
            $this->db->beginTransaction();
    
            // First, delete related records in event_category_mapping
            $this->deleteEventCategoryMappings($eventId);
        
            // Then, delete the event itself
            $stmt = $this->db->prepare("DELETE FROM events WHERE event_id = :eventId");
            $stmt->execute([':eventId' => $eventId]);
    
            // Commit the transaction
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $this->db->rollBack();
            // Optionally, you can log the error message: $e->getMessage();
            return false;
        }
    }    

    public function approveEventById($eventId) {
        // 1' represents the status of an approved event
        return $this->setEventApprovalStatus($eventId, 1);
    }

    public function setEventApprovalStatus($eventId, $status) {
        $stmt = $this->db->prepare("UPDATE events SET is_approved = :status WHERE event_id = :eventId");
        return $stmt->execute([':status' => $status, ':eventId' => $eventId]);
    }

    public function getTotalEvents() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM Events");
        return $stmt->fetchColumn(); // Fetch the count from the first column
    }

    public function getApprovedEventsCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM Events WHERE is_approved = 1");
        return $stmt->fetchColumn(); // Fetch the count from the first column
    }

    public function getPendingEventsCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM Events WHERE is_approved = 0");
        return $stmt->fetchColumn(); // Fetch the count from the first column
    }
}
