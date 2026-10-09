<?php
/**
 * MIRACLE SPA - API: Eventos para el Calendario de Administración
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Models/Appointment.php';

$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-t', strtotime('+1 month'));

try {
    $rows = Appointment::getEventsForCalendar($start, $end);

    $events = [];
    foreach ($rows as $r) {
        // Color según estado
        $color = '#10b981'; // Confirmada (verde)
        if ($r['status'] === 'cancelada') {
            $color = '#ef4444'; // Roja
        } elseif ($r['status'] === 'completada') {
            $color = '#3b82f6'; // Azul
        } elseif ($r['status'] === 'reprogramada') {
            $color = '#8b5cf6'; // Violeta
        } elseif ($r['status'] === 'pendiente') {
            $color = '#f59e0b'; // Ámbar
        }

        $events[] = [
            'id' => (int)$r['id'],
            'title' => $r['service_name'] . ' - ' . $r['client_name'],
            'start' => $r['date'] . 'T' . $r['start_time'],
            'end' => $r['date'] . 'T' . $r['end_time'],
            'color' => $color,
            'extendedProps' => [
                'code' => $r['code'],
                'client_name' => $r['client_name'],
                'client_phone' => $r['client_phone'],
                'client_email' => $r['client_email'],
                'service_name' => $r['service_name'],
                'professional_name' => $r['professional_name'],
                'professional_id' => (int)$r['professional_id'],
                'price' => (float)$r['price'],
                'status' => $r['status'],
                'manage_token' => $r['manage_token']
            ]
        ];
    }

    echo json_encode($events);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar eventos: ' . $e->getMessage()]);
}
