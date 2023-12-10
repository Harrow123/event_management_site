<?php
class Authentication {
    public function login($username, $password) {
        // Check credentials and set up session
    }

    function logout() {
        // Clear the session data and destroy the session
        $_SESSION = array();
        session_destroy();
        header('Location: login');
    }

    function isLoggedIn() {
        return isset($_SESSION['user_id']); // Assuming user_id is set in session on login
    }

    function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }
}