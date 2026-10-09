<?php
/**
 * MIRACLE SPA - Configuración General del Negocio & Parámetros SMTP
 */

$pageTitle = 'Configuración del Spa & Servidor de Correo';
require_once __DIR__ . '/layout_header.php';
require_once __DIR__ . '/../app/Services/MailerService.php';

// Guardar Configuración
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save_settings') {
        $fields = [
            'spa_name', 'spa_address', 'spa_phone', 'spa_email',
            'currency_symbol', 'buffer_minutes', 'min_cancel_hours',
            'opening_time', 'closing_time', 'slot_interval_minutes',
            'mail_driver', 'smtp_host', 'smtp_port', 'smtp_user',
            'smtp_pass', 'smtp_secure', 'smtp_from_email', 'smtp_from_name'
        ];

        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                AppConfig::setSetting($f, trim($_POST[$f]));
            }
        }

        AuthHelper::setFlash('success', 'Configuración actualizada exitosamente.');
        header('Location: configuracion.php');
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] === 'send_test_email') {
        $testEmail = trim($_POST['test_recipient'] ?? '');
        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            AuthHelper::setFlash('error', 'Correo de destino inválido.');
        } else {
            $html = "<h3>¡Prueba de Configuración de Correo Exitosa!</h3><p>Este es un correo de prueba enviado desde <strong>" . htmlspecialchars(AppConfig::getSetting('spa_name')) . "</strong> para validar el despachador de notificaciones.</p>";
            $res = MailerService::dispatch(
                to: $testEmail,
                name: 'Administrador de Pruebas',
                subject: 'Prueba de Conexión de Correo - Miracle Spa',
                htmlBody: $html,
                type: 'test'
            );

            if ($res['success']) {
                AuthHelper::setFlash('success', "Correo de prueba procesado en modo: {$res['status']}. Revisa la bandeja de entrada o la Bandeja de Correos del panel.");
            } else {
                AuthHelper::setFlash('error', "Fallo al enviar correo: " . ($res['error'] ?? 'Error desconocido'));
            }
        }
        header('Location: configuracion.php');
        exit;
    }
}

$settings = AppConfig::getAllSettings();
?>

<div class="space-y-8 max-w-4xl mx-auto">
    <div>
        <h2 class="text-xl font-serif font-bold text-gray-900">Configuración del Establecimiento & Notificaciones</h2>
        <p class="text-xs text-gray-500 mt-0.5">Parámetros del spa, horarios operativos, buffers de amortiguamiento y credenciales de correo.</p>
    </div>

    <form method="POST" class="space-y-6">
        <input type="hidden" name="action" value="save_settings">

        <!-- Sección 1: Datos del Spa -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs space-y-4">
            <h3 class="font-serif font-bold text-base text-[#12372a] border-b border-gray-100 pb-2">
                1. Información Comercial de Miracle Spa
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nombre Comercial del Spa</label>
                    <input type="text" name="spa_name" value="<?= htmlspecialchars($settings['spa_name'] ?? 'Miracle Spa Sanctuary') ?>" required class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Dirección Física / Sede Principal</label>
                    <input type="text" name="spa_address" value="<?= htmlspecialchars($settings['spa_address'] ?? 'Av. Las Palmas 450, Piso 2') ?>" required class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Teléfono Principal de Contacto</label>
                    <input type="text" name="spa_phone" value="<?= htmlspecialchars($settings['spa_phone'] ?? '+1 (555) 789-2345') ?>" required class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Correo Electrónico Oficial</label>
                    <input type="email" name="spa_email" value="<?= htmlspecialchars($settings['spa_email'] ?? 'citas@miraclespa.com') ?>" required class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Símbolo de Moneda</label>
                    <input type="text" name="currency_symbol" value="<?= htmlspecialchars($settings['currency_symbol'] ?? '$') ?>" required class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
            </div>
        </div>

        <!-- Sección 2: Reglas de Disponibilidad y Políticas -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs space-y-4">
            <h3 class="font-serif font-bold text-base text-[#12372a] border-b border-gray-100 pb-2">
                2. Lógica de Agendamiento, Buffers & Políticas
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Buffer de Higienización entre Citas (Minutos)</label>
                    <input type="number" name="buffer_minutes" value="<?= (int)($settings['buffer_minutes'] ?? 15) ?>" required min="0" max="60" step="5" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                    <span class="text-[11px] text-gray-400 mt-1 block">Tiempo que se reserva automáticamente para desinfección y preparación de cabina.</span>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Anticipación Mínima para Cancelar / Reprogramar (Horas)</label>
                    <input type="number" name="min_cancel_hours" value="<?= (int)($settings['min_cancel_hours'] ?? 4) ?>" required min="1" max="48" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                    <span class="text-[11px] text-gray-400 mt-1 block">Los clientes solo podrán modificar su cita en línea si faltan más de estas horas.</span>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Hora de Apertura del Spa</label>
                    <input type="time" name="opening_time" value="<?= substr($settings['opening_time'] ?? '09:00:00', 0, 5) ?>" required class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Hora de Cierre del Spa</label>
                    <input type="time" name="closing_time" value="<?= substr($settings['closing_time'] ?? '20:00:00', 0, 5) ?>" required class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Intervalo de la Grilla de Turnos (Minutos)</label>
                    <select name="slot_interval_minutes" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                        <option value="15" <?= ($settings['slot_interval_minutes'] ?? '') == '15' ? 'selected' : '' ?>>Cada 15 minutos</option>
                        <option value="30" <?= ($settings['slot_interval_minutes'] ?? '30') == '30' ? 'selected' : '' ?>>Cada 30 minutos (Recomendado)</option>
                        <option value="45" <?= ($settings['slot_interval_minutes'] ?? '') == '45' ? 'selected' : '' ?>>Cada 45 minutos</option>
                        <option value="60" <?= ($settings['slot_interval_minutes'] ?? '') == '60' ? 'selected' : '' ?>>Cada 60 minutos</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Sección 3: Configuración de Correo Electrónico (SMTP) -->
        <div id="email-settings" class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs space-y-4">
            <h3 class="font-serif font-bold text-base text-[#12372a] border-b border-gray-100 pb-2">
                3. Configuración de Correo Electrónico (Confirmaciones y Notificaciones)
            </h3>

            <div class="text-xs text-gray-600 bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-2">
                <strong>Recomendación para XAMPP:</strong> El modo <strong>Simulado</strong> es 100% autónomo y almacena los correos en la base de datos para verlos en la <em>Bandeja de Correos</em> sin necesidad de configurar sendmail. Si deseas enviar correos reales por internet, elige <strong>SMTP</strong> y proporciona tus credenciales (Gmail, Mailtrap, Hostinger o cPanel).
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Controlador de Correo (Driver)</label>
                    <select name="mail_driver" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                        <option value="simulated" <?= ($settings['mail_driver'] ?? 'simulated') === 'simulated' ? 'selected' : '' ?>>Simulado (Visor en Panel - Sin SMTP Externo)</option>
                        <option value="smtp" <?= ($settings['mail_driver'] ?? '') === 'smtp' ? 'selected' : '' ?>>SMTP (Envío Real con Servidor)</option>
                        <option value="mail" <?= ($settings['mail_driver'] ?? '') === 'mail' ? 'selected' : '' ?>>PHP mail() Nativo</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nombre del Remitente</label>
                    <input type="text" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? 'Miracle Spa Sanctuary') ?>" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Correo Remitente (From Email)</label>
                    <input type="email" name="smtp_from_email" value="<?= htmlspecialchars($settings['smtp_from_email'] ?? 'citas@miraclespa.com') ?>" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Servidor SMTP (Host)</label>
                    <input type="text" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? 'sandbox.smtp.mailtrap.io') ?>" placeholder="smtp.gmail.com o mail.tudominio.com" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Puerto SMTP</label>
                    <input type="number" name="smtp_port" value="<?= (int)($settings['smtp_port'] ?? 2525) ?>" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Seguridad / Encriptación</label>
                    <select name="smtp_secure" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                        <option value="tls" <?= ($settings['smtp_secure'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Recomendado - Puerto 587 o 2525)</option>
                        <option value="ssl" <?= ($settings['smtp_secure'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (Puerto 465)</option>
                        <option value="none" <?= ($settings['smtp_secure'] ?? '') === 'none' ? 'selected' : '' ?>>Ninguna (Inseguro - Puerto 25)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Usuario SMTP</label>
                    <input type="text" name="smtp_user" value="<?= htmlspecialchars($settings['smtp_user'] ?? '') ?>" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Contraseña SMTP</label>
                    <input type="password" name="smtp_pass" value="<?= htmlspecialchars($settings['smtp_pass'] ?? '') ?>" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-[#12372a] text-[#d4af37] px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-[#1a4a39] shadow-md transition-all">
                Guardar Todos los Cambios
            </button>
        </div>
    </form>

    <!-- Formulario de Envío de Correo de Prueba -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-2xs">
        <h3 class="font-serif font-bold text-base text-[#12372a] mb-2">Probar Envío de Correo Electrónico</h3>
        <p class="text-xs text-gray-500 mb-4">Ingresa un correo de destino para disparar una prueba con las configuraciones actuales.</p>

        <form method="POST" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="action" value="send_test_email">
            <input type="email" name="test_recipient" required placeholder="tu-correo@ejemplo.com" class="flex-1 px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#12372a]">
            <button type="submit" class="bg-[#12372a] text-white px-5 py-2 rounded-xl text-xs font-bold hover:bg-[#1a4a39] transition-colors shrink-0">
                Enviar Correo de Prueba
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
