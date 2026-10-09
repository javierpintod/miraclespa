<?php
/**
 * MIRACLE SPA - Calendario Global de Citas
 */

$pageTitle = 'Calendario Global de Citas';
require_once __DIR__ . '/layout_header.php';
require_once __DIR__ . '/../app/Models/Appointment.php';
require_once __DIR__ . '/../app/Models/Professional.php';
require_once __DIR__ . '/../app/Models/Service.php';

$professionals = Professional::getAll(false);
$services = Service::getAll(true);

// Determinar mes y año a mostrar (por defecto mes actual)
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$selectedProfId = isset($_GET['professional_id']) ? (int)$_GET['professional_id'] : 0;

$firstDayTimestamp = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = (int)date('t', $firstDayTimestamp);
$firstDayOfWeek = (int)date('w', $firstDayTimestamp); // 0=Dom, 1=Lun, ...
$monthName = date('F', $firstDayTimestamp);

$mesesEspanol = [
    'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
    'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
    'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
    'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
];
$mesActualNombre = $mesesEspanol[$monthName] ?? $monthName;

// Cargar todas las citas del mes
$startDate = sprintf('%04d-%02d-01', $year, $month);
$endDate = sprintf('%04d-%02d-%02d', $year, $month, $daysInMonth);
$monthAppointments = Appointment::getEventsForCalendar($startDate, $endDate);

// Agrupar por día "YYYY-MM-DD"
$appsByDay = [];
foreach ($monthAppointments as $app) {
    if ($selectedProfId > 0 && (int)$app['professional_id'] !== $selectedProfId) {
        continue;
    }
    $appsByDay[$app['date']][] = $app;
}

// Navegación meses
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}
?>

<div class="space-y-6">
    <!-- Barra Superior de Filtros y Controles del Calendario -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="calendario.php?year=<?= $prevYear ?>&month=<?= $prevMonth ?>&professional_id=<?= $selectedProfId ?>" class="p-2 rounded-xl border border-gray-200 hover:bg-gray-100 text-gray-700 transition-colors">
                ←
            </a>
            <h2 class="font-serif font-bold text-xl text-[#12372a]">
                <?= $mesActualNombre ?> <?= $year ?>
            </h2>
            <a href="calendario.php?year=<?= $nextYear ?>&month=<?= $nextMonth ?>&professional_id=<?= $selectedProfId ?>" class="p-2 rounded-xl border border-gray-200 hover:bg-gray-100 text-gray-700 transition-colors">
                →
            </a>
            <a href="calendario.php" class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Hoy
            </a>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <form method="GET" class="flex items-center gap-2">
                <input type="hidden" name="year" value="<?= $year ?>">
                <input type="hidden" name="month" value="<?= $month ?>">
                <select name="professional_id" onchange="this.form.submit()" class="text-xs font-semibold px-3 py-2 rounded-xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#12372a]">
                    <option value="0">Todos los Especialistas</option>
                    <?php foreach ($professionals as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $selectedProfId == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <a href="citas.php?action=new" class="bg-[#12372a] text-[#d4af37] px-4 py-2 rounded-xl text-xs font-bold hover:bg-[#1a4a39] transition-all flex items-center gap-1.5 shrink-0 shadow-2xs">
                <span>+ Nueva Cita</span>
            </a>
        </div>
    </div>

    <!-- Grilla del Calendario Mensual -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs overflow-hidden">
        <!-- Encabezados de Días de Semana -->
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200 text-center text-xs font-bold uppercase tracking-wider text-gray-500 py-3">
            <div>Dom</div>
            <div>Lun</div>
            <div>Mar</div>
            <div>Mié</div>
            <div>Jue</div>
            <div>Vie</div>
            <div>Sáb</div>
        </div>

        <!-- Celdas de Días -->
        <div class="grid grid-cols-7 divide-x divide-y divide-gray-100">
            <?php
            // Relleno antes del primer día del mes
            for ($i = 0; $i < $firstDayOfWeek; $i++):
            ?>
                <div class="min-h-[110px] bg-gray-50/40 p-2 text-gray-300"></div>
            <?php endfor; ?>

            <?php
            $todayStr = date('Y-m-d');
            for ($d = 1; $d <= $daysInMonth; $d++):
                $currentDateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
                $isToday = ($currentDateStr === $todayStr);
                $dayAppointments = $appsByDay[$currentDateStr] ?? [];
            ?>
                <div class="min-h-[110px] p-2 hover:bg-emerald-50/20 transition-colors relative flex flex-col justify-between <?= $isToday ? 'bg-amber-50/30' : '' ?>">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold <?= $isToday ? 'w-6 h-6 rounded-full bg-[#12372a] text-[#d4af37] flex items-center justify-center' : 'text-gray-700' ?>">
                            <?= $d ?>
                        </span>
                        <?php if (count($dayAppointments) > 0): ?>
                            <span class="text-[10px] font-bold text-gray-400"><?= count($dayAppointments) ?> cita(s)</span>
                        <?php endif; ?>
                    </div>

                    <!-- Lista de citas del día -->
                    <div class="space-y-1 overflow-y-auto max-h-[85px] flex-1">
                        <?php foreach ($dayAppointments as $app): 
                            $statusBg = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                            if ($app['status'] === 'cancelada') $statusBg = 'bg-rose-100 text-rose-800 border-rose-300 line-through';
                            if ($app['status'] === 'completada') $statusBg = 'bg-blue-100 text-blue-800 border-blue-300';
                            if ($app['status'] === 'reprogramada') $statusBg = 'bg-purple-100 text-purple-800 border-purple-300';
                        ?>
                            <div onclick='openAppointmentDetails(<?= json_encode($app) ?>)' 
                                 class="p-1 px-1.5 rounded text-[11px] font-medium border <?= $statusBg ?> truncate cursor-pointer hover:opacity-80 transition-opacity">
                                <span class="font-bold"><?= substr($app['start_time'], 0, 5) ?></span>
                                <span><?= htmlspecialchars($app['service_name']) ?></span>
                                <span class="text-[10px] block text-gray-600 truncate"><?= htmlspecialchars($app['client_name']) ?> (<?= htmlspecialchars($app['professional_name']) ?>)</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endfor; ?>

            <?php
            // Relleno restante para completar la última semana
            $totalCells = $firstDayOfWeek + $daysInMonth;
            $remainder = (7 - ($totalCells % 7)) % 7;
            for ($i = 0; $i < $remainder; $i++):
            ?>
                <div class="min-h-[110px] bg-gray-50/40 p-2 text-gray-300"></div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- MODAL DE DETALLES Y ACCIONES DE CITA -->
<div id="modal-appointment-detail" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400" id="detail-code">MRC-XXXX</span>
                <h3 class="font-serif font-bold text-lg text-[#12372a]" id="detail-service">Servicio</h3>
            </div>
            <button type="button" onclick="closeDetailsModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>

        <div class="space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3.5 rounded-xl border border-gray-200">
                <div>
                    <span class="text-gray-400 block">Fecha y Hora:</span>
                    <span class="font-bold text-gray-900" id="detail-datetime">-</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Especialista:</span>
                    <span class="font-bold text-[#12372a]" id="detail-professional">-</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Cliente:</span>
                    <span class="font-bold text-gray-900" id="detail-client">-</span>
                    <span class="text-gray-500 block text-[10px]" id="detail-contact">-</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Precio:</span>
                    <span class="font-bold text-emerald-700 text-sm" id="detail-price">-</span>
                </div>
            </div>

            <!-- Cambio de Estado Rápido -->
            <div>
                <label class="block font-bold text-gray-700 mb-1">Estado de la Cita:</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="updateAppStatus('confirmada')" class="px-3 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:bg-emerald-100">
                        Confirmada
                    </button>
                    <button type="button" onclick="updateAppStatus('completada')" class="px-3 py-1.5 rounded-lg border border-blue-300 bg-blue-50 text-blue-800 font-bold hover:bg-blue-100">
                        Completada
                    </button>
                    <button type="button" onclick="updateAppStatus('cancelada')" class="px-3 py-1.5 rounded-lg border border-rose-300 bg-rose-50 text-rose-800 font-bold hover:bg-rose-100">
                        Cancelar
                    </button>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <button type="button" onclick="resendEmail()" class="text-xs font-semibold text-[#12372a] hover:underline flex items-center gap-1">
                    ✉ Reenviar Correo
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeDetailsModal()" class="px-3 py-1.5 rounded-lg text-gray-600 hover:bg-gray-100 font-semibold">
                        Cerrar
                    </button>
                    <a id="detail-manage-link" href="#" target="_blank" class="bg-[#12372a] text-white px-4 py-2 rounded-xl font-bold hover:bg-[#1a4a39]">
                        Ver Ficha Completa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentAppointmentId = null;

function openAppointmentDetails(app) {
    currentAppointmentId = app.id;
    document.getElementById('detail-code').textContent = app.code;
    document.getElementById('detail-service').textContent = app.service_name;
    document.getElementById('detail-datetime').textContent = `${app.date} | ${app.start_time.substring(0,5)} - ${app.end_time.substring(0,5)}`;
    document.getElementById('detail-professional').textContent = app.professional_name;
    document.getElementById('detail-client').textContent = app.client_name;
    document.getElementById('detail-contact').textContent = `${app.client_email} • ${app.client_phone}`;
    document.getElementById('detail-price').textContent = `$${parseFloat(app.price).toFixed(2)} USD`;
    document.getElementById('detail-manage-link').href = `../gestionar-cita.php?token=${encodeURIComponent(app.manage_token)}`;

    document.getElementById('modal-appointment-detail').classList.remove('hidden');
}

function closeDetailsModal() {
    document.getElementById('modal-appointment-detail').classList.add('hidden');
}

async function updateAppStatus(newStatus) {
    if (!currentAppointmentId) return;
    try {
        const resp = await fetch('../api/appointment_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'update_status',
                appointment_id: currentAppointmentId,
                status: newStatus
            })
        });
        const res = await resp.json();
        if (res.success) {
            alert('Estado actualizado.');
            location.reload();
        } else {
            alert(res.error || 'Error al actualizar.');
        }
    } catch (e) {
        alert('Error de conexión.');
    }
}

async function resendEmail() {
    if (!currentAppointmentId) return;
    try {
        const resp = await fetch('../api/appointment_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'resend_email',
                appointment_id: currentAppointmentId
            })
        });
        const res = await resp.json();
        if (res.success) {
            alert('Correo reenviado exitosamente.');
        } else {
            alert(res.error || 'No se pudo reenviar el correo.');
        }
    } catch (e) {
        alert('Error de conexión.');
    }
}
</script>

<?php require_once __DIR__ . '/layout_footer.php'; ?>
