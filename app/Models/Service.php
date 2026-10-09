<?php
/**
 * MIRACLE SPA - Modelo Service
 */

require_once __DIR__ . '/../../config/database.php';

class Service {
    public static function getAll(bool $onlyActive = true): array {
        $db = Database::getConnection();
        $where = $onlyActive ? "WHERE s.active = 1" : "";
        $sql = "SELECT s.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon
                FROM services s
                JOIN categories c ON c.id = s.category_id
                {$where}
                ORDER BY c.display_order ASC, s.name ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT s.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon
                              FROM services s
                              JOIN categories c ON c.id = s.category_id
                              WHERE s.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getByCategory(int $categoryId, bool $onlyActive = true): array {
        $db = Database::getConnection();
        $sql = "SELECT * FROM services WHERE category_id = ?" . ($onlyActive ? " AND active = 1" : "") . " ORDER BY name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO services (category_id, name, description, duration_minutes, price, active, image_url) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['category_id'],
            trim($data['name']),
            $data['description'] ?? '',
            (int)$data['duration_minutes'],
            (float)$data['price'],
            isset($data['active']) ? (int)$data['active'] : 1,
            $data['image_url'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE services 
                              SET category_id = ?, name = ?, description = ?, duration_minutes = ?, price = ?, active = ?
                              WHERE id = ?");
        return $stmt->execute([
            $data['category_id'],
            trim($data['name']),
            $data['description'] ?? '',
            (int)$data['duration_minutes'],
            (float)$data['price'],
            (int)$data['active'],
            $id
        ]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function toggleActive(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE services SET active = IF(active = 1, 0, 1) WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getQualifiedProfessionals(int $serviceId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.* 
                              FROM professionals p
                              JOIN professional_services ps ON ps.professional_id = p.id
                              WHERE ps.service_id = ? AND p.active = 1
                              ORDER BY p.name ASC");
        $stmt->execute([$serviceId]);
        return $stmt->fetchAll();
    }
}
