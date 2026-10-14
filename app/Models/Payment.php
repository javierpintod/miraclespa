<?php
/**
 * MIRACLE SPA - Modelo de Pagos & Transacciones en Línea (Payment)
 */

require_once __DIR__ . '/../../config/database.php';

class Payment {
    /**
     * Registra una nueva transacción de pago
     */
    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO `payments` (
                `appointment_id`,
                `transaction_id`,
                `amount`,
                `currency`,
                `gateway`,
                `payment_method`,
                `status`,
                `card_brand`,
                `card_last4`,
                `receipt_url`,
                `customer_name`,
                `customer_email`,
                `metadata_json`,
                `paid_at`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['appointment_id'],
            $data['transaction_id'],
            $data['amount'],
            $data['currency'] ?? 'USD',
            $data['gateway'] ?? 'stripe',
            $data['payment_method'] ?? 'card',
            $data['status'] ?? 'completado',
            $data['card_brand'] ?? null,
            $data['card_last4'] ?? null,
            $data['receipt_url'] ?? null,
            $data['customer_name'] ?? null,
            $data['customer_email'] ?? null,
            isset($data['metadata_json']) 
                ? (is_array($data['metadata_json']) ? json_encode($data['metadata_json']) : $data['metadata_json']) 
                : (isset($data['gateway_response']) ? json_encode($data['gateway_response']) : null),
            $data['paid_at'] ?? date('Y-m-d H:i:s')
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Busca un pago por ID
     */
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.*, a.code AS appointment_code, a.date AS appointment_date, 
                                     s.name AS service_name, c.name AS client_name, c.email AS client_email
                              FROM `payments` p
                              JOIN `appointments` a ON a.id = p.appointment_id
                              JOIN `services` s ON s.id = a.service_id
                              JOIN `clients` c ON c.id = a.client_id
                              WHERE p.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Busca un pago por ID de transacción (ej. pi_...)
     */
    public static function findByTransactionId(string $transactionId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `payments` WHERE `transaction_id` = ?");
        $stmt->execute([trim($transactionId)]);
        $row = $stmt->fetch();
        if ($row) {
            $decoded = !empty($row['metadata_json']) ? json_decode($row['metadata_json'], true) : [];
            $row['metadata'] = $decoded;
            $row['gateway_response'] = $decoded;
        }
        return $row ?: null;
    }

    /**
     * Obtiene el pago más reciente asociado a una cita
     */
    public static function findByAppointmentId(int $appointmentId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `payments` WHERE `appointment_id` = ? ORDER BY `id` DESC LIMIT 1");
        $stmt->execute([$appointmentId]);
        $row = $stmt->fetch();
        if ($row) {
            $decoded = !empty($row['metadata_json']) ? json_decode($row['metadata_json'], true) : [];
            $row['metadata'] = $decoded;
            $row['gateway_response'] = $decoded;
        }
        return $row ?: null;
    }

    /**
     * Actualiza el estado de un pago
     */
    public static function updateStatus(int $id, string $status, ?array $metadata = null): bool {
        $db = Database::getConnection();
        if ($metadata !== null) {
            $stmt = $db->prepare("UPDATE `payments` SET `status` = ?, `metadata_json` = ? WHERE `id` = ?");
            return $stmt->execute([$status, json_encode($metadata), $id]);
        }
        $stmt = $db->prepare("UPDATE `payments` SET `status` = ? WHERE `id` = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Lista todos los pagos con filtros
     */
    public static function getAll(array $filters = []): array {
        $db = Database::getConnection();
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "p.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['gateway'])) {
            $where[] = "p.gateway = ?";
            $params[] = $filters['gateway'];
        }
        if (!empty($filters['search'])) {
            $term = "%" . trim($filters['search']) . "%";
            $where[] = "(p.transaction_id LIKE ? OR p.customer_name LIKE ? OR p.customer_email LIKE ? OR a.code LIKE ?)";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $whereClause = implode(" AND ", $where);
        $sql = "SELECT p.*, a.code AS appointment_code, a.date AS appointment_date,
                       s.name AS service_name, c.name AS client_name, c.email AS client_email
                FROM `payments` p
                JOIN `appointments` a ON a.id = p.appointment_id
                JOIN `services` s ON s.id = a.service_id
                JOIN `clients` c ON c.id = a.client_id
                WHERE {$whereClause}
                ORDER BY p.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
