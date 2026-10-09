<?php
/**
 * MIRACLE SPA - Ejecutor de Pruebas Automatizadas por Consola (CLI)
 * Uso: php tests/run_tests.php
 */

require_once __DIR__ . '/AvailabilityTest.php';
require_once __DIR__ . '/BookingTest.php';

echo "\n=======================================================\n";
echo "  MIRACLE SPA - SUITE DE PRUEBAS AUTOMATIZADAS (PHP)\n";
echo "=======================================================\n\n";

$startTime = microtime(true);

$availSuite = new AvailabilityTest();
$availResults = $availSuite->runAll();

$bookingSuite = new BookingTest();
$bookingResults = $bookingSuite->runAll();

$allResults = array_merge($availResults, $bookingResults);

$total = count($allResults);
$passed = 0;
$failed = 0;

$currentSuite = '';

foreach ($allResults as $res) {
    if ($res['suite'] !== $currentSuite) {
        $currentSuite = $res['suite'];
        echo "\n[+] MÓDULO: {$currentSuite}\n";
        echo str_repeat('-', 55) . "\n";
    }

    if ($res['passed']) {
        $passed++;
        echo "  [✓ PASS] " . $res['test'] . "\n";
    } else {
        $failed++;
        echo "  [✗ FAIL] " . $res['test'] . "\n";
        if (!empty($res['message'])) {
            echo "           Motivo: " . $res['message'] . "\n";
        }
    }
}

$duration = round((microtime(true) - $startTime) * 1000, 2);

echo "\n=======================================================\n";
echo "RESUMEN DE EJECUCIÓN:\n";
echo "  Total de Pruebas: {$total}\n";
echo "  Aprobadas:        {$passed}\n";
echo "  Fallidas:         {$failed}\n";
echo "  Tiempo total:     {$duration} ms\n";
echo "=======================================================\n";

if ($failed > 0) {
    echo "RESULTADO: HUBO FALLOS EN LAS PRUEBAS.\n\n";
    exit(1);
} else {
    echo "RESULTADO: TODAS LAS PRUEBAS PASARON EXITOSAMENTE (100%).\n\n";
    exit(0);
}
