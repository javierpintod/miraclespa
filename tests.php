<?php
/**
 * MIRACLE SPA - Ejecutor Visual de Pruebas en el Navegador
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/tests/AvailabilityTest.php';
require_once __DIR__ . '/tests/BookingTest.php';
require_once __DIR__ . '/tests/JwtTest.php';

$startTime = microtime(true);
$availSuite = new AvailabilityTest();
$availResults = $availSuite->runAll();

$bookingSuite = new BookingTest();
$bookingResults = $bookingSuite->runAll();

$jwtSuite = new JwtTest();
$jwtResults = $jwtSuite->runAll();

$allResults = array_merge($availResults, $bookingResults, $jwtResults);
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
    <title>Pruebas de Disponibilidad & Reservas - Miracle Spa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-[#f7f9f7] text-[#1c2420] min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-[#e2e8e3] sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-[#12372a] text-[#d4af37] flex items-center justify-center font-serif font-bold text-sm">M</span>
                    <span class="font-serif text-lg font-bold text-[#12372a]">Miracle Spa</span>
                </a>
                <span class="text-xs bg-[#e9f2ec] text-[#12372a] px-2.5 py-1 rounded-full font-semibold">Suite de Pruebas Automatizadas</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="index.php" class="text-sm font-medium text-gray-600 hover:text-[#12372a] transition-colors">Portal Cliente</a>
                <a href="admin/index.php" class="text-sm font-semibold bg-[#12372a] text-white px-3.5 py-1.5 rounded-lg hover:bg-[#1a4a39] transition-colors">Panel Admin</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <!-- Titulo y Resumen -->
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-[#12372a] mb-2">Validación de Lógica y Algoritmos</h1>
            <p class="text-gray-600 text-sm">
                Pruebas de integridad para el cálculo de intervalos temporales, solapamiento con buffer de higienización, doble reserva atómica y políticas de cancelación.
            </p>
        </div>

        <!-- KPI Cards de Resultados -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Total de Pruebas</div>
                <div class="text-3xl font-bold text-gray-900"><?= $total ?></div>
            </div>
            <div class="bg-white p-5 rounded-xl border border-emerald-200 shadow-sm bg-gradient-to-br from-emerald-50/50 to-white">
                <div class="text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-1">Aprobadas (PASS)</div>
                <div class="text-3xl font-bold text-emerald-600"><?= $passed ?></div>
            </div>
            <div class="bg-white p-5 rounded-xl border <?= $failed > 0 ? 'border-rose-300 bg-rose-50' : 'border-gray-200' ?> shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wider <?= $failed > 0 ? 'text-rose-700' : 'text-gray-500' ?> mb-1">Fallidas (FAIL)</div>
                <div class="text-3xl font-bold <?= $failed > 0 ? 'text-rose-600' : 'text-gray-400' ?>"><?= $failed ?></div>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Tiempo de Ejecución</div>
                <div class="text-3xl font-bold text-gray-900"><?= $duration ?> <span class="text-sm font-normal text-gray-500">ms</span></div>
            </div>
        </div>

        <!-- Barra de Progreso General -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-8">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="font-semibold text-gray-700">Tasa de Aprobación</span>
                <span class="font-bold text-emerald-600"><?= $total > 0 ? round(($passed / $total) * 100) : 0 ?>% Éxito</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: <?= $total > 0 ? round(($passed / $total) * 100) : 0 ?>%"></div>
            </div>
        </div>

        <!-- Lista detallada de casos de prueba -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-800">Desglose de Aserciones y Pruebas Unitarias</h2>
                <a href="tests.php" class="text-xs font-semibold text-[#12372a] hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Re-ejecutar Pruebas
                </a>
            </div>

            <div class="divide-y divide-gray-100">
                <?php foreach ($allResults as $idx => $r): ?>
                    <div class="p-4 sm:px-6 flex items-start gap-4 hover:bg-gray-50/70 transition-colors">
                        <div class="mt-0.5">
                            <?php if ($r['passed']): ?>
                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                            <?php else: ?>
                                <span class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-xs font-bold">✗</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-700"><?= htmlspecialchars($r['suite']) ?></span>
                                <span class="text-xs text-gray-400">#<?= $idx + 1 ?></span>
                            </div>
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($r['test']) ?></div>
                            <?php if (!empty($r['message'])): ?>
                                <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($r['message']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider <?= $r['passed'] ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                                <?= $r['passed'] ? 'PASS' : 'FAIL' ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-8 text-center text-xs text-gray-500">
            También puedes ejecutar esta suite vía consola con el comando: <code class="bg-gray-200 px-2 py-1 rounded text-gray-800 font-mono">php tests/run_tests.php</code>
        </div>
    </main>
</body>
</html>
