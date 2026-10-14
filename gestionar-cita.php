<?php
/**
 * MIRACLE SPA - Gestión de Cita del Cliente (Cancelar y Reprogramar)
 * Estilo Gráfico: Glassmorfismo / Glassmorphism & Aurora Luxury Spa
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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/app.css?v=<?= filemtime(__DIR__ . '/assets/css/app.css') ?>">
    <style>
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .font-mono-pm { font-family: 'Space Mono', monospace; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col antialiased selection:bg-teal-500 selection:text-white relative overflow-x-hidden">

    <!-- Orbes de Luz de Fondo Glassmorfista -->
    <div class="aurora-orb aurora-orb-teal w-96 h-96 -top-20 -left-20"></div>
    <div class="aurora-orb aurora-orb-violet w-[32rem] h-[32rem] top-1/3 -right-24"></div>
    <div class="aurora-orb aurora-orb-rose w-80 h-80 -bottom-10 left-1/4"></div>

    <!-- Header Glassmorfista -->
    <header class="glass-card rounded-none border-x-0 border-t-0 border-b border-white/60 sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-18 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3 group">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white flex items-center justify-center font-outfit font-extrabold text-lg shadow-md shadow-teal-500/25 group-hover:scale-105 transition-transform border border-white/40">M</span>
                <div>
                    <span class="font-outfit font-bold text-lg text-slate-900 leading-none block">Miracle Spa</span>
                    <span class="font-mono-pm text-[9px] uppercase tracking-wider text-teal-700 font-bold block mt-1">[ AUTOGESTIÓN DE CITAS ]</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <a href="index.php" class="text-xs font-semibold text-slate-600 hover:text-teal-700 transition-colors px-3 py-1.5 rounded-xl hover:bg-white/50">← Portal Principal</a>
                <a href="index.php#booking-wizard-section" class="btn-glass-primary text-xs px-4 py-2 rounded-xl">Nueva Reserva</a>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-12 flex-1 w-full relative z-10">
        <?php if (!$appointment): ?>
            <!-- Formulario de Búsqueda si no se encontró la cita -->
            <div class="glass-card p-8 sm:p-10 text-center max-w-lg mx-auto border border-white/80 shadow-2xl">
                <div class="w-16 h-16 rounded-2xl bg-teal-500/15 text-teal-700 border border-teal-500/30 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">
                    🔍
                </div>
                <span class="glass-badge glass-badge-teal mb-3">[ LOCALIZADOR DE RESERVAS ]</span>
                <h1 class="text-2xl sm:text-3xl font-outfit font-extrabold text-slate-900 mb-2">Consulta tu Cita</h1>
                <p class="text-xs text-slate-600 mb-6 leading-relaxed">Ingresa tu código único de reserva (ejemplo: <span class="font-mono-pm font-bold text-teal-700">MRC-2026-1007</span>) para revisar el estado en vivo, reprogramar o cancelar tu turno.</p>

                <form method="GET" class="space-y-4">
                    <div>
                        <input type="text" name="code" required placeholder="Código: MRC-2026-XXXX" class="w-full text-center px-4 py-3.5 rounded-2xl border border-white/80 bg-white/75 font-mono-pm text-sm uppercase font-bold focus:ring-2 focus:ring-teal-500 focus:outline-none shadow-inner">
                    </div>
                    <button type="submit" class="w-full btn-glass-primary font-outfit font-bold py-3.5 rounded-2xl text-xs tracking-wide">
                        Buscar Mi Cita →
                    </button>
                </form>

                <div class="mt-6 text-xs text-slate-500 font-mono-pm">
                    ¿No recuerdas tu código? Revisa el correo electrónico de confirmación emitido al realizar tu reserva.
                </div>
            </div>

        <?php else: ?>
            <!-- DETALLES DE LA CITA ENCONTRADA -->
            <div class="glass-card overflow-hidden shadow-2xl border border-white/80">
                <!-- Cabecera de Estado Glassmorfista -->
                <div class="p-6 sm:p-8 bg-slate-900/85 backdrop-blur-md text-white border-b border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="font-mono-pm text-[11px] text-teal-300 uppercase font-bold tracking-wider mb-1 flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                            [ COMPROBANTE OFICIAL DE RESERVA ]
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-outfit font-extrabold text-white"><?= htmlspecialchars($appointment['service_name']) ?></h1>
                        <span class="font-mono-pm text-xs text-slate-300 font-medium block mt-1">Código: <span class="bg-teal-500/20 text-teal-200 border border-teal-400/30 px-2 py-0.5 rounded-md text-[11px] font-bold"><?= htmlspecialchars($appointment['code']) ?></span></span>
                    </div>
                    <div>
                        <?= ViewHelper::statusBadge($appointment['status']) ?>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Alerta de Estado Cancelado -->
                    <?php if ($appointment['status'] === 'cancelada'): ?>
                        <div class="glass-card-subtle bg-rose-500/10 border border-rose-500/30 rounded-2xl p-4 text-xs text-rose-900">
                            <div class="font-outfit font-bold text-rose-700 text-sm mb-1 flex items-center gap-1.5">
                                <span>✖</span> Esta cita fue cancelada
                            </div>
                            <div class="font-mono-pm text-slate-600">Fecha de cancelación: <?= $appointment['cancelled_at'] ? date('d/m/Y h:i A', strtotime($appointment['cancelled_at'])) : '-' ?></div>
                            <?php if (!empty($appointment['cancellation_reason'])): ?>
                                <div class="mt-1 text-slate-700 italic">Motivo registrado: <?= htmlspecialchars($appointment['cancellation_reason']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Grid de Información Glassmorfista -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 glass-subtle-box p-6 text-xs">
                        <div>
                            <span class="text-slate-500 font-mono-pm text-[11px] uppercase tracking-wider block mb-0.5">Fecha del Turno:</span>
                            <span class="font-bold text-slate-900 text-sm"><?= ViewHelper::formatDate($appointment['date']) ?></span>
                        </div>
                        <div>
                            <span class="text-slate-500 font-mono-pm text-[11px] uppercase tracking-wider block mb-0.5">Horario Reservado:</span>
                            <span class="font-bold text-slate-900 text-sm font-mono-pm"><?= ViewHelper::formatTime($appointment['start_time']) ?> - <?= ViewHelper::formatTime($appointment['end_time']) ?> (<?= $appointment['duration_minutes'] ?> min)</span>
                        </div>
                        <div>
                            <span class="text-slate-500 font-mono-pm text-[11px] uppercase tracking-wider block mb-0.5">Especialista Asignado:</span>
                            <span class="font-bold text-teal-800 text-sm"><?= htmlspecialchars($appointment['professional_name']) ?></span>
                            <span class="text-slate-500 block text-[11px]"><?= htmlspecialchars($appointment['professional_title']) ?></span>
                        </div>
                        <div>
                            <span class="text-slate-500 font-mono-pm text-[11px] uppercase tracking-wider block mb-0.5">Inversión del Servicio:</span>
                            <span class="font-mono-pm font-extrabold text-teal-700 text-base">$<?= number_format((float)$appointment['price'], 2) ?> USD</span>
                        </div>
                        <div>
                            <span class="text-slate-500 font-mono-pm text-[11px] uppercase tracking-wider block mb-0.5">Cliente:</span>
                            <span class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($appointment['client_name']) ?></span>
                            <span class="text-slate-500 block text-[11px] font-mono-pm"><?= htmlspecialchars($appointment['client_email']) ?> &bull; <?= htmlspecialchars($appointment['client_phone']) ?></span>
                        </div>
                        <div>
                            <span class="text-slate-500 font-mono-pm text-[11px] uppercase tracking-wider block mb-0.5">Sede:</span>
                            <span class="text-slate-700 text-xs"><?= htmlspecialchars($spaAddress) ?></span>
                        </div>
                    </div>

                    <!-- Panel de Acciones (Solo si está Confirmada o Reprogramada) -->
                    <?php if (in_array($appointment['status'], ['confirmada', 'reprogramada'])): ?>
                        <div class="border-t border-slate-200/60 pt-6">
                            <h3 class="text-sm font-outfit font-bold text-slate-900 mb-3">Acciones Disponibles para tu Reserva</h3>

                            <?php if ($policyCheck && !$policyCheck['allowed']): ?>
                                <div class="glass-card-subtle bg-amber-500/10 border border-amber-500/30 rounded-2xl p-4 text-xs text-amber-900 mb-4 font-mono-pm">
                                    <span class="font-bold block mb-1">Aviso de Política de Modificaciones:</span>
                                    <?= htmlspecialchars($policyCheck['reason']) ?>
                                </div>
                            <?php else: ?>
                                <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                                    Puedes reprogramar tu fecha y horario sin costo, o cancelar tu reserva si no puedes asistir (restan <?= $policyCheck['hoursRemaining'] ?? 0 ?> horas para tu turno).
                                </p>
                            <?php endif; ?>

                            <div class="flex flex-wrap items-center gap-3">
                                <?php if ($policyCheck && $policyCheck['allowed']): ?>
                                    <button type="button" onclick="openRescheduleModal()" class="btn-glass-secondary px-5 py-2.5 rounded-2xl text-xs flex items-center gap-2">
                                        <span>📅 Reprogramar Cita</span>
                                    </button>
                                    <button type="button" onclick="openCancelModal()" class="btn-glass-rose px-5 py-2.5 rounded-2xl text-xs flex items-center gap-2">
                                        <span>✖ Cancelar Cita</span>
                                    </button>
                                <?php else: ?>
                                    <a href="tel:<?= urlencode($spaPhone) ?>" class="btn-glass-primary px-5 py-2.5 rounded-2xl text-xs inline-block">
                                        Llamar al Concierge del Spa (<?= htmlspecialchars($spaPhone) ?>)
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- MODAL DE REPROGRAMACIÓN GLASSMORFISTA -->
            <div id="modal-reschedule" class="fixed inset-0 bg-slate-950/40 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
                <div class="glass-modal max-w-md w-full p-6 sm:p-8 rounded-3xl shadow-2xl relative">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200/60 mb-5">
                        <h3 class="font-outfit font-extrabold text-lg text-slate-900">Reprogramar Mi Cita</h3>
                        <button type="button" onclick="closeRescheduleModal()" class="w-8 h-8 rounded-full bg-slate-200/50 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xl font-bold transition-colors">&times;</button>
                    </div>

                    <form id="form-reschedule" class="space-y-4">
                        <input type="hidden" id="reschedule-token" value="<?= htmlspecialchars($appointment['manage_token']) ?>">
                        <input type="hidden" id="reschedule-service-id" value="<?= (int)$appointment['service_id'] ?>">

                        <div>
                            <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nueva Fecha</label>
                            <input type="date" id="reschedule-date" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required class="w-full px-4 py-2.5 rounded-xl border border-white/80 bg-white/80 font-mono-pm text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nuevo Horario Disponible</label>
                            <select id="reschedule-time" required class="w-full px-4 py-2.5 rounded-xl border border-white/80 bg-white/80 font-mono-pm text-sm focus:ring-2 focus:ring-teal-500 focus:outline-none">
                                <option value="">Cargando horarios disponibles...</option>
                            </select>
                        </div>

                        <div class="pt-5 border-t border-slate-200/60 flex justify-end gap-3">
                            <button type="button" onclick="closeRescheduleModal()" class="px-4 py-2 text-xs font-mono-pm font-bold text-slate-600 hover:text-slate-900">
                                Volver
                            </button>
                            <button type="submit" id="btn-submit-reschedule" class="btn-glass-primary px-5 py-2.5 rounded-2xl text-xs">
                                Confirmar Reprogramación
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL DE CANCELACIÓN GLASSMORFISTA -->
            <div id="modal-cancel" class="fixed inset-0 bg-slate-950/40 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
                <div class="glass-modal max-w-md w-full p-6 sm:p-8 rounded-3xl shadow-2xl relative">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200/60 mb-5">
                        <h3 class="font-outfit font-extrabold text-lg text-rose-600">Confirmar Cancelación</h3>
                        <button type="button" onclick="closeCancelModal()" class="w-8 h-8 rounded-full bg-slate-200/50 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xl font-bold transition-colors">&times;</button>
                    </div>

                    <form id="form-cancel" class="space-y-4">
                        <input type="hidden" id="cancel-token" value="<?= htmlspecialchars($appointment['manage_token']) ?>">

                        <p class="text-xs text-slate-700 leading-relaxed font-medium">
                            ¿Estás seguro de que deseas cancelar tu cita para <strong class="text-slate-900"><?= htmlspecialchars($appointment['service_name']) ?></strong> el día <strong class="text-slate-900"><?= ViewHelper::formatDate($appointment['date']) ?></strong>?
                        </p>

                        <div>
                            <label class="block text-xs font-mono-pm font-bold uppercase tracking-wider text-slate-700 mb-1.5">Motivo de Cancelación (Opcional)</label>
                            <textarea id="cancel-reason" rows="2" placeholder="Motivos personales, cambio de itinerario..." class="w-full px-4 py-2.5 rounded-xl border border-white/80 bg-white/80 text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
                        </div>

                        <div class="pt-5 border-t border-slate-200/60 flex justify-end gap-3">
                            <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-xs font-mono-pm font-bold text-slate-600 hover:text-slate-900">
                                No, Mantener Cita
                            </button>
                            <button type="submit" id="btn-submit-cancel" class="btn-glass-rose px-5 py-2.5 rounded-2xl text-xs">
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

    <!-- Footer Glassmorfista -->
    <footer class="glass-card rounded-none border-x-0 border-b-0 border-t border-white/60 py-6 text-center text-xs font-mono-pm text-slate-500 mt-auto relative z-10">
        <?= htmlspecialchars($spaName) ?> &bull; Atención telefónica: <?= htmlspecialchars($spaPhone) ?>
    </footer>
</body>
</html>
