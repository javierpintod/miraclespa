<?php
/**
 * MIRACLE SPA - Dashboard de Ocupación, Demanda & Analítica del Negocio
 * Estilo Gráfico: Glassmorfismo / Glassmorphism & Aurora Luxury Spa
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
    <!-- Fila 1: KPIs Rápidos Glassmorfistas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Citas Hoy -->
        <div class="glass-card p-6 rounded-3xl border border-white/80 shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-500">[ CITAS HOY ]</span>
                <span class="w-10 h-10 rounded-2xl bg-teal-500/15 text-teal-700 border border-teal-500/30 flex items-center justify-center text-base font-bold shadow-xs">📅</span>
            </div>
            <div class="text-3xl font-outfit font-extrabold text-slate-900"><?= $kpis['today_appointments'] ?></div>
            <div class="text-xs font-mono-pm text-teal-700 font-bold mt-2 bg-teal-500/15 border border-teal-500/30 px-2.5 py-1 rounded-xl inline-block">
                Proyectado: $<?= number_format($kpis['today_revenue'], 2) ?> USD
            </div>
        </div>

        <!-- Citas Mes -->
        <div class="glass-card p-6 rounded-3xl border border-white/80 shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-500">[ CITAS MES ]</span>
                <span class="w-10 h-10 rounded-2xl bg-sky-500/15 text-sky-700 border border-sky-500/30 flex items-center justify-center text-base font-bold shadow-xs">📊</span>
            </div>
            <div class="text-3xl font-outfit font-extrabold text-slate-900"><?= $kpis['month_appointments'] ?></div>
            <div class="text-xs font-mono-pm text-sky-700 font-bold mt-2">
                Facturación: $<?= number_format($kpis['month_revenue'], 2) ?> USD
            </div>
        </div>

        <!-- Completadas vs Canceladas -->
        <div class="glass-card p-6 rounded-3xl border border-white/80 shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-500">[ EFECTIVIDAD ]</span>
                <span class="w-10 h-10 rounded-2xl bg-violet-500/15 text-violet-700 border border-violet-500/30 flex items-center justify-center text-base font-bold shadow-xs">✓</span>
            </div>
            <div class="text-3xl font-outfit font-extrabold text-slate-900"><?= $kpis['month_completed'] ?></div>
            <div class="text-xs font-mono-pm text-slate-600 font-medium mt-2">
                Canceladas: <span class="text-rose-600 font-bold"><?= $kpis['month_cancelled'] ?></span> (<?= $kpis['cancellation_rate'] ?>%)
            </div>
        </div>

        <!-- Tasa Global -->
        <div class="glass-card p-6 rounded-3xl border border-white/80 shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-500">[ OCUPACIÓN ]</span>
                <span class="w-10 h-10 rounded-2xl bg-amber-500/15 text-amber-700 border border-amber-500/30 flex items-center justify-center text-base font-bold shadow-xs">✨</span>
            </div>
            <div class="text-3xl font-outfit font-extrabold text-slate-900"><?= $occupancy['month']['rate'] ?>%</div>
            <div class="text-xs font-mono-pm text-slate-600 font-medium mt-2">
                <?= $occupancy['month']['booked_hours'] ?>h de <?= $occupancy['month']['total_hours'] ?>h disponibles
            </div>
        </div>
    </div>

    <!-- Fila 2: Indicadores de Ocupación por Día, Semana y Mes -->
    <div class="glass-card rounded-3xl border border-white/80 p-6 sm:p-8 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 pb-4 border-b border-slate-200/60 gap-4">
            <div>
                <span class="font-mono-pm text-[11px] text-teal-700 font-bold block mb-0.5">[ CAPACIDAD CLÍNICA ]</span>
                <h2 class="text-lg font-outfit font-extrabold text-slate-900">Indicadores de Ocupación de Cabinas y Terapeutas</h2>
                <p class="text-xs text-slate-600 mt-0.5">Relación en tiempo real entre horas reservadas por clientes y capacidad laboral activa disponible.</p>
            </div>
            <span class="glass-badge glass-badge-teal self-start sm:self-auto">⚡ MÉTRICAS EN VIVO</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Ocupación Hoy -->
            <div class="glass-subtle-box p-5 rounded-2xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-700">Ocupación Hoy</span>
                    <span class="text-base font-mono-pm font-extrabold text-teal-700"><?= $occupancy['day']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-white/70 rounded-full h-3 mb-2 overflow-hidden border border-white/80 shadow-inner">
                    <div class="bg-gradient-to-r from-teal-500 to-emerald-500 h-3 rounded-full transition-all duration-700" style="width: <?= min(100, $occupancy['day']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-slate-600 flex justify-between font-mono-pm">
                    <span>Reservadas: <strong class="text-slate-800"><?= $occupancy['day']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['day']['total_hours'] ?>h</span>
                </div>
            </div>

            <!-- Ocupación Semana -->
            <div class="glass-subtle-box p-5 rounded-2xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-700">Ocupación Semana</span>
                    <span class="text-base font-mono-pm font-extrabold text-violet-700"><?= $occupancy['week']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-white/70 rounded-full h-3 mb-2 overflow-hidden border border-white/80 shadow-inner">
                    <div class="bg-gradient-to-r from-violet-500 to-purple-600 h-3 rounded-full transition-all duration-700" style="width: <?= min(100, $occupancy['week']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-slate-600 flex justify-between font-mono-pm">
                    <span>Reservadas: <strong class="text-slate-800"><?= $occupancy['week']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['week']['total_hours'] ?>h</span>
                </div>
            </div>

            <!-- Ocupación Mes -->
            <div class="glass-subtle-box p-5 rounded-2xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-700">Ocupación Mes</span>
                    <span class="text-base font-mono-pm font-extrabold text-sky-700"><?= $occupancy['month']['rate'] ?>%</span>
                </div>
                <div class="w-full bg-white/70 rounded-full h-3 mb-2 overflow-hidden border border-white/80 shadow-inner">
                    <div class="bg-gradient-to-r from-sky-500 to-cyan-500 h-3 rounded-full transition-all duration-700" style="width: <?= min(100, $occupancy['month']['rate']) ?>%"></div>
                </div>
                <div class="text-[11px] text-slate-600 flex justify-between font-mono-pm">
                    <span>Reservadas: <strong class="text-slate-800"><?= $occupancy['month']['booked_hours'] ?>h</strong></span>
                    <span>Capacidad: <?= $occupancy['month']['total_hours'] ?>h</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Gráficos de Demanda y Horas Pico Glassmorfistas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Gráfico 1: Horas Pico -->
        <div class="glass-card rounded-3xl border border-white/80 p-6 sm:p-7 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-outfit font-extrabold text-base text-slate-900">Horas Pico del Negocio</h3>
                    <p class="text-xs text-slate-600">Distribución de reservas por franja horaria (09:00 - 20:00).</p>
                </div>
                <span class="glass-badge glass-badge-blue">DEMANDA HORARIA</span>
            </div>
            <div class="h-64">
                <canvas id="chartPeakHours"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Servicios Más Demandados -->
        <div class="glass-card rounded-3xl border border-white/80 p-6 sm:p-7 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-outfit font-extrabold text-base text-slate-900">Servicios con Mayor Demanda</h3>
                    <p class="text-xs text-slate-600">Tratamientos con mayor volumen de reservas en el spa.</p>
                </div>
                <span class="glass-badge glass-badge-teal">TOP VENTAS</span>
            </div>
            <div class="h-64">
                <canvas id="chartTopServices"></canvas>
            </div>
        </div>
    </div>

    <!-- Fila 4: Rendimiento de Cada Profesional -->
    <div class="glass-card rounded-3xl border border-white/80 shadow-lg overflow-hidden">
        <div class="p-6 border-b border-slate-200/60 flex items-center justify-between bg-white/40">
            <div>
                <span class="font-mono-pm text-[10px] text-teal-700 font-bold block mb-0.5">[ EQUIPO & TALENTO ]</span>
                <h3 class="font-outfit font-extrabold text-base text-slate-900">Rendimiento por Especialista</h3>
                <p class="text-xs text-slate-600 mt-0.5">Citas atendidas, ingresos generados, índice de ocupación y valoración de clientes.</p>
            </div>
            <a href="profesionales.php" class="btn-glass-secondary text-xs px-4 py-2 rounded-xl font-outfit">Gestionar Especialistas →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-white/60 text-slate-700 uppercase tracking-wider font-mono-pm font-bold border-b border-slate-200/60">
                        <th class="p-4">Profesional</th>
                        <th class="p-4">Especialidad</th>
                        <th class="p-4 text-center">Valoración</th>
                        <th class="p-4 text-center">Citas Atendidas</th>
                        <th class="p-4 text-center">Horas en Cabina</th>
                        <th class="p-4 text-center">Ocupación</th>
                        <th class="p-4 text-right">Ingresos Generados</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/50">
                    <?php foreach ($staff as $p): ?>
                        <tr class="hover:bg-teal-50/40 transition-colors">
                            <td class="p-4 font-bold text-slate-900 flex items-center gap-3">
                                <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-teal-600 to-cyan-600 text-white flex items-center justify-center font-outfit font-bold text-xs shadow-sm border border-white/30">
                                    <?= substr($p['name'], 0, 1) ?>
                                </span>
                                <div>
                                    <div class="text-sm font-outfit font-bold text-slate-900"><?= htmlspecialchars($p['name']) ?></div>
                                    <div class="text-[11px] text-slate-500 font-mono-pm"><?= htmlspecialchars($p['title']) ?></div>
                                </div>
                            </td>
                            <td class="p-4 text-slate-600"><?= htmlspecialchars($p['title']) ?></td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center gap-1 text-amber-900 font-mono-pm font-bold bg-amber-400/20 px-2.5 py-0.5 rounded-full border border-amber-500/30">
                                    ★ <?= number_format($p['rating'], 1) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center font-mono-pm font-bold text-slate-800">
                                <?= $p['total_appointments'] ?> cita(s)
                            </td>
                            <td class="p-4 text-center text-slate-600 font-mono-pm">
                                <?= $p['hours_worked'] ?> hrs
                            </td>
                            <td class="p-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <span class="font-mono-pm font-bold text-slate-800"><?= $p['occupancy_rate'] ?>%</span>
                                    <div class="w-12 bg-white/80 rounded-full h-2 overflow-hidden border border-slate-200 shadow-inner">
                                        <div class="bg-gradient-to-r from-teal-500 to-emerald-500 h-2 rounded-full" style="width: <?= min(100, $p['occupancy_rate']) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-right font-mono-pm font-extrabold text-teal-700 text-sm">
                                $<?= number_format((float)$p['total_revenue'], 2) ?> USD
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts de Inicialización de Gráficos con Paleta Glassmorfista Aurora -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Gráfico de Horas Pico (Barras Glassmorfistas Esmeralda)
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
                backgroundColor: 'rgba(13, 148, 136, 0.75)',
                borderColor: '#0D9488',
                borderWidth: 1.5,
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

    // 2. Gráfico de Servicios Top (Doughnut con Tonos Joya Glassmorfismo)
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
                    'rgba(13, 148, 136, 0.85)', // Teal Esmeralda
                    'rgba(2, 132, 199, 0.85)',  // Cyan Zafiro
                    'rgba(124, 58, 237, 0.85)', // Violeta Amatista
                    'rgba(225, 29, 72, 0.85)',  // Rubí Rosé
                    'rgba(217, 119, 6, 0.85)',  // Ámbar Dorado
                    'rgba(5, 150, 105, 0.85)'   // Jade
                ],
                borderWidth: 2,
                borderColor: '#FFFFFF'
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
                        font: { size: 11, family: 'Outfit' }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
