<?php
class ImageUploader {

    public static function uploadImage($file, $location) {
        // Define the upload directory
        $uploadDirectory = __DIR__ . '/../../public/assets/images/' . $location . '/';

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
            return $uniqueFileName; // Return the filename (or relative path from public directory)
        } else {
            // Handle file upload error, e.g., return an error message or use a default image path
            return null; // Or handle the error as per your application's need
        }
    }
}
