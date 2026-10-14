<?php
/**
 * MIRACLE SPA - Gestión de Cita del Cliente (Cancelar y Reprogramar)
 * Estilo Gráfico: Posmodernismo / Postmodernism & Architectural Atelier
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Models/Appointment.php';
require_once __DIR__ . '/app/Services/AvailabilityService.php';
require_once __DIR__ . '/app/Helpers/ViewHelper.php';

$token = $_GET['token'] ?? null;
$code = $_GET['code'] ?? null;
$actionQuery = $_GET['action'] ?? null; // 'cancelar' o 'reprogramar'

$appointment = null;
if (!empty($token)) {
    $appointment = Appointment::findByManageToken($token);
} elseif (!empty($code)) {
    $appointment = Appointment::findByCode($code);
}

$policyCheck = null;
if ($appointment) {
    $policyCheck = AvailabilityService::canModifyAppointment($appointment);
}

$spaName = AppConfig::getSetting('spa_name', 'Miracle Spa Sanctuary');
$spaAddress = AppConfig::getSetting('spa_address', 'Av. Las Palmas 450, Centro de Bienestar');
$spaPhone = AppConfig::getSetting('spa_phone', '+1 (555) 789-2345');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Mi Cita — <?= htmlspecialchars($spaName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=Space+Mono:wght@400;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .font-syne { font-family: 'Syne', sans-serif; }
        .font-space { font-family: 'Space Grotesk', sans-serif; }
        .font-mono-pm { font-family: 'Space Mono', monospace; }
    </style>
</head>
<body class="bg-[#F7F5EE] text-[#0D1117] min-h-screen flex flex-col antialiased selection:bg-[#D4FF00] selection:text-[#0D1117]">

    <!-- Header Posmodernista -->
    <header class="bg-white/95 backdrop-blur-md border-b-2 border-[#0D1117] sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-18 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-[#2548E8] text-white flex items-center justify-center font-syne font-bold text-lg border-2 border-[#0D1117] pm-shadow-xs">M</span>
                <div>
                    <span class="font-syne font-bold text-lg text-[#0D1117] leading-none block">Miracle Spa</span>
                    <span class="font-mono-pm text-[9px] uppercase tracking-wider text-[#2548E8] font-bold block mt-0.5">[ AUTOGESTIÓN ]</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <a href="index.php" class="text-xs font-mono-pm font-bold text-gray-700 hover:text-[#2548E8]">← Portal Principal</a>
                <a href="index.php#booking-wizard-section" class="btn-pm-primary text-xs px-3.5 py-1.5 rounded-xl font-space">Nueva Reserva</a>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-10 flex-1 w-full">
        <?php if (!$appointment): ?>
            <!-- Formulario de Búsqueda si no se encontró la cita -->
            <div class="bg-white rounded-2xl border-2 border-[#0D1117] p-8 pm-shadow-lg text-center max-w-lg mx-auto">
                <div class="w-14 h-14 rounded-2xl bg-[#D4FF00] text-[#0D1117] flex items-center justify-center text-2xl mx-auto mb-4 font-bold border-2 border-[#0D1117] pm-shadow-xs">
                    🔍
                </div>
                <span class="font-mono-pm text-[11px] font-bold text-[#2548E8] uppercase tracking-wider block mb-1">[ LOCALIZADOR DE RESERVAS ]</span>
                <h1 class="text-2xl font-syne font-bold text-[#0D1117] mb-2">Consulta de Citas</h1>
                <p class="text-xs text-gray-600 mb-6 font-medium">Ingresa tu código único de reserva (ejemplo: MRC-2026-1007) para revisar el estado, reprogramar o cancelar.</p>

                <form method="GET" class="space-y-4">
                    <div>
                        <input type="text" name="code" required placeholder="Código: MRC-2026-XXXX" class="w-full text-center px-4 py-3 rounded-xl border-2 border-[#0D1117] font-mono-pm text-sm uppercase font-bold focus:ring-2 focus:ring-[#2548E8] focus:outline-none pm-shadow-xs">
                    </div>
                    <button type="submit" class="w-full btn-pm-primary font-space font-bold py-3.5 rounded-xl text-xs">
                        Buscar Mi Cita →
                    </button>
                </form>

                <div class="mt-6 text-xs text-gray-500 font-mono-pm">
                    ¿No encuentras tu código? Revisa el correo electrónico de confirmación que te emitimos al agendar.
                </div>
            </div>

        <?php else: ?>
            <!-- DETALLES DE LA CITA ENCONTRADA -->
            <div class="bg-white rounded-2xl border-2 border-[#0D1117] overflow-hidden pm-shadow-lg">
                <!-- Cabecera de Estado Posmodernista -->
                <div class="p-6 bg-[#0D1117] text-white border-b-2 border-[#0D1117] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="font-mono-pm text-[11px] text-[#D4FF00] uppercase font-bold tracking-wider mb-1">[ COMPROBANTE OFICIAL DE RESERVA ]</div>
                        <h1 class="text-2xl font-syne font-bold"><?= htmlspecialchars($appointment['service_name']) ?></h1>
                        <span class="font-mono-pm text-xs text-gray-300 font-bold block mt-0.5">Código: <span class="bg-[#2548E8] text-white px-2 py-0.5 rounded text-[11px]"><?= htmlspecialchars($appointment['code']) ?></span></span>
                    </div>
                    <div>
                        <?= ViewHelper::statusBadge($appointment['status']) ?>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Alerta de Estado Cancelado -->
                    <?php if ($appointment['status'] === 'cancelada'): ?>
                        <div class="bg-[#FF2E7E]/10 border-2 border-[#FF2E7E] rounded-xl p-4 text-xs text-[#0D1117] pm-shadow-xs">
                            <div class="font-syne font-bold text-[#FF2E7E] mb-1">Esta cita fue cancelada</div>
                            <div class="font-mono-pm">Fecha de cancelación: <?= $appointment['cancelled_at'] ? date('d/m/Y h:i A', strtotime($appointment['cancelled_at'])) : '-' ?></div>
                            <?php if (!empty($appointment['cancellation_reason'])): ?>
                                <div class="mt-1 text-gray-700 italic font-medium">Motivo registrado: <?= htmlspecialchars($appointment['cancellation_reason']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Grid de Información Posmodernista -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-[#EFECE3] p-5 rounded-xl border-2 border-[#0D1117] text-xs pm-shadow-xs font-medium">
                        <div>
                            <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Fecha del Turno:</span>
                            <span class="font-bold text-gray-900 text-sm"><?= ViewHelper::formatDate($appointment['date']) ?></span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Horario Reservado:</span>
                            <span class="font-bold text-gray-900 text-sm font-mono-pm"><?= ViewHelper::formatTime($appointment['start_time']) ?> - <?= ViewHelper::formatTime($appointment['end_time']) ?> (<?= $appointment['duration_minutes'] ?> min)</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Especialista Asignado:</span>
                            <span class="font-bold text-[#2548E8] text-sm"><?= htmlspecialchars($appointment['professional_name']) ?></span>
                            <span class="text-gray-600 block text-[11px]"><?= htmlspecialchars($appointment['professional_title']) ?></span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Inversión del Servicio:</span>
                            <span class="font-mono-pm font-extrabold text-[#2548E8] text-base">$<?= number_format((float)$appointment['price'], 2) ?> USD</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Cliente:</span>
                            <span class="font-bold text-gray-900"><?= htmlspecialchars($appointment['client_name']) ?></span>
                            <span class="text-gray-600 block text-[11px] font-mono-pm"><?= htmlspecialchars($appointment['client_email']) ?> &bull; <?= htmlspecialchars($appointment['client_phone']) ?></span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-mono-pm font-bold block mb-0.5">Sede:</span>
                            <span class="text-gray-800"><?= htmlspecialchars($spaAddress) ?></span>
                        </div>
                    </div>

                    <!-- Panel de Acciones (Solo si está Confirmada o Reprogramada) -->
                    <?php if (in_array($appointment['status'], ['confirmada', 'reprogramada'])): ?>
                        <div class="border-t-2 border-[#0D1117] pt-6">
                            <h3 class="text-sm font-syne font-bold text-gray-900 mb-3">Acciones Disponibles para tu Reserva</h3>

                            <?php if ($policyCheck && !$policyCheck['allowed']): ?>
                                <div class="bg-[#D4FF00]/25 border-2 border-[#0D1117] rounded-xl p-4 text-xs text-[#0D1117] mb-4 pm-shadow-xs font-mono-pm">
                                    <span class="font-bold block mb-1">Aviso de Política de Cancelación:</span>
                                    <?= htmlspecialchars($policyCheck['reason']) ?>
                                </div>
                            <?php else: ?>
                                <p class="text-xs text-gray-600 font-medium mb-4">
                                    Puedes reprogramar tu fecha y hora sin penalidad, o cancelar si no puedes asistir (restan <?= $policyCheck['hoursRemaining'] ?? 0 ?> horas para tu turno).
                                </p>
                            <?php endif; ?>

                            <div class="flex flex-wrap items-center gap-3">
                                <?php if ($policyCheck && $policyCheck['allowed']): ?>
                                    <button type="button" onclick="openRescheduleModal()" class="btn-pm-acid px-5 py-2.5 rounded-xl text-xs flex items-center gap-1.5 font-space">
                                        <span>📅 Reprogramar Cita</span>
                                    </button>
                                    <button type="button" onclick="openCancelModal()" class="bg-white border-2 border-[#0D1117] text-[#FF2E7E] px-5 py-2.5 rounded-xl font-bold text-xs pm-shadow-xs hover:bg-[#FF2E7E]/10 transition-all flex items-center gap-1.5 font-space">
                                        <span>✖ Cancelar Cita</span>
                                    </button>
                                <?php else: ?>
                                    <a href="tel:<?= urlencode($spaPhone) ?>" class="btn-pm-primary px-5 py-2.5 rounded-xl text-xs inline-block font-space">
                                        Llamar al Concierge del Spa (<?= htmlspecialchars($spaPhone) ?>)
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- MODAL DE REPROGRAMACIÓN POSMODERNO -->
            <div id="modal-reschedule" class="fixed inset-0 bg-[#0D1117]/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 pm-shadow-lg border-2 border-[#0D1117]">
                    <div class="flex items-center justify-between pb-3 border-b-2 border-[#0D1117] mb-4">
                        <h3 class="font-syne font-bold text-lg text-[#0D1117]">Reprogramar Mi Cita</h3>
                        <button type="button" onclick="closeRescheduleModal()" class="text-gray-500 hover:text-black text-2xl font-bold leading-none">&times;</button>
                    </div>

                    <form id="form-reschedule" class="space-y-4">
                        <input type="hidden" id="reschedule-token" value="<?= htmlspecialchars($appointment['manage_token']) ?>">
                        <input type="hidden" id="reschedule-service-id" value="<?= (int)$appointment['service_id'] ?>">

                        <div>
                            <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-gray-700 mb-1">Nueva Fecha</label>
                            <input type="date" id="reschedule-date" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required class="w-full px-3 py-2.5 rounded-xl border-2 border-[#0D1117] font-mono-pm text-sm focus:ring-2 focus:ring-[#2548E8] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-gray-700 mb-1">Nuevo Horario Disponible</label>
                            <select id="reschedule-time" required class="w-full px-3 py-2.5 rounded-xl border-2 border-[#0D1117] font-mono-pm text-sm focus:ring-2 focus:ring-[#2548E8] focus:outline-none">
                                <option value="">Cargando horarios disponibles...</option>
                            </select>
                        </div>

                        <div class="pt-4 border-t-2 border-[#0D1117] flex justify-end gap-2">
                            <button type="button" onclick="closeRescheduleModal()" class="px-4 py-2 text-xs font-mono-pm font-bold text-gray-600 hover:text-gray-900">
                                Volver
                            </button>
                            <button type="submit" id="btn-submit-reschedule" class="btn-pm-primary px-5 py-2.5 rounded-xl text-xs font-space">
                                Confirmar Reprogramación
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL DE CANCELACIÓN POSMODERNO -->
            <div id="modal-cancel" class="fixed inset-0 bg-[#0D1117]/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 pm-shadow-lg border-2 border-[#0D1117]">
                    <div class="flex items-center justify-between pb-3 border-b-2 border-[#0D1117] mb-4">
                        <h3 class="font-syne font-bold text-lg text-[#FF2E7E]">Confirmar Cancelación</h3>
                        <button type="button" onclick="closeCancelModal()" class="text-gray-500 hover:text-black text-2xl font-bold leading-none">&times;</button>
                    </div>

                    <form id="form-cancel" class="space-y-4">
                        <input type="hidden" id="cancel-token" value="<?= htmlspecialchars($appointment['manage_token']) ?>">

                        <p class="text-xs text-gray-700 leading-relaxed font-medium">
                            ¿Estás seguro de que deseas cancelar tu cita para <strong><?= htmlspecialchars($appointment['service_name']) ?></strong> el día <strong><?= ViewHelper::formatDate($appointment['date']) ?></strong>?
                        </p>

                        <div>
                            <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-gray-700 mb-1">Motivo de Cancelación (Opcional)</label>
                            <textarea id="cancel-reason" rows="2" placeholder="Motivos personales, cambio de itinerario..." class="w-full px-3 py-2 rounded-xl border-2 border-[#0D1117] text-xs focus:ring-2 focus:ring-[#FF2E7E] focus:outline-none"></textarea>
                        </div>

                        <div class="pt-4 border-t-2 border-[#0D1117] flex justify-end gap-2">
                            <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-xs font-mono-pm font-bold text-gray-600 hover:text-gray-900">
                                No, Mantener Cita
                            </button>
                            <button type="submit" id="btn-submit-cancel" class="btn-pm-rose px-5 py-2.5 rounded-xl text-xs font-space">
                                Sí, Cancelar Cita
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openRescheduleModal() {
                    document.getElementById('modal-reschedule').classList.remove('hidden');
                    loadRescheduleSlots();
                }
                function closeRescheduleModal() {
                    document.getElementById('modal-reschedule').classList.add('hidden');
                }
                function openCancelModal() {
                    document.getElementById('modal-cancel').classList.remove('hidden');
                }
                function closeCancelModal() {
                    document.getElementById('modal-cancel').classList.add('hidden');
                }

                // Cargar horarios para modal de reprogramar
                const dateInput = document.getElementById('reschedule-date');
                if (dateInput) {
                    dateInput.addEventListener('change', loadRescheduleSlots);
                }

                async function loadRescheduleSlots() {
                    const date = document.getElementById('reschedule-date').value;
                    const serviceId = document.getElementById('reschedule-service-id').value;
                    const select = document.getElementById('reschedule-time');

                    select.innerHTML = '<option value="">Consultando disponibilidad...</option>';

                    try {
                        const resp = await fetch(`api/get_slots.php?date=${encodeURIComponent(date)}&service_id=${serviceId}`);
                        const data = await resp.json();

                        select.innerHTML = '';
                        if (!data.slots || data.slots.length === 0) {
                            select.innerHTML = '<option value="">Sin turnos libres para esta fecha</option>';
                            return;
                        }

                        data.slots.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.time;
                            opt.textContent = `${s.formatted_time} (${s.available_professionals_count} especialista(s) libre(s))`;
                            select.appendChild(opt);
                        });
                    } catch (e) {
                        select.innerHTML = '<option value="">Error al cargar horarios</option>';
                    }
                }

                // Manejo de Reprogramación
                const formReschedule = document.getElementById('form-reschedule');
                if (formReschedule) {
                    formReschedule.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const token = document.getElementById('reschedule-token').value;
                        const newDate = document.getElementById('reschedule-date').value;
                        const newTime = document.getElementById('reschedule-time').value;

                        if (!newTime) {
                            alert('Por favor selecciona un horario disponible.');
                            return;
                        }

                        const btn = document.getElementById('btn-submit-reschedule');
                        btn.disabled = true;
                        btn.textContent = 'Procesando...';

                        try {
                            const resp = await fetch('api/appointment_actions.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    action: 'reschedule',
                                    token: token,
                                    new_date: newDate,
                                    new_time: newTime
                                })
                            });
                            const res = await resp.json();
                            if (res.success) {
                                alert('¡Cita reprogramada con éxito! Hemos enviado un correo con los nuevos datos.');
                                location.reload();
                            } else {
                                alert(res.error || 'No se pudo reprogramar la cita.');
                                btn.disabled = false;
                                btn.textContent = 'Confirmar Reprogramación';
                            }
                        } catch (err) {
                            alert('Error de conexión.');
                            btn.disabled = false;
                            btn.textContent = 'Confirmar Reprogramación';
                        }
                    });
                }

                // Manejo de Cancelación
                const formCancel = document.getElementById('form-cancel');
                if (formCancel) {
                    formCancel.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const token = document.getElementById('cancel-token').value;
                        const reason = document.getElementById('cancel-reason').value.trim();

                        const btn = document.getElementById('btn-submit-cancel');
                        btn.disabled = true;
                        btn.textContent = 'Cancelando...';

                        try {
                            const resp = await fetch('api/appointment_actions.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    action: 'cancel',
                                    token: token,
                                    reason: reason
                                })
                            });
                            const res = await resp.json();
                            if (res.success) {
                                alert('Tu cita ha sido cancelada satisfactoriamente.');
                                location.reload();
                            } else {
                                alert(res.error || 'No se pudo cancelar la cita.');
                                btn.disabled = false;
                                btn.textContent = 'Sí, Cancelar Cita';
                            }
                        } catch (err) {
                            alert('Error de conexión.');
                            btn.disabled = false;
                            btn.textContent = 'Sí, Cancelar Cita';
                        }
                    });
                }

                // Si la URL traía ?action=reprogramar o ?action=cancelar, abrir el modal respectivo automáticamente
                <?php if ($actionQuery === 'reprogramar'): ?>
                    openRescheduleModal();
                <?php elseif ($actionQuery === 'cancelar'): ?>
                    openCancelModal();
                <?php endif; ?>
            </script>
        <?php endif; ?>
    </main>

    <footer class="bg-white border-t-2 border-[#0D1117] py-6 text-center text-xs font-mono-pm text-gray-500 mt-auto">
        <?= htmlspecialchars($spaName) ?> &bull; Atención telefónica: <?= htmlspecialchars($spaPhone) ?>
    </footer>
</body>
</html>
