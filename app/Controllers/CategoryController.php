<?php

class CategoryController {
    public function __construct($twig, $pdo) {
        $this->twig = $twig;
    }

    public function getAllCategories() {
        // Fetch categories from the database (adjust the query as needed)
        $query = "SELECT * FROM Event_Categories";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
