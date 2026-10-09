<?php
/**
 * MIRACLE SPA - Modelo EmailLog
 */

require_once __DIR__ . '/../../config/database.php';

class EmailLog {
    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO email_logs 
                              (appointment_id, recipient_email, recipient_name, subject, email_type, status, body_html, error_message)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['appointment_id'] ?? null,
            trim($data['recipient_email']),
            trim($data['recipient_name'] ?? ''),
            trim($data['subject']),
            $data['email_type'] ?? 'confirmacion',
            $data['status'] ?? 'simulado',
            $data['body_html'],
            $data['error_message'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }

    public static function getAll(int $limit = 100): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT el.*, a.code AS appointment_code
                              FROM email_logs el
                              LEFT JOIN appointments a ON a.id = el.appointment_id
                              ORDER BY el.sent_at DESC
                              LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM email_logs WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
