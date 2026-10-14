<?php
/**
 * MIRACLE SPA - API: Procesamiento de Pago en Línea para Citas Existentes (Stripe)
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Models/Appointment.php';
require_once __DIR__ . '/../app/Services/StripePaymentService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Solo se acepta POST.']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$token = trim($data['token'] ?? '');
$code = trim($data['code'] ?? '');

$appointment = null;
if (!empty($token)) {
    $appointment = Appointment::findByManageToken($token);
} elseif (!empty($code)) {
    $appointment = Appointment::findByCode($code);
}

if (!$appointment) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'No se encontró la cita solicitada.']);
    exit;
}

if ($appointment['status'] === 'cancelada') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se puede procesar el pago de una cita cancelada.']);
    exit;
}

if ($appointment['payment_status'] === 'pagado') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Esta cita ya ha sido pagada previamente.']);
    exit;
}

try {
    $result = StripePaymentService::processAppointmentPayment((int)$appointment['id'], $data);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => '¡Pago completado con éxito a través de Stripe!',
            'payment' => $result,
            'appointment' => Appointment::findById((int)$appointment['id'])
        ]);
    } else {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'error' => $result['error'] ?? 'No se pudo procesar el pago con Stripe.'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno al procesar el pago: ' . $e->getMessage()
    ]);
}
