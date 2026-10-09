<?php
/**
 * MIRACLE SPA - Gestión Integral de Citas & Reservas (CRUD)
 */

$pageTitle = 'Gestión de Citas & Reservas';
require_once __DIR__ . '/layout_header.php';
require_once __DIR__ . '/../app/Models/Appointment.php';
require_once __DIR__ . '/../app/Models/Service.php';
require_once __DIR__ . '/../app/Models/Professional.php';
require_once __DIR__ . '/../app/Services/BookingService.php';

// Filtros
$filters = [
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? '',
    'professional_id' => $_GET['professional_id'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? ''
];

$appointments = Appointment::getAll($filters);
$professionals = Professional::getAll(false);
$services = Service::getAll(true);

// Procesar Creación Manual de Cita desde el Panel Admin
$createMessage = '';
$createError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_admin_booking') {
    $bookingData = [
        'client_name' => trim($_POST['client_name'] ?? ''),
        'client_email' => trim($_POST['client_email'] ?? ''),
        'client_phone' => trim($_POST['client_phone'] ?? ''),
        'service_id' => (int)($_POST['service_id'] ?? 0),
        'professional_id' => $_POST['professional_id'] === 'any' ? 'any' : (int)$_POST['professional_id'],
        'date' => trim($_POST['date'] ?? ''),
        'start_time' => trim($_POST['start_time'] ?? ''),
        'client_notes' => trim($_POST['client_notes'] ?? 'Reserva manual creada por recepción.')
    ];

    $res = BookingService::createBooking($bookingData);
    if ($res['success']) {
        AuthHelper::setFlash('success', "Cita creada exitosamente con código {$res['code']}. Correo de confirmación emitido.");
        header('Location: citas.php');
        exit;
    } else {
        $createError = $res['error'] ?? 'Error al crear la cita.';
    }
}
?>

<div class="space-y-6">
    <!-- Barra Superior con Botón de Creación -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-serif font-bold text-gray-900">Listado de Citas & Reservaciones</h2>
            <p class="text-xs text-gray-500 mt-0.5">Control global de citas, reprogramaciones, estados y cancelaciones.</p>
        </div>
        <button type="button" onclick="openNewAppointmentModal()" class="bg-[#12372a] text-[#d4af37] px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-[#1a4a39] transition-all flex items-center gap-2 shadow-2xs self-start sm:self-auto">
            <span>+ Agendar Nueva Cita Manual</span>
        </button>
    </div>

    <?php if ($createError): ?>
        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl p-4">
            <strong>Error:</strong> <?= htmlspecialchars($createError) ?>
        </div>
    <?php endif; ?>

    <!-- Barra de Filtros y Búsqueda -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block text-gray-500 font-semibold mb-1">Buscar (Código, Cliente, Teléfono)</label>
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>" placeholder="Ej. Lucía o MRC-..." class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
            </div>

            <div>
                <label class="block text-gray-500 font-semibold mb-1">Estado</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                    <option value="">Todos los Estados</option>
                    <option value="confirmada" <?= $filters['status'] === 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                    <option value="completada" <?= $filters['status'] === 'completada' ? 'selected' : '' ?>>Completada</option>
                    <option value="reprogramada" <?= $filters['status'] === 'reprogramada' ? 'selected' : '' ?>>Reprogramada</option>
                    <option value="cancelada" <?= $filters['status'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-500 font-semibold mb-1">Especialista</label>
                <select name="professional_id" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                    <option value="">Todos</option>
                    <?php foreach ($professionals as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $filters['professional_id'] == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-500 font-semibold mb-1">Desde Fecha</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-[#12372a] text-white py-2 px-3 rounded-xl font-bold hover:bg-[#1a4a39] transition-colors">
                    Filtrar
                </button>
                <a href="citas.php" class="p-2 rounded-xl border border-gray-300 hover:bg-gray-100 text-gray-600 transition-colors" title="Limpiar Filtros">
                    ↺
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Citas -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between text-xs text-gray-500">
            <span>Mostrando <strong><?= count($appointments) ?></strong> cita(s) encontradas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold border-b border-gray-200">
                        <th class="p-3.5">Código</th>
                        <th class="p-3.5">Fecha & Horario</th>
                        <th class="p-3.5">Cliente</th>
                        <th class="p-3.5">Servicio</th>
                        <th class="p-3.5">Especialista</th>
                        <th class="p-3.5 text-right">Inversión</th>
                        <th class="p-3.5 text-center">Estado</th>
                        <th class="p-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-400">
                                No se encontraron citas con los filtros especificados.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($appointments as $a): ?>
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="p-3.5 font-mono font-bold text-[#12372a]">
                                <?= htmlspecialchars($a['code']) ?>
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-gray-900"><?= ViewHelper::formatDate($a['date']) ?></div>
                                <div class="text-[11px] text-gray-500"><?= substr($a['start_time'], 0, 5) ?> - <?= substr($a['end_time'], 0, 5) ?> (<?= $a['service_duration'] ?> min)</div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-semibold text-gray-900"><?= htmlspecialchars($a['client_name']) ?></div>
                                <div class="text-[11px] text-gray-500"><?= htmlspecialchars($a['client_phone']) ?> &bull; <?= htmlspecialchars($a['client_email']) ?></div>
                            </td>
                            <td class="p-3.5 font-medium text-gray-800">
                                <?= htmlspecialchars($a['service_name']) ?>
                            </td>
                            <td class="p-3.5 text-[#12372a] font-semibold">
                                <?= htmlspecialchars($a['professional_name']) ?>
                            </td>
                            <td class="p-3.5 text-right font-serif font-bold text-emerald-800">
                                $<?= number_format((float)$a['price'], 2) ?>
                            </td>
                            <td class="p-3.5 text-center">
                                <?= ViewHelper::statusBadge($a['status']) ?>
                            </td>
                            <td class="p-3.5 text-right space-x-1">
                                <a href="../gestionar-cita.php?token=<?= urlencode($a['manage_token']) ?>" target="_blank" class="p-1.5 text-gray-500 hover:text-[#12372a] rounded hover:bg-gray-100 inline-block" title="Ver Comprobante">
                                    👁
                                </a>
                                <button type="button" onclick="quickStatusChange(<?= $a['id'] ?>, 'completada')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Marcar como Completada">
                                    ✓
                                </button>
                                <?php if ($a['status'] !== 'cancelada'): ?>
                                    <button type="button" onclick="quickCancelApp(<?= $a['id'] ?>)" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded" title="Cancelar Cita">
                                        ✕
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL PARA NUEVA CITA MANUAL -->
<div id="modal-new-booking" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="font-serif font-bold text-lg text-[#12372a]">Agendar Nueva Cita Manual</h3>
            <button type="button" onclick="closeNewAppointmentModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>

        <form method="POST" class="space-y-3.5 text-xs">
            <input type="hidden" name="action" value="create_admin_booking">

            <div>
                <label class="block font-bold text-gray-700 mb-1">Nombre Completo del Cliente *</label>
                <input type="text" name="client_name" required placeholder="Ej. Roberto Gómez" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Correo Electrónico *</label>
                    <input type="email" name="client_email" required placeholder="cliente@correo.com" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Teléfono *</label>
                    <input type="tel" name="client_phone" required placeholder="+1 555 000 0000" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Servicio *</label>
                <select name="service_id" id="admin-service-select" required onchange="fetchAdminSlots()" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                    <option value="">Selecciona un servicio...</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= htmlspecialchars($s['name']) ?> (<?= $s['duration_minutes'] ?> min - $<?= number_format((float)$s['price'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Fecha *</label>
                    <input type="date" name="date" id="admin-date-input" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required onchange="fetchAdminSlots()" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Horario de Inicio *</label>
                    <select name="start_time" id="admin-slot-select" required class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                        <option value="">Primero selecciona servicio y fecha...</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Especialista</label>
                <select name="professional_id" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                    <option value="any">Asignación Automática (Recomendado)</option>
                    <?php foreach ($professionals as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['title']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Notas Internas</label>
                <textarea name="client_notes" rows="2" placeholder="Notas sobre el cliente o cita..." class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#12372a]"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeNewAppointmentModal()" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" class="bg-[#12372a] text-[#d4af37] px-5 py-2 rounded-xl font-bold hover:bg-[#1a4a39]">
                    Confirmar Cita & Enviar Correo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openNewAppointmentModal() {
    document.getElementById('modal-new-booking').classList.remove('hidden');
    fetchAdminSlots();
}
function closeNewAppointmentModal() {
    document.getElementById('modal-new-booking').classList.add('hidden');
}

async function fetchAdminSlots() {
    const serviceId = document.getElementById('admin-service-select').value;
    const date = document.getElementById('admin-date-input').value;
    const select = document.getElementById('admin-slot-select');

    if (!serviceId || !date) {
        select.innerHTML = '<option value="">Selecciona servicio y fecha</option>';
        return;
    }

    select.innerHTML = '<option value="">Consultando disponibilidad...</option>';

    try {
        const resp = await fetch(`../api/get_slots.php?date=${encodeURIComponent(date)}&service_id=${serviceId}`);
        const data = await resp.json();

        select.innerHTML = '';
        if (!data.slots || data.slots.length === 0) {
            select.innerHTML = '<option value="">Sin turnos libres para esta fecha</option>';
            return;
        }

        data.slots.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.time;
            opt.textContent = `${s.formatted_time} (${s.available_professionals_count} libre(s))`;
            select.appendChild(opt);
        });
    } catch (e) {
        select.innerHTML = '<option value="">Error al cargar turnos</option>';
    }
}

async function quickStatusChange(id, status) {
    if (!confirm(`¿Deseas marcar esta cita como ${status}?`)) return;
    try {
        const resp = await fetch('../api/appointment_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'update_status', appointment_id: id, status: status })
        });
        const res = await resp.json();
        if (res.success) {
            location.reload();
        } else {
            alert(res.error || 'Error al actualizar.');
        }
    } catch (e) {
        alert('Error de conexión.');
    }
}

async function quickCancelApp(id) {
    const reason = prompt('Indica el motivo de cancelación:');
    if (reason === null) return;
    try {
        const resp = await fetch('../api/appointment_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'cancel', appointment_id: id, reason: reason })
        });
        const res = await resp.json();
        if (res.success) {
            alert('Cita cancelada y notificada.');
            location.reload();
        } else {
            alert(res.error || 'Error al cancelar.');
        }
    } catch (e) {
        alert('Error de conexión.');
    }
}

<?php if (isset($_GET['action']) && $_GET['action'] === 'new'): ?>
    openNewAppointmentModal();
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
