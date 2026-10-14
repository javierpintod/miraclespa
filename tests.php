<?php
/**
 * MIRACLE SPA - Ejecutor Visual de Pruebas en el Navegador
 * Estilo Gráfico: Posmodernismo / Postmodernism & Architectural Atelier
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/tests/AvailabilityTest.php';
require_once __DIR__ . '/tests/BookingTest.php';
require_once __DIR__ . '/tests/JwtTest.php';
require_once __DIR__ . '/tests/SessionSingletonTest.php';

$startTime = microtime(true);
$availSuite = new AvailabilityTest();
$availResults = $availSuite->runAll();

$bookingSuite = new BookingTest();
$bookingResults = $bookingSuite->runAll();

$jwtSuite = new JwtTest();
$jwtResults = $jwtSuite->runAll();

$sessionSuite = new SessionSingletonTest();
$sessionResults = $sessionSuite->runAll();

$allResults = array_merge($availResults, $bookingResults, $jwtResults, $sessionResults);
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=Space+Mono:wght@400;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .font-syne { font-family: 'Syne', sans-serif; }
        .font-space { font-family: 'Space Grotesk', sans-serif; }
        .font-mono-pm { font-family: 'Space Mono', monospace; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F7F5EE] text-[#0D1117] min-h-screen antialiased selection:bg-[#D4FF00] selection:text-[#0D1117]">
    <!-- Header Posmodernista -->
    <header class="bg-white/95 backdrop-blur-md border-b-2 border-[#0D1117] sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-[#2548E8] text-white flex items-center justify-center font-syne font-bold text-lg border-2 border-[#0D1117] pm-shadow-xs">M</span>
                    <span class="font-syne font-bold text-lg text-[#0D1117]">Miracle Spa</span>
                </a>
                <span class="text-xs bg-[#D4FF00] text-[#0D1117] px-3 py-1 rounded-md font-mono-pm font-bold border-2 border-[#0D1117] pm-shadow-xs">[ 🧪 SUITE AUTOMATIZADA ]</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="index.php" class="text-xs font-mono-pm font-bold text-gray-700 hover:text-[#2548E8]">← Portal Cliente</a>
                <a href="admin/index.php" class="btn-pm-acid text-xs px-3.5 py-1.5 rounded-xl font-space">Panel Admin</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <!-- Titulo y Resumen -->
        <div class="mb-8">
            <span class="pm-stamp sticker-yellow mb-2">✦ VERIFICACIÓN CRIPTOGRÁFICA & LÓGICA ✦</span>
            <h1 class="text-3xl sm:text-4xl font-syne font-extrabold text-[#0D1117] mb-2 mt-2">Validación de Algoritmos & Concurrencia</h1>
            <p class="text-gray-700 text-sm font-medium">
                Pruebas de integridad para el cálculo de intervalos temporales, solapamiento con buffer de 15 min, prevención de doble reserva, tokens JWT y control Singleton de sesión única.
            </p>
        </div>

        <!-- KPI Cards de Resultados Posmodernistas -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm">
                <div class="text-xs font-mono-pm font-bold uppercase tracking-wider text-gray-500 mb-1">[ TOTAL PRUEBAS ]</div>
                <div class="text-3xl font-syne font-extrabold text-[#0D1117]"><?= $total ?></div>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm">
                <div class="text-xs font-mono-pm font-bold uppercase tracking-wider text-[#00E5A3] mb-1">[ APROBADAS PASS ]</div>
                <div class="text-3xl font-syne font-extrabold text-[#00E5A3]"><?= $passed ?></div>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm">
                <div class="text-xs font-mono-pm font-bold uppercase tracking-wider <?= $failed > 0 ? 'text-[#FF2E7E]' : 'text-gray-500' ?> mb-1">[ FALLIDAS FAIL ]</div>
                <div class="text-3xl font-syne font-extrabold <?= $failed > 0 ? 'text-[#FF2E7E]' : 'text-gray-400' ?>"><?= $failed ?></div>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm">
                <div class="text-xs font-mono-pm font-bold uppercase tracking-wider text-[#2548E8] mb-1">[ LATENCIA TEST ]</div>
                <div class="text-3xl font-mono-pm font-extrabold text-[#2548E8]"><?= $duration ?> <span class="text-sm font-bold text-gray-500">ms</span></div>
            </div>
        </div>

        <!-- Barra de Progreso General Posmodernista -->
        <div class="bg-white p-6 rounded-2xl border-2 border-[#0D1117] pm-shadow-sm mb-8">
            <div class="flex items-center justify-between text-sm mb-2 font-mono-pm">
                <span class="font-bold text-[#0D1117]">Tasa de Aprobación Global</span>
                <span class="font-bold text-[#0D1117] bg-[#D4FF00] px-3 py-1 rounded-md border-2 border-[#0D1117] pm-shadow-xs text-xs"><?= $total > 0 ? round(($passed / $total) * 100) : 0 ?>% Éxito</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden border-2 border-[#0D1117]">
                <div class="bg-[#00E5A3] h-4 rounded-full transition-all duration-500" style="width: <?= $total > 0 ? round(($passed / $total) * 100) : 0 ?>%"></div>
            </div>
        </div>

        <!-- Lista detallada de casos de prueba Posmodernista -->
        <div class="bg-white rounded-2xl border-2 border-[#0D1117] pm-shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-[#EFECE3] border-b-2 border-[#0D1117] flex items-center justify-between">
                <div>
                    <span class="font-mono-pm text-[10px] text-[#2548E8] font-bold block mb-0.5">[ UNIT TESTING SUITE ]</span>
                    <h2 class="text-base font-syne font-bold text-[#0D1117]">Desglose de Aserciones y Pruebas Unitarias</h2>
                </div>
                <a href="tests.php" class="btn-pm-acid text-xs px-3.5 py-1.5 rounded-xl font-space flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Re-ejecutar Pruebas
                </a>
            </div>

            <div class="divide-y-2 divide-gray-100">
                <?php foreach ($allResults as $idx => $r): ?>
                    <div class="p-4 sm:px-6 flex items-start gap-4 hover:bg-[#F7F5EE] transition-colors">
                        <div class="mt-0.5">
                            <?php if ($r['passed']): ?>
                                <span class="w-7 h-7 rounded-lg bg-[#00E5A3] text-[#0D1117] border-2 border-[#0D1117] pm-shadow-xs flex items-center justify-center text-xs font-bold font-mono-pm">✓</span>
                            <?php else: ?>
                                <span class="w-7 h-7 rounded-lg bg-[#FF2E7E] text-white border-2 border-[#0D1117] pm-shadow-xs flex items-center justify-center text-xs font-bold font-mono-pm">✗</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[11px] font-mono-pm font-bold px-2 py-0.5 rounded border border-[#0D1117] bg-[#EFECE3] text-[#0D1117]"><?= htmlspecialchars($r['suite']) ?></span>
                                <span class="text-xs text-gray-500 font-mono-pm">#<?= sprintf('%02d', $idx + 1) ?></span>
                            </div>
                            <div class="text-sm font-space font-bold text-gray-900"><?= htmlspecialchars($r['test']) ?></div>
                            <?php if (!empty($r['message'])): ?>
                                <div class="text-xs font-mono-pm text-gray-600 mt-1"><?= htmlspecialchars($r['message']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono-pm font-extrabold border-2 border-[#0D1117] pm-shadow-xs <?= $r['passed'] ? 'bg-[#00E5A3] text-[#0D1117]' : 'bg-[#FF2E7E] text-white' ?>">
                                <?= $r['passed'] ? '[ PASS ]' : '[ FAIL ]' ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-8 text-center text-xs font-mono-pm text-gray-600">
            Ejecución vía CLI en terminal: <code class="bg-white border border-[#0D1117] px-2.5 py-1 rounded text-[#0D1117] font-bold">php tests/run_tests.php</code>
        </div>
    </main>
</body>
</html>
