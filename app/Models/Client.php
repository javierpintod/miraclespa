<?php
/**
 * MIRACLE SPA - Modelo Client
 */

require_once __DIR__ . '/../../config/database.php';

class Client {
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM clients WHERE LOWER(email) = LOWER(?) LIMIT 1");
        $stmt->execute([trim($email)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findOrCreate(string $name, string $email, string $phone, ?string $notes = null): array {
        $existing = self::findByEmail($email);
        $db = Database::getConnection();

        if ($existing) {
            // Actualizar nombre o teléfono si han cambiado
            $stmt = $db->prepare("UPDATE clients SET name = ?, phone = ?, notes = COALESCE(?, notes) WHERE id = ?");
            $stmt->execute([trim($name), trim($phone), $notes, $existing['id']]);
            $existing['name'] = trim($name);
            $existing['phone'] = trim($phone);
            return $existing;
        }

        $stmt = $db->prepare("INSERT INTO clients (name, email, phone, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([trim($name), trim($email), trim($phone), $notes]);
        $newId = (int)$db->lastInsertId();

        return [
            'id' => $newId,
            'name' => trim($name),
            'email' => trim($email),
            'phone' => trim($phone),
            'notes' => $notes
        ];
    }

    public static function getAllWithStats(): array {
        $db = Database::getConnection();
        $sql = "SELECT c.*, 
                       COUNT(a.id) AS total_appointments,
                       COALESCE(SUM(CASE WHEN a.status = 'completada' THEN a.price ELSE 0 END), 0) AS total_spent,
                       MAX(a.date) AS last_visit
                FROM clients c
                LEFT JOIN appointments a ON a.client_id = c.id
                GROUP BY c.id
                ORDER BY total_appointments DESC, c.name ASC";
        return $db->query($sql)->fetchAll();
    }
}
