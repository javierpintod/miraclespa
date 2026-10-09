<?php
/**
 * MIRACLE SPA - Modelo Professional
 */

require_once __DIR__ . '/../../config/database.php';

class Professional {
    public static function getAll(bool $onlyActive = true): array {
        $db = Database::getConnection();
        $where = $onlyActive ? "WHERE p.active = 1" : "";
        $sql = "SELECT p.*,
                       COUNT(DISTINCT ps.service_id) AS services_count,
                       COUNT(DISTINCT a.id) AS appointments_count
                FROM professionals p
                LEFT JOIN professional_services ps ON ps.professional_id = p.id
                LEFT JOIN appointments a ON a.professional_id = p.id AND a.status = 'completada'
                {$where}
                GROUP BY p.id
                ORDER BY p.name ASC";
        return $db->query($sql)->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM professionals WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getAssignedServiceIds(int $professionalId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT service_id FROM professional_services WHERE professional_id = ?");
        $stmt->execute([$professionalId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public static function getAssignedServices(int $professionalId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT s.*, c.name AS category_name 
                              FROM services s
                              JOIN professional_services ps ON ps.service_id = s.id
                              JOIN categories c ON c.id = s.category_id
                              WHERE ps.professional_id = ? AND s.active = 1
                              ORDER BY s.name ASC");
        $stmt->execute([$professionalId]);
        return $stmt->fetchAll();
    }

    public static function setAssignedServices(int $professionalId, array $serviceIds): void {
        $db = Database::getConnection();
        $db->prepare("DELETE FROM professional_services WHERE professional_id = ?")->execute([$professionalId]);
        if (!empty($serviceIds)) {
            $stmt = $db->prepare("INSERT INTO professional_services (professional_id, service_id) VALUES (?, ?)");
            foreach ($serviceIds as $sid) {
                $stmt->execute([$professionalId, (int)$sid]);
            }
        }
    }

    public static function create(array $data, array $serviceIds = []): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO professionals (name, email, phone, title, bio, avatar, rating, active)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            $data['phone'] ?? '',
            $data['title'] ?? 'Especialista en Spa',
            $data['bio'] ?? '',
            $data['avatar'] ?? null,
            $data['rating'] ?? 5.0,
            isset($data['active']) ? (int)$data['active'] : 1
        ]);
        $newId = (int)$db->lastInsertId();

        if (!empty($serviceIds)) {
            self::setAssignedServices($newId, $serviceIds);
        }

        // Crear horarios por defecto (Lunes a Sábado 09:00 - 19:00, Domingo Libre)
        $scheduleStmt = $db->prepare("INSERT INTO schedules (professional_id, day_of_week, start_time, end_time, lunch_start, lunch_end, is_off) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
        for ($day = 0; $day <= 6; $day++) {
            $isOff = ($day === 0) ? 1 : 0;
            $scheduleStmt->execute([
                $newId,
                $day,
                '09:00:00',
                ($day === 6) ? '18:00:00' : '19:00:00',
                '13:00:00',
                '14:00:00',
                $isOff
            ]);
        }

        return $newId;
    }

    public static function update(int $id, array $data, ?array $serviceIds = null): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE professionals 
                              SET name = ?, email = ?, phone = ?, title = ?, bio = ?, active = ?
                              WHERE id = ?");
        $res = $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            $data['phone'] ?? '',
            $data['title'] ?? '',
            $data['bio'] ?? '',
            (int)$data['active'],
            $id
        ]);

        if ($serviceIds !== null) {
            self::setAssignedServices($id, $serviceIds);
        }

        return $res;
    }

    public static function toggleActive(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE professionals SET active = IF(active = 1, 0, 1) WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
