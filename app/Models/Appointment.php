<?php
/**
 * MIRACLE SPA - Modelo Appointment
 */

require_once __DIR__ . '/../../config/database.php';

class Appointment {
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT a.*, 
                                     c.name AS client_name, c.email AS client_email, c.phone AS client_phone,
                                     s.name AS service_name, s.duration_minutes AS service_duration,
                                     cat.name AS category_name, cat.slug AS category_slug,
                                     p.name AS professional_name, p.title AS professional_title, p.avatar AS professional_avatar
                              FROM appointments a
                              JOIN clients c ON c.id = a.client_id
                              JOIN services s ON s.id = a.service_id
                              JOIN categories cat ON cat.id = s.category_id
                              JOIN professionals p ON p.id = a.professional_id
                              WHERE a.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByCode(string $code): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT a.*, 
                                     c.name AS client_name, c.email AS client_email, c.phone AS client_phone,
                                     s.name AS service_name, s.duration_minutes AS service_duration,
                                     cat.name AS category_name, cat.slug AS category_slug,
                                     p.name AS professional_name, p.title AS professional_title, p.avatar AS professional_avatar
                              FROM appointments a
                              JOIN clients c ON c.id = a.client_id
                              JOIN services s ON s.id = a.service_id
                              JOIN categories cat ON cat.id = s.category_id
                              JOIN professionals p ON p.id = a.professional_id
                              WHERE UPPER(a.code) = UPPER(?)");
        $stmt->execute([trim($code)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByManageToken(string $token): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT a.*, 
                                     c.name AS client_name, c.email AS client_email, c.phone AS client_phone,
                                     s.name AS service_name, s.duration_minutes AS service_duration,
                                     cat.name AS category_name, cat.slug AS category_slug,
                                     p.name AS professional_name, p.title AS professional_title, p.avatar AS professional_avatar
                              FROM appointments a
                              JOIN clients c ON c.id = a.client_id
                              JOIN services s ON s.id = a.service_id
                              JOIN categories cat ON cat.id = s.category_id
                              JOIN professionals p ON p.id = a.professional_id
                              WHERE a.manage_token = ?");
        $stmt->execute([trim($token)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getAll(array $filters = []): array {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['date'])) {
            $where[] = "a.date = ?";
            $params[] = $filters['date'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "a.date >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "a.date <= ?";
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['status'])) {
            $where[] = "a.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['professional_id'])) {
            $where[] = "a.professional_id = ?";
            $params[] = (int)$filters['professional_id'];
        }
        if (!empty($filters['service_id'])) {
            $where[] = "a.service_id = ?";
            $params[] = (int)$filters['service_id'];
        }
        if (!empty($filters['search'])) {
            $term = "%" . trim($filters['search']) . "%";
            $where[] = "(a.code LIKE ? OR c.name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?)";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $whereClause = implode(" AND ", $where);
        $sql = "SELECT a.*, 
                       c.name AS client_name, c.email AS client_email, c.phone AS client_phone,
                       s.name AS service_name, s.duration_minutes AS service_duration,
                       p.name AS professional_name
                FROM appointments a
                JOIN clients c ON c.id = a.client_id
                JOIN services s ON s.id = a.service_id
                JOIN professionals p ON p.id = a.professional_id
                WHERE {$whereClause}
                ORDER BY a.date DESC, a.start_time DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getEventsForCalendar(string $startDate, string $endDate): array {
        $db = Database::getConnection();
        $sql = "SELECT a.id, a.code, a.date, a.start_time, a.end_time, a.status, a.price, a.manage_token,
                       c.name AS client_name, c.email AS client_email, c.phone AS client_phone,
                       s.name AS service_name, s.duration_minutes,
                       p.name AS professional_name, p.id AS professional_id
                FROM appointments a
                JOIN clients c ON c.id = a.client_id
                JOIN services s ON s.id = a.service_id
                JOIN professionals p ON p.id = a.professional_id
                WHERE a.date BETWEEN ? AND ?
                ORDER BY a.date ASC, a.start_time ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }

    public static function getActiveCollisions(
        string $date,
        int $professionalId,
        string $startTime,
        string $endTime,
        int $bufferMinutes = 15,
        ?int $excludeAppointmentId = null
    ): array {
        $db = Database::getConnection();
        // Dos intervalos [S1, E1+buffer] y [S2, E2] colisionan si: S1 < (E2+buffer) Y E1 > S2
        // Excluimos las canceladas
        $sql = "SELECT a.*, c.name AS client_name, s.name AS service_name
                FROM appointments a
                JOIN clients c ON c.id = a.client_id
                JOIN services s ON s.id = a.service_id
                WHERE a.date = ?
                  AND a.professional_id = ?
                  AND a.status IN ('pendiente', 'confirmada', 'reprogramada')
                  AND (? < ADDTIME(a.end_time, SEC_TO_TIME(a.buffer_minutes * 60)) AND ? > a.start_time)";

        $params = [$date, $professionalId, $startTime, $endTime];

        if ($excludeAppointmentId) {
            $sql .= " AND a.id != ?";
            $params[] = $excludeAppointmentId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $id, string $status, ?string $reason = null): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE appointments 
                              SET status = ?, 
                                  cancellation_reason = COALESCE(?, cancellation_reason),
                                  cancelled_at = IF(? = 'cancelada', NOW(), cancelled_at)
                              WHERE id = ?");
        return $stmt->execute([$status, $reason, $status, $id]);
    }
}
