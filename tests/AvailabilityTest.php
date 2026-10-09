<?php
/**
 * MIRACLE SPA - Suite de Pruebas Unitarias para Disponibilidad y Horarios
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Services/AvailabilityService.php';

class AvailabilityTest {
    private array $results = [];

    public function runAll(): array {
        $this->testTimeConversions();
        $this->testCalculateEndTime();
        $this->testIntervalOverlapWithoutBuffer();
        $this->testIntervalOverlapWithBuffer();
        $this->testNonOverlappingIntervals();
        $this->testCancellationPolicyEnforcement();
        return $this->results;
    }

    private function assert(string $testName, bool $condition, string $message = ''): void {
        $this->results[] = [
            'suite' => 'Disponibilidad y Lógica Temporal',
            'test' => $testName,
            'passed' => $condition,
            'message' => $message
        ];
    }

    private function testTimeConversions(): void {
        $mins1 = AvailabilityService::timeToMinutes('09:30');
        $this->assert('Conversión de 09:30 a minutos debe ser 570', $mins1 === 570);

        $timeStr = AvailabilityService::minutesToTime(570);
        $this->assert('Conversión de 570 minutos debe ser 09:30', $timeStr === '09:30');

        $minsMidnight = AvailabilityService::timeToMinutes('00:00');
        $this->assert('Medianoche 00:00 debe ser 0 minutos', $minsMidnight === 0);
    }

    private function testCalculateEndTime(): void {
        // Servicio de 60 mins que inicia a las 10:00
        $end1 = AvailabilityService::calculateEndTime('10:00', 60);
        $this->assert('Servicio de 60 min desde 10:00 debe finalizar a 11:00', $end1 === '11:00');

        // Servicio de 45 mins que inicia a las 11:30
        $end2 = AvailabilityService::calculateEndTime('11:30', 45);
        $this->assert('Servicio de 45 min desde 11:30 debe finalizar a 12:15', $end2 === '12:15');

        // Servicio de 90 mins que inicia a las 17:45
        $end3 = AvailabilityService::calculateEndTime('17:45', 90);
        $this->assert('Servicio de 90 min desde 17:45 debe finalizar a 19:15', $end3 === '19:15');
    }

    private function testIntervalOverlapWithoutBuffer(): void {
        // [600, 660] (10:00 - 11:00) y [630, 690] (10:30 - 11:30)
        $overlap = AvailabilityService::doIntervalsOverlap(630, 690, 600, 660, 0);
        $this->assert('Intervalos solapados en 30 min deben colisionar', $overlap === true);

        // Turno exactamente contiguo sin buffer [660, 720] vs [600, 660]
        $contiguous = AvailabilityService::doIntervalsOverlap(660, 720, 600, 660, 0);
        $this->assert('Turnos exactamente contiguos sin buffer no deben colisionar', $contiguous === false);
    }

    private function testIntervalOverlapWithBuffer(): void {
        // Turno A de 10:00 a 11:00 (600 a 660). Con buffer de 15 min, el especialista se libera a las 11:15 (675).
        // Si el cliente pide a las 11:00 (660) a 12:00 (720), debe detectar colisión con el buffer de higienización.
        $collisionWithBuffer = AvailabilityService::doIntervalsOverlap(660, 720, 600, 660, 15);
        $this->assert('Nuevo turno que invade el buffer de 15 min debe ser rechazado', $collisionWithBuffer === true);

        // Si el cliente pide a las 11:15 (675), el buffer ya concluyó y debe estar disponible
        $freeAfterBuffer = AvailabilityService::doIntervalsOverlap(675, 735, 600, 660, 15);
        $this->assert('Nuevo turno posterior al buffer de 15 min debe permitirse', $freeAfterBuffer === false);
    }

    private function testNonOverlappingIntervals(): void {
        // Cita 1: 09:00 - 10:00 (540 a 600).
        // Cita 2: 15:00 - 16:00 (900 a 960).
        $overlap = AvailabilityService::doIntervalsOverlap(900, 960, 540, 600, 15);
        $this->assert('Citas distantes en la misma jornada no colisionan', $overlap === false);
    }

    private function testCancellationPolicyEnforcement(): void {
        // Cita dentro de 2 horas (política requiere 4 horas)
        $soonDate = date('Y-m-d', strtotime('+2 hours'));
        $soonTime = date('H:i:s', strtotime('+2 hours'));
        $appointmentTooSoon = [
            'date' => $soonDate,
            'start_time' => $soonTime
        ];

        $check1 = AvailabilityService::canModifyAppointment($appointmentTooSoon, 4);
        $this->assert('Cancelación con solo 2 horas de anticipación debe ser denegada por política de 4h', $check1['allowed'] === false);

        // Cita dentro de 48 horas
        $futureDate = date('Y-m-d', strtotime('+48 hours'));
        $futureTime = '14:00:00';
        $appointmentFar = [
            'date' => $futureDate,
            'start_time' => $futureTime
        ];

        $check2 = AvailabilityService::canModifyAppointment($appointmentFar, 4);
        $this->assert('Cancelación con 48 horas de anticipación debe ser permitida', $check2['allowed'] === true);
    }
}
