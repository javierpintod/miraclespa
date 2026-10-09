<?php
/**
 * MIRACLE SPA - Dashboard de Ocupación, Demanda & Analítica del Negocio
 */

$pageTitle = 'Dashboard de Ocupación & Demanda';
require_once __DIR__ . '/layout_header.php';
require_once __DIR__ . '/../app/Services/AnalyticsService.php';

$metrics = AnalyticsService::getDashboardMetrics();
$kpis = $metrics['summary_kpis'];
$occupancy = $metrics['occupancy'];
$topServices = $metrics['top_services'];
$staff = $metrics['staff_performance'];
$peakHours = $metrics['peak_hours'];
$trend = $metrics['monthly_trend'];
?>

<div class="space-y-8">
    <!-- Fila 1: KPIs Rápidos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Citas Hoy -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Citas para Hoy</span>
                <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm font-bold">📅</span>
            </div>
            <div class="text-3xl font-bold text-gray-900"><?= $kpis['today_appointments'] ?></div>
            <div class="text-xs text-emerald-700 font-semibold mt-1">
                Ingresos Proyectados: $<?= number_format($kpis['today_revenue'], 2) ?> USD
            </div>
        </div>

        <!-- Citas Mes -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Citas este Mes</span>
                <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center text-sm font-bold">📊</span>
            </div>
            <div class="text-3xl font-bold text-gray-900"><?= $kpis['month_appointments'] ?></div>
            <div class="text-xs text-blue-700 font-semibold mt-1">
                Facturación: $<?= number_format($kpis['month_revenue'], 2) ?> USD
            </div>
        </div>

        <!-- Completadas vs Canceladas -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Citas Completadas</span>
                <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm font-bold">✓</span>
            </div>
            <div class="text-3xl font-bold text-gray-900"><?= $kpis['month_completed'] ?></div>
            <div class="text-xs text-gray-500 mt-1">
                Canceladas: <span class="text-rose-600 font-bold"><?= $kpis['month_cancelled'] ?></span> (<?= $kpis['cancellation_rate'] ?>%)
            </div>
        </div>

        <!-- Tasa Global -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Ocupación Mensual</span>
                <span class="w-8 h-8 rounded-full bg-amber-50 text-amber-700 flex items-center justify-center text-sm font-bold">✨</span>
            </div>
            <div class="text-3xl font-bold text-gray-900"><?= $occupancy['month']['rate'] ?>%</div>
            <div class="text-xs text-gray-500 mt-1">
                <?= $occupancy['month']['booked_hours'] ?>h de <?= $occupancy['month']['total_hours'] ?>h disponibles
            </div>
        </div>
    </div>

    <!-- Fila 2: Indicadores de Ocupación por Día, Semana y Mes -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-base font-bold text-gray-900 font-serif">Indicadores de Ocupación de Cabinas y Terapeutas</h2>
                <p class="text-xs text-gray-500 mt-0.5">Relación entre horas reservadas por clientes y capacidad laboral activa disponible.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Ocupación Hoy -->
            <div class="bg-[#fafcfb] p-5 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-600">Ocupación del Día (Hoy)</span>
                    <span class="text-base font-bold text-emerald-700"><?= $occupancy['day']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2 overflow-hidden">
                    <div class="bg-emerald-600 h-2.5 rounded-full" style="width: <?= min(100, $occupancy['day']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-gray-500 flex justify-between">
                    <span>Horas reservadas: <strong><?= $occupancy['day']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['day']['total_hours'] ?>h</span>
                </div>
            </div>

            <!-- Ocupación Semana -->
            <div class="bg-[#fafcfb] p-5 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-600">Ocupación de la Semana</span>
                    <span class="text-base font-bold text-blue-700"><?= $occupancy['week']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2 overflow-hidden">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?= min(100, $occupancy['week']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-gray-500 flex justify-between">
                    <span>Horas reservadas: <strong><?= $occupancy['week']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['week']['total_hours'] ?>h</span>
                </div>
            </div>

            <!-- Ocupación Mes -->
            <div class="bg-[#fafcfb] p-5 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-600">Ocupación del Mes</span>
                    <span class="text-base font-bold text-[#12372a]"><?= $occupancy['month']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2 overflow-hidden">
                    <div class="bg-[#12372a] h-2.5 rounded-full" style="width: <?= min(100, $occupancy['month']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-gray-500 flex justify-between">
                    <span>Horas reservadas: <strong><?= $occupancy['month']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['month']['total_hours'] ?>h</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Gráficos de Servicios con Mayor Demanda y Horas Pico -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Gráfico 1: Horas Pico -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-serif font-bold text-base text-gray-900">Horas Pico del Negocio</h3>
                    <p class="text-xs text-gray-500">Distribución de reservas por franja horaria (09:00 - 20:00).</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-amber-50 text-amber-800 rounded-lg border border-amber-200">Demanda Horaria</span>
            </div>
            <div class="h-64">
                <canvas id="chartPeakHours"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Servicios Más Demandados -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-serif font-bold text-base text-gray-900">Servicios con Mayor Demanda</h3>
                    <p class="text-xs text-gray-500">Tratamientos con mayor volumen de reservas en el spa.</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-emerald-50 text-emerald-800 rounded-lg border border-emerald-200">Top Ventas</span>
            </div>
            <div class="h-64">
                <canvas id="chartTopServices"></canvas>
            </div>
        </div>
    </div>

    <!-- Fila 4: Rendimiento de Cada Profesional -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="font-serif font-bold text-base text-gray-900">Rendimiento por Profesional</h3>
                <p class="text-xs text-gray-500 mt-0.5">Citas atendidas, ingresos generados, índice de ocupación y valoración de clientes.</p>
            </div>
            <a href="profesionales.php" class="text-xs font-bold text-[#12372a] hover:underline">Gestionar Especialistas →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold border-b border-gray-200">
                        <th class="p-4">Profesional</th>
                        <th class="p-4">Especialidad</th>
                        <th class="p-4 text-center">Valoración</th>
                        <th class="p-4 text-center">Citas Atendidas</th>
                        <th class="p-4 text-center">Horas en Cabina</th>
                        <th class="p-4 text-center">Ocupación</th>
                        <th class="p-4 text-right">Ingresos Generados</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($staff as $p): ?>
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="p-4 font-bold text-gray-900 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-[#12372a]/10 text-[#12372a] flex items-center justify-center font-bold text-xs">
                                    <?= substr($p['name'], 0, 1) ?>
                                </span>
                                <div>
                                    <div class="text-sm"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-[11px] text-gray-400 font-normal"><?= htmlspecialchars($p['title']) ?></div>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600"><?= htmlspecialchars($p['title']) ?></td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center gap-1 text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                    ★ <?= number_format($p['rating'], 1) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center font-bold text-gray-800">
                                <?= $p['total_appointments'] ?> cita(s)
                            </td>
                            <td class="p-4 text-center text-gray-600">
                                <?= $p['hours_worked'] ?> hrs
                            </td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <span class="font-bold text-gray-800"><?= $p['occupancy_rate'] ?>%</span>
                                    <div class="w-12 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-600 h-1.5 rounded-full" style="width: <?= min(100, $p['occupancy_rate']) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-right font-serif font-bold text-emerald-800 text-sm">
                                $<?= number_format((float)$p['total_revenue'], 2) ?> USD
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts de Inicialización de Gráficos -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Gráfico de Horas Pico (Barras)
    const peakData = <?= json_encode($peakHours) ?>;
    const peakLabels = peakData.map(d => d.label);
    const peakCounts = peakData.map(d => d.count);

    const ctxPeak = document.getElementById('chartPeakHours').getContext('2d');
    new Chart(ctxPeak, {
        type: 'bar',
        data: {
            labels: peakLabels,
            datasets: [{
                label: 'Citas Reservadas',
                data: peakCounts,
                backgroundColor: 'rgba(18, 55, 42, 0.75)',
                borderColor: '#12372a',
                borderWidth: 1.5,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // 2. Gráfico de Servicios Top (Doughnut)
    const topData = <?= json_encode($topServices) ?>;
    const topLabels = topData.map(d => d.name);
    const topBookings = topData.map(d => d.bookings_count);

    const ctxTop = document.getElementById('chartTopServices').getContext('2d');
    new Chart(ctxTop, {
        type: 'doughnut',
        data: {
            labels: topLabels,
            datasets: [{
                data: topBookings,
                backgroundColor: [
                    '#12372a',
                    '#c5a880',
                    '#2563eb',
                    '#7c3aed',
                    '#059669',
                    '#d97706'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        font: { size: 10 }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
