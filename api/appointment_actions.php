<?php
/**
 * MIRACLE SPA - API: Acciones sobre Citas (Cancelar, Reprogramar, Cambiar Estado)
 */

header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Helpers/AuthHelper.php';
require_once __DIR__ . '/../app/Services/BookingService.php';
require_once __DIR__ . '/../app/Services/MailerService.php';

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

$action = $data['action'] ?? '';
$token = $data['token'] ?? null;
$isAdmin = AuthHelper::isLoggedIn();

// Si no es admin, se requiere token de gestión
if (!$isAdmin && empty($token)) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado. Se requiere token de gestión válido.']);
    exit;
}

$appointment = null;
if (!empty($token)) {
    $appointment = Appointment::findByManageToken($token);
} elseif (!empty($data['appointment_id'])) {
    $appointment = Appointment::findById((int)$data['appointment_id']);
}

if (!$appointment) {
    http_response_code(404);
    echo json_encode(['error' => 'Cita no encontrada.']);
    exit;
}

try {
    switch ($action) {
        case 'cancel':
            $reason = trim($data['reason'] ?? 'Cancelada por el cliente.');
            $res = BookingService::cancelBooking((int)$appointment['id'], $reason, $isAdmin);
            if ($res['success']) {
                echo json_encode(['success' => true, 'message' => 'La cita ha sido cancelada satisfactoriamente.']);
            } else {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => $res['error']]);
            }
            break;

        case 'reschedule':
            $newDate = trim($data['new_date'] ?? '');
            $newTime = trim($data['new_time'] ?? '');
            $newProfId = !empty($data['new_professional_id']) && $data['new_professional_id'] !== 'any' ? (int)$data['new_professional_id'] : null;

            if (empty($newDate) || empty($newTime)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'Debes especificar una nueva fecha y horario.']);
                exit;
            }

            $res = BookingService::rescheduleBooking(
                (int)$appointment['id'],
                $newDate,
                $newTime,
                $newProfId,
                $isAdmin
            );

            if ($res['success']) {
                echo json_encode(['success' => true, 'message' => 'La cita ha sido reprogramada con éxito.', 'appointment' => $res['appointment']]);
            } else {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => $res['error']]);
            }
            break;

        case 'update_status':
            if (!$isAdmin) {
                http_response_code(403);
                echo json_encode(['error' => 'Solo los administradores pueden cambiar el estado manualmente.']);
                exit;
            }
            $status = $data['status'] ?? '';
            $valid = ['confirmada', 'completada', 'cancelada', 'pendiente', 'reprogramada'];
            if (!in_array($status, $valid)) {
                http_response_code(422);
                echo json_encode(['error' => 'Estado inválido.']);
                exit;
            }
            Appointment::updateStatus((int)$appointment['id'], $status);
            echo json_encode(['success' => true, 'message' => 'Estado actualizado exitosamente.']);
            break;

        case 'resend_email':
            if (!$isAdmin) {
                http_response_code(403);
                echo json_encode(['error' => 'Acción exclusiva para administradores.']);
                exit;
            }
            $mailRes = MailerService::sendBookingConfirmation($appointment);
            echo json_encode(['success' => true, 'message' => 'Correo de confirmación reenviado.', 'mail_status' => $mailRes['status']]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no reconocida.']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al procesar la acción: ' . $e->getMessage()]);
}
