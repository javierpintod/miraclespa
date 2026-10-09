<?php
/**
 * MIRACLE SPA - Suite de Pruebas de Integración para Reservas y Anticolisión
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Services/BookingService.php';
require_once __DIR__ . '/../app/Services/AvailabilityService.php';
require_once __DIR__ . '/../app/Models/Appointment.php';

class BookingTest {
    private array $results = [];
    private array $cleanupAppointmentIds = [];

    public function runAll(): array {
        try {
            $this->testSuccessfulBookingCreation();
            $this->testDoubleBookingRejection();
            $this->testParallelBookingDifferentProfessionals();
            $this->testReschedulingConflictHandling();
            $this->testSlotFreedAfterCancellation();
        } finally {
            $this->cleanup();
        }

        return $this->results;
    }

    private function assert(string $testName, bool $condition, string $message = ''): void {
        $this->results[] = [
            'suite' => 'Integración de Reservas y Anticolisión',
            'test' => $testName,
            'passed' => $condition,
            'message' => $message
        ];
    }

    private function testSuccessfulBookingCreation(): void {
        // Reservar servicio 1 (Manicura) con Profesional 2 (Camila Morales) en una fecha futura
        $targetDate = date('Y-m-d', strtotime('+14 days'));
        
        $bookingData = [
            'client_name' => 'Prueba Integración Alpha',
            'client_email' => 'test.alpha@example.com',
            'client_phone' => '+15559990001',
            'service_id' => 1, // 50 min
            'professional_id' => 2,
            'date' => $targetDate,
            'start_time' => '10:00',
            'client_notes' => 'Prueba unitaria automatizada'
        ];

        $res = BookingService::createBooking($bookingData);
        $this->assert('Creación exitosa de reserva atómica', $res['success'] === true, $res['error'] ?? '');

        if ($res['success']) {
            $this->cleanupAppointmentIds[] = (int)$res['appointment']['id'];
            $this->assert('Generación de código único MRC-XXXX', str_starts_with($res['code'], 'MRC-'));
            $this->assert('Generación de token de gestión seguro', strlen($res['manage_token']) >= 32);
        }
    }

    private function testDoubleBookingRejection(): void {
        // Intentar reservar exactamente el mismo especialista en la misma fecha y horario solapado
        $targetDate = date('Y-m-d', strtotime('+14 days'));

        $duplicateData = [
            'client_name' => 'Cliente Solapado Beta',
            'client_email' => 'test.beta@example.com',
            'client_phone' => '+15559990002',
            'service_id' => 1,
            'professional_id' => 2,
            'date' => $targetDate,
            'start_time' => '10:20', // Empieza mientras Camila aún atiende a Alpha (10:00 a 10:50)
            'client_notes' => 'Intento de doble reserva'
        ];

        $res = BookingService::createBooking($duplicateData);
        $this->assert('Rechazo estricto de doble reserva para el mismo especialista', $res['success'] === false);
        if ($res['success']) {
            $this->cleanupAppointmentIds[] = (int)$res['appointment']['id'];
        }
    }

    private function testParallelBookingDifferentProfessionals(): void {
        // Reservar simultáneamente a las 10:00 pero con otro especialista (ej. David Mendoza o Sofía Carvajal)
        $targetDate = date('Y-m-d', strtotime('+14 days'));

        $parallelData = [
            'client_name' => 'Cliente Paralelo Gamma',
            'client_email' => 'test.gamma@example.com',
            'client_phone' => '+15559990003',
            'service_id' => 11, // Masaje Relajante con David Mendoza (profesional 3)
            'professional_id' => 3,
            'date' => $targetDate,
            'start_time' => '10:00',
            'client_notes' => 'Cita paralela simultánea'
        ];

        $res = BookingService::createBooking($parallelData);
        $this->assert('Permitir reservas simultáneas en cabinas distintas con especialistas diferentes', $res['success'] === true, $res['error'] ?? '');
        if ($res['success']) {
            $this->cleanupAppointmentIds[] = (int)$res['appointment']['id'];
        }
    }

    private function testReschedulingConflictHandling(): void {
        // Tomar la primera cita y reprogramarla a una tarde libre
        if (empty($this->cleanupAppointmentIds)) return;

        $appId = $this->cleanupAppointmentIds[0];
        $targetDate = date('Y-m-d', strtotime('+15 days'));

        $res = BookingService::rescheduleBooking($appId, $targetDate, '16:00', 2, true);
        $this->assert('Reprogramación exitosa hacia un horario libre', $res['success'] === true, $res['error'] ?? '');
    }

    private function testSlotFreedAfterCancellation(): void {
        // Cancelar la cita y verificar que el slot anterior vuelve a quedar 100% disponible
        if (empty($this->cleanupAppointmentIds)) return;

        $appId = $this->cleanupAppointmentIds[0];
        $resCancel = BookingService::cancelBooking($appId, 'Cancelación de prueba automatizada', true);
        $this->assert('Cancelación de cita ejecutada correctamente', $resCancel['success'] === true, $resCancel['error'] ?? '');

        // Verificar que el especialista ahora figura libre
        $targetDate = date('Y-m-d', strtotime('+15 days'));
        $avail = AvailabilityService::isProfessionalAvailable(2, $targetDate, '16:00', 50);
        $this->assert('El horario queda liberado para nuevos clientes tras la cancelación', $avail['available'] === true);
    }

    private function cleanup(): void {
        if (!empty($this->cleanupAppointmentIds)) {
            $db = Database::getConnection();
            $inClause = implode(',', array_map('intval', $this->cleanupAppointmentIds));
            $db->exec("DELETE FROM email_logs WHERE appointment_id IN ({$inClause})");
            $db->exec("DELETE FROM appointments WHERE id IN ({$inClause})");
            $db->exec("DELETE FROM clients WHERE email IN ('test.alpha@example.com', 'test.beta@example.com', 'test.gamma@example.com')");
        }
    }
}
