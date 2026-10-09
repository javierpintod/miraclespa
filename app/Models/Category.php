<?php
/**
 * MIRACLE SPA - Modelo Category
 */

require_once __DIR__ . '/../../config/database.php';

class Category {
    public static function getAll(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM categories ORDER BY display_order ASC, name ASC")->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
