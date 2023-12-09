<?php
class Validator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function sanitizeInput($input) {
        return htmlspecialchars(strip_tags($input));
    }

    public static function validatePasswordStrength($password) {
        // Example: Check if password is at least 6 characters long and contains numbers and letters
        return preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{6,}$/', $password);
    }

    public static function isNotEmpty($value) {
        return isset($value) && trim($value) !== '';
    }

    public static function validateUsername($username) {
        // Example: Check if username is alphanumeric and 5-15 characters long
        return preg_match('/^[a-zA-Z0-9]{5,15}$/', $username);
    }

    // Other validation methods...
}