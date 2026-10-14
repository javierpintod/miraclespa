<?php
/**
 * MIRACLE SPA - Dashboard de Ocupación, Demanda & Analítica del Negocio
 * Estilo Gráfico: Posmodernismo / Postmodernism & Architectural Atelier
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
    <!-- Fila 1: KPIs Rápidos Posmodernistas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Citas Hoy -->
        <div class="bg-white p-5 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm hover:pm-shadow transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-gray-600">[ CITAS HOY ]</span>
                <span class="w-9 h-9 rounded-xl bg-[#00E5A3] text-[#0D1117] border-2 border-[#0D1117] flex items-center justify-center text-sm font-bold pm-shadow-xs">📅</span>
            </div>
            <div class="text-3xl font-syne font-extrabold text-[#0D1117]"><?= $kpis['today_appointments'] ?></div>
            <div class="text-xs font-mono-pm text-[#00E5A3] font-bold mt-1 bg-[#0D1117] px-2 py-0.5 rounded inline-block">
                Proyectado: $<?= number_format($kpis['today_revenue'], 2) ?> USD
            </div>
        </div>

        <!-- Citas Mes -->
        <div class="bg-white p-5 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm hover:pm-shadow transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-gray-600">[ CITAS MES ]</span>
                <span class="w-9 h-9 rounded-xl bg-[#2548E8] text-white border-2 border-[#0D1117] flex items-center justify-center text-sm font-bold pm-shadow-xs">📊</span>
            </div>
            <div class="text-3xl font-syne font-extrabold text-[#0D1117]"><?= $kpis['month_appointments'] ?></div>
            <div class="text-xs font-mono-pm text-[#2548E8] font-bold mt-1">
                Facturación: $<?= number_format($kpis['month_revenue'], 2) ?> USD
            </div>
        </div>

        <!-- Completadas vs Canceladas -->
        <div class="bg-white p-5 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm hover:pm-shadow transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-gray-600">[ EFECTIVIDAD ]</span>
                <span class="w-9 h-9 rounded-xl bg-[#9E00FF] text-white border-2 border-[#0D1117] flex items-center justify-center text-sm font-bold pm-shadow-xs">✓</span>
            </div>
            <div class="text-3xl font-syne font-extrabold text-[#0D1117]"><?= $kpis['month_completed'] ?></div>
            <div class="text-xs font-mono-pm text-gray-600 font-bold mt-1">
                Canceladas: <span class="text-[#FF2E7E] font-extrabold"><?= $kpis['month_cancelled'] ?></span> (<?= $kpis['cancellation_rate'] ?>%)
            </div>
        </div>

        <!-- Tasa Global -->
        <div class="bg-white p-5 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm hover:pm-shadow transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-gray-600">[ OCUPACIÓN ]</span>
                <span class="w-9 h-9 rounded-xl bg-[#D4FF00] text-[#0D1117] border-2 border-[#0D1117] flex items-center justify-center text-sm font-bold pm-shadow-xs">✨</span>
            </div>
            <div class="text-3xl font-syne font-extrabold text-[#0D1117]"><?= $occupancy['month']['rate'] ?>%</div>
            <div class="text-xs font-mono-pm text-gray-600 font-bold mt-1">
                <?= $occupancy['month']['booked_hours'] ?>h de <?= $occupancy['month']['total_hours'] ?>h disponibles
            </div>
        </div>
    </div>

    <!-- Fila 2: Indicadores de Ocupación por Día, Semana y Mes -->
    <div class="bg-white rounded-2xl border-2 border-[#0D1117] p-6 pm-shadow-sm">
        <div class="flex items-center justify-between mb-6 pb-3 border-b-2 border-[#0D1117]">
            <div>
                <span class="font-mono-pm text-[11px] text-[#2548E8] font-bold block mb-0.5">[ CAPACIDAD CLÍNICA ]</span>
                <h2 class="text-base font-syne font-bold text-[#0D1117]">Indicadores de Ocupación de Cabinas y Terapeutas</h2>
                <p class="text-xs text-gray-600 mt-0.5 font-medium">Relación en tiempo real entre horas reservadas por clientes y capacidad laboral activa disponible.</p>
            </div>
            <span class="pm-stamp sticker-yellow">⚡ MÉTRICAS EN VIVO</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Ocupación Hoy -->
            <div class="bg-[#EFECE3] p-5 rounded-xl border-2 border-[#0D1117] pm-shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117]">Ocupación Hoy</span>
                    <span class="text-base font-mono-pm font-extrabold text-[#2548E8]"><?= $occupancy['day']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-white rounded-full h-3 mb-2 overflow-hidden border-2 border-[#0D1117]">
                    <div class="bg-[#2548E8] h-3 rounded-full" style="width: <?= min(100, $occupancy['day']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-gray-700 flex justify-between font-mono-pm font-bold">
                    <span>Reservadas: <strong><?= $occupancy['day']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['day']['total_hours'] ?>h</span>
                </div>
            </div>

            <!-- Ocupación Semana -->
            <div class="bg-[#EFECE3] p-5 rounded-xl border-2 border-[#0D1117] pm-shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117]">Ocupación Semana</span>
                    <span class="text-base font-mono-pm font-extrabold text-[#9E00FF]"><?= $occupancy['week']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-white rounded-full h-3 mb-2 overflow-hidden border-2 border-[#0D1117]">
                    <div class="bg-[#9E00FF] h-3 rounded-full" style="width: <?= min(100, $occupancy['week']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-gray-700 flex justify-between font-mono-pm font-bold">
                    <span>Reservadas: <strong><?= $occupancy['week']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['week']['total_hours'] ?>h</span>
                </div>
            </div>

            <!-- Ocupación Mes -->
            <div class="bg-[#EFECE3] p-5 rounded-xl border-2 border-[#0D1117] pm-shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-[#0D1117]">Ocupación Mes</span>
                    <span class="text-base font-mono-pm font-extrabold text-[#00E5A3]"><?= $occupancy['month']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-white rounded-full h-3 mb-2 overflow-hidden border-2 border-[#0D1117]">
                    <div class="bg-[#00E5A3] h-3 rounded-full" style="width: <?= min(100, $occupancy['month']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-gray-700 flex justify-between font-mono-pm font-bold">
                    <span>Reservadas: <strong><?= $occupancy['month']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['month']['total_hours'] ?>h</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Gráficos de Demanda y Horas Pico Posmodernistas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Gráfico 1: Horas Pico -->
        <div class="bg-white rounded-2xl border-2 border-[#0D1117] p-6 pm-shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-syne font-bold text-base text-[#0D1117]">Horas Pico del Negocio</h3>
                    <p class="text-xs text-gray-600 font-medium">Distribución de reservas por franja horaria (09:00 - 20:00).</p>
                </div>
                <span class="pm-stamp sticker-blue">DEMANDA HORARIA</span>
            </div>
            <div class="h-64">
                <canvas id="chartPeakHours"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Servicios Más Demandados -->
        <div class="bg-white rounded-2xl border-2 border-[#0D1117] p-6 pm-shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-syne font-bold text-base text-[#0D1117]">Servicios con Mayor Demanda</h3>
                    <p class="text-xs text-gray-600 font-medium">Tratamientos con mayor volumen de reservas en el spa.</p>
                </div>
                <span class="pm-stamp sticker-yellow">TOP VENTAS</span>
            </div>
            <div class="h-64">
                <canvas id="chartTopServices"></canvas>
            </div>
        </div>
    </div>

    <!-- Fila 4: Rendimiento de Cada Profesional -->
    <div class="bg-white rounded-2xl border-2 border-[#0D1117] pm-shadow-sm overflow-hidden">
        <div class="p-6 border-b-2 border-[#0D1117] flex items-center justify-between bg-[#EFECE3]">
            <div>
                <span class="font-mono-pm text-[10px] text-[#2548E8] font-bold block mb-0.5">[ EQUIPO & TALENTO ]</span>
                <h3 class="font-syne font-bold text-base text-[#0D1117]">Rendimiento por Especialista</h3>
                <p class="text-xs text-gray-600 mt-0.5 font-medium">Citas atendidas, ingresos generados, índice de ocupación y valoración de clientes.</p>
            </div>
            <a href="profesionales.php" class="btn-pm-acid text-xs px-3.5 py-1.5 rounded-xl font-space">Gestionar Especialistas →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100 text-[#0D1117] uppercase tracking-wider font-mono-pm font-bold border-b-2 border-[#0D1117]">
                        <th class="p-4">Profesional</th>
                        <th class="p-4">Especialidad</th>
                        <th class="p-4 text-center">Valoración</th>
                        <th class="p-4 text-center">Citas Atendidas</th>
                        <th class="p-4 text-center">Horas en Cabina</th>
                        <th class="p-4 text-center">Ocupación</th>
                        <th class="p-4 text-right">Ingresos Generados</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-gray-100 font-medium">
                    <?php foreach ($staff as $p): ?>
                        <tr class="hover:bg-[#F7F5EE] transition-colors">
                            <td class="p-4 font-bold text-[#0D1117] flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl bg-[#2548E8] text-white border-2 border-[#0D1117] pm-shadow-xs flex items-center justify-center font-syne font-bold text-xs">
                                    <?= substr($p['name'], 0, 1) ?>
                                </span>
                                <div>
                                    <div class="text-sm font-space font-bold text-[#0D1117]"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-[11px] text-gray-500 font-mono-pm"><?= htmlspecialchars($p['title']) ?></div>
                                </div>
                            </td>
                            <td class="p-4 text-gray-700"><?= htmlspecialchars($p['title']) ?></td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[#0D1117] font-mono-pm font-bold bg-[#D4FF00] px-2.5 py-0.5 rounded-md border-2 border-[#0D1117] pm-shadow-xs">
                                    ★ <?= number_format($p['rating'], 1) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center font-mono-pm font-bold text-[#0D1117]">
                                <?= $p['total_appointments'] ?> cita(s)
                            </td>
                            <td class="p-4 text-center text-gray-700 font-mono-pm font-bold">
                                <?= $p['hours_worked'] ?> hrs
                            </td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <span class="font-mono-pm font-bold text-[#0D1117]"><?= $p['occupancy_rate'] ?>%</span>
                                    <div class="w-12 bg-gray-200 rounded-full h-2 overflow-hidden border border-[#0D1117]">
                                        <div class="bg-[#00E5A3] h-2 rounded-full" style="width: <?= min(100, $p['occupancy_rate']) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-right font-mono-pm font-extrabold text-[#2548E8] text-sm">
                                $<?= number_format((float)$p['total_revenue'], 2) ?> USD
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts de Inicialización de Gráficos con Paleta Posmodernista -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Gráfico de Horas Pico (Barras Posmodernistas Cobalt Klein)
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
                backgroundColor: '#2548E8',
                borderColor: '#0D1117',
                borderWidth: 2,
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

    // 2. Gráfico de Servicios Top (Doughnut con Paleta de Alto Impacto Posmoderna)
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
                    '#2548E8', // Cobalt Klein
                    '#D4FF00', // Acid Lime
                    '#9E00FF', // Cyber Orchid
                    '#FF2E7E', // Vivid Rosé
                    '#00E5A3', // Mint Green
                    '#FF5A36'  // Terracotta
                ],
                borderWidth: 2.5,
                borderColor: '#0D1117'
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
                        font: { size: 11, weight: 'bold', family: 'Space Mono' }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
