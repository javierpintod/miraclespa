<?php
/**
 * MIRACLE SPA - Motor de Disponibilidad y Gestión de Horarios
 * Previene dobles reservas, valida buffers de higienización y turnos de trabajo
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../Models/Service.php';
require_once __DIR__ . '/../Models/Professional.php';
require_once __DIR__ . '/../Models/Schedule.php';
require_once __DIR__ . '/../Models/Appointment.php';

class AvailabilityService {
    public const DEFAULT_BUFFER_MINUTES = 15;
    public const DEFAULT_MIN_CANCEL_HOURS = 4;

    /**
     * Convierte cadena HH:MM o HH:MM:SS a minutos totales del día
     */
    public static function timeToMinutes(string $timeStr): int {
        $parts = explode(':', $timeStr);
        $hours = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);
        return ($hours * 60) + $minutes;
    }

    /**
     * Convierte minutos totales del día a formato HH:MM
     */
    public static function minutesToTime(int $totalMinutes): string {
        $hours = (int)floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Calcula la hora final a partir de la hora de inicio y duración en minutos
     */
    public static function calculateEndTime(string $startTime, int $durationMinutes): string {
        $startMins = self::timeToMinutes($startTime);
        $endMins = $startMins + $durationMinutes;
        return self::minutesToTime($endMins);
    }

    /**
     * Determina si dos intervalos de tiempo se solapan, considerando el buffer de higienización del intervalo existente.
     * Propuesto: [startA, endA]
     * Existente:  [startB, endB + bufferB]
     */
    public static function doIntervalsOverlap(
        int $startA,
        int $endA,
        int $startB,
        int $endB,
        int $bufferB = self::DEFAULT_BUFFER_MINUTES
    ): bool {
        $effectiveEndB = $endB + $bufferB;
        return ($startA < $effectiveEndB) && ($endA > $startB);
    }

    /**
     * Valida si un profesional específico está libre y en horario laboral para un turno propuesto.
     */
    public static function isProfessionalAvailable(
        int $professionalId,
        string $date,
        string $startTime,
        int $durationMinutes,
        int $bufferMinutes = self::DEFAULT_BUFFER_MINUTES,
        ?int $excludeAppointmentId = null
    ): array {
        $prof = Professional::findById($professionalId);
        if (!$prof || !$prof['active']) {
            return ['available' => false, 'reason' => 'El profesional no está activo o no existe.'];
        }

        // Obtener día de la semana (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
        $dayOfWeek = (int)date('w', strtotime($date));

        // Obtener horario laboral del profesional
        $schedule = Schedule::getByProfessionalAndDay($professionalId, $dayOfWeek);
        if (!$schedule || $schedule['is_off']) {
            return ['available' => false, 'reason' => "{$prof['name']} no labora en este día."];
        }

        $proposedStart = self::timeToMinutes($startTime);
        $proposedEnd = $proposedStart + $durationMinutes;

        $workStart = self::timeToMinutes($schedule['start_time']);
        $workEnd = self::timeToMinutes($schedule['end_time']);

        // Verificar límites de jornada
        if ($proposedStart < $workStart || $proposedEnd > $workEnd) {
            return [
                'available' => false, 
                'reason' => "El horario solicitado ($startTime a " . self::minutesToTime($proposedEnd) . ") excede la jornada de {$prof['name']} (" . substr($schedule['start_time'], 0, 5) . " - " . substr($schedule['end_time'], 0, 5) . ")."
            ];
        }

        // Verificar receso de almuerzo
        if (!empty($schedule['lunch_start']) && !empty($schedule['lunch_end'])) {
            $lunchStart = self::timeToMinutes($schedule['lunch_start']);
            $lunchEnd = self::timeToMinutes($schedule['lunch_end']);

            if (self::doIntervalsOverlap($proposedStart, $proposedEnd, $lunchStart, $lunchEnd, 0)) {
                return ['available' => false, 'reason' => "{$prof['name']} se encuentra en su horario de descanso/almuerzo."];
            }
        }

        // Verificar colisión con citas existentes
        $formattedStartTime = strlen($startTime) === 5 ? "{$startTime}:00" : $startTime;
        $endTime = self::calculateEndTime($startTime, $durationMinutes);
        $formattedEndTime = strlen($endTime) === 5 ? "{$endTime}:00" : $endTime;

        $collisions = Appointment::getActiveCollisions(
            $date,
            $professionalId,
            $formattedStartTime,
            $formattedEndTime,
            $bufferMinutes,
            $excludeAppointmentId
        );

        if (!empty($collisions)) {
            $conflict = $collisions[0];
            $occupiedSlot = substr($conflict['start_time'], 0, 5) . ' a ' . substr($conflict['end_time'], 0, 5);
            return [
                'available' => false, 
                'reason' => "{$prof['name']} tiene una cita reservada ({$occupiedSlot}) más el tiempo de preparación."
            ];
        }

        return ['available' => true, 'professional' => $prof];
    }

    /**
     * Retorna todos los profesionales disponibles para un servicio y un slot específico.
     */
    public static function getAvailableProfessionalsForSlot(
        string $date,
        string $startTime,
        int $serviceId,
        ?int $excludeAppointmentId = null
    ): array {
        $service = Service::findById($serviceId);
        if (!$service) return [];

        $qualified = Service::getQualifiedProfessionals($serviceId);
        $buffer = (int)AppConfig::getSetting('buffer_minutes', (string)self::DEFAULT_BUFFER_MINUTES);
        $available = [];

        foreach ($qualified as $prof) {
            $check = self::isProfessionalAvailable(
                (int)$prof['id'],
                $date,
                $startTime,
                (int)$service['duration_minutes'],
                $buffer,
                $excludeAppointmentId
            );
            if ($check['available']) {
                $available[] = $prof;
            }
        }

        return $available;
    }

    /**
     * Genera la lista completa de horarios (slots) disponibles para una fecha y servicio dado.
     * Si se pasa un professionalId específico, filtra solo para ese profesional.
     * Si no, evalúa si al menos un profesional calificado está libre en cada slot.
     */
    public static function getSlotsForDate(
        string $date,
        int $serviceId,
        ?int $professionalId = null,
        ?int $excludeAppointmentId = null
    ): array {
        $service = Service::findById($serviceId);
        if (!$service) return [];

        $duration = (int)$service['duration_minutes'];
        $opening = AppConfig::getSetting('opening_time', '09:00:00');
        $closing = AppConfig::getSetting('closing_time', '20:00:00');
        $interval = (int)AppConfig::getSetting('slot_interval_minutes', '30');
        $buffer = (int)AppConfig::getSetting('buffer_minutes', (string)self::DEFAULT_BUFFER_MINUTES);

        $startMin = self::timeToMinutes($opening);
        $endMin = self::timeToMinutes($closing);

        $slots = [];
        $now = new DateTime();
        $isToday = ($date === $now->format('Y-m-d'));
        $currentMinuteOfDay = ((int)$now->format('H') * 60) + (int)$now->format('i');

        // Determinar qué profesionales calificados aplican
        if ($professionalId && $professionalId > 0) {
            $professionals = [Professional::findById($professionalId)];
            $professionals = array_filter($professionals);
        } else {
            $professionals = Service::getQualifiedProfessionals($serviceId);
        }

        if (empty($professionals)) {
            return [];
        }

        for ($m = $startMin; ($m + $duration) <= $endMin; $m += $interval) {
            $slotTime = self::minutesToTime($m);

            // Si es hoy, descartar horarios pasados con margen de 30 mins
            if ($isToday && ($m <= $currentMinuteOfDay + 15)) {
                continue;
            }

            // Buscar si algún profesional calificado está libre en este slot
            $availableProfs = [];
            foreach ($professionals as $prof) {
                $check = self::isProfessionalAvailable(
                    (int)$prof['id'],
                    $date,
                    $slotTime,
                    $duration,
                    $buffer,
                    $excludeAppointmentId
                );
                if ($check['available']) {
                    $availableProfs[] = [
                        'id' => (int)$prof['id'],
                        'name' => $prof['name'],
                        'title' => $prof['title'],
                        'rating' => (float)$prof['rating']
                    ];
                }
            }

            if (!empty($availableProfs)) {
                // Periodo del día para agrupar visualmente en UI (Mañana, Tarde, Noche)
                $period = 'manana';
                if ($m >= 720 && $m < 1020) { // 12:00 a 17:00
                    $period = 'tarde';
                } elseif ($m >= 1020) { // 17:00+
                    $period = 'noche';
                }

                $slots[] = [
                    'time' => $slotTime,
                    'formatted_time' => date('h:i A', strtotime($slotTime)),
                    'period' => $period,
                    'available_professionals_count' => count($availableProfs),
                    'professionals' => $availableProfs
                ];
            }
        }

        return $slots;
    }

    /**
     * Verifica la política de cancelación o reprogramación (mínimo X horas de anticipación)
     */
    public static function canModifyAppointment(array $appointment, ?int $minHoursBefore = null): array {
        if ($minHoursBefore === null) {
            $minHoursBefore = (int)AppConfig::getSetting('min_cancel_hours', (string)self::DEFAULT_MIN_CANCEL_HOURS);
        }

        $appDateTimeStr = $appointment['date'] . ' ' . $appointment['start_time'];
        $appTime = strtotime($appDateTimeStr);
        $currentTime = time();

        $diffSeconds = $appTime - $currentTime;
        $diffHours = $diffSeconds / 3600;

        if ($diffHours < $minHoursBefore) {
            return [
                'allowed' => false,
                'hoursRemaining' => max(0, round($diffHours, 1)),
                'minHoursRequired' => $minHoursBefore,
                'reason' => "La política del spa requiere al menos {$minHoursBefore} horas de anticipación para modificaciones en línea. Por favor contacta al spa directamente vía telefónica."
            ];
        }

        return [
            'allowed' => true,
            'hoursRemaining' => max(0, round($diffHours, 1)),
            'minHoursRequired' => $minHoursBefore
        ];
    }
}
