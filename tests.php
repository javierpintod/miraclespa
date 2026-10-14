<?php
/**
 * MIRACLE SPA - Ejecutor Visual de Pruebas en el Navegador
 * Estilo Gráfico: Glassmorfismo / Glassmorphism & Aurora Luxury Spa
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/tests/AvailabilityTest.php';
require_once __DIR__ . '/tests/BookingTest.php';
require_once __DIR__ . '/tests/JwtTest.php';
require_once __DIR__ . '/tests/SessionSingletonTest.php';
require_once __DIR__ . '/tests/PaymentTest.php';

$startTime = microtime(true);
$availSuite = new AvailabilityTest();
$availResults = $availSuite->runAll();

$bookingSuite = new BookingTest();
$bookingResults = $bookingSuite->runAll();

$jwtSuite = new JwtTest();
$jwtResults = $jwtSuite->runAll();

$sessionSuite = new SessionSingletonTest();
$sessionResults = $sessionSuite->runAll();

$paymentSuite = new PaymentTest();
$paymentResults = $paymentSuite->runAll();

$allResults = array_merge($availResults, $bookingResults, $jwtResults, $sessionResults, $paymentResults);
$total = count($allResults);
$passed = count(array_filter($allResults, fn($r) => $r['passed']));
$failed = $total - $passed;
$duration = round((microtime(true) - $startTime) * 1000, 2);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pruebas de Disponibilidad & Reservas — Miracle Spa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css?v=<?= filemtime(__DIR__ . '/assets/css/app.css') ?>">
    <style>
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .font-mono-pm { font-family: 'Space Mono', monospace; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="text-slate-800 min-h-screen antialiased selection:bg-teal-500 selection:text-white relative overflow-x-hidden">

    <!-- Orbes de Luz de Fondo Glassmorfista -->
    <div class="aurora-orb aurora-orb-teal w-96 h-96 -top-20 -left-20"></div>
    <div class="aurora-orb aurora-orb-violet w-[32rem] h-[32rem] top-1/3 -right-24"></div>
    <div class="aurora-orb aurora-orb-rose w-80 h-80 -bottom-10 left-1/4"></div>

    <!-- Header Glassmorfista -->
    <header class="glass-card rounded-none border-x-0 border-t-0 border-b border-white/60 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-2.5 group">
                    <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 text-white flex items-center justify-center font-outfit font-extrabold text-lg shadow-md shadow-teal-500/25 group-hover:scale-105 transition-transform border border-white/30">M</span>
                    <span class="font-outfit font-extrabold text-lg text-slate-900">Miracle Spa</span>
                </a>
                <span class="glass-badge glass-badge-teal">[ 🧪 SUITE AUTOMATIZADA ]</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="index.php" class="text-xs font-semibold text-slate-600 hover:text-teal-700 transition-colors px-3 py-1.5 rounded-xl hover:bg-white/50">← Portal Cliente</a>
                <a href="admin/index.php" class="btn-glass-secondary text-xs px-3.5 py-1.5 rounded-xl font-outfit">Panel Admin</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-12 relative z-10">
        <!-- Titulo y Resumen -->
        <div class="mb-8">
            <span class="glass-badge glass-badge-teal mb-2">✦ VERIFICACIÓN CRIPTOGRÁFICA & LÓGICA ✦</span>
            <h1 class="text-3xl sm:text-4xl font-outfit font-extrabold text-slate-900 mb-2 mt-2">Validación de Algoritmos & Concurrencia</h1>
            <p class="text-slate-600 text-sm leading-relaxed">
                Pruebas de integridad para el cálculo de intervalos temporales, solapamiento con buffer de 15 min, prevención de doble reserva, tokens JWT y control Singleton de sesión única.
            </p>
        </div>

        <!-- KPI Cards de Resultados Glassmorfistas -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
            <div class="glass-card p-6 rounded-3xl border border-white/80 shadow-md">
                <div class="text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-500 mb-1">[ TOTAL PRUEBAS ]</div>
                <div class="text-3xl font-outfit font-extrabold text-slate-900"><?= $total ?></div>
            </div>
            <div class="glass-card p-6 rounded-3xl border border-white/80 shadow-md">
                <div class="text-xs font-mono-pm font-bold uppercase tracking-wider text-emerald-600 mb-1">[ APROBADAS PASS ]</div>
                <div class="text-3xl font-outfit font-extrabold text-emerald-600"><?= $passed ?></div>
            </div>
            <div class="glass-card p-6 rounded-3xl border border-white/80 shadow-md">
                <div class="text-xs font-mono-pm font-bold uppercase tracking-wider <?= $failed > 0 ? 'text-rose-600' : 'text-slate-500' ?> mb-1">[ FALLIDAS FAIL ]</div>
                <div class="text-3xl font-outfit font-extrabold <?= $failed > 0 ? 'text-rose-600' : 'text-slate-400' ?>"><?= $failed ?></div>
            </div>
            <div class="glass-card p-6 rounded-3xl border border-white/80 shadow-md">
                <div class="text-xs font-mono-pm font-bold uppercase tracking-wider text-teal-700 mb-1">[ LATENCIA TEST ]</div>
                <div class="text-3xl font-mono-pm font-extrabold text-teal-700"><?= $duration ?> <span class="text-sm font-bold text-slate-500">ms</span></div>
            </div>
        </div>

        <!-- Barra de Progreso General Glassmorfista -->
        <div class="glass-card p-6 rounded-3xl border border-white/80 shadow-md mb-8">
            <div class="flex items-center justify-between text-sm mb-2 font-mono-pm">
                <span class="font-bold text-slate-800">Tasa de Aprobación Global</span>
                <span class="font-bold text-teal-800 bg-teal-500/20 border border-teal-500/30 px-3 py-1 rounded-xl text-xs"><?= $total > 0 ? round(($passed / $total) * 100) : 0 ?>% Éxito</span>
            </div>
            <div class="w-full bg-white/70 rounded-full h-4 overflow-hidden border border-white/80 shadow-inner">
                <div class="bg-gradient-to-r from-teal-500 to-emerald-500 h-4 rounded-full transition-all duration-700" style="width: <?= $total > 0 ? round(($passed / $total) * 100) : 0 ?>%"></div>
            </div>
        </div>

        <!-- Lista detallada de casos de prueba Glassmorfista -->
        <div class="glass-card rounded-3xl border border-white/80 shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-white/40 border-b border-slate-200/60 flex items-center justify-between">
                <div>
                    <span class="font-mono-pm text-[10px] text-teal-700 font-bold block mb-0.5">[ UNIT TESTING SUITE ]</span>
                    <h2 class="text-base font-outfit font-extrabold text-slate-900">Desglose de Aserciones y Pruebas Unitarias</h2>
                </div>
                <a href="tests.php" class="btn-glass-secondary text-xs px-3.5 py-1.5 rounded-xl font-outfit flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Re-ejecutar Pruebas
                </a>
            </div>

            <div class="divide-y divide-slate-200/50">
                <?php foreach ($allResults as $idx => $r): ?>
                    <div class="p-4 sm:px-6 flex items-start gap-4 hover:bg-teal-50/30 transition-colors">
                        <div class="mt-0.5">
                            <?php if ($r['passed']): ?>
                                <span class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-700 border border-emerald-500/40 flex items-center justify-center text-xs font-bold font-mono-pm shadow-2xs">✓</span>
                            <?php else: ?>
                                <span class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-700 border border-rose-500/40 flex items-center justify-center text-xs font-bold font-mono-pm shadow-2xs">✗</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[11px] font-mono-pm font-bold px-2 py-0.5 rounded-lg border border-teal-500/30 bg-teal-500/10 text-teal-800"><?= htmlspecialchars($r['suite']) ?></span>
                                <span class="text-xs text-slate-500 font-mono-pm">#<?= sprintf('%02d', $idx + 1) ?></span>
                            </div>
                            <div class="text-sm font-outfit font-bold text-slate-900"><?= htmlspecialchars($r['test']) ?></div>
                            <?php if (!empty($r['message'])): ?>
                                <div class="text-xs font-mono-pm text-slate-600 mt-1"><?= htmlspecialchars($r['message']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono-pm font-extrabold border <?= $r['passed'] ? 'bg-emerald-500/15 border-emerald-500/30 text-emerald-800' : 'bg-rose-500/15 border-rose-500/30 text-rose-800' ?>">
                                <?= $r['passed'] ? '[ PASS ]' : '[ FAIL ]' ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-8 text-center text-xs font-mono-pm text-slate-600">
            Ejecución vía CLI en terminal: <code class="bg-white/70 border border-white/90 px-3 py-1.5 rounded-xl text-teal-800 font-bold shadow-2xs">php tests/run_tests.php</code>
        </div>
    </main>
</body>
</html>
