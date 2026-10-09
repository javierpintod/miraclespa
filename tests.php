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
<body class="bg-[#FDFBF7] text-[#18181B] min-h-screen">
    <!-- Header Memphis -->
    <header class="bg-white border-b-2 border-[#18181B] sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="index.php" class="flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl bg-[#FF006E] text-white flex items-center justify-center font-bold text-base border-2 border-[#18181B] memphis-shadow-sm">M</span>
                    <span class="font-bold text-lg text-[#18181B]">Miracle Spa</span>
                </a>
                <span class="text-xs bg-[#FFBE0B] text-[#18181B] px-3 py-1 rounded-full font-extrabold border border-[#18181B] memphis-shadow-sm">🧪 Suite Automatizada</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="index.php" class="text-xs font-bold text-gray-700 hover:text-[#FF006E]">Portal Cliente</a>
                <a href="admin/index.php" class="btn-memphis-yellow text-xs px-3.5 py-1.5 rounded-xl font-extrabold">Panel Admin</a>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <!-- Titulo y Resumen -->
        <div class="mb-8">
            <span class="memphis-sticker sticker-pink mb-2">⚡ Verificación Continua</span>
            <h1 class="text-3xl font-extrabold text-[#18181B] mb-2 mt-2">Validación de Lógica y Algoritmos</h1>
            <p class="text-gray-700 text-sm font-medium">
                Pruebas de integridad para el cálculo de intervalos temporales, solapamiento con buffer de 15 min, prevención de doble reserva y JWT.
            </p>
        </div>

        <!-- KPI Cards de Resultados Memphis -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm">
                <div class="text-xs font-extrabold uppercase tracking-wider text-gray-500 mb-1">Total de Pruebas</div>
                <div class="text-3xl font-extrabold text-[#18181B]"><?= $total ?></div>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm">
                <div class="text-xs font-extrabold uppercase tracking-wider text-[#06D6A0] mb-1">Aprobadas (PASS)</div>
                <div class="text-3xl font-extrabold text-[#06D6A0]"><?= $passed ?></div>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm">
                <div class="text-xs font-extrabold uppercase tracking-wider <?= $failed > 0 ? 'text-[#FF006E]' : 'text-gray-500' ?> mb-1">Fallidas (FAIL)</div>
                <div class="text-3xl font-extrabold <?= $failed > 0 ? 'text-[#FF006E]' : 'text-gray-400' ?>"><?= $failed ?></div>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm">
                <div class="text-xs font-extrabold uppercase tracking-wider text-gray-500 mb-1">Tiempo de Ejecución</div>
                <div class="text-3xl font-extrabold text-[#3A86FF]"><?= $duration ?> <span class="text-sm font-bold text-gray-500">ms</span></div>
            </div>
        </div>

        <!-- Barra de Progreso General Memphis -->
        <div class="bg-white p-6 rounded-2xl border-2 border-[#18181B] memphis-shadow-sm mb-8">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="font-extrabold text-[#18181B]">Tasa de Aprobación</span>
                <span class="font-extrabold text-[#06D6A0] bg-[#18181B] px-3 py-1 rounded-full text-xs"><?= $total > 0 ? round(($passed / $total) * 100) : 0 ?>% Éxito</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3.5 overflow-hidden border border-[#18181B]">
                <div class="bg-[#06D6A0] h-3.5 rounded-full transition-all duration-500" style="width: <?= $total > 0 ? round(($passed / $total) * 100) : 0 ?>%"></div>
            </div>
        </div>

        <!-- Lista detallada de casos de prueba Memphis -->
        <div class="bg-white rounded-2xl border-2 border-[#18181B] memphis-shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-[#FFF9E6] border-b-2 border-[#18181B] flex items-center justify-between">
                <h2 class="text-base font-extrabold text-[#18181B]">Desglose de Aserciones y Pruebas Unitarias</h2>
                <a href="tests.php" class="btn-memphis-yellow text-xs px-3 py-1.5 rounded-xl font-extrabold flex items-center gap-1">
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
