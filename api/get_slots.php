<?php
/**
 * MIRACLE SPA - API: Obtención de Horarios Disponibles
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Services/AvailabilityService.php';

$date = $_GET['date'] ?? '';
$serviceId = (int)($_GET['service_id'] ?? 0);
$profId = isset($_GET['professional_id']) && $_GET['professional_id'] !== 'any' ? (int)$_GET['professional_id'] : null;

if (empty($date) || $serviceId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Se requiere fecha (date) y servicio (service_id) válidos.']);
    exit;
}

// Validar que la fecha no sea en el pasado
$today = date('Y-m-d');
if ($date < $today) {
    echo json_encode(['slots' => [], 'message' => 'No se pueden agendar citas en fechas anteriores a hoy.']);
    exit;
}

try {
    $slots = AvailabilityService::getSlotsForDate($date, $serviceId, $profId);
    echo json_encode([
        'date' => $date,
        'service_id' => $serviceId,
        'professional_id' => $profId,
        'total_slots' => count($slots),
        'slots' => $slots
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al calcular disponibilidad: ' . $e->getMessage()]);
}
