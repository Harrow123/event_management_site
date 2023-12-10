<?php
class Category {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getAllCategories() {
        $stmt = $this->db->prepare("SELECT * FROM event_categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM event_categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategory($name) {
        $stmt = $this->db->prepare("INSERT INTO event_categories (name) VALUES (?)");
        $stmt->execute([$name]);
        return $this->db->lastInsertId(); // Return the ID of the newly created category
    }

    public function updateCategory($categoryId, $name) {
        $stmt = $this->db->prepare("UPDATE event_categories SET name = ? WHERE id = ?");
        $stmt->execute([$name, $categoryId]);
        return $stmt->rowCount() > 0; // Check if any rows were affected
    }

    public function deleteCategory($categoryId) {
        $stmt = $this->db->prepare("DELETE FROM event_categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->rowCount() > 0; // Check if any rows were affected
    }
}
