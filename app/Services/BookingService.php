<?php
/**
 * MIRACLE SPA - Servicio de Reservas y Gestión del Ciclo de Vida de Citas
 * Maneja transacciones atómicas, prevención de colisiones y notificaciones
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Client.php';
require_once __DIR__ . '/../Models/Service.php';
require_once __DIR__ . '/../Models/Professional.php';
require_once __DIR__ . '/../Models/Appointment.php';
require_once __DIR__ . '/AvailabilityService.php';
require_once __DIR__ . '/MailerService.php';

class BookingService {
    /**
     * Crea una nueva reserva de manera atómica con verificación contra dobles reservas
     */
    public static function createBooking(array $data): array {
        $db = Database::getConnection();

        // 1. Validar datos requeridos
        $clientName = trim($data['client_name'] ?? '');
        $clientEmail = trim($data['client_email'] ?? '');
        $clientPhone = trim($data['client_phone'] ?? '');
        $serviceId = (int)($data['service_id'] ?? 0);
        $date = trim($data['date'] ?? '');
        $startTime = trim($data['start_time'] ?? '');
        $requestedProfId = isset($data['professional_id']) && $data['professional_id'] !== 'any' ? (int)$data['professional_id'] : null;
        $notes = trim($data['client_notes'] ?? '');

        if (empty($clientName) || empty($clientEmail) || empty($clientPhone)) {
            return ['success' => false, 'error' => 'Por favor completa todos tus datos de contacto (nombre, correo y teléfono).'];
        }

        if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'El formato del correo electrónico no es válido.'];
        }

        $service = Service::findById($serviceId);
        if (!$service || !$service['active']) {
            return ['success' => false, 'error' => 'El servicio seleccionado no está disponible.'];
        }

        if (empty($date) || empty($startTime)) {
            return ['success' => false, 'error' => 'Debes seleccionar una fecha y hora válidas.'];
        }

        $duration = (int)$service['duration_minutes'];
        $endTime = AvailabilityService::calculateEndTime($startTime, $duration);
        $buffer = (int)AppConfig::getSetting('buffer_minutes', '15');

        // 2. Determinar profesional asignado
        $assignedProfId = null;

        if ($requestedProfId) {
            // Validar que el profesional esté calificado para este servicio
            $assignedServices = Professional::getAssignedServiceIds($requestedProfId);
            if (!in_array($serviceId, $assignedServices)) {
                return ['success' => false, 'error' => 'El profesional seleccionado no realiza este servicio.'];
            }

            $availCheck = AvailabilityService::isProfessionalAvailable($requestedProfId, $date, $startTime, $duration, $buffer);
            if (!$availCheck['available']) {
                return ['success' => false, 'error' => $availCheck['reason'] ?? 'El profesional no está disponible en este horario.'];
            }
            $assignedProfId = $requestedProfId;
        } else {
            // Auto-asignación inteligente: buscar profesionales calificados y libres
            $availableProfs = AvailabilityService::getAvailableProfessionalsForSlot($date, $startTime, $serviceId);
            if (empty($availableProfs)) {
                return ['success' => false, 'error' => 'Lo sentimos, no hay ningún especialista calificado disponible en ese horario. Por favor selecciona otro turno.'];
            }

            // Ordenar por calificación (más alta primero)
            usort($availableProfs, fn($a, $b) => $b['rating'] <=> $a['rating']);
            $assignedProfId = (int)$availableProfs[0]['id'];
        }

        // 3. Iniciar Transacción Atómica en Base de Datos
        $db->beginTransaction();

        try {
            // Verificación final con bloqueo de lectura para evitar condiciones de carrera (Race Conditions)
            $formattedStart = strlen($startTime) === 5 ? "{$startTime}:00" : $startTime;
            $formattedEnd = strlen($endTime) === 5 ? "{$endTime}:00" : $endTime;

            $lockSql = "SELECT id FROM appointments 
                        WHERE date = ? 
                          AND professional_id = ? 
                          AND status IN ('pendiente', 'confirmada', 'reprogramada')
                          AND (? < ADDTIME(end_time, SEC_TO_TIME(buffer_minutes * 60)) AND ? > start_time)
                        FOR UPDATE";
            $lockStmt = $db->prepare($lockSql);
            $lockStmt->execute([$date, $assignedProfId, $formattedStart, $formattedEnd]);

            if ($lockStmt->fetch()) {
                $db->rollBack();
                return ['success' => false, 'error' => 'El horario acaba de ser ocupado por otra persona. Por favor selecciona un nuevo turno.'];
            }

            // Crear o actualizar cliente
            $client = Client::findOrCreate($clientName, $clientEmail, $clientPhone, $notes);

            // Generar código único y token criptográfico seguro
            $code = 'MRC-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $manageToken = bin2hex(random_bytes(24));

            $insertSql = "INSERT INTO appointments 
                          (code, client_id, service_id, professional_id, date, start_time, end_time, duration_minutes, buffer_minutes, price, status, manage_token, client_notes)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmada', ?, ?)";
            $insertStmt = $db->prepare($insertSql);
            $insertStmt->execute([
                $code,
                $client['id'],
                $service['id'],
                $assignedProfId,
                $date,
                $formattedStart,
                $formattedEnd,
                $duration,
                $buffer,
                $service['price'],
                $manageToken,
                $notes
            ]);

            $appointmentId = (int)$db->lastInsertId();
            $db->commit();

            // Cargar datos completos para la notificación
            $appointment = Appointment::findById($appointmentId);

            // Enviar correo de confirmación
            MailerService::sendBookingConfirmation($appointment);

            return [
                'success' => true,
                'appointment' => $appointment,
                'code' => $code,
                'manage_token' => $manageToken
            ];
        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Error al procesar la reserva: ' . $e->getMessage()];
        }
    }

    /**
     * Reprograma una cita existente hacia una nueva fecha, horario o profesional
     */
    public static function rescheduleBooking(
        int $appointmentId,
        string $newDate,
        string $newStartTime,
        ?int $newProfessionalId = null,
        bool $isAdmin = false
    ): array {
        $db = Database::getConnection();
        $appointment = Appointment::findById($appointmentId);

        if (!$appointment) {
            return ['success' => false, 'error' => 'La cita no existe.'];
        }

        if ($appointment['status'] === 'cancelada') {
            return ['success' => false, 'error' => 'Una cita cancelada no puede ser reprogramada. Debes crear una nueva cita.'];
        }

        // Si es cliente, validar política de tiempo de anticipación
        if (!$isAdmin) {
            $policyCheck = AvailabilityService::canModifyAppointment($appointment);
            if (!$policyCheck['allowed']) {
                return ['success' => false, 'error' => $policyCheck['reason']];
            }
        }

        $serviceId = (int)$appointment['service_id'];
        $service = Service::findById($serviceId);
        $duration = (int)$appointment['duration_minutes'];
        $newEndTime = AvailabilityService::calculateEndTime($newStartTime, $duration);
        $buffer = (int)$appointment['buffer_minutes'];

        // Definir profesional
        $targetProfId = $newProfessionalId ?: (int)$appointment['professional_id'];

        $availCheck = AvailabilityService::isProfessionalAvailable(
            $targetProfId,
            $newDate,
            $newStartTime,
            $duration,
            $buffer,
            $appointmentId // Excluir la cita actual para que no colisione consigo misma
        );

        if (!$availCheck['available']) {
            return ['success' => false, 'error' => $availCheck['reason'] ?? 'El especialista no está disponible en el nuevo horario.'];
        }

        $db->beginTransaction();
        try {
            $oldDate = $appointment['date'];
            $oldTime = $appointment['start_time'];

            $formattedStart = strlen($newStartTime) === 5 ? "{$newStartTime}:00" : $newStartTime;
            $formattedEnd = strlen($newEndTime) === 5 ? "{$newEndTime}:00" : $newEndTime;

            $updateSql = "UPDATE appointments 
                          SET date = ?, 
                              start_time = ?, 
                              end_time = ?, 
                              professional_id = ?, 
                              status = 'reprogramada', 
                              rescheduled_at = NOW() 
                          WHERE id = ?";
            $stmt = $db->prepare($updateSql);
            $stmt->execute([$newDate, $formattedStart, $formattedEnd, $targetProfId, $appointmentId]);

            $db->commit();

            $updatedAppointment = Appointment::findById($appointmentId);
            MailerService::sendRescheduleConfirmation($updatedAppointment, $oldDate, $oldTime);

            return ['success' => true, 'appointment' => $updatedAppointment];
        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'error' => 'Error al reprogramar la cita: ' . $e->getMessage()];
        }
    }

    /**
     * Cancela una cita registrando el motivo y notificando al cliente
     */
    public static function cancelBooking(int $appointmentId, string $reason = '', bool $isAdmin = false): array {
        $appointment = Appointment::findById($appointmentId);

        if (!$appointment) {
            return ['success' => false, 'error' => 'La cita no existe.'];
        }

        if ($appointment['status'] === 'cancelada') {
            return ['success' => false, 'error' => 'La cita ya se encuentra cancelada.'];
        }

        // Si es cliente, validar política de tiempo de anticipación
        if (!$isAdmin) {
            $policyCheck = AvailabilityService::canModifyAppointment($appointment);
            if (!$policyCheck['allowed']) {
                return ['success' => false, 'error' => $policyCheck['reason']];
            }
        }

        $res = Appointment::updateStatus($appointmentId, 'cancelada', $reason);
        if ($res) {
            $updatedAppointment = Appointment::findById($appointmentId);
            MailerService::sendCancellationConfirmation($updatedAppointment, $reason);
            return ['success' => true, 'appointment' => $updatedAppointment];
        }

        return ['success' => false, 'error' => 'No se pudo cancelar la cita.'];
    }
}
