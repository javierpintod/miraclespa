<?php
/**
 * MIRACLE SPA - Directorio de Clientes & Historial de Visitas
 */

$pageTitle = 'Directorio de Clientes';
require_once __DIR__ . '/layout_header.php';
require_once __DIR__ . '/../app/Models/Client.php';

$clients = Client::getAllWithStats();
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-serif font-bold text-gray-900">Directorio de Clientes & Historial de Visitas</h2>
            <p class="text-xs text-gray-500 mt-0.5">Control de frecuencia, inversión acumulada y datos de contacto de cada cliente.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between text-xs text-gray-500">
            <span>Total Clientes Registrados: <strong><?= count($clients) ?></strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold border-b border-gray-200">
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Correo Electrónico</th>
                        <th class="p-4">Teléfono Móvil</th>
                        <th class="p-4 text-center">Citas Totales</th>
                        <th class="p-4 text-right">Inversión Acumulada</th>
                        <th class="p-4 text-center">Última Visita</th>
                        <th class="p-4">Notas / Preferencias</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($clients as $c): ?>
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="p-4 font-bold text-gray-900 flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-full bg-[#12372a]/10 text-[#12372a] flex items-center justify-center font-bold text-xs">
                                    <?= substr($c['name'], 0, 1) ?>
                                </span>
                                <span><?= htmlspecialchars($c['name']) ?></span>
                            </td>
                            <td class="p-4 text-gray-600">
                                <?= htmlspecialchars($c['email']) ?>
                            </td>
                            <td class="p-4 text-gray-600 font-mono">
                                <?= htmlspecialchars($c['phone']) ?>
                            </td>
                            <td class="p-4 text-center font-bold text-gray-800">
                                <?= (int)$c['total_appointments'] ?>
                            </td>
                            <td class="p-4 text-right font-serif font-bold text-emerald-800 text-sm">
                                $<?= number_format((float)$c['total_spent'], 2) ?> USD
                            </td>
                            <td class="p-4 text-center text-gray-500">
                                <?= $c['last_visit'] ? ViewHelper::formatDate($c['last_visit']) : 'Sin visitas' ?>
                            </td>
                            <td class="p-4 text-gray-500 text-[11px] max-w-xs truncate">
                                <?= htmlspecialchars($c['notes'] ?: 'Sin notas') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
