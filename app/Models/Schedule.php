<?php
/**
 * MIRACLE SPA - Modelo Schedule
 */

require_once __DIR__ . '/../../config/database.php';

class Schedule {
    public static function getByProfessional(int $professionalId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM schedules WHERE professional_id = ? ORDER BY day_of_week ASC");
        $stmt->execute([$professionalId]);
        return $stmt->fetchAll();
    }

    public static function getByProfessionalAndDay(int $professionalId, int $dayOfWeek): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM schedules WHERE professional_id = ? AND day_of_week = ? LIMIT 1");
        $stmt->execute([$professionalId, $dayOfWeek]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function updateDaySchedule(
        int $professionalId,
        int $dayOfWeek,
        string $startTime,
        string $endTime,
        ?string $lunchStart,
        ?string $lunchEnd,
        bool $isOff
    ): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO schedules (professional_id, day_of_week, start_time, end_time, lunch_start, lunch_end, is_off)
                              VALUES (?, ?, ?, ?, ?, ?, ?)
                              ON DUPLICATE KEY UPDATE 
                                start_time = VALUES(start_time),
                                end_time = VALUES(end_time),
                                lunch_start = VALUES(lunch_start),
                                lunch_end = VALUES(lunch_end),
                                is_off = VALUES(is_off)");
        return $stmt->execute([
            $professionalId,
            $dayOfWeek,
            $startTime,
            $endTime,
            $lunchStart ?: null,
            $lunchEnd ?: null,
            $isOff ? 1 : 0
        ]);
    }
}
