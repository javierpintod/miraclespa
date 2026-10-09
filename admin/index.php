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
    <!-- Fila 1: KPIs Rápidos Memphis -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Citas Hoy -->
        <div class="bg-white p-5 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm hover:memphis-shadow transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gray-600">Citas para Hoy</span>
                <span class="w-9 h-9 rounded-xl bg-[#06D6A0] text-[#18181B] border-2 border-[#18181B] flex items-center justify-center text-sm font-bold memphis-shadow-sm">📅</span>
            </div>
            <div class="text-3xl font-extrabold text-[#18181B]"><?= $kpis['today_appointments'] ?></div>
            <div class="text-xs text-[#06D6A0] font-extrabold mt-1 bg-[#18181B] px-2 py-0.5 rounded-md inline-block">
                Proyectado: $<?= number_format($kpis['today_revenue'], 2) ?> USD
            </div>
        </div>

        <!-- Citas Mes -->
        <div class="bg-white p-5 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm hover:memphis-shadow transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gray-600">Citas este Mes</span>
                <span class="w-9 h-9 rounded-xl bg-[#3A86FF] text-white border-2 border-[#18181B] flex items-center justify-center text-sm font-bold memphis-shadow-sm">📊</span>
            </div>
            <div class="text-3xl font-extrabold text-[#18181B]"><?= $kpis['month_appointments'] ?></div>
            <div class="text-xs text-[#3A86FF] font-extrabold mt-1">
                Facturación: $<?= number_format($kpis['month_revenue'], 2) ?> USD
            </div>
        </div>

        <!-- Completadas vs Canceladas -->
        <div class="bg-white p-5 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm hover:memphis-shadow transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gray-600">Completadas</span>
                <span class="w-9 h-9 rounded-xl bg-[#8338EC] text-white border-2 border-[#18181B] flex items-center justify-center text-sm font-bold memphis-shadow-sm">✓</span>
            </div>
            <div class="text-3xl font-extrabold text-[#18181B]"><?= $kpis['month_completed'] ?></div>
            <div class="text-xs text-gray-600 font-bold mt-1">
                Canceladas: <span class="text-[#FF006E] font-extrabold"><?= $kpis['month_cancelled'] ?></span> (<?= $kpis['cancellation_rate'] ?>%)
            </div>
        </div>

        <!-- Tasa Global -->
        <div class="bg-white p-5 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm hover:memphis-shadow transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-extrabold uppercase tracking-wider text-gray-600">Ocupación Mensual</span>
                <span class="w-9 h-9 rounded-xl bg-[#FFBE0B] text-[#18181B] border-2 border-[#18181B] flex items-center justify-center text-sm font-bold memphis-shadow-sm">✨</span>
            </div>
            <div class="text-3xl font-extrabold text-[#18181B]"><?= $occupancy['month']['rate'] ?>%</div>
            <div class="text-xs text-gray-600 font-bold mt-1">
                <?= $occupancy['month']['booked_hours'] ?>h de <?= $occupancy['month']['total_hours'] ?>h disponibles
            </div>
        </div>
    </div>

    <!-- Fila 2: Indicadores de Ocupación por Día, Semana y Mes Memphis -->
    <div class="bg-white rounded-2xl border-2 border-[#18181B] p-6 memphis-shadow-sm">
        <div class="flex items-center justify-between mb-6 pb-3 border-b-2 border-[#18181B]">
            <div>
                <h2 class="text-base font-extrabold text-[#18181B]">Indicadores de Ocupación de Cabinas y Terapeutas</h2>
                <p class="text-xs text-gray-600 mt-0.5 font-medium">Relación entre horas reservadas por clientes y capacidad laboral activa disponible.</p>
            </div>
            <span class="memphis-sticker sticker-yellow">⚡ Métricas en Vivo</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Ocupación Hoy -->
            <div class="bg-[#FFF9E6] p-5 rounded-xl border-2 border-[#18181B] memphis-shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-[#18181B]">Ocupación Hoy</span>
                    <span class="text-base font-extrabold text-[#FF006E]"><?= $occupancy['day']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-white rounded-full h-3 mb-2 overflow-hidden border border-[#18181B]">
                    <div class="bg-[#FF006E] h-3 rounded-full" style="width: <?= min(100, $occupancy['day']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-gray-700 flex justify-between font-bold">
                    <span>Reservadas: <strong><?= $occupancy['day']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['day']['total_hours'] ?>h</span>
                </div>
            </div>

            <!-- Ocupación Semana -->
            <div class="bg-[#FFF9E6] p-5 rounded-xl border-2 border-[#18181B] memphis-shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-[#18181B]">Ocupación Semana</span>
                    <span class="text-base font-extrabold text-[#3A86FF]"><?= $occupancy['week']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-white rounded-full h-3 mb-2 overflow-hidden border border-[#18181B]">
                    <div class="bg-[#3A86FF] h-3 rounded-full" style="width: <?= min(100, $occupancy['week']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-gray-700 flex justify-between font-bold">
                    <span>Reservadas: <strong><?= $occupancy['week']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['week']['total_hours'] ?>h</span>
                </div>
            </div>

            <!-- Ocupación Mes -->
            <div class="bg-[#FFF9E6] p-5 rounded-xl border-2 border-[#18181B] memphis-shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-[#18181B]">Ocupación Mes</span>
                    <span class="text-base font-extrabold text-[#06D6A0]"><?= $occupancy['month']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-white rounded-full h-3 mb-2 overflow-hidden border border-[#18181B]">
                    <div class="bg-[#06D6A0] h-3 rounded-full" style="width: <?= min(100, $occupancy['month']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-gray-700 flex justify-between font-bold">
                    <span>Reservadas: <strong><?= $occupancy['month']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['month']['total_hours'] ?>h</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Gráficos de Demanda y Horas Pico Memphis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Gráfico 1: Horas Pico -->
        <div class="bg-white rounded-2xl border-2 border-[#18181B] p-6 memphis-shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-extrabold text-base text-[#18181B]">Horas Pico del Negocio</h3>
                    <p class="text-xs text-gray-600 font-medium">Distribución de reservas por franja horaria (09:00 - 20:00).</p>
                </div>
                <span class="memphis-sticker sticker-pink">Demanda Horaria</span>
            </div>
            <div class="h-64">
                <canvas id="chartPeakHours"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Servicios Más Demandados -->
        <div class="bg-white rounded-2xl border-2 border-[#18181B] p-6 memphis-shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-extrabold text-base text-[#18181B]">Servicios con Mayor Demanda</h3>
                    <p class="text-xs text-gray-600 font-medium">Tratamientos con mayor volumen de reservas en el spa.</p>
                </div>
                <span class="memphis-sticker sticker-yellow">Top Ventas</span>
            </div>
            <div class="h-64">
                <canvas id="chartTopServices"></canvas>
            </div>
        </div>
    </div>

    <!-- Fila 4: Rendimiento de Cada Profesional Memphis -->
    <div class="bg-white rounded-2xl border-2 border-[#18181B] memphis-shadow-sm overflow-hidden">
        <div class="p-6 border-b-2 border-[#18181B] flex items-center justify-between bg-[#FFF9E6]">
            <div>
                <h3 class="font-extrabold text-base text-[#18181B]">Rendimiento por Profesional</h3>
                <p class="text-xs text-gray-600 mt-0.5 font-medium">Citas atendidas, ingresos generados, índice de ocupación y valoración de clientes.</p>
            </div>
            <a href="profesionales.php" class="btn-memphis-yellow text-xs px-3 py-1.5 rounded-xl font-extrabold">Gestionar Especialistas →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100 text-[#18181B] uppercase tracking-wider font-extrabold border-b-2 border-[#18181B]">
                        <th class="p-4">Profesional</th>
                        <th class="p-4">Especialidad</th>
                        <th class="p-4 text-center">Valoración</th>
                        <th class="p-4 text-center">Citas Atendidas</th>
                        <th class="p-4 text-center">Horas en Cabina</th>
                        <th class="p-4 text-center">Ocupación</th>
                        <th class="p-4 text-right">Ingresos Generados</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-gray-100">
                    <?php foreach ($staff as $p): ?>
                        <tr class="hover:bg-[#FFF9E6]/60 transition-colors">
                            <td class="p-4 font-bold text-[#18181B] flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl bg-[#FF006E] text-white border-2 border-[#18181B] memphis-shadow-sm flex items-center justify-center font-extrabold text-xs">
                                    <?= substr($p['name'], 0, 1) ?>
                                </span>
                                <div>
                                    <div class="text-sm font-extrabold text-[#18181B]"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-[11px] text-gray-500 font-bold"><?= htmlspecialchars($p['title']) ?></div>
                                </div>
                            </td>
                            <td class="p-4 text-gray-700 font-medium"><?= htmlspecialchars($p['title']) ?></td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[#18181B] font-extrabold bg-[#FFBE0B] px-2.5 py-0.5 rounded-full border-2 border-[#18181B] memphis-shadow-sm">
                                    ★ <?= number_format($p['rating'], 1) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center font-extrabold text-[#18181B]">
                                <?= $p['total_appointments'] ?> cita(s)
                            </td>
                            <td class="p-4 text-center text-gray-700 font-bold">
                                <?= $p['hours_worked'] ?> hrs
                            </td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <span class="font-extrabold text-[#18181B]"><?= $p['occupancy_rate'] ?>%</span>
                                    <div class="w-12 bg-gray-200 rounded-full h-2 overflow-hidden border border-[#18181B]">
                                        <div class="bg-[#06D6A0] h-2 rounded-full" style="width: <?= min(100, $p['occupancy_rate']) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-right font-extrabold text-[#FF006E] text-sm">
                                $<?= number_format((float)$p['total_revenue'], 2) ?> USD
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts de Inicialización de Gráficos con Paleta Memphis Design -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Gráfico de Horas Pico (Barras estilo Memphis)
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
                backgroundColor: '#3A86FF',
                borderColor: '#18181B',
                borderWidth: 2,
                borderRadius: 8
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

    // 2. Gráfico de Servicios Top (Doughnut con Paleta Neón Memphis)
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
                    '#FF006E', // Memphis Pink
                    '#FFBE0B', // Memphis Yellow
                    '#3A86FF', // Memphis Blue
                    '#06D6A0', // Memphis Teal
                    '#8338EC', // Memphis Purple
                    '#FF5C9D'  // Memphis Light Pink
                ],
                borderWidth: 2.5,
                borderColor: '#18181B'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 14,
                        font: { size: 11, weight: 'bold' }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
