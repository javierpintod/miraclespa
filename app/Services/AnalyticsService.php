<?php
/**
 * MIRACLE SPA - Servicio de Analítica, Dashboard de Ocupación y Demanda
 * Procesa KPIs clínicos, rendimiento por profesional, horas pico y demanda de servicios
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Professional.php';
require_once __DIR__ . '/../Models/Service.php';

class AnalyticsService {
    /**
     * Retorna el conjunto completo de métricas e indicadores para el Dashboard
     */
    public static function getDashboardMetrics(): array {
        $db = Database::getConnection();

        return [
            'summary_kpis' => self::getSummaryKpis($db),
            'occupancy' => self::getOccupancyRates($db),
            'top_services' => self::getTopServices($db),
            'staff_performance' => self::getStaffPerformance($db),
            'peak_hours' => self::getPeakHours($db),
            'monthly_trend' => self::getMonthlyTrend($db)
        ];
    }

    /**
     * Tarjetas de KPI principales (Totales de citas, Ingresos, Citas de hoy, Tasa de cancelación)
     */
    private static function getSummaryKpis(PDO $db): array {
        // Citas de hoy
        $stmtToday = $db->query("SELECT COUNT(*) AS total, 
                                        COALESCE(SUM(price), 0) AS revenue 
                                 FROM appointments 
                                 WHERE date = CURDATE() AND status != 'cancelada'");
        $today = $stmtToday->fetch();

        // Citas del mes actual
        $stmtMonth = $db->query("SELECT COUNT(*) AS total, 
                                         COALESCE(SUM(CASE WHEN status != 'cancelada' THEN price ELSE 0 END), 0) AS revenue,
                                         COALESCE(SUM(CASE WHEN status = 'completada' THEN 1 ELSE 0 END), 0) AS completed,
                                         COALESCE(SUM(CASE WHEN status = 'cancelada' THEN 1 ELSE 0 END), 0) AS cancelled
                                  FROM appointments 
                                  WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())");
        $month = $stmtMonth->fetch();

        $totalMonth = (int)$month['total'];
        $cancelRate = $totalMonth > 0 ? round(((int)$month['cancelled'] / $totalMonth) * 100, 1) : 0;

        return [
            'today_appointments' => (int)$today['total'],
            'today_revenue' => (float)$today['revenue'],
            'month_appointments' => $totalMonth,
            'month_revenue' => (float)$month['revenue'],
            'month_completed' => (int)$month['completed'],
            'month_cancelled' => (int)$month['cancelled'],
            'cancellation_rate' => $cancelRate
        ];
    }

    /**
     * Indicadores de ocupación por Día (Hoy), Semana y Mes
     * Ocupación = (Horas reservadas / Horas laborables disponibles) * 100
     */
    private static function getOccupancyRates(PDO $db): array {
        // Profesionales activos
        $activeStaffCount = (int)$db->query("SELECT COUNT(*) FROM professionals WHERE active = 1")->fetchColumn();
        $dailyHoursPerStaff = 8; // Promedio de horas laborales por terapeuta
        $totalDailyCapacityMinutes = max(1, $activeStaffCount * $dailyHoursPerStaff * 60);

        // 1. Ocupación de Hoy
        $stmtToday = $db->query("SELECT COALESCE(SUM(duration_minutes), 0) AS booked_minutes 
                                 FROM appointments 
                                 WHERE date = CURDATE() AND status != 'cancelada'");
        $todayBookedMinutes = (int)$stmtToday->fetchColumn();
        $occupancyDay = min(100, round(($todayBookedMinutes / $totalDailyCapacityMinutes) * 100, 1));

        // 2. Ocupación de la Semana Actual (6 días laborales Lun-Sab)
        $weeklyCapacityMinutes = $totalDailyCapacityMinutes * 6;
        $stmtWeek = $db->query("SELECT COALESCE(SUM(duration_minutes), 0) AS booked_minutes 
                                FROM appointments 
                                WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1) AND status != 'cancelada'");
        $weekBookedMinutes = (int)$stmtWeek->fetchColumn();
        $occupancyWeek = min(100, round(($weekBookedMinutes / $weeklyCapacityMinutes) * 100, 1));

        // 3. Ocupación del Mes Actual (aprox 24 días laborales)
        $monthlyCapacityMinutes = $totalDailyCapacityMinutes * 24;
        $stmtMonth = $db->query("SELECT COALESCE(SUM(duration_minutes), 0) AS booked_minutes 
                                 FROM appointments 
                                 WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE()) AND status != 'cancelada'");
        $monthBookedMinutes = (int)$stmtMonth->fetchColumn();
        $occupancyMonth = min(100, round(($monthBookedMinutes / $monthlyCapacityMinutes) * 100, 1));

        return [
            'day' => [
                'rate' => $occupancyDay,
                'booked_hours' => round($todayBookedMinutes / 60, 1),
                'total_hours' => round($totalDailyCapacityMinutes / 60, 1)
            ],
            'week' => [
                'rate' => $occupancyWeek,
                'booked_hours' => round($weekBookedMinutes / 60, 1),
                'total_hours' => round($weeklyCapacityMinutes / 60, 1)
            ],
            'month' => [
                'rate' => $occupancyMonth,
                'booked_hours' => round($monthBookedMinutes / 60, 1),
                'total_hours' => round($monthlyCapacityMinutes / 60, 1)
            ]
        ];
    }

    /**
     * Servicios con mayor demanda (Top servicios más solicitados)
     */
    private static function getTopServices(PDO $db, int $limit = 6): array {
        $sql = "SELECT s.id, s.name, s.price, s.duration_minutes, c.name AS category_name,
                       COUNT(a.id) AS bookings_count,
                       COALESCE(SUM(CASE WHEN a.status != 'cancelada' THEN a.price ELSE 0 END), 0) AS total_revenue
                FROM services s
                JOIN categories c ON c.id = s.category_id
                LEFT JOIN appointments a ON a.service_id = s.id
                GROUP BY s.id
                ORDER BY bookings_count DESC, total_revenue DESC
                LIMIT {$limit}";

        $rows = $db->query($sql)->fetchAll();
        $totalBookings = array_sum(array_column($rows, 'bookings_count')) ?: 1;

        foreach ($rows as &$r) {
            $r['percentage'] = round(($r['bookings_count'] / $totalBookings) * 100, 1);
        }

        return $rows;
    }

    /**
     * Rendimiento por profesional (citas atendidas, ingresos generados, índice de satisfacción y ocupación)
     */
    private static function getStaffPerformance(PDO $db): array {
        $sql = "SELECT p.id, p.name, p.title, p.rating, p.avatar,
                       COUNT(a.id) AS total_appointments,
                       COALESCE(SUM(CASE WHEN a.status = 'completada' THEN 1 ELSE 0 END), 0) AS completed_appointments,
                       COALESCE(SUM(CASE WHEN a.status = 'cancelada' THEN 1 ELSE 0 END), 0) AS cancelled_appointments,
                       COALESCE(SUM(CASE WHEN a.status != 'cancelada' THEN a.price ELSE 0 END), 0) AS total_revenue,
                       COALESCE(SUM(CASE WHEN a.status != 'cancelada' THEN a.duration_minutes ELSE 0 END), 0) AS total_minutes
                FROM professionals p
                LEFT JOIN appointments a ON a.professional_id = p.id
                WHERE p.active = 1
                GROUP BY p.id
                ORDER BY total_revenue DESC, completed_appointments DESC";

        $rows = $db->query($sql)->fetchAll();

        foreach ($rows as &$r) {
            $totalHours = round($r['total_minutes'] / 60, 1);
            $r['hours_worked'] = $totalHours;
            // Estimación de ocupación individual mensual (base 160h mensuales)
            $r['occupancy_rate'] = min(100, round(($r['total_minutes'] / (160 * 60)) * 100, 1));
        }

        return $rows;
    }

    /**
     * Horas pico del negocio (distribución de citas por franja horaria)
     */
    private static function getPeakHours(PDO $db): array {
        $sql = "SELECT HOUR(start_time) AS hour_slot, COUNT(*) AS count
                FROM appointments
                WHERE status != 'cancelada'
                GROUP BY HOUR(start_time)
                ORDER BY hour_slot ASC";

        $rows = $db->query($sql)->fetchAll(PDO::FETCH_KEY_PAIR);

        $hours = [];
        for ($h = 9; $h <= 19; $h++) {
            $formattedHour = sprintf('%02d:00', $h);
            $hours[] = [
                'hour' => $h,
                'label' => date('h:i A', strtotime($formattedHour)),
                'count' => (int)($rows[$h] ?? 0)
            ];
        }

        return $hours;
    }

    /**
     * Tendencia de reservas de los últimos 7 días
     */
    private static function getMonthlyTrend(PDO $db): array {
        $sql = "SELECT date, 
                       COUNT(*) AS total_appointments,
                       COALESCE(SUM(CASE WHEN status != 'cancelada' THEN price ELSE 0 END), 0) AS revenue
                FROM appointments
                WHERE date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
                GROUP BY date
                ORDER BY date ASC";

        return $db->query($sql)->fetchAll();
    }
}
