<?php
class User {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function emailExists($email, $userId) {
        // SQL query to check for existing email, excluding current user
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND user_id != :userId");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    
        return $stmt->fetchColumn() > 0;
    }
    
    // Method to check if a username exists in the database
    public function usernameExists($username, $userId) {
        // SQL query to check for existing username, excluding current user
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username AND user_id != :userId");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    
        return $stmt->fetchColumn() > 0;
    }

    //write a method to get if the user is a admin based on the session
    public function isAdmin() {
        $stmt = $this->db->prepare("SELECT is_admin FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $isAdmin = $stmt->fetchColumn();
    
        return $isAdmin;
    }

    public function register($name, $username, $email, $password, $gender, $address, $profile_picture) {
        // Check if the email is already registered
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            return "Email is already registered.";
        }

        // Check if the username is already taken
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            return "Username is already taken.";
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO Users (name, username, email, password, gender, address, profile_picture, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $username, $email, $hashedPassword, $gender, $address, $profile_picture, 0]);
        // Additional error handling and checks can be added

         // Check if the registration was successful
        if ($stmt->rowCount() > 0) {
            return "Registration successful."; // You can also return a success message or status code
        } else {
            return "Registration failed. Please try again later."; // Registration failed for some reason
        }
    }   

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session or token as per your session handling logic
            $_SESSION['user_id'] = $user['user_id'];
            return true;
        }
        return false;
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAllUsers() {
        $stmt = $this->db->prepare("SELECT * FROM Users where is_admin=0");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserEvents($userId) {
        // Fetch user's events from the database based on user ID
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE user_id = ?");
        $stmt->execute([$userId]);
        $userEvents = $stmt->fetchAll();
    
        // You can further process the $userEvents data as needed, e.g., filter by status, date, etc.
    
        return $userEvents;
    }

    public function updateUserProfile($userId, $userData) {
        try {
            // Start building the SQL statement
            $sql = "UPDATE users SET ";
            $params = [];
            $updateFields = [];
    
            foreach ($userData as $key => $value) {
                if (!empty($value) && $key != 'confirm_password') {
                    // Hash the password
                    if ($key == 'password') {
                        $value = password_hash($value, PASSWORD_DEFAULT);
                    }
                    $updateFields[] = "$key = :$key";
                    $params[$key] = $value;
                }
            }
    
            if (count($updateFields) == 0) {
                // No fields to update
                return false;
            }
    
            $sql .= implode(", ", $updateFields);
            $sql .= " WHERE user_id = :userId";
            $params['userId'] = $userId;

            // echo "SQL Query: " . $sql;
            // echo "Parameters: " . print_r($params, true);
            // exit;
    
            // Prepare the statement
            $stmt = $this->db->prepare($sql);
    
            // Bind the parameters
            foreach ($params as $key => &$val) {
                $stmt->bindParam(':'.$key, $val);
            }
    
            // Execute the statement
            $stmt->execute();

            // $errorInfo = $stmt->errorInfo();
            // if ($errorInfo[0] != '00000') {
            //     echo error_log("SQL Error: " . print_r($errorInfo, true));
            //     exit;
            // }
                
            // Check if any rows were updated
            if ($stmt->rowCount() > 0) {
                return true; // Update successful
            } else {
                return false; // No rows updated
            }
        } catch (PDOException $e) {
            // Handle any errors (e.g., log them)
            error_log("Database error: " . $e->getMessage()); // Log the error
            return false;
        }
    }  

    public function updateUserById($userId, $userData) {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            $query = "UPDATE Users SET username = :username, name = :name, address = :address, gender = :gender WHERE user_id = :user_id";

            // If password is set and not empty, include it in the update
            if (!empty($userData['password'])) {
                $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
                $query = "UPDATE Users SET username = :username, name = :name, address = :address, gender = :gender, password = :password WHERE user_id = :user_id";
            }

            $stmt = $this->db->prepare($query);

            // Bind values
            $stmt->bindValue(':username', $userData['username']);
            $stmt->bindValue(':name', $userData['name']);
            $stmt->bindValue(':address', $userData['address']);
            $stmt->bindValue(':gender', $userData['gender']);
            $stmt->bindValue(':user_id', $userId);

            // Bind password if it's set
            if (!empty($userData['password'])) {
                $stmt->bindValue(':password', $userData['password']);
            }

            // Execute the statement
            $stmt->execute();
            
            // Commit transaction
            $this->db->commit();
        } catch (PDOException $e) {
            // Roll back if there is an error
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function getTotalUsers() {
        // Logic to fetch the total number of users
        $stmt = $this->db->query("SELECT COUNT(*) FROM Users");
        return $stmt->fetchColumn(); // Fetch the count from the first column
    }
    
}
