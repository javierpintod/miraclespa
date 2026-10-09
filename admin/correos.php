<?php
/**
 * MIRACLE SPA - Bandeja de Salida de Correos Electrónicos (Email Simulator & Outbox)
 */

$pageTitle = 'Bandeja de Correos Electrónicos';
require_once __DIR__ . '/layout_header.php';
require_once __DIR__ . '/../app/Models/EmailLog.php';

$emails = EmailLog::getAll(100);
$mailDriver = AppConfig::getSetting('mail_driver', 'simulated');
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-serif font-bold text-gray-900">Historial de Correos & Simulador de Bandeja</h2>
            <p class="text-xs text-gray-500 mt-0.5">
                Modo actual de envío: <strong class="text-[#12372a] uppercase"><?= htmlspecialchars($mailDriver) ?></strong>.
                Todos los correos de confirmación, reprogramación y cancelación quedan registrados con su plantilla HTML completa.
            </p>
        </div>
        <a href="configuracion.php#email-settings" class="bg-[#12372a] text-[#d4af37] px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-[#1a4a39] transition-all flex items-center gap-2 shadow-2xs self-start sm:self-auto">
            <span>⚙ Configurar Servidor SMTP Real</span>
        </a>
    </div>

    <!-- Alerta Informativa sobre el Modo de Correo -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-xs text-blue-900 flex items-start gap-3">
        <span class="text-lg">💡</span>
        <div class="leading-relaxed">
            <span class="font-bold">Herramienta ideal para desarrollo y pruebas en XAMPP:</span>
            Cuando el modo está en <strong>simulado</strong>, el sistema genera el 100% de la plantilla de diseño de lujo y la registra aquí sin requerir servidor SMTP externo. Puedes hacer clic en <strong class="underline">"👁 Ver HTML"</strong> para inspeccionar la experiencia exacta que recibe el cliente y probar los botones de cancelar o reprogramar.
        </div>
    </div>

    <!-- Tabla de Correos -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between text-xs text-gray-500">
            <span>Total de Notificaciones Registradas: <strong><?= count($emails) ?></strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold border-b border-gray-200">
                        <th class="p-4">Fecha y Hora</th>
                        <th class="p-4">Destinatario</th>
                        <th class="p-4">Asunto</th>
                        <th class="p-4 text-center">Tipo</th>
                        <th class="p-4 text-center">Estado</th>
                        <th class="p-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($emails)): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400">
                                Aún no se han enviado notificaciones por correo.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($emails as $em): 
                        $badgeClass = match($em['status']) {
                            'enviado' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'simulado' => 'bg-amber-100 text-amber-800 border-amber-300',
                            'fallido' => 'bg-rose-100 text-rose-800 border-rose-300',
                            default => 'bg-gray-100 text-gray-800 border-gray-300'
                        };
                    ?>
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="p-4 text-gray-500 font-mono text-[11px]">
                                <?= date('d/m/Y h:i A', strtotime($em['sent_at'])) ?>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-900"><?= htmlspecialchars($em['recipient_name']) ?></div>
                                <div class="text-[11px] text-gray-500 font-mono"><?= htmlspecialchars($em['recipient_email']) ?></div>
                            </td>
                            <td class="p-4 font-medium text-gray-800 max-w-sm truncate">
                                <?= htmlspecialchars($em['subject']) ?>
                            </td>
                            <td class="p-4 text-center">
                                <span class="bg-gray-100 text-gray-700 font-semibold px-2 py-0.5 rounded text-[10px] uppercase">
                                    <?= htmlspecialchars($em['email_type']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $badgeClass ?>">
                                    <?= ucfirst($em['status']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <button type="button" onclick="previewEmailModal(<?= $em['id'] ?>)" class="bg-[#12372a] text-[#d4af37] px-3 py-1.5 rounded-lg font-bold text-[11px] hover:bg-[#1a4a39] shadow-2xs">
                                    👁 Ver HTML
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL VISOR DE CORREO HTML -->
<div id="modal-email-preview" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full h-[85vh] flex flex-col shadow-2xl border border-gray-200 overflow-hidden">
        <div class="p-4 bg-[#12372a] text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-base">✉</span>
                <span class="font-serif font-bold text-sm">Vista Previa de Correo Electrónico</span>
            </div>
            <button type="button" onclick="closeEmailPreview()" class="text-gray-300 hover:text-white text-2xl font-bold">&times;</button>
        </div>

        <div class="flex-1 bg-gray-100 p-2 overflow-hidden">
            <iframe id="email-preview-iframe" class="w-full h-full bg-white rounded-xl border border-gray-200 shadow-inner"></iframe>
        </div>

        <div class="p-3 bg-gray-50 border-t border-gray-200 flex justify-end">
            <button type="button" onclick="closeEmailPreview()" class="px-4 py-1.5 bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-300">
                Cerrar Visor
            </button>
        </div>
    </div>
</div>

<script>
async function previewEmailModal(id) {
    const iframe = document.getElementById('email-preview-iframe');
    iframe.src = `ver_email_raw.php?id=${id}`;
    document.getElementById('modal-email-preview').classList.remove('hidden');
}

function closeEmailPreview() {
    document.getElementById('modal-email-preview').classList.add('hidden');
    document.getElementById('email-preview-iframe').src = 'about:blank';
}
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
