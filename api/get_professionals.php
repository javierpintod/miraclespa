<?php
/**
 * MIRACLE SPA - API: Obtención de Profesionales Disponibles para un Servicio y Horario
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Models/Service.php';
require_once __DIR__ . '/../app/Services/AvailabilityService.php';

$serviceId = (int)($_GET['service_id'] ?? 0);
$date = $_GET['date'] ?? null;
$time = $_GET['time'] ?? null;

if ($serviceId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Se requiere service_id válido.']);
    exit;
}

try {
    $allQualified = Service::getQualifiedProfessionals($serviceId);

    if ($date && $time) {
        // Filtrar solo los que están libres en este horario exacto
        $availableForSlot = AvailabilityService::getAvailableProfessionalsForSlot($date, $time, $serviceId);
        $availableIds = array_column($availableForSlot, 'id');

        $result = array_map(function($p) use ($availableIds) {
            $p['is_available_for_slot'] = in_array($p['id'], $availableIds);
            return $p;
        }, $allQualified);
    } else {
        $result = $allQualified;
    }

    echo json_encode([
        'service_id' => $serviceId,
        'professionals' => $result
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener especialistas: ' . $e->getMessage()]);
}
