<?php
/**
 * MIRACLE SPA - API: Creación de Cita / Reserva
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Services/BookingService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

try {
    $result = BookingService::createBooking($data);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => '¡Tu cita ha sido reservada con éxito!',
            'code' => $result['code'],
            'manage_token' => $result['manage_token'],
            'appointment' => $result['appointment']
        ]);
    } else {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'error' => $result['error'] ?? 'No fue posible completar la reserva.'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno en el servidor: ' . $e->getMessage()
    ]);
}
